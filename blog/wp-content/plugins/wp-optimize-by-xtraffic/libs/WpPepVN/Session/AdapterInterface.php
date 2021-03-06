<?php 
namespace WpPepVN\Session;

/**
 * WpPepVN\Session\AdapterInterface
 *
 * Interface for WpPepVN\Session adapters
 */
interface AdapterInterface
{

	/**
	 * Starts session, optionally using an adapter
	 */
	public function start();

	/**
	 * Sets session options
	 */
	public function setOptions($options);

	/**
	 * Get internal options
	 */
	public function getOptions();

	/**
	 * Gets a session variable from an application context
	 *
	 * @param string index
	 * @param mixed defaultValue
	 * @return mixed
	 */
	public function get($index, $defaultValue = null);

	/**
	 * Sets a session variable in an application context
	 *
	 * @param string index
	 * @param string value
	 */
	public function set($index, $value);

	/**
	 * Check whether a session variable is set in an application context
	 */
	public function has($index);

	/**
	 * Removes a session variable from an application context
	 */
	public function remove($index);

	/**
	 * Returns active session id
	 */
	public function getId();

	/**
	 * Check whether the session has been started
	 */
	public function isStarted();

	/**
	 * Destroys the active session
	 */
	public function destroy();

}