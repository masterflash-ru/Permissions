<?php
namespace Mf\Permissions\Lib\Func;

use Mf\Permissions\Service\UserManager;

class SaveUserDetal
{


public function __invoke($obj,$tab_rec,$struct0,$struct2,$tab_name,$const,$row_item,$a,$b,$action)
{


    //запись строки
    if ($action==-2) {
        $UserManager=$obj->container->get(UserManager::class);
        
        if (trim($tab_rec["gr"])) {
            $parent_group=explode(",",$tab_rec["gr"]);
        } else {$parent_group=[];}
        $id=(int)$tab_rec['id'];

       
        if ($id>9){
            /*менять статусы и остальное можно только для не системных юзеров*/
            $UserManager->updateUserInfo($id,$tab_rec);
            $UserManager->setGroupIds($id,$parent_group);
        }


	}
return true;
}

}
