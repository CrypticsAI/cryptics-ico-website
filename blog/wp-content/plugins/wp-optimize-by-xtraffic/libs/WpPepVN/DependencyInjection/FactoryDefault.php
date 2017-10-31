<?php
namespace WpPepVN\DependencyInjection;

/**
 * WpPepVN\DependencyInjection\FactoryDefault
 *
 * This is a variant of the standard WpPepVN\Di. By default it automatically
 * registers all the services provided by the framework. Thanks to this, the developer does not need
 * to register each service individually providing a full stack framework
 */
class FactoryDefault extends \WpPepVN\DependencyInjection
{
    
	/**
	 * WpPepVN\DependencyInjection\FactoryDefault constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_services = array(
			/*
			'router' =>              new Service('router', 'WpPepVN\\Mvc\\Router', true),
			'dispatcher' =>          new Service('dispatcher', 'WpPepVN\\Mvc\\Dispatcher', true),
			'url' =>                 new Service('url', 'WpPepVN\\Mvc\\Url', true),
			'modelsManager' =>       new Service('modelsManager', 'WpPepVN\\Mvc\\Model\\Manager', true),
			'modelsMetadata' =>      new Service('modelsMetadata', 'WpPepVN\\Mvc\\Model\\MetaData\\Memory', true),
			'response' =>            new Service('response', 'WpPepVN\\Http\\Response', true),
			'cookies' =>             new Service('cookies', 'WpPepVN\\Http\\Response\\Cookies', true),
			'request' =>             new Service('request', 'WpPepVN\\Http\\Request', true),
			'filter' =>              new Service('filter', 'WpPepVN\\Filter', true),
			
			'security' =>            new Service('security', 'WpPepVN\\Security', true),
			'crypt' =>               new Service('crypt', 'WpPepVN\\Crypt', true),
			'annotations' =>         new Service('annotations', 'WpPepVN\\Annotations\\Adapter\\Memory', true),
			'flash' =>               new Service('flash', 'WpPepVN\\Flash\\Direct', true),
			'flashSession' =>        new Service('flashSession', 'WpPepVN\\Flash\\Session', true),
			'tag' =>                 new Service('tag', 'WpPepVN\\Tag', true),
			'session' =>             new Service('session', 'WpPepVN\\Session\\Adapter\\Files', true),
			'sessionBag' =>          new Service('sessionBag', 'WpPepVN\\Session\\Bag'),
			'eventsManager' =>       new Service('eventsManager', 'WpPepVN\\Events\\Manager', true),
			'transactionManager' =>  new Service('transactions', 'WpPepVN\\Mvc\\Model\\Transaction\\Manager', true),
			'assets' =>              new Service('assets', 'WpPepVN\\Assets\\Manager', true)
			*/
			'router' =>              new Service('router', '\\WpPepVN\\Mvc\\Router', true),
			'view' =>              	 new Service('view', '\\WpPepVN\\Mvc\\View', true),
			'url' =>				 new Service('url', '\\WpPepVN\\Mvc\\Url', true),
			'crypt' =>               new Service('crypt', '\\WpPepVN\\Crypt', true),
			'filter' =>              new Service('filter', '\\WpPepVN\\Filter', true),
			'session' =>             new Service('session', '\\WpPepVN\\Session\\Adapter\\Files', true),
			'cookies' =>             new Service('cookies', '\\WpPepVN\\Http\\Response\\Cookies', true),
			'eventsManager' =>       new Service('eventsManager', '\\WpPepVN\\Event\\Manager', true),
			'response' =>            new Service('response', '\\WpPepVN\\Http\\Response', true),
			'request' =>             new Service('request', '\\WpPepVN\\Http\\Request', true),
			'escaper' =>             new Service('escaper', 'WpPepVN\\Escaper', true),
			'translate' =>           new Service('translate', 'WpPepVN\\Translate', true),
			'validation' =>          new Service('translate', 'WpPepVN\\Validation', true),
			'hook' =>           	 new Service('translate', 'WpPepVN\\Hook', true),
			//'remote' =>				 new Service('remote', 'WpPepVN\\Remote', true),
		);
		
	}
    
}