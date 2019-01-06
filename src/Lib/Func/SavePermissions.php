<?php
namespace Mf\Permissions\Lib\Func;



class SavePermissions 
{
public function __invoke($obj,$tab_rec,$id,$spec_poles,$tab_name,$action)
{


    //запись/создание строки
    if ($action==-2) {
        echo $id;
	}

    
    //удаление строки
    if ($action==-3) {
        echo $id;
	}

    
    
    
    return true;
}


}
