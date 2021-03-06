<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;

use Mf\Users\Service\User;

/**
 */
class AclFactory
{
    /**
     * 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $connection=$container->get('DefaultSystemDb');
        $user=$container->get(User::class);
        $config=$container->get('config');
        $cache = $container->get('DefaultSystemCache');
        return new $requestedName($connection,$user,$cache,$config["permission"]);
    }
}
