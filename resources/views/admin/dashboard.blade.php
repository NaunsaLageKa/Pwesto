@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10">
    <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <a href="{{ route('admin.users') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex flex-col items-center border">
            <svg class="w-10 h-10 text-blue-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <div class="text-xl font-semibold">User Management</div>
            <div class="text-gray-500 mt-1 text-sm">Manage all users and hub owners</div>
        </a>
        <a href="#" class="bg-white p-6 rounded-lg shadow flex flex-col items-center border opacity-60 cursor-not-allowed">
            <svg class="w-10 h-10 text-yellow-500 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-6a2 2 0 00-2 2v10z"/></svg>
            <div class="text-xl font-semibold">Analytics</div>
            <div class="text-gray-500 mt-1 text-sm">Coming soon</div>
        </a>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border">
        <h2 class="text-xl font-semibold mb-4">Quick Stats</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-gray-500">Total Users</div>
                <div class="text-2xl font-bold">{{ $totalUsers ?? '-' }}</div>
            </div>
            <div class="text-center">
                <div class="text-gray-500">Total Hub Owners</div>
                <div class="text-2xl font-bold">{{ $totalHubOwners ?? '-' }}</div>
            </div>
            <div class="text-center">
                <div class="text-gray-500">Pending Hub Owners</div>
                <div class="text-2xl font-bold">{{ $pendingHubOwners ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 