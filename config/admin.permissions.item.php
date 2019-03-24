<?php
/**
* конфиг формы для редактирования доступа к выбранному элементу
*/


namespace Mf\Permissions;



return [

        "type" => "izform",
        "options" => [
            "container" => "zfacl",
            "podval" =>"",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*внешний вид*/
            "layout"=>[
                "rowModel" => [
                ],
            ],
        ],
];