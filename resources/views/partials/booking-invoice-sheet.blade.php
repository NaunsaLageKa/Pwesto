@php
    $start = $booking->start_time ?? $booking->booking_time;
    $end = $booking->end_time;
@endphp
<div class="sheet booking-invoice-sheet" data-booking-status="{{ e($booking->status) }}">
    <div class="invoice-brand">
        <div>
            <h1>{{ config('app.name', 'Pwesto') }}</h1>
            <p class="muted">Booking invoice / receipt</p>
        </div>
        <div class="invoice-id-badge">
            <code>INV-{{ $booking->id }}-{{ $booking->booking_date?->format('Ymd') }}</code>
            <span class="issued">Issued {{ now()->format('M j, Y · g:i A') }}</span>
        </div>
    </div>

    <div class="row">
        <div class="block">
            <h2>Status</h2>
            <p>
                <span class="invoice-status-pill">{{ $booking->status }}</span>
            </p>
        </div>
        <div class="block">
            <h2>Booking reference</h2>
            <p style="font-family: ui-monospace, monospace; font-size: 0.875rem;">#{{ $booking->id }}</p>
            <p class="muted">Keep this ID for support inquiries.</p>
        </div>
    </div>

    <div class="row">
        <div class="block">
            <h2>Bill to</h2>
            <p>{{ $booking->user->name ?? 'Customer' }}</p>
            <p class="muted">{{ $booking->user->email ?? '' }}</p>
        </div>
        <div class="block">
            <h2>Workspace / hub</h2>
            <p>{{ $booking->hub_name ?? ($booking->hubOwner->company ?? $booking->hubOwner->name ?? '—') }}</p>
        </div>
    </div>

    <div class="invoice-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ ucfirst(str_replace('-', ' ', $booking->service_type)) }}</strong>
                        @if($booking->seat_label)
                            <br><span class="muted">{{ $booking->seat_label }}</span>
                        @endif
                    </td>
                    <td>{{ $booking->booking_date?->format('M j, Y') ?? '—' }}</td>
                    <td>
                        @if($start)
                            {{ \Carbon\Carbon::parse($start)->format('g:i A') }}
                            @if($end)
                                – {{ \Carbon\Carbon::parse($end)->format('g:i A') }}
                            @endif
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($booking->transaction_number)
        <div class="invoice-txn">
            Transaction ID<br>
            <span>{{ $booking->transaction_number }}</span>
        </div>
    @endif

    <div class="total">
        ₱{{ number_format((float) $booking->amount, 2) }}
        <small>Amount (PHP)</small>
    </div>

    @if($booking->notes)
        <p class="muted" style="margin-top:1.25rem;font-size:0.8125rem;"><strong>Notes:</strong> {{ $booking->notes }}</p>
    @endif

    @if(!empty($showActions))
        <div class="actions">
            <a href="{{ route('booking-history.invoice.pdf', $booking) }}" class="btn btn-pdf">Download PDF</a>
            <button type="button" class="btn btn-print" onclick="window.print()">Print</button>
            <a href="{{ route('booking-history') }}" class="btn btn-back">Back to booking history</a>
        </div>
    @endif
</div>
