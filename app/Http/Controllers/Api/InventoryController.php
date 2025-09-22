<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //return Inventory::all();

        $inventories = Cache::remember('inventory_summary', 60, function () {
            return Inventory::with('product:id,name,sku,cost_price,sale_price')
                ->select('product_id', 'quantity', 'last_updated')
                ->get();
        });

        // $inventories = Inventory::with('product:id,name,sku,cost_price,sale_price')
        //     ->select('product_id', 'quantity', 'last_updated')
        //     ->get();

        $summary = [
            'total_quantity'   => $inventories->sum('quantity'),
            'total_cost'       => $inventories->sum(fn($i) => $i->quantity * $i->product->cost_price),
            'total_sale_value' => $inventories->sum(fn($i) => $i->quantity * $i->product->sale_price),
            'projected_profit' => $inventories->sum(fn($i) => $i->quantity * ($i->product->sale_price - $i->product->cost_price))
        ];

        return response()->json([
            'summary'   => $summary,
            'inventory' => $inventories
        ]);
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|integer',
            'sku'        => 'nullable|string',
            'quantity'   => 'required|integer|min:1',
        ]);
        
        $product = null;

        if (!empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
        } elseif (!empty($validated['sku'])) {
            $product = Product::where('sku', $validated['sku'])->first();
        }
        
        if (!$product) {
            return response()->json([
                'message' => 'Produto não cadastrado'
            ], 404);
        }

       
        $inventory = Inventory::where('product_id', $product->id)->first();

        // Atualiza quantidade existente ou ria novo registro no estoque
        if ($inventory) {
            $inventory->quantity += $validated['quantity'];
            $inventory->last_updated = now();
            $inventory->save();
        } else {
            $inventory = Inventory::create([
                'product_id'   => $product->id,
                'quantity'     => $validated['quantity'],
                'last_updated' => now(),
            ]);
        }

        return response()->json([
            'message'   => 'Estoque atualizado com sucesso!',
            'inventory' => $inventory->load('product')
        ], 201);
    }
            
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
