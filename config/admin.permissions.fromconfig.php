<?php
namespace Mf\Permissions;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Zend\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Таблица доступов по умолчанию из конфига",
        "options" => [
            "container" => "acl",
            "podval" =>"<br/><b>Формат данных:<br>Владелец:Группа код_доступа в восьмеричной системе аналогично UNIX</b>",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "LoadPermissions"=>[],
            ],
            /*внешний вид*/
            "layout"=>[
                "caption" => "Таблица доступов по умолчанию из конфига",
                "height" => "auto",
                "sortname" => "name",
                "sortorder" => "asc",
                "viewrecords" => true,
                //"autowidth"=>true,
                "hidegrid" => false,
                "rownumbers" => false,
                "navgrid" => [
                    "button" => NavGridHelper::Button(["add"=>false,"edit"=>false,"search"=>false,"view"=>false,"del"=>false]),
                ],
                "colModel" => [
                    ColModelHelper::text("name",["label"=>"Объект","width"=>400]),
                    ColModelHelper::permissions("mode",["label"=>"Доступ","width"=>400]),
                ],
            ],
        ],
];