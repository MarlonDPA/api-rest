<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $validated;

    /**
     * Criar nova instância do Job.
     */
    public function __construct(array $validated)
    {
        $this->validated = $validated;
    }

    /**
     * Executa o job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $totalAmount = 0;
            $totalCost   = 0;

            $status = $this->validated['status'] ?? 'completed';

            $sale = Sale::create([
                'total_amount' => 0,
                'total_cost'   => 0,
                'total_profit' => 0,
                'status'       => $status,
            ]);

            foreach ($this->validated['items'] as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $inventory = Inventory::where('product_id', $product->id)->first();

                if ($status === 'completed') {
                    if (!$inventory || $inventory->quantity < $item['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto {$product->name}");
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
        });
    }
}

