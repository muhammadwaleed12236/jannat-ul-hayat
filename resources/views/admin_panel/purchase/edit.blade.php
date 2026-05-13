@extends('admin_panel.layout.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ================= RESPONSIVE PURCHASE UI (Modernized) ================= */
        body {
            background-color: #f4f6f9;
            /* Light gray background for contrast */
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .sales-table {
            min-width: 1000px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sales-table thead th {
            background-color: #f1f3f5;
            color: #495057;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .sales-table tbody td {
            vertical-align: middle;
            padding: 4px 6px;
            border-color: #f1f3f5;
        }

        .sales-table tfoot td {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 8px;
        }

        /* Premium Table Look */
        .table-bordered>:not(caption)>*>* {
            border-width: 1px;
            border-color: #eef0f2;
        }

        /* Column widths */
        .col-product {
            width: 250px;
            min-width: 200px;
        }

        .col-warehouse {
            width: 120px;
        }

        .col-location {
            width: 120px;
        }

        .col-qty {
            width: 80px;
        }

        .col-stock {
            width: 80px;
        }

        .col-pieces {
            width: 80px;
        }

        .col-price {
            width: 110px;
        }

        .col-disc {
            width: 70px;
        }

        .col-disc-amt {
            width: 85px;
        }

        .col-amount {
            width: 110px;
            text-align: right;
        }

        .col-action {
            width: 40px;
            text-align: center;
        }

        .input-readonly {
            background: #f8f9fa;
            color: #495057;
            font-weight: 500;
            border: 1px solid #dee2e6;
        }

        .form-control,
        .form-select {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 0.3rem 0.5rem;
            font-size: 0.8rem;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.1);
        }

        .main-container {
            font-size: .8rem;
            max-width: 99.5%;
            border-radius: 10px !important;
            border: none !important;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05) !important;
        }

        .card {
            border-radius: 8px;
            border: 1px solid #eef0f2;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .btn {
            font-size: .78rem;
            padding: .3rem .7rem;
            border-radius: 4px;
            font-weight: 600;
        }

        .section-title {
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-left: 3px solid #0d6efd;
            padding-left: 6px;
        }

        /* Product Search Dropdown */
        .search-results {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            z-index: 1000;
            max-height: 250px;
            overflow-y: auto;
            width: 100%;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }

        .search-result-item {
            padding: 8px 10px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            font-size: 0.8rem;
        }

        .search-result-item:hover,
        .search-result-item.active {
            background-color: #e7f1ff;
            color: #0b5ed7;
        }

        .summary-row {
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-row:last-child { border-bottom: none; }

        .select2-container .select2-selection--single {
            height: 36px !important;
            padding: 3px 12px;
            border-color: #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 5px !important;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="main-container bg-white border shadow-sm mx-auto p-2 rounded-3">

            <form id="purchaseForm" action="{{ route('purchase.update', $purchase->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <a href="{{ route('Purchase.home') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <h2 class="header-text text-secondary fw-bold mb-0">Edit Purchase #{{ $purchase->invoice_no }}</h2>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-secondary" id="entryDate">Date: {{ date('d-M-Y') }}</small>
                    </div>
                </div>

                {{-- TOP HEADER: Invoice & Vendor --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small">Invoice No</label>
                                <input type="text" class="form-control input-readonly" name="invoice_no"
                                    value="{{ $purchase->invoice_no }}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small">Vendor Inv#</label>
                                <input type="text" class="form-control" name="purchase_order_no"
                                    value="{{ $purchase->purchase_order_no }}" placeholder="Manual Ref">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Select Vendor</label>
                                <select class="form-select select2" id="vendorSelect" name="vendor_id">
                                    <option value="" disabled>Select Vendor</option>
                                    @foreach ($Vendor as $v)
                                        <option value="{{ $v->id }}"
                                            {{ $v->id == $purchase->vendor_id ? 'selected' : '' }}>
                                            {{ $v->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small">Date</label>
                                <input type="date" name="purchase_date" class="form-control"
                                    value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d') }}">
                            </div>
                            @if(!$showItemWarehouse)
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small">Warehouse</label>
                                <select name="warehouse_id" class="form-select select2">
                                    @foreach ($Warehouse as $w)
                                        <option value="{{ $w->id }}" {{ $w->id == $purchase->warehouse_id ? 'selected' : '' }}>
                                            {{ $w->warehouse_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-{{ $showItemWarehouse ? '3' : '1' }}">
                                <label class="form-label fw-bold text-muted small">Remarks</label>
                                <textarea class="form-control" name="note" id="remarks" rows="1" placeholder="Notes...">{{ $purchase->note }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FULL WIDTH: Items --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">Purchase Items</div>
                            <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm" onclick="addBlankRow()">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                        </div>

                        <div class="table-responsive border rounded-3 bg-white">
                            <table class="table table-bordered sales-table mb-0" id="purchaseTable">
                                <thead>
                                    <tr>
                                        <th class="col-product">Product</th>
                                        @if($showItemWarehouse) <th class="col-warehouse">Warehouse</th> @endif
                                        @if($showItemLocation) <th class="col-location">Location</th> @endif
                                        <th class="col-qty">Boxes</th>
                                        <th class="col-stock">Pack</th>
                                        <th class="col-pieces">Pcs</th>
                                        <th class="col-price">Price</th>
                                        <th class="col-disc">Disc %</th>
                                        <th class="col-disc-amt">D.Amt</th>
                                        <th class="col-amount">Amount</th>
                                        <th class="col-action">X</th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseTableBody">
                                    @foreach ($purchase->items as $item)
                                        @php
                                            $sizeMode = $item->size_mode ?? 'by_pieces';
                                            $ppb = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : 1);
                                            $boxes = (float) ($item->boxes_qty ?? 0);
                                            $loose = (float) ($item->loose_qty ?? 0);

                                            $displayBoxes = $boxes > 0 ? $boxes : '0';
                                            if ($loose > 0) {
                                                $displayBoxes .= '.' . $loose;
                                            } elseif ($boxes == 0) {
                                                $displayBoxes = '';
                                            }

                                            $unitLabel = '';
                                            if ($sizeMode == 'by_size') {
                                                $unitLabel = '(m²)';
                                            } elseif ($sizeMode == 'by_cartons') {
                                                $unitLabel = '(carton)';
                                            } else {
                                                $unitLabel = '(piece)';
                                            }
                                        @endphp
                                        <tr data-sizemode="{{ $sizeMode }}" data-pieces_per_m2="{{ $item->pieces_per_m2 }}">
                                            <td>
                                                <select class="form-select product-select2" name="product_id[]">
                                                    <option value="{{ $item->product_id }}" selected>
                                                        {{ $item->product->item_name }} ({{ $item->product->item_code }})
                                                    </option>
                                                </select>
                                                <input type="hidden" name="size_mode[]" class="hidden-size-mode" value="{{ $sizeMode }}">
                                                <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="{{ $ppb }}">
                                                <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="{{ $item->pieces_per_m2 }}">
                                                <input type="hidden" name="length[]" class="hidden-length" value="{{ $item->length }}">
                                                <input type="hidden" name="width[]" class="hidden-width" value="{{ $item->width }}">
                                                <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="{{ $boxes }}">
                                                <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="{{ $loose }}">
                                            </td>
                                            @if($showItemWarehouse)
                                            <td>
                                                <select class="form-select" name="item_warehouse_id[]">
                                                    @foreach ($Warehouse as $w)
                                                        <option value="{{ $w->id }}" {{ $w->id == $item->warehouse_id ? 'selected' : '' }}>
                                                            {{ $w->warehouse_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @endif
                                            @if($showItemLocation)
                                            <td><input type="text" class="form-control" name="item_location[]" value="{{ $item->location }}" placeholder="Loc"></td>
                                            @endif
                                            <td><input type="text" class="form-control box-qty" value="{{ $displayBoxes }}" placeholder="Boxes"></td>
                                            <td><input type="number" class="form-control input-readonly pack-size" value="{{ $ppb }}" readonly></td>
                                            <td><input type="number" name="qty[]" class="form-control input-readonly qty-pcs" value="{{ (float) $item->qty }}" readonly></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="price[]" class="form-control price" step="0.01" value="{{ (float) $item->price }}">
                                                </div>
                                                <small class="text-muted price-unit-label" style="font-size:0.7rem;">{{ $unitLabel }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $gross = $item->line_total + $item->item_discount;
                                                    $dPct = $gross > 0 ? ($item->item_discount / $gross) * 100 : 0;
                                                @endphp
                                                <input type="number" class="form-control item-disc-percent" value="{{ round($dPct, 2) }}">
                                            </td>
                                            <td><input type="number" name="item_discount[]" class="form-control item-disc-amt" value="{{ (float) $item->item_discount }}"></td>
                                            <td><input type="number" class="form-control input-readonly row-total" value="{{ (float) $item->line_total }}" readonly></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="bi bi-x-lg"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        @php $colspan = 7 + ($showItemWarehouse ? 1 : 0) + ($showItemLocation ? 1 : 0); @endphp
                                        <td colspan="{{ $colspan }}" class="text-end fw-bold text-muted">Total:</td>
                                        <td class="text-end fw-bold fs-6 text-dark"><span id="totalAmount">0.00</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SUMMARY --}}
                <div class="row g-3 mt-1">
                    {{-- LEFT: Payment / Receipt Voucher --}}
                    <div class="col-lg-7">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Payment / Receipt Voucher</div>
                            <div id="paymentWrapper" class="border rounded p-3 bg-light mb-3">
                                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                    <select class="form-select rv-account" name="payment_account_id[]"
                                        style="max-width: 300px; flex-grow: 1;">
                                        <option value="" selected disabled>Select Account</option>
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" class="form-control text-end payment-amount"
                                        name="payment_amount[]" placeholder="Amount" style="width:140px">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="me-2 fw-bold text-muted">Total Paid:</span>
                                <span class="fw-bold fs-6 text-success" id="totalPaid">0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Summary --}}
                    <div class="col-lg-5">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Summary</div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Total Qty (Pieces)</div>
                                <div class="col-5 text-end"><span id="tQty" class="fw-bold">0</span></div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Sub-Total</div>
                                <div class="col-5 text-end fw-bold"><span id="tSub">0.00</span></div>
                                <input type="hidden" name="subtotal" id="subtotalInput">
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Bill Discount</div>
                                <div class="col-5 text-end d-flex gap-1">
                                    @php
                                        $bSub = (float) $purchase->subtotal;
                                        $bDisc = (float) $purchase->discount;
                                        $bPct = $bSub > 0 ? ($bDisc / $bSub) * 100 : 0;
                                    @endphp
                                    <input type="number" class="form-control text-end form-control-sm"
                                        id="billDiscountPct" value="{{ round($bPct, 2) }}" placeholder="%" style="width: 70px;" step="0.01">
                                    <input type="number" class="form-control text-end form-control-sm" name="discount"
                                        id="billDiscount" value="{{ (float) $purchase->discount }}" step="0.01">
                                </div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Extra Cost</div>
                                <div class="col-5 text-end">
                                    <input type="number" class="form-control text-end form-control-sm" name="extra_cost"
                                        id="extraCost" value="{{ (float) $purchase->extra_cost }}">
                                </div>
                            </div>
                            <hr class="my-2 border-secondary">
                            <div class="row py-2">
                                <div class="col-6 fw-bold fs-5 text-primary">Net Payable</div>
                                <div class="col-6 text-end fw-bold fs-5 text-primary"><span id="tPayable">0.00</span>
                                </div>
                                <input type="hidden" name="net_amount" id="netAmountInput">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i> Update Purchase
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const showWarehouseCol = {{ $showItemWarehouse ? 'true' : 'false' }};
        const showLocationCol = {{ $showItemLocation ? 'true' : 'false' }};
        const warehouseOptions = `
            @foreach ($Warehouse as $w)
                <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
            @endforeach
        `;
        $(document).ready(function() {
            // Init Global Select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize existing product selects
            $('.product-select2').each(function() {
                initProductSelect2($(this));
            });

            // Recalc existing rows
            recalcAll();

            // Add Row
            window.addBlankRow = function() {
                let warehouseTd = showWarehouseCol ? `<td><select class="form-select" name="item_warehouse_id[]">${warehouseOptions}</select></td>` : '';
                let locationTd = showLocationCol ? `<td><input type="text" class="form-control" name="item_location[]" placeholder="Loc"></td>` : '';
                const html = `
                <tr>
                    <td>
                        <select class="form-select product-select2" name="product_id[]"></select>
                        <input type="hidden" name="size_mode[]" class="hidden-size-mode">
                        <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                        <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                        <input type="hidden" name="length[]" class="hidden-length">
                        <input type="hidden" name="width[]" class="hidden-width">
                        <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="0">
                        <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="0">
                    </td>
                    ${warehouseTd}
                    ${locationTd}
                    <td><input type="text" class="form-control box-qty" placeholder="qty"></td>
                    <td><input type="number" class="form-control input-readonly pack-size" value="1" readonly></td>
                    <td><input type="number" name="qty[]" class="form-control input-readonly qty-pcs" value="0" readonly></td>
                    <td><div class="input-group input-group-sm"><input type="number" name="price[]" class="form-control price" step="0.01" value="0"></div></td>
                    <td><input type="number" class="form-control item-disc-percent" value="0"></td>
                    <td><input type="number" name="item_discount[]" class="form-control item-disc-amt" value="0"></td>
                    <td><input type="number" class="form-control input-readonly row-total" value="0" readonly></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                </tr>`;
                const $row = $(html);
                $('#purchaseTableBody').append($row);
                initProductSelect2($row.find('.product-select2'));
            };

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcAll();
            });

            // Inputs -> Calc
            $('#purchaseTableBody').on('input', '.box-qty, .price, .item-disc-percent, .item-disc-amt', function() {
                if ($(this).hasClass('box-qty')) {
                    normalizeQtyInput($(this), $(this).closest('tr'));
                }
                recalcRow($(this).closest('tr'));
                recalcAll();
            });

            $('#billDiscount, #billDiscountPct, #extraCost').on('input', function() {
                recalcAll();
            });

            // --- Payment Section Logic ---
            $('#btnAddPayment').on('click', function() {
                const row = `
                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                    <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                        <option value="" selected disabled>Select Account</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`;
                $('#paymentWrapper').append(row);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                recalcPayments();
            });

            $(document).on('input', '.payment-amount', function() {
                recalcPayments();
            });

            function recalcPayments() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
            }

            function normalizeQtyInput($input, $row) {
                // Same logic as add_purchase_v2
                const val = $input.val();
                const ppb = parseFloat($row.find('.pack-size').val()) || 1;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();

                if (sizeMode === 'by_pieces') {
                    if (val.includes('.')) {
                        $input.val(val.split('.')[0]);
                    }
                    return;
                }

                if (ppb > 1 && val.includes('.')) {
                    const parts = val.split('.');
                    const boxes = parseInt(parts[0]) || 0;
                    const loose = parts[1] ? parseInt(parts[1]) : 0;

                    if (loose >= ppb) {
                        const extraBoxes = Math.floor(loose / ppb);
                        const newLoose = loose % ppb;
                        const newBoxes = boxes + extraBoxes;
                        let newVal = newBoxes.toString();
                        if (newLoose > 0) newVal += '.' + newLoose;
                        $input.val(newVal);
                    }
                }
            }

            function recalcRow($row) {
                let boxesStr = $row.find('.box-qty').val();
                if (!boxesStr) boxesStr = "0";
                boxesStr = boxesStr.toString();

                const ppb = parseFloat($row.find('.pack-size').val()) || 1;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const pieces_per_m2 = parseFloat($row.data('pieces_per_m2')) || parseFloat($row.find(
                    '.hidden-pieces-per-m2').val()) || 0;

                let boxes = 0;
                let loose = 0;
                let totalPieces = 0;

                if (ppb > 1 && boxesStr.includes('.')) {
                    const parts = boxesStr.split('.');
                    boxes = parseInt(parts[0]) || 0;
                    loose = parts[1] ? parseInt(parts[1]) : 0;
                    totalPieces = (boxes * ppb) + loose;
                } else {
                    boxes = parseFloat(boxesStr) || 0;
                    totalPieces = boxes * ppb;
                }

                // Update hidden separate fields
                $row.find('.hidden-boxes-qty').val(boxes);
                $row.find('.hidden-loose-qty').val(loose);

                $row.find('.qty-pcs').val(totalPieces);

                const price = parseFloat($row.find('.price').val()) || 0;

                // --- TOTAL CALCULATION ---
                let grossTotal = 0;

                if (sizeMode == 'by_size') {
                    // Price is per M2. Total M2 = totalPieces * pieces_per_m2 (m2/piece)
                    grossTotal = (totalPieces * pieces_per_m2) * price;
                } else if (sizeMode == 'by_cartons') {
                    // Price is per Carton.
                    // If ppb > 0
                    if (ppb > 0) {
                        grossTotal = (totalPieces / ppb) * price;
                    } else {
                        grossTotal = totalPieces * price; // fallback
                    }
                } else {
                    // Price is per Piece
                    grossTotal = totalPieces * price;
                }

                // Discount
                let discAmt = parseFloat($row.find('.item-disc-amt').val()) || 0;
                // If focus on %, calc amt
                if ($(document.activeElement).hasClass('item-disc-percent')) {
                    const pct = parseFloat($row.find('.item-disc-percent').val()) || 0;
                    discAmt = grossTotal > 0 ? grossTotal * (pct / 100) : 0;
                    $row.find('.item-disc-amt').val(discAmt.toFixed(2));
                } else {
                    // Else calc % from amt (default or if amt edited)
                    const pct = grossTotal > 0 ? (discAmt / grossTotal) * 100 : 0;
                    $row.find('.item-disc-percent').val(pct.toFixed(2));
                }

                const net = grossTotal - discAmt;
                $row.find('.row-total').val(net.toFixed(2));
            }

            function recalcAll() {
                let totalQty = 0;
                let subtotal = 0;

                $('#purchaseTableBody tr').each(function() {
                    const qty = parseFloat($(this).find('.qty-pcs').val()) || 0;
                    const total = parseFloat($(this).find('.row-total').val()) || 0;
                    totalQty += qty;
                    subtotal += total;
                });

                $('#tQty').text(totalQty.toFixed(2));
                $('#tSub').text(subtotal.toFixed(2));
                $('#subtotalInput').val(subtotal.toFixed(2));
                $('#totalAmount').text(subtotal.toFixed(2));

                const billDiscVal = parseFloat($('#billDiscount').val()) || 0;
                
                // If focus is on %, recalc the amount before using it
                if ($(document.activeElement).is('#billDiscountPct')) {
                    const pct = parseFloat($('#billDiscountPct').val()) || 0;
                    const calculatedAmt = subtotal * (pct / 100);
                    $('#billDiscount').val(calculatedAmt.toFixed(2));
                } else {
                    // Calc % from amount
                    const pct = subtotal > 0 ? (billDiscVal / subtotal) * 100 : 0;
                    $('#billDiscountPct').val(pct.toFixed(2));
                }

                const finalBillDisc = parseFloat($('#billDiscount').val()) || 0;
                const extraCost = parseFloat($('#extraCost').val()) || 0;

                const net = subtotal - finalBillDisc + extraCost;

                $('#tPayable').text(net.toFixed(2));
                $('#netAmountInput').val(net.toFixed(2));
            }

            function initProductSelect2($el) {
                $el.select2({
                    placeholder: 'Search Product...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('products.ajax.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            // Map if needed or just return results
                            return {
                                results: data.results || data
                            };
                        },
                        cache: true
                    },
                    templateResult: formatProduct,
                    templateSelection: formatSelection
                });

                $el.on('select2:select', function(e) {
                    const data = e.params.data;
                    const $row = $(this).closest('tr');

                    // Populate Snapshots
                    $row.find('.hidden-size-mode').val(data.size_mode || '');
                    $row.find('.hidden-pieces-per-box').val(data.pieces_per_box || 1);
                    $row.find('.hidden-pieces-per-m2').val(data.pieces_per_m2 || 0);
                    $row.find('.hidden-length').val(data.length || '');
                    $row.find('.hidden-width').val(data.width || '');

                    $row.find('.pack-size').val(data.pieces_per_box || 1);

                    // Set default discount
                    $row.find('.item-disc-percent').val(data.purchase_discount_percent || 0);

                    // Set Price & Label
                    const sizeMode = data.size_mode || 'std';
                    const pM2 = parseFloat(data.purchase_price_per_m2) || 0;
                    const pPiece = parseFloat(data.purchase_price_per_piece) || 0;
                    const ppb = parseFloat(data.pieces_per_box) || 1;

                    let price = 0;
                    let unitLabel = '';

                    if (sizeMode === 'by_size') {
                        price = pM2;
                        unitLabel = '(m²)';
                    } else if (sizeMode === 'by_cartons') {
                        price = pPiece * ppb; // Carton Price
                        unitLabel = '(carton)';
                    } else {
                        price = pPiece;
                        unitLabel = '(piece)';
                    }

                    $row.find('.price').val(price);
                    // Add/Update label (remove old if any)
                    $row.find('.price-unit-label').remove();
                    $row.find('.price').after(
                        '<small class="text-muted price-unit-label" style="font-size:0.7rem;">' +
                        unitLabel + '</small>');

                    // Data Attributes
                    $row.data('sizemode', sizeMode);
                    $row.data('pieces_per_m2', Number(data.pieces_per_m2) || 0);

                    // Recalc
                    $row.find('.box-qty').focus();
                    recalcRow($row);
                    recalcAll();
                });
            }

            function formatProduct(repo) {
                if (repo.loading) return repo.text;
                let stock = repo.stock !== undefined ? repo.stock : 0;
                let sku = repo.sku || 'N/A';
                return $(`
                <div class="clearfix">
                    <div class="float-start">
                        <div class="fw-bold">${repo.name || repo.text}</div>
                        <small class="text-muted">SKU: ${sku}</small>
                    </div>
                    <div class="float-end">
                        <span class="badge bg-secondary rounded-pill">Stock: ${stock}</span>
                    </div>
                </div>`);
            }

            function formatSelection(repo) {
                return repo.name || repo.text;
            }
        });
    </script>
@endsection
