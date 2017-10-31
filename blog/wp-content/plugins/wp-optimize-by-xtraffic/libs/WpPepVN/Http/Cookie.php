<?php 
namespace WpPepVN\Http;

use WpPepVN\DependencyInjection;
use WpPepVN\CryptInterface;
use WpPepVN\DependencyInjection\InjectionAwareInterface;
use WpPepVN\Http\Response\Exception;
use WpPepVN\Session\AdapterInterface as SessionInterface;

/**
 * WpPepVN\Http\Cookie
 *
 * Provide OO wrappers to manage a HTTP cookie
 */
class Cookie implements InjectionAwareInterface
{
	const COOKIE_PREFIX = 'wppvc_';
	
	protected $_readed = false;

	protected $_restored = false;

	protected $_useEncryption = false;

	protected $_dependencyInjector;

	protected $_filter;

	protected $_name;

	protected $_value;

	protected $_expire;

	protected $_path = '/';

	protected $_domain;

	protected $_secure;

	protected $_httpOnly = true;

	/**
	 * WpPepVN\Http\Cookie constructor
	 *
	 * @param string name
	 * @param mixed value
	 * @param int expire
	 * @param string path
	 * @param boolean secure
	 * @param string domain
	 * @param boolean httpOnly
	 */
	public function __construct($name, $value = null, $expire = 0, $path = '/', $secure = null, $domain = null, $httpOnly = null)
	{
		$this->_name = $name;

		if ($value !== null) {
			$this->_value = $value;
		}

		$this->_expire = $expire;

		if ($path !== null) {
			$this->_path = $path;
		}

		if ($secure !== null) {
			$this->_secure = $secure;
		}

		if ($domain !== null) {
			$this->_domain = $domain;
		}

		if ($httpOnly !== null) {
			$this->_httpOnly = $httpOnly;
		}
	}

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
		return $this->_dependencyInjector;
	}

	/**
	 * Sets the cookie's value
	 *
	 * @param string value
	 * @return WpPepVN\Http\Cookie
	 */
	public function setValue($value) 
	{
		$this->_value = $value;
		$this->_readed = true;
		return $this;
	}

	/**
	 * Returns the cookie's value
	 *
	 * @param string|array filters
	 * @param string defaultValue
	 * @return mixed
	 */
	public function getValue($filters = null, $defaultValue = null)
	{
		
		if (!$this->_restored) {
			$this->restore();
		}

		$dependencyInjector = null;

		if ($this->_readed === false) {

			//if fetch value, _COOKIE[this->_name] 
			if(isset($_COOKIE[$this->_name])) {

				if ($this->_useEncryption) {

					$dependencyInjector = $this->_dependencyInjector;
					if (!is_object($dependencyInjector)){
						throw new Exception('A dependency injection object is required to access the \'filter\' service');
					}

					$crypt = $dependencyInjector->getShared('crypt');

					/**
					 * Decrypt the value also decoding it with base64
					 */
					$decryptedValue = $crypt->decryptBase64($value);

				} else {
					$decryptedValue = $value;
				}

				/**
				 * Update the decrypted value
				 */
				$this->_value = $decryptedValue;

				if ($filters !== null) {
					
					if (!is_object($this->_filter)){

						if ($dependencyInjector === null) {
							$dependencyInjector = $this->_dependencyInjector;
							if (!is_object($dependencyInjector)){
								throw new Exception('A dependency injection object is required to access the \'filter\' service');
							}
						}

						$filter = $dependencyInjector->getShared('filter');
						$this->_filter = $filter;
					}

					return $filter->sanitize($decryptedValue, $filters);
				}

				/**
				 * Return the value without filtering
				 */
				return $decryptedValue;
			}
			return $defaultValue;
		}

		return $this->_value;
	}

	/**
	 * Sends the cookie to the HTTP client
	 * Stores the cookie definition in session
	 */
	public function send()
	{
		$name = $this->_name;
		$value = $this->_value;
		$expire = $this->_expire;
		$domain = $this->_domain;
		$path = $this->_path;
		$secure = $this->_secure;
		$httpOnly = $this->_httpOnly;

		$dependencyInjector = $this->_dependencyInjector;

		if (!is_object($dependencyInjector)){
			throw new Exception('A dependency injection object is required to access the \'session\' service');
		}

		$definition = array();

		if ($expire !== 0) {
			$definition['expire'] = $expire;
		}

		if (!empty ($path)) {
			$definition['path'] = $path;
		}

		if (!empty ($domain)) {
			$definition['domain'] = $domain;
		}

		if (!empty ($secure)) {
			$definition['secure'] = $secure;
		}

		if (!empty ($httpOnly)) {
			$definition['httpOnly'] = $httpOnly;
		}

		/**
		 * The definition is stored in session
		 */
		if (!empty($definition)) {
			$session = $dependencyInjector->getShared('session');
			$session->set(self::COOKIE_PREFIX . $name, $definition);
		}

		if ($this->_useEncryption) {

			if (!empty ($value)) {

				if (!is_object($dependencyInjector)){
					throw new Exception('A dependency injection object is required to access the \'filter\' service');
				}

				$crypt = $dependencyInjector->getShared('crypt');

				/**
				 * Encrypt the value also coding it with base64
				 */
				$encryptValue = $crypt->encryptBase64((string) $value);

			} else {
				$encryptValue = $value;
			}

		} else {
			$encryptValue = $value;
		}

		/**
		 * Sets the cookie using the standard 'setcookie' function
		 */
		setcookie($name, $encryptValue, $expire, $path, $domain, $secure, $httpOnly);

		return $this;
	}

	/**
	 * Reads the cookie-related info from the SESSION to restore the cookie as it was set
	 * This method is automatically called internally so normally you don't need to call it
	 */
	public function restore() 
	{
		
		if (!$this->_restored) {

			$dependencyInjector = $this->_dependencyInjector;
			if (is_object($dependencyInjector)){

				$session = $dependencyInjector->getShared('session');

				$definition = $session->get(self::COOKIE_PREFIX . $this->_name);
				if (is_array($definition)) {

					if(isset($definition['expire'])) {
						$this->_expire = $expire;
					}

					if(isset($definition['domain'])) {
						$this->_domain = $domain;
					}

					if(isset($definition['path'])) {
						$this->_path = $path;
					}

					if(isset($definition['path'])) {
						$this->_secure = $secure;
					}

					if(isset($definition['path'])) {
						$this->_httpOnly = $httpOnly;
					}
				}
			}

			$this->_restored = true;
		}

		return $this;
	}

	/**
	 * Deletes the cookie by setting an expire time in the past
	 */
	public function delete()
	{
		$name     = $this->_name;
		$domain   = $this->_domain;
		$path     = $this->_path;
		$secure   = $this->_secure;
		$httpOnly = $this->_httpOnly;

		$dependencyInjector = $this->_dependencyInjector;
		if (is_object($dependencyInjector)) {
			$session = $dependencyInjector->getShared('session');
			$session->remove(self::COOKIE_PREFIX . $name);
		}

		$this->_value = null;
		setcookie($name, null, time() - 691200, $path, $domain, $secure, $httpOnly);
	}

	/**
	 * Sets if the cookie must be encrypted/decrypted automatically
	 */
	public function useEncryption($useEncryption)
	{
		$this->_useEncryption = $useEncryption;
		return $this;
	}

	/**
	 * Check if the cookie is using implicit encryption
	 */
	public function isUsingEncryption() 
	{
		return $this->_useEncryption;
	}

	/**
	 * Sets the cookie's expiration time
	 */
	public function setExpiration($expire) 
	{
		if (!$this->_restored) {
			$this->restore();
		}
		$this->_expire = $expire;
		return $this;
	}

	/**
	 * Returns the current expiration time
	 */
	public function getExpiration()
	{
		if (!$this->_restored) {
			$this->restore();
		}
		return $this->_expire;
	}

	/**
	 * Sets the cookie's expiration time
	 */
	public function setPath($path) 
	{
		if (!$this->_restored) {
			$this->restore();
		}
		$this->_path = $path;
		return $this;
	}

	/**
	 * Returns the current cookie's name
	 */
	public function getName() 
	{
		return $this->_name;
	}

	/**
	 * Returns the current cookie's path
	 */
	public function getPath() 
	{
		if (!$this->_restored) {
			$this->restore();
		}
		return $this->_path;
	}

	/**
	 * Sets the domain that the cookie is available to
	 */
	public function setDomain($domain) 
	{
		if (!$this->_restored) {
			$this->restore();
		}
		$this->_domain = $domain;
		return $this;
	}

	/**
	 * Returns the domain that the cookie is available to
	 */
	public function getDomain()
	{
		if (!$this->_restored) {
			$this->restore();
		}
		return $this->_domain;
	}

	/**
	 * Sets if the cookie must only be sent when the connection is secure (HTTPS)
	 */
	public function setSecure($secure)
	{
		if (!$this->_restored) {
			$this->restore();
		}
		$this->_secure = $secure;
		return $this;
	}

	/**
	 * Returns whether the cookie must only be sent when the connection is secure (HTTPS)
	 */
	public function getSecure()
	{
		if (!$this->_restored) {
			$this->restore();
		}
		return $this->_secure;
	}

	/**
	 * Sets if the cookie is accessible only through the HTTP protocol
	 */
	public function setHttpOnly($httpOnly) 
	{
		if (!$this->_restored) {
			$this->restore();
		}
		$this->_httpOnly = $httpOnly;
		return $this;
	}

	/**
	 * Returns if the cookie is accessible only through the HTTP protocol
	 */
	public function getHttpOnly()
	{
		if (!$this->_restored) {
			$this->restore();
		}
		return $this->_httpOnly;
	}

	/**
	 * Magic __toString method converts the cookie's value to string
	 */
	public function __toString()
	{
		return (string) $this->getValue();
	}
}
