@extends('dashboard.template')

@section('content')
<div class="container mt-4">

    <!-- CARD -->
    <div class="card invoice-card">
        <div class="card-body">

            <!-- HEADER FILTER -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                <div class="invoice-tabs">
                    <button class="active">All <span>35</span></button>
                    <button>Unpaid <span>5</span></button>
                    <button>Paid <span>23</span></button>
                    <button>Archived <span>17</span></button>
                </div>

                <input type="text" class="form-control search-input" placeholder="Search invoice">
            </div>

            <!-- ACTION BUTTON -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="btn btn-success-soft">✔ Mark as paid</button>
                <button class="btn btn-warning-soft">✖ Mark as unpaid</button>
                <button class="btn btn-light">🖨 Print</button>
                <button class="btn btn-light text-danger">🗑 Delete</button>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table invoice-table align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Invoice</th>
                            <th>Company</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>#1001</td>
                            <td class="fw-semibold">Tech Jungle</td>
                            <td>14 Sep 2022</td>
                            <td><span class="badge badge-unpaid">Unpaid</span></td>
                            <td>$973.48</td>
                            <td class="text-end action-icons">
                                <i class="bi bi-eye"></i>
                                <i class="bi bi-pencil"></i>
                                <i class="bi bi-trash"></i>
                            </td>
                        </tr>

                        <tr class="active-row">
                            <td><input type="checkbox" checked></td>
                            <td>#1001</td>
                            <td class="fw-semibold">Tech Jungle</td>
                            <td>14 Sep 2022</td>
                            <td><span class="badge badge-paid">Paid</span></td>
                            <td>$480.21</td>
                            <td class="text-end action-icons">
                                <i class="bi bi-eye"></i>
                                <i class="bi bi-pencil"></i>
                                <i class="bi bi-trash"></i>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection