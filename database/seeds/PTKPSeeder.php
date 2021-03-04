<?php

use App\Models\PTKP;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PTKPSeeder extends Seeder
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
            PTKP::truncate();
            DB::statement("SET foreign_key_checks=1");
        } else {
            PTKP::truncate();
        }
        $ptkps = json_decode(File::get(database_path('datas/ptkp.json')));
        foreach ($ptkps as $ptkp) {
            PTKP::create([
                'key'   => $ptkp->key,
                'value' => $ptkp->value
            ]);
        }
    }
}