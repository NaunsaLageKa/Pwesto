<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HubOwnerUserController extends Controller
{
    public function index(Request $request)
    {
        // Only show users who have bookings with this hub owner
        $query = User::whereHas('bookings', function($q) {
            $q->where('hub_owner_id', auth()->id());
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

        $users = $query->withCount('bookings')
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        // Get analytics data
        $analytics = $this->getUserAnalytics();

        return view('hub-owner.users.index', compact('users', 'analytics'));
    }

    public function show(User $user)
    {
        // Ensure this user has bookings with the current hub owner
        if (!$user->bookings()->where('hub_owner_id', auth()->id())->exists()) {
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
            ->groupBy('date')
            ->orderBy('date')
            ->get(),
            
            'most_active_users' => $usersWithBookings->withCount(['bookings' => function($q) use ($hubOwnerId) {
                $q->where('hub_owner_id', $hubOwnerId);
            }])
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get(),
            
            'user_engagement' => [
                'users_with_bookings' => $usersWithBookings->count(),
                'users_without_bookings' => 0, // All users shown have bookings with this hub owner
                'avg_bookings_per_user' => $usersWithBookings->withCount(['bookings' => function($q) use ($hubOwnerId) {
                    $q->where('hub_owner_id', $hubOwnerId);
                }])->get()->avg('bookings_count'),
            ]
        ];
    }
}
