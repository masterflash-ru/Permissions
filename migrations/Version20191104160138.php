<?php

namespace Mf\Permissions;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;
use Laminas\Db\Sql\Ddl;

class Version20191104160138 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Create table for permissions";

    public function up($schema, $adapter)
    {
        $this->mysql_add_create_table=" ENGINE=MyIsam DEFAULT CHARSET=utf8";
        $table = new Ddl\CreateTable("permissions");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Char('name', 127,false,"",["COMMENT"=>"Описание объекта"]));
        $table->addColumn(new Ddl\Column\Char('object',255,false,"",["COMMENT"=>"Объект, его хеш или имя"]));
        $table->addColumn(new Ddl\Column\Integer('mode',true,0,["COMMENT"=>"код доступа в как в UNIX"]));
        $table->addColumn(new Ddl\Column\Integer('owner_user',true,0,["COMMENT"=>"ID владельца-юзера"]));
        $table->addColumn(new Ddl\Column\Integer('owner_group',true,0,["COMMENT"=>"ID владельца-группы"]));
        
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $this->addSql($table);
    }

    public function down($schema, $adapter)
    {
        $drop = new Ddl\DropTable('permissions');
        $this->addSql($drop);
    }
}
