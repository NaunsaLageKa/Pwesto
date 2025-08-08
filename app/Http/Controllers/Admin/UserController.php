<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\FloorPlan;

class UserController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalHubOwners = User::where('role', 'hub_owner')->count();
        $pendingHubOwners = User::where('role', 'hub_owner')->where('status', 'pending')->count();
        $totalBookings = Booking::count();
        $totalFloorPlans = FloorPlan::count();
        $recentUsers = User::latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalHubOwners', 
            'pendingHubOwners',
            'totalBookings',
            'totalFloorPlans',
            'recentUsers'
        ));
    }

    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,hub_owner,admin',
        ]);
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return redirect()->back()->with('success', 'User role updated.');
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
        return redirect()->back()->with('success', 'User status updated.');
    }

    public function approve($id)
    {
        $user = User::where('role', 'hub_owner')->findOrFail($id);
        $user->status = 'approved';
        $user->save();
        return redirect()->back()->with('success', 'Hub owner approved.');
    }

    public function reject($id)
    {
        $user = User::where('role', 'hub_owner')->findOrFail($id);
        $user->status = 'rejected';
        $user->save();
        return redirect()->back()->with('success', 'Hub owner rejected.');
    }
} 