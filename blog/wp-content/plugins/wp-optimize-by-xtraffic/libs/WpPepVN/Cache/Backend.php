<?php
namespace WpPepVN\Cache;

use WpPepVN\Cache\FrontendInterface;

/**
 * WpPepVN\Cache\Backend
 *
 * This class implements common functionality for backend adapters. A backend cache adapter may extend this class
 */
abstract class Backend implements BackendInterface
{

	protected $_frontend;

	protected $_options;

	protected $_prefix = '';

	protected $_lastKey;

	protected $_lastLifetime;

	protected $_fresh = false;

	protected $_started = false;

	/**
	 * Phalcon\Cache\Backend constructor
	 *
	 * @param	Phalcon\Cache\FrontendInterface frontend
	 * @param	array options
	 */
	public function __construct(FrontendInterface $frontend, $options = null)
	{
		
		/**
		 * A common option is the prefix
		 */
		if(isset($options['prefix'])) {
			$this->_prefix = $options['prefix'];
		}

		$this->_frontend = $frontend;
		$this->_options = $options;
	}

	/**
	 * Starts a cache. The keyname allows to identify the created fragment
	 *
	 * @param   int|string keyName
	 * @param   int lifetime
	 * @return  mixed
	 */
	public function start($keyName, $lifetime = null)
	{
		
		/**
		 * Get the cache content verifying if it was expired
		 */
		$existingCache = $this->{'get'}($keyName, $lifetime);

		if ($existingCache === null) {
			$fresh = true;
			$this->_frontend->start();
		} else {
			$fresh = false;
		}

		$this->_fresh = $fresh;
		$this->_started = true;

		/**
		 * Update the last lifetime to be used in save()
		 */
		if(!is_null($lifetime)) {
			$this->_lastLifetime = $lifetime;
		}

		return $existingCache;
	}

	/**
	 * Stops the frontend without store any cached content
	 */
	public function stop($stopBuffer = true)
	{
		if ($stopBuffer === true) {
			$this->_frontend->stop();
		}
		$this->_started = false;
	}

	/**
	 * Checks whether the last cache is fresh or cached
	 */
	public function isFresh()
	{
		return $this->_fresh;
	}

	/**
	 * Checks whether the cache has starting buffering or not
	 */
	public function isStarted()
	{
		return $this->_started;
	}

	/**
	 * Gets the last lifetime set
	 *
	 * @return int
	 */
	public function getLifetime()
	{
		return $this->_lastLifetime;
	}
	
	public function getOptions()
	{
		return $this->_options;
	}
	
	public function getFrontend()
	{
		return $this->_frontend;
	}
	
	public function setLastKey($lastKey)
	{
		$this->_lastKey = $lastKey;
	}
	
	public function getLastKey()
	{
		return $this->_lastKey;
	}
	
}
