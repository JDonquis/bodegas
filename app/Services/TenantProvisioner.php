<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\TypeUser;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Throwable;

class TenantProvisioner
{
    public function provision(array $data): Tenant
    {
        $context = app(TenantContext::class);

        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'name' => $data['name'],
                'is_active' => true,
                'is_default' => false,
            ]);

            $context->set($tenant);

            $typeUser = TypeUser::firstOrCreate(['name' => 'admin']);

            $admin = User::create([
                'name' => $data['admin_name'],
                'last_name' => '',
                'charge' => 'Administrador',
                'ci' => $data['admin_ci'],
                'type_user_id' => $typeUser->id,
                'password' => $data['admin_password'],
            ]);

            $admin->assignRole('admin');

            Client::create([
                'name' => 'Cliente',
                'debt' => 0,
                'credit_limit' => 0,
            ]);

            DB::commit();

            return $tenant;
        } catch (Throwable $error) {
            DB::rollBack();

            throw $error;
        } finally {
            $context->clear();
        }
    }
}
