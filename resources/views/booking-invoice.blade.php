<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice — Booking #{{ $booking->id }} | {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 2rem; color: #111827; background: #f9fafb; }
        .sheet { max-width: 720px; margin: 0 auto; background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); border: 1px solid #e5e7eb; }
        .invoice-brand { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; padding-bottom: 1.25rem; margin-bottom: 1.25rem; border-bottom: 1px solid #e5e7eb; }
        .invoice-brand h1 { font-size: 1.5rem; margin: 0 0 0.25rem; color: #0f766e; }
        .invoice-id-badge { text-align: right; }
        .invoice-id-badge code { font-family: ui-monospace, monospace; font-size: 0.8125rem; font-weight: 600; padding: 0.35rem 0.65rem; border-radius: 0.5rem; background: #f0fdfa; color: #115e59; border: 1px solid #99f6e4; }
        .invoice-id-badge .issued { display: block; font-size: 0.6875rem; color: #94a3b8; margin-top: 0.25rem; }
        .muted { color: #6b7280; font-size: 0.875rem; }
        .row { display: flex; justify-content: space-between; gap: 1rem; margin-top: 0.75rem; flex-wrap: wrap; }
        .row:first-of-type { margin-top: 0; }
        .block { flex: 1; min-width: 200px; padding: 0.875rem 1rem; border-radius: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; }
        .block h2 { font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin: 0 0 0.375rem; }
        .block p { margin: 0; font-weight: 600; color: #1e293b; }
        .block .muted { font-weight: 400; font-size: 0.8125rem; }
        .invoice-status-pill { display: inline-block; margin-top: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: capitalize; background: #e2e8f0; color: #475569; }
        .sheet[data-booking-status="pending"] .invoice-status-pill { background: #fef3c7; color: #92400e; }
        .sheet[data-booking-status="confirmed"] .invoice-status-pill { background: #d1fae5; color: #065f46; }
        .sheet[data-booking-status="completed"] .invoice-status-pill { background: #dbeafe; color: #1e40af; }
        .sheet[data-booking-status="cancelled"] .invoice-status-pill,
        .sheet[data-booking-status="rejected"] .invoice-status-pill { background: #fee2e2; color: #991b1b; }
        .invoice-table-wrap { margin-top: 1.25rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; margin-top: 0; font-size: 0.9375rem; }
        thead { background: #f1f5f9; }
        th, td { text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb; }
        th { color: #475569; font-weight: 700; font-size: 0.6875rem; text-transform: uppercase; letter-spacing: 0.06em; }
        tbody tr:last-child td { border-bottom: none; }
        .invoice-txn { margin-top: 1rem; padding: 0.65rem 0.85rem; border-radius: 0.5rem; background: #f8fafc; border: 1px dashed #cbd5e1; font-size: 0.8125rem; color: #475569; }
        .invoice-txn span { font-family: ui-monospace, monospace; font-weight: 600; color: #334155; }
        .total { font-size: 1.15rem; font-weight: 800; margin-top: 1.25rem; text-align: center; padding: 1rem 1.15rem; border-radius: 0.75rem; background: linear-gradient(135deg, #0f766e, #14b8a6); color: #fff; box-shadow: 0 4px 14px rgba(13, 148, 136, 0.25); }
        .total small { display: block; margin-top: 0.25rem; font-size: 0.6875rem; font-weight: 600; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.04em; }
        .actions { margin-top: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; font-size: 0.875rem; text-decoration: none; cursor: pointer; border: none; }
        .btn-pdf { background: #0369a1; color: #fff; }
        .btn-print { background: #0d9488; color: #fff; }
        .btn-back { background: #e5e7eb; color: #374151; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    @include('partials.booking-invoice-sheet', ['showActions' => true])
</body>
</html>
