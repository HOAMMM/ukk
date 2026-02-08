@extends('dashboard.template')

@section('content')
<style>
    .invoice-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    .invoice-tabs button {
        border: none;
        background: transparent;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 14px;
        color: #6c757d;
        transition: .2s;
    }

    .invoice-tabs button.active {
        background: #0d6efd;
        color: #fff;
    }

    .search-input {
        max-width: 220px;
        border-radius: 20px;
        font-size: 14px;
    }

    .btn-success-soft {
        background: #e7f6ee;
        color: #198754;
        border-radius: 20px;
        border: none;
    }

    .btn-warning-soft {
        background: #fff3cd;
        color: #b58105;
        border-radius: 20px;
        border: none;
    }

    .invoice-table thead th {
        font-size: 13px;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: none;
    }

    .invoice-table tbody tr:hover {
        background: #f8f9fa;
    }

    .badge-paid {
        background: #e6f4ea;
        color: #1e7e34;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .badge-unpaid {
        background: #fdecea;
        color: #dc3545;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .action-icons i {
        margin-left: 10px;
        cursor: pointer;
        color: #6c757d;
        transition: .2s;
    }

    .action-icons i:hover {
        color: #0d6efd;
    }
</style>
<div class="container mt-4">

    <div class="card invoice-card">
        <div class="card-body">

            {{-- FILTER TAB --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="invoice-tabs">
                    <button class="active">
                        All <span>{{ $orders->count() }}</span>
                    </button>
                    <button>
                        Unpaid <span>{{ $orders->where('order_status','unpaid')->count() }}</span>
                    </button>
                    <button>
                        Paid <span>{{ $orders->where('order_status','paid')->count() }}</span>
                    </button>
                    <button>
                        Archived <span>{{ $orders->where('order_status','archived')->count() }}</span>
                    </button>
                </div>

                <input type="text" class="form-control search-input" placeholder="Search order...">
            </div>

            {{-- ACTION --}}
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="btn btn-success-soft">✔ Mark as paid</button>
                <button class="btn btn-warning-soft">✖ Mark as unpaid</button>
                <button class="btn btn-light">🖨 Print</button>
                <button class="btn btn-light text-danger">🗑 Delete</button>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table invoice-table align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Order ID</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($orders as $order)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $order->order_id }}">
                            </td>
                            <td>#{{ $order->order_id }}</td>
                            <td class="fw-semibold">{{ $order->order_csname }}</td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                @if ($order->order_status == 'paid')
                                <span class="badge badge-paid">Paid</span>
                                @elseif ($order->order_status == 'unpaid')
                                <span class="badge badge-unpaid">Unpaid</span>
                                @else
                                <span class="badge bg-secondary">Archived</span>
                                @endif
                            </td>
                            <td>
                                Rp {{ number_format($order->order_total,0,',','.') }}
                            </td>
                            <td class="text-end action-icons">
                                <i class="bi bi-eye btn-detail"
                                    data-id="{{ $order->order_id }}"></i>
                                <i class="bi bi-pencil"></i>
                                <i class="bi bi-trash text-danger"></i>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Tidak ada data pesanan
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection