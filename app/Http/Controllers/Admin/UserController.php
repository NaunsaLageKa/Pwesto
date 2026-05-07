<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\FloorPlan;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $hubOwnerStats = $this->getHubOwnerStats();
        $totalBookings = Booking::count();
        $totalFloorPlans = FloorPlan::count();
        $recentUsers = User::latest()->take(5)->get();
        $peakUsageTimes = $this->getPeakUsageTimes();
        $highDemandLocations = $this->getHighDemandLocations();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'hubOwnerStats',
            'totalBookings',
            'totalFloorPlans',
            'recentUsers',
            'peakUsageTimes',
            'highDemandLocations'
        ));
    }

    protected function getHubOwnerStats(): array
    {
        return [
            'total' => User::where('role', 'hub_owner')->count(),
            'pending' => User::where('role', 'hub_owner')->where('status', 'pending')->count(),
        ];
    }

    private function getPeakUsageTimes()
    {
        return Booking::query()
            ->select('booking_time')
            ->selectRaw('COUNT(*) as booking_count')
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereNotNull('booking_time')
            ->groupBy('booking_time')
            ->orderByDesc('booking_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $row->time_label = date('g:i A', strtotime((string) $row->booking_time));
                return $row;
            });
    }

    private function getHighDemandLocations()
    {
        return Booking::query()
            ->select('hub_name')
            ->selectRaw('COUNT(*) as booking_count')
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereNotNull('hub_name')
            ->where('hub_name', '!=', '')
            ->groupBy('hub_name')
            ->orderByDesc('booking_count')
            ->limit(5)
            ->get();
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }


        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }


        if ($request->filled('company')) {
            $company = $request->input('company');
            $query->where('company', 'like', "%$company%");
        }

    
        $users = $query->orderBy('created_at', 'desc')
                       ->paginate(15)
                       ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,hub_owner,admin,company',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return $this->successRedirect('User role updated.');
    }

    public function toggleBan($id)
    {
        $user = User::findOrFail($id);

        if ($user->status === 'banned') {
            $user->status = 'approved';
        } else {
            $user->status = 'banned';
        }

        $user->save();
        return $this->successRedirect('User status updated.');
    }

    public function approve($id)
    {
        $user = User::where('role', 'hub_owner')->findOrFail($id);
        $user->status = 'approved';
        $user->save();
        return $this->successRedirect('Hub owner approved.');
    }

    public function reject($id)
    {
        $user = User::where('role', 'hub_owner')->findOrFail($id);
        $user->status = 'rejected';
        $user->save();
        return $this->successRedirect('Hub owner rejected.');
    }
}