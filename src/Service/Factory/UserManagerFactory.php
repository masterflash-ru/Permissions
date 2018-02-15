<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;


/**
 */
class UserManagerFactory
{
    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $connection=$container->get('ADO\Connection');
                        
        return new $requestedName($connection);
    }
}
