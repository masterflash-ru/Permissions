<?php
namespace Mf\Permissions\Lib\Func;

use ADO\Service\RecordSet;

class SaveGroupTree
{


public function __invoke($obj,$tab_rec,$struct0,$struct2,$tab_name,$const,$row_item,$a,$b,$action)
{



    //запись строки
    if ($action==-2) {
        $rs=new RecordSet();
        $rs->CursorType = adOpenKeyset;
        $rs->open("SELECT * FROM users_group",$obj->connection);
        if (trim($tab_rec["parent_group"])) {
            $parent_group=explode(",",$tab_rec["parent_group"]);
        } else {$parent_group=[];}
        

            if (empty($tab_rec['id'])){
                //	добавление
                $rs->AddNew();

                $rs->Fields->Item['name']->Value=$tab_rec['name'];
                $rs->Fields->Item['description']->Value=$tab_rec['description'];
                $rs->Update();
                $id=(int)$rs->Fields->Item['id']->Value;
            } else {
                //редактирвоание
                //удалим старые связи
                $obj->connection->Execute("delete from users_group_tree where id=".(int)$tab_rec['id']);

                $rs->Find("id=".(int)$tab_rec['id'],0,adSearchForward);
                $rs->Fields->Item['name']->Value=$tab_rec['name'];
                $rs->Fields->Item['description']->Value=$tab_rec['description'];
                $rs->Update();
                $id=(int)$tab_rec['id'];
            }
    //добавляем в дерево связи
    if (!empty($parent_group)){
        $rs1=new RecordSet();
        $rs1->CursorType = adOpenKeyset;
        $rs1->open("SELECT * FROM users_group_tree where id={$id}",$obj->connection);
        
        foreach ($parent_group as $parent_id){
            $rs1->AddNew();
            $rs1->Fields->Item['id']->Value=$id;
            $rs1->Fields->Item['parent_id']->Value=$parent_id;
            $rs1->Update();
        }
        
    }


	}
return true;
}

}
