@extends('admin_panel.layout.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">

<style>
:root {
    --primary: #4f46e5;
    --primary-hover: #4338ca;
    --primary-light: #eef2ff;
    --bg-body: #f1f5f9;
    --bg-card: #ffffff;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --radius-md: 10px;
    --radius-lg: 16px;
}
body {
    font-family: 'Inter', sans-serif;
    background-color: var(--bg-body);
    color: var(--text-main);
}
.page-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 16px;
}
.section-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.06);
    border: 1px solid var(--border-color);
    margin-bottom: 16px;
    overflow: hidden;
}
.card-header-pro {
    padding: 12px 20px;
    border-bottom: 1px solid var(--border-color);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card-title-pro {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.card-body-pro {
    padding: 20px;
}
.form-label-pro {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 4px;
    letter-spacing: 0.02em;
}
.form-control-pro, .form-select:not(.select2-hidden-accessible) {
    display: block;
    width: 100%;
    padding: 8px 12px;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-main);
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.form-control-pro:focus, .form-select:focus {
    border-color: var(--primary);
    outline: 0;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.step-badge {
    width: 26px;
    height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
.step-card {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    height: 100%;
    background: #fff;
}
.step-card-header {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fafbfc;
    border-radius: var(--radius-md) var(--radius-md) 0 0;
}
.step-card-body {
    padding: 16px;
}
.product-list-box {
    height: 200px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 12px;
    background: #fafbfc;
}
.product-list-box::-webkit-scrollbar { width: 6px; }
.product-list-box::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
.product-list-box::-webkit-scrollbar-track { background: transparent; }
.table-pro {
    font-size: 0.85rem;
    margin-bottom: 0;
}
.table-pro th {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--text-muted);
    font-weight: 600;
    padding: 10px 12px;
    border-bottom: 1px solid var(--border-color);
    background: #f8fafc;
}
.table-pro td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}
.table-pro tr:last-child td { border-bottom: none; }
.btn-pro-primary {
    background: var(--primary);
    color: #fff;
    border: none;
    padding: 10px 24px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    transition: all 0.2s;
}
.btn-pro-primary:hover {
    background: var(--primary-hover);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
.btn-pro-outline {
    background: transparent;
    color: var(--primary);
    border: 1px solid var(--border-color);
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-pro-outline:hover {
    border-color: var(--primary);
    background: var(--primary-light);
}
</style>

<div class="page-container">
    {{-- Page Title --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('product') }}" class="btn btn-white border shadow-sm rounded-circle p-0" style="width: 40px; height: 40px; display: grid; place-items: center;">
                <i class="las la-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Attar & Bottle Mapping</h4>
                <small class="text-muted">Define bottle linking rules for attar products</small>
            </div>
        </div>
    </div>

    <form action="{{ route('bottle-mappings.bulk-assign') }}" method="POST">
        @csrf
        <div class="row g-3">
            {{-- Step 1: Source Products --}}
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-card-header">
                        <span class="step-badge">1</span>
                        <span class="fw-bold" style="font-size:0.85rem">Source Products</span>
                    </div>
                    <div class="step-card-body">
                        <div class="mb-3">
                            <label class="form-label-pro">Category</label>
                            <select id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label-pro">Sub Category</label>
                            <select id="sub_category_id" class="form-select" required>
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Select Items --}}
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-card-header">
                        <span class="step-badge">2</span>
                        <span class="fw-bold" style="font-size:0.85rem">Select Items</span>
                    </div>
                    <div class="step-card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label-pro mb-0">Products List</label>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" id="check_all" style="cursor:pointer">
                                <label class="form-check-label" for="check_all" style="font-size:0.75rem;cursor:pointer">All</label>
                            </div>
                        </div>
                        <div id="product_list" class="product-list-box">
                            <p class="text-muted small text-center mt-5">Select a sub-category first</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Define Rules --}}
            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-card-header">
                        <span class="step-badge">3</span>
                        <span class="fw-bold" style="font-size:0.85rem">Define Rules (ML Slabs)</span>
                        <button type="button" class="btn btn-pro-outline ms-auto btn-sm" id="btnAddSlab">
                            <i class="las la-plus"></i> Add
                        </button>
                    </div>
                    <div class="step-card-body">
                        <div class="border rounded" style="border-color:var(--border-color);overflow:hidden">
                            <table class="table-pro w-100" id="slabTable">
                                <thead>
                                    <tr>
                                        <th style="width:80px">Min (ML)</th>
                                        <th style="width:80px">Max (ML)</th>
                                        <th>Link Bottle</th>
                                        <th style="width:40px"></th>
                                    </tr>
                                </thead>
                                <tbody id="slabTableBody">
                                    <tr class="slab-row">
                                        <td>
                                            <input type="number" step="0.01" name="min_qty[]" class="form-control form-control-sm text-center min-qty-input" value="1" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="max_qty[]" class="form-control form-control-sm text-center max-qty-input" placeholder="6" required>
                                        </td>
                                        <td>
                                            <select name="bottle_product_id[]" class="form-select form-select-sm select2-dynamic" required>
                                                <option value="">Select Bottle</option>
                                                @foreach($bottleProducts as $bottle)
                                                    <option value="{{ $bottle->id }}">{{ $bottle->item_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-link text-danger p-0 btnRemSlab" tabindex="-1" style="font-size:1.1rem"><i class="las la-times-circle"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-pro-primary w-100 mt-3">
                            <i class="las la-check-circle"></i> Confirm & Save All Mappings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Active Mappings --}}
    <div class="section-card mt-3">
        <div class="card-header-pro">
            <h5 class="card-title-pro"><i class="las la-link text-primary"></i> Active Mapping Rules</h5>
        </div>
        <div class="card-body-pro p-0">
            <table class="table-pro w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product (Attar)</th>
                        <th>ML Range</th>
                        <th>Linked Bottle</th>
                        <th>Status</th>
                        <th style="width:60px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mappings as $mapping)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold" style="font-size:0.9rem">{{ $mapping->product->item_name }}</div>
                            <small class="text-muted">{{ $mapping->product->item_code }}</small>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:0.75rem">
                                {{ number_format($mapping->min_qty, 1) }} – {{ number_format($mapping->max_qty, 1) }} ML
                            </span>
                        </td>
                        <td>
                            <div style="font-size:0.9rem">{{ $mapping->bottleProduct->item_name }}</div>
                            <small class="text-muted">Code: {{ $mapping->bottleProduct->item_code }}</small>
                        </td>
                        <td>
                            <span class="badge" style="background:#dcfce7;color:#166534;font-size:0.75rem">Active</span>
                        </td>
                        <td>
                            <a href="{{ route('bottle-mappings.delete', $mapping->id) }}" class="btn btn-sm" style="color:#ef4444" onclick="return confirm('Remove this mapping?')">
                                <i class="las la-trash-alt" style="font-size:1.1rem"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="las la-info-circle me-1"></i> No mapping rules defined yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('.select2-dynamic').select2({ theme: 'bootstrap-5', dropdownParent: $('body') });

    $('#category_id').on('change', function() {
        const catId = $(this).val();
        if(catId) {
            $.get('{{ url("get-subcategories") }}/' + catId, function(data) {
                let options = '<option value="">Select Sub Category</option>';
                data.forEach(sub => {
                    options += `<option value="${sub.id}">${sub.name}</option>`;
                });
                $('#sub_category_id').html(options);
                $('#product_list').html('<p class="text-muted small text-center mt-5">Select a sub-category to load products</p>');
            });
        }
    });

    $('#sub_category_id').on('change', function() {
        const subId = $(this).val();
        if(subId) {
            $('#product_list').html('<div class="text-center mt-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div><p class="text-muted small mt-2">Loading...</p></div>');
            $.get('{{ url("get-products-by-subcategory") }}/' + subId, function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(prod => {
                        html += `
                            <div class="form-check mb-1">
                                <input class="form-check-input product-check" type="checkbox" name="product_ids[]" value="${prod.id}" id="prod_${prod.id}">
                                <label class="form-check-label" for="prod_${prod.id}" style="font-size:0.85rem;cursor:pointer">
                                    ${prod.item_name} <span class="text-muted">(${prod.item_code})</span>
                                </label>
                            </div>`;
                    });
                } else {
                    html = '<p class="text-muted small text-center mt-5">No products found</p>';
                }
                $('#product_list').html(html);
            });
        }
    });

    function initDynamicSelect2($el) {
        $el.select2({ theme: 'bootstrap-5', dropdownParent: $('body') });
    }

    $('#btnAddSlab').on('click', function() {
        const $lastRow = $('#slabTableBody tr:last');
        const lastMax = parseFloat($lastRow.find('.max-qty-input').val()) || 0;
        const nextMin = lastMax + 1;
        const newRow = `
            <tr class="slab-row">
                <td><input type="number" step="0.01" name="min_qty[]" class="form-control form-control-sm text-center min-qty-input" value="${nextMin}" required></td>
                <td><input type="number" step="0.01" name="max_qty[]" class="form-control form-control-sm text-center max-qty-input" placeholder="${nextMin + 5}" required></td>
                <td>
                    <select name="bottle_product_id[]" class="form-select form-select-sm select2-dynamic" required>
                        <option value="">Select Bottle</option>
                        @foreach($bottleProducts as $bottle)
                            <option value="{{ $bottle->id }}">{{ $bottle->item_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger p-0 btnRemSlab" tabindex="-1" style="font-size:1.1rem"><i class="las la-times-circle"></i></button>
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

    $(document).on('input', '.max-qty-input', function() {
        const $row = $(this).closest('tr');
        const $nextRow = $row.next('tr');
        if($nextRow.length) {
            const currentMax = parseFloat($(this).val()) || 0;
            $nextRow.find('.min-qty-input').val(currentMax + 1);
        }
    });

    $('#check_all').on('change', function() {
        $('.product-check').prop('checked', this.checked);
    });
});
</script>
<style>
.select2-container { width: 100% !important; }
.select2-container--bootstrap-5 .select2-selection { border-radius: 8px; border-color: var(--border-color); }
.select2-container--bootstrap-5 .select2-selection--single { height: 38px; padding: 4px 8px; }
.form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
</style>
@endsection
