<?php 
namespace WpPepVN\DependencyInjection;

use WpPepVN\DependencyInjection
	,WpPepVN\DependencyInjection\ServiceInterface
	,WpPepVN\DependencyInjection\Service\Builder as ServiceBuilder
;

class Service implements ServiceInterface
{
    protected $_name;

	protected $_definition;

	protected $_shared = false;

	protected $_resolved = false;

	protected $_sharedInstance;
    
    
    public final function __construct($name, $definition, $shared = false)
	{
		$this->_name = (string)$name;
        $this->_definition = $definition;
        $this->_shared = (boolean)$shared;
	}
    
    /**
	 * Returns the service's name
	 */
	public function getName()
	{
		return $this->_name;
	}
    
    /**
	 * Sets if the service is shared or not
	 */
	public function setShared(boolean $shared)
	{
		$this->_shared = $shared;
	}

	/**
	 * Check whether the service is shared or not
	 */
	public function isShared()
	{
		return $this->_shared;
	}

	/**
	 * Sets/Resets the shared instance related to the service
	 *
	 * @param mixed sharedInstance
	 */
	public function setSharedInstance($sharedInstance)
	{
		$this->_sharedInstance = $sharedInstance;
	}

	/**
	 * Set the service definition
	 *
	 * @param mixed definition
	 */
	public function setDefinition($definition)
	{
		$this->_definition = $definition;
	}

	/**
	 * Returns the service definition
	 *
	 * @return mixed
	 */
	public function getDefinition()
	{
		return $this->_definition;
	}

	/**
	 * Resolves the service
	 *
	 * @param array parameters
	 * @param Phalcon\DiInterface dependencyInjector
	 * @return mixed
	 */
	public function resolve($parameters = null, DependencyInjection $dependencyInjector = null)
	{
		
		$shared = $this->_shared;

		/**
		 * Check if the service is shared
		 */
		if ($shared) {
            if($this->_sharedInstance !== null) {
                return $this->_sharedInstance;
            }
		}

		$found = true;
        $instance = null;

		if(is_string($this->_definition)) {

			/**
			 * String definitions can be class names without implicit parameters
			 */
			if(class_exists($this->_definition)) {
                if(is_array($parameters)) {
					if (!empty($parameters)) {
                        $class = new \ReflectionClass($this->_definition);
                        $instance = $class->newInstanceArgs($parameters);
					} else {
                        $instance = new $this->_definition();
					}
				} else {
					$instance = new $this->_definition();
				}
			} else {
                $found = false;
			}
		} else {
            
			/**
			 * Object definitions can be a Closure or an already resolved instance
			 */
            if(is_object($this->_definition)) {
				if ($this->_definition instanceof \Closure) {
					if(is_array($parameters)) {
						$instance = call_user_func_array($this->_definition, $parameters);
					} else {
						$instance = call_user_func($this->_definition);
					}
				} else {
					$instance = $this->_definition;
				}
			} else {
				/**
				 * Array definitions require a 'className' parameter
				 */
                if(is_array($this->_definition)) {
					$builder = new ServiceBuilder();
                    $instance = $builder->build($dependencyInjector, $this->_definition, $parameters);
				} else {
					$found = false;
				}
			}
		}

		/**
		 * If the service can't be built, we must throw an exception
		 */
		if ($found === false)  {
			//throw new Exception("Service '" . this->_name . "' cannot be resolved");
            return null;
		}

		/**
		 * Update the shared instance if the service is shared
		 */
		if ($shared) {
			$this->_sharedInstance = $instance;
		}

		$this->_resolved = true;
		
		if ($shared) {
			return $this->_sharedInstance;
		} else {
			//$instance1 = clone $instance;
			return $instance;
		}
	}
    
    
    /**
	 * Changes a parameter in the definition without resolve the service
	 */
	public function setParameter(int $position, array $parameter)
	{
		 if(!is_array($this->_definition)) {
			throw new \Exception("Definition must be an array to update its parameters");
		}

		/**
		 * Update the parameter
		 */
        if(isset($this->_definition['arguments'])) {
            $arguments = $this->_definition['arguments'];
            $arguments[$position] = $parameter;
        } else {
            $arguments[$position] = $parameter;
        }
		
		/**
		 * Re-update the arguments
		 */
		$this->_definition['arguments'] = $arguments;
        
		return $this;
	}

	/**
	 * Returns a parameter in a specific position
	 *
	 * @param int position
	 * @return array
	 */
	public function getParameter(int $position)
	{
         if(!is_array($this->_definition)) {
			throw new \Exception("Definition must be an array to obtain its parameters");
		}

		/**
		 * Update the parameter
		 */
        if(isset($this->_definition['arguments'])) {
            if(isset($this->_definition['arguments'][$position])) {
                return $this->_definition['arguments'][$position];
            }
        }
        
		return null;
	}
    
	/**
	 * Returns true if the service was resolved
	 */
	public function isResolved()
	{
		return $this->_resolved;
	}

	/**
	 * Restore the internal state of a service
	 */
	public static function __set_state(array $attributes)
	{
		
        if(isset($attributes['_name'])) {
            $name = $attributes['_name'];
        } else {
            throw new \Exception('The attribute "_name" is required');
        }
        
        if(isset($attributes['_definition'])) {
            $definition = $attributes['_definition'];
        } else {
            throw new \Exception('The attribute "_definition" is required');
        }
        
        if(isset($attributes['_shared'])) {
            $shared = $attributes['_shared'];
        } else {
            throw new \Exception('The attribute "_shared" is required');
        }
        
		return new self($name, $definition, $shared);
	}
}
