<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user1 = User::create([
            
            "name" => "Admin",
            "last_name" => "Admin",
            "ci" => "12345",
            "charge" => "Encargado",
            "type_user_id" => 1,
            "password" => Hash::make("12345"),
        ]);

        $user1->assignRole('admin');


    }
}
