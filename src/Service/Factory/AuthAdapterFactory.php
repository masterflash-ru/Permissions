<?php
namespace Mf\Permissions\Service\Factory;

use Interop\Container\ContainerInterface;
use Mf\Permissions\Service\AuthAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
фабрика адаптера авторизацйии
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * собсвтенно генератор объекта адаптера генератора, передаем в сам объект соединение с базой
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
        return new AuthAdapter($connection);
    }
}
