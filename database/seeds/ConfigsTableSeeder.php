<?php

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
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
            Config::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            Config::truncate();
        }
        $configs = json_decode(File::get(database_path('datas/configs.json')));
        foreach ($configs as $config) {
            Config::create([
                'option'=> $config->option,
                'value'=> $config->value
            ]);
        }
    }
}
