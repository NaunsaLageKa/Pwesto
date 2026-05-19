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
                <span class="text-sm font-semibold text-gray-300">
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
                            @php
                                $bookingStart = $booking->start_time ?? $booking->booking_time;
                                $bookingEnd = $booking->end_time;
                            @endphp
                            <div class="text-center" style="min-width: 80px; {{ $booking->status === 'confirmed' ? 'transform: translateX(-50px);' : '' }}">
                                <p class="text-sm text-gray-300">Date</p>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d') }}</p>
                            </div>
                            <div class="text-center" style="min-width: 80px; {{ $booking->status === 'confirmed' ? 'transform: translateX(-50px);' : '' }}">
                                <p class="text-sm text-gray-300">Start Time</p>
                                <p class="font-semibold">{{ $bookingStart ? \Carbon\Carbon::parse($bookingStart)->format('g:i A') : '—' }}</p>
                            </div>
                            <div class="text-center" style="min-width: 80px; {{ $booking->status === 'confirmed' ? 'transform: translateX(-50px);' : '' }}">
                                <p class="text-sm text-gray-300">End Time</p>
                                <p class="font-semibold">{{ $bookingEnd ? \Carbon\Carbon::parse($bookingEnd)->format('g:i A') : '—' }}</p>
                            </div>
                            @if($booking->status === 'pending')
                                <div class="flex flex-col gap-2 items-center justify-center min-w-[140px]">
                                    <button type="button"
                                            class="w-full px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-sm text-center"
                                            onclick="showInvoiceModal({{ $booking->id }})">
                                        Invoice
                                    </button>
                                    <button type="button"
                                            class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm text-center"
                                            onclick="showReportModal({{ $booking->id }})">
                                        Report Issue
                                    </button>
                                    <button type="button"
                                            class="w-full px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors text-center"
                                            onclick="cancelBooking({{ $booking->id }})">
                                        Cancel
                                    </button>
                                </div>
                            @else
                                <div class="flex flex-col gap-2 items-center justify-center min-w-[140px]">
                                    <button type="button"
                                            class="w-full px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-sm text-center"
                                            onclick="showInvoiceModal({{ $booking->id }})">
                                        Invoice
                                    </button>
                                    <button type="button"
                                            class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm text-center"
                                            onclick="showReportModal({{ $booking->id }})">
                                        Report Issue
                                    </button>
                                </div>
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
                            @php
                                $pastStart = $booking->start_time ?? $booking->booking_time;
                                $pastEnd = $booking->end_time;
                            @endphp
                            <p class="text-gray-400 text-xs">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                @if($pastStart)
                                    · {{ \Carbon\Carbon::parse($pastStart)->format('g:i A') }}
                                    @if($pastEnd)
                                        – {{ \Carbon\Carbon::parse($pastEnd)->format('g:i A') }}
                                    @endif
                                @endif
                            </p>
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
                            <button type="button"
                                    onclick="showInvoiceModal({{ $booking->id }})"
                                    class="block w-full mb-3 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center text-sm">
                                Invoice
                            </button>
                            <div class="mt-4 p-3 bg-teal-900 bg-opacity-50 rounded-lg border border-teal-500">
                                <p class="text-white text-sm mb-2">💡 Share your experience—you can send workspace or platform feedback again after each completed visit.</p>
                                <a href="{{ route('feedback.create', ['booking_id' => $booking->id]) }}"
                                   class="block w-full px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center mb-2">
                                    Feedback on Workspace
                                </a>
                                <button onclick="showPlatformFeedbackModal()"
                                   class="block w-full px-4 py-2 bg-teal-800 text-white rounded-lg font-semibold hover:bg-teal-900 transition-colors">
                                    Feedback on Pwesto Website
                                </button>
                            </div>
                            <a href="{{ $rebookRoute }}" class="block w-full mt-2 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center">
                                Book Again
                            </a>
                            <button type="button"
                                    onclick="showReportModal({{ $booking->id }})"
                                    class="block w-full mt-2 px-4 py-2 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors text-sm">
                                Report Issue
                            </button>
                        @else
                            <button type="button"
                                    onclick="showInvoiceModal({{ $booking->id }})"
                                    class="block w-full mb-2 px-4 py-2 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition-colors text-center text-sm">
                                Invoice
                            </button>
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

            <div id="report-form-errors" class="hidden mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"></div>

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
                    <label for="report-evidence-dropzone" class="block text-sm font-medium text-gray-700 mb-1">Evidence <span class="text-red-500">*</span></label>
                    <div id="report-evidence-dropzone"
                         tabindex="0"
                         role="region"
                         aria-label="Paste evidence screenshot here"
                         class="relative min-h-[140px] rounded-md border-2 border-dashed border-gray-300 bg-gray-50 p-4 text-center outline-none transition-colors hover:border-orange-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                        <p id="report-evidence-placeholder" class="text-sm text-gray-500 pointer-events-none select-none">Paste Evidence here</p>
                        <div id="report-evidence-loading" class="hidden py-6 text-sm text-orange-700">
                            <span class="inline-block h-5 w-5 animate-spin rounded-full border-2 border-orange-300 border-t-orange-600 align-middle mr-2"></span>
                            Processing image…
                        </div>
                        <img id="report-evidence-preview" src="" alt="Evidence preview" class="mx-auto hidden max-h-48 max-w-full rounded-lg border border-gray-200 object-contain shadow-sm">
                        <button type="button" id="report-evidence-clear" class="mt-3 hidden text-sm font-medium text-orange-600 hover:text-orange-800 underline">
                            Remove image
                        </button>
                    </div>
                    <textarea name="evidence" id="report-evidence" minlength="10" maxlength="400000" tabindex="-1" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;overflow:hidden" aria-label="Evidence data for submit"></textarea>
                    <p id="report-evidence-error" class="mt-1 hidden text-sm text-red-600">Please paste a screenshot as evidence (click the box and press Ctrl+V).</p>
                    @error('evidence')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

