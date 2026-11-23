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
        $inventories = InventoryGeneral::whereHas('product')->count();

        return view('home.index')->with(compact('entries', 'outputs', 'inventories'));
    }

    public function statistics(Request $request)
    {
        $period = $request->input('period', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
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

        $topProducts = Output::selectRaw('product_id, COUNT(*) as total_sales')
            ->whereHas('outputGeneral', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->with('product:id,name,sale_type')
            ->groupBy('product_id')
            ->orderByDesc('total_sales')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->total_sales,
                    'unit' => 'ventas',
                ];
            });

        $totalSales = OutputGeneral::whereBetween('created_at', [$start, $end])->sum('total_sold');
        $totalProfits = OutputGeneral::whereBetween('created_at', [$start, $end])->sum('total_profit');
        $totalOutputs = OutputGeneral::whereBetween('created_at', [$start, $end])->count();

        $days = max(1, (int) round($start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1));
        $previousEnd = $start->copy()->subSecond();
        $previousStart = $start->copy()->subDays($days)->startOfDay();

        $previousSales = OutputGeneral::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total_sold');
        $previousProfits = OutputGeneral::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total_profit');
        $previousOutputs = OutputGeneral::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        $calculateChange = function ($current, $previous) {
            if ($previous <= 0) {
                return null;
            }

            return round((($current - $previous) / $previous) * 100, 1);
        };

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
            'previous' => [
                'sales' => round($previousSales, 2),
                'profits' => round($previousProfits, 2),
                'outputs' => $previousOutputs,
            ],
            'changes' => [
                'sales' => $calculateChange($totalSales, $previousSales),
                'profits' => $calculateChange($totalProfits, $previousProfits),
                'outputs' => $calculateChange($totalOutputs, $previousOutputs),
            ],
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'days' => $days,
            ],
        ]);
    }
}
