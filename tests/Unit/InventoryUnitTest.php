<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Inventory;

class InventoryUnitTest extends TestCase
{
    /** @test */
    public function pode_criar_produto_no_inventario_unitario()
    {        
        $product = new Product();
        $product->id   = 1;
        $product->name = 'Produto Teste';
        $product->sku  = 'SKU001';
        
        $inventory = new Inventory();
        $inventory->product_id   = $product->id;
        $inventory->quantity     = 10;
        $inventory->last_updated = now();

        $this->assertEquals(1, $inventory->product_id);
        $this->assertEquals(10, $inventory->quantity);
        $this->assertNotNull($inventory->last_updated);

        $this->assertEquals('Produto Teste', $product->name);
        $this->assertEquals('SKU001', $product->sku);
    }
}
