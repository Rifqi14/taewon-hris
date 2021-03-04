<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
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
            App\Role::truncate();
            App\User::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            App\Role::truncate();
            App\User::truncate();
        }
        $roles = json_decode(File::get(database_path('datas/roles.json')));
        foreach ($roles as $role) {
            $newrole = App\Role::create([
                'name'=> $role->name,
                'display_name'=> $role->display_name,
                'description'=> $role->description
            ]);
            $newuser = App\User::create([
                'name' 	=> $role->display_name,
                'email' 	=> $role->name.'@enviostore.com',
                'username' 	=> $role->name,
                'password'	=> Hash::make(123456),
                'last_login' => new \DateTime,
                'status' 	=> 1,
            ]);
            $newrole->users()->sync([]); 
            $newuser->attachRole($newrole);

            $menus = App\Models\Menu::all();
            foreach($menus as $menu){
                App\Models\RoleMenu::create([
                    'role_id' => $newrole->id,
                    'menu_id' => $menu->id,
                    'role_access' => 1
                ]);
            }

            $sites = App\Models\Site::all();
            foreach($sites as $site){
                App\Models\SiteUser::create([
                    'user_id' => $newuser->id,
                    'site_id' => $site->id
                ]);
            }
        }
    }
}
