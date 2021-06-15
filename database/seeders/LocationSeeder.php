<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataArr = ['Tang 1- Dinamond Tower','31A- Nguyen Quoc Tri','Charmvit Tower','Tang 8- MoonCity'];
        foreach ($dataArr as $value){
            Location::firstOrCreate([
                'name' => $value
            ]);
        }
    }
}
