<?php 
namespace WpPepVN\Cache\Frontend;

use WpPepVN\Cache\FrontendInterface;

/**
 * WpPepVN\Cache\Frontend\None
 *
 * Discards any kind of frontend data input. This frontend does not have expiration time or any other options
 *
 *<code>
 *<?php
 *
 *	//Create a None Cache
 *	$frontCache = new \WpPepVN\Cache\Frontend\None();
 *
 *	// Create the component that will cache "Data" to a "Memcached" backend
 *	// Memcached connection settings
 *	$cache = new \WpPepVN\Cache\Backend\Memcache($frontCache, array(
 *		"host" => "localhost",
 *		"port" => "11211"
 *	));
 *
 *	// This Frontend always return the data as it's returned by the backend
 *	$cacheKey = 'robots_order_id.cache';
 *	$robots    = $cache->get($cacheKey);
 *	if ($robots === null) {
 *
 *		// This cache doesn't perform any expiration checking, so the data is always expired
 *		// Make the database call and populate the variable
 *		$robots = Robots::find(array("order" => "id"));
 *
 *		$cache->save($cacheKey, $robots);
 *	}
 *
 *	// Use $robots :)
 *	foreach ($robots as $robot) {
 *		echo $robot->name, "\n";
 *	}
 *</code>
 */
class None implements FrontendInterface
{

	/**
	 * Returns cache lifetime, always one second expiring content
	 */
	public function getLifetime()
	{
		return 1;
	}

	/**
	 * Check whether if frontend is buffering output, always false
	 */
	public function isBuffering()
	{
		return false;
	}

	/**
	 * Starts output frontend
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

	}

	/**
	 * Stops output frontend
	 */
	public function stop()
	{

	}

	/**
	 * Prepare data to be stored
	 *
	 * @param mixed $data
	 */
	public function beforeStore($data)
	{
		return $data;
	}

	/**
	 * Prepares data to be retrieved to user
	 *
	 * @param mixed $data
	 */
	public function afterRetrieve($data)
	{
		return $data;
	}
}

