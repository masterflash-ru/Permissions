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
        $config=$container->get('config');
        $user=$container->get(User::class);
        return new $requestedName($config,$user);
    }
}
