<!DOCTYPE html>
<html>
<head>
    <title>Sale Barcodes - {{ $sale->invoice_no }}</title>
    <style>
        @page {
            margin: 0;
            size: 37mm 28mm;
        }
        body {
            font-family: 'Arial Black', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }
        .container { 
            display: flex;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }
        .label-wrapper {
            position: relative;
            margin: 10px;
        }
        .label {
            width: 37mm;
            height: 28mm;
            padding: 2px;
            text-align: center;
            overflow: hidden;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* border: 1px dashed #ccc; */
        }
        .barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        @media print {
            .no-print, .selection-checkbox {
                display: none !important;
            }
            .label {
                border: none !important;
                margin: 0 !important;
                background: white !important;
                page-break-after: always;
            }
            .label-wrapper:not(.selected) {
                display: none !important;
            }
            .label-wrapper {
                margin: 0 !important;
                border: none !important;
                background: white !important;
            }
            .label-wrapper.selected .label {
                border: none !important;
                background: white !important;
            }
            body {
                background: white !important;
            }
        }
        .no-print {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .btn-print {
            padding: 8px 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 2px;
        }
        .selection-checkbox {
            position: absolute;
            top: -5px;
            left: -5px;
            z-index: 10;
            width: 18px;
            height: 18px;
        }
        .label-wrapper.selected .label {
            border: 1px solid #28a745;
            background: #f0fff0;
        }
    </style>
</head>
<body>

<div class="no-print">
    <div style="margin-bottom: 8px;">
        <button class="btn-print" onclick="window.print()">Print Selected</button>
        <button class="btn-print" style="background: #6c757d;" onclick="toggleAll(true)">Select All</button>
        <button class="btn-print" style="background: #dc3545;" onclick="toggleAll(false)">Deselect All</button>
    </div>
    <div style="font-size: 11px; color: #666;">Uncheck to skip specific labels</div>
</div>

<div class="container">
    @foreach($sale->items as $item)
        @php
            $product = $item->product;
            $totalCount = $sale->items->count();
        @endphp
        @if($product)
        <div class="label-wrapper selected" id="wrapper-{{ $loop->index }}">
            <input type="checkbox" checked class="selection-checkbox" onclick="toggleSelection({{ $loop->index }})">
            
            <div class="label" style="padding: 2px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; box-sizing: border-box;">
                <div style="font-size: 10px; text-transform: uppercase; line-height: 1.2; width: 100%;">JANNAT UL HAYAT</div>
                
                @php
                    $barcodeValue = $product->barcode_path ?: $product->item_code;
                @endphp

                <div style="font-weight: 700; font-size: 9px; margin-top: 2px; width: 100%;">
                  0332-2691604  &nbsp; {{ \Carbon\Carbon::parse($product->created_at)->format('d/m/y') }}
                </div>

                <div style="font-weight: 900; font-size: 13px; width: 100%; word-wrap: break-word; line-height: 1.2;">
                    {{ $product->item_name }}
                </div>

                <div class="barcode" style="width: 100%; display: flex; justify-content: center;">
                    {!! DNS1D::getBarcodeSVG($barcodeValue, 'C128', 0.9, 38) !!}
                </div>
            </div>

               
            </div>
        </div>
        @endif
    @endforeach
</div>

<script>
    function toggleSelection(index) {
        const wrapper = document.getElementById('wrapper-' + index);
        wrapper.classList.toggle('selected');
    }

    function toggleAll(status) {
        const wrappers = document.querySelectorAll('.label-wrapper');
        const checkboxes = document.querySelectorAll('.selection-checkbox');
        wrappers.forEach(w => {
            if (status) w.classList.add('selected');
            else w.classList.remove('selected');
        });
        checkboxes.forEach(c => c.checked = status);
    }
</script>
</body>
</html>
