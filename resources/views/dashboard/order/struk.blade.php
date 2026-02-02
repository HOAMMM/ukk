@extends('dashboard.template')

@section('content')
<style>
    .struk-container {
        position: relative;
        margin: 0 auto !important;
        /* CENTER */
        left: 0;
        right: 0;
        box-shadow: none;
        width: 80mm;
        max-width: 80mm;
        border-radius: 0;
    }


    .struk-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        text-align: center;
    }

    .struk-header h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .struk-header p {
        margin: 5px 0 0;
        font-size: 13px;
        opacity: 0.95;
    }

    .struk-body {
        padding: 24px;
    }

    .struk-info {
        border-bottom: 2px dashed #e2e8f0;
        padding-bottom: 16px;
        margin-bottom: 16px;
    }

    .struk-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .struk-info-row .label {
        color: #718096;
        font-weight: 500;
    }

    .struk-info-row .value {
        color: #2d3748;
        font-weight: 600;
        text-align: right;
    }

    .struk-items {
        border-bottom: 2px dashed #e2e8f0;
        padding-bottom: 16px;
        margin-bottom: 16px;
    }

    .struk-items h6 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .item-name {
        color: #2d3748;
        flex: 1;
    }

    .item-qty {
        color: #718096;
        margin: 0 12px;
        font-weight: 500;
    }

    .item-price {
        color: #2d3748;
        font-weight: 600;
        text-align: right;
        min-width: 80px;
    }

    .struk-total {
        border-bottom: 2px dashed #e2e8f0;
        padding-bottom: 16px;
        margin-bottom: 16px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .total-row.grand-total {
        font-size: 18px;
        font-weight: 700;
        color: #667eea;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
    }

    .total-row .label {
        color: #2d3748;
    }

    .total-row .value {
        font-weight: 600;
        text-align: right;
    }

    .struk-footer {
        text-align: center;
        padding-top: 16px;
    }

    .thank-you {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .footer-note {
        font-size: 12px;
        color: #718096;
        margin-bottom: 20px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
    }

    .btn-action {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-print {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-back {
        background: #f7fafc;
        color: #2d3748;
        border: 2px solid #e2e8f0;
    }

    .btn-back:hover {
        background: #edf2f7;
    }

    /* PRINT STYLES */
    @media print {

        @page {
            size: 80mm auto;
            margin: 0;
        }

        /* SEMBUNYIKAN SEMUA */
        * {
            visibility: hidden;
        }

        /* TAMPILKAN STRUK SAJA */
        .struk-container,
        .struk-container * {
            visibility: visible;
        }

        /* ROOT CETAK */
        .struk-container {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80mm !important;
            max-width: 80mm !important;
            margin: 0;
            box-shadow: none;
            border-radius: 0;
            page-break-inside: avoid;
        }

        .action-buttons {
            display: none !important;
        }
    }



    /* =========================
   AUTO DETECT THERMAL 80mm
   ========================= */
    @media print and (max-width: 320px) {

        @page {
            size: 80mm auto;
            margin: 0;
        }

        html,
        body {
            width: 80mm;
            margin: 0 auto;
            padding: 0;
        }

        .struk-container {
            width: 80mm !important;
            max-width: 80mm !important;
            margin: 0 auto !important;
            box-shadow: none;
            border-radius: 0;
        }

        .struk-header {
            padding: 10px;
        }

        .struk-body {
            padding: 10px;
        }

        .struk-info-row,
        .item-row {
            font-size: 11px;
        }

        .total-row {
            font-size: 12px;
        }

        .total-row.grand-total {
            font-size: 15px;
        }
    }


    @media (max-width: 576px) {
        .struk-container {
            margin: 20px 16px;
            max-width: 100%;
        }
    }
</style>

<div class="struk-container">
    {{-- HEADER --}}
    <div class="struk-header">
        <h3>{{ config('app.name', 'RESTO') }}</h3>
        <p>Jl. Purwosari, Kec Purwosari Kab Pasuruan</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    {{-- BODY --}}
    <div class="struk-body">
        {{-- INFO TRANSAKSI --}}
        <div class="struk-info">
            <div class="struk-info-row">
                <span class="label">No. Order</span>
                <span class="value">#{{ $order->order_id }}</span>
            </div>
            <div class="struk-info-row">
                <span class="label">Kode Transaksi</span>
                <span class="value">{{ $transaksi->transaksi_code }}</span>
            </div>
            <div class="struk-info-row">
                <span class="label">Tanggal</span>
                <span class="value">{{ date('d/m/Y H:i', strtotime($transaksi->created_at)) }}</span>
            </div>
            <div class="struk-info-row">
                <span class="label">Kasir</span>
                <span class="value">{{ $order->order_csname }}</span>
            </div>
            <div class="struk-info-row">
                <span class="label">Pelanggan</span>
                <span class="value">{{ $transaksi->transaksi_csname ?? 'Umum' }}</span>
            </div>
            <div class="struk-info-row">
                <span class="label">Meja</span>
                <span class="value">{{ $meja->meja_nama }}</span>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="struk-items">
            <h6>Detail Pesanan</h6>
            @foreach($details as $d)
            <div class="item-row">
                <span class="item-name">{{ $d->trans_name }}</span>
                <span class="item-qty">x{{ $d->trans_qty }}</span>
                <span class="item-price">Rp {{ number_format($d->trans_subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        {{-- TOTAL --}}
        <div class="struk-total">
            <div class="total-row">
                <span class="label">Subtotal</span>
                <span class="value">Rp {{ number_format($transaksi->transaksi_total, 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span class="label">TOTAL</span>
                <span class="value">Rp {{ number_format($transaksi->transaksi_total, 0, ',', '.') }}</span>
            </div>
            <div class="total-row" style="margin-top: 12px;">
                <span class="label">Bayar</span>
                <span class="value">Rp {{ number_format($transaksi->transaksi_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="label">Kembalian</span>
                <span class="value">Rp {{ number_format($transaksi->transaksi_change, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="struk-footer">
            <p class="thank-you">Terima Kasih!</p>
            <p class="footer-note">Selamat menikmati hidangan Anda</p>

            <div class="action-buttons">
                <button onclick="window.print()" class="btn-action btn-print">
                    🖨️ Cetak Struk
                </button>
                <button onclick="window.location.href='{{ route('dashboard.order') }}'" class="btn-action btn-back">
                    ← Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto focus print dialog jika dari redirect pembayaran
    @if(session('print_auto'))
    window.onload = function() {
        setTimeout(() => {
            window.print();
        }, 500);
    };
    @endif
</script>

@endsection