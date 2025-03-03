<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);


        $readEntryPermission = Permission::create(['name' => 'read-entries']);
        $createEntryPermission = Permission::create(['name' => 'create-entries']);
        $updateEntryPermission = Permission::create(['name' => 'update-entries']);
        $deleteEntryPermission = Permission::create(['name' => 'delete-entries']);

        $readOutputPermission = Permission::create(['name' => 'read-outputs']);
        $createOutputPermission = Permission::create(['name' => 'create-outputs']);
        $updateOutputPermission = Permission::create(['name' => 'update-outputs']);
        $deleteOutputPermission = Permission::create(['name' => 'delete-outputs']);

        $readInventoriesPermission = Permission::create(['name' => 'read-inventories']);


        $readPatientsPermission = Permission::create(['name' => 'read-patients']);
        $createPatientsPermission = Permission::create(['name' => 'create-patients']);
        $updatePatientsPermission = Permission::create(['name' => 'update-patients']);
        $deletePatientsPermission = Permission::create(['name' => 'delete-patients']);



        $adminPermissions = [

            $readEntryPermission,  $readOutputPermission,
            $createEntryPermission, $createOutputPermission,
            $updateEntryPermission, $updateOutputPermission,
            $deleteEntryPermission, $deleteOutputPermission,

            $readInventoriesPermission,

        ];


        $adminRole->syncPermissions($adminPermissions);

    }
}
