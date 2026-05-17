@extends('admin.layouts.app')

@section('page-title', 'Print Bill')
@section('page-subtitle', 'Review and print completed invoice')

@section('content')

@php
    $appSettings = \App\Models\AppSetting::allAsArray();
    $shopNameLineOne = $appSettings['shop_name'] ?? 'Excel Power';
    $shopNameLineTwo = $appSettings['shop_name_second_line'] ?? 'Hardware and Tools';
    $shopAddress = $appSettings['address'] ?? 'Address';
    $shopPhone = $appSettings['phone_number'] ?? 'Phone Number';
    $shopLogo = $appSettings['logo'] ?? null;
    $receiptFooter = $appSettings['footer_message'] ?: 'Thank you, Come again';
    $printSize = $appSettings['print_size'] ?? '80mm';
    $pageWidth = $printSize === '58mm' ? '58mm' : '80mm';
    $printWidth = $printSize === '58mm' ? '52mm' : '74mm';
    $receiptDate = ($sale->created_at ?? now())->copy()->timezone($appSettings['timezone'] ?? config('app.timezone'));
    $separator = str_repeat('-', 42);
    $receiptMoney = function ($value) {
        return abs((float) $value) < 0.005 ? '00' : number_format((float) $value, 0, '.', '');
    };
    $receiptRows = $sale->items->map(function ($item) {
        $variant = $item->variant;
        $quantity = (int) $item->quantity;
        $ourPrice = (float) ($variant->our_price ?? $item->unit_price);
        $unitPrice = (float) $item->unit_price;
        $discPrice = $unitPrice < $ourPrice ? $unitPrice : 0;
        $total = (float) $item->line_total;
        $profit = $discPrice > 0 ? ($ourPrice - $discPrice) * $quantity : 0;

        return [
            'item' => $item,
            'quantity' => $quantity,
            'our_price' => $ourPrice,
            'disc_price' => $discPrice,
            'total' => $total,
            'profit' => $profit,
        ];
    });
    $receiptSubtotal = $receiptRows->sum('total');
    $receiptProfit = $receiptRows->sum('profit');
@endphp

<style>
.receipt-shell {
    display: flex;
    justify-content: center;
    width: 100%;
}

.receipt {
    box-sizing: border-box;
    width: {{ $pageWidth }};
    max-width: 100%;
    background: #fff;
    color: #000;
    border: 1px solid #111;
    padding: 14px 12px;
    font-family: "Courier New", Consolas, monospace;
    font-size: 15px;
    line-height: 1.25;
}

.receipt-header {
    text-align: center;
}

.receipt-logo {
    display: inline-block;
    height: 92px;
    margin-bottom: 4px;
    width: 116px;
}

.receipt-logo svg,
.receipt-logo img {
    display: block;
    height: 100%;
    object-fit: contain;
    filter: grayscale(1) contrast(1.35);
    width: 100%;
}

.shop-name {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 6px;
}

.shop-meta {
    font-size: 13px;
    line-height: 1.35;
}

.dash {
    font-weight: 800;
    letter-spacing: 0;
    margin: 8px 0;
    overflow: hidden;
    white-space: nowrap;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 1px 0;
    font-size: 13px;
}

.receipt-label {
    white-space: nowrap;
}

.receipt-value {
    text-align: right;
}

.items-grid {
    width: 100%;
}

.items-head,
.item-amounts {
    display: grid;
    gap: 4px;
    grid-template-columns: 32px 63px 63px 63px;
    margin-left: 40px;
}

.items-head {
    align-items: end;
    font-size: 11px;
    font-weight: 900;
    line-height: 1.15;
    margin-left: 56px;
}

.item-name {
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    padding: 2px 0 5px;
}

.items-head > div,
.item-amounts > div {
    min-width: 0;
}

.item-amounts > div {
    text-align: right;
}

.items-head > div {
    text-align: left;
}

.item-amounts {
    align-items: start;
    font-size: 12px;
    padding-bottom: 6px;
}

.item-money {
    white-space: nowrap;
}

.summary-block {
    margin-left: auto;
    width: 70%;
}

