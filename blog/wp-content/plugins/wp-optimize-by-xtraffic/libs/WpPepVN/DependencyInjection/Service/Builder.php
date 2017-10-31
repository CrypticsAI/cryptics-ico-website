<?php 
namespace WpPepVN\DependencyInjection\Service;

/**
 * WpPepVN\DependencyInjection\Service\Builder
 *
 * This class builds instances based on complex definitions
 */

class Builder
{

	/**
	 * Resolves a constructor/call parameter
	 *
	 * @param WpPepVN\DependencyInjection dependencyInjector
	 * @param int position
	 * @param array argument
	 * @return mixed
	 */
	private function _buildParameter($dependencyInjector, int $position, array $argument)
	{
		
        if(!isset($argument['type'])) {
            throw new \Exception("Argument at position " . $position . " must have a type");
        }
        switch ($argument['type']) {
            /**
			 * If the argument type is 'service', we obtain the service from the DI
			 */
			case 'service' :
                if(!isset($argument['name'])) {
					throw new \Exception("Service 'name' is required in parameter on position " . $position);
				}
                if(gettype($dependencyInjector) !== 'object') {
                    throw new \Exception("The dependency injector container is not valid");
                }
                
				return $dependencyInjector->get($argument['name']);

			/**
			 * If the argument type is 'parameter', we assign the value as it is
			 */
			case 'parameter':
                if(!isset($argument['value'])) {
                    throw new \Exception("Service 'value' is required in parameter on position " . $position);
                }
				
				return $argument['value'];

			/**
			 * If the argument type is 'instance', we assign the value as it is
			 */
			case 'instance':
                if(!isset($argument['className'])) {
                    throw new \Exception("Service 'className' is required in parameter on position " . $position);
                }
                
				if(gettype($dependencyInjector) !== 'object') {
					throw new \Exception("The dependency injector container is not valid");
				}
                
                if(isset($argument['arguments'])) {
				
					/**
					 * Build the instance with arguments
					 */
					return $dependencyInjector->get($argument['className'], $argument['arguments']);
				}

				/**
				 * The instance parameter does not have arguments for its constructor
				 */
				return $dependencyInjector->get($argument['className']);

			default:
				/**
				 * Unknown parameter type
				 */
				throw new \Exception("Unknown service type in parameter on position " . $position);
        }
	}

	/**
	 * Resolves an array of parameters
	 */
	private function _buildParameters($dependencyInjector, array $arguments)
	{
		$buildArguments = array();
        
        foreach($arguments as $position => $argument) {
            $buildArguments[] = $this->_buildParameter($dependencyInjector, $position, $argument);
        }
		
		return $buildArguments;
	}
    
	/**
	 * Builds a service using a complex service definition
	 *
	 * @param Phalcon\DiInterface dependencyInjector
	 * @param array definition
	 * @param array parameters
	 * @return mixed
	 */
	public function build($dependencyInjector, array $definition, $parameters = null)
	{

		/**
		 * The class name is required
		 */
        if(!isset($definition['className'])) {
            throw new \Exception("Invalid service definition. Missing 'className' parameter");
        }
		
        if(is_array($parameters)) {

			/**
			 * Build the instance overriding the definition constructor parameters
			 */
            if (!empty($parameters)) {
                $class = new \ReflectionClass($definition['className']);
                $instance = $class->newInstanceArgs($parameters);
            } else {
                $instance = new $definition['className']();
            }
            
		} else {

			/**
			 * Check if the argument has constructor arguments
			 */
            if(isset($definition['arguments'])) {

				/**
				 * Create the instance based on the parameters
				 */
                $class = new \ReflectionClass($definition['className']);
                $instance = $class->newInstanceArgs($this->_buildParameters($dependencyInjector, $definition['arguments']));

			} else {
				$instance = new $definition['className']();
			}
		}
        
		/**
		 * The definition has calls?
		 */
        if(isset($definition['calls'])) {
            
            if(!is_object($instance)) {
				throw new \Exception("The definition has setter injection parameters but the constructor didn't return an instance");
			}
			
            if(!is_array($definition['calls'])) {
				throw new \Exception("Setter injection parameters must be an array");
			}

			/**
			 * The method call has parameters
			 */
             
            foreach($definition['calls'] as $methodPosition => $method) {
                
				/**
				 * The call parameter must be an array of arrays
				 */
				if(!is_array($method['calls'])) {
					throw new \Exception("Method call must be an array on position " . $methodPosition);
				}

				/**
				 * A param 'method' is required
				 */
                if(!isset($method['method'])) {
					throw new \Exception("The method name is required on position " . $methodPosition);
				}
                
                if(isset($method['arguments'])) {
					if(!is_array($method['arguments'])) {
						throw new \Exception("Call arguments must be an array " . $methodPosition);
					}

					if(!empty($method['arguments'])) {
                        
						/**
						 * Call the method on the instance
						 */
						call_user_func_array(array(
                            $instance
                            , $method['method']
                        ), $this->_buildParameters($dependencyInjector, $method['arguments']));

						/**
						 * Go to next method call
						 */
						continue;
					}

				}

				/**
				 * Call the method on the instance without arguments
				 */
				call_user_func(array(
                    $instance
                    , $method['method']
                ));
			}

		}

		/**
		 * The definition has properties?
		 */
        if(isset($definition['properties'])) {
			
			if(!is_object($instance)) {
                throw new \Exception("The definition has properties injection parameters but the constructor didn't return an instance");
            }
			
			if(!is_array($definition['properties'])) {
				throw new \Exception("Setter injection parameters must be an array");
			}

			/**
			 * The method call has parameters
			 */
			//for propertyPosition, property in paramCalls {
            foreach($definition['properties'] as $propertyPosition => $property) {
                
				/**
				 * The call parameter must be an array of arrays
				 */
				if(!is_array($definitionproperty)) {
					throw new \Exception("Property must be an array on position " . $propertyPosition);
				}

				/**
				 * A param 'name' is required
				 */
                if(!isset($property['name'])) {
					throw new \Exception("The property name is required on position " . $propertyPosition);
				}

				/**
				 * A param 'value' is required
				 */
                if(!isset($property['value'])) {
					throw new \Exception("The property value is required on position " . $propertyPosition);
				}

				/**
				 * Update the public property
				 */
				$instance->{$property['name']} = $this->_buildParameter($dependencyInjector, $propertyPosition, $property['value']);
			}
            
		}

		return $instance;
	}
}