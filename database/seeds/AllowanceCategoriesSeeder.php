<?php

use App\Models\AllowanceCategories;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceCategoriesSeeder extends Seeder
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
            AllowanceCategories::truncate();
            DB::statement("SET foreign_key_checks=1");
        } else {
            AllowanceCategories::truncate();
        }
        $categories = json_decode(File::get(database_path('datas/allowance_categories.json')));
        foreach ($categories as $category) {
            AllowanceCategories::create([
                'key'   => $category->key,
                'value' => $category->value,
                'type'  => $category->type
            ]);
        }
    }
}