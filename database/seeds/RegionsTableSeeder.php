<?php

use App\Models\Region;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsTableSeeder extends Seeder
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
            Region::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            Region::truncate();
        }
        $regions = json_decode(File::get(database_path('datas/regions.json')));
        foreach ($regions as $region) {
            Region::create([
                'id'=> $region->id,
                'province_id'=> $region->province_id,
                'name'=> $region->name,
                'type'=> $region->type,
            ]);
        }
    }
}
