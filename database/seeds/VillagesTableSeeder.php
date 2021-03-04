<?php

use App\Models\Village;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillagesTableSeeder extends Seeder
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
            Village::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            Village::truncate();
        }
        $villages = json_decode(File::get(database_path('datas/villages.json')));
        foreach ($villages as $village) {
            Village::create([
                'id'  => $village->id,
                'district_id'=> $village->district_id,
                'name'=> $village->name
            ]);
        }
    }
}
