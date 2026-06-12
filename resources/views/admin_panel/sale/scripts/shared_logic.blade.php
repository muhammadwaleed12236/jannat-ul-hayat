

<script>
    /* =========================================
       SHARED SALES LOGIC (Add/Edit)
       ========================================= */

    // --- Helpers ---
    function pad(n) {
        return n < 10 ? '0' + n : n
    }

    function setNowStamp() {
        if ($('#entryDateTime').length) {
            const d = new Date();
            const dt =
                `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${String(d.getFullYear()).slice(-2)} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
            const dOnly = `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${String(d.getFullYear()).slice(-2)}`;
            $('#entryDateTime').text('Entry Date_Time: ' + dt);
            $('#entryDate').text('Date: ' + dOnly);
        }
    }

    setNowStamp();
    setInterval(setNowStamp, 60 * 1000);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    function showAlert(type, msg) {
        const el = $('#alertBox');
        if (el.length) {
            el.removeClass('d-none alert-success alert-danger alert-warning').addClass('alert-' + type).text(msg)
                .show();
            setTimeout(() => el.addClass('d-none'), 3000);
        } else {
            // fallback if alertBox missing
            if (type === 'error') Swal.fire('Error', msg, 'error');
            else if (type === 'warning') Swal.fire('Warning', msg, 'warning');
            else Swal.fire('Info', msg, 'info');
        }
    }

    function toNum(v) {
        if (typeof v === 'number') return v;
        if (!v) return 0;
        // Handle thousands separator (comma)
        const str = v.toString().replace(/,/g, '');
        return parseFloat(str) || 0;
    }

    /* =========================================
       PRODUCT SELECT2
       ========================================= */
    function initProductSelect2($el) {
        $el.select2({
            placeholder: 'Search Product (Name / SKU / Barcode)',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('products.ajax.search') }}?type=sale',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: formatProduct,
            templateSelection: formatSelection
        });
    }

    function formatProduct(repo) {
        if (repo.loading) return repo.text;
        let stock = repo.stock !== undefined ? repo.stock : 0;
        let badgeClass = stock > 0 ? 'bg-success' : 'bg-danger';
            
        return $(`
        <div class="clearfix">
            <div class="float-start">
                <div class="fw-bold">${repo.name || repo.text}</div>
                <div class="d-flex gap-2 align-items-center">
                    <small class="text-muted">SKU: ${repo.sku || 'N/A'}</small>
                    <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">${repo.warehouse_name || 'WH'}</span>
                    <span class="badge bg-info text-white" style="font-size: 0.65rem;">Rack: ${repo.rack || '-'}</span>
                </div>
            </div>
            <div class="float-end text-end">
                <span class="badge ${badgeClass} rounded-pill d-block mb-1">Stock: ${stock}</span>
            </div>
        </div>
    `);
    }

    function formatSelection(repo) {
        return repo.name || repo.text;
    }


    /* =========================================
       CORE LOGIC
       ========================================= */

    function addNewRow() {
        const rowHtml = `
  <tr>
    <!-- PRODUCT -->
    <td class="col-product">
      <select class="form-select product" name="product_id[]" style="width:100%">
        <option value=""></option>
      </select>
      <input type="hidden" class="item-code-display">
      <input type="hidden" class="size-h">
      <input type="hidden" class="size-w">
      <input type="hidden" class="size-mode-text">
    </td>

    <!-- STOCK -->
    <td class="col-stock">
      <input type="text" class="form-control stock text-center input-readonly" readonly tabindex="-1" placeholder="Stock">
      <input type="hidden" class="warehouse-id-hidden" name="warehouse_id[]">
      <input type="hidden" class="location-input" name="location[]">
    </td>

    <!-- BOTTLE -->
    <td style="width: 100px; vertical-align: middle;">
        <div class="bottle-info text-center">
            <span class="bottle-name small text-muted d-block" style="font-size: 0.7rem; line-height: 1;">—</span>
            <div class="form-check form-check-inline m-0">
                <input class="form-check-input include-bottle-check" type="checkbox" checked title="Include free bottle and reduce stock" onchange="$(this).closest('td').find('.include-bottle-hidden').val(this.checked ? 1 : 0)">
                <input type="hidden" name="include_bottle[]" class="include-bottle-hidden" value="1">
            </div>
            <input type="hidden" class="bottle-product-id" name="bottle_product_id[]">
            <input type="hidden" class="bottle-mappings-json">
        </div>
    </td>

    <!-- Qty Pieces (Input) -->
    <td class="col-qty">
      <input type="text" class="form-control sales-qty text-end" name="qty[]" id="sales-qty" placeholder="Boxes + Pcs">
    </td>

    <!-- Pack Size (Readonly) -->
    <td class="col-qty">
       <input type="text" class="form-control pack-qty text-end input-readonly" name="pack_qty[]" readonly placeholder="Size" tabindex="-1">
    </td>

    <!-- Packet/Box (Calculated) -->
    <td class="col-pieces">
      <input type="text" class="form-control total-pieces text-end input-readonly" name="total_pieces[]" readonly placeholder="Box" tabindex="-1">
    </td>
 
    <!-- Price/Piece (EDITABLE) -->
    <td class="col-price-p">
      <input type="text" class="form-control visible-price text-end" name="visible_price[]" placeholder="0.00">
      <input type="hidden" class="price-per-piece" name="price_per_piece[]">
      <input type="hidden" class="retail-price"> <!-- Hidden retail/box price storage if needed -->
    </td>

    <!-- DISCOUNT -->
    <td class="col-disc">
      <div class="discount-wrapper">
        <input type="number"
               class="form-control discount-value text-end"
               name="item_disc[]"
               placeholder="">
        <!-- Hidden: tells backend whether value is % or fixed PKR -->
        <input type="hidden" class="discount-type-hidden" name="discount_type[]" value="percent">
        <button type="button"
                class="btn btn-outline-secondary discount-toggle"
                data-type="percent" tabindex="-1">%</button>
      </div>
    </td>

    <!-- DISCOUNT AMOUNT -->
    <td class="col-disc-amt">
      <input type="text" class="form-control discount-amount text-end" readonly tabindex="-1">
    </td>

    <!-- NET AMOUNT -->
    <td class="col-amount">
      <input type="text" class="form-control sales-amount text-end input-readonly" name="total[]" value="0" readonly tabindex="-1">
      <input type="hidden" class="gross-amount">
    </td>

    <!-- ACTION -->
    <td class="col-action">
      <button type="button" class="btn btn-sm btn-outline-danger del-row" tabindex="-1">&times;</button>
    </td>
  </tr>`;

        const $row = $(rowHtml);
        $('#salesTableBody').append($row);
        initProductSelect2($row.find('.product'));
    }

    // --- appendNewRow: Add row with pre-filled data (for Edit mode) ---
    function appendNewRow(item) {
        addNewRow();
        var $row = $('#salesTableBody tr:last');
        var productId = item.product_id || item.productId;

        if (productId) {
            var productName = item.product_name || ('Product #' + productId);
            var opt = new Option(productName, productId, true, true);
            $row.find('.product').append(opt).trigger('change');
            // Set size_mode data attribute for computeRow
            $row.data('size_mode', item.size_mode || 'std');
        }

        // Set all hidden + visible fields directly (no setTimeout needed)
        $row.find('.sales-qty').val(item.qty || '');
        $row.find('.visible-price').val(item.price || item.price_per_piece || '');
        $row.find('.price-per-piece').val(item.price || item.price_per_piece || '');
        $row.find('.discount-value').val(item.discount_percent || 0);
        $row.find('.warehouse-id-hidden').val(item.warehouse_id || '');
        $row.find('.location-input').val(item.location || '');
        $row.find('.loose_pieces').val(item.loose_pieces || 0);
        $row.find('.total_pieces').val(item.total_pieces || '');
        $row.find('.pack-qty').val(item.pieces_per_box || '');
        $row.find('.size-h').val(item.height || 0);
        $row.find('.size-w').val(item.width || 0);
        $row.find('.item-code-display').val(item.barcode || '');

        if (item.bottle_product_id) {
            $row.find('.bottle-product-id').val(item.bottle_product_id);
        }
        if (item.include_bottle !== undefined) {
            $row.find('.include-bottle-check').prop('checked', !!item.include_bottle);
            $row.find('.include-bottle-hidden').val(item.include_bottle ? 1 : 0);
        }
    }

    // --- Loading Data for Rows ---

    function fetchProductPrice($row, productId) {
        console.log('Fetching price for product:', productId);
        $.get('{{ route('get-price') }}', {
            product_id: productId
        }).done(function(pRes) {
            // Fill Item Code
            $row.find('.item-code-display').val(pRes.item_code || '');

            // Populate Fields
            // Store retail price (box price) in hidden field and visible if needed
            $row.find('.retail-price').val(pRes.retail_price || 0);
            // Visible price logic: usually per piece, but ensure consistent display
            if (pRes.size_mode == "by_cartons") {
                $row.find('.visible-price').val(pRes.sale_price_per_piece || pRes.retail_price || 0);
            } else if (pRes.size_mode == "by_pieces") {
                $row.find('.visible-price').val(pRes.sale_price_per_piece || pRes.retail_price || 0);
            } else {
                $row.find('.visible-price').val(pRes.price_per_m2 || pRes.retail_price || 0);
            }

            $row.find('.pack-qty').val(pRes.pieces_per_box || 1);
            $row.find('.price-per-piece').val(pRes.sale_price_per_piece || pRes.price_per_m2 || 0);

            $row.find('.size-h').val(pRes.height || '-');
            $row.find('.size-w').val(pRes.width || '-');
            $row.find('.size-mode-text').val(pRes.size_mode || '-');

            // Set default discount
            $row.find('.discount-value').val(pRes.sale_discount_percent || 0);

            $row.data('size_mode', pRes.size_mode);
            $row.data('pieces_per_box', pRes.pieces_per_box || 1);
            $row.data('price_per_m2', pRes.price_per_m2 || 0);

            computeRow($row);
        }).fail(function(err) {
            console.error('Price fetch failed', err);
        });
    }



    // --- Calculation ---

    function computeRow($row, isManual = false) {
        const rp = toNum($row.find('.retail-price').val()); // Box Price
        const visiblePrice = toNum($row.find('.visible-price').val());

        const m2_per_piece = parseFloat($row.find('.size-h').val() * $row.find('.size-w').val() / 10000);

        const qtyInput = $row.find('.sales-qty').val(); // String
        const sizeMode = $row.data('size_mode');
        const packQty = parseFloat($row.find('.pack-qty').val()) || 1;

        let totalPieces = 0;
        let displayCalc = 0;

        // Qty Parsing
        if (sizeMode === 'by_cartons' || sizeMode === 'by_size') {
            // Box.Loose
            const parts = qtyInput.toString().split('.');
            const boxes = parseInt(parts[0]) || 0;
            const loose = parts[1] ? parseInt(parts[1]) : 0;
            totalPieces = (boxes * packQty) + loose;
            displayCalc = totalPieces;
        } else {
            // Pieces
            totalPieces = toNum(qtyInput);
            if (packQty > 0) {
                displayCalc = totalPieces / packQty;
            }
        }

        const discValue = toNum($row.find('.discount-value').val());
        const discType = $row.find('.discount-toggle').data('type');
        let dam = toNum($row.find('.discount-amount').val());

        $row.find('.total-pieces').val(Number.isInteger(displayCalc) ? displayCalc : displayCalc.toFixed(2));

        // Gross Calc
        // Logic: if mode=by_carton, use box price?
        // Prioritize `price-per-piece` (hidden) if available, or fall back.
        let unitPrice = toNum($row.find('.price-per-piece').val());
        if (unitPrice <= 0) unitPrice = visiblePrice; // fallback

        let gross = 0;

        if (sizeMode === 'by_size') {
            gross = m2_per_piece * totalPieces * unitPrice;
            if (!m2_per_piece) gross = 0;
        } else if (sizeMode === 'by_cartons') {
            // Check if we use m2 or box price
            if (m2_per_piece > 0) {
                gross = m2_per_piece * totalPieces * unitPrice;
            } else {
                // By Cartons (without size)
                // If unitPrice is "Price per piece", gross = totalPieces * unitPrice
                // If unitPrice is "Price per box", we need to adjust.
                // Usually `price-per-piece` is strictly price per single unit.
                gross = totalPieces * unitPrice;

                // Note: In edit_sale logic:
                // if (packQty > 0) gross = (totalPieces / packQty) * unitPrice;
                // This implies unitPrice was Box Price in that specific context.
                // Let's check `fetchProductPrice`. 
                // .price-per-piece -> pRes.sale_price_per_piece.
                // So it IS per piece. So gross = pieces * price_per_piece is correct.
            }
        } else {
            gross = unitPrice * totalPieces;
        }

        // Discount Calculation — alwys derived from discValue + discType
        // Never rely on manually entered discount-amount (that field is now readonly/calculated)
        if (discType === 'percent') {
            dam = discValue > 0 ? (gross * discValue) / 100 : 0;
        } else {
            // PKR / fixed mode: discValue IS the rupee amount
            dam = discValue > 0 ? discValue : 0;
        }
        $row.find('.discount-amount').val(dam.toFixed(2));

        const netRow = Math.max(0, gross - dam);
        $row.find('.gross-amount').val(gross.toFixed(2));
        $row.find('.sales-amount').val(netRow.toFixed(2));
    }

    function updateGrandTotals() {
        let tQty = 0;
        let tGross = 0;
        let tLineDisc = 0;
        let tNet = 0;

        $('#salesTableBody tr').each(function() {
            const $r = $(this);
            // We use the hidden gross amount if available, otherwise fallback to sales-amount
            let gross = toNum($r.find('.gross-amount').val());
            const net = toNum($r.find('.sales-amount').val());
            const dam = toNum($r.find('.discount-amount').val());
            
            // If gross wasn't set (maybe during initial load), calculate it
            if (gross <= 0 && net > 0) gross = net + dam;

            // Piece calc for total
            const mode = $r.data('size_mode');
            const qtyStr = $r.find('.sales-qty').val().toString();
            const pq = parseFloat($r.find('.pack-qty').val()) || 1;

            let pieces = 0;
            if (mode === 'by_cartons' || mode === 'by_size') {
                const parts = qtyStr.split('.');
                const b = parseInt(parts[0]) || 0;
                const l = parts[1] ? parseInt(parts[1]) : 0;
                pieces = (b * pq) + l;
            } else {
                pieces = toNum(qtyStr);
                // Plus loose pieces?? In 'std' mode, loose-pieces input might be used? 
                // In edit_sale logic: `totalPieces = qtyPcs + loose`.
                const loose = toNum($r.find('.loose-pieces').val());
                pieces += loose;
            }

            tQty += pieces;
            tGross += gross;
            tLineDisc += dam;
            tNet += net;
        });

        const orderPct = toNum($('#discountPercent').val());
        const orderDisc = (tNet * orderPct) / 100;
        const prev = toNum($('#previousBalance').val());
        const receipts = toNum($('#receiptsTotal').text());
        const payable = Math.max(0, tNet - orderDisc + prev - receipts);
        const currentInvoiceTotal = Math.max(0, tNet - orderDisc);

        $('#tQty').text(tQty.toFixed(0));
        $('#tGross').text(tGross.toFixed(2));
        $('#tLineDisc').text(tLineDisc.toFixed(2));
        $('#tSub').text(tNet.toFixed(2));
        $('#tOrderDisc').text(orderDisc.toFixed(2));
        $('#tPrev').text(prev.toFixed(2));
        $('#tPayable').text(payable.toFixed(2));
        $('#totalAmount').text(tNet.toFixed(2));

        // Display current bill total after all discounts
        $('#tCurrentBill').text(currentInvoiceTotal.toFixed(2));

        $('#subTotal1').val(tGross.toFixed(2));
        $('#subTotal2').val(tNet.toFixed(2));
        $('#discountAmount').val(orderDisc.toFixed(2));
        $('#totalBalance').val(currentInvoiceTotal.toFixed(2));
        $('input[name="cash"]').val(receipts.toFixed(2));
    }


    /* =========================================
       VALIDATION & SAVE
       ========================================= */

    function serializeForm() {
        return $('#saleForm').serialize();
    }

    function canPost() {
        let ok = false;
        $('#salesTableBody tr').each(function() {
            const pid = $(this).find('.product').val();
            const qtyStr = $(this).find('.sales-qty').val();
            // simple check: qty string not empty/0
            if (pid && qtyStr && qtyStr != 0) {
                ok = true;
                return false;
            }
        });
        return ok;
    }

    function refreshPostedState() {
        const state = canPost();
        $('#btnPosted, #btnHeaderPosted').prop('disabled', !state);
    }

    function ensureSaved() {
        return new Promise(function(resolve, reject) {
            const existing = $('#booking_id').val();
            let url = '{{ route('sales.store') }}';
            let method = 'POST';
            if (existing) {
                url = '{{ route('sales.update', ':id') }}'.replace(':id', existing);
                method = 'PUT';
            }

            $('#btnSave, #btnHeaderPosted, #btnPosted').prop('disabled', true);

            $.ajax({
                url: url,
                type: method,
                data: serializeForm(),
                success: function(res) {
                    $('#btnSave, #btnHeaderPosted, #btnPosted').prop('disabled', false);
                    if (res?.ok) {
                        const bid = res.booking_id || existing;
                        $('#booking_id').val(bid);
                        Swal.fire('Saved', 'Sale saved successfully', 'success');
                        resolve(bid);
                    } else {
                        Swal.fire('Error', res.msg || 'Save failed', 'error');
                        reject(res);
                    }
                },
                error: function(xhr) {
                    $('#btnSave, #btnHeaderPosted, #btnPosted').prop('disabled', false);
                    var msg = 'Save error';
                    if (xhr.responseJSON) {
                        msg = xhr.responseJSON.message || xhr.responseJSON.msg || JSON.stringify(xhr.responseJSON.errors);
                    } else if (xhr.statusText) {
                        msg = xhr.statusText;
                    }
                    Swal.fire('Error (' + xhr.status + ')', msg, 'error');
                    reject(xhr);
                }
            });
        });
    }

    function postNow() {
        let formData = $('#saleForm').serializeArray();
        formData = formData.filter(item => item.name !== '_method');

        $.post('{{ route('sales.post_final') }}', $.param(formData))
            .done(function(res) {
                if (res?.ok) {
                    window.open(res.invoice_url, '_blank');
                    Swal.fire({
                        title: 'Success!',
                        text: 'Posted & invoice opened',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.href = "{{ route('sale.index') }}", 2000);
                } else {
                    Swal.fire('Post Failed', res.msg || 'Post failed', 'error');
                }
            })
            .fail(function(xhr) {
                Swal.fire('Error', 'Post error', 'error');
            });
    }

    // Validation Utils
    function markInvalid($el) {
        $el.addClass('invalid-input invalid-select');
        $el.closest('td').addClass('invalid-cell');
    }

    function clearInvalid($el) {
        $el.removeClass('invalid-input invalid-select');
        $el.closest('td').removeClass('invalid-cell');
    }

    function clearAllInvalids() {
        $('.invalid-input, .invalid-select').removeClass('invalid-input invalid-select');
        $('.invalid-cell').removeClass('invalid-cell');
    }

    function cleanupEmptyRows() {
        $('#salesTableBody tr').each(function() {
            const $r = $(this);
            const prod = $r.find('.product').val();
            const wh = $r.find('.warehouse-id-hidden').val();
            const qty = parseFloat($r.find('.sales-qty').val() || '0') || 0;
            if ((qty <= 0) || ((!prod || prod === '') && (!wh || wh === ''))) {
                if ($('#salesTableBody tr').length > 1) {
                    $r.remove();
                } else {
                    // clear last row if needed
                    $r.find('select').val('');
                    $r.find('input').val('');
                    $r.find('.stock').val('');
                    $r.find('.sales-amount').val('0');
                }
            }
        });
        if ($('#salesTableBody tr').length === 0) addNewRow();
    }

    function validateHeader() {
        let ok = true;
        let firstMessage = null;
        let firstEl = null;

        const partyType = $('input[name="partyType"]:checked').val();
        if (!partyType) {
            ok = false;
            firstMessage = 'Please select Type';
            firstEl = $('input[name="partyType"]').first();
            $('#partyTypeGroup').addClass('invalid-cell');
        } else {
            $('#partyTypeGroup').removeClass('invalid-cell');
        }

        if (partyType === 'Walking Customer') {
            const walkin = $('#walkin_name').val();
            if (!walkin) {
                ok = false;
                firstMessage = 'Please enter Walk-in Customer Name';
                firstEl = $('#walkin_name');
                markInvalid($('#walkin_name'));
            }
        } else {
            const cust = $('#customerSelect').val();
            if (!cust) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please select Customer';
                    firstEl = $('#customerSelect');
                }
                markInvalid($('#customerSelect'));
            }
        }

        return {
            ok,
            firstMessage,
            firstEl
        };
    }

    function validateRows() {
        let ok = true;
        let firstMessage = null;
        let firstEl = null;

        $('#salesTableBody tr').each(function(rowIndex) {
            const $row = $(this);
            const $wh = $row.find('.warehouse-id-hidden');
            const $prod = $row.find('.product');
            const $qty = $row.find('.sales-qty');

            if (!$wh.val()) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please select Item with a valid Location for row ' + (rowIndex + 1);
                    firstEl = $prod;
                }
                markInvalid($prod);
            }

            if (!$prod.val()) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please select Item for row ' + (rowIndex + 1);
                    firstEl = $prod;
                }
                markInvalid($prod);
            }

            const qtyVal = parseFloat($qty.val() || '0') || 0;
            if (qtyVal <= 0) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please enter Item qty (> 0) for row ' + (rowIndex + 1);
                    firstEl = $qty;
                }
                markInvalid($qty);
            }
        });

        return {
            ok,
            firstMessage,
            firstEl
        };
    }

    function validateReceipts() {
        let ok = true;
        let firstMessage = null;
        let firstEl = null;

        $('#rvWrapper .rv-row').each(function(i) {
            const $row = $(this);
            const $acc = $row.find('.rv-account');
            const $amt = $row.find('.rv-amount');
            const amtVal = parseFloat($amt.val() || '0') || 0;

            if (amtVal > 0 && (!$acc.val() || $acc.val() === "")) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please select Account for receipt row ' + (i + 1);
                    firstEl = $acc;
                }
                markInvalid($acc);
            }
        });

        return {
            ok,
            firstMessage,
            firstEl
        };
    }

    function validateFormAll() {
        clearAllInvalids();

        const h = validateHeader();
        if (!h.ok) return {
            ok: false,
            message: h.firstMessage,
            el: h.firstEl
        };

        const r = validateRows();
        if (!r.ok) return {
            ok: false,
            message: r.firstMessage,
            el: r.firstEl
        };

        const rec = validateReceipts();
        if (!rec.ok) return {
            ok: false,
            message: rec.firstMessage,
            el: rec.firstEl
        };

        return {
            ok: true
        };
    }


    /* =========================================
       EVENT BINDINGS
       ========================================= */

    $(document).ready(function() {

        // Remove invalid classes on input
        $(document).on('input change', 'select, input, textarea', function() {
            clearInvalid($(this));
        });

        // Product change
        $('#salesTableBody').on('change select2:select', '.product', function(e) {
            console.log("product change in shared logic");
            if (window.isEditModeLoading) return; // Block during edit load
            
            const $row = $(this).closest('tr');
            
            if (e.type === 'select2:select') {
                const data = e.params.data;
                $row.find('.warehouse-id-hidden').val(data.warehouse_id);
                $row.find('.location-input').val(data.rack === 'No Rack' ? '' : data.rack);
                $row.find('.stock').val(data.stock);
                
                // Store bottle mappings
                $row.find('.bottle-mappings-json').val(JSON.stringify(data.bottle_mappings || []));
                updateBottleSelection($row);
            }
            
            const pid = $(this).val();
            if (!pid) return;

            fetchProductPrice($row, pid);
        });


        // Inputs -> Calc
        // --- Helper for Box Conversion ---
        window.normalizeQtyInput = function($input, $row) {
            const val = $input.val();
            const sizeMode = $row.data('size_mode');
            const ppb = parseFloat($row.find('.pack-qty').val()) || 1;

            if ((sizeMode === 'by_cartons' || sizeMode === 'by_size') && ppb > 1 && val.includes('.')) {
                const parts = val.split('.');
                const boxes = parseInt(parts[0]) || 0;
                const looseStr = parts[1];

                if (looseStr && looseStr !== '') {
                    const loose = parseInt(looseStr);
                    if (loose >= ppb) {
                        const extraBoxes = Math.floor(loose / ppb);
                        const newLoose = loose % ppb;
                        const newBoxes = boxes + extraBoxes;

                        let newVal = newBoxes.toString();
                        if (newLoose > 0) {
                            newVal += '.' + newLoose;
                        } // re-run
                        $input.val(newVal);
                    }
                }
            }
        };

        // $(document).ready(function() {

        // ... existing bindings ...

        // Inputs -> Calc
    function updateBottleSelection($row) {
        const mappingsStr = $row.find('.bottle-mappings-json').val();
        const mappings = JSON.parse(mappingsStr || '[]');
        const qty = toNum($row.find('.sales-qty').val());
        const $bottleName = $row.find('.bottle-name');
        const $bottleId = $row.find('.bottle-product-id');
        const $check = $row.find('.include-bottle-check');

        if (mappings.length > 0) {
            // Find matching slab
            const match = mappings.find(m => qty >= parseFloat(m.min_qty) && qty <= parseFloat(m.max_qty));
            if (match) {
                $bottleName.text(match.bottle_product.item_name).removeClass('text-muted').addClass('text-primary fw-bold');
                $bottleId.val(match.bottle_product_id);
                $check.prop('disabled', false).prop('checked', true);
                $row.find('.include-bottle-hidden').val(1);
            } else {
                $bottleName.text('No Slab').addClass('text-muted').removeClass('text-primary fw-bold');
                $bottleId.val('');
                $check.prop('checked', false).prop('disabled', true);
                $row.find('.include-bottle-hidden').val(0);
            }
        } else {
            $bottleName.text('—').addClass('text-muted').removeClass('text-primary fw-bold');
            $bottleId.val('');
            $check.prop('checked', false).prop('disabled', true);
            $row.find('.include-bottle-hidden').val(0);
        }
    }

    $(document).on('input', '.sales-qty, .pack-qty, .loose-pieces, .discount-value, .visible-price',
        function() {
            const $row = $(this).closest('tr');
            if ($(this).hasClass('sales-qty')) {
                normalizeQtyInput($(this), $row);
                updateBottleSelection($row);
            }

                // If user manually changes the visible price, also update the hidden price-per-piece
                if ($(this).hasClass('visible-price')) {
                    const $row = $(this).closest('tr');
                    const newPrice = toNum($(this).val());
                    $row.find('.price-per-piece').val(newPrice);
                    $row.find('.retail-price').val(newPrice);
                }

                computeRow($(this).closest('tr'));
                updateGrandTotals();
                refreshPostedState();
            });

        $(document).on('input', '.discount-amount', function() {
            computeRow($(this).closest('tr'), true);
            updateGrandTotals();
            refreshPostedState();
        });

        // Delete Row
        $(document).on('click', '.del-row', function() {
            if ($('#salesTableBody tr').length > 1) {
                $(this).closest('tr').remove();
                updateGrandTotals();
                refreshPostedState();
            }
        });

        // Add Row Button
        $('#btnAdd').click(addNewRow);

        // Enter on any editable input -> compute row, add new row & open product select
        $('#salesTableBody').on('keydown', '.sales-qty, .discount-value, .discount-amount, .visible-price', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                const $current = $(this).closest('tr');
                computeRow($current);
                updateGrandTotals();

                // Add new row and open product dropdown
                addNewRow();
                setTimeout(() => $('#salesTableBody tr:last-child .product').select2('open'), 50);
            }
        });

        // Discount Toggle: % <-> PKR
        $(document).on('click', '.discount-toggle', function() {
            const $btn = $(this);
            const currentType = $btn.data('type');
            const newType = currentType === 'percent' ? 'pkr' : 'percent';
            $btn.data('type', newType).text(newType === 'percent' ? '%' : 'PKR');
            // Sync hidden input so form submission carries correct type
            $btn.closest('.discount-wrapper').find('.discount-type-hidden').val(newType);
            computeRow($btn.closest('tr'));
            updateGrandTotals();
        });

        // Buttons: Booking (Save)
        $('#btnSave').off('click').on('click', function() {
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }
            $('#action').val('booking');
            ensureSaved();
        });

        // Buttons: Sale (Post)
        $('#btnPosted, #btnHeaderPosted').off('click').on('click', function() {
            $('#action').val('sale');
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }

            // Credit Limit Check
            // Credit Limit Check
            const rangeBal = toNum($('#rangeBalance').val());
            if (rangeBal > 0) {
                const prevBal = toNum($('#previousBalance').val());

                // Invoice Net Amount (Subtotal - Extra Discount)
                const invoiceNet = toNum($('#totalBalance').val());
                // Amount Paid Now
                const paidNow = toNum($('#receiptsTotal').text());

                // Projected Balance: Previous + New Debt - Payment
                const projectedBalance = prevBal + invoiceNet - paidNow;

                // Validate
                if (projectedBalance > rangeBal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Credit Limit Exceeded',
                        html: `Projected Balance (<strong>${projectedBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>) exceeds Credit Limit (<strong>${rangeBal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>)`,
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }

            if (!canPost()) {
                showAlert('warning', 'No valid item lines to post.');
                return;
            }

            ensureSaved().then(postNow);
        });

        // Receipts Logic
        $(document).on('input', '.rv-amount', recomputeReceipts);

        $('#btnAddRV').on('click', function() {
            const row = `
              <div class="d-flex gap-2 align-items-center mb-2 rv-row">
                <select class="form-select rv-account" name="receipt_account_id[]" style="max-width:320px">
                  <option value="">Select account</option>
                </select>
                <input type="text" class="form-control text-end rv-amount" name="receipt_amount[]" placeholder="0.00" style="max-width:160px">
                <button type="button" class="btn btn-outline-danger btn-sm btnRemRV">&times;</button>
              </div>`;
            $('#rvWrapper').append(row);
            loadAccountsInto($('#rvWrapper .rv-account:last'));
        });

        $(document).on('click', '.btnRemRV', function() {
            $(this).closest('.rv-row').remove();
            recomputeReceipts();
        });
        // });


        // --- Customers & Accounts ---
        // We leave accountData here as a helper if available, but parent should ideally provide it.
        const accountData =
            @if (isset($accounts))
                @json($accounts)
            @else
                []
            @endif ;

        function loadAccountsInto($select, customerId) {
            const currentVal = $select.val();
            let options = '<option value="">Select account</option>';
            accountData.forEach(acc => {
                options += `<option value="${acc.id}">${acc.title}</option>`;
            });
            $select.html(options);
            if (currentVal) $select.val(currentVal);
        }

        function recomputeReceipts() {
            let sum = 0;
            $('.rv-amount').each(function() {
                sum += toNum($(this).val());
            });
            $('#receiptsTotal').text(sum.toFixed(2));
            updateGrandTotals();
        }

        // NOTE: Customer Loading Logic (loadCustomersByType, customer change events)
        // is now delegated to the parent view (add_sale / edit_sale) 
        // to avoid conflicts and allow specific behaviors for each mode.

        // Initialize Posted Button State
        refreshPostedState();
    }); // Close $(document).ready

    // --- Barcode Scanner Handler ---
    $(document).on('keypress', '#barcodeScanner', function(e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            const barcode = $(this).val().trim();
            if (!barcode) return;

            const $input = $(this);
            $input.prop('disabled', true);

            $.get('{{ route('products.find_by_barcode') }}', { barcode: barcode }, function(res) {
                $input.prop('disabled', false).val('').focus();
                
                if (res.success) {
                    // Add new row if no empty product row exists, or use the last empty one
                    let $lastRow = $('#salesTableBody tr:last');
                    let $productSelect = $lastRow.find('.product');
                    
                    if ($productSelect.val()) {
                        addNewRow();
                        $lastRow = $('#salesTableBody tr:last');
                        $productSelect = $lastRow.find('.product');
                    }

                    // Trigger Select2 selection
                    const data = res.data;
                    const newOption = new Option(data.name, data.id, true, true);
                    
                    // Attach data to the option so formatSelection/formatProduct can use it if needed
                    // But more importantly, Select2 needs to know about it
                    $productSelect.append(newOption).trigger('change');
                    
                    // Manually trigger the select2:select event or our own change handler
                    $productSelect.trigger({
                        type: 'select2:select',
                        params: { data: data }
                    });

                    showAlert('success', 'Product added: ' + data.name);
                } else {
                    showAlert('error', res.message || 'Product not found');
                }
            }).fail(function() {
                $input.prop('disabled', false).focus();
                showAlert('error', 'Server Error');
            });
        }
    });
</script>
