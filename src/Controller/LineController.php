<?php
/**

 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class LineController extends AbstractActionController
{
	protected $container;

public function __construct($container)
{
	$this->container=$container;
	
	/*$this->cache=$cache;
	$this->config=$config;
	$EventManager=new EventManager($SharedEventManager);
	$EventManager->addIdentifiers(["simba.admin"]);
	
	$this->EventManager=$EventManager;
	//$r=$EventManager->trigger("GetControllersInfo");
*/
}



/*собственно вывод, через одно место*/
public function indexAction()
{//\Zend\Debug\Debug::dump(get_class_methods());
		$table=$this->params('table',"");	
  		return new ViewModel(
			["table"=>$table,
			"container"=>$this->container,
			]);
	
}



}
