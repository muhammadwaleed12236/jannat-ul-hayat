@extends('admin_panel.layout.app')
@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

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
    .page-container { max-width: 1200px; margin: 0 auto; padding: 16px; }
    .import-card {
        background: #fff;
        border-radius: var(--radius-md);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    .import-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
    }
    .import-card-header h5 { font-size: 0.95rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
    .import-card-body { padding: 20px; }
    .csv-format-box {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 12px 14px;
        font-size: 0.72rem;
        font-family: 'Courier New', monospace;
        line-height: 1.8;
        overflow-x: auto;
        white-space: nowrap;
        color: var(--text-main);
    }
    .csv-format-box .hl { color: #7c3aed; font-weight: 600; }
    .csv-format-box .hl2 { color: #0891b2; font-weight: 600; }
    .csv-format-box .hl3 { color: #059669; font-weight: 600; }
    .note-item { font-size: 0.8rem; padding: 3px 0; display: flex; align-items: flex-start; gap: 6px; }
    .note-item i { color: var(--primary); margin-top: 3px; font-size: 0.65rem; }
    .btn-primary { background: var(--primary); border-color: var(--primary); }
    .btn-primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
    .btn-outline-primary { color: var(--primary); border-color: var(--primary); }
    .btn-outline-primary:hover { background: var(--primary-light); color: var(--primary); }
    .step-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
    .step-item:last-child { border-bottom: none; }
    .step-num {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--primary); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
    }
    .step-content { font-size: 0.82rem; color: var(--text-main); padding-top: 3px; }
    .step-content strong { display: block; margin-bottom: 2px; }
    .file-upload-area {
        border: 2px dashed #c7d2fe;
        border-radius: 10px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        background: #f8f9ff;
        transition: border-color .2s, background .2s;
    }
    .file-upload-area:hover,
    .file-upload-area.dragover { border-color: var(--primary); background: var(--primary-light); }
    .file-upload-area.has-file { border-style: solid; border-color: #22c55e; background: #f0fdf4; }
    .upload-icon { font-size: 2rem; color: #a5b4fc; margin-bottom: 8px; }
    .result-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-md); padding: 16px; }
    .result-card.error { background: #fef2f2; border-color: #fecaca; }
    #uploadPreviewWrap { max-height: 300px; overflow: auto; }
    #uploadPreviewWrap table { font-size: 0.72rem; }
    #uploadPreviewWrap thead th { position: sticky; top: 0; background: #f8fafc; z-index: 2; }
</style>

<div class="page-container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark" style="font-size:1.1rem">Import Products</h4>
            <small class="text-muted">Bulk import products via CSV upload</small>
        </div>
        <a href="{{ route('product') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:6px;font-size:0.82rem">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" style="font-size:0.82rem">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if (session('import_errors'))
        <div class="alert alert-warning alert-dismissible fade show py-2 mb-3" style="font-size:0.82rem">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('import_errors') }}
            <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" style="font-size:0.82rem">
            <i class="fas fa-times-circle me-1"></i> {{ $errors->first() }}
            <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if (session('error_log_csv'))
        <div class="alert alert-info alert-dismissible fade show py-2 mb-3 d-flex align-items-center justify-content-between" style="font-size:0.82rem">
            <span><i class="fas fa-download me-1"></i> Some rows had errors. Download the error log for details.</span>
            <a href="#" id="downloadErrorLog" class="btn btn-sm btn-outline-danger ms-3" style="border-radius:6px;font-size:0.75rem">
                <i class="fas fa-file-csv me-1"></i> Download Error Log
            </a>
            <button type="button" class="close py-2 ms-2" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">
        {{-- Left Column: Upload Card --}}
        <div class="col-md-7 mb-4">
            <div class="import-card">
                <div class="import-card-header">
                    <h5><i class="fas fa-upload text-primary"></i> Upload Product CSV</h5>
                </div>
                <div class="import-card-body">
                    <form id="importForm" action="{{ route('product.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- File Upload Area --}}
                        <div class="file-upload-area" id="uploadDropZone">
                            <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                            <p class="mb-1 fw-semibold text-dark" style="font-size:0.85rem">Drag & drop your CSV file here</p>
                            <p class="mb-2 text-muted" style="font-size:0.75rem">or click to browse</p>
                            <input type="file" name="csv" id="csvFileInput" accept=".csv,.txt" style="display:none" required>
                            <span id="fileNameDisplay" class="badge bg-light text-muted border px-3 py-2" style="font-size:0.78rem;font-weight:400">
                                <i class="fas fa-file-csv me-1 text-primary"></i> No file chosen
                            </span>
                        </div>

                        {{-- Download Template --}}
                        <div class="mt-2 mb-3">
                            <a href="#" id="downloadTemplateLink" style="font-size:0.8rem;color:var(--primary);text-decoration:none">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>

                        {{-- CSV Format Box --}}
                        <div class="csv-format-box mb-3">
                            <div class="fw-bold mb-1 text-dark" style="font-size:0.75rem">Required CSV Format:</div>
                            <span class="hl">item_code</span>,<span class="hl2">item_name</span>,<span class="hl3">category</span>,subcategory,brand,unit,size_mode,height,width,<span class="hl">pieces_per_box</span>,price_per_m2,purchase_price_per_m2,sale_price_per_box,sale_price_per_piece,purchase_price_per_piece,purchase_price_per_box,alert_qty
                        </div>

                        {{-- Notes --}}
                        <div style="font-size:0.78rem;color:var(--text-main)">
                            <div class="fw-semibold mb-1" style="font-size:0.82rem">Notes:</div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span><strong class="text-primary">Product Code (item_code)</strong> must be unique and not already exist in the system</span></div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span><strong>item_name</strong> is required for every row</span></div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span><strong>Pieces Per Box</strong> must be numeric</span></div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span><strong>Brand</strong> is required and must already exist in the system</span></div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span><strong>Category</strong> will be auto-created if it doesn't exist (leave blank if not needed)</span></div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span><strong>Unit</strong> must match existing system unit (e.g. Piece)</span></div>
                            <div class="note-item"><i class="fas fa-circle"></i> <span>Leave other columns blank if not applicable — system uses defaults</span></div>
                        </div>

                        {{-- Preview Table --}}
                        <div id="previewSection" class="mt-3 d-none">
                            <hr>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-dark" style="font-size:0.82rem">
                                    <i class="fas fa-table text-primary me-1"></i> Preview
                                    <span id="previewCount" class="text-muted fw-normal" style="font-size:0.75rem"></span>
                                </span>
                                <button type="button" id="resetFileBtn" class="btn btn-sm btn-outline-secondary" style="font-size:0.7rem;border-radius:6px">
                                    <i class="fas fa-redo me-1"></i> Change file
                                </button>
                            </div>
                            <div id="uploadPreviewWrap">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead id="previewHead" class="table-light"></thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" id="importBtn" class="btn btn-primary px-4" style="border-radius:6px;font-size:0.85rem" disabled>
                                <i class="fas fa-file-import me-1"></i> Import
                            </button>
                            <a href="#" id="downloadTemplateBtn" class="btn btn-outline-primary px-4" style="border-radius:6px;font-size:0.85rem">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Instructions Card --}}
        <div class="col-md-5 mb-4">
            <div class="import-card">
                <div class="import-card-header">
                    <h5><i class="fas fa-info-circle text-primary"></i> Instructions</h5>
                </div>
                <div class="import-card-body">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-content">
                            <strong>Download the CSV template</strong>
                            Click the "Download Template" button to get the blank CSV with correct headers.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-content">
                            <strong>Fill in product details</strong>
                            Enter Name, Code, Pieces Per Box, Unit, Category, Pricing, and other fields.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-content">
                            <strong>Ensure unique Product Codes</strong>
                            Product Code must be unique and not already exist in the system. Duplicate codes will be skipped.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">4</div>
                        <div class="step-content">
                            <strong>Save as CSV and upload</strong>
                            Save your file as CSV (not Excel) and upload using the drag-and-drop area above.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">5</div>
                        <div class="step-content">
                            <strong>System validates and inserts</strong>
                            The system validates each row, skips invalid ones, and inserts valid products directly into the product listing table. A summary is shown after completion.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function() {
    const dropZone = document.getElementById('uploadDropZone');
    const fileInput = document.getElementById('csvFileInput');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const importBtn = document.getElementById('importBtn');
    const previewSection = document.getElementById('previewSection');
    const previewHead = document.getElementById('previewHead');
    const previewBody = document.getElementById('previewBody');
    const previewCount = document.getElementById('previewCount');
    const resetFileBtn = document.getElementById('resetFileBtn');
    const importForm = document.getElementById('importForm');

    let parsedHeaders = [];
    let parsedRows = [];

    dropZone.addEventListener('click', () => fileInput.click());
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) handleFile(fileInput.files[0]);
    });

    function handleFile(file) {
        if (!file.name.endsWith('.csv') && !file.name.endsWith('.txt')) {
            alert('Please select a CSV or TXT file.');
            return;
        }
        fileNameDisplay.innerHTML = `<i class="fas fa-file-csv me-1 text-primary"></i> ${file.name}`;
        dropZone.classList.add('has-file');
        const reader = new FileReader();
        reader.onload = e => {
            const parsed = parseCSV(e.target.result);
            if (parsed.error) {
                alert(parsed.error);
                return;
            }
            parsedHeaders = parsed.headers;
            parsedRows = parsed.rows;
            renderPreview(parsedHeaders, parsedRows);
            importBtn.disabled = false;
        };
        reader.readAsText(file);
    }

    resetFileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        resetUpload();
    });

    function resetUpload() {
        fileInput.value = '';
        parsedHeaders = [];
        parsedRows = [];
        fileNameDisplay.innerHTML = '<i class="fas fa-file-csv me-1 text-primary"></i> No file chosen';
        dropZone.classList.remove('has-file');
        previewSection.classList.add('d-none');
        importBtn.disabled = true;
    }

    function renderPreview(headers, rows) {
        const show = rows.slice(0, 20);
        let th = '<tr><th>#</th>';
        headers.forEach(h => { th += `<th>${escH(h)}</th>`; });
        th += '</tr>';
        previewHead.innerHTML = th;
        let tb = '';
        show.forEach((row, i) => {
            tb += `<tr><td class="text-muted text-center">${i+1}</td>`;
            headers.forEach(h => { tb += `<td>${escH(row[h] ?? '') || '<span class="text-muted">—</span>'}</td>`; });
            tb += '</tr>';
        });
        previewBody.innerHTML = tb;
        previewCount.textContent = `— ${rows.length} row${rows.length !== 1 ? 's' : ''} found${rows.length > 20 ? ' (showing first 20)' : ''}`;
        previewSection.classList.remove('d-none');
    }

    function parseCSV(text) {
        const lines = text.trim().split(/\r?\n/);
        if (lines.length < 2) return { error: 'CSV must have a header row and at least one data row.' };
        const headers = splitLine(lines[0]);
        if (!headers.includes('item_code') || !headers.includes('item_name'))
            return { error: 'Missing required columns: item_code and item_name must be present.' };
        const rows = [];
        for (let i = 1; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line) continue;
            const cells = splitLine(line);
            const obj = {};
            headers.forEach((h, idx) => { obj[h] = cells[idx] ?? ''; });
            rows.push(obj);
        }
        return { headers, rows };
    }

    function splitLine(line) {
        const result = []; let cur = ''; let inQ = false;
        for (let i = 0; i < line.length; i++) {
            const ch = line[i];
            if (ch === '"') { if (inQ && line[i+1] === '"') { cur += '"'; i++; } else { inQ = !inQ; } }
            else if (ch === ',' && !inQ) { result.push(cur.trim()); cur = ''; }
            else { cur += ch; }
        }
        result.push(cur.trim());
        return result;
    }

    function escH(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // Download Template
    function downloadTemplate() {
        const headers = ['item_code','item_name','category','subcategory','brand','unit','size_mode','height','width','pieces_per_box','price_per_m2','purchase_price_per_m2','sale_price_per_box','sale_price_per_piece','purchase_price_per_piece','purchase_price_per_box','alert_qty'];
                        const sample = ['ITEM-0001','Sample Product','Electronics','Fan','Samsung','Piece','by_size','60','60','4','1500','1000','6000','1500','250','6000','10'];
        const escape = v => { v = String(v ?? ''); return (v.includes(',') || v.includes('"') || v.includes('\n')) ? '"' + v.replace(/"/g,'""') + '"' : v; };
        let out = headers.join(',') + '\n';
        out += headers.map((h, i) => escape(sample[i])).join(',') + '\n';
        const blob = new Blob([out], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'product_import_template.csv';
        a.click();
        URL.revokeObjectURL(url);
    }

    document.getElementById('downloadTemplateLink').addEventListener('click', e => { e.preventDefault(); downloadTemplate(); });
    document.getElementById('downloadTemplateBtn').addEventListener('click', e => { e.preventDefault(); downloadTemplate(); });

    // Download error log
    const errorLogCsv = @json(session('error_log_csv'));
    if (errorLogCsv) {
        document.getElementById('downloadErrorLog').addEventListener('click', function(e) {
            e.preventDefault();
            const blob = new Blob([errorLogCsv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'import_errors_' + new Date().toISOString().slice(0,10) + '.csv';
            a.click();
            URL.revokeObjectURL(url);
        });
    }
})();
</script>
@endsection
