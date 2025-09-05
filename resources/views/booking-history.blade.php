@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-800">
    <!-- Navigation Header -->
    <div class="bg-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-6">
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="admin-button">
                        Admin Panel
                    </a>
                    @endif
                    <div class="text-2xl font-bold text-teal-600 tracking-wider">PWESTO!</div>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
                    <a href="{{ route('booking-history') }}" class="nav-link active">Booking History</a>
                    <a href="{{ route('services.index') }}" class="nav-link">Services</a>
                    <a href="#" class="nav-link">About</a>
                    <a href="#" class="nav-link">Location</a>
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <img 
                            src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" 
                            alt="Profile" 
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 {{ !Auth::user()->profile_image ? 'bg-gray-100 p-2' : '' }}"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-white mb-4">
                Booking History
            </h1>
            <p class="text-xl text-gray-300">
                Manage your upcoming and past bookings.
            </p>
        </div>

        <!-- Upcoming & Pending Bookings Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-8">Upcoming & Pending Bookings</h2>
            
            <div class="bg-gray-700 rounded-lg p-6">
                @forelse($upcomingBookings as $booking)
                    <div class="flex items-center justify-between bg-gray-600 rounded-lg p-4 mb-4 last:mb-0">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 rounded-lg overflow-hidden {{ $booking->status === 'pending' ? 'bg-yellow-500' : 'bg-green-500' }} flex items-center justify-center">
                                @if($booking->service_type === 'hot-desk')
                                    <img src="{{ asset('images/produktiv.png') }}" alt="{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}" class="w-16 h-16 object-contain">
                                @elseif($booking->service_type === 'private-office')
                                    <img src="{{ asset('images/nest.png') }}" alt="{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}" class="w-16 h-16 object-contain">
                                @else
                                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-700">{{ strtoupper(substr($booking->service_type, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-white">
                                <h3 class="font-semibold text-lg">{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}</h3>
                                <p class="text-gray-300">{{ $booking->seat_label }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    @if($booking->status === 'pending')
                                        <span class="px-2 py-1 bg-yellow-500 text-black text-xs rounded-full font-semibold">Pending Approval</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-semibold">Confirmed</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-8 text-white">
                            <div class="text-center">
                                <p class="text-sm text-gray-300">Date</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-300">Time</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:iA') }}</p>
                            </div>
                            @if($booking->status === 'pending')
                                <button class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors"
                                        onclick="cancelBooking({{ $booking->id }})">
                                    Cancel
                                </button>
                            @else
                                <button class="px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed" disabled>
                                    Confirmed
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">
                        <p class="text-lg">No upcoming or pending bookings</p>
                        <p class="text-sm mt-2">Book a workspace to see it here!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent & Past Bookings Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-8">Recent & Past Bookings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pastBookings as $booking)
                    <div class="bg-gray-700 rounded-lg p-6">
                        <div class="w-full h-32 rounded-lg overflow-hidden bg-gray-600 mb-4 flex items-center justify-center">
                            @if($booking->service_type === 'hot-desk')
                                <img src="{{ asset('images/produktiv.png') }}" alt="{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}" class="w-full h-full object-cover">
                            @elseif($booking->service_type === 'private-office')
                                <img src="{{ asset('images/nest.png') }}" alt="{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-white flex items-center justify-center">
                                    <span class="text-4xl font-bold text-gray-700">{{ strtoupper(substr($booking->service_type, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-white mb-4">
                            <h3 class="font-semibold text-lg mb-2">{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}</h3>
                            <p class="text-gray-300 text-sm mb-2">{{ $booking->seat_label }}</p>
                            <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:iA') }}</p>
                            <div class="mt-2">
                                @if($booking->status === 'completed')
                                    <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Completed</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full">Cancelled</span>
                                @elseif($booking->status === 'rejected')
                                    <span class="px-2 py-1 bg-red-600 text-white text-xs rounded-full">Rejected</span>
                                @endif
                            </div>
                        </div>
                        @if($booking->status === 'completed')
                            <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors"
                                    onclick="rebook({{ $booking->id }})">
                                Rebook
                            </button>
                        @else
                            <button class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed" disabled>
                                {{ ucfirst($booking->status) }}
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-400 py-8">
                        <p class="text-lg">No past bookings</p>
                        <p class="text-sm mt-2">Your completed bookings will appear here</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.nav-link {
    @apply text-gray-700 hover:text-teal-600 font-medium transition-colors;
}

.nav-link.active {
    @apply text-teal-600 border-b-2 border-teal-600 pb-1;
}

.admin-button {
    @apply bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-teal-700 transition-colors;
}
</style>

<script>
// Cancel booking functionality
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        fetch(`/booking-history/${bookingId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking cancelled successfully!');
                location.reload(); // Refresh the page to show updated status
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling booking. Please try again.');
        });
    }
}

// Rebook functionality
function rebook(bookingId) {
    if (confirm('Would you like to rebook this service?')) {
        fetch(`/booking-history/${bookingId}/rebook`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.json();
            }
        })
        .then(data => {
            if (data && data.success) {
                window.location.href = '{{ route("services.booking") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing rebook. Please try again.');
        });
    }
}
</script>
@endsection
