<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards): Response
    {
        $this->authenticate($request, $guards);

        $guard = $guards[0] ?? null;
        $user = $request->user($guard);

        if ($user instanceof User && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);

            if (! $tenant || ! $tenant->is_active) {
                Auth::guard($guard)->logout();

                $message = 'Su bodega ha sido inhabilitada, por favor comunicarse con el administrador.';

                if ($request->expectsJson()) {
                    return response()->json(['error' => $message], 403);
                }

                return redirect()->route('login')->withErrors(['error' => $message]);
            }

            app(TenantContext::class)->set($tenant);
            config(['app.name' => $tenant->name]);
        }

        return $next($request);
    }

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
