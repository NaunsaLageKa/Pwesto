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
    private const TREND_DAYS = 30;

    /** Stack / legend order (bottom → top on chart). */
    private const ROLE_ORDER = ['customer', 'hub_owner', 'admin'];

    public function index()
    {
        $trendStart = Carbon::now()->subDays(self::TREND_DAYS);

        // User sign-ups by role (last 30 days) — combined chart
        $userActivity = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderByDesc('count')
            ->get();

        $signupsByRole = User::query()
            ->selectRaw('DATE(created_at) as date, role, COUNT(*) as count')
            ->where('created_at', '>=', $trendStart)
            ->groupBy(DB::raw('DATE(created_at)'), 'role')
            ->orderBy('date')
            ->get();

        $userSignupsChart = $this->buildSignupsByRoleChart(
            $signupsByRole,
            $this->sortRoles($userActivity->pluck('role'))
        );

        // High-demand hubs (bookings per workspace, last 30 days)
        $hubDemandRows = Booking::query()
            ->select('hub_name')
            ->selectRaw('COUNT(*) as count')
            ->where('created_at', '>=', $trendStart)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereNotNull('hub_name')
            ->where('hub_name', '!=', '')
            ->groupBy('hub_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Highest demand first (top of horizontal bar chart)
        $hubDemandChart = [
            'labels' => $hubDemandRows->pluck('hub_name')->reverse()->values()->all(),
            'data' => $hubDemandRows->pluck('count')->map(fn ($c) => (int) $c)->reverse()->values()->all(),
        ];

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
            'userSignupsChart',
            'hubDemandChart',
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

    /**
     * @param  \Illuminate\Support\Collection<int, object{date: string, role: string, count: int|string}>  $rows
     * @param  \Illuminate\Support\Collection<int, string>  $roles
     * @return array{labels: list<string>, datasets: list<array{label: string, data: list<int>}>}
     */
    private function buildSignupsByRoleChart($rows, $roles, int $days = self::TREND_DAYS): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = Carbon::today()->subDays($i)->toDateString();
        }

        $byRoleDate = collect($rows)->groupBy('role')->map(function ($roleRows) {
            return $roleRows->mapWithKeys(function ($row) {
                $key = Carbon::parse($row->date)->toDateString();

                return [$key => (int) $row->count];
            });
        });

        $palette = [
            'admin' => 'rgb(245, 158, 11)',
            'hub_owner' => 'rgb(16, 185, 129)',
            'customer' => 'rgb(59, 130, 246)',
        ];

        $datasets = [];
        foreach ($roles as $role) {
            $roleKey = (string) $role;
            $dateMap = $byRoleDate->get($roleKey, collect());
            $data = array_map(fn (string $date) => (int) $dateMap->get($date, 0), $labels);
            $color = $palette[$roleKey] ?? 'rgb(139, 92, 246)';

            $datasets[] = [
                'label' => ucwords(str_replace('_', ' ', $roleKey)),
                'data' => $data,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'borderWidth' => 1,
            ];
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, string>  $roles
     * @return list<string>
     */
    private function sortRoles($roles): array
    {
        $unique = $roles->unique()->values();

        $ordered = collect(self::ROLE_ORDER)
            ->filter(fn (string $role) => $unique->contains($role))
            ->values();

        $extras = $unique
            ->diff(self::ROLE_ORDER)
            ->sort()
            ->values();

        return $ordered->merge($extras)->all();
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
