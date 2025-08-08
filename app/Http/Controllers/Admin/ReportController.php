<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\FloorPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // User growth over time
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Booking trends
        $bookingTrends = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User activity by role
        $userActivity = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        // Recent activity
        $recentBookings = Booking::with('user', 'hubOwner')
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::latest()->take(10)->get();

        // System performance stats
        $totalUsers = User::count();
        $totalHubOwners = User::where('role', 'hub_owner')->count();
        $totalBookings = Booking::count();
        $totalFloorPlans = FloorPlan::count();
        $pendingApprovals = User::where('role', 'hub_owner')->where('status', 'pending')->count();

        return view('admin.reports.index', compact(
            'userGrowth',
            'bookingTrends', 
            'userActivity',
            'recentBookings',
            'recentUsers',
            'totalUsers',
            'totalHubOwners',
            'totalBookings',
            'totalFloorPlans',
            'pendingApprovals'
        ));
    }

    public function exportUsers()
    {
        $users = User::all(['name', 'email', 'role', 'status', 'created_at']);
        
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Role', 'Status', 'Created At']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->status,
                    $user->created_at
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportBookings()
    {
        $bookings = Booking::with('user', 'hubOwner')->get();
        
        $filename = 'bookings_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User', 'Hub Owner', 'Status', 'Created At']);
            
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->user->name ?? 'N/A',
                    $booking->hubOwner->name ?? 'N/A',
                    $booking->status,
                    $booking->created_at
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