<!-- Invoice modal -->
<div id="invoice-modal" class="fixed inset-0 z-[60] hidden overflow-y-auto overscroll-contain p-4 pt-20 pb-4 sm:p-6 sm:pt-24" role="dialog" aria-modal="true" aria-labelledby="invoice-modal-title">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-[2px] transition-opacity" onclick="closeInvoiceModal()" aria-hidden="true"></div>
    <div class="relative z-10 mx-auto flex w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.35)] ring-1 ring-black/5 sm:max-w-2xl" style="max-height: calc(100vh - 6rem);" onclick="event.stopPropagation()">
        <header class="shrink-0 bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-700 px-5 py-4 sm:px-6 sm:py-5 text-white">
            <h2 id="invoice-modal-title" class="text-lg font-bold tracking-tight sm:text-xl">Invoice</h2>
            <p class="mt-0.5 text-sm text-teal-100/90">Official booking receipt</p>
        </header>
        <div id="invoice-modal-body" class="min-h-0 flex-1 overflow-y-auto bg-gradient-to-b from-slate-50 to-slate-100/90 p-4 sm:p-6"></div>
        <footer class="shrink-0 flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 bg-white px-4 py-3 sm:px-5">
            <button type="button" id="invoice-download-pdf-btn" onclick="downloadInvoicePdf()" disabled
                    class="invoice-pdf-btn inline-flex h-10 w-10 items-center justify-center rounded-lg shadow-md transition disabled:cursor-not-allowed"
                    aria-label="Download invoice as PDF"
                    title="Download PDF">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </button>
            <button type="button" onclick="closeInvoiceModal()"
                    class="invoice-close-btn inline-flex h-10 w-10 items-center justify-center rounded-lg shadow-md transition"
                    aria-label="Close invoice"
                    title="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </footer>
    </div>
</div>

