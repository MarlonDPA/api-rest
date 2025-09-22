<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Sale;

class LucroTest extends TestCase
{
    /** @test */
    public function calcula_lucro_da_venda()
    {
        $sale = new Sale([
            'total_amount' => 200,
            'total_cost'   => 120,
        ]);

        $lucro = $sale->total_amount - $sale->total_cost;

        $this->assertEquals(80, $lucro);
    }
}
