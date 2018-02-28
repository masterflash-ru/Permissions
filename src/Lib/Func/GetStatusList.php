<?php
namespace Mf\Permissions\Lib\Func;


class GetStatusList
{


function __invoke($obj,$infa,$struct_arr,$pole_type,$pole_dop,$tab_name,$idname,$const,$id,$action)

{

$status=$obj->config["permission"]["users_status"];


$obj->dop_sql['name']=$status;
$obj->dop_sql['id']=array_keys($status);
return $infa;

}



}