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
                <li><a href="{{ route('hub-owner.users.index') }}" class="flex items-center px-3 py-2 rounded-lg bg-blue-50 text-blue-700 font-semibold"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.847.607 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Users</a></li>
                <li><a href="{{ route('hub-owner.floor-plan') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Floor Plan</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">User Management</h1>
            <a href="{{ route('hub-owner.users.analytics') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>
        </div>

        <!-- Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Total Users</div>
                <div class="text-2xl font-bold">{{ $analytics['total_users'] }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Active Users</div>
                <div class="text-2xl font-bold text-green-600">{{ $analytics['active_users'] }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">New This Month</div>
                <div class="text-2xl font-bold text-blue-600">{{ $analytics['new_users_30_days'] }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Engagement Rate</div>
                <div class="text-2xl font-bold text-purple-600">{{ number_format(($analytics['user_engagement']['users_with_bookings'] / max($analytics['total_users'], 1)) * 100, 1) }}%</div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white p-6 rounded-lg shadow mb-6 border">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Roles</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="hub_owner" {{ request('role') == 'hub_owner' ? 'selected' : '' }}>Hub Owner</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Filter</button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Users ({{ $users->total() }} total)</h2>
            </div>
            
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->profile_image)
                                                <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : 
                                           ($user->role == 'hub_owner' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 
                                           ($user->status == 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->bookings_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('hub-owner.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        <button onclick="openStatusModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->status }}', '{{ $user->role }}')" class="text-green-600 hover:text-green-900">Manage</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-sm">No users found matching your criteria.</div>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Update User Status</h3>
            <form id="statusForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">User: <span id="userName"></span></label>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="statusSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="roleSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="customer">Customer</option>
                        <option value="hub_owner">Hub Owner</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openStatusModal(userId, userName, currentStatus, currentRole) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('statusSelect').value = currentStatus;
    document.getElementById('roleSelect').value = currentRole;
    document.getElementById('statusForm').action = `/hub-owner/users/${userId}/status`;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
</script>
@endsection
