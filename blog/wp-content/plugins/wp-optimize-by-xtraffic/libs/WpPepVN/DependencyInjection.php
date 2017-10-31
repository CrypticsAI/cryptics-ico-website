<?php 
namespace WpPepVN;

use WpPepVN\DependencyInjection\Service
	,WpPepVN\DependencyInjection\ServiceInterface
	,WpPepVN\DependencyInjection\InjectionAwareInterface
	,WpPepVN\DependencyInjection\Store as DIStore
	,WpPepVN\DependencyInjectionInterface
;

class DependencyInjection implements DependencyInjectionInterface
{
    /**
	 * List of registered services
	 */
	protected $_services;

	/**
	 * List of shared instances
	 */
	protected $_sharedInstances;

	/**
	 * To know if the latest resolved instance was shared or not
	 */
	protected $_freshInstance = false;
    

	/**
	 * Latest DI build
	 */
	//protected static $_default = array();
	
	
	private $_DIID = null;

	/**
	 * DependencyInjection constructor
	 */
	public function __construct()
	{
		$this->_DIID = Utils::hashKey(Utils::randomHash());
		if (!isset(DIStore::$instances[$this->_DIID])) {
			DIStore::$instances[$this->_DIID] = $this;
		}
	}
	
	public function getDIID()
	{
		return $this->_DIID;
	}
    
	public function newDIID()
	{
		$newDIID = Utils::hashKey(Utils::randomHash());
		$this->_DIID = $newDIID;
		DIStore::$instances[$newDIID] = $this;
		return DIStore::$instances[$newDIID];
	}
    
	/**
	 * Registers a service in the services container
	 */
	public function set($name, $definition, $shared = false)
	{
        $name = (string)$name;
        
		$this->_services[$name] = new Service($name, $definition, $shared);
		
		return $this->_services[$name];
	}

	/**
	 * Registers an "always shared" service in the services container
	 */
	public function setShared($name, $definition)
	{
		$this->_services[$name] = new Service($name, $definition, true);
		return $this->_services[$name];
	}

	/**
	 * Removes a service in the services container
	 */
	public function remove($name)
	{
		unset ($this->_services[$name]);
	}

	/**
	 * Attempts to register a service in the services container
	 * Only is successful if a service hasn"t been registered previously
	 * with the same name
	 */
	public function attempt($name, $definition, $shared = false)
	{
		if (!isset($this->_services[$name])) {
			return $this->set($name, $definition, $shared);
		}
        
		return false;
	}

	/**
	 * Sets a service using a raw Phalcon\Di\Service definition
	 */
	public function setRaw($name, ServiceInterface $rawDefinition)
	{
		$this->_services[$name] = $rawDefinition;
		return $this->_services[$name];
	}

	/**
	 * Returns a service definition without resolving
	 */
	public function getRaw($name)
	{
        if(isset($this->_services[$name])) {
            return $this->_services[$name]->getDefinition();
        }
        
        return null;
		//throw new Exception('Service "'. $name . '" wasn\'t found in the dependency injection container');
	}

	/**
	 * Returns a Phalcon\Di\Service instance
	 */
	public function getService($name)
	{
		if(isset($this->_services[$name])) {
            return $this->_services[$name];
        }
        
        return null;
	}
    
    
	/**
	 * Resolves the service based on its configuration
	 */
	public function get($name, $parameters = null)
	{
        
        if(isset($this->_services[$name])) {
			/**
			 * The service is registered in the DI
			 */
			$instance = $this->_services[$name]->resolve($parameters, $this);
		} else {
			/**
			 * The DI also acts as builder for any class even if it isn't defined in the DI
			 */
			if (!class_exists($name)) {
				throw new \Exception("Service '" . $name . "' wasn't found in the dependency injection container");
			}
			
            if(is_array($parameters)) {
				if (!empty($parameters)) {
                    $class = new \ReflectionClass($name);
                    $instance = $class->newInstanceArgs($parameters);
				} else {
					$instance = new $name();
				}
			} else {
				$instance = new $name();
			}
		}

		/**
		 * Pass the DI itself if the instance implements InjectionAwareInterface
		 */
        if(is_object($instance)) {
			if($instance instanceof InjectionAwareInterface) {
				$instance->setDI($this);
			}
		}
        
		return $instance;
	}

