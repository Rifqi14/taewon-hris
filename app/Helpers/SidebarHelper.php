<?php
use App\Models\Menu;
if (!function_exists('buildMenuAdmin')) {
   
    function buildMenuAdmin($elements,$parent=0,$menu_active)
    {
        $parentmenu = Menu::with('parent')->where('menu_route',str_replace(url('admin').'/','',$menu_active))->first();
        $parentname = '';
        if(@$parentmenu->parent){
            $parentname = $parentmenu->parent->menu_name;
        }
        $result = '';
        if($parent){
            $result .= '<ul class="nav nav-treeview">';
        }
        foreach ($elements as $element)
        {
            if ($element->parent_id == $parent){
                if (menuHasChildren($elements,$element->id)){
                    $result.= '<li id="menu-'.$element->id.'" class="nav-item has-treeview '.($element->menu_name == $parentname?'active menu-open':'').'"><a href="'.url('admin/'.$element->menu_route).'" class="nav-link"><i class="nav-icon '.$element->menu_icon.'"></i> <p>'.$element->menu_name.'<i class="fas fa-angle-left right"></i></p>';
                    $result.='</a>';
                }
                else{
                    $style = '';
                    if(url('admin/'.$element->menu_route)==$menu_active && $element->parent_id != 0){
                        $style='color:#dc3545';
                    }
                    $result.= '<li id="menu-'.$element->id.'" class="nav-item"><a href="'.url('admin/'.$element->menu_route).'" class="nav-link '.(url('admin/'.$element->menu_route)==$menu_active?'active':'').'"><i class="nav-icon '.($element->menu_icon?$element->menu_icon:(url('admin/'.$element->menu_route)==$menu_active?'fas fa-circle nav-icon':'far fa-circle nav-icon')).'" style="'.$style.'"></i> <p>'.$element->menu_name.'</p></a>';
                }
                if (menuHasChildren($elements,$element->id))
                    $result.= buildMenuAdmin($elements,$element->id,$menu_active);
                $result.= "</li>";
            }
        }
        if($parent){
            $result.= "</ul>";
        }
        return $result; 
    }
}

if (!function_exists('menuHasChildren')) {
    function menuHasChildren($rows,$id) {
        foreach ($rows as $row) {
            if ($row->parent_id == $id)
                return true;
        }
        return false;
    }
}