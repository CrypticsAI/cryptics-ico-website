<?php 
namespace WpPepVN\Http\Response;

use WpPepVN\Http\Response\HeadersInterface;

/**
 * WpPepVN\Http\Response\Headers
 *
 * This class is a bag to manage the response headers
 */
class Headers implements HeadersInterface
{
	protected $_headers = [];

	/**
	 * Sets a header to be sent at the end of the request
	 *
	 * @param string name
	 * @param string value
	 */
	public function set($name, $value)
	{
		$this->_headers[$name] = $value;
	}

	/**
	 * Gets a header value from the internal bag
	 *
	 * @param string name
	 * @return string
	 */
	public function get($name)
	{
		
		if(isset($this->_headers[$name])) {
			return $this->_headers[$name];
		}
		
		return false;
	}

	/**
	 * Sets a raw header to be sent at the end of the request
	 *
	 * @param string header
	 */
	public function setRaw($header)
	{
		$this->_headers[$header] = null;
	}

	/**
	 * Removes a header to be sent at the end of the request
	 *
	 * @param string header Header name
	 */
	public function remove($header)
	{
		
		unset($this->_headers[$header]);
		
	}

	/**
	 * Sends the headers to the client
	 */
	public function send()
	{
		
		if (!headers_sent()) {
			foreach($this->_headers as $header => $value) {
				if (!empty ($value)) {
					header($header . ': ' . $value, true);
				} else {
					header($header, true);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Reset set headers
	 */
	public function reset()
	{
		$this->_headers = array();
	}

	/**
	 * Returns the current headers as an array
	 */
	public function toArray() 
	{
		return $this->_headers;
	}

	/**
	 * Restore a WpPepVN\Http\Response\Headers object
	 */
	public static function __set_state($data) 
	{
		$headers = new self();
		
		if(isset($data['_headers'])) {
			foreach($data['_headers'] as $key => $value) {
				$headers->set($key, $value);
			}
		}
		
		return $headers;
	}
}
