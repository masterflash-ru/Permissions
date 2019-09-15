<?php
namespace Mf\Permissions\Service\Admin\JqGrid\Plugin;

use Admin\Service\JqGrid\Plugin\AbstractPlugin;


class LoadPermissions extends AbstractPlugin
{

    protected $config;
    
    public function __construct($config)
    {
        $this->config=$config;
    }


 public function iread()
 {

     $rez["rows"]=[];
     $i=0;
     foreach ($this->config as $k=>$v){
         $rez["rows"][]=["id"=>++$i,"name"=>$k,"mode"=>$v[2],"owner_user"=>$v[0],"owner_group"=>$v[1]];
     }
     return $rez;
 }
}
