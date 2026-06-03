@extends('admin_panel.layout.app')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

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
        #productTable {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 0.78rem;
            border: 1px solid var(--border-color);
        }
        #productTable thead th {
            padding: 8px 10px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-muted);
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            background: #f8fafc;
            white-space: nowrap;
        }
        #productTable thead th:last-child { border-right: none; }
        #productTable tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            vertical-align: middle;
        }
        #productTable tbody td:last-child { border-right: none; }
        #productTable tbody tr:last-child td { border-bottom: none; }
        #productTable tbody tr:hover { background: #f1f5f9; }
        #productTable tbody tr:nth-child(even) { background: #fafbfc; }
        #productTable tbody tr:nth-child(even):hover { background: #f1f5f9; }
        .prod-img { width: 32px; height: 32px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); }
        .barcode-mini { display: inline-block; line-height: 0; }
        .barcode-mini svg { width: 60px; height: 16px; }
        .stock-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            background: #f1f5f9;
            color: var(--text-main);
        }
        .price-cell { font-weight: 600; white-space: nowrap; }
        .price-cell small { font-weight: 400; color: var(--text-muted); font-size: 0.65rem; }
        .status-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
        }
        .status-dot.active { background: #22c55e; }
        .status-dot.inactive { background: #ef4444; }
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
        .dropdown-more .dropdown-item.text-danger:hover { background: #fef2f2; }
        #search_all {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 7px 14px 7px 36px;
            font-size: 0.82rem;
            width: 260px;
        }
        #search_all:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
    </style>

    <div class="page-container">
        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="font-size:1.1rem">Products</h4>
                <small class="text-muted">Manage all products</small>
            </div>
            <div class="d-flex gap-2">
                @if (auth()->user()->can('products.create') || auth()->user()->email === 'admin@admin.com')
                    <a href="create_prodcut" class="action-btn primary"><i class="fas fa-plus"></i> Add Product</a>
                @endif
            </div>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="d-flex align-items-center gap-3">
                    <input type="text" id="search_all" placeholder="Search products...">
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if (auth()->user()->can('discount.products.create') || auth()->user()->email === 'admin@admin.com')
                        <button id="createDiscountBtn" class="action-btn"><i class="fas fa-tag"></i> Create Discount</button>
                    @endif
                </div>
            </div>
            <div class="table-card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3 py-2" style="font-size:0.82rem">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                <table id="productTable" class="table">
                    <thead>
                        <tr>
                            <th style="width:32px"><input type="checkbox" id="selectAll" style="cursor:pointer"></th>
                            <th style="width:32px">#</th>
                            <th>Code</th>
                            <th style="width:40px">Img</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Barcode</th>
                            <th>Stock</th>
                            <th>Trade Price</th>
                            <th>Retail Price</th>
                            <th>Brand</th>
                            <th style="width:50px">Status</th>
                            <th style="width:80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $product)
                            @php
                                $stockPieces = (float) ($product->warehouse_stocks_sum_total_pieces ?? 0);
                                $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                                if (($product->size_mode === 'by_cartons' || $product->size_mode === 'by_size') && $ppb > 1) {
                                    $boxes = floor($stockPieces / $ppb);
                                    $loose = $stockPieces % $ppb;
                                    $stockDisplay = $loose > 0 ? "{$boxes}.{$loose}" : "{$boxes}";
                                    $stockUnit = $loose > 0 ? 'Box.L' : 'Box';
                                } else {
                                    $stockDisplay = $stockPieces;
                                    $stockUnit = 'Pcs';
                                }
                                $tradePrice = 0;
                                $retailPrice = 0;
                                if ($product->size_mode === 'by_size') {
                                    $m2PerPiece = ($product->height * $product->width) / 10000;
                                    $tradePrice = $m2PerPiece * (float)$product->purchase_price_per_m2;
                                    $retailPrice = $m2PerPiece * (float)$product->price_per_m2;
                                } else {
                                    $tradePrice = (float)$product->purchase_price_per_piece;
                                    $retailPrice = (float)$product->sale_price_per_piece ?: (float)$product->sale_price_per_box;
                                }
                                $rowClass = $product->is_active ? '' : 'opacity-50';
                            @endphp
                            <tr id="product-row-{{ $product->id }}" class="{{ $rowClass }}">
                                <td><input type="checkbox" class="selectProduct" value="{{ $product->id }}" style="cursor:pointer"></td>
                                <td class="text-muted">{{ $key + 1 }}</td>
                                <td class="fw-semibold">{{ $product->item_code }}</td>
                                <td>
                                    @if ($product->image)
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" class="prod-img">
                                    @else
                                        <span class="text-muted" style="font-size:0.65rem">—</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $product->item_name }}</td>
                                <td>
                                    <span>{{ $product->category_relation->name ?? '-' }}</span>
                                    @if($product->sub_category_relation)
                                        <span class="text-muted">/ {{ $product->sub_category_relation->name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->item_code)
                                        <div class="barcode-mini">{!! DNS1D::getBarcodeHTML($product->item_code, 'C128', 0.8, 16) !!}</div>
                                        <div style="font-size:0.6rem;color:var(--text-muted);letter-spacing:0.5px">{{ $product->barcode_path ?: $product->item_code }}</div>
                                    @else
                                        <span class="text-muted" style="font-size:0.65rem">—</span>
                                    @endif
                                </td>
                                <td><span class="stock-badge">{{ $stockDisplay }} <small>{{ $stockUnit }}</small></span></td>
                                <td class="price-cell">Rs. {{ number_format($tradePrice, 2) }} <small>/pc</small></td>
                                <td class="price-cell">Rs. {{ number_format($retailPrice, 2) }} <small>/pc</small></td>
                                <td>{{ $product->brand->name ?? '-' }}</td>
                                <td>
                                    <span class="status-dot {{ $product->is_active ? 'active' : 'inactive' }}" id="status-dot-{{ $product->id }}" title="{{ $product->is_active ? 'Active' : 'Inactive' }}"></span>
                                </td>
                                <td>
                                    <div class="dropdown dropdown-more">
                                        <button class="action-btn dropdown-toggle" data-toggle="dropdown" style="border:none;background:transparent;padding:2px 8px;font-size:0.9rem">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a class="dropdown-item viewProductBtn" href="#" data-id="{{ $product->id }}"><i class="fas fa-eye text-info"></i> View</a></li>
                                            @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                                                <li><a class="dropdown-item" href="{{ route('products.edit', $product->id) }}"><i class="fas fa-edit text-primary"></i> Edit</a></li>
                                            @endif
                                            <li><a class="dropdown-item" href="{{ route('generate-barcode-image', $product->id) }}" target="_blank"><i class="fas fa-barcode text-success"></i> Barcode</a></li>
                                            <li><a class="dropdown-item viewBreakdownBtn" href="#" data-id="{{ $product->id }}" data-name="{{ $product->item_name }}"><i class="fas fa-map-marker-alt text-warning"></i> Location</a></li>
                                            @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger toggle-active-btn" href="#" 
                                                       data-id="{{ $product->id }}"
                                                       data-active="{{ $product->is_active ? '1' : '0' }}"
                                                       data-name="{{ $product->item_name }}">
                                                        <i class="fas fa-{{ $product->is_active ? 'ban' : 'check-circle' }}"></i>
                                                        {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (method_exists($products, 'hasPages') && $products->hasPages())
            <div class="px-3 py-2 d-flex justify-content-end border-top" style="background:#fafbfc">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Modals --}}
    <div class="modal fade" id="productViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark" id="view_item_name">Product Name</h5>
                        <p class="text-muted small mb-0"><i class="fas fa-barcode"></i> <span id="view_item_code">CODE</span></p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body bg-light p-3">
                    <div id="modalLoadingSpinner" class="text-center py-5 d-none"><div class="spinner-border text-primary" role="status"></div></div>
                    <div class="row" id="modalContentRow">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="card h-100 border-0 shadow-sm rounded">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-primary font-weight-bold small mb-3 border-bottom pb-2">1. Information</h6>
                                    <div class="text-center mb-3">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto" style="width:100px;height:100px;overflow:hidden;border:1px solid #eee;">
                                            <img id="view_image_preview" src="" class="img-fluid d-none">
                                            <div id="view_image_placeholder" class="text-center"><i class="fas fa-image text-muted" style="font-size:2rem;"></i><small class="d-block text-muted" style="font-size:10px;">No Image</small></div>
                                        </div>
                                    </div>
                                    <div class="text-center mb-3">
                                        <div id="view_barcode_container" class="p-2 bg-white border rounded d-inline-block"></div>
                                        <div class="small fw-bold text-dark mt-1" id="view_barcode_text"></div>
                                    </div>
                                    <div class="mb-2"><small class="text-muted d-block">Category</small><span class="font-weight-bold text-dark" id="view_cat_sub">-</span></div>
                                    <div class="mb-2"><small class="text-muted d-block">Brand / Model</small><span class="font-weight-bold text-dark" id="view_brand_model">-</span></div>
                                    <div class="mb-2"><small class="text-muted d-block">Colors</small><span class="text-dark" id="view_color" style="font-size:0.9rem;">-</span></div>
                                    <div class="mb-0 border-top pt-2 mt-2"><small class="text-muted d-block">Created On</small><span class="text-dark small" id="view_created_at">-</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="card h-100 border-0 shadow-sm rounded">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                        <h6 class="text-uppercase text-info font-weight-bold small mb-0">2. Measurement</h6>
                                        <span class="badge badge-secondary" id="view_size_mode_badge">Mode</span>
                                    </div>
                                    <div id="sec_by_size" class="d-none">
                                        <div class="row no-gutters mb-2">
                                            <div class="col-6 pr-1"><small class="text-muted d-block">Dim (HxW)</small><span class="font-weight-bold text-dark" id="view_dimensions">-</span></div>
                                            <div class="col-6 pl-1"><small class="text-muted d-block">m²/Pc</small><span class="font-weight-bold text-dark" id="view_m2_piece">-</span></div>
                                        </div>
                                        <div class="bg-light p-2 rounded mb-2 border">
                                            <div class="d-flex justify-content-between"><small class="text-muted">Box Qty</small><strong class="text-dark" id="view_boxes_qty_size">-</strong></div>
                                            <div class="d-flex justify-content-between"><small class="text-muted">Pcs/Box</small><strong class="text-dark" id="view_pcs_box_size">-</strong></div>
                                        </div>
                                        <div class="text-center mt-2"><small class="text-muted d-block text-uppercase">Total Area (m²)</small><div class="h5 font-weight-bold text-info" id="view_total_m2">-</div></div>
                                    </div>
                                    <div id="sec_packing" class="d-none">
                                        <div class="row text-center mb-2 mx-0">
                                            <div class="col-4 px-1"><div class="bg-light p-1 rounded border"><small class="d-block" style="font-size:0.6rem;">PCS/BOX</small><strong class="text-dark" id="view_pcs_box">-</strong></div></div>
                                            <div class="col-4 px-1"><div class="bg-light p-1 rounded border"><small class="d-block" style="font-size:0.6rem;">BOXES</small><strong class="text-primary" id="view_boxes_qty">-</strong></div></div>
                                            <div class="col-4 px-1"><div class="bg-light p-1 rounded border"><small class="d-block" style="font-size:0.6rem;">LOOSE</small><strong class="text-warning" id="view_loose_pcs">-</strong></div></div>
                                        </div>
                                    </div>
                                    <div id="sec_by_piece" class="d-none text-center mb-3"><div class="alert alert-light border"><i class="fas fa-layer-group text-primary" style="font-size:1.5rem;"></i><br><span class="text-muted small">Unit Tracking Only</span></div></div>
                                    <div class="mt-auto pt-3 border-top"><div class="d-flex justify-content-between align-items-center"><small class="text-muted font-weight-bold">TOTAL PCS</small><span class="h4 mb-0 font-weight-bold text-success" id="view_total_stock_qty">0</span></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded">
                                <div class="card-body p-3">
                                    <h6 class="text-uppercase text-success font-weight-bold small mb-3 border-bottom pb-2">3. Financials</h6>
                                    <div class="mb-3"><div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-bold" id="lbl_price_unit">Sale Price</small><span class="font-weight-bold text-dark" id="view_price_unit">-</span></div><div class="progress" style="height:4px;"><div class="progress-bar bg-success" style="width:100%"></div></div></div>
                                    <div class="mb-3"><div class="d-flex justify-content-between mb-1"><small class="text-muted font-weight-bold" id="lbl_purch_unit">Purch Price</small><span class="text-secondary" id="view_purch_unit">-</span></div><div class="progress" style="height:4px;"><div class="progress-bar bg-secondary" style="width:60%"></div></div></div>
                                    <div class="alert alert-success p-2 mb-0 mt-4 text-center" style="background:#d1e7dd;border-color:#badbcc;"><small class="d-block text-success font-weight-bold text-uppercase" style="font-size:0.7rem;">Est. Sale Value</small><div class="font-weight-bold text-dark h4 mb-0" id="view_sale_total">-</div></div>
                                    <div class="text-center mt-2"><small class="text-muted">Total Purch: <span id="view_purch_total" class="text-danger">-</span></small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 py-2 bg-white rounded-bottom">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
                <div class="modal-header bg-info text-white" style="border-radius:12px 12px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-map-marker-alt me-2"></i> Stock Breakdown</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom"><h6 id="breakdown_product_name" class="mb-0 fw-bold text-dark">Product Name</h6></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light"><tr><th class="ps-3">Warehouse</th><th>Location</th><th class="text-center">Boxes</th><th class="text-end pe-3">Pieces</th></tr></thead>
                            <tbody id="breakdown_body"></tbody>
                        </table>
                    </div>
                    <div id="breakdown_loading" class="text-center py-4 d-none"><div class="spinner-border text-info" role="status"></div></div>
                    <div id="breakdown_empty" class="text-center py-4 d-none text-muted">No stock found.</div>
                </div>
                <div class="modal-footer bg-light border-0 py-2" style="border-radius:0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).on('click', '.toggle-active-btn', function (e) {
        e.preventDefault();
        const btn = $(this);
        const productId = btn.data('id');
        const isActive = btn.data('active') == '1';
        const productName = btn.data('name');
        const actionText = isActive ? 'Deactivate' : 'Activate';
        Swal.fire({
            title: actionText + ' Product?',
            html: `<b>${productName}</b><br><small class="text-muted">${isActive ? 'Product will be hidden from forms.' : 'Product will be visible in forms.'}</small>`,
            icon: isActive ? 'warning' : 'success',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + actionText,
            confirmButtonColor: isActive ? '#dc3545' : '#28a745',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/product/${productId}/toggle-active`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            const row = $(`#product-row-${productId}`);
                            const dot = $(`#status-dot-${productId}`);
                            if (res.is_active) {
                                row.removeClass('opacity-50');
                                dot.removeClass('inactive').addClass('active').attr('title', 'Active');
                            } else {
                                row.addClass('opacity-50');
                                dot.removeClass('active').addClass('inactive').attr('title', 'Inactive');
                            }
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, showConfirmButton: false, timer: 2000 });
                        }
                    },
                    error: () => Swal.fire('Error', 'Could not update status.', 'error')
                });
            }
        });
    });

    $(document).on('click', '.viewProductBtn', function() {
        let productId = $(this).data('id');
        $('#modalContentRow').addClass('d-none');
        $('#modalLoadingSpinner').removeClass('d-none');
        $('#productViewModal').modal('show');
        $.ajax({
            url: "/productview/" + productId, type: "GET",
            success: function(product) {
                $('#modalLoadingSpinner').addClass('d-none');
                $('#modalContentRow').removeClass('d-none');
                $('#view_item_name').text(product.item_name ?? 'Unknown');
                $('#view_item_code').text(product.item_code ?? 'N/A');
                $('#view_cat_sub').text((product.category_relation?.name ?? '') + (product.sub_category_relation ? ' / ' + product.sub_category_relation.name : ''));
                $('#view_brand_model').text((product.brand?.name ?? '-') + (product.model ? ' / ' + product.model : ''));
                $('#view_created_at').text(product.created_at ? new Date(product.created_at).toLocaleDateString() : '-');
                const bcode = product.barcode_path || product.item_code;
                if (bcode) {
                    $('#view_barcode_container').html(`<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=${bcode}&scale=2&rotate=N&includetext" style="max-width:100%;height:auto;">`);
                    $('#view_barcode_text').text(bcode);
                } else {
                    $('#view_barcode_container').html('<span class="text-muted">No Barcode</span>');
                    $('#view_barcode_text').text('');
                }
                if (product.image) {
                    $('#view_image_preview').attr('src', '/uploads/products/' + product.image).removeClass('d-none');
                    $('#view_image_placeholder').addClass('d-none');
                } else {
                    $('#view_image_preview').addClass('d-none');
                    $('#view_image_placeholder').removeClass('d-none');
                }
                if (product.color) {
                    try { let colors = JSON.parse(product.color); $('#view_color').text(Array.isArray(colors) ? colors.join(', ') : colors); }
                    catch(e) { $('#view_color').text(product.color); }
                } else { $('#view_color').text('-'); }
                let mode = product.size_mode ?? 'by_size';
                $('#sec_by_size, #sec_packing, #sec_by_piece').addClass('d-none');
                let calcBoxes = product.calculated_boxes_quantity ?? 0;
                let calcLoose = product.calculated_loose_pieces ?? 0;
                let calcTotal = product.calculated_total_stock_qty ?? 0;
                let salePrice = 0, purchPrice = 0, estSaleVal = 0, estPurchVal = 0;
                if (mode === 'by_size') {
                    $('#view_size_mode_badge').text('By Size').removeClass('bg-info bg-warning').addClass('bg-light text-primary border-primary');
                    $('#sec_by_size').removeClass('d-none');
                    $('#view_dimensions').text((product.height ?? 0) + ' x ' + (product.width ?? 0));
                    let m2Piece = ((product.height * product.width) / 10000).toFixed(4);
                    $('#view_m2_piece').text(m2Piece);
                    $('#view_boxes_qty_size').text(calcBoxes);
                    $('#view_pcs_box_size').text(product.pieces_per_box ?? 0);
                    $('#view_total_m2').text(parseFloat(product.total_m2 ?? 0).toFixed(2));
                    $('#view_total_stock_qty').text(calcTotal);
                    $('#lbl_price_unit').text('Price per m²'); $('#lbl_purch_unit').text('Cost per m²');
                    salePrice = product.price_per_m2; purchPrice = product.purchase_price_per_m2;
                    estSaleVal = (product.total_m2 ?? 0) * calcBoxes * salePrice;
                    estPurchVal = (product.total_m2 ?? 0) * calcBoxes * purchPrice;
                } else if (mode === 'by_cartons') {
                    $('#view_size_mode_badge').text('By Box').removeClass('bg-light text-primary border-primary bg-warning').addClass('bg-info text-white border-0');
                    $('#sec_packing').removeClass('d-none');
                    $('#view_boxes_qty').text(calcBoxes); $('#view_loose_pcs').text(calcLoose);
                    $('#view_pcs_box').text(product.pieces_per_box ?? '-');
                    $('#view_total_stock_qty').text(calcTotal);
                    $('#lbl_price_unit').text('Price per Box'); $('#lbl_purch_unit').text('Cost per Piece');
                    salePrice = product.sale_price_per_box; purchPrice = product.purchase_price_per_piece;
                    let ppb = product.pieces_per_box > 0 ? product.pieces_per_box : 1;
                    estSaleVal = calcTotal * (salePrice / ppb);
                    estPurchVal = calcTotal * purchPrice;
                } else {
                    $('#view_size_mode_badge').text('By Piece').removeClass('bg-light text-primary border-primary bg-info text-white').addClass('bg-warning text-dark border-0');
                    $('#sec_by_piece').removeClass('d-none');
                    $('#view_total_stock_qty').text(calcTotal);
                    $('#lbl_price_unit').text('Price per Piece'); $('#lbl_purch_unit').text('Cost per Piece');
                    salePrice = product.sale_price_per_box; purchPrice = product.purchase_price_per_piece;
                    estSaleVal = calcTotal * salePrice; estPurchVal = calcTotal * purchPrice;
                }
                $('#view_price_unit').text('Rs. ' + parseFloat(salePrice || 0).toFixed(2));
                $('#view_purch_unit').text('Rs. ' + parseFloat(purchPrice || 0).toFixed(2));
                $('#view_sale_total').text('Rs. ' + parseFloat(estSaleVal || 0).toLocaleString('en-US', {minimumFractionDigits:2}));
                $('#view_purch_total').text('Rs. ' + parseFloat(estPurchVal || 0).toLocaleString('en-US', {minimumFractionDigits:2}));
            },
            error: function() { $('#modalLoadingSpinner').addClass('d-none'); Swal.fire('Error', 'Could not fetch details', 'error'); }
        });
    });

    $(document).on('click', '.viewBreakdownBtn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#breakdown_product_name').text(name);
        $('#breakdown_body').empty();
        $('#breakdown_loading').removeClass('d-none');
        $('#breakdown_empty').addClass('d-none');
        $('#breakdownModal').modal('show');
        $.get(`/warehouse-stock/breakdown/${id}`, function(data) {
            $('#breakdown_loading').addClass('d-none');
            if (data.length === 0) { $('#breakdown_empty').removeClass('d-none'); }
            else {
                let html = '';
                data.forEach(s => { html += `<tr><td class="ps-3 fw-semibold">${s.warehouse}</td><td><span class="badge bg-light text-dark border">${s.location || '--'}</span></td><td class="text-center text-primary fw-semibold">${s.boxes}</td><td class="text-end pe-3 text-success fw-semibold">${s.total_pieces.toLocaleString()}</td></tr>`; });
                $('#breakdown_body').html(html);
            }
        });
    });

    $(document).ready(function() {
        $('#selectAll').click(function() { $('.selectProduct').prop('checked', this.checked); });
        $('#createDiscountBtn').click(function() {
            var selected = [];
            $('.selectProduct:checked').each(function() { selected.push($(this).val()); });
            if (selected.length === 0) { Swal.fire({ icon: "error", title: "Oops...", text: "Please select at least one product!" }); return; }
            window.location.href = "{{ route('discount.create') }}" + "?products=" + selected.join(',');
        });
        var table = $('#productTable').DataTable({
            paging: false,
            ordering: true,
            info: false,
            order: [[1, 'asc']],
            dom: 'rt',
            language: { search: "", searchPlaceholder: "Search..." },
            columnDefs: [{ targets: [0, 11], searchable: false }]
        });
        $('#search_all').on('keyup', function() { table.search(this.value).draw(); });
    });
    </script>
@endsection
