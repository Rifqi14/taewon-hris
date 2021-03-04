<?php

use App\Models\District;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(env('DB_CONNECTION', 'pgsql') == 'mysql'){
            DB::statement("SET foreign_key_checks=0");
            District::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            District::truncate();
        }
        $districts = json_decode(File::get(database_path('datas/districts.json')));
        foreach ($districts as $district) {
            District::create([
                'id'  => $district->id,
                'region_id'=> $district->region_id,
                'name'=> $district->name
            ]);
        }
    }
}
