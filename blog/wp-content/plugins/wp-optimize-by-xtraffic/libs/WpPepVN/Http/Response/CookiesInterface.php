<?php 
namespace WpPepVN\Http\Response;

/**
 * WpPepVN\Http\Response\CookiesInterface
 *
 * Interface for WpPepVN\Http\Response\Cookies
 */
interface CookiesInterface
{

	/**
	 * Set if cookies in the bag must be automatically encrypted/decrypted
	 */
	public function useEncryption($useEncryption);

	/**
	 * Returns if the bag is automatically encrypting/decrypting cookies
	 */
	public function isUsingEncryption();

	/**
	 * Sets a cookie to be sent at the end of the request
	 *
	 * @param string name
	 * @param mixed value
	 * @param int expire
	 * @param string path
	 * @param boolean secure
	 * @param string domain
	 * @param boolean httpOnly
	 * @return WpPepVN\Http\Response\CookiesInterface
	 */
	public function set($name, $value = null, $expire = 0, $path = "/", $secure = null, $domain = null, $httpOnly = null);

	/**
	 * Gets a cookie from the bag
	 */
	public function get($name);

	/**
	 * Check if a cookie is defined in the bag or exists in the _COOKIE superglobal
	 */
	public function has($name);

	/**
	 * Deletes a cookie by its name
	 * This method does not removes cookies from the _COOKIE superglobal
	 */
	public function delete($name);

	/**
	 * Sends the cookies to the client
	 */
	public function send();

	/**
	 * Reset set cookies
	 */
	public function reset();

}
