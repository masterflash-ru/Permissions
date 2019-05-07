<?php
namespace Mf\Permissions\Service\Admin\JqGrid\Plugin;

use Interop\Container\ContainerInterface;
/*

*/

class FactoryLoadPermissions
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $config=$container->get('config');
    return new $requestedName($config["permission"]["objects"]);
}
}

