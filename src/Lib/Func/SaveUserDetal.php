<?php
namespace Mf\Permissions\Lib\Func;

use ADO\Service\RecordSet;

class SaveUserDetal
{


public function __invoke($obj,$tab_rec,$struct0,$struct2,$tab_name,$const,$row_item,$a,$b,$action)
{


    //запись строки
    if ($action==-2) {
        $rs=new RecordSet();
        $rs->CursorType = adOpenKeyset;
        $rs->open("SELECT * FROM users where id=".(int)$tab_rec['id'],$obj->connection);
        
        if (trim($tab_rec["gr"])) {
            $parent_group=explode(",",$tab_rec["gr"]);
        } else {$parent_group=[];}
        $id=(int)$tab_rec['id'];

        //редактирвоание
        //удалим старые связи
        $obj->connection->Execute("delete from users2group where users=".(int)$tab_rec['id']);
        $rs->Fields->Item['login']->Value=$tab_rec['login'];
        $rs->Fields->Item['name']->Value=$tab_rec['name'];
        $rs->Fields->Item['full_name']->Value=$tab_rec['full_name'];
       
        if ($id>9){
            /*менять статусы и остальное можно только для не системных юзеров*/
            $rs->Fields->Item['status']->Value=$tab_rec['status'];
            $rs->Fields->Item['date_registration']->Value=$tab_rec['date_registration'];
            $rs->Fields->Item['date_last_login']->Value=$tab_rec['date_last_login'];

        }
        
        
        $rs->Update();
        

    //добавляем  связи
    if (!empty($parent_group)){
        $rs1=new RecordSet();
        $rs1->CursorType = adOpenKeyset;
        $rs1->open("SELECT * FROM users2group where users={$id}",$obj->connection);
        
        foreach ($parent_group as $parent_id){
            $rs1->AddNew();
            $rs1->Fields->Item['users']->Value=$id;
            $rs1->Fields->Item['users_group']->Value=$parent_id;
            $rs1->Update();
        }
        
    }


	}
return true;
}

}
