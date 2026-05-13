<!DOCTYPE html>
<html>
<head>
    <title>Product Barcode</title>
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
        .label {
            width: 37mm;
            height: 28mm;
            padding: 3px;
            margin: 0 auto;
            text-align: center;
            overflow: hidden;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            page-break-after: always;
        }
        .barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        @media print {
            body {
                height: auto;
                background: white !important;
            }
            .label {
                border: none !important;
                background: white !important;
            }
        }
    </style>
</head>
<body>

<div class="label">
    <div style="font-weight: 900; font-size: 14px; margin-bottom: 2px; width: 100%; word-wrap: break-word; line-height: 1.2;">
        {{ $product->item_name }}
    </div>

    <div style="font-weight: 900; font-size: 24px; margin-bottom: 4px; color: #000;">
        {{ number_format($product->sale_price_per_piece, 0) }}/-
    </div>

    @php
        $barcodeValue = $product->barcode_path ?: $product->item_code;
    @endphp

    <div class="barcode" style="width: 100%;">
        {!! DNS1D::getBarcodeSVG($barcodeValue, 'C128', 1.0, 35) !!}
    </div>

    
</div>

<script>
    window.onload = function() {
        // window.print(); // Uncomment if you want auto-print dialog
    };
</script>


</body>
</html>