.summary-block .receipt-row {
    font-size: 13px;
    padding: 3px 0;
}

.grand-row {
    font-size: 14px !important;
    font-weight: 900;
}

.profit-row {
    font-size: 13px;
    font-weight: 900;
    padding: 3px 0;
    justify-content: center;
    gap: 20px;
}

.thank-you {
    font-size: 13px;
    font-weight: 900;
    text-align: center;
}

.print-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 20px;
}

.print-btn {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    border: none;
    border-radius: 14px;
    color: #fff;
    font-weight: 850;
    padding: 12px 20px;
}

@page {
    size: {{ $pageWidth }} 1000mm;
    margin: 3mm;
}

@media print {
    html,
    body {
        margin: 0 !important;
        min-height: 0 !important;
        width: {{ $pageWidth }} !important;
    }

    body {
        background: #fff !important;
    }

    .sidebar,
    .topbar,
    .print-actions,
    .page-header {
        display: none !important;
    }

    .main-content {
        margin: 0 !important;
        width: 100% !important;
    }

    .content-area {
        padding: 0 !important;
    }

    .receipt-shell {
        display: block;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .receipt {
        border: none;
        break-after: avoid;
        break-before: avoid;
        break-inside: avoid;
        box-shadow: none;
        margin: 0;
        min-height: max-content;
        padding: 0;
        page-break-after: avoid;
        page-break-before: avoid;
        page-break-inside: avoid;
        width: {{ $printWidth }};
    }

    .receipt-header,
    .items-grid,
    .item-name,
    .item-amounts,
    .receipt-row,
    .dash,
    .summary-block,
    .thank-you {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>

<div class="receipt-shell">
    <div class="receipt">
        <div class="receipt-header">
            <div class="receipt-logo">
                @if($shopLogo)
                    <img src="{{ asset('storage/' . $shopLogo) }}" alt="{{ $shopNameLineOne }} logo">
                @else
                <svg viewBox="0 0 140 120" aria-label="Excel Power Hardware and Tools logo" role="img">
                    <path d="M70 5 L79 7 L81 18 L91 21 L100 15 L108 21 L104 32 L112 41 L124 39 L129 49 L120 57 L120 68 L129 76 L124 86 L112 84 L104 93 L108 104 L100 110 L91 104 L81 107 L79 118 L70 120 L61 118 L59 107 L49 104 L40 110 L32 104 L36 93 L28 84 L16 86 L11 76 L20 68 L20 57 L11 49 L16 39 L28 41 L36 32 L32 21 L40 15 L49 21 L59 18 L61 7 Z" fill="#000"/>
                    <circle cx="70" cy="62" r="39" fill="#fff"/>
                    <path d="M39 61 L70 32 L101 61" fill="none" stroke="#000" stroke-width="8" stroke-linecap="square" stroke-linejoin="miter"/>
                    <path d="M93 42 V65 H104 V51" fill="none" stroke="#000" stroke-width="7" stroke-linejoin="miter"/>
                    <rect x="63" y="57" width="8" height="8" fill="#000"/>
                    <rect x="75" y="57" width="8" height="8" fill="#000"/>
                    <rect x="63" y="69" width="8" height="8" fill="#000"/>
                    <rect x="75" y="69" width="8" height="8" fill="#000"/>
                    <path d="M43 78 L79 111" stroke="#000" stroke-width="11" stroke-linecap="round"/>
                    <circle cx="43" cy="78" r="12" fill="#fff"/>
                    <path d="M34 70 L43 79 L52 70" fill="none" stroke="#000" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M98 77 L61 112" stroke="#000" stroke-width="11" stroke-linecap="round"/>
                    <circle cx="98" cy="77" r="12" fill="#fff"/>
                    <path d="M89 69 L98 78 L107 69" fill="none" stroke="#000" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @endif
            </div>
            <div class="shop-name">
                {{ $shopNameLineOne }}<br>
                {{ $shopNameLineTwo }}
            </div>
            <div class="shop-meta">
                {{ $shopAddress }}<br>
                {{ $shopPhone }}
            </div>
        </div>

        <div class="dash">{{ $separator }}</div>

        <div class="receipt-row">
            <span class="receipt-label">Date:</span>
            <span class="receipt-value">{{ $receiptDate->format('d/m/Y h:i A') }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Cashier Name:</span>
            <span class="receipt-value">{{ $sale->cashier->name ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Invoice No:</span>
            <span class="receipt-value">{{ $sale->invoice_no ?? 'N/A' }}</span>
        </div>

        <div class="dash">{{ $separator }}</div>

        <div class="items-grid">
            <div class="items-head">
                <div>QTY</div>
                <div>Our<br>Price<br>(Rs.)</div>
                <div>Disc<br>Price<br>(Rs.)</div>
                <div>Total</div>
            </div>

            <div class="dash">{{ $separator }}</div>

            @foreach($receiptRows as $row)
                @php
                    $item = $row['item'];
                @endphp

                <div class="item-name">{{ $item->product_name }} {{ $item->variant_name }}</div>
                <div class="item-amounts">
                    <div>{{ $row['quantity'] }}</div>
                    <div class="item-money">{{ $receiptMoney($row['our_price']) }}</div>
                    <div class="item-money">{{ $receiptMoney($row['disc_price']) }}</div>
                    <div class="item-money">{{ $receiptMoney($row['total']) }}</div>
                </div>

                <div class="dash">{{ $separator }}</div>
            @endforeach
        </div>

        <div class="summary-block">
            <div class="receipt-row">
                <span>Sub Total :</span>
                <span>{{ $receiptMoney($receiptSubtotal) }}</span>
            </div>
            <div class="receipt-row">
                <span>Tax({{ number_format($sale->tax_rate, 0) }}%) :</span>
                <span>{{ $receiptMoney($sale->tax_amount) }}</span>
            </div>
        </div>

        <div class="dash">{{ $separator }}</div>

        <div class="summary-block">
            <div class="receipt-row grand-row">
                <span>Grand Total :</span>
                <span>{{ $receiptMoney($sale->grand_total) }}</span>
            </div>
        </div>

        <div class="dash">{{ $separator }}</div>

        <div class="receipt-row">
            <span>Paid Amount :</span>
            <span>{{ $receiptMoney($sale->paid_amount) }}</span>
        </div>
        <div class="receipt-row">
            <span>Balance :</span>
            <span>{{ $receiptMoney($sale->balance_amount) }}</span>
        </div>
        <div class="dash">{{ $separator }}</div>

        <div class="receipt-row">
            <span>No. of Items :</span>
            <span>{{ $receiptRows->count() }}</span>
        </div>

        <div class="dash">{{ $separator }}</div>

        <div class="receipt-row profit-row">
            <span>Your Profit - Rs. {{ $receiptMoney($receiptProfit) }}</span>
        </div>

        <div class="dash">{{ $separator }}</div>

        <div class="thank-you">{{ $receiptFooter }}</div>
    </div>
</div>

<div class="print-actions">
    <button type="button" onclick="printReceipt()" class="print-btn">
        <i class="bi bi-printer me-1"></i> Print Bill
    </button>
    <a href="{{ route('admin.sales.index') }}" class="btn btn-light fw-bold">New Bill</a>
</div>

<script>
function setReceiptPageHeight() {
    const receipt = document.querySelector('.receipt');
    if (!receipt) return;

    const pxToMm = 25.4 / 96;
    const receiptHeightMm = Math.ceil(receipt.scrollHeight * pxToMm);
    const pageHeightMm = Math.max(receiptHeightMm + 8, 120);
    let style = document.getElementById('receipt-page-height');

    if (!style) {
        style = document.createElement('style');
        style.id = 'receipt-page-height';
        document.head.appendChild(style);
    }

    style.textContent = `@page { size: {{ $pageWidth }} ${pageHeightMm}mm; margin: 3mm; }`;
}

function printReceipt() {
    setReceiptPageHeight();
    window.print();
}

window.addEventListener('load', setReceiptPageHeight);
window.addEventListener('beforeprint', setReceiptPageHeight);
</script>

@endsection
