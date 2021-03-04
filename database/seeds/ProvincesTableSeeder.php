<?php

use App\Models\Province;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesTableSeeder extends Seeder
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
            Province::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            Province::truncate();
        }
        $provinces = json_decode(File::get(database_path('datas/provinces.json')));
        foreach ($provinces as $province) {
            Province::create([
                'id'  => $province->id,
                'name'=> $province->name
            ]);
        }
    }
}