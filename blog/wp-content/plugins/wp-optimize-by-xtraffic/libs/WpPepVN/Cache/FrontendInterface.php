<?php
namespace WpPepVN\Cache;

/**
 * WpPepVN\Cache\FrontendInterface
 *
 * Interface for WpPepVN\Cache\Frontend adapters
 */
interface FrontendInterface
{

	/**
	 * Returns the cache lifetime
	 */
	public function getLifetime();

	/**
	 * Check whether if frontend is buffering output
	 */
	public function isBuffering();

	/**
	 * Starts the frontend
	 */
	public function start();

	/**
	 * Returns output cached content
	 *
	 * @return string
	 */
	public function getContent();

	/**
	 * Stops the frontend
	 */
	public function stop();

	/**
	 * Serializes data before storing it
	 *
	 * @param mixed data
	 */
	public function beforeStore($data);

	/**
	 * Unserializes data after retrieving it
	 *
	 * @param mixed data
	 */
	public function afterRetrieve($data);
}