<style>
#invoice-modal .invoice-pdf-btn {
    background-color: #0d9488;
    color: #fff;
    border: 2px solid #0f766e;
}
#invoice-modal .invoice-pdf-btn:hover:not(:disabled) {
    background-color: #0f766e;
}
#invoice-modal .invoice-pdf-btn:disabled {
    background-color: #64748b;
    border-color: #475569;
    opacity: 1;
}
#invoice-modal .invoice-close-btn {
    background-color: #dc2626;
    color: #fff;
    border: 2px solid #b91c1c;
}
#invoice-modal .invoice-close-btn:hover {
    background-color: #b91c1c;
}

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" crossorigin="anonymous"></script>
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
    resetReportEvidenceUi();
    clearReportFormErrors();
    document.getElementById('report-modal').classList.remove('hidden');
}

function revokeReportEvidencePreviewUrl() {
    if (window.__reportEvidencePreviewUrl) {
        try { URL.revokeObjectURL(window.__reportEvidencePreviewUrl); } catch (e) {}
        window.__reportEvidencePreviewUrl = '';
    }
}

function setReportEvidenceLoading(isLoading) {
    var loading = document.getElementById('report-evidence-loading');
    var dropzone = document.getElementById('report-evidence-dropzone');
    if (loading) loading.classList.toggle('hidden', !isLoading);
    if (dropzone) {
        dropzone.classList.toggle('pointer-events-none', isLoading);
        dropzone.classList.toggle('opacity-70', isLoading);
    }
}

function resetReportEvidenceUi() {
    window.__reportEvidenceImageData = '';
    revokeReportEvidencePreviewUrl();
    var hidden = document.getElementById('report-evidence');
    var ph = document.getElementById('report-evidence-placeholder');
    var img = document.getElementById('report-evidence-preview');
    var clr = document.getElementById('report-evidence-clear');
    var loading = document.getElementById('report-evidence-loading');
    if (hidden) hidden.value = '';
    if (loading) loading.classList.add('hidden');
    if (ph) ph.classList.remove('hidden');
    if (img) {
        img.removeAttribute('src');
        img.classList.add('hidden');
    }
    if (clr) clr.classList.add('hidden');
    setReportEvidenceLoading(false);
}

function syncReportEvidenceHiddenField() {
    var hidden = document.getElementById('report-evidence');
    if (!hidden) return;
    hidden.value = window.__reportEvidenceImageData || '';
}

function clearReportFormErrors() {
    var box = document.getElementById('report-form-errors');
    if (box) {
        box.classList.add('hidden');
        box.innerHTML = '';
    }
    var evErr = document.getElementById('report-evidence-error');
    if (evErr) evErr.classList.add('hidden');
    var dropzone = document.getElementById('report-evidence-dropzone');
    if (dropzone) dropzone.classList.remove('border-red-500', 'ring-2', 'ring-red-400');
}

function validateReportForm() {
    clearReportFormErrors();
    var errors = [];

    var typeEl = document.getElementById('report-type');
    if (!typeEl || !typeEl.value) {
        errors.push('Please select an issue type.');
    }

    var descEl = document.getElementById('report-description');
    var desc = descEl ? descEl.value.trim() : '';
    if (!desc) {
        errors.push('Please describe what happened.');
    } else if (desc.length < 10) {
        errors.push('Description must be at least 10 characters.');
    }

    syncReportEvidenceHiddenField();
    var evidence = (window.__reportEvidenceImageData || '').trim();
    if (!evidence || evidence.length < 10) {
        errors.push('Evidence is required. Paste a screenshot into the evidence box.');
        var evErr = document.getElementById('report-evidence-error');
        if (evErr) evErr.classList.remove('hidden');
        var dropzone = document.getElementById('report-evidence-dropzone');
        if (dropzone) dropzone.classList.add('border-red-500', 'ring-2', 'ring-red-400');
    }

    if (errors.length === 0) {
        return true;
    }

    var box = document.getElementById('report-form-errors');
    if (box) {
        box.innerHTML = '<p class="font-semibold mb-1">Please fix the following:</p><ul class="list-disc list-inside">' +
            errors.map(function (e) { return '<li>' + e + '</li>'; }).join('') + '</ul>';
        box.classList.remove('hidden');
    }
    return false;
}

function hideReportModal() {
    document.getElementById('report-modal').classList.add('hidden');
    document.getElementById('report-type').value = '';
    document.getElementById('report-description').value = '';
    resetReportEvidenceUi();
}

