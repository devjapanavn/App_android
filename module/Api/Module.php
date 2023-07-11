<?php
namespace Api;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\Feature;

class Module
{
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($event) {
            $controller      = $event->getTarget();
            $controllerName  = get_class($controller);
            $moduleNamespace = substr($controllerName, 0, strpos($controllerName, '\\'));
            $configs         = $event->getApplication()->getServiceManager()->get('config');
            if (isset($configs['moduleLayouts'][$moduleNamespace])) {
                $controller->layout($configs['moduleLayouts'][$moduleNamespace]);
            }
        }, 100);
       $eventManager->attach('dispatch.error', function($e) {

// 			header("HTTP/1.1 301 Moved Permanently");
//            header("location: ".URL);
//            exit();
       });
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}