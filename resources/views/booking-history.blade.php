@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-800">
    @include('partials.dashboard-navbar', ['active' => 'booking-history'])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="mb-8 rounded-lg border border-yellow-300 bg-yellow-100 px-5 py-4 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Cancellation Policy</h2>
            <p class="text-sm font-medium text-gray-800">
                Pending bookings can be cancelled from this page. Confirmed, completed, and previously cancelled bookings are no longer eligible for cancellation.
                If you cancel a pending booking, any payment already made is non-refundable and will not be returned.
            </p>
        </div>

        @if(request('payment') === 'success' && request('booking'))
            <div class="mb-8 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                <p class="font-semibold">Payment successful. Your booking is recorded and currently pending hub owner confirmation.</p>
                @if($paymentSuccessBooking && $paymentSuccessBooking->transaction_number)
                    <p class="text-sm mt-2 text-green-900">
                        Transaction number: <span class="font-mono font-semibold">{{ $paymentSuccessBooking->transaction_number }}</span>
                    </p>
                @endif
            </div>
        @endif

        @if(session('success'))
            <div class="mb-8 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                    {{ $upcomingBookings->total() }} {{ $upcomingBookings->total() === 1 ? 'Booking' : 'Bookings' }}
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
                                @if($booking->transaction_number)
                                    <p class="text-gray-400 text-xs mt-2 font-mono">Txn {{ $booking->transaction_number }}</p>
                                @endif
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
                                <div class="flex flex-col gap-2">
                                    <button class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors"
                                            onclick="cancelBooking({{ $booking->id }})">
                                        Cancel
                                    </button>
                                    <button type="button"
                                            class="px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm"
                                            onclick="showReportModal({{ $booking->id }})">
                                        Report Issue
                                    </button>
                                </div>
                            @else
                                <button type="button"
                                        class="px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm"
                                        onclick="showReportModal({{ $booking->id }})">
                                    Report Issue
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
                    {{ $pastBookings->total() }} {{ $pastBookings->total() === 1 ? 'Booking' : 'Bookings' }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pastBookings as $booking)
                    @php
                        $rebookRoute = match ($booking->service_type) {
                            'private-office' => route('services.nest-booking'),
                            'meeting-room' => route('services.mesh-booking'),
                            default => route('services.booking'),
                        };
                    @endphp
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
                            @if($booking->transaction_number)
                                <p class="text-gray-500 text-xs font-mono mt-1">Txn {{ $booking->transaction_number }}</p>
                            @endif
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
                            @php
                                $hasFeedback = \App\Models\Review::where('booking_id', $booking->id)
                                    ->where('user_id', Auth::id())
                                    ->exists();
                            @endphp
                            @if(!$hasFeedback)
                                <!-- Feedback Prompt -->
                                <div class="mt-4 p-3 bg-teal-900 bg-opacity-50 rounded-lg border border-teal-500">
                                    <p class="text-white text-sm mb-2">💡 Would you like to share your experience?</p>
                                    <a href="{{ route('feedback.create', ['booking_id' => $booking->id]) }}" 
                                       class="block w-full px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center mb-2">
                                        Feedback on Workspace
                                    </a>
                                    <button onclick="showPlatformFeedbackModal()" 
                                       class="block w-full px-4 py-2 bg-teal-800 text-white rounded-lg font-semibold hover:bg-teal-900 transition-colors">
                                        Feedback on Pwesto Website
                                    </button>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <button class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed" disabled>
                                        Feedback Submitted ✓
                                    </button>
                                    <a href="{{ $rebookRoute }}" class="block w-full px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center">
                                        Book Again
                                    </a>
                                </div>
                            @endif
                            <button type="button"
                                    onclick="showReportModal({{ $booking->id }})"
                                    class="block w-full mt-2 px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm">
                                Report Issue
                            </button>
                        @else
                            <div class="space-y-2">
                                <button class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed" disabled>
                                    {{ ucfirst($booking->status) }}
                                </button>
                                <a href="{{ $rebookRoute }}" class="block w-full px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center">
                                    Book Again
                                </a>
                                <button type="button"
                                        onclick="showReportModal({{ $booking->id }})"
                                        class="block w-full px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm">
                                    Report Issue
                                </button>
                            </div>
                        @endif
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

