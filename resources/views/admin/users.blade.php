@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <a href="{{ route('admin.dashboard') }}" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-6">User Management</h1>
    <form method="GET" action="" class="flex flex-wrap gap-4 mb-6 items-center bg-white p-4 rounded-lg shadow">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email" class="border rounded px-3 py-2 flex-1 min-w-[200px]" />
        <select name="role" class="border rounded px-3 py-2">
            <option value="">All Roles</option>
            <option value="user" @if(request('role')=='user') selected @endif>User</option>
            <option value="hub_owner" @if(request('role')=='hub_owner') selected @endif>Hub Owner</option>
            <option value="admin" @if(request('role')=='admin') selected @endif>Admin</option>
        </select>
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition" type="submit">Filter</button>
        <a href="{{ route('admin.users') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">Clear</a>
    </form>
    <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Name</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Email</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Phone</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Company</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Company ID</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Role</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-2 px-4">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">{{ $user->phone }}</td>
                    <td class="py-2 px-4">@if($user->role == 'hub_owner'){{ $user->company }}@else<span class="text-gray-400">N/A</span>@endif</td>
                    <td class="py-2 px-4">@if($user->role == 'hub_owner' && $user->company_id)<a href="{{ asset('storage/' . $user->company_id) }}" target="_blank" class="text-blue-600 underline hover:text-blue-800">View</a>@elseif($user->role == 'hub_owner')<span class="text-gray-400">N/A</span>@else<span class="text-gray-400">N/A</span>@endif</td>
                    <td class="py-2 px-4 capitalize">
                        <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <select name="role" onchange="this.form.submit()" class="border rounded px-2 py-1 text-xs">
                                <option value="user" @if($user->role=='user') selected @endif>User</option>
                                <option value="hub_owner" @if($user->role=='hub_owner') selected @endif>Hub Owner</option>
                                <option value="admin" @if($user->role=='admin') selected @endif>Admin</option>
                            </select>
                        </form>
                    </td>
                    <td class="py-2 px-4 capitalize">
                        @if($user->status == 'banned')
                            <span class="inline-block bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold">Banned</span>
                        @else
                            <span class="inline-block bg-green-600 text-white px-2 py-1 rounded text-xs font-semibold">Active</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        @if($user->role == 'hub_owner' && $user->status == 'pending')
                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 transition" type="submit">Approve</button>
                            </form>
                            <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 transition" type="submit">Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.users.toggleBan', $user->id) }}" method="POST" class="inline ml-2">
                            @csrf
                            @if($user->status == 'banned')
                                <button class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 transition" type="submit">Unban</button>
                            @else
                                <button class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 transition" type="submit">Ban</button>
                            @endif
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-4 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
