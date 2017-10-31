<?php 
namespace WpPepVN;

/**
 * WpPepVN\DependencyInjectionInterface
 *
 * Interface for WpPepVN\DependencyInjection
 */
interface DependencyInjectionInterface extends \ArrayAccess
{

	/**
	 * Registers a service in the services container
	 *
	 * @param string name
	 * @param mixed definition
	 * @param boolean shared
	 */
	public function set($name, $definition, $shared = false);

	/**
	 * Registers an "always shared" service in the services container
	 *
	 * @param string name
	 * @param mixed definition
	 */
	public function setShared($name, $definition);

	/**
	 * Removes a service in the services container
	 */
	public function remove($name);

	/**
	 * Attempts to register a service in the services container
	 * Only is successful if a service hasn't been registered previously
	 * with the same name
	 *
	 * @param string name
	 * @param mixed definition
	 * @param boolean shared
	 */
	public function attempt($name, $definition, $shared = false);

	/**
	 * Resolves the service based on its configuration
	 *
	 * @param string name
	 * @param array parameters
	 * @return mixed
	 */
	public function get($name, $parameters = null);

	/**
	 * Returns a shared service based on their configuration
	 *
	 * @param string name
	 * @param array parameters
	 * @return mixed
	 */
	public function getShared($name, $parameters = null);

	/**
	 * Sets a service using a raw WpPepVN\DependencyInjection\Service definition
	 */
	public function setRaw($name, \WpPepVN\DependencyInjection\ServiceInterface $rawDefinition);

	/**
	 * Returns a service definition without resolving
	 *
	 * @param string name
	 * @return mixed
	 */
	public function getRaw($name);

	/**
	 * Returns the corresponding \WpPepVN\DependencyInjection\Service instance for a service
	 */
	public function getService($name);

	/**
	 * Check whether the DI contains a service by a name
	 */
	public function has($name);

	/**
	 * Check whether the last service obtained via getShared produced a fresh instance or an existing one
	 */
	public function wasFreshInstance();

	/**
	 * Return the services registered in the DI
	 *
	 * @return array
	 */
	public function getServices();
    
	/**
	 * Check if a service is registered using the array syntax
	 */
	//public function offsetExists($name);
    
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
	//public function offsetSet($name, $definition);
    
	/**
	 * Allows to obtain a shared service using the array syntax
	 * @param string name
	 * @return mixed
	 */
	//public function offsetGet($name);
    
	/**
	 * Removes a service from the services container using the array syntax
	 */
	//public function offsetUnset($name);

	/**
	 * Set a default dependency injection container to be obtained into static methods
	 */
	public static function setDefault(\WpPepVN\DependencyInjection $dependencyInjector, $DIID);
    
	/**
	 * Return the last DI created
	 */
	public static function getDefault($DIID);

	/**
	 * Resets the internal default DI
	 */
	public static function reset($DIID);
}