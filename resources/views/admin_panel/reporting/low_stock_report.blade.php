@extends('admin_panel.layout.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root { --primary: #4f46e5; --border-color: #e2e8f0; --radius-md: 10px; }
    body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
    .report-card { background: #fff; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .report-header { padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .report-body { padding: 20px; }
    .stat-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    .badge-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .table-pro { font-size: 0.85rem; }
    .table-pro thead th { background: #f8fafc; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.03em; color: #64748b; font-weight: 700; border-bottom: 2px solid var(--border-color); padding: 10px 12px; }
    .table-pro tbody td { padding: 10px 12px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-pro tbody tr:hover { background: #f8fafc; }
    .shortage-badge { background: #dc2626; color: #fff; padding: 3px 10px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; }
    .stock-ok { color: #16a34a; font-weight: 700; }
    .stock-low { color: #dc2626; font-weight: 700; }
    .btn-print { background: #0f172a; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; cursor: pointer; }
    .btn-print:hover { background: #1e293b; }
    @media print {
        @page { margin: 0.5cm !important; }
        .no-print, .rt_nav_header, .footer-area, .nav-bottom { display: none !important; }
        .report-card { border: none; box-shadow: none; }
        body { background: #fff; margin: 0; padding: 0; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .main-panel { padding: 0 !important; margin: 0 !important; }
        .content-wrapper { padding: 0 !important; margin: 0 !important; }
    }
</style>

<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-exclamation-triangle text-danger"></i> Low Stock Alert</h4>
            <small class="text-muted">Products below minimum stock level</small>
        </div>
        <div class="d-flex gap-2 no-print">
            <a href="{{ route('report.profit_loss') }}" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fas fa-chart-line"></i> P&L</a>
            <button onclick="window.print()" class="btn-print"><i class="fas fa-file-pdf"></i> Print / PDF</button>
        </div>
    </div>

    <div class="report-card mb-3">
        <div class="report-header no-print">
            <div class="d-flex align-items-center gap-2">
                <label class="fw-bold small text-muted">Category</label>
                <select id="filterCategory" class="form-select form-select-sm" style="width:200px; border-radius:8px;">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button onclick="fetchReport()" class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-sync-alt"></i> Refresh</button>
        </div>
        <div class="report-body">
            <div class="d-flex gap-3 mb-3" id="statCards">
                <span class="stat-badge badge-danger"><i class="fas fa-exclamation-circle"></i> Low Items: <span id="totalItems">0</span></span>
                <span class="stat-badge badge-warning"><i class="fas fa-box-open"></i> Total Shortage: <span id="totalShortage">0</span> pcs</span>
            </div>

            <div class="table-responsive">
                <table class="table table-pro table-hover align-middle mb-0" id="lowStockTable">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Code</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-center">Current Stock</th>
                            <th class="text-center">Alert Qty</th>
                            <th class="text-center">Shortage</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="7" class="text-center text-muted py-5">Click Refresh to load data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    fetchReport();
});

function fetchReport() {
    $.ajax({
        url: "{{ route('report.low_stock.fetch') }}",
        type: "GET",
        data: { category_id: $('#filterCategory').val() },
        success: function(data) {
            var tbody = $('#tableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-check-circle text-success fs-1 d-block mb-2"></i>All products are above alert level</td></tr>');
                $('#totalItems').text(0);
                $('#totalShortage').text(0);
                return;
            }

            var totalShortage = 0;
            $.each(data, function(i, r) {
                totalShortage += r.shortage;
                tbody.append(
                    '<tr>' +
                    '<td class="text-muted">' + (i + 1) + '</td>' +
                    '<td><span class="fw-semibold text-muted small">' + r.item_code + '</span></td>' +
                    '<td class="fw-bold">' + r.item_name + '</td>' +
                    '<td><span class="badge bg-light text-dark border">' + r.category + '</span></td>' +
                    '<td class="text-center"><span class="' + (r.current_stock <= r.alert_qty ? 'stock-low' : 'stock-ok') + '">' + r.current_stock + '</span></td>' +
                    '<td class="text-center fw-bold">' + r.alert_qty + '</td>' +
                    '<td class="text-center"><span class="shortage-badge">' + r.shortage + '</span></td>' +
                    '</tr>'
                );
            });

            $('#totalItems').text(data.length);
            $('#totalShortage').text(totalShortage);
        }
    });
}
</script>
@endsection
