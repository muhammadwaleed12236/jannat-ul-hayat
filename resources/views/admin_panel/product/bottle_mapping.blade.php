@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-flask me-2"></i> Attar & Bottle Mapping</h4>
                    <span class="badge bg-light text-primary">Manage Inventory Links</span>
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('bottle-mappings.bulk-assign') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <!-- Step 1: Select Category & Subcategory -->
                            <div class="col-md-3">
                                <div class="card h-100 border shadow-sm" style="border-top: 4px solid #0d6efd !important;">
                                    <div class="card-body">
                                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">1</span> 
                                            Source Products
                                        </h6>
                                        <div class="p-3 border rounded bg-white mb-3">
                                            <label class="form-label small fw-bold text-muted">Category</label>
                                            <select id="category_id" class="form-select select2" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="p-3 border rounded bg-white">
                                            <label class="form-label small fw-bold text-muted">Sub Category</label>
                                            <select id="sub_category_id" class="form-select select2" required>
                                                <option value="">Select Sub Category</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Select Products -->
                            <div class="col-md-3">
                                <div class="card h-100 border shadow-sm" style="border-top: 4px solid #0d6efd !important;">
                                    <div class="card-body">
                                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                            <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">2</span> 
                                            Select Items
                                        </h6>
                                        <div class="mb-2 d-flex justify-content-between align-items-center px-1">
                                            <label class="form-label mb-0 small fw-bold text-muted">Products List</label>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="checkbox" id="check_all">
                                                <label class="form-check-label small fw-bold" for="check_all" style="font-size: 0.75rem;">All</label>
                                            </div>
                                        </div>
                                        <div id="product_list" class="border rounded p-3 bg-white" style="height: 200px; overflow-y: auto;">
                                            <p class="text-muted small text-center my-4">Select a sub-category first</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Define Rules (ML Slabs) -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-sm" style="border-top: 4px solid #0d6efd !important;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-primary fw-bold mb-0 d-flex align-items-center">
                                                <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">3</span> 
                                                Define Rules (ML Slabs)
                                            </h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnAddSlab">
                                                <i class="fas fa-plus me-1"></i> Add More
                                            </button>
                                        </div>
                                        
                                        <div class="border rounded bg-white mb-3">
                                            <table class="table table-sm align-middle mb-0" id="slabTable">
                                                <thead class="bg-light">
                                                    <tr style="font-size: 0.75rem; text-transform: uppercase; color: #6c757d;">
                                                        <th class="px-3 py-2" style="width: 80px;">Min (ML)</th>
                                                        <th class="px-3 py-2" style="width: 80px;">Max (ML)</th>
                                                        <th class="px-3 py-2">Link Bottle</th>
                                                        <th class="px-3 py-2 text-center" style="width: 50px;">-</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="slabTableBody">
                                                    <tr class="slab-row">
                                                        <td class="p-2">
                                                            <input type="number" step="0.01" name="min_qty[]" class="form-control form-control-sm text-center min-qty-input" value="1" required>
                                                        </td>
                                                        <td class="p-2">
                                                            <input type="number" step="0.01" name="max_qty[]" class="form-control form-control-sm text-center max-qty-input" placeholder="6" required>
                                                        </td>
                                                        <td class="p-2">
                                                            <select name="bottle_product_id[]" class="form-select form-select-sm select2-dynamic" required>
                                                                <option value="">Select Bottle</option>
                                                                @foreach($bottleProducts as $bottle)
                                                                    <option value="{{ $bottle->id }}">{{ $bottle->item_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="p-2 text-center">
                                                            <button type="button" class="btn btn-link text-danger p-0 btnRemSlab" tabindex="-1"><i class="fas fa-times-circle"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                            <i class="fas fa-check-circle me-1"></i> Confirm & Save All Mappings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Mappings Table -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-dark fw-bold">Active Mapping Rules</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product (Attar)</th>
                                    <th>ML Range (Slab)</th>
                                    <th>Linked Bottle</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mappings as $mapping)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $mapping->product->item_name }}</div>
                                        <small class="text-muted">{{ $mapping->product->item_code }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ number_format($mapping->min_qty, 1) }} - {{ number_format($mapping->max_qty, 1) }} ML
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bold">{{ $mapping->bottleProduct->item_name }}</div>
                                        <small class="text-muted">Code: {{ $mapping->bottleProduct->item_code }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('bottle-mappings.delete', $mapping->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this mapping?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle me-1"></i> No mapping rules defined yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
$(document).ready(function() {
    // Initialize Select2 if available
    if($.fn.select2) {
        $('.select2, .select2-dynamic').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('body')
        });
    }

    // Category -> Subcategory
    $('#category_id').on('change', function() {
        const catId = $(this).val();
        if(catId) {
            $.get('{{ url("get-subcategories") }}/' + catId, function(data) {
                let options = '<option value="">Select Sub Category</option>';
                data.forEach(sub => {
                    options += `<option value="${sub.id}">${sub.name}</option>`;
                });
                $('#sub_category_id').html(options);
                $('#product_list').html('<p class="text-muted small text-center my-4">Select a sub-category to load products</p>');
            });
        }
    });

    // Subcategory -> Products
    $('#sub_category_id').on('change', function() {
        const subId = $(this).val();
        if(subId) {
            $('#product_list').html('<div class="text-center my-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</div>');
            $.get('{{ url("get-products-by-subcategory") }}/' + subId, function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(prod => {
                        html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input product-check" type="checkbox" name="product_ids[]" value="${prod.id}" id="prod_${prod.id}">
                                <label class="form-check-label small" for="prod_${prod.id}">
                                    ${prod.item_name} <span class="text-muted">(${prod.item_code})</span>
                                </label>
                            </div>
                        `;
                    });
                } else {
                    html = '<p class="text-muted small text-center my-4">No products found in this sub-category</p>';
                }
                $('#product_list').html(html);
            });
        }
    });

    // --- Dynamic Slab Logic ---
    function initDynamicSelect2($el) {
        if($.fn.select2) {
            $el.select2({
                theme: 'bootstrap-5',
                dropdownParent: $('body')
            });
        }
    }

    $('#btnAddSlab').on('click', function() {
        const $lastRow = $('#slabTableBody tr:last');
        const lastMax = parseFloat($lastRow.find('.max-qty-input').val()) || 0;
        const nextMin = lastMax + 1;

        const newRow = `
            <tr class="slab-row">
                <td class="p-2">
                    <input type="number" step="0.01" name="min_qty[]" class="form-control form-control-sm text-center min-qty-input" value="${nextMin}" required>
                </td>
                <td class="p-2">
                    <input type="number" step="0.01" name="max_qty[]" class="form-control form-control-sm text-center max-qty-input" placeholder="${nextMin + 5}" required>
                </td>
                <td class="p-2">
                    <select name="bottle_product_id[]" class="form-select form-select-sm select2-dynamic" required>
                        <option value="">Select Bottle</option>
                        @foreach($bottleProducts as $bottle)
                            <option value="{{ $bottle->id }}">{{ $bottle->item_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="p-2 text-center">
                    <button type="button" class="btn btn-link text-danger p-0 btnRemSlab" tabindex="-1"><i class="fas fa-times-circle"></i></button>
                </td>
            </tr>`;
        
        const $newRow = $(newRow);
        $('#slabTableBody').append($newRow);
        initDynamicSelect2($newRow.find('.select2-dynamic'));
    });

    $(document).on('click', '.btnRemSlab', function() {
        if($('#slabTableBody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert("At least one slab is required.");
        }
    });

    // Auto-update next min when max changes
    $(document).on('input', '.max-qty-input', function() {
        const $row = $(this).closest('tr');
        const $nextRow = $row.next('tr');
        if($nextRow.length) {
            const currentMax = parseFloat($(this).val()) || 0;
            $nextRow.find('.min-qty-input').val(currentMax + 1);
        }
    });

    // Check All functionality
    $('#check_all').on('change', function() {
        $('.product-check').prop('checked', this.checked);
    });
});
</script>
<style>
    .card { transition: transform 0.2s ease; }
    .card:hover { transform: translateY(-2px); }
    .form-select, .form-control { border-radius: 8px; border-color: #dee2e6; }
    .form-select:focus, .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); }
    .badge { border-radius: 6px; }
    #product_list::-webkit-scrollbar { width: 6px; }
    #product_list::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    #product_list::-webkit-scrollbar-track { background: #f7fafc; }
    .select2-container { width: 100% !important; }
    .select2-container--bootstrap-5 .select2-selection { border-radius: 8px; }
</style>
@endsection
@endsection
