<?php 

$diConfig = $di->getShared('config');

$diConfig->extendConfigs($config);

$config = $diConfig; unset($diConfig);

$view = $di->getShared('view');
$view->setTemplateAfter('layout');
$view->setBasePath($config['application']['viewsBasePath']);
$di->set('view', $view, true);

$router = $di->getShared('router');
$router->setControllerDir($config['application']['controllerDir']);
$router->setNamespace('\\'.$namespace.'\\Controller');
$di->set('router', $router, true);

$di->set('adminMenu', function() use ($di) {
	
	$adminMenu = new \WPOptimizeByxTraffic\Application\Module\Backend\Service\AdminMenu($di);
	
	return $adminMenu;
	
}, true);


$di->set('adminPage', function() use ($di) {
	
	$adminPage = new \WPOptimizeByxTraffic\Application\Module\Backend\Service\AdminPage($di);
	
	return $adminPage;
	
}, true);


$di->set('config', function() use ($config) {
	return $config;
});
