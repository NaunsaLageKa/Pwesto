<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\HubUserBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HubOwnerUserController extends Controller
{
    public function index(Request $request)
    {
        $hubOwner = auth()->user();

        // Show users who booked this hub owner by ID or matching company name.
        $query = User::whereHas('bookings', function($q) use ($hubOwner) {
            $q->where('hub_owner_id', $hubOwner->id);
            if ($hubOwner->company) {
                $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($hubOwner->company) . '%']);
            }
        });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount([
                        'bookings as hub_bookings_count' => function ($q) use ($hubOwner) {
                            $q->where('hub_owner_id', $hubOwner->id);
                            if ($hubOwner->company) {
                                $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($hubOwner->company) . '%']);
                            }
                        }
                    ])
                    ->orderByDesc('hub_bookings_count')
                    ->orderBy('name')
                    ->paginate(15);

        $bannedUserIds = HubUserBan::where('hub_owner_id', $hubOwner->id)
            ->whereIn('user_id', $users->pluck('id'))
            ->pluck('user_id')
            ->all();

        // Get analytics data
        $analytics = $this->getUserAnalytics();

        return view('hub-owner.users.index', compact('users', 'analytics', 'bannedUserIds'));
    }

    public function show(User $user)
    {
        if (!$this->userHasBookedThisHub(auth()->user(), $user)) {
            abort(403, 'Unauthorized access to this user.');
        }
        
        $user->load(['bookings' => function($query) {
            $query->where('hub_owner_id', auth()->id())
                  ->orderBy('created_at', 'desc')->limit(10);
        }]);

        $bookingStats = [
            'total_bookings' => $user->bookings()->where('hub_owner_id', auth()->id())->count(),
            'confirmed_bookings' => $user->bookings()->where('hub_owner_id', auth()->id())->where('status', 'confirmed')->count(),
            'pending_bookings' => $user->bookings()->where('hub_owner_id', auth()->id())->where('status', 'pending')->count(),
            'cancelled_bookings' => $user->bookings()->where('hub_owner_id', auth()->id())->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()->where('hub_owner_id', auth()->id())->where('status', 'confirmed')->sum('amount'),
        ];

        $recentActivity = $user->bookings()
            ->where('hub_owner_id', auth()->id())
            ->with('floorPlan')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('hub-owner.users.show', compact('user', 'bookingStats', 'recentActivity'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'role' => 'sometimes|in:customer,hub_owner,admin'
        ]);

        $user->update([
            'status' => $request->status,
            'role' => $request->role ?? $user->role
        ]);

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function toggleBan(User $user)
    {
        $hubOwner = auth()->user();

        if ($user->id === $hubOwner->id) {
            abort(403);
        }

        if (!$this->userHasBookedThisHub($hubOwner, $user)) {
            abort(403, 'This user has no bookings with your workspace.');
        }

        $existing = HubUserBan::where('hub_owner_id', $hubOwner->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'User unbanned. They can book your workspace again.';
        } else {
            HubUserBan::create([
                'hub_owner_id' => $hubOwner->id,
                'user_id' => $user->id,
            ]);
            $message = 'User banned from booking your workspace.';
        }

        return redirect()->back()->with('success', $message);
    }

    private function userHasBookedThisHub(User $hubOwner, User $target): bool
    {
        return $target->bookings()->where(function ($q) use ($hubOwner) {
            $q->where('hub_owner_id', $hubOwner->id);
            if ($hubOwner->company) {
                $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($hubOwner->company) . '%']);
            }
        })->exists();
    }

    public function analytics()
    {
        $analytics = $this->getUserAnalytics();
        
        return view('hub-owner.users.analytics', compact('analytics'));
    }

    private function getUserAnalytics()
    {
        $now = Carbon::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sevenDaysAgo = $now->copy()->subDays(7);
        $hubOwnerId = auth()->id();

        // Only show users who have bookings with this hub owner
        $usersWithBookings = User::whereHas('bookings', function($q) use ($hubOwnerId) {
            $q->where('hub_owner_id', $hubOwnerId);
        });

        return [
            'total_users' => $usersWithBookings->count(),
            'active_users' => $usersWithBookings->where('status', 'active')->count(),
            'inactive_users' => $usersWithBookings->where('status', 'inactive')->count(),
            'suspended_users' => $usersWithBookings->where('status', 'suspended')->count(),
            
            'new_users_30_days' => $usersWithBookings->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'new_users_7_days' => $usersWithBookings->where('created_at', '>=', $sevenDaysAgo)->count(),
            
            'users_by_role' => $usersWithBookings->select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->pluck('count', 'role'),
            
            'registration_trends' => $usersWithBookings->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get(),
            
            'most_active_users' => $usersWithBookings->limit(10)->get(),
            
            'user_engagement' => [
                'users_with_bookings' => $usersWithBookings->count(),
                'users_without_bookings' => 0, // All users shown have bookings with this hub owner
                'avg_bookings_per_user' => 0, // Simplified to avoid SQL issues
            ],
            
            // Booking statistics for the last 30 days
            'confirmed_bookings_30_days' => \App\Models\Booking::where('hub_owner_id', $hubOwnerId)
                ->where('status', 'confirmed')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
            'pending_bookings_30_days' => \App\Models\Booking::where('hub_owner_id', $hubOwnerId)
                ->where('status', 'pending')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
            'cancelled_bookings_30_days' => \App\Models\Booking::where('hub_owner_id', $hubOwnerId)
                ->where('status', 'cancelled')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
            
            // Booking trend calculation (comparing last 15 days vs previous 15 days)
            'booking_trend' => $this->calculateBookingTrend($hubOwnerId, $thirtyDaysAgo),
            
            // Weekly booking data for the last 7 days
            'weekly_booking_data' => $this->getWeeklyBookingData($hubOwnerId)
        ];
    }

    private function calculateBookingTrend($hubOwnerId, $thirtyDaysAgo)
    {
        $fifteenDaysAgo = Carbon::now()->subDays(15);
        
        // Bookings in the last 15 days
        $recentBookings = \App\Models\Booking::where('hub_owner_id', $hubOwnerId)
            ->where('created_at', '>=', $fifteenDaysAgo)
            ->count();
            
        // Bookings in the previous 15 days (15-30 days ago)
        $previousBookings = \App\Models\Booking::where('hub_owner_id', $hubOwnerId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('created_at', '<', $fifteenDaysAgo)
            ->count();
            
        if ($previousBookings == 0) {
            return $recentBookings > 0 ? 100 : 0;
        }
        
        $trend = (($recentBookings - $previousBookings) / $previousBookings) * 100;
        return round($trend, 1);
    }
    
    private function getWeeklyBookingData($hubOwnerId)
    {
        $weeklyData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = \App\Models\Booking::where('hub_owner_id', $hubOwnerId)
                ->whereDate('created_at', $date)
                ->count();
            $weeklyData[] = $count;
        }
        
        return $weeklyData;
    }
}
