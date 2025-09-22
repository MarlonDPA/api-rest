<?php

// Regra de negócio definida pelo cliente
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use Carbon\Carbon;

class CleanOldInventory extends Command
{
    protected $signature = 'inventory:clean-old';
    protected $description = 'Remove registros de estoque não atualizados há mais de 90 dias';

    public function handle()
    {
        $cutoff = Carbon::now()->subDays(90);
        $deleted = Inventory::where('last_updated', '<', $cutoff)->delete();

        $this->info("{$deleted} registros antigos de estoque foram removidos.");
    }
}
