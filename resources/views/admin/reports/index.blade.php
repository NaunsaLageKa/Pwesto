@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-6">Reports & Analytics</h1>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border">
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
        
        <div class="bg-white p-6 rounded-lg shadow border">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hub Owners</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalHubOwners ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow border">
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
        
        <div class="bg-white p-6 rounded-lg shadow border">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pendingApprovals ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="bg-white p-6 rounded-lg shadow border mb-8">
        <h2 class="text-xl font-semibold mb-4">Export Data</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.reports.export-users') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Users
            </a>
            <a href="{{ route('admin.reports.export-bookings') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Bookings
            </a>
        </div>
    </div>

    <!-- User Activity Chart -->
    @if(isset($userActivity) && $userActivity->count() > 0)
    <div class="bg-white p-6 rounded-lg shadow border mb-8">
        <h2 class="text-xl font-semibold mb-4">User Activity by Role</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($userActivity as $activity)
            <div class="text-center p-4 bg-gray-50 rounded">
                <div class="text-2xl font-bold text-gray-900">{{ $activity->count }}</div>
                <div class="text-sm text-gray-600 capitalize">{{ $activity->role }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Bookings -->
        @if(isset($recentBookings) && $recentBookings->count() > 0)
        <div class="bg-white p-6 rounded-lg shadow border">
            <h2 class="text-xl font-semibold mb-4">Recent Bookings</h2>
            <div class="space-y-3">
                @foreach($recentBookings as $booking)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <div class="font-medium text-gray-900">{{ $booking->user->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ $booking->hubOwner->name ?? 'N/A' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($booking->status) }}</div>
                        <div class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Users -->
        @if(isset($recentUsers) && $recentUsers->count() > 0)
        <div class="bg-white p-6 rounded-lg shadow border">
            <h2 class="text-xl font-semibold mb-4">Recent Users</h2>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 capitalize">{{ $user->role }}</div>
                        <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
