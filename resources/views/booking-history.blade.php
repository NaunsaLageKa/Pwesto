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
                    <a href="{{ route('about') }}" class="nav-link">About</a>
                    <a href="#" class="nav-link">Location</a>
                    <x-profile-dropdown />
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
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-semibold text-white">Upcoming & Pending Bookings</h2>
                <span class="bg-teal-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    {{ $upcomingBookings->count() }} {{ $upcomingBookings->count() === 1 ? 'Booking' : 'Bookings' }}
                </span>
            </div>
            
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
                        <div class="flex items-start text-white" style="gap: 5rem;">
                            <div class="text-center" style="min-width: 80px; {{ $booking->status === 'confirmed' ? 'transform: translateX(-50px);' : '' }}">
                                <p class="text-sm text-gray-300">Date</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d') }}</p>
                            </div>
                            <div class="text-center" style="min-width: 80px; {{ $booking->status === 'confirmed' ? 'transform: translateX(-50px);' : '' }}">
                                <p class="text-sm text-gray-300">Time</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</p>
                            </div>
                            @if($booking->status === 'pending')
                                <button class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors"
                                        onclick="cancelBooking({{ $booking->id }})">
                                    Cancel
                                </button>
                            @else
                                <div class="px-4 py-2"></div>
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
            
            <!-- Pagination for Upcoming Bookings -->
            @if($upcomingBookings->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $upcomingBookings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Recent & Past Bookings Section -->
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-semibold text-white">Recent & Past Bookings</h2>
                <span class="bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    {{ $pastBookings->count() }} {{ $pastBookings->count() === 1 ? 'Booking' : 'Bookings' }}
                </span>
            </div>
            
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
                        <button class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed" disabled>
                            {{ ucfirst($booking->status) }}
                        </button>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-400 py-8">
                        <p class="text-lg">No past bookings</p>
                        <p class="text-sm mt-2">Your completed bookings will appear here</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination for Past Bookings -->
            @if($pastBookings->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $pastBookings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancel-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-96 mx-4">
        <div class="p-6 text-center">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Cancel Booking</h3>
            <p class="text-base text-gray-600 mb-6">Are you sure you want to cancel this booking?</p>
            <div class="flex space-x-3">
                <button id="cancel-modal-no-btn" class="flex-1 bg-gray-500 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-gray-600 transition-colors">
                    No
                </button>
                <button id="cancel-modal-yes-btn" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-red-700 transition-colors">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-96 mx-4">
        <div class="p-6 text-center">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Booking Cancelled!</h3>
            <p class="text-base text-gray-600 mb-6">Your booking has been successfully cancelled.</p>
            <button id="success-modal-ok-btn" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-blue-700 transition-colors">
                OK
            </button>
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

/* Custom pagination styling for dark theme */
.pagination {
    @apply flex justify-center space-x-2;
}

.pagination > li > a,
.pagination > li > span {
    @apply px-3 py-2 text-sm font-medium text-gray-300 bg-gray-700 border border-gray-600 rounded-md hover:bg-gray-600 hover:text-white transition-colors;
}

.pagination > li.active > span {
    @apply bg-teal-600 text-white border-teal-600;
}

.pagination > li.disabled > span {
    @apply bg-gray-800 text-gray-500 border-gray-700 cursor-not-allowed;
}
</style>

<script>
// Cancel booking functionality
let currentBookingId = null;

function cancelBooking(bookingId) {
    currentBookingId = bookingId;
    showCancelModal();
}

function showCancelModal() {
    document.getElementById('cancel-modal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancel-modal').classList.add('hidden');
}

function showSuccessModal() {
    document.getElementById('success-modal').classList.remove('hidden');
}

function hideSuccessModal() {
    document.getElementById('success-modal').classList.add('hidden');
}

function confirmCancel() {
    if (currentBookingId) {
        fetch(`/booking-history/${currentBookingId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideCancelModal();
            if (data.success) {
                showSuccessModal();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideCancelModal();
            alert('Error cancelling booking. Please try again.');
        });
    }
}


// Modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cancel modal buttons
    document.getElementById('cancel-modal-no-btn').addEventListener('click', function() {
        hideCancelModal();
    });
    
    document.getElementById('cancel-modal-yes-btn').addEventListener('click', function() {
        confirmCancel();
    });
    
    // Success modal button
    document.getElementById('success-modal-ok-btn').addEventListener('click', function() {
        hideSuccessModal();
        location.reload(); // Refresh the page to show updated status
    });
});
</script>
@endsection
