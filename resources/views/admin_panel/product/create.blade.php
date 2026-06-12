@extends('admin_panel.layout.app')

@section('content')
    {{-- 
        SUCCESS: Horizontal Layout Redesign
        Features: 
        - Top Section: Identity (Image + Details side-by-side)
        - Middle Section: Measurements & Stock
        - Bottom Section: Financials & Action
    --}}
    
    {{-- External Resources --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--text-main);
            padding-bottom: 40px;
            min-height: 100vh;
        }

        .page-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 16px;
        }

        /* --- Global Cards --- */
        .section-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 20px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
        }

        .card-header-pro {
            padding: 14px 20px;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title-pro {
            font-size: 0.9rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .card-body-pro {
            padding: 20px;
        }

        /* --- Form Styling --- */
        .form-label-pro {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
            letter-spacing: 0.02em;
        }

        .form-control-pro {
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

        .form-control-pro:focus {
            border-color: #667eea;
            outline: 0;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-select-pro {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        /* --- Section 1: Identity Grid --- */
        .identity-wrapper {
            display: flex;
            gap: 16px;
        }
        
        .image-section {
            width: 200px;
            flex-shrink: 0;
        }

        .details-section {
            flex: 1;
        }

        .img-uploader {
            width: 100%;
            aspect-ratio: 1/1; /* Square for product */
            border: 2px dashed #cbd5e1;
            border-radius: var(--radius-lg);
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.2s;
        }

        .img-uploader:hover {
            border-color: #667eea;
            background: linear-gradient(145deg, #eef2ff 0%, #e0e7ff 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .img-uploader img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Show full product */
            padding: 10px;
        }

        /* --- Section 2: Specs --- */
        .specs-grid {
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 16px;
            align-items: start;
        }

        /* Mode Switcher Horizontal Pills */
        .mode-pills {
            display: flex;
            gap: 4px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: var(--radius-md);
            margin-bottom: 14px;
        }
        .mode-btn-v {
            flex: 1;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            border: none;
            background: transparent;
        }
        .mode-btn-v:hover { color: var(--text-main); }
        .mode-btn-v.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        /* Stats Box */
        .stats-summary-box {
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 14px;
            border: 1px solid var(--border-color);
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .stat-item:last-child { margin-bottom: 0; padding-bottom: 0; border: none; }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); }
        .stat-value { font-size: 1rem; font-weight: 700; color: var(--text-main); }


        /* --- Section 3: Financials --- */
        .financials-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 200px;
            gap: 16px;
        }

        .total-value-display {
            background: #0f172a;
            color: #fff;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 120px;
            max-height: 180px;
            overflow-y: auto;
        }

        .btn-save-floating {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-save-floating:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: #fff;
        }

        /* --- Responsive --- */
        @media (max-width: 991px) {
            .identity-wrapper { flex-direction: column; }
            .image-section { width: 100%; }
            .img-uploader { aspect-ratio: 16/9; }
            .specs-grid { grid-template-columns: 1fr; }
            .financials-grid { grid-template-columns: 1fr; }
            .mode-switcher-vertical { flex-direction: row; overflow-x: auto; }
            .btn-save-floating { width: calc(100% - 32px); justify-content: center; text-align: center; }
        }
    </style>

    <div class="page-container">
        
        {{-- Page Title --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('product') }}" class="btn btn-white border shadow-sm rounded-circle p-0" style="width: 42px; height: 42px; display: grid; place-items: center; transition: all 0.2s;">
                    <i class="fas fa-arrow-left" style="color: #4f46e5;"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0" style="color: #1e293b; font-size: 1.3rem;">Create Product</h4>
                    <small style="color: #94a3b8; font-size: 0.8rem;">Add new item to inventory system</small>
                </div>
            </div>
        </div>

        <form id="productForm" action="{{ route('store-product') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- SECTION 1: IDENTITY --}}
            <div class="section-card" style="overflow: visible;">
                <div class="card-header-pro" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-bottom: none;">
                    <h5 class="card-title-pro" style="color: #fff;"><i class="fas fa-cube"></i> Product Identity</h5>
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: #fff; font-weight: 600;">STEP 1</span>
                </div>
                <div class="card-body-pro" style="padding: 24px;">
                    <div class="identity-wrapper">
                        {{-- Image (Left) --}}
                        <div class="image-section" style="width: 180px;">
                            <input type="file" id="imageInput" name="image" class="d-none" accept="image/*">
                            <div class="img-uploader" onclick="document.getElementById('imageInput').click()" style="border: 2px dashed #c7d2fe; background: linear-gradient(145deg, #f5f7ff 0%, #eef2ff 100%); border-radius: 16px; transition: all 0.3s ease;">
                                <button type="button" id="clearImageBtn" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 d-none rounded-circle" style="width:24px;height:24px;padding:0;z-index: 10;">&times;</button>
                                <img id="preview" class="d-none" style="border-radius: 12px;">
                                <div id="uploadPlaceholder" class="text-center">
                                    <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                        <i class="fas fa-camera" style="color: #fff; font-size: 1.3rem;"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color: #4f46e5; font-size: 0.85rem;">Upload Photo</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">JPG, PNG up to 5MB</small>
                                </div>
                            </div>
                        </div>

                        {{-- Details (Right) --}}
                        <div class="details-section">
                            {{-- Row 1: Name & Barcode --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label-pro" style="color: #4f46e5; font-size: 0.75rem;"><i class="fas fa-tag me-1"></i>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control-pro" name="product_name" required placeholder="e.g. Ceramic Floor Tile 60x60" style="font-size: 0.95rem; font-weight: 600; padding: 12px 16px; border-radius: 12px; border: 2px solid #e2e8f0; transition: all 0.3s;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-pro" style="color: #4f46e5; font-size: 0.75rem;"><i class="fas fa-barcode me-1"></i>Barcode</label>
                                    <div class="input-group" style="border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; transition: all 0.3s;">
                                        <input type="text" class="form-control" id="barcodeInput" name="barcode_path" style="border: none; padding: 12px 16px; font-weight: 600; font-size: 0.95rem; background: #f8fafc;">
                                        <button type="button" class="btn" id="generateBarcodeBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; padding: 0 16px; font-size: 1.1rem;"><i class="fas fa-wand-magic-sparkles"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div style="height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent); margin: 20px 0;"></div>

                            {{-- Row 2: Categorization --}}
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label-pro" style="color: #4f46e5; font-size: 0.75rem;"><i class="fas fa-layer-group me-1"></i>Category <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-1">
                                        <select class="form-select form-control-pro form-select-pro" id="category-dropdown" name="category_id" required style="border-radius: 10px;">
                                            <option value="">Select...</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm" style="background: #eef2ff; color: #4f46e5; border-radius: 10px; font-weight: 700; border: 2px solid #c7d2fe;" data-toggle="modal" data-target="#categoryModal">+</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-pro" style="color: #4f46e5; font-size: 0.75rem;"><i class="fas fa-folder-open me-1"></i>Sub Category</label>
                                    <div class="d-flex gap-1">
                                        <select class="form-select form-control-pro form-select-pro" id="subcategory-dropdown" name="sub_category_id" style="border-radius: 10px;">
                                            <option value="">Select...</option>
                                        </select>
                                        <button type="button" class="btn btn-sm" style="background: #eef2ff; color: #4f46e5; border-radius: 10px; font-weight: 700; border: 2px solid #c7d2fe;" data-toggle="modal" data-target="#subcategoryModal">+</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-pro" style="color: #4f46e5; font-size: 0.75rem;"><i class="fas fa-award me-1"></i>Brand</label>
                                    <select class="form-select form-control-pro form-select-pro" id="brand-dropdown" name="brand_id" style="border-radius: 10px;">
                                        <option value="">Select...</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-pro" style="color: #4f46e5; font-size: 0.75rem;"><i class="fas fa-ruler me-1"></i>Unit</label>
                                    <select class="form-select form-control-pro form-select-pro" id="unit-dropdown" name="unit" style="border-radius: 10px;">
                                        <option value="">Select...</option>
                                        @foreach ($units as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: MEASUREMENTS & STOCK --}}
            <div class="section-card">
                <div class="card-header-pro" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                    <h5 class="card-title-pro" style="color: #fff;"><i class="fas fa-ruler-combined"></i> Dimensions & Stock</h5>
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: #fff; font-weight: 600;">STEP 2</span>
                </div>
                <div class="card-body-pro">
                    <div class="specs-grid">
                        
                        {{-- Left: Mode Pills + Inputs --}}
                        <div>
                            <div class="mode-pills">
                                <input type="radio" class="d-none" name="size_mode" id="mode_carton" value="by_cartons">
                                <label class="mode-btn-v" for="mode_carton" onclick="selectMode(this)">By Carton</label>
                                <input type="radio" class="d-none" name="size_mode" id="mode_piece" value="by_pieces" checked>
                                <label class="mode-btn-v active" for="mode_piece" onclick="selectMode(this)">By Piece</label>
                            </div>

                            <div class="specs-inputs">
                                {{-- Piece Only (default) --}}
                                <div class="group-piece-only">
                                    <div class="row g-2">
                                        <div class="col-8">
                                            <label class="form-label-pro">Total Quantity</label>
                                            <input type="number" class="form-control-pro border-primary text-primary fw-bold" name="piece_quantity" id="piece_quantity" placeholder="0">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label-pro">Low Stock Alert</label>
                                            <input type="number" class="form-control-pro border-warning text-warning fw-bold" name="alert_qty" placeholder="0" min="0">
                                        </div>
                                    </div>
                                </div>

                                {{-- By Carton Inputs --}}
                                <div class="group-by-carton d-none">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label-pro">Pcs / Box</label>
                                            <input type="number" class="form-control-pro bg-light" name="pieces_per_box" id="pieces_per_box" placeholder="0">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label-pro">In-Stock Boxes</label>
                                            <input type="number" class="form-control-pro border-primary text-primary fw-bold" name="boxes_quantity" id="boxes_quantity" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label-pro text-warning">Loose Pieces</label>
                                            <input type="number" class="form-control-pro border-warning" name="loose_pieces" id="loose_pieces">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label-pro">Low Stock Alert</label>
                                            <input type="number" class="form-control-pro border-warning text-warning fw-bold" name="alert_qty" placeholder="0" min="0">
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Compact Stats --}}
                        <div class="stats-summary-box">
                            <h6 class="text-uppercase text-muted fw-bold mb-2 small">Stock Summary</h6>
                            <div class="stat-item">
                                <span class="stat-label" id="stock_unit_label">Total Pieces</span>
                                <span class="stat-value" id="total_stock_display">0</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- SECTION 3: FINANCIALS --}}
            <div class="section-card">
                <div class="card-header-pro" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <h5 class="card-title-pro" style="color: #fff;"><i class="fas fa-wallet"></i> Pricing & Value</h5>
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: #fff; font-weight: 600;">STEP 3</span>
                </div>
                <div class="card-body-pro">
                    <div class="financials-grid">

                        {{-- Col 1: Pricing Inputs (Sale + Purchase side by side) --}}
                        <div class="pricing-inputs">
                            <div class="group-price-unit">
                                <h6 class="form-label-pro text-primary mb-2">Rate per Unit</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label-pro text-success">Sale <span class="unit-label text-muted fw-normal">(pc)</span></label>
                                        <input type="number" class="form-control-pro fw-bold text-success" name="sale_price_per_box" id="sale_price_per_box" step="0.01" placeholder="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-pro text-danger">Cost / Purchase <span class="unit-label text-muted fw-normal">(pc)</span></label>
                                        <input type="number" class="form-control-pro text-muted" name="purchase_price_per_piece" id="purchase_price_per_piece" step="0.01" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-top">
                                <h6 class="form-label-pro text-primary mb-2">Discounts</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label-pro">Sale Disc %</label>
                                        <input type="number" class="form-control-pro" name="sale_discount_percent" step="0.01" value="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-pro">Purch Disc %</label>
                                        <input type="number" class="form-control-pro" name="purchase_discount_percent" step="0.01" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Col 2: Calculated Unit Prices (compact) --}}
                        <div class="calculated-info" id="calc_unit_prices">
                            <h6 class="form-label-pro text-primary mb-2">Unit Prices</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-1 border rounded bg-light text-center">
                                        <small class="d-block text-muted" style="font-size:0.65rem">Sale / Pc</small>
                                        <strong class="text-success small" id="calc_sale_piece">0.00</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-1 border rounded bg-light text-center">
                                        <small class="d-block text-muted" style="font-size:0.65rem">Cost / Pc</small>
                                        <span class="text-dark small" id="calc_purch_piece">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Col 3: Compact Estimated Stock Value with scroll --}}
                        <div class="total-section">
                            <div class="total-value-display">
                                <small class="text-uppercase opacity-75" style="font-size:0.6rem;letter-spacing:1px">Stock Value</small>
                                <div class="mt-1">
                                    <small class="opacity-75" style="font-size:0.7rem">PKR</small>
                                    <div class="fw-bold" style="font-size:1.1rem;line-height:1.2" id="sale_total_display">0.00</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Floating Save Button --}}
            <button type="submit" class="btn-save-floating">
                <i class="las la-check-circle fs-4"></i>
                <span>SAVE PRODUCT</span>
            </button>
        </form>

        {{-- Modals --}}
        {{-- Modals --}}
        <div id="categoryModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
                    <form action="{{ route('store.category') }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h6 class="modal-title fw-bold">New Category</h6>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="page" value="product_page">
                            <div class="mb-3">
                                <label class="form-label-pro">Category Name</label>
                                <input type="text" name="name" class="form-control-pro" required placeholder="e.g. Ceramics">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">Create Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="subcategoryModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-md);">
                    <form action="{{ route('store.subcategory') }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h6 class="modal-title fw-bold">New Subcategory</h6>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="page" value="product_page">
                            <div class="mb-3">
                                <label class="form-label-pro">Parent Category</label>
                                <select name="category_id" class="form-select form-control-pro">
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label-pro">Name</label>
                                <input type="text" name="name" class="form-control-pro" required placeholder="e.g. Floor Tiles">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">Create Subcategory</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        function selectMode(labelEl) {
            document.querySelectorAll('.mode-btn-v').forEach(btn => btn.classList.remove('active'));
            labelEl.classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- UI Elements ---
            const form = document.getElementById('productForm');
            const modeRadios = document.querySelectorAll('input[name="size_mode"]');

            // Containers
            const grpByCarton = document.querySelector('.group-by-carton');
            const grpPieceOnly = document.querySelector('.group-piece-only');
            const stockLabel = document.getElementById('stock_unit_label');

            // --- Logic Update Mode ---
            function updateMode() {
                const modeEl = document.querySelector('input[name="size_mode"]:checked');
                if(!modeEl) return;
                const mode = modeEl.value;

                document.querySelectorAll('.mode-btn-v').forEach(btn => btn.classList.remove('active'));
                const labelFor = document.querySelector(`label[for="${modeEl.id}"]`);
                if(labelFor) labelFor.classList.add('active');

                if (grpByCarton) grpByCarton.classList.add('d-none');
                if (grpPieceOnly) grpPieceOnly.classList.add('d-none');

                if (mode === 'by_cartons') {
                    if (grpByCarton) grpByCarton.classList.remove('d-none');
                    if (stockLabel) stockLabel.innerText = "Total Pieces";
                    setRequired(['pieces_per_box', 'boxes_quantity', 'loose_pieces', 'sale_price_per_box', 'purchase_price_per_piece'], true);
                    setRequired(['piece_quantity'], false);
                } else {
                    if (grpPieceOnly) grpPieceOnly.classList.remove('d-none');
                    if (stockLabel) stockLabel.innerText = "Total Pieces";
                    setRequired(['piece_quantity', 'sale_price_per_box', 'purchase_price_per_piece'], true);
                    setRequired(['pieces_per_box', 'boxes_quantity', 'loose_pieces'], false);
                }

                calculate();
            }

            function calculate() {
                const modeEl = document.querySelector('input[name="size_mode"]:checked');
                if(!modeEl) return;
                const mode = modeEl.value;

                const v = (id) => parseFloat(document.getElementById(id)?.value) || 0;
                let stock = 0;
                let costVal = 0;
                let salePc = 0;
                let costPc = 0;

                if (mode === 'by_cartons') {
                    stock = (v('pieces_per_box') * v('boxes_quantity')) + v('loose_pieces');
                    costVal = stock * v('purchase_price_per_piece');
                    salePc = v('sale_price_per_box');
                    costPc = v('purchase_price_per_piece');
                } else {
                    stock = v('piece_quantity');
                    costVal = stock * v('purchase_price_per_piece');
                    salePc = v('sale_price_per_box');
                    costPc = v('purchase_price_per_piece');
                }

                setText('total_stock_display', stock);
                setText('sale_total_display', costVal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                setText('calc_sale_piece', salePc.toFixed(2));
                setText('calc_purch_piece', costPc.toFixed(2));
            }

            function setRequired(ids, isReq) {
                ids.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) isReq ? el.setAttribute('required', 'required') : el.removeAttribute('required');
                });
            }

            function setText(id, val) {
                const el = document.getElementById(id);
                if (el) el.innerText = val;
            }

            // Events
            modeRadios.forEach(r => r.addEventListener('change', function() {
                updateMode();
            }));
            form.querySelectorAll('input').forEach(i => i.addEventListener('input', calculate));

            updateMode();

            // Image Handler
            const imgInput = document.getElementById('imageInput');
            const preview = document.getElementById('preview');
            const ph = document.getElementById('uploadPlaceholder');
            const clr = document.getElementById('clearImageBtn');

            imgInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const r = new FileReader();
                    r.onload = (e) => {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                        ph.classList.add('d-none');
                        clr.classList.remove('d-none');
                    };
                    r.readAsDataURL(this.files[0]);
                }
            });

            clr.addEventListener('click', (e) => {
                e.stopPropagation();
                imgInput.value = '';
                preview.classList.add('d-none');
                ph.classList.remove('d-none');
                clr.classList.add('d-none');
            });

            // AJAX Submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.querySelector('.btn-save-floating');
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';
                btn.disabled = true;

                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
                    body: formData
                })
                .then(r => r.json().then(data => ({status: r.status, body: data})))
                .then(({status, body}) => {
                    if (status === 200 || body.status === 'success') {
                         Swal.fire({
                            icon: 'success', title: 'Saved!',
                            text: 'Product created successfully', timer: 1500, showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        const msg = body.errors ? Object.values(body.errors).flat().join('<br>') : (body.message || 'Error');
                        Swal.fire({icon: 'error', title: 'Error', html: msg});
                    }
                })
                .catch(err => Swal.fire({icon: 'error', title: 'Error', text: 'Server Error'}))
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
            });

            // Barcode
            const barIn = document.getElementById('barcodeInput');
            const barBtn = document.getElementById('generateBarcodeBtn');
            const barcodeUrl = '{{ route('generate-barcode-image') }}';
            
            const ajaxHeaders = { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } };
            if (!barIn.value) fetch(barcodeUrl, ajaxHeaders).then(r => r.json()).then(d => barIn.value = d.barcode_number);
            barBtn.addEventListener('click', () => fetch(barcodeUrl, ajaxHeaders).then(r => r.json()).then(d => barIn.value = d.barcode_number));

            // Select2 for category/subcategory/brand
            $('#category-dropdown').select2({ placeholder: "Select Category", allowClear: true, width: '100%' });
            $('#subcategory-dropdown').select2({ placeholder: "Select Subcategory", allowClear: true, width: '100%' });
            $('#brand-dropdown').select2({ placeholder: "Select Brand", allowClear: true, width: '100%' });
            $('#unit-dropdown').select2({ placeholder: "Select Unit", allowClear: true, width: '100%' });

            $('#category-dropdown').on('change', function() {
                var cid = $(this).val();
                if (cid) {
                    $.get('/get-subcategories/' + cid, function(d) {
                        var sub = $('#subcategory-dropdown');
                        sub.empty().append('<option value="">Select...</option>');
                        $.each(d, function(_, v) {
                            sub.append('<option value="' + v.id + '">' + v.name + '</option>');
                        });
                        sub.val('').trigger('change');
                    });
                }
            });
        });
    </script>
@endsection
