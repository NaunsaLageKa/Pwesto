@php
    $start = $booking->start_time ?? $booking->booking_time;
    $end = $booking->end_time;
    $statusColors = [
        'pending' => ['bg' => '#fef3c7', 'color' => '#92400e'],
        'confirmed' => ['bg' => '#d1fae5', 'color' => '#065f46'],
        'completed' => ['bg' => '#dbeafe', 'color' => '#1e40af'],
        'cancelled' => ['bg' => '#fee2e2', 'color' => '#991b1b'],
        'rejected' => ['bg' => '#fee2e2', 'color' => '#991b1b'],
    ];
    $pill = $statusColors[$booking->status] ?? ['bg' => '#e2e8f0', 'color' => '#475569'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $booking->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 24px; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #0f766e; }
        .muted { color: #64748b; font-size: 11px; }
        .brand-row { width: 100%; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 14px; }
        .brand-row td { vertical-align: top; }
        .brand-right { text-align: right; }
        .inv-code { font-family: DejaVu Sans Mono, monospace; font-size: 11px; font-weight: bold; padding: 6px 10px; background: #f0fdfa; color: #115e59; border: 1px solid #99f6e4; }
        .grid { width: 100%; border-collapse: separate; border-spacing: 8px 8px; margin: 0 -8px 8px; }
        .block { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; vertical-align: top; width: 50%; }
        .block h2 { font-size: 9px; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 6px; }
        .block p { margin: 0; font-weight: bold; }
        .pill { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: bold; text-transform: capitalize; background: {{ $pill['bg'] }}; color: {{ $pill['color'] }}; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; border: 1px solid #e2e8f0; }
        table.items th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 9px; text-transform: uppercase; color: #475569; border-bottom: 1px solid #cbd5e1; }
        table.items td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .txn { margin-top: 12px; padding: 8px 10px; border: 1px dashed #cbd5e1; background: #f8fafc; font-size: 11px; color: #475569; }
        .txn span { font-family: DejaVu Sans Mono, monospace; font-weight: bold; color: #334155; }
        .total { margin-top: 16px; padding: 14px; text-align: center; background: #0f766e; color: #fff; font-size: 18px; font-weight: bold; border-radius: 6px; }
        .total small { display: block; font-size: 9px; font-weight: normal; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
    </style>
</head>
<body>
    <table class="brand-row">
        <tr>
            <td>
                <h1>{{ config('app.name', 'Pwesto') }}</h1>
                <p class="muted">Booking invoice / receipt</p>
            </td>
            <td class="brand-right">
                <span class="inv-code">INV-{{ $booking->id }}-{{ $booking->booking_date?->format('Ymd') }}</span><br>
                <span class="muted">Issued {{ now()->format('M j, Y · g:i A') }}</span>
            </td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td class="block">
                <h2>Status</h2>
                <span class="pill">{{ $booking->status }}</span>
            </td>
            <td class="block">
                <h2>Booking reference</h2>
                <p>#{{ $booking->id }}</p>
                <p class="muted">Keep this ID for support inquiries.</p>
            </td>
        </tr>
        <tr>
            <td class="block">
                <h2>Bill to</h2>
                <p>{{ $booking->user->name ?? 'Customer' }}</p>
                <p class="muted">{{ $booking->user->email ?? '' }}</p>
            </td>
            <td class="block">
                <h2>Workspace / hub</h2>
                <p>{{ $booking->hub_name ?? ($booking->hubOwner->company ?? $booking->hubOwner->name ?? '—') }}</p>
            </td>
        </tr>
    </table>

    <table class="items">
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

    @if($booking->transaction_number)
        <div class="txn">
            Transaction ID<br>
            <span>{{ $booking->transaction_number }}</span>
        </div>
    @endif

    <div class="total">
        ₱{{ number_format((float) $booking->amount, 2) }}
        <small>Amount (PHP)</small>
    </div>

    @if($booking->notes)
        <p class="muted" style="margin-top:14px;"><strong>Notes:</strong> {{ $booking->notes }}</p>
    @endif
</body>
</html>
