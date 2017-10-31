<?php 
namespace WpPepVN\DependencyInjection;

use WpPepVN\DependencyInjection
    , WpPepVN\DependencyInjection\InjectionAwareInterface
	, WpPepVN\Event\ManagerInterface
	, WpPepVN\Event\EventsAwareInterface
	, WpPepVN\DependencyInjection\Exception
;

abstract class Injectable implements InjectionAwareInterface, EventsAwareInterface
{
	public function __construct() 
    {
		
	}
	
	/**
	 * Dependency Injector
	 *
	 * @var \WpPepVN\DependencyInjection
	 */
	protected $_dependencyInjector;
	
	/**
	 * Events Manager
	 *
	 * @var \WpPepVN\Event\ManagerInterface
	 */
	protected $_eventsManager;

	/**
	 * Sets the dependency injector
	 */
	public function setDI(DependencyInjection $dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns the internal dependency injector
	 */
	public function getDI()
	{
        if(!is_object($this->_dependencyInjector)) {
            $this->_dependencyInjector = DependencyInjection::getDefault(null);
        }
		
		return $this->_dependencyInjector;
	}
    
	/**
	 * Sets the event manager
	 */
	public function setEventsManager(ManagerInterface $eventsManager)
	{
		$this->_eventsManager = $eventsManager;
	}

	/**
	 * Returns the internal event manager
	 */
	public function getEventsManager()
	{
		if(!$this->_eventsManager) {
			$this->_eventsManager = $this->getDI()->getShared('eventsManager');
		}
		return $this->_eventsManager;
	}

	/**
	 * Magic method __get
	 */
	public function __get($propertyName)
	{
		
        $this->getDI();
		
        if(!is_object($this->_dependencyInjector)) {
            throw new \Exception("A dependency injection object is required to access the application services");
        }

		/**
		 * Fallback to the PHP userland if the cache is not available
		 */
        
		if($this->_dependencyInjector->has($propertyName)) {
			$this->{$propertyName} = $this->_dependencyInjector->getShared($propertyName);
			return $this->{$propertyName};
		}

		if($propertyName === 'di') {
			return $this->_dependencyInjector;
		}
        
		/**
		 * A notice is shown if the property is not defined and isn't a valid service
		 */
		trigger_error("Access to undefined property " . $propertyName);
        
		return null;
	}
}