	/**
	 * Resolves a service, the resolved service is stored in the DI, subsequent requests for this service will return the same instance
	 *
	 * @param string name
	 * @param array parameters
	 * @return mixed
	 */
	public function getShared($name, $parameters = null)
	{
		
		/**
		 * This method provides a first level to shared instances allowing to use non-shared services as shared
		 */
        if(isset($this->_sharedInstances[$name])) {
			$this->_freshInstance = false;
		} else {
            
			/**
			 * Resolve the instance normally & Save the instance in the first level shared
			 */
			$this->_sharedInstances[$name] = $this->get($name, $parameters);
            $this->_freshInstance = true;
		}

		return $this->_sharedInstances[$name];
	}

	/**
	 * Check whether the DI contains a service by a name
	 */
	public function has($name)
	{
		return isset($this->_services[$name]);
	}

	/**
	 * Check whether the last service obtained via getShared produced a fresh instance or an existing one
	 */
	public function wasFreshInstance()
	{
		return $this->_freshInstance;
	}

	/**
	 * Return the services registered in the DI
	 */
	public function getServices()
	{
		return $this->_services;
	}

	/**
	 * Check if a service is registered using the array syntax
	 */
	public function offsetExists($name)
	{
		return $this->has($name);
	}

	/**
	 * Allows to register a shared service using the array syntax
	 *
	 *<code>
	 *	$di["request"] = new \Phalcon\Http\Request();
	 *</code>
	 *
	 * @param string name
	 * @param mixed definition
	 * @return boolean
	 */
	public function offsetSet($name, $definition)
	{
		$this->setShared($name, $definition);
		return true;
	}

	/**
	 * Allows to obtain a shared service using the array syntax
	 *
	 * @param string name
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		return $this->getShared($name);
	}

	/**
	 * Removes a service from the services container using the array syntax
	 */
	public function offsetUnset($name)
	{
		return false;
	}

	/**
	 * Magic method to get or set services using setters/getters
	 *
	 * @param string method
	 * @param array arguments
	 * @return mixed
	 */
	public function __call($method, $arguments = null)
	{
		

		/**
		 * If the magic method starts with "get" we try to get a service with that name
		 */
        if(0 === strpos($method,'get')) {
            $possibleService = lcfirst(substr($method, 3));
            if(isset($this->_services[$possibleService])) {
                if(!empty($arguments)) {
                    $instance = $this->get($possibleService, $arguments);
                } else {
                    $instance = $this->get($possibleService);
                }
                return $instance;
            }
        }

		/**
		 * If the magic method starts with "set" we try to set a service using that name
		 */
		
        if(0 === strpos($method,'set')) {
            if(isset($arguments[0])) {
                $this->set(lcfirst(substr($method, 3)), $arguments[0]);
                return null;
            }
		}

		/**
		 * The method doesn't start with set/get throw an exception
		 */
		throw new \Exception("Call to undefined method or service '" . $method . "'");
	}

	/**
	 * Set a default dependency injection container to be obtained into static methods
	 */
	
	public static function setDefault(\WpPepVN\DependencyInjection $dependencyInjector, $DIID)
	{
		DIStore::$instances[$DIID] = $dependencyInjector;
	}
	
	/**
	 * Return the lastest DI created
	 */
	public static function getDefault($DIID)
	{
		return DIStore::$instances[$DIID];
	}

	/**
	 * Resets the internal default DI
	 */
	public static function reset($DIID)
	{
		if($DIID) {
			DIStore::$instances[$DIID] = null;
		} else {
			DIStore::$instances = array();
		}
	}
}
