<?php
namespace Admin;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Zend\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Таблица доступов",
        "options" => [
            "container" => "acl",
            "podval" =>"<br/><b>Формат данных:<br>Владелец:Группа код_доступа в восьмеричной системе аналогично UNIX</b>",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*редактирование*/
            "edit"=>[
                "cache" =>[
                    "tags"=>["permissions"],
                    "keys"=>["permissions"],
                ],
                "db"=>[ 
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            "add"=>[
                "db"=>[ 
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            //удаление записи
            "del"=>[
                "cache" =>[
                    "tags"=>["permissions"],
                    "keys"=>["permissions"],
                ],
                "db"=>[ 
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*внешний вид*/
            "layout"=>[
                "caption" => "Таблица доступов",
                "height" => "auto",
                "width" => 1000,
                "rowNum" => 20,
                "rowList" => [10,30,100],
                "sortname" => "name",
                "sortorder" => "asc",
                "viewrecords" => true,
                "autoencode" => true,
                //"autowidth"=>true,
                "hidegrid" => false,
                "toppager" => true,
                "rownumbers" => false,
                "navgrid" => [
                    "button" => NavGridHelper::Button(),
                    "editOptions"=>NavGridHelper::editOptions(),
                    "addOptions"=>NavGridHelper::addOptions(),
                    "delOptions"=>NavGridHelper::delOptions(),
                    "viewOptions"=>NavGridHelper::viewOptions(),
                    "searchOptions"=>NavGridHelper::searchOptions(),
                ],
                "colModel" => [
                    ColModelHelper::text("name",["label"=>"Имя объекта","width"=>400]),
                    ColModelHelper::text("object",["label"=>"Объект","width"=>400]),
                    ColModelHelper::permissions("mode",["label"=>"Доступ","width"=>400]),
                    ColModelHelper::cellActions(),
                ],
            ],
        ],
];