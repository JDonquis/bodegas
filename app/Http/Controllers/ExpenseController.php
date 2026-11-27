<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\OutputGeneral;
use App\Services\BCVService;
use App\Services\ExpenseService;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $query = Expense::with('items')->orderByDesc('created_at');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $totalCost = (clone $query)->sum('total_cost');
        $totalRetail = (clone $query)->sum('total_retail');
        $records = (clone $query)->count();

        $expenses = $query->paginate(10)->withQueryString();

        $grossProfitQuery = OutputGeneral::query();

        if ($from) {
            $grossProfitQuery->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $grossProfitQuery->whereDate('created_at', '<=', $to);
        }

        $grossProfit = (float) $grossProfitQuery->sum('total_profit');
        $netProfit = round($grossProfit - (float) $totalCost, 2);
        $lostProfit = round((float) $totalRetail - (float) $totalCost, 2);
        $impact = $grossProfit > 0
            ? round(((float) $totalCost / $grossProfit) * 100, 1)
            : 0;

        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue() ?? 0;

        return view('home.expenses.index')->with(compact(
            'expenses',
            'totalCost',
            'totalRetail',
            'grossProfit',
            'netProfit',
            'lostProfit',
            'impact',
            'records',
            'from',
            'to',
            'usdRate'
        ));
    }

    public function create()
    {
        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue() ?? 0;

        return view('home.expenses.create')->with(compact('usdRate'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventoryID' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('inventories', 'id')->where('tenant_id', app(TenantContext::class)->id()),
            ],
            'items.*.productID' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('tenant_id', app(TenantContext::class)->id()),
            ],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
        ]);

        DB::beginTransaction();

        try {
            $expenseService = new ExpenseService;
            $expenseService->create($validated['items'], $validated['description'] ?? null);

            DB::commit();

            return redirect()->route('expenses')->with([
                'success' => 'Gasto registrado y stock actualizado exitosamente',
            ]);
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error('ERROR AL REGISTRAR GASTO: ' . $error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()])->withInput();
        }
    }

    public function destroy(Expense $expense)
    {
        DB::beginTransaction();

        try {
            $expenseService = new ExpenseService;
            $expenseService->delete($expense);

            DB::commit();

            return redirect()->route('expenses')->with([
                'success' => 'Gasto eliminado y stock restaurado exitosamente',
            ]);
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error('ERROR AL ELIMINAR GASTO: ' . $error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }
}
