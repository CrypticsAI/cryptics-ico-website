<?php 
namespace WpPepVN\Cache\Frontend;

use WpPepVN\Cache\FrontendInterface;

/**
 * WpPepVN\Cache\Frontend\Data
 *
 * Allows to cache native PHP data in a serialized form
 *
 *<code>
 *<?php
 *
 *	// Cache the files for 2 days using a Data frontend
 *	$frontCache = new \WpPepVN\Cache\Frontend\Data(array(
 *		"lifetime" => 172800
 *	));
 *
 *	// Create the component that will cache "Data" to a "File" backend
 *	// Set the cache file directory - important to keep the "/" at the end of
 *	// of the value for the folder
 *	$cache = new \WpPepVN\Cache\Backend\File($frontCache, array(
 *		"cacheDir" => "../app/cache/"
 *	));
 *
 *	// Try to get cached records
 *	$cacheKey = 'robots_order_id.cache';
 *	$robots    = $cache->get($cacheKey);
 *	if ($robots === null) {
 *
 *		// $robots is null due to cache expiration or data does not exist
 *		// Make the database call and populate the variable
 *		$robots = Robots::find(array("order" => "id"));
 *
 *		// Store it in the cache
 *		$cache->save($cacheKey, $robots);
 *	}
 *
 *	// Use $robots :)
 *	foreach ($robots as $robot) {
 *		echo $robot->name, "\n";
 *	}
 *</code>
 */
class Data implements FrontendInterface
{

	protected $_frontendOptions;

	/**
	 * WpPepVN\Cache\Frontend\Data constructor
	 *
	 * @param array frontendOptions
	 */
	public function __construct($frontendOptions = null)
	{
		$this->_frontendOptions = $frontendOptions;
	}

	/**
	 * Returns the cache lifetime
	 */
	public function getLifetime()
	{
		if(isset($this->_frontendOptions['lifetime'])) {
			return $this->_frontendOptions['lifetime'];
		}
		return 1;
	}

	/**
	 * Check whether if frontend is buffering output
	 */
	public function isBuffering()
	{
		return false;
	}

	/**
	 * Starts output frontend. Actually, does nothing
	 */
	public function start()
	{

	}

	/**
	 * Returns output cached content
	 *
	 * @return string
	 */
	public function getContent()
	{
		return null;
	}

	/**
	 * Stops output frontend
	 */
	public function stop()
	{

	}

	/**
	 * Serializes data before storing them
	 */
	public function beforeStore($data)
	{
		return serialize($data);
	}

	/**
	 * Unserializes data after retrieval	 
	 */
	public function afterRetrieve($data)
	{
		return unserialize($data);
	}
}
