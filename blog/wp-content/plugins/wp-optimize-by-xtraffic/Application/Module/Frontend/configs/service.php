<?php 

$diConfig = $di->get('config');

$diConfig->extendConfigs($config);

$config = $diConfig; unset($diConfig);

$view = $di->getShared('view');

$view->setTemplateAfter('layout');

$view->setBasePath($config['application']['viewsBasePath']);

$di->set('view', $view);

