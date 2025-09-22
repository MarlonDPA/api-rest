<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessSale;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /*public function store(Request $request)
    {
        $validated = $request->validate([
            'status'              => 'nullable|in:pending,completed',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $totalCost   = 0;

            $status = $validated['status'] ?? 'completed';

            $sale = Sale::create([
                'total_amount' => 0,
                'total_cost'   => 0,
                'total_profit' => 0,
                'status'       => $status,
            ]);

            foreach ($validated['items'] as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $inventory = Inventory::where('product_id', $product->id)->first();

                // Se for "completed", valida e dá baixa no estoque
                if ($status === 'completed') {
                    if (!$inventory || $inventory->quantity < $item['quantity']) {
                        return response()->json([
                            'message' => "Estoque insuficiente para o produto {$product->name}"
                        ], 400);
                    }

                    $inventory->quantity -= $item['quantity'];
                    $inventory->last_updated = now();
                    $inventory->save();
                }

                $unitCost  = $product->cost_price;
                $unitPrice = $product->sale_price;

                $lineCost   = $unitCost * $item['quantity'];
                $lineAmount = $unitPrice * $item['quantity'];

                $totalCost   += $lineCost;
                $totalAmount += $lineAmount;

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'unit_cost'  => $unitCost,
                ]);
            }

            $sale->update([
                'total_amount' => $totalAmount,
                'total_cost'   => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
            ]);
            
            $statusSale = ($status === 'completed' ? 'Venda registrada com sucesso!' : 'Venda pendente'); 

            return response()->json([
                'message' => $statusSale,                  
                'sale'    => $sale->load('items.product')
            ], 201);
        });
    }*/

    public function store(Request $request)
    {
        $validated = $request->validate([
            'status'              => 'nullable|in:pending,completed',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
        ]);
        
        ProcessSale::dispatch($validated);

        $message = ($validated['status'] ?? 'completed') === 'completed' ? 'Venda em processamento!' : 'Venda pendente em processamento!';

        return response()->json(['message' => $message], 202);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);

        return response()->json($sale);
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
