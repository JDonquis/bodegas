<?php

namespace App\Http\Controllers;

use App\Models\EntryGeneral;
use App\Models\InventoryGeneral;
use App\Models\Output;
use App\Models\OutputGeneral;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function login()
    {
        return view('welcome');
    }

    public function home()
    {
        $entries = EntryGeneral::count();
        $outputs = OutputGeneral::count();
        $inventories = InventoryGeneral::count();

        return view('home.index')->with(compact('entries', 'outputs', 'inventories'));
    }

    public function statistics(Request $request)
    {
        $period = $request->input('period', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $now = Carbon::now();

        switch ($period) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'custom':
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                break;
            default:
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
        }

        $salesData = [];
        $profitData = [];
        $labels = [];

        if ($period === 'week') {
            for ($i = 0; $i < 7; $i++) {
                $day = $start->copy()->addDays($i);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $sales = OutputGeneral::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_sold');
                $profits = OutputGeneral::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_profit');

                $salesData[] = round($sales, 2);
                $profitData[] = round($profits, 2);
                $labels[] = $day->shortDayName;
            }
        } else {
            $current = $start->copy();
            while ($current->lte($end)) {
                $periodStart = $current->copy()->startOfDay();
                $periodEnd = $current->copy()->endOfDay();

                $sales = OutputGeneral::whereBetween('created_at', [$periodStart, $periodEnd])->sum('total_sold');
                $profits = OutputGeneral::whereBetween('created_at', [$periodStart, $periodEnd])->sum('total_profit');

                $salesData[] = round($sales, 2);
                $profitData[] = round($profits, 2);
                $labels[] = $current->format('d/m');
                $current->addDay();
            }
        }

        $topProducts = Output::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->whereHas('outputGeneral', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->total_quantity,
                ];
            });

        $totalSales = OutputGeneral::whereBetween('created_at', [$start, $end])->sum('total_sold');
        $totalProfits = OutputGeneral::whereBetween('created_at', [$start, $end])->sum('total_profit');
        $totalOutputs = OutputGeneral::whereBetween('created_at', [$start, $end])->count();

        return response()->json([
            'labels' => $labels,
            'sales' => $salesData,
            'profits' => $profitData,
            'topProducts' => $topProducts,
            'totals' => [
                'sales' => round($totalSales, 2),
                'profits' => round($totalProfits, 2),
                'outputs' => $totalOutputs,
            ],
        ]);
    }
}
