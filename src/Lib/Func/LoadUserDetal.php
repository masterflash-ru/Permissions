<?php


namespace Mf\Permissions\Lib\Func;

use Admin\Lib\simba;
use Mf\Permissions\Service\UserManager;


class LoadUserDetal
{



public function __invoke ($obj,$tab_rec,$struct0,$struct2,$tab_name,$const,$row_item,$a,$b,$action)
{


    //однодневные, отелей нет,
   /* $arr= simba::queryAllRecords("select users.*, 
            (select group_concat(users_group) from users2group where users=users.id) as gr 
                from users as u where id=$get_interface_input");
                */
//глобальная выборка для всей таблицы
if ($action==-1) {
    $UserManager=$obj->container->get(UserManager::class);
    $arr=[];
    $r=$UserManager->GetUserIdInfo((int)$obj->get_interface_input);
    foreach (["id","name","full_name","status","date_registration","date_last_login","login"] as $item) {
        $m="get".$item;
        $arr[$item]=[$r->$m()];
    }
    //добавим группы
    $arr["gr"]=[implode(",",$UserManager->getGroupIds($r->getId()))];
    return $arr;
}



return true;
}

}
