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
                    <a href="{{ route('hub-owner.bookings.index') }}" class="block px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg">
                        Bookings
                    </a>
                    <a href="{{ route('hub-owner.users.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Users
                    </a>
                    <a href="{{ route('hub-owner.feedback.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Reviews
                    </a>
                    <a href="{{ route('hub-owner.floor-plan') }}" class="block px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                        Floor Plan
                    </a>
                </nav>
                <div class="mt-6 pt-4 border-t border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bookings Management</h1>
                    <p class="text-gray-600 mt-1">Manage and track all bookings</p>
                </div>
                <a href="{{ route('hub-owner.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">&larr; Back to Dashboard</a>
            </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Search and Filter -->
        <div class="bg-white p-6 rounded-lg shadow mb-6 border">
            <form method="GET" action="{{ route('hub-owner.bookings.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user name or hub name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" @if(request('status') == 'pending') selected @endif>Pending</option>
                        <option value="confirmed" @if(request('status') == 'confirmed') selected @endif>Confirmed</option>
                        <option value="cancelled" @if(request('status') == 'cancelled') selected @endif>Cancelled</option>
                        <option value="completed" @if(request('status') == 'completed') selected @endif>Completed</option>
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select name="date_range" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Dates</option>
                        <option value="today" @if(request('date_range') == 'today') selected @endif>Today</option>
                        <option value="week" @if(request('date_range') == 'week') selected @endif>This Week</option>
                        <option value="month" @if(request('date_range') == 'month') selected @endif>This Month</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Filter</button>
                    <a href="{{ route('hub-owner.bookings.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">Clear</a>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-lg shadow border">
            @if($bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                        </div>
                                    </div>
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
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('hub-owner.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-1.5 rounded-md border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                            👁 View
                                        </a>
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700 transition-colors">
                                                    ✓ Confirm
                                                </button>
                                            </form>
                                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md border border-red-300 text-red-700 hover:bg-red-50 transition-colors">
                                                    ✕ Cancel
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'confirmed')
                                            <form action="{{ route('hub-owner.bookings.update-status', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                                    ✓ Mark Complete
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button"
                                                onclick="showReportCustomerModal({{ $booking->id }}, '{{ addslashes($booking->user->name) }}')"
                                                class="inline-flex items-center px-3 py-1.5 rounded-md border border-orange-300 text-orange-700 bg-orange-50 hover:bg-orange-100 transition-colors">
                                            ⚠ Report Customer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 text-lg mb-2">No bookings found</div>
                    <div class="text-gray-500 text-sm">When users make bookings, they will appear here.</div>
                </div>
            @endif
        </div>
    </main>
</div>

<!-- Report Customer Modal -->
<div id="report-customer-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <form action="{{ route('disputes.report-user') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="booking_id" id="report-customer-booking-id" value="">

            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Report Customer</h3>
                    <p class="text-sm text-gray-600 mt-1">Filing a report for <span id="report-customer-name" class="font-semibold text-gray-800">this customer</span>. An admin will review your report.</p>
                </div>
                <button type="button" onclick="hideReportCustomerModal()" class="text-gray-400 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="report-customer-form-errors" class="hidden mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"></div>

            <div class="space-y-4">
                <div>
                    <label for="report-customer-type" class="block text-sm font-medium text-gray-700 mb-1">Issue Type <span class="text-red-500">*</span></label>
                    <select name="type" id="report-customer-type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select an issue type</option>
                        <option value="behavior">Customer Behavior (rude, harassment, no-show)</option>
                        <option value="payment">Payment Issue (chargeback, fraud, unpaid balance)</option>
                        <option value="service">Property Damage / Misuse of Workspace</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label for="report-customer-description" class="block text-sm font-medium text-gray-700 mb-1">What happened? <span class="text-red-500">*</span></label>
                    <textarea name="description" id="report-customer-description" rows="4" required minlength="10" maxlength="2000"
                              placeholder="Describe the issue in detail. Include dates, times, and any relevant context (min 10 chars)."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                @include('partials.report-evidence-field', ['prefix' => 'report-customer-evidence', 'showServerErrors' => true])

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 text-sm text-yellow-800">
                    <strong>Note:</strong> False or malicious reports may result in account penalties. Only file a report when there is a genuine issue.
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="hideReportCustomerModal()"
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

@include('partials.report-evidence-paste-script')

<script>
var REPORT_CUSTOMER_EVIDENCE_PREFIX = 'report-customer-evidence';

function clearReportCustomerFormErrors() {
    var box = document.getElementById('report-customer-form-errors');
    if (box) {
        box.classList.add('hidden');
        box.innerHTML = '';
    }
    if (window.ReportEvidencePaste) {
        ReportEvidencePaste.clearInvalid(REPORT_CUSTOMER_EVIDENCE_PREFIX);
    }
}

function validateReportCustomerForm() {
    clearReportCustomerFormErrors();
    var errors = [];

    var typeEl = document.getElementById('report-customer-type');
    if (!typeEl || !typeEl.value) {
        errors.push('Please select an issue type.');
    }

    var descEl = document.getElementById('report-customer-description');
    var desc = descEl ? descEl.value.trim() : '';
    if (!desc) {
        errors.push('Please describe what happened.');
    } else if (desc.length < 10) {
        errors.push('Description must be at least 10 characters.');
    }

    var evidence = window.ReportEvidencePaste ? ReportEvidencePaste.getData(REPORT_CUSTOMER_EVIDENCE_PREFIX).trim() : '';
    if (!evidence || evidence.length < 10) {
        errors.push('Evidence is required. Paste a screenshot into the evidence box.');
        if (window.ReportEvidencePaste) {
            ReportEvidencePaste.markInvalid(REPORT_CUSTOMER_EVIDENCE_PREFIX);
        }
    }

    if (errors.length === 0) {
        return true;
    }

    var box = document.getElementById('report-customer-form-errors');
    if (box) {
        box.innerHTML = '<p class="font-semibold mb-1">Please fix the following:</p><ul class="list-disc list-inside">' +
            errors.map(function (e) { return '<li>' + e + '</li>'; }).join('') + '</ul>';
        box.classList.remove('hidden');
    }
    return false;
}

function showReportCustomerModal(bookingId, customerName) {
    document.getElementById('report-customer-booking-id').value = bookingId;
    document.getElementById('report-customer-name').textContent = customerName;
    clearReportCustomerFormErrors();
    if (window.ReportEvidencePaste) {
        ReportEvidencePaste.reset(REPORT_CUSTOMER_EVIDENCE_PREFIX);
    }
    document.getElementById('report-customer-modal').classList.remove('hidden');
}

function hideReportCustomerModal() {
    document.getElementById('report-customer-modal').classList.add('hidden');
    document.getElementById('report-customer-type').value = '';
    document.getElementById('report-customer-description').value = '';
    if (window.ReportEvidencePaste) {
        ReportEvidencePaste.reset(REPORT_CUSTOMER_EVIDENCE_PREFIX);
    }
    clearReportCustomerFormErrors();
}

document.addEventListener('DOMContentLoaded', function () {
    if (window.ReportEvidencePaste) {
        ReportEvidencePaste.init(REPORT_CUSTOMER_EVIDENCE_PREFIX);
    }

    var reportForm = document.querySelector('#report-customer-modal form');
    if (reportForm) {
        reportForm.addEventListener('submit', function (e) {
            if (!validateReportCustomerForm()) {
                e.preventDefault();
                return;
            }
            if (window.ReportEvidencePaste) {
                ReportEvidencePaste.syncHidden(REPORT_CUSTOMER_EVIDENCE_PREFIX);
            }
        });
    }

    @if($errors->any() && old('booking_id'))
    showReportCustomerModal(@json(old('booking_id')), @json(old('customer_name', 'this customer')));
    var typeEl = document.getElementById('report-customer-type');
    if (typeEl) typeEl.value = @json(old('type', ''));
    var descEl = document.getElementById('report-customer-description');
    if (descEl) descEl.value = @json(old('description', ''));
    @if($errors->has('evidence'))
    var box = document.getElementById('report-customer-form-errors');
    if (box) {
        box.innerHTML = '<p class="font-semibold">{{ $errors->first('evidence') }}</p>';
        box.classList.remove('hidden');
    }
    if (window.ReportEvidencePaste) {
        ReportEvidencePaste.markInvalid(REPORT_CUSTOMER_EVIDENCE_PREFIX);
    }
    @endif
    @endif
});
</script>
@endsection