<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$s = App\Models\Sale::with(['items.product.warehouseStocks', 'customer_relation'])->find(6);
if (!$s) { echo "Sale NOT found\n"; exit; }
echo "Sale found: {$s->id}\n";
echo "customer_id: " . var_export($s->customer_id, true) . "\n";
echo "walkin_name: " . var_export($s->walkin_name, true) . "\n";
echo "reference: " . var_export($s->reference, true) . "\n";
echo "credit_days: " . var_export($s->credit_days, true) . "\n";
echo "sales_officer_id: " . var_export($s->sales_officer_id, true) . "\n";
echo "created_at: " . ($s->created_at ? $s->created_at->format('Y-m-d H:i') : 'NULL') . "\n";
echo "customer_relation loaded: " . var_export($s->relationLoaded('customer_relation'), true) . "\n";
if ($s->customer_id && $s->customer_relation) {
    echo "customer_name: " . $s->customer_relation->customer_name . "\n";
} elseif ($s->customer_id && !$s->customer_relation) {
    echo "WARNING: customer_id={$s->customer_id} but customer_relation is NULL!\n";
}
echo "items count: " . $s->items->count() . "\n";
foreach ($s->items as $item) {
    echo "  item: product_id={$item->product_id} name={$item->product_name} price={$item->price} qty={$item->qty}\n";
    echo "    product loaded: " . var_export($item->relationLoaded('product'), true) . "\n";
    if ($item->product) {
        echo "    product name: {$item->product->name}\n";
    } else {
        echo "    WARNING: product relation is NULL for item {$item->id}\n";
    }
}
