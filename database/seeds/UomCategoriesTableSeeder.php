<?php

use App\Models\UomCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UomCategoriesTableSeeder extends Seeder
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
            App\Models\UomCategory::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            App\Models\UomCategory::truncate();
        }
        $uomcategories = json_decode(File::get(database_path('datas/uomcategories.json')));
        foreach ($uomcategories as $uomcategory) {
            UomCategory::create([
                'code' 	=> $uomcategory->code,
                'name' 	=> $uomcategory->name
            ]);
        };
    }
}
