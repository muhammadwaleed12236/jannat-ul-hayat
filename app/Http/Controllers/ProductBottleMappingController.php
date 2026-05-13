<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\ProductBottleMapping;
use Illuminate\Http\Request;

class ProductBottleMappingController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $bottleProducts = Product::where('item_name', 'like', '%bottle%')
            ->orWhere('item_name', 'like', '%sheshi%')
            ->get();
            
        // If no products found with those keywords, just get all for selection
        if($bottleProducts->isEmpty()) {
            $bottleProducts = Product::all();
        }

        $mappings = ProductBottleMapping::with(['product', 'bottleProduct'])->latest()->get();

        return view('admin_panel.product.bottle_mapping', compact('categories', 'bottleProducts', 'mappings'));
    }

    public function getProductsBySubcategory($subcategory_id)
    {
        $products = Product::where('sub_category_id', $subcategory_id)->get();
        return response()->json($products);
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'min_qty' => 'required|array',
            'max_qty' => 'required|array',
            'bottle_product_id' => 'required|array',
        ]);

        foreach ($request->product_ids as $product_id) {
            foreach ($request->min_qty as $index => $min) {
                $max = $request->max_qty[$index];
                $bottle_id = $request->bottle_product_id[$index];

                if (!$min || !$max || !$bottle_id) continue;

                ProductBottleMapping::updateOrCreate(
                    [
                        'product_id' => $product_id,
                        'min_qty' => $min,
                        'max_qty' => $max,
                    ],
                    [
                        'bottle_product_id' => $bottle_id,
                        'is_active' => true
                    ]
                );
            }
        }

        return back()->with('success', 'Bottle mappings assigned successfully!');
    }

    public function delete($id)
    {
        ProductBottleMapping::findOrFail($id)->delete();
        return back()->with('success', 'Mapping deleted successfully!');
    }
}