<!-- Platform Feedback Recommendation Modal -->
<div id="platform-feedback-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-6">
            <div class="text-center mb-4">
                <div class="mx-auto w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Share Your Experience!</h3>
                <p class="text-base text-gray-600 mb-4">
                    Would you like to provide feedback about the Pwesto website? Your input helps us improve!
                </p>
            </div>
            <div class="flex space-x-3">
                <button onclick="hidePlatformFeedbackModal()" class="flex-1 bg-gray-500 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-gray-600 transition-colors">
                    Maybe Later
                </button>
                <a href="{{ route('feedback.create', ['workspace' => 'pwesto']) }}" class="flex-1 bg-teal-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-teal-700 transition-colors text-center">
                    Yes, I'll Feedback
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showPlatformFeedbackModal() {
    document.getElementById('platform-feedback-modal').classList.remove('hidden');
}

function hidePlatformFeedbackModal() {
    document.getElementById('platform-feedback-modal').classList.add('hidden');
}
</script>

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
            <p class="text-base text-gray-600 mb-2">Your booking has been successfully cancelled.</p>
            <p class="text-sm text-gray-500 mb-6">Any payment made for this booking is non-refundable.</p>
            <button id="success-modal-ok-btn" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-blue-700 transition-colors">
                OK
            </button>
        </div>
    </div>
</div>

<!-- Report Issue Modal -->
<div id="report-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('disputes.report-hub-owner') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="booking_id" id="report-booking-id" value="">

            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Report an Issue</h3>
                    <p class="text-sm text-gray-600 mt-1">Tell us what went wrong with this booking. An admin will review your report.</p>
                </div>
                <button type="button" onclick="hideReportModal()" class="text-gray-400 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="report-type" class="block text-sm font-medium text-gray-700 mb-1">Issue Type <span class="text-red-500">*</span></label>
                    <select name="type" id="report-type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select an issue type</option>
                        <option value="service">Service Quality (workspace condition, equipment, etc.)</option>
                        <option value="payment">Payment Issue (overcharged, refund, double-billing)</option>
                        <option value="behavior">Hub Owner Behavior (rudeness, harassment, no-show)</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label for="report-description" class="block text-sm font-medium text-gray-700 mb-1">What happened? <span class="text-red-500">*</span></label>
                    <textarea name="description" id="report-description" rows="4" required minlength="10" maxlength="2000"
                              placeholder="Describe the issue in detail. Include dates, times, and any relevant context (min 10 chars)."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div>
                    <label for="report-evidence" class="block text-sm font-medium text-gray-700 mb-1">Evidence (optional)</label>
                    <textarea name="evidence" id="report-evidence" rows="2" maxlength="2000"
                              placeholder="Paste links to photos, screenshots, transaction IDs, or other supporting info."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 text-sm text-yellow-800">
                    <strong>Note:</strong> False or malicious reports may result in account penalties. Only file a report when there is a genuine issue.
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="hideReportModal()"
                        class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md font-medium hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-md font-medium hover:bg-orange-700 transition-colors">
                    Submit Report
                </button>
            </div>
        </form>
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

// Report Issue modal
function showReportModal(bookingId) {
    document.getElementById('report-booking-id').value = bookingId;
    document.getElementById('report-modal').classList.remove('hidden');
}

function hideReportModal() {
    document.getElementById('report-modal').classList.add('hidden');
    document.getElementById('report-type').value = '';
    document.getElementById('report-description').value = '';
    document.getElementById('report-evidence').value = '';
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
