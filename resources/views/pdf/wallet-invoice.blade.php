<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Invoice - WLT-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</title>
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

        .status-badge.is-debit {
            background: #fdeceb;
            color: #b3261e;
            border-color: #f3b8b3;
        }

        /* ---- Totals ---- */
        .totals-box {
            width: 260px;
            border-collapse: collapse;
            margin-left: auto;
            margin-top: 4px;
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
        $isCredit = strtolower($transaction->type) === 'credit';
        $currency = $transaction->currency ?? 'INR';
        $currencySymbol = $currency === 'USD' ? '$' : 'Rs. ';
        $invoiceNo = 'WLT-' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT);
        $description = $transaction->remark ?? ($isCredit ? 'Wallet Credit' : 'Wallet Debit');
    @endphp
    <table class="letterhead-table">
        <tr>
            <td style="width: 62%;">
                <p class="company-name">EKAGRATA ONE UNIVERSAL PRIVATE LIMITED</p>
                <p class="company-meta">GSTIN / Registration Number: 24AAJCE0403B1ZI</p>
                <p class="company-meta">Wallet Transaction</p>
            </td>
            <td style="width: 38%;">
                <p class="invoice-title">Invoice</p>
                <p class="invoice-subtitle">Wallet Statement</p>
            </td>
        </tr>
    </table>

    <div class="page">
        <table class="meta-strip">
            <tr>
                <td>
                    <div class="meta-label">Invoice No.</div>
                    <div class="meta-value">{{ $invoiceNo }}</div>
                </td>
                <td>
                    <div class="meta-label">Invoice Date</div>
                    <div class="meta-value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M, Y') }}</div>
                </td>
                <td>
                    <div class="meta-label">Transaction Type</div>
                    <div class="meta-value">
                        <span class="status-badge {{ $isCredit ? '' : 'is-debit' }}">{{ ucfirst($transaction->type) }}</span>
                    </div>
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
                    <div class="party-name">{{ $transaction->user->name ?? 'N/A' }}</div>
                    <div class="party-line">
                        @if ($transaction->user->mobile ?? null)
                            Mobile: {{ $transaction->user->mobile }}<br>
                        @endif
                        @if ($transaction->user->email ?? null)
                            Email: {{ $transaction->user->email }}<br>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 54%;">Description</th>
                <th class="text-center" style="width: 15%;">Type</th>
                <th class="text-right" style="width: 25%;">Amount</th>
            </tr>
            <tr>
                <td>1</td>
                <td>
                    <div class="item-title">{{ $description }}</div>
                    @if ($transaction->package)
                        <div class="item-sub">Wallet Package #{{ $transaction->package->id }}</div>
                    @endif
                </td>
                <td class="text-center">{{ ucfirst($transaction->type) }}</td>
                <td class="text-right">
                    {{ $isCredit ? '+' : '-' }} {{ $currencySymbol }}{{ number_format((float) $transaction->amount, 2) }}
                </td>
            </tr>
        </table>

        <table class="totals-box">
            <tr>
                <td>Previous Balance</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format((float) $transaction->previous_balance, 2) }}
                </td>
            </tr>
            <tr>
                <td>{{ $isCredit ? 'Amount Credited' : 'Amount Debited' }}</td>
                <td class="text-right">{{ $isCredit ? '+' : '-' }}
                    {{ $currencySymbol }}{{ number_format((float) $transaction->amount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Updated Balance</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format((float) $transaction->updated_balance, 2) }}
                </td>
            </tr>
        </table>

        <p class="amount-words">Amount in words:
            {{ ucwords(\App\Helpers\NumberToText::convertNumberToText((float) $transaction->amount, $currency === 'USD' ? 'US Dollars' : 'Rupees', $currency === 'USD' ? 'Cents' : 'Paise')) }}
        </p>
        <p class="note">This is a system-generated invoice for the wallet transaction referenced above
            and does not require a physical signature.</p>

        <div class="footer">
            EKAGRATA ONE UNIVERSAL PRIVATE LIMITED &middot; GSTIN / Registration Number: 24AAJCE0403B1ZI &middot;
            Powered by Phoneshree
        </div>
    </div>
</body>

</html>