/**
 * Compress pasted screenshots so the page stays responsive (max ~800px, JPEG, size cap).
 */
function compressEvidenceBlob(blob, callback) {
    if (!blob || !blob.type || blob.type.indexOf('image') === -1) {
        callback(null, 'Please paste an image file.');
        return;
    }
    if (blob.size > 12 * 1024 * 1024) {
        callback(null, 'Image is too large. Use a smaller screenshot (under 12 MB).');
        return;
    }

    var objectUrl = URL.createObjectURL(blob);
    var img = new Image();

    img.onload = function () {
        URL.revokeObjectURL(objectUrl);
        var maxDim = 800;
        var w = img.naturalWidth || 1;
        var h = img.naturalHeight || 1;
        if (w > maxDim || h > maxDim) {
            if (w >= h) {
                h = Math.round((h * maxDim) / w);
                w = maxDim;
            } else {
                w = Math.round((w * maxDim) / h);
                h = maxDim;
            }
        }
        var canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        var ctx = canvas.getContext('2d', { alpha: false });
        if (!ctx) {
            callback(null, 'Could not process image.');
            return;
        }
        ctx.drawImage(img, 0, 0, w, h);
        var quality = 0.72;
        var out = '';
        try {
            out = canvas.toDataURL('image/jpeg', quality);
            while (out.length > 320000 && quality > 0.4) {
                quality -= 0.08;
                out = canvas.toDataURL('image/jpeg', quality);
            }
        } catch (err) {
            callback(null, 'Could not compress image.');
            return;
        }
        img.onload = img.onerror = null;
        setTimeout(function () { callback(out, null); }, 0);
    };

    img.onerror = function () {
        URL.revokeObjectURL(objectUrl);
        img.onload = img.onerror = null;
        callback(null, 'Could not read pasted image.');
    };

    img.src = objectUrl;
}

function processEvidenceImageBlob(blob, applyFn) {
    setReportEvidenceLoading(true);
    setTimeout(function () {
        compressEvidenceBlob(blob, function (dataUrl, errMsg) {
            setReportEvidenceLoading(false);
            if (errMsg) {
                alert(errMsg);
                return;
            }
            if (dataUrl) {
                applyFn(dataUrl);
            }
        });
    }, 10);
}

document.addEventListener('DOMContentLoaded', function () {
    var dropzone = document.getElementById('report-evidence-dropzone');
    var hidden = document.getElementById('report-evidence');
    var ph = document.getElementById('report-evidence-placeholder');
    var preview = document.getElementById('report-evidence-preview');
    var clearBtn = document.getElementById('report-evidence-clear');

    if (!dropzone || !hidden) {
        return;
    }

    function applyImageDataUrl(dataUrl) {
        window.__reportEvidenceImageData = dataUrl;
        if (ph) ph.classList.add('hidden');
        revokeReportEvidencePreviewUrl();
        if (preview) {
            preview.src = dataUrl;
            preview.classList.remove('hidden');
        }
        if (clearBtn) clearBtn.classList.remove('hidden');
        var evErr = document.getElementById('report-evidence-error');
        if (evErr) evErr.classList.add('hidden');
        dropzone.classList.remove('border-red-500', 'ring-2', 'ring-red-400');
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            resetReportEvidenceUi();
        });
    }

    dropzone.addEventListener('paste', function (e) {
        var cd = e.clipboardData;
        if (!cd || !cd.items) {
            return;
        }
        for (var i = 0; i < cd.items.length; i++) {
            if (cd.items[i].type.indexOf('image') === -1) {
                continue;
            }
            e.preventDefault();
            var blob = cd.items[i].getAsFile();
            if (!blob) {
                return;
            }
            processEvidenceImageBlob(blob, applyImageDataUrl);
            return;
        }
    });

    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropzone.classList.add('border-orange-500', 'bg-orange-50');
    });
    dropzone.addEventListener('dragleave', function () {
        dropzone.classList.remove('border-orange-500', 'bg-orange-50');
    });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.classList.remove('border-orange-500', 'bg-orange-50');
        var f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
        if (!f || f.type.indexOf('image') === -1) {
            return;
        }
        processEvidenceImageBlob(f, applyImageDataUrl);
    });

    var reportForm = dropzone.closest('form');
    if (reportForm) {
        reportForm.addEventListener('submit', function (e) {
            if (!validateReportForm()) {
                e.preventDefault();
                return;
            }
            syncReportEvidenceHiddenField();
        });
    }

    @if($errors->any() && old('booking_id'))
    showReportModal(@json(old('booking_id')));
    var typeEl = document.getElementById('report-type');
    if (typeEl) typeEl.value = @json(old('type', ''));
    var descEl = document.getElementById('report-description');
    if (descEl) descEl.value = @json(old('description', ''));
    @if($errors->has('evidence'))
    var box = document.getElementById('report-form-errors');
    if (box) {
        box.innerHTML = '<p class="font-semibold">{{ $errors->first('evidence') }}</p>';
        box.classList.remove('hidden');
    }
    document.getElementById('report-evidence-error')?.classList.remove('hidden');
    document.getElementById('report-evidence-dropzone')?.classList.add('border-red-500', 'ring-2', 'ring-red-400');
    @endif
    @endif
});

