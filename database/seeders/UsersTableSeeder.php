<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        User::firstOrCreate([
            'id' => 1,
            'email' => 'test@ttc-solutions.com.vn',
            'password' => Hash::make('12345678'),
            'full_name' => 'Truong To Hoai',
            'phone_number' => '0908070605',
            'role_id' => 1,
            'image' => null,
            'status' => 1,
            'department_id' => 1
        ]);
    }
}
