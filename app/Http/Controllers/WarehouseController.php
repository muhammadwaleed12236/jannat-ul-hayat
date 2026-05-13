<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // Return warehouses for a given product_id
    public function getWarehouses(Request $request)
    {
        $productId = $request->input('product_id');

        // Get stock entries for this product including locations
        $warehouseStocks = WarehouseStock::with(['stockWarehouse', 'product'])
            ->where('product_id', $productId)
            ->where('total_pieces', '>', 0) // Only show locations with stock
            ->get();

        $response = $warehouseStocks->map(function ($ws) {
            $ppb = ($ws->product && $ws->product->pieces_per_box > 0) ? $ws->product->pieces_per_box : 1;
            
            // Robust Calculation
            $calcPieces = $ws->quantity * $ppb;
            if (abs($calcPieces - $ws->total_pieces) > 0.1) {
                 $stockVal = $calcPieces;
            } else {
                 $stockVal = $ws->total_pieces;
            }

            $warehouseName = $ws->stockWarehouse->warehouse_name ?? 'Unknown';
            $locationName = $ws->location ? "-{$ws->location}" : '';

            return [
                'warehouse_id' => $ws->warehouse_id,
                'location' => $ws->location ?? '',
                'warehouse_name' => $warehouseName . $locationName,
                'stock' => $stockVal, // Total pieces
                'boxes' => $ws->quantity, 
                'ppb' => $ppb,
                'size_mode' => $ws->product ? $ws->product->size_mode : 'std',
                // Unique ID for selection (warehouse_id:location)
                'uid' => $ws->warehouse_id . ($ws->location ? ':' . $ws->location : ':_none_')
            ];
        });

        return response()->json($response);
    }

    // VendorController.php aur WarehouseController.php same hoga
    public function index()
    {
        if (! auth()->user()->can('warehouse.view')) {
            abort(403, 'Unauthorized action.');
        }
        $warehouses = Warehouse::with('user')->get(); // ya $warehouses = Warehouse::all();

        return view('admin_panel.warehouses.index', compact('warehouses')); // ya warehouses.index
    }

    public function store(Request $request)
    {
        if ($request->id) {
            if (! auth()->user()->can('warehouse.edit')) {
                return back()->with('error', 'Unauthorized action.');
            }
            Warehouse::findOrFail($request->id)->update($request->all());

            return back()->with('success', 'Warehouse Updated Successfully');
        } else {
            if (! auth()->user()->can('warehouse.create')) {
                return back()->with('error', 'Unauthorized action.');
            }
            Warehouse::create($request->all());

            return back()->with('success', 'Warehouse Created Successfully');
        }
    }

    public function delete($id)
    {
        if (! auth()->user()->can('warehouse.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        Warehouse::findOrFail($id)->delete();

        return response()->json([
            'success' => 'Warehouse Deleted Successfully',
            'reload' => true,
        ]);
    }
}
