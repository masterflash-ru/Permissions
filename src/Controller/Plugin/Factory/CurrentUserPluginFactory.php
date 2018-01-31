<?php
namespace Admin\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Admin\Controller\Plugin\CurrentUserPlugin;
use Zend\Authentication\AuthenticationService;

class CurrentUserPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {        
        $connection=$container->get('ADO\Connection');
        $authService = $container->get(AuthenticationService::class);
        
        return new CurrentUserPlugin($connection, $authService);
    }
}


