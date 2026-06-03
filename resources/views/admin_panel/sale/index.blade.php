@extends('admin_panel.layout.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --bg-body: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-md: 8px;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg-body); color: var(--text-main); }
        .page-container { max-width: 1400px; margin: 0 auto; padding: 16px; }
        .table-card {
            background: #fff;
            border-radius: var(--radius-md);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        .table-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }
        .table-card-header h5 { font-size: 0.9rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
        .table-card-body { padding: 0; }
        #salesTable {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 0.78rem;
        }
        #salesTable thead th {
            padding: 8px 10px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #fff;
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
            background: var(--primary);
            white-space: nowrap;
        }
        #salesTable tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        #salesTable tbody tr:hover { background: #f8fafc; }
        #salesTable tbody tr:last-child td { border-bottom: none; }
        .filter-card {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-bottom: 0;
        }
        .filter-card .form-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; letter-spacing: 0.02em; }
        .filter-card .form-control, .filter-card .form-select {
            padding: 6px 10px;
            font-size: 0.82rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
        }
        .filter-card .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .action-btn {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            background: #fff;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .action-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
        .action-btn.primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .action-btn.primary:hover { background: var(--primary-hover); }
        .action-btn.danger { border-color: #ef4444; color: #ef4444; }
        .action-btn.danger:hover { background: #fef2f2; }
        .action-btn.warning { border-color: #f59e0b; color: #f59e0b; }
        .action-btn.warning:hover { background: #fffbeb; }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .dropdown-more .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 4px;
            font-size: 0.78rem;
            min-width: 150px;
        }
        .dropdown-more .dropdown-item {
            padding: 6px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dropdown-more .dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
        @media (max-width: 767px) {
            .filter-card .row > div { margin-bottom: 8px; }
        }
    </style>

    <div class="page-container">
        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="font-size:1.1rem">Sales</h4>
                <small class="text-muted">Manage all sales transactions</small>
            </div>
            <div class="d-flex gap-2">
                @can('sales.create')
                    <a href="{{ route('sale.add') }}" class="action-btn primary"><i class="las la-plus"></i> Add Sale</a>
                @endcan
                <a href="{{ url('bookings') }}" class="action-btn"><i class="las la-calendar"></i> Bookings</a>
                <a href="{{ route('sale.return.index') }}" class="action-btn warning"><i class="las la-undo"></i> Returns</a>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-card-header">
                <span style="font-size:0.8rem;color:var(--text-muted)">{{ $sales->count() }} records</span>
                <button class="action-btn" onclick="resetFilters()" style="font-size:0.7rem"><i class="las la-undo"></i> Reset</button>
            </div>
            <div class="table-card-body">
                {{-- Filters --}}
                <div class="filter-card mx-3 mt-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" id="min_date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" id="max_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Invoice</label>
                            <input type="text" id="invoice_search" class="form-control" placeholder="e.g. INV-001">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Customer</label>
                            <input type="text" id="customer_search" class="form-control" placeholder="Search by name...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table id="salesTable" class="table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Reference</th>
                                <th>Products</th>
                                <th>Qty</th>
                                <th>Gross</th>
                                <th>Disc</th>
                                <th>Net Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                @php
                                    $pNames = 'N/A';
                                    if ($sale->items && $sale->items->count() > 0) {
                                        $pNames = $sale->items->map(fn($item) => optional($item->product)->item_name ?? '?')->implode(', ');
                                    } elseif ($sale->product) {
                                        $pNames = $sale->product;
                                    }
                                    $statusClass = 'bg-secondary';
                                    $statusText = 'Draft';
                                    if ($sale->sale_status === 'posted') {
                                        $statusClass = 'bg-primary';
                                        $statusText = 'Posted';
                                    } elseif ($sale->sale_status === 'returned') {
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Returned';
                                    } elseif ($sale->sale_status == 1) {
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Return';
                                    } elseif ($sale->sale_status === null) {
                                        $statusClass = 'bg-success';
                                        $statusText = 'Sale';
                                    }
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $sale->invoice_no ?: '#'.$sale->id }}</td>
                                    <td>{{ optional($sale->customer_relation)->customer_name ?? 'N/A' }}</td>
                                    <td><small class="text-muted">{{ $sale->reference }}</small></td>
                                    <td><span class="text-truncate d-inline-block" style="max-width:150px" title="{{ $pNames }}">{{ $pNames }}</span></td>
                                    <td class="fw-semibold">{{ $sale->items->sum('quantity') }}</td>
                                    <td class="fw-semibold">{{ number_format($sale->gross_total, 0) }}</td>
                                    <td>{{ number_format($sale->discount_total, 0) }}</td>
                                    <td class="fw-bold">{{ number_format($sale->net_total, 0) }}</td>
                                    <td><small>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') }}</small></td>
                                    <td><span class="status-badge {{ $statusClass }} text-white">{{ $statusText }}</span></td>
                                    <td>
                                        <div class="dropdown dropdown-more">
                                            <button class="action-btn dropdown-toggle" data-bs-toggle="dropdown" style="border:none;background:transparent;padding:2px 8px;font-size:0.9rem">
                                                <i class="las la-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('sales.invoice', $sale->id) }}" target="_blank"><i class="las la-file-invoice text-primary"></i> Invoice</a></li>
                                                <li><a class="dropdown-item" href="{{ route('sales.dc', $sale->id) }}" target="_blank"><i class="las la-truck text-info"></i> DC</a></li>
                                                <li><a class="dropdown-item" href="{{ route('sales.receipt', $sale->id) }}" target="_blank"><i class="las la-receipt text-success"></i> Receipt</a></li>
                                                <li><a class="dropdown-item" href="{{ route('sales.barcodes', $sale->id) }}" target="_blank"><i class="las la-barcode text-warning"></i> Barcodes</a></li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li><a class="dropdown-item" href="{{ route('sales.edit', $sale->id) }}"><i class="las la-pen text-secondary"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="{{ route('sale.return.show', $sale->id) }}"><i class="las la-undo text-danger"></i> Return</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if (method_exists($sales, 'hasPages') && $sales->hasPages())
            <div class="px-3 py-2 d-flex justify-content-end border-top" style="background:#fafbfc">
                {{ $sales->links() }}
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        function resetFilters() {
            $('#min_date, #max_date, #invoice_search, #customer_search').val('');
            $.fn.dataTable.tables({ visible: true, api: true }).search('').draw();
        }

        $(document).ready(function() {
            var table = $('#salesTable').DataTable({
                paging: false,
                ordering: true,
                info: false,
                order: [[8, 'desc']],
                dom: 'rt',
                language: { search: "", searchPlaceholder: "Search..." },
                columnDefs: [{ targets: [10], searchable: false }]
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var min = $('#min_date').val();
                var max = $('#max_date').val();
                var date = data[8];
                if (min && date < min) return false;
                if (max && date > max) return false;
                return true;
            });

            $('#min_date, #max_date').on('change', function() { table.draw(); });
            $('#invoice_search').on('keyup', function() {
                table.column(0).search(this.value).draw();
            });
            $('#customer_search').on('keyup', function() {
                table.column(1).search(this.value).draw();
            });
        });
    </script>
@endsection
