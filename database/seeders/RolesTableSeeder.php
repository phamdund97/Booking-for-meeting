<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate([
            'id' => '1', 'name' => 'admin'
        ]);
        Role::firstOrCreate([
            'id'=> '2', 'name' => 'user'
        ]);
    }
}
