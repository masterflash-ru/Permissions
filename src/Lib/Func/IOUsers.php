<?php


namespace Mf\Permissions\Lib\Func;

use Admin\Lib\simba;
use Mf\Permissions\Service\UserManager;


class IOUsers
{



public function __invoke ($obj,$tab_rec,$struct0,$struct2,$tab_name,$const,$row_item,$a,$b,$action)
{


//глобальная выборка для всей таблицы
if ($action==-1) {
    //однодневные, отелей нет,
    $arr= simba::queryAllRecords("select ue.*, u.login,u.date_registration, u.date_last_login,u.status 
        from users as u, users_ext as ue
            where u.id=ue.id and
            u.status=\"{$obj->pole_dop[2]}\" and 
                (u.date_registration>=\"{$obj->pole_dop[0]} 00:00:00\" and  
                        u.date_registration<=\"{$obj->pole_dop[1]} 23:59:59\" or isnull(u.date_registration)) 
                    and
                    (\"{$obj->pole_dop[4]}\">0 and u.login like concat(char(\"{$obj->pole_dop[4]}\"),\"%\") or \"{$obj->pole_dop[4]}\"=0) 
                    and 
                    (u.id in(select users from users2group where users_group={$obj->pole_dop[3]}) and {$obj->pole_dop[3]}>0 or {$obj->pole_dop[3]}=0 )    ");
//\Zend\Debug\Debug::dump($arr);
    return $arr;
}

//запись строки
if ($action==-2){
    //получить UserManager - экземпляр
    $UserManager=$obj->container->get(UserManager::class);
    $user=$UserManager->addUser ($tab_rec);
    if ((int)$obj->pole_dop[3]>0){
        //добавим группу которая выбрана в фильтре интерфейса
        $UserManager->setGroupIds($user->getId(),[(int)$obj->pole_dop[3]]);
    }
    
}

//удаление строки
if ($action==-3){
    //$tab_rec - идентификатор строки
}


return true;
}

}
