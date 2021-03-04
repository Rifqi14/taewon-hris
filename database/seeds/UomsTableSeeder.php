<?php

use App\Models\Uom;
use App\Models\UomCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UomsTableSeeder extends Seeder
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
            Uom::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            Uom::truncate();
        }

        // DB::table('uoms')->truncate();
        $uoms = json_decode(File::get(database_path('datas/uoms.json')));
        foreach ($uoms as $uom) {
            $uomcategory = UomCategory::where('code','=',$uom->code)->get()->first();
            if($uomcategory){
                Uom::create([
                    'uomcategory_id' 	=> $uomcategory->id,
                    'name' 	=> $uom->name,
                    'type' 	=> $uom->type,
                    'ratio' => $uom->ratio
                ]);
            }
        }
    }
}
