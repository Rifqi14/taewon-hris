<?php

use App\Models\Site;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesTableSeeder extends Seeder
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
            Site::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            Site::truncate();
        }
        $sites = json_decode(File::get(database_path('datas/sites.json')));
        foreach ($sites as $site) {
            Site::create([
                'code'=> $site->code,
                'name'=> $site->name,
                'phone'=> $site->phone,
                'email'=> $site->email,
                'province_id'=> $site->province_id,
                'region_id'=> $site->region_id,
                'district_id'=> $site->district_id,
                'postal_code'=> $site->postal_code,
                'address'=> $site->address,
                'logo'=> $site->logo,
                'receipt_header'=> $site->receipt_header,
                'receipt_footer'=> $site->receipt_footer,
            ]);
        }
    }
}
