<?php

use App\Models\WorkgroupMaster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('DB_CONNECTION', 'pgsql') == 'mysql') {
            DB::statement("SET foreign_key_checks=0");
            WorkgroupMaster::truncate();
            DB::statement("SET foreign_key_checks=1");
        } else {
            WorkgroupMaster::truncate();
        }
        $WMS = json_decode(File::get(database_path('datas/workgroups.json')));
        foreach ($WMS as $WM) {
            WorkgroupMaster::create([
                'code' => $WM->code,
                'name' => $WM->name,
                'can_edit' => $WM->can_edit,
                'can_delete' => $WM->can_delete
            ]);
        }
    }
}