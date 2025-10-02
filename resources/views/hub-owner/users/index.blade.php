@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 min-h-screen">
            <div class="p-6">
                <div class="text-sm font-bold mb-1" style="color: #19c2b8;">PWESTO</div>
                <div class="text-xl font-bold text-yellow-400 mb-8">{{ strtoupper(auth()->user()->company ?? 'HUB') }} HUB</div>
                <nav class="space-y-1">
                    <a href="{{ route('hub-owner.dashboard') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('hub-owner.bookings.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Bookings
                    </a>
                    <a href="{{ route('hub-owner.users.index') }}" class="block px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg">
                        Users
                    </a>
                    <a href="{{ route('hub-owner.floor-plan') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Floor Plan
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="text-gray-600 mt-1">Manage and track all users</p>
            </div>


        <!-- Search -->
        <div class="bg-white p-4 rounded-lg shadow mb-6 border">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Search</button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Users ({{ $users->total() }} total)</h2>
            </div>
            
            @if($users->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : ($user->role == 'hub_owner' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : ($user->status == 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('hub-owner.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400">No users found.</div>
                </div>
            @endif
        </div>
    </main>
</div>

@endsection
