<?php 
namespace WpPepVN\Http\Response;

use WpPepVN\DependencyInjection;
use WpPepVN\Http\Cookie;
use WpPepVN\Http\Response\CookiesInterface;
use WpPepVN\DependencyInjection\InjectionAwareInterface;
use WpPepVN\Http\Cookie\Exception;

/**
 * WpPepVN\Http\Response\Cookies
 *
 * This class is a bag to manage the cookies
 * A cookies bag is automatically registered as part of the 'response' service in the DI
 */
class Cookies implements CookiesInterface, InjectionAwareInterface
{

	protected $_dependencyInjector;

	protected $_registered = false;

	protected $_useEncryption = true;

	protected $_cookies;

	/**
	 * Sets the dependency injector
	 */
	public function setDI(DependencyInjection $dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns the internal dependency injector
	 */
	public function getDI()
	{
		$this->_dependencyInjector;
	}

	/**
	 * Set if cookies in the bag must be automatically encrypted/decrypted
	 */
	public function useEncryption($useEncryption)
	{
		$this->_useEncryption = $useEncryption;
		return $this;
	}

	/**
	 * Returns if the bag is automatically encrypting/decrypting cookies
	 */
	public function isUsingEncryption()
	{
		return $this->_useEncryption;
	}

	/**
	 * Sets a cookie to be sent at the end of the request
	 * This method overrides any cookie set before with the same name
	 *
	 * @param string name
	 * @param mixed value
	 * @param int expire
	 * @param string path
	 * @param boolean secure
	 * @param string domain
	 * @param boolean httpOnly
	 * @return WpPepVN\Http\Response\Cookies
	 */
	public function set($name, $value = null, $expire = 0, $path = '/', $secure = null, $domain = null, $httpOnly = null)
	{
		$encryption = $this->_useEncryption;

		/**
		 * Check if the cookie needs to be updated or
		 */
		
		if(!isset($this->_cookies[$name])) {

			$cookie = new Cookie($name, $value, $expire, $path, $secure, $domain, $httpOnly);

			/**
			 * Pass the DI to created cookies
			 */
			$cookie->setDi($this->_dependencyInjector);

			/**
			 * Enable encryption in the cookie
			 */
			if ($encryption) {
				$cookie->useEncryption($encryption);
			}

			$this->_cookies[$name] = $cookie;

		} else {

			/**
			 * Override any settings in the cookie
			 */
			$this->_cookies[$name]->setValue($value);
			$this->_cookies[$name]->setExpiration($expire);
			$this->_cookies[$name]->setPath($path);
			$this->_cookies[$name]->setSecure($secure);
			$this->_cookies[$name]->setDomain($domain);
			$this->_cookies[$name]->setHttpOnly($httpOnly);
		}

		/**
		 * Register the cookies bag in the response
		 */
		if ($this->_registered === false) {

			$dependencyInjector = $this->_dependencyInjector;
			if(!is_object($dependencyInjector)) {
				throw new Exception('A dependency injection object is required to access the \'response\' service');
			}

			$response = $dependencyInjector->getShared('response');

			/**
			 * Pass the cookies bag to the response so it can send the headers at the of the request
			 */
			$response->setCookies($this);
		}

		return $this;
	}

	/**
	 * Gets a cookie from the bag
	 */
	public function get($name) 
	{
		
		if(isset($this->_cookies[$name])) {
			return $this->_cookies[$name];
		}

		/**
         * Create the cookie if the it does not exist
         */
		$cookie = new Cookie($name);
		$dependencyInjector = $this->_dependencyInjector;

		if(is_object($dependencyInjector)) {

			/**
			 * Pass the DI to created cookies
			 */
			$cookie->setDi($dependencyInjector);

			$encryption = $this->_useEncryption;

			/**
			 * Enable encryption in the cookie
			 */
			if ($encryption) {
				$cookie->useEncryption($encryption);
			}
		}

		$this->_cookies[$name] = $cookie;
		return $cookie;
	}

	/**
	 * Check if a cookie is defined in the bag or exists in the _COOKIE superglobal
	 */
	public function has($name) 
	{
		/**
		 * Check the internal bag
		 */
		if (isset ($this->_cookies[$name])) {
			return true;
		}

		/**
		 * Check the superglobal
		 */
		if (isset ($_COOKIE[$name])) {
			return true;
		}

		return false;
	}

	/**
	 * Deletes a cookie by its name
	 * This method does not removes cookies from the _COOKIE superglobal
	 */
	public function delete($name) 
	{
		

		/**
		 * Check the internal bag
		 */
		
		if(isset($this->_cookies[$name])) {
			$cookie->delete();
			return true;
		}

		return false;
	}

	/**
	 * Sends the cookies to the client
	 * Cookies aren't sent if headers are sent in the current request
	 */
	public function send() 
	{
		
		if (!headers_sent()) {
			
			foreach($this->_cookies as $cookie) {
				$cookie->send();
			}
			return true;
		}
		return false;
	}

	/**
	 * Reset set cookies
	 */
	public function reset() 
	{
		$this->_cookies = array();
		return $this;
	}
}
