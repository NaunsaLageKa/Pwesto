@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-cyan-50 to-indigo-100 py-10">
<div class="container mx-auto">
    <div class="flex items-center justify-between mb-8 bg-white/70 backdrop-blur-sm border border-white rounded-2xl px-6 py-5 shadow-sm">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-cyan-600 to-indigo-700 bg-clip-text text-transparent">Admin</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 shadow-sm transition-colors">
                Log Out
            </button>
        </form>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl shadow border border-blue-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 rounded-xl shadow border border-emerald-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hub Owners</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $hubOwnerStats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-6 rounded-xl shadow border border-amber-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $hubOwnerStats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-violet-50 to-violet-100 p-6 rounded-xl shadow border border-violet-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalBookings ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.users') }}" class="bg-white/90 p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col items-center border border-blue-100 hover:border-blue-300">
            <svg class="w-10 h-10 text-blue-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <div class="text-xl font-semibold">User Management</div>
            <div class="text-gray-500 mt-1 text-sm">Manage all users and hub owners</div>
        </a>
        
        <a href="{{ route('admin.reviews.index') }}" class="bg-white/90 p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col items-center border border-green-100 hover:border-green-300">
            <svg class="w-10 h-10 text-green-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <div class="text-xl font-semibold">Review Moderation</div>
            <div class="text-gray-500 mt-1 text-sm">Platform moderation &amp; guest workspace reviews (public home)</div>
        </a>
        
        <a href="{{ route('admin.reports.index') }}" class="bg-white/90 p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col items-center border border-yellow-100 hover:border-yellow-300">
            <svg class="w-10 h-10 text-yellow-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <div class="text-xl font-semibold">Reports & Analytics</div>
            <div class="text-gray-500 mt-1 text-sm">Generate reports and view trends</div>
        </a>
        
        <a href="{{ route('admin.disputes.index') }}" class="bg-white/90 p-6 rounded-xl shadow hover:shadow-lg transition flex flex-col items-center border border-rose-100 hover:border-rose-300">
            <svg class="w-10 h-10 text-red-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div class="text-xl font-semibold">Dispute Resolution</div>
            <div class="text-gray-500 mt-1 text-sm">Resolve conflicts and ensure compliance</div>
        </a>
    </div>

    <!-- Usage Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white/90 p-6 rounded-xl shadow border border-cyan-100">
            <h2 class="text-xl font-semibold mb-4">Peak Usage Times</h2>
            <p class="text-sm text-gray-500 mb-4">Most booked time slots across active bookings.</p>
            <div class="space-y-3">
                @forelse(($peakUsageTimes ?? collect()) as $timeSlot)
                    <div class="flex items-center justify-between border rounded-lg px-4 py-3 bg-gradient-to-r from-white to-blue-50">
                        <span class="font-medium text-gray-800">{{ $timeSlot->time_label }}</span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                            {{ $timeSlot->booking_count }} bookings
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No booking data available yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white/90 p-6 rounded-xl shadow border border-emerald-100">
            <h2 class="text-xl font-semibold mb-4">High-Demand Locations</h2>
            <p class="text-sm text-gray-500 mb-4">Top hub locations by booking volume.</p>
            <div class="space-y-3">
                @forelse(($highDemandLocations ?? collect()) as $location)
                    <div class="flex items-center justify-between border rounded-lg px-4 py-3 bg-gradient-to-r from-white to-emerald-50">
                        <span class="font-medium text-gray-800">{{ $location->hub_name }}</span>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                            {{ $location->booking_count }} bookings
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No location demand data available yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    @if(isset($recentUsers) && $recentUsers->count() > 0)
    <div class="bg-white/90 p-6 rounded-xl shadow border border-slate-200">
        <h2 class="text-xl font-semibold mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentUsers as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $user->role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->status == 'approved')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @elseif($user->status == 'pending')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($user->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
</div>
@endsection 