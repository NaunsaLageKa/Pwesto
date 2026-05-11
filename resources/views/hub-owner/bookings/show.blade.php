@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-60 bg-white border-r flex flex-col py-8 px-4 min-h-screen">
        <div class="text-2xl font-bold mb-10 tracking-tight">{{ strtoupper(auth()->user()->company ?? 'HUB') }} HUB</div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li><a href="{{ route('hub-owner.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7m6 0v6m0 0H7m6 0h6"/></svg>Dashboard</a></li>
                <li><a href="{{ route('hub-owner.bookings.index') }}" class="flex items-center px-3 py-2 rounded-lg bg-blue-50 text-blue-700 font-semibold"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m0-5V3m-8 9v6a2 2 0 002 2h4a2 2 0 002-2v-6"/></svg>Bookings</a></li>
                <li><a href="{{ route('hub-owner.users.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.847.607 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Users</a></li>
                <li><a href="{{ route('hub-owner.feedback.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>Reviews</a></li>
                <li><a href="{{ route('hub-owner.floor-plan') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>Floor Plan</a></li>
            </ul>
        </nav>
        <div class="mt-6 pt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Booking Details</h1>
            <a href="{{ route('hub-owner.bookings.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Bookings</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow border">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Booking #{{ $booking->id }}</h2>
                    <div class="flex items-center space-x-4">
                        @if($booking->status === 'confirmed')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                        @elseif($booking->status === 'pending')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                        @elseif($booking->status === 'completed')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Name:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $booking->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Email:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $booking->user->email }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Phone:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $booking->user->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Hub:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $booking->hub_name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Date:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $booking->booking_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Time:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Amount:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">₱{{ number_format($booking->amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Notes</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-700">{{ $booking->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex space-x-4">
                        @if($booking->status === 'pending')
                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">Confirm Booking</button>
                            </form>
                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">Cancel Booking</button>
                            </form>
                        @elseif($booking->status === 'confirmed')
                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Mark as Completed</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection 