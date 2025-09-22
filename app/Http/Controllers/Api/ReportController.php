<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('start')) {
            $query->whereDate('created_at', '>=', $request->start);
        }

        if ($request->filled('end')) {
            $query->whereDate('created_at', '<=', $request->end);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->with('items.product')->get();

        $summary = [
            'total_sales'  => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'total_cost'   => $sales->sum('total_cost'),
            'total_profit' => $sales->sum('total_profit'),
        ];

        return response()->json([
            'summary' => $summary,
            'sales'   => $sales,
        ]);
    }
}