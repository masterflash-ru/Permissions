<?php
/**
* конфиг формы для редактирования доступа к выбранному элементу
*/


namespace Mf\Permissions;

use Zend\Form\Element;


return [

        "type" => "izform",
        "options" => [
            "container" => "permissions_item",
            "podval" =>"",
            "container-attr"=>"style=\"width:1000px\"",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            
            "layout"=>[
                "rowModel"=>[
                     'elements' => [
                         [
                             'spec' => [
                                 'type' => Element\Text::class,
                                 'name' => 'email',
                                 "attributes"=>[
                                     "placeholder"=>"Введите Email",
                                 ],
                                 'options' => [
                                     'label' => 'Ваш email адрес',
                                 ]
                             ],
                         ],
                         [
                             'spec' => [
                                 'type' => Element\Textarea::class,
                                 'name' => 'message',
                                 'options' => [
                                     'label' => 'Сообщение',
                                 ]
                             ],
                         ],
                            [
                                'spec' => [
                                    'name' => 'submit',
                                    'type' => 'button',
                                 'options' => [
                                     'label' => 'Записать',
                                 ],
                                ],
                            ],
                     ],
                ],
            ],

        
        
        
        ],
];