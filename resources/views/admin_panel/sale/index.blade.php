@extends('admin_panel.layout.app')
@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">SALES</h5>
                <div>
                    @can('sales.create')
                        <span class="fw-bold text-dark"><a href="{{ route('sale.add') }}" class="btn btn-primary">Add
                                sale</a></span>
                    @endcan
                    <span class="fw-bold text-dark"><a href="{{ url('bookings') }}" class="btn btn-primary">All
                            Booking</a></span>
                    <span class="fw-bold text-dark ms-1"><a href="{{ route('sale.return.index') }}"
                            class="btn btn-secondary text-white">All Returns</a></span>
                </div>
            </div>

            <div class="card-body">
                <!-- Advanced Filters -->
                <div class="row mb-4 no-print">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">From Date</label>
                        <input type="date" id="min_date" class="form-control shadow-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">To Date</label>
                        <input type="date" id="max_date" class="form-control shadow-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Invoice Search</label>
                        <input type="text" id="invoice_search" class="form-control shadow-sm" placeholder="Invoice # (e.g. INV-001)">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Customer Search</label>
                        <input type="text" id="customer_search" class="form-control shadow-sm" placeholder="Search by name...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-outline-danger w-100 shadow-sm" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="salesTable" class="table table-bordered table-hover table-striped w-100">
                        <thead class="bg-primary text-white">
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
                                    // Product Names
                                    $pNames = 'N/A';
                                    if ($sale->items && $sale->items->count() > 0) {
                                        $pNames = $sale->items
                                            ->map(fn($item) => optional($item->product)->item_name ?? '?')
                                            ->implode(', ');
                                    } elseif ($sale->product) {
                                        $pNames = $sale->product;
                                    }

                                    // Status
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
                                    <td class="fw-bold text-primary">{{ $sale->invoice_no ?: '#'.$sale->id }}</td>
                                    <td>{{ optional($sale->customer_relation)->customer_name ?? 'N/A' }}</td>
                                    <td><small>{{ $sale->reference }}</small></td>
                                    <td title="{{ $pNames }}">
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $pNames }}
                                        </span>
                                    </td>
                                    <td>{{ $sale->total_items > 0 ? $sale->total_items : $sale->qty }}</td>
                                    <td class="text-end fw-bold">{{ number_format($sale->total_bill_amount > 0 ? $sale->total_bill_amount : (float) $sale->per_total, 0) }}</td>
                                    <td class="text-end text-danger">{{ number_format($sale->total_extradiscount, 0) }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($sale->total_net, 0) }}</td>
                                    <td data-sort="{{ $sale->created_at->format('Ymd') }}">
                                        {{ $sale->created_at->format('d-m-Y') }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        @if ($sale->returns && $sale->returns->count() > 0)
                                            <div class="mt-1"><span class="badge bg-warning text-dark border border-dark"><i class="fas fa-undo-alt"></i> Partial</span></div>
                                        @endif
                                    </td>
                                    <td class="no-print">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" data-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0">
                                                @if ($sale->sale_status === 'draft' || $sale->sale_status === 'booked')
                                                    <a class="dropdown-item" href="{{ route('sales.edit', $sale->id) }}"><i class="fas fa-check-circle text-warning me-2"></i> Confirm</a>
                                                    <a class="dropdown-item" href="{{ route('sales.invoice', $sale->id) }}" target="_blank"><i class="fas fa-file-invoice text-info me-2"></i> Invoice</a>
                                                @else
                                                    <a class="dropdown-item" href="{{ route('sales.invoice', $sale->id) }}" target="_blank"><i class="fas fa-file-invoice text-info me-2"></i> Invoice</a>
                                                    <a class="dropdown-item" href="{{ route('sales.invoice', ['id' => $sale->id, 'type' => 'estimate']) }}" target="_blank"><i class="fas fa-file-alt text-secondary me-2"></i> Estimate</a>
                                                    <a class="dropdown-item" href="{{ route('sales.dc', $sale->id) }}" target="_blank"><i class="fas fa-shipping-fast text-warning me-2"></i> Delivery Challan (DC)</a>
                                                    <a class="dropdown-item" href="{{ route('sales.dc_thermal', $sale->id) }}" target="_blank"><i class="fas fa-print text-dark me-2"></i> DC Thermal</a>
                                                    <a class="dropdown-item" href="{{ route('sales.receipt', $sale->id) }}" target="_blank"><i class="fas fa-receipt text-success me-2"></i> Receipt</a>
                                                    <a class="dropdown-item" href="{{ route('sales.barcodes', $sale->id) }}" target="_blank"><i class="fas fa-barcode text-primary me-2"></i> Labels / Barcodes</a>
                                                    <div class="dropdown-divider"></div>
                                                    @if ($sale->sale_status !== 'returned')
                                                        <a class="dropdown-item text-danger" href="{{ route('sale.return.show', $sale->id) }}"><i class="fas fa-undo text-danger me-2"></i> Process Return</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@section('js')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#salesTable').DataTable({
        "order": [[ 0, "desc" ]], // Sort by ID desc
        "pageLength": 25,
        "language": {
            "search": "Global Search:",
            "lengthMenu": "Show _MENU_ entries"
        },
        "dom": '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
        "responsive": true
    });

    // Custom filtering function for date range
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = $('#min_date').val();
            var max = $('#max_date').val();
            var dateStr = data[8]; // Date column index (0-based)
            
            if (!min && !max) return true;
            
            // Convert d-m-Y to Y-m-d for comparison
            var parts = dateStr.split('-');
            var rowDate = parts[2] + '-' + parts[1] + '-' + parts[0];
            
            if (min && rowDate < min) return false;
            if (max && rowDate > max) return false;
            
            return true;
        }
    );

    // Use a more robust way to handle searches
    $(document).on('input', '#invoice_search', function() {
        table.column(0).search(this.value).draw();
    });

    $(document).on('input', '#customer_search', function() {
        table.column(1).search(this.value).draw();
    });

    $(document).on('change', '#min_date, #max_date', function() {
        table.draw();
    });

    // Define resetFilters globally so it's accessible from onclick
    window.resetFilters = function() {
        $('#min_date').val('');
        $('#max_date').val('');
        $('#invoice_search').val('');
        $('#customer_search').val('');
        table.search('').columns().search('').draw();
    };
});
</script>
<style>
    .dataTables_wrapper .dataTables_filter { margin-bottom: 0 !important; }
    .dataTables_wrapper .dataTables_length { margin-bottom: 0 !important; }
    .table thead th { vertical-align: middle; border-bottom: none; }
    .badge { padding: 0.5em 0.8em; }
    .dropdown-item { padding: 0.6rem 1.2rem; transition: background 0.2s; }
    .dropdown-item:hover { background-color: #f8f9fa; }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endsection
