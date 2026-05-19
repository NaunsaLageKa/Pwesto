{{-- HTML fragment for the booking history invoice modal --}}
<div class="booking-invoice-modal-inner">
    <style>
        .booking-invoice-modal-inner { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; color: #0f172a; line-height: 1.5; }
        .booking-invoice-modal-inner * { box-sizing: border-box; }

        .booking-invoice-modal-inner .sheet {
            margin: 0;
            max-width: 100%;
            padding: 1.5rem 1.25rem 1.75rem;
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        @media (min-width: 640px) {
            .booking-invoice-modal-inner .sheet { padding: 1.75rem 1.75rem 2rem; }
        }

        .booking-invoice-modal-inner .invoice-brand {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .booking-invoice-modal-inner .invoice-brand h1 {
            margin: 0;
            font-size: 1.375rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f766e;
            line-height: 1.2;
        }
        .booking-invoice-modal-inner .invoice-brand .muted {
            margin: 0.35rem 0 0;
            font-size: 0.8125rem;
            color: #64748b;
        }
        .booking-invoice-modal-inner .invoice-id-badge {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
            text-align: right;
        }
        .booking-invoice-modal-inner .invoice-id-badge code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 0.5rem;
            background: #f0fdfa;
            color: #115e59;
            border: 1px solid #99f6e4;
        }
        .booking-invoice-modal-inner .invoice-id-badge .issued {
            font-size: 0.6875rem;
            color: #94a3b8;
        }

        .booking-invoice-modal-inner .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }
        @media (max-width: 480px) {
            .booking-invoice-modal-inner .row { grid-template-columns: 1fr; }
        }
        .booking-invoice-modal-inner .row:first-of-type { margin-top: 0; }

        .booking-invoice-modal-inner .block {
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .booking-invoice-modal-inner .block h2 {
            margin: 0 0 0.375rem;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }
        .booking-invoice-modal-inner .block p { margin: 0; font-size: 0.9375rem; font-weight: 600; color: #1e293b; }
        .booking-invoice-modal-inner .block .muted { font-weight: 400; font-size: 0.8125rem; color: #64748b; margin-top: 0.25rem; }

        .booking-invoice-modal-inner .invoice-status-pill {
            display: inline-flex;
            align-items: center;
            margin-top: 0.25rem;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
            letter-spacing: 0.02em;
            background: #e2e8f0;
            color: #475569;
        }
        .booking-invoice-modal-inner [data-booking-status="pending"] .invoice-status-pill { background: #fef3c7; color: #92400e; }
        .booking-invoice-modal-inner [data-booking-status="confirmed"] .invoice-status-pill { background: #d1fae5; color: #065f46; }
        .booking-invoice-modal-inner [data-booking-status="completed"] .invoice-status-pill { background: #dbeafe; color: #1e40af; }
        .booking-invoice-modal-inner [data-booking-status="cancelled"] .invoice-status-pill,
        .booking-invoice-modal-inner [data-booking-status="rejected"] .invoice-status-pill { background: #fee2e2; color: #991b1b; }

        .booking-invoice-modal-inner .invoice-table-wrap {
            margin-top: 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            background: #fff;
        }
        .booking-invoice-modal-inner table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .booking-invoice-modal-inner thead { background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%); }
        .booking-invoice-modal-inner th {
            text-align: left;
            padding: 0.65rem 1rem;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }
        .booking-invoice-modal-inner td {
            padding: 0.85rem 1rem;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .booking-invoice-modal-inner tbody tr:last-child td { border-bottom: none; }
        .booking-invoice-modal-inner td strong { color: #0f172a; font-weight: 700; }

        .booking-invoice-modal-inner .invoice-txn {
            margin-top: 1rem;
            padding: 0.65rem 0.85rem;
            border-radius: 0.5rem;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            font-size: 0.8125rem;
            color: #475569;
        }
        .booking-invoice-modal-inner .invoice-txn span { font-family: ui-monospace, monospace; font-weight: 600; color: #334155; }

        .booking-invoice-modal-inner .total {
            margin-top: 1.25rem;
            padding: 1rem 1.15rem;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #14b8a6 100%);
            color: #fff;
            font-size: 1.05rem;
            font-weight: 800;
            text-align: center;
            letter-spacing: -0.02em;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        }
        .booking-invoice-modal-inner .total small {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.6875rem;
            font-weight: 600;
            opacity: 0.85;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .booking-invoice-modal-inner .actions { margin-top: 1.5rem; }
    </style>
    @include('partials.booking-invoice-sheet')
</div>
