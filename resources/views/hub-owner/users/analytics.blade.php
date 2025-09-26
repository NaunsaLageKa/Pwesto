@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-60 bg-white border-r flex flex-col py-8 px-4 min-h-screen">
        <div class="text-2xl font-bold mb-10 tracking-tight">{{ strtoupper(auth()->user()->company ?? 'HUB') }} HUB</div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li><a href="{{ route('hub-owner.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7m6 0v6m0 0H7m6 0h6"/></svg>Dashboard</a></li>
                <li><a href="{{ route('hub-owner.bookings.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m0-5V3m-8 9v6a2 2 0 002 2h4a2 2 0 002-2v-6"/></svg>Bookings</a></li>
                <li><a href="{{ route('hub-owner.users.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.847.607 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Users</a></li>
                <li><a href="{{ route('hub-owner.floor-plan') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Floor Plan</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center">
                <a href="{{ route('hub-owner.users.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold">User Analytics</h1>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Total Users</div>
                <div class="text-3xl font-bold">{{ $analytics['total_users'] }}</div>
                <div class="text-sm text-gray-500 mt-1">All time</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Active Users</div>
                <div class="text-3xl font-bold text-green-600">{{ $analytics['active_users'] }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ number_format(($analytics['active_users'] / max($analytics['total_users'], 1)) * 100, 1) }}% of total</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">New This Month</div>
                <div class="text-3xl font-bold text-blue-600">{{ $analytics['new_users_30_days'] }}</div>
                <div class="text-sm text-gray-500 mt-1">Last 30 days</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Engagement Rate</div>
                <div class="text-3xl font-bold text-purple-600">{{ number_format(($analytics['user_engagement']['users_with_bookings'] / max($analytics['total_users'], 1)) * 100, 1) }}%</div>
                <div class="text-sm text-gray-500 mt-1">Users with bookings</div>
            </div>
        </div>

        <!-- User Status Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border">
                <h3 class="text-lg font-semibold mb-4">User Status Distribution</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium">Active</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">{{ $analytics['active_users'] }}</div>
                            <div class="text-sm text-gray-500">{{ number_format(($analytics['active_users'] / max($analytics['total_users'], 1)) * 100, 1) }}%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium">Inactive</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">{{ $analytics['inactive_users'] }}</div>
                            <div class="text-sm text-gray-500">{{ number_format(($analytics['inactive_users'] / max($analytics['total_users'], 1)) * 100, 1) }}%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                            <span class="text-sm font-medium">Suspended</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">{{ $analytics['suspended_users'] }}</div>
                            <div class="text-sm text-gray-500">{{ number_format(($analytics['suspended_users'] / max($analytics['total_users'], 1)) * 100, 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border">
                <h3 class="text-lg font-semibold mb-4">User Roles</h3>
                <div class="space-y-4">
                    @foreach($analytics['users_by_role'] as $role => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 {{ $role == 'admin' ? 'bg-red-500' : ($role == 'hub_owner' ? 'bg-blue-500' : 'bg-green-500') }} rounded-full mr-3"></div>
                            <span class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">{{ $count }}</div>
                            <div class="text-sm text-gray-500">{{ number_format(($count / max($analytics['total_users'], 1)) * 100, 1) }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Registration Trends Chart -->
        <div class="bg-white p-6 rounded-lg shadow border mb-8">
            <h3 class="text-lg font-semibold mb-4">Registration Trends (Last 30 Days)</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="registrationChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Most Active Users -->
        <div class="bg-white rounded-lg shadow border mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Most Active Users</h3>
            </div>
            <div class="p-6">
                @if($analytics['most_active_users']->count() > 0)
                    <div class="space-y-4">
                        @foreach($analytics['most_active_users'] as $user)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                @if($user->profile_image)
                                    <img class="h-10 w-10 rounded-full mr-4" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                        <span class="text-gray-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-semibold">{{ $user->bookings_count }} bookings</div>
                                <div class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-sm">No user data available.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Engagement Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow border">
                <h4 class="text-lg font-semibold mb-4">User Engagement</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Users with bookings</span>
                        <span class="font-semibold">{{ $analytics['user_engagement']['users_with_bookings'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Users without bookings</span>
                        <span class="font-semibold">{{ $analytics['user_engagement']['users_without_bookings'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Avg bookings per user</span>
                        <span class="font-semibold">{{ number_format($analytics['user_engagement']['avg_bookings_per_user'], 1) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border">
                <h4 class="text-lg font-semibold mb-4">Growth Metrics</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">New users (7 days)</span>
                        <span class="font-semibold text-blue-600">{{ $analytics['new_users_7_days'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">New users (30 days)</span>
                        <span class="font-semibold text-blue-600">{{ $analytics['new_users_30_days'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Growth rate</span>
                        <span class="font-semibold text-green-600">
                            @if($analytics['total_users'] > 0)
                                {{ number_format(($analytics['new_users_30_days'] / max($analytics['total_users'] - $analytics['new_users_30_days'], 1)) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border">
                <h4 class="text-lg font-semibold mb-4">Quick Actions</h4>
                <div class="space-y-3">
                    <a href="{{ route('hub-owner.users.index') }}" class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700">
                        View All Users
                    </a>
                    <a href="{{ route('hub-owner.users.index', ['status' => 'inactive']) }}" class="block w-full bg-gray-600 text-white text-center py-2 px-4 rounded-lg hover:bg-gray-700">
                        View Inactive Users
                    </a>
                    <a href="{{ route('hub-owner.users.index', ['role' => 'customer']) }}" class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-lg hover:bg-green-700">
                        View Customers
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Registration Trends Chart
const ctx = document.getElementById('registrationChart').getContext('2d');
const registrationData = @json($analytics['registration_trends']);

const labels = registrationData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});

const data = registrationData.map(item => item.count);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'New Registrations',
            data: data,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection
