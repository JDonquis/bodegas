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

            "name" => "Jose",
            "last_name" => "Gomez",
            "ci" => "1234",
            "charge" => "Doctor",
            "type_user_id" => 2,
            "password" => Hash::make("123"),
        ]);

        $user2 = User::create([
            
            "name" => "John",
            "last_name" => "Doe",
            "ci" => "12345",
            "charge" => "Enfermero",
            "type_user_id" => 2,
            "password" => Hash::make("123"),
        ]);

        $user3 = User::create([
            
            "name" => "Oscar",
            "last_name" => "Castellanos",
            "ci" => "123",
            "charge" => "Encargado",
            "type_user_id" => 1,
            "password" => Hash::make("123"),
        ]);

        $user1->assignRole('user');
        $user2->assignRole('user');
        $user3->assignRole('admin');


    }
}
