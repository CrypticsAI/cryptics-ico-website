<?php 

namespace WpPepVN\Mvc;

/**
 * WpPepVN\Mvc\UrlInterface
 *
 * Interface for WpPepVN\Mvc\UrlInterface
 */
interface UrlInterface
{

	/**
	 * Sets a prefix to all the urls generated
	 */
	public function setBaseUri($baseUri);

	/**
	 * Returns the prefix for all the generated urls. By default /
	 */
	public function getBaseUri();

	/**
	 * Sets a base paths for all the generated paths
	 */
	public function setBasePath($basePath);

	/**
	 * Returns a base path
	 */
	public function getBasePath();

	/**
	 * Generates a URL
	 *
	 * @param string|array uri
	 * @param array|object args Optional arguments to be appended to the query string
	 * @param bool $local
	 * @return string
	 */
	public function get($uri = null, $args = null, $local = null);

	/**
	 * Generates a local path
	 *
	 * @param string path
	 * @return string
	 */
	public function path($path = null);
}
