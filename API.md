API сервиса контроля доступа

Используется пространство имен Mf\Permissions.


Сервис Mf\Permissions\Service\Acl

Вызов | описание
------|--------------
isAllowed($action,$resource):bollean | Возвращает разрешение на доступ true|false к объекту $resource (строка), запрос доступа в $action - символ r,w,x,d 
 
Помощник Mf\Permissions\View\Helper\Acl

Вызов | описание
------|--------------
__invoke($resource = null) | Магический метод при обращении из сценария вывода. При $resource = null - возвращается экземпляр данного объекта 
isAllowed($permission,$resource=null) | проверить разрешение на доступ, повторяет аналогичный метод, сервиса Mf\Permissions\Service\Acl, если $resource=null - берется ресурс который был указан в __invoke
setResource($resource) | Установить ресурс как текущий
getResource(): string| Возвращает текущий ресурс, к которому проверяется доступ
GetAclService():object | Возвращает сервис Mf\Permissions\Service\Acl

Помощник Mf\Statpage\Controller\Plugin

Все вызовы полностью соответсвуют помощнику Mf\Permissions\View\Helper

