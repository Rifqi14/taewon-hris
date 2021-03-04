<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
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
            App\Models\Menu::truncate();
            DB::statement("SET foreign_key_checks=1");
        }
        else{
            App\Models\Menu::truncate();
        }
        $menus = json_decode(File::get(database_path('datas/menus.json')));
        foreach ($menus as $menu) {
            if($menu->parent_name){
                $parent = App\Models\Menu::select('menus.id')
                                ->where('menu_name','=',$menu->parent_name)
                                ->get()->first();
                if($parent){
                    $newmenu = App\Models\Menu::create([
                        'parent_id' => $parent->id,
                        'menu_name' => $menu->menu_name,
                        'menu_route'=> $menu->menu_route,
                        'menu_icon' => $menu->menu_icon
                    ]);
                }
                else{
                    $newmenu = App\Models\Menu::create([
                        'parent_id' => 0,
                        'menu_name' => $menu->menu_name,
                        'menu_route'=> $menu->menu_route,
                        'menu_icon' => $menu->menu_icon
                    ]);
                }
                } 
            else{
                $newmenu = App\Models\Menu::create([
                    'parent_id' => 0,
                    'menu_name' => $menu->menu_name,
                    'menu_route'=> $menu->menu_route,
                    'menu_icon' => $menu->menu_icon,
                ]);
            }
            $newmenu->menu_sort = $newmenu->id;
            $newmenu->save();
        }
    }
}
