<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;
use Mf\Permissions\Service\RbacManager;
use Zend\Authentication\AuthenticationService;

/**
фабрика
 */
class RbacManagerFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
		 $connection=$container->get('ADO\Connection');
		 
        $authService = $container->get(AuthenticationService::class);
        $cache = $container->get('DefaultSystemCache');
		
        $assertionManagers = [];
        $config = $container->get('Config');
        if (isset($config['rbac_manager']['assertions'])) {
            foreach ($config['rbac_manager']['assertions'] as $serviceName) {
                $assertionManagers[$serviceName] = $container->get($serviceName);
            }
        }
        
        return new RbacManager($connection, $authService, $cache, $assertionManagers);
    }
}

