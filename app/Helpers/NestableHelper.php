<?php
if (!function_exists('buildDD')) {
   
    function buildDD($elements, $parentId = 0, $currLevel = 0, $prevLevel = -1)
    {
        foreach ($elements as $element) {

            if ($element->parent_id == $parentId) {                       
                if ($currLevel > $prevLevel){
                    if($parentId == 0){
                        echo '<ol class="dd-list">'; 
                    }
                    else{
                        echo '<ol>';
                    }
                }
            
                if ($currLevel == $prevLevel) echo " </li> ";
            
                    echo '<li class="dd-item" data-id="'.$element->id.'">
                    <div class="item_actions">
                        <button class="btn btn-xs btn-primary edit" data-id="'.$element->id.'">
                            <i class="fas fa-edit"></i>
                        </button> / 
                        <button class="btn btn-xs btn-danger delete" data-id="'.$element->id.'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="dd-handle">
                      <span>'.$element->description. '</span> <small>'. $element->url.'</small>
                    </div>
                   ';
            
                if ($currLevel > $prevLevel) { $prevLevel = $currLevel; }
            
                $currLevel++; 
            
                buildDD ($elements, $element->id, $currLevel, $prevLevel);
            
                $currLevel--;               
            }   
        
        }
        if ($currLevel == $prevLevel) echo " </li>  </ol> ";   
    }
}