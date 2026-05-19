@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 min-h-screen">
            <div class="p-6">
                <div class="text-sm font-bold mb-1" style="color: #19c2b8;">PWESTO</div>
                <div class="text-xl font-bold text-yellow-400 mb-8">{{ strtoupper($hubOwner->company ?? 'HUB') }} HUB</div>
                <nav class="space-y-1">
                    <a href="#" class="block px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg">
                        Dashboard
                    </a>
                    <a href="{{ route('hub-owner.bookings.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Bookings
                    </a>
                    <a href="{{ route('hub-owner.users.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Users
                    </a>
                    <a href="{{ route('hub-owner.feedback.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Reviews
                    </a>
                    <a href="{{ route('hub-owner.floor-plan') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Floor Plan
                    </a>
                </nav>
                <div class="mt-6 pt-4 border-t border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome to your {{ $hubOwner->company ?? 'Hub' }} dashboard</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-xl font-bold text-gray-900">{{ $totalBookings ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-50">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total users</p>
                            <p class="text-xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-purple-50">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Revenue</p>
                            <p class="text-xl font-bold text-gray-900">₱{{ number_format($revenue ?? 0, 2) }}</p>
                        </div>
            </div>
        </div>
            </div>
            <!-- Recent Bookings Table -->
            <div class="bg-white rounded-lg border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
                        <a href="{{ route('hub-owner.bookings.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View All</a>
                    </div>
            </div>
            @if($recentBookings && $recentBookings->count() > 0)
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($recentBookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->booking_date->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">₱{{ number_format($booking->amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->status === 'confirmed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Confirmed
                                        </span>
                                    @elseif($booking->status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Pending
                                        </span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Cancelled
                                        </span>
                                    @elseif($booking->status === 'completed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Completed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('hub-owner.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-1.5 rounded-md border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                            👁 View
                                        </a>
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700 transition-colors">
                                                    ✓ Confirm
                                                </button>
                                            </form>
                                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md border border-red-300 text-red-700 hover:bg-red-50 transition-colors">
                                                    ✕ Cancel
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'confirmed')
                                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                                    ✓ Mark Complete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($recentBookings->hasPages())
                    <div class="mt-4">
                        {{ $recentBookings->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-sm">No recent bookings.</div>
                </div>
            @endif
        </div>
            <!-- Total booked chart -->
            <div class="bg-white rounded-lg border border-gray-200 mt-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ $bookingsChart['title'] ?? 'Total booked' }}</h2>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $bookingsChart['subtitle'] ?? '' }}</p>
                            <p class="mt-2 text-2xl font-bold text-teal-600">{{ $bookingsChart['total'] ?? 0 }} <span class="text-sm font-medium text-gray-500">booking{{ ($bookingsChart['total'] ?? 0) === 1 ? '' : 's' }}</span></p>
                        </div>
                        <form method="GET" action="{{ route('hub-owner.dashboard') }}" class="flex flex-wrap items-end gap-2">
                            <div>
                                <label for="chart_view" class="block text-xs font-medium text-gray-500 mb-1">View</label>
                                <select name="chart_view" id="chart_view" class="rounded-md border border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="month" @selected(($chartView ?? 'month') === 'month')>By month (daily)</option>
                                    <option value="year" @selected(($chartView ?? '') === 'year')>By year (monthly)</option>
                                </select>
                            </div>
                            <div>
                                <label for="chart_month" class="block text-xs font-medium text-gray-500 mb-1">Month</label>
                                <select name="chart_month" id="chart_month" class="rounded-md border border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" @disabled(($chartView ?? 'month') === 'year')>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected(($chartMonth ?? now()->month) == $m)>{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="chart_year" class="block text-xs font-medium text-gray-500 mb-1">Year</label>
                                <select name="chart_year" id="chart_year" class="rounded-md border border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($availableYears ?? [now()->year] as $y)
                                        <option value="{{ $y }}" @selected(($chartYear ?? now()->year) == $y)>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply</button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <div class="h-72">
                        <canvas id="hubBookingsChart" aria-label="Total bookings chart"></canvas>
                    </div>
                </div>
            </div>
    </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const chartView = document.getElementById('chart_view');
    const chartMonth = document.getElementById('chart_month');
    if (chartView && chartMonth) {
        chartView.addEventListener('change', function () {
            chartMonth.disabled = chartView.value === 'year';
        });
    }

    const bookingsChart = @json($bookingsChart ?? ['labels' => [], 'data' => [], 'title' => 'Total booked']);
    const canvas = document.getElementById('hubBookingsChart');
    if (!canvas) return;

    if (!bookingsChart.labels || !bookingsChart.labels.length) {
        canvas.parentElement.innerHTML = '<p class="text-sm text-gray-500 text-center py-16">No bookings for this period.</p>';
        return;
    }

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: bookingsChart.labels,
            datasets: [{
                label: 'Bookings',
                data: bookingsChart.data,
                backgroundColor: 'rgba(13, 148, 136, 0.75)',
                borderColor: 'rgb(13, 148, 136)',
                borderWidth: 1,
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.parsed.y + ' booking' + (ctx.parsed.y === 1 ? '' : 's');
                        },
                    },
                },
            },
            scales: {
                x: { ticks: { maxRotation: 45, autoSkip: true, maxTicksLimit: 16 } },
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
            },
        },
    });
})();
</script>
@endsection
