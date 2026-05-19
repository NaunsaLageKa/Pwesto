<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hubOwner = $request->user();
        $baseQuery = $this->hubBookingsQuery($hubOwner);

        $totalBookings = (clone $baseQuery)->count();
        $totalUsers = User::whereHas('bookings', function ($q) use ($hubOwner) {
            $this->applyHubScope($q, $hubOwner);
        })->count();
        $revenue = (clone $baseQuery)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('amount');
        $recentBookings = (clone $baseQuery)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        $chartView = $request->input('chart_view', 'month') === 'year' ? 'year' : 'month';
        $chartYear = (int) $request->input('chart_year', now()->year);
        $chartMonth = (int) $request->input('chart_month', now()->month);
        $chartYear = max(2020, min($chartYear, now()->year + 1));
        $chartMonth = max(1, min($chartMonth, 12));

        $availableYears = (clone $baseQuery)
            ->whereNotNull('booking_date')
            ->get(['booking_date'])
            ->pluck('booking_date')
            ->map(fn ($d) => (int) $d->format('Y'))
            ->unique()
            ->sort()
            ->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        $bookingsChart = $this->buildBookingsChart($baseQuery, $chartView, $chartYear, $chartMonth);

        return view('hub-owner.dashboard', compact(
            'totalBookings',
            'totalUsers',
            'revenue',
            'recentBookings',
            'hubOwner',
            'bookingsChart',
            'chartView',
            'chartYear',
            'chartMonth',
            'availableYears',
        ));
    }

    private function hubBookingsQuery($hubOwner): Builder
    {
        return Booking::query()->where(function ($q) use ($hubOwner) {
            $this->applyHubScope($q, $hubOwner);
        });
    }

    private function applyHubScope(Builder $q, $hubOwner): void
    {
        $q->where('hub_owner_id', $hubOwner->id);
        if ($hubOwner->company) {
            $q->orWhereRaw('LOWER(hub_name) LIKE ?', ['%' . strtolower($hubOwner->company) . '%']);
        }
    }

    private function buildBookingsChart(Builder $baseQuery, string $view, int $year, int $month): array
    {
        $counted = (clone $baseQuery)
            ->whereNotNull('booking_date')
            ->whereNotIn('status', ['cancelled'])
            ->get(['booking_date']);

        if ($view === 'year') {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfDay();
            $inPeriod = $counted->filter(fn ($b) => $b->booking_date->between($start, $end));
            $byMonth = $inPeriod->countBy(fn ($b) => (int) $b->booking_date->format('n'));

            $labels = [];
            $data = [];
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::create($year, $m, 1)->format('M');
                $data[] = (int) ($byMonth[$m] ?? 0);
            }

            $total = array_sum($data);

            return [
                'labels' => $labels,
                'data' => $data,
                'title' => "Total booked in {$year}",
                'subtitle' => 'Bookings per month',
                'total' => $total,
            ];
        }

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $inPeriod = $counted->filter(fn ($b) => $b->booking_date->between($start, $end));
        $byDay = $inPeriod->countBy(fn ($b) => (int) $b->booking_date->format('j'));

        $daysInMonth = $start->daysInMonth;
        $labels = [];
        $data = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $labels[] = (string) $d;
            $data[] = (int) ($byDay[$d] ?? 0);
        }

        $monthName = $start->format('F');

        return [
            'labels' => $labels,
            'data' => $data,
            'title' => "Total booked in {$monthName} {$year}",
            'subtitle' => 'Bookings per day',
            'total' => array_sum($data),
        ];
    }
}
