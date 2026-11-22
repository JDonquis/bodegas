<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDebt;
use App\Models\ClientPayment;
use App\Models\OutputGeneral;
use App\Services\BCVService;
use App\Tenancy\TenantContext;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $clientId = $request->input('client_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = ClientDebt::with(['client', 'outputGeneral', 'payments'])
            ->orderByDesc('created_at');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $filteredTotal = (clone $query)->sum('amount');
        $debts = $query->paginate(10)->withQueryString();

        $clients = Client::excludingDefault()->orderBy('name')->get();
        $debtorClients = Client::excludingDefault()->where('debt', '>', 0)->orderBy('name')->get();
        $sales = OutputGeneral::with('client')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $pendingDebts = ClientDebt::with(['client', 'payments'])
            ->orderByDesc('created_at')
            ->get()
            ->filter(function ($debt) {
                return ((float) $debt->amount - $debt->payments->sum('amount')) > 0.001;
            })
            ->values();

        $totalDebt = Client::excludingDefault()->sum('debt');
        $debtorsCount = Client::excludingDefault()->where('debt', '>', 0)->count();

        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue() ?? 0;

        $openDebtSheet = $request->boolean('open_debt');
        $openPaymentSheet = $request->boolean('open_payment');

        return view('home.debts.index')->with(compact(
            'debts',
            'clients',
            'debtorClients',
            'sales',
            'pendingDebts',
            'totalDebt',
            'debtorsCount',
            'filteredTotal',
            'clientId',
            'from',
            'to',
            'openDebtSheet',
            'openPaymentSheet',
            'usdRate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['nullable'],
            'new_client_name' => ['nullable', 'string', 'max:255'],
            'new_client_ci' => ['nullable', 'string', 'max:50'],
            'new_client_phone' => ['nullable', 'string', 'max:50'],
            'output_general_id' => [
                'nullable',
                Rule::exists('output_generals', 'id')->where('tenant_id', app(TenantContext::class)->id()),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $clientId = $validated['client_id'] ?? null;
        $newClientName = trim((string) ($validated['new_client_name'] ?? ''));

        if (! $clientId && $newClientName === '') {
            return back()
                ->withErrors(['client_id' => 'Selecciona un cliente o crea uno nuevo.'])
                ->withInput();
        }

        $isNewClient = $clientId === 'new' || (! $clientId && $newClientName !== '');

        if ($isNewClient && $newClientName === '') {
            return back()
                ->withErrors(['new_client_name' => 'El nombre del nuevo cliente es obligatorio.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            if ($isNewClient) {
                $client = Client::create([
                    'name' => ucwords(strtolower($newClientName)),
                    'ci' => $validated['new_client_ci'] ?? null,
                    'phone_number' => $validated['new_client_phone'] ?? null,
                    'debt' => 0,
                    'credit_limit' => 0,
                ]);
            } else {
                $client = Client::findOrFail($clientId);
            }

            $amount = round((float) $validated['amount'], 2);
            $outputGeneralId = $validated['output_general_id'] ?? null;

            if ($outputGeneralId) {
                $sale = OutputGeneral::find($outputGeneralId);

                if ($sale && ! $sale->client_id) {
                    $sale->update([
                        'client_id' => $client->id,
                        'client_name' => $client->name,
                    ]);
                }
            }

            ClientDebt::create([
                'client_id' => $client->id,
                'output_general_id' => $outputGeneralId,
                'amount' => $amount,
                'description' => $validated['description'] ?? null,
            ]);

            $client->update(['debt' => round((float) $client->debt + $amount, 2)]);

            DB::commit();

            return redirect()->route('debts')->with(['success' => 'Deuda registrada exitosamente']);
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error('ERROR AL REGISTRAR DEUDA: ' . $error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()])->withInput();
        }
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('tenant_id', app(TenantContext::class)->id()),
            ],
            'client_debt_id' => [
                'nullable',
                Rule::exists('client_debts', 'id')->where('tenant_id', app(TenantContext::class)->id()),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $client = Client::findOrFail($validated['client_id']);
        $amount = round((float) $validated['amount'], 2);
        $debt = null;

        if (! empty($validated['client_debt_id'])) {
            $debt = ClientDebt::with('payments')->findOrFail($validated['client_debt_id']);

            if ($debt->client_id != $client->id) {
                return back()
                    ->withErrors(['client_debt_id' => 'La deuda seleccionada no pertenece al cliente.'])
                    ->withInput();
            }

            $paid = $debt->payments->sum('amount');
            $remaining = round((float) $debt->amount - $paid, 2);

            if ($amount > $remaining) {
                return back()
                    ->withErrors(['amount' => 'El abono supera el saldo pendiente de la deuda.'])
                    ->withInput();
            }
        }

        if ($amount > round((float) $client->debt, 2)) {
            return back()
                ->withErrors(['amount' => 'El abono supera la deuda total del cliente.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            ClientPayment::create([
                'client_id' => $client->id,
                'client_debt_id' => $debt?->id,
                'amount' => $amount,
                'description' => $validated['description'] ?? null,
            ]);

            $client->update([
                'debt' => max(0, round((float) $client->debt - $amount, 2)),
            ]);

            DB::commit();

            return redirect()->route('debts')->with(['success' => 'Abono registrado exitosamente']);
        } catch (Throwable $error) {
            DB::rollBack();
            Log::error('ERROR AL REGISTRAR ABONO: ' . $error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()])->withInput();
        }
    }
}
