<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantPayment;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('superadmin')->check()) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Credenciales incorrectas.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('superadmin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login');
    }

    public function dashboard()
    {
        $tenants = Tenant::orderByDesc('id')->get();

        return view('superadmin.dashboard', compact('tenants'));
    }

    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_ci' => ['required', 'string', 'max:50', Rule::unique('users', 'ci')],
            'admin_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        try {
            $tenant = (new TenantProvisioner)->provision($validated);

            return back()->with([
                'success' => 'Bodega creada correctamente.',
            ]);
        } catch (Throwable $error) {
            Log::error('ERROR AL CREAR BODEGA: ' . $error->getMessage());

            return back()
                ->withErrors(['error' => 'No se pudo crear la bodega: ' . $error->getMessage()])
                ->withInput();
        }
    }

    public function toggleTenant(Tenant $tenant)
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        return back()->with([
            'success' => $tenant->is_active
                ? 'Bodega habilitada correctamente.'
                : 'Bodega inhabilitada. Sus usuarios ya no podrán acceder.',
        ]);
    }

    public function updateCredentials(Request $request)
    {
        $superAdmin = Auth::guard('superadmin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('super_admins', 'email')->ignore($superAdmin->id),
            ],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $superAdmin->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual es incorrecta.',
            ])->withInput();
        }

        $superAdmin->name = $validated['name'];
        $superAdmin->email = $validated['email'];

        if (! empty($validated['password'])) {
            $superAdmin->password = $validated['password'];
        }

        $superAdmin->save();

        return back()->with(['success' => 'Credenciales actualizadas correctamente.']);
    }

    public function billing()
    {
        $tenants = Tenant::with('payments')->orderBy('name')->get();

        $billing = $tenants->map(function (Tenant $tenant) {
            $start = $tenant->created_at->copy()->startOfMonth();
            $current = now()->startOfMonth();
            $months = max(1, (int) $start->diffInMonths($current) + 1);
            $due = $months * Tenant::MONTHLY_FEE;
            $paid = (float) $tenant->payments->sum('amount');
            $balance = round($due - $paid, 2);

            return [
                'tenant' => $tenant,
                'months' => $months,
                'due' => round($due, 2),
                'paid' => round($paid, 2),
                'balance' => $balance,
                'up_to_date' => $balance <= 0,
            ];
        });

        $totalCollected = (float) TenantPayment::sum('amount');
        $totalPending = (float) $billing->sum(function ($row) {
            return max(0, $row['balance']);
        });
        $activeTenants = $tenants->where('is_active', true)->count();
        $monthlyRecurring = $activeTenants * Tenant::MONTHLY_FEE;

        $recentPayments = TenantPayment::with('tenant')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('superadmin.billing', compact(
            'billing',
            'totalCollected',
            'totalPending',
            'activeTenants',
            'monthlyRecurring',
            'recentPayments'
        ));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'period' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        TenantPayment::create($validated);

        return back()->with(['success' => 'Pago registrado correctamente.']);
    }
}
