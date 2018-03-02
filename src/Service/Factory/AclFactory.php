<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;

use Mf\Permissions\Service\User;

/**
 */
class AclFactory
{
    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $connection=$container->get('ADO\Connection');
        $config=$container->get('config');
        $user=$container->get(User::class);
        return new $requestedName($connection,$config,$user);
    }
}
