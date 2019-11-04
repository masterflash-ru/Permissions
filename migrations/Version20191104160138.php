<?php

namespace Mf\Permissions;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;

class Version20191104160138 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Create table for permissions";

    public function up($schema)
    {
        switch ($this->db_type){
            case "mysql":{
                $this->addSql("CREATE TABLE `permissions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` char(127) DEFAULT NULL COMMENT 'просто описание',
                  `object` char(127) DEFAULT NULL COMMENT 'строка объекта',
                  `mode` int(11) DEFAULT NULL COMMENT 'код доступа как в unix',
                  `owner_user` int(11) DEFAULT NULL COMMENT 'ID владельца-юзера',
                  `owner_group` int(11) DEFAULT NULL COMMENT 'ID владельца-группы',
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COMMENT='список объектов доступа'");
                break;
            }
            default:{
                throw new \Exception("the database {$this->db_type} is not supported !");
            }
        }
    }

    public function down($schema)
    {
        switch ($this->db_type){
            case "mysql":{
                $this->addSql("DROP TABLE `permissions`");
                break;
            }
            default:{
                throw new \Exception("the database {$this->db_type} is not supported !");
            }
        }
    }
}
