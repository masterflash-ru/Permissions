<?php
namespace Mf\Permissions\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Mf\Permissions\Service\Acl;

class AclFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $acl=$container->get(Acl::class);
        $config=$container->get("config");
        return new $requestedName($acl,$config["permission"]["view_helpers"]);
    }
}

