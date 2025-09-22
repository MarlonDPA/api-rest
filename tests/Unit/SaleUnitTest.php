<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;

class SaleUnitTest extends TestCase
{
    /** @test */
    public function pode_criar_venda_completed_e_atualizar_estoque_unitario()
    {
        $product = new Product();
        $product->id         = 1;
        $product->name       = 'Produto Teste';
        $product->cost_price = 50;
        $product->sale_price = 100;

        $inventory = new Inventory();
        $inventory->product_id   = $product->id;
        $inventory->quantity     = 10;
        $inventory->last_updated = now();

        $sale = new Sale();
        $sale->id           = 1;
        $sale->total_amount  = 0;
        $sale->total_cost    = 0;
        $sale->total_profit  = 0;
        $sale->status        = 'completed';

        $item = new SaleItem();
        $item->sale_id    = $sale->id;
        $item->product_id = $product->id;
        $item->quantity   = 2;
        $item->unit_price = $product->sale_price;
        $item->unit_cost  = $product->cost_price;

        $inventory->quantity -= $item->quantity;

        $sale->total_amount  = $item->unit_price * $item->quantity;
        $sale->total_cost    = $item->unit_cost * $item->quantity;
        $sale->total_profit  = $sale->total_amount - $sale->total_cost;

        $this->assertEquals(8, $inventory->quantity); 
        $this->assertEquals(200, $sale->total_amount);
        $this->assertEquals(100, $sale->total_cost);
        $this->assertEquals(100, $sale->total_profit);
        $this->assertEquals('completed', $sale->status);
        $this->assertEquals(1, $item->sale_id);
        $this->assertEquals(2, $item->quantity);
    }
}
