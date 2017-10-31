<?php 
namespace WpPepVN\Http\Response;

/**
 * WpPepVN\Http\Response\HeadersInterface
 *
 * Interface for WpPepVN\Http\Response\Headers compatible bags
 */
interface HeadersInterface
{

	/**
	 * Sets a header to be sent at the end of the request
	 *
	 * @param string name
	 * @param string value
	 */
	public function set($name, $value);

	/**
	 * Gets a header value from the internal bag
	 *
	 * @param string name
	 * @return string
	 */
	public function get($name);

	/**
	 * Sets a raw header to be sent at the end of the request
	 *
	 * @param string header
	 */
	public function setRaw($header);

	/**
	 * Sends the headers to the client
	 */
	public function send();

	/**
	 * Reset set headers
	 */
	public function reset();

	/**
	 * Restore a WpPepVN\Http\Response\Headers object
	 */
	public static function __set_state($data);

}