var currentInvoiceBookingId = null;

function bookingInvoiceModalUrl(bookingId) {
    var base = @json(rtrim(parse_url(route('booking-history'), PHP_URL_PATH) ?: '/booking-history', '/'));
    return base + '/' + bookingId + '/invoice?modal=1';
}

function bookingInvoicePdfUrl(bookingId) {
    var base = @json(rtrim(parse_url(route('booking-history'), PHP_URL_PATH) ?: '/booking-history', '/'));
    return base + '/' + bookingId + '/invoice/pdf';
}

function downloadInvoicePdf() {
    if (!currentInvoiceBookingId) return;
    var sheet = document.querySelector('#invoice-modal-body .booking-invoice-sheet');
    if (sheet && typeof html2pdf !== 'undefined') {
        html2pdf().set({
            margin: [0.35, 0.4, 0.35, 0.4],
            filename: 'INV-' + currentInvoiceBookingId + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        }).from(sheet).save();
        return;
    }
    window.location.href = bookingInvoicePdfUrl(currentInvoiceBookingId);
}

function showInvoiceModal(bookingId) {
    const modal = document.getElementById('invoice-modal');
    const body = document.getElementById('invoice-modal-body');
    const pdfBtn = document.getElementById('invoice-download-pdf-btn');
    currentInvoiceBookingId = bookingId;
    if (pdfBtn) pdfBtn.disabled = true;
    body.innerHTML = '<div class="flex flex-col items-center justify-center gap-4 py-16 px-4"><div class="h-10 w-10 rounded-full border-2 border-teal-200 border-t-teal-600 animate-spin" aria-hidden="true"></div><p class="text-sm font-medium text-slate-600">Loading invoice…</p></div>';
    modal.classList.remove('hidden');
    modal.scrollTop = 0;
    document.body.classList.add('overflow-hidden');

    fetch(bookingInvoiceModalUrl(bookingId), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
    })
        .then(function (r) {
            if (!r.ok) throw new Error('Request failed');
            return r.text();
        })
        .then(function (html) {
            body.innerHTML = html;
            if (pdfBtn) pdfBtn.disabled = false;
        })
        .catch(function () {
            body.innerHTML = '<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-8 text-center"><p class="text-sm font-medium text-red-700">Could not load invoice. Please try again.</p></div>';
        });
}

function closeInvoiceModal() {
    const modal = document.getElementById('invoice-modal');
    modal.classList.add('hidden');
    document.getElementById('invoice-modal-body').innerHTML = '';
    currentInvoiceBookingId = null;
    const pdfBtn = document.getElementById('invoice-download-pdf-btn');
    if (pdfBtn) pdfBtn.disabled = true;
    document.body.classList.remove('overflow-hidden');
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

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        const inv = document.getElementById('invoice-modal');
        if (inv && !inv.classList.contains('hidden')) {
            closeInvoiceModal();
        }
    });
});
</script>
@endsection
