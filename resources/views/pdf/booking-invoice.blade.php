<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Invoice - {{ $booking->booking_uid ?? 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        @page {
            size: A4;
            margin: 28px 36px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            font-size: 13px;
            margin: 0;
        }

        .page {
            padding: 0;
        }

        /* ---- Letterhead band ---- */
        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            background: #12243e;
            margin-bottom: 20px;
        }

        .letterhead-table td {
            padding: 18px 24px;
            vertical-align: top;
        }

        .company-name {
            font-size: 17px;
            font-weight: bold;
            color: #ffffff;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .company-meta {
            font-size: 10.5px;
            color: #b9c2d0;
            margin: 3px 0 0 0;
        }

        .invoice-title {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #ffffff;
            text-transform: uppercase;
            margin: 0;
        }

        .invoice-subtitle {
            text-align: right;
            font-size: 10.5px;
            color: #d4af37;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ---- Meta strip ---- */
        .meta-strip {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 22px;
        }

        .meta-strip td {
            padding: 10px 16px;
            border: 1px solid #e4e7ec;
            background: #f7f8fa;
            width: 33.33%;
        }

        .meta-label {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #8a94a3;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: bold;
            color: #12243e;
        }

        /* ---- Billing cards ---- */
        .parties-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }

        .parties-table td {
            width: 50%;
            vertical-align: top;
            padding: 16px;
            border: 1px solid #e4e7ec;
        }

        .parties-table td.bill-by {
            border-right: none;
        }

        .party-label {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #8a94a3;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .party-name {
            font-size: 14px;
            font-weight: bold;
            color: #12243e;
            margin-bottom: 4px;
        }

        .party-line {
            font-size: 12px;
            color: #4a5568;
            line-height: 1.7;
        }

        /* ---- Items table ---- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 24px;
        }

        .items-table th {
            background: #12243e;
            color: #ffffff;
            text-align: left;
            padding: 11px 10px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #e4e7ec;
            font-size: 12.5px;
            vertical-align: top;
        }

        .item-title {
            font-weight: bold;
            color: #12243e;
        }

        .item-sub {
            font-size: 10.5px;
            color: #8a94a3;
            margin-top: 3px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            background: #e5f5ea;
            color: #1e7e34;
            border: 1px solid #a9dbb8;
        }

        .status-badge.is-pending {
            background: #fdf3e3;
            color: #a15c00;
            border-color: #f2d49b;
        }

        /* ---- Totals ---- */
        .totals-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .totals-wrap td {
            vertical-align: top;
            padding: 0;
        }

        .totals-box {
            width: 260px;
            border-collapse: collapse;
            margin-left: auto;
        }

        .totals-box td {
            padding: 7px 10px;
            font-size: 12.5px;
            color: #4a5568;
        }

        .totals-box tr.grand-total td {
            border-top: 2px solid #12243e;
            font-weight: bold;
            font-size: 15px;
            color: #12243e;
            padding-top: 12px;
        }

        /* ---- Footer ---- */
        .amount-words {
            margin-top: 22px;
            font-size: 12px;
            color: #4a5568;
            padding: 10px 14px;
            background: #f7f8fa;
            border-left: 3px solid #d4af37;
        }

        .note {
            margin-top: 14px;
            font-size: 10.5px;
            color: #8a94a3;
        }

        .footer {
            margin-top: 40px;
            padding-top: 14px;
            border-top: 1px solid #e4e7ec;
            font-size: 10px;
            color: #8a94a3;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $invoiceDate = $booking->confirmed_at ?? $booking->created_at;
        $status = $booking->transaction->status ?? $booking->status ?? 'confirmed';
        $isPaid = in_array(strtolower($status), ['paid', 'captured', 'confirmed', 'success']);
        $currency = $booking->currency ?? 'INR';
        $currencySymbol = $currency === 'USD' ? '$' : 'Rs. ';
        $amount = $booking->transaction->amount ?? $booking->final_total ?? 0;
        $astroName = trim(
            ($booking->astro->first_name ?? '') .
                ' ' .
                ($booking->astro->middle_name ?? '') .
                ' ' .
                ($booking->astro->last_name ?? ''),
        );
        $astroName = $astroName !== '' ? $astroName : 'Astrologer';
    @endphp
    <table class="letterhead-table">
        <tr>
            <td style="width: 62%;">
                <p class="company-name">EKAGRATA ONE UNIVERSAL PRIVATE LIMITED</p>
                <p class="company-meta">GSTIN / Registration Number: 24AAJCE0403B1ZI</p>
                <p class="company-meta">Consultation Booking</p>
            </td>
            <td style="width: 38%;">
                <p class="invoice-title">Invoice</p>
                <p class="invoice-subtitle">Payment Receipt</p>
            </td>
        </tr>
    </table>

    <div class="page">
        <table class="meta-strip">
            <tr>
                <td>
                    <div class="meta-label">Invoice No.</div>
                    <div class="meta-value">{{ $booking->booking_uid ?? 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td>
                    <div class="meta-label">Invoice Date</div>
                    <div class="meta-value">{{ \Carbon\Carbon::parse($invoiceDate)->format('d M, Y') }}</div>
                </td>
                <td>
                    <div class="meta-label">Status</div>
                    <div class="meta-value">{{ ucfirst($status) }}</div>
                </td>
            </tr>
        </table>

        <table class="parties-table">
            <tr>
                <td class="bill-by">
                    <div class="party-label">Billed By</div>
                    <div class="party-name">EKAGRATA ONE UNIVERSAL PRIVATE LIMITED</div>
                    <div class="party-line">GSTIN / Registration Number: 24AAJCE0403B1ZI</div>
                </td>
                <td>
                    <div class="party-label">Billed To</div>
                    <div class="party-name">{{ $booking->name ?? ($booking->user->name ?? 'N/A') }}</div>
                    <div class="party-line">
                        @if ($booking->mobile)
                            Mobile: {{ $booking->mobile }}<br>
                        @endif
                        @if ($booking->user && $booking->user->email)
                            Email: {{ $booking->user->email }}<br>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 42%;">Description</th>
                <th style="width: 20%;">Session</th>
                <th class="text-center" style="width: 14%;">Duration</th>
                <th class="text-right" style="width: 18%;">Amount</th>
            </tr>
            <tr>
                <td>1</td>
                <td>
                    <div class="item-title">{{ ucfirst($booking->type ?? 'Consultation') }} Consultation with
                        {{ $astroName }}</div>
                    <div class="item-sub">Booking ID: {{ $booking->booking_uid ?? $booking->id }}</div>
                </td>
                <td>
                    {{ $booking->session_date ? \Carbon\Carbon::parse($booking->session_date)->format('d M, Y') : 'N/A' }}
                    <div class="item-sub">
                        {{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('h:i A') : '' }}
                        @if ($booking->start_time && $booking->end_time)
                            -
                        @endif
                        {{ $booking->end_time ? \Carbon\Carbon::parse($booking->end_time)->format('h:i A') : '' }}
                    </div>
                </td>
                <td class="text-center">{{ $booking->duration ?? 'N/A' }} Min(s)</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format((float) ($booking->final_total ?? 0), 2) }}
                </td>
            </tr>
        </table>

        <table class="totals-box">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format((float) ($booking->sub_total ?? 0), 2) }}
                </td>
            </tr>
            @if ($booking->promocode_discount > 0)
                <tr>
                    <td>Promocode Discount ({{ $booking->promocode }})</td>
                    <td class="text-right">- {{ $currencySymbol }}{{ number_format((float) $booking->promocode_discount, 2) }}
                    </td>
                </tr>
            @endif
            @if ($booking->wallet > 0)
                <tr>
                    <td>Wallet Used</td>
                    <td class="text-right">- {{ $currencySymbol }}{{ number_format((float) $booking->wallet, 2) }}
                    </td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>Total Paid</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format((float) $amount, 2) }}</td>
            </tr>
        </table>

        <p class="amount-words">Amount in words:
            {{ ucwords(\App\Helpers\NumberToText::convertNumberToText((float) $amount, $currency === 'USD' ? 'US Dollars' : 'Rupees', $currency === 'USD' ? 'Cents' : 'Paise')) }}
        </p>
        <p class="note">This is a system-generated invoice for the consultation booking referenced above
            and does not require a physical signature.</p>

        <div class="footer">
            EKAGRATA ONE UNIVERSAL PRIVATE LIMITED &middot; GSTIN / Registration Number: 24AAJCE0403B1ZI &middot;
            Powered by Phoneshree
        </div>
    </div>
</body>

</html>
