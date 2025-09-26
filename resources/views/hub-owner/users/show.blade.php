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
            <div class="flex items-center">
                <a href="{{ route('hub-owner.users.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold">User Details</h1>
            </div>
            <button onclick="openStatusModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                Manage User
            </button>
        </div>

        <!-- User Profile Card -->
        <div class="bg-white rounded-lg shadow border mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Profile Information</h2>
            </div>
            <div class="p-6">
                <div class="flex items-start space-x-6">
                    <div class="flex-shrink-0">
                        @if($user->profile_image)
                            <img class="h-24 w-24 rounded-full" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-600 font-medium text-2xl">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-gray-600">{{ $user->email }}</p>
                                @if($user->phone)
                                    <p class="text-gray-600">{{ $user->phone }}</p>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 w-20">Role:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : 
                                           ($user->role == 'hub_owner' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 w-20">Status:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 
                                           ($user->status == 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 w-20">Joined:</span>
                                    <span class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($user->company_name)
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-500 w-20">Company:</span>
                                        <span class="text-sm text-gray-900">{{ $user->company_name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Total Bookings</div>
                <div class="text-2xl font-bold">{{ $bookingStats['total_bookings'] }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Confirmed</div>
                <div class="text-2xl font-bold text-green-600">{{ $bookingStats['confirmed_bookings'] }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Pending</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $bookingStats['pending_bookings'] }}</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="text-gray-500 text-sm">Total Spent</div>
                <div class="text-2xl font-bold text-blue-600">₱{{ number_format($bookingStats['total_spent'], 2) }}</div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Recent Bookings</h2>
            </div>
            
            @if($recentActivity->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hub</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentActivity as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->hub_name }}</div>
                                    @if($booking->floorPlan)
                                        <div class="text-sm text-gray-500">{{ $booking->floorPlan->name }}</div>
                                    @endif
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
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                                    @elseif($booking->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('hub-owner.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-sm">No bookings found for this user.</div>
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
            <form action="{{ route('hub-owner.users.update-status', $user) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">User: {{ $user->name }}</label>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="hub_owner" {{ $user->role == 'hub_owner' ? 'selected' : '' }}>Hub Owner</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
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
function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
</script>
@endsection
