<?php
namespace WpPepVN\Cache\Backend;

use WpPepVN\Cache\Exception;
use WpPepVN\Cache\Backend;
use WpPepVN\Cache\BackendInterface;

/**
 * WpPepVN\Cache\Backend\Apc
 *
 * Allows to cache output fragments, PHP data and raw data using an APC backend
 *
 *<code>
 *	//Cache data for 2 days
 *	$frontCache = new \WpPepVN\Cache\Frontend\Data(array(
 *		'lifetime' => 172800
 *	));
 *
 *  $cache = new \WpPepVN\Cache\Backend\Apc($frontCache, array(
 *      'prefix' => 'app-data'
 *  ));
 *
 *	//Cache arbitrary data
 *	$cache->save('my-data', array(1, 2, 3, 4, 5));
 *
 *	//Get data
 *	$data = $cache->get('my-data');
 *
 *</code>
 */
class Apc extends Backend implements BackendInterface
{
	const PREFIX = '_WPVC';
	/**
	 * Returns a cached content
	 *
	 * @param 	string|long keyName
	 * @param   long lifetime
	 * @return  mixed
	 */
	public function get($keyName, $lifetime = null)
	{
		$prefixedKey = self::PREFIX . $this->_prefix . $keyName;
		$this->_lastKey = $prefixedKey;

		$cachedContent = apc_fetch($prefixedKey);
		if ($cachedContent === false) {
			return null;
		}

		return $this->_frontend->afterRetrieve($cachedContent);
	}

	/**
	 * Stores cached content into the APC backend and stops the frontend
	 *
	 * @param string|long keyName
	 * @param string content
	 * @param long lifetime
	 * @param boolean stopBuffer
	 */
	public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
	{
		
		if ($keyName === null) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = self::PREFIX . $this->_prefix . $keyName;
		}

		if (!$lastKey) {
			throw new \Exception("Cache must be started first");
		}

		$frontend = $this->_frontend;
		if ($content === null) {
			$cachedContent = $frontend->getContent();
		} else {
			$cachedContent = $content;
		}

		$preparedContent = $frontend->beforeStore($cachedContent);

		/**
		 * Take the lifetime from the frontend or read it from the set in start()
		 */
		if ($lifetime === null) {
			$lifetime = $this->_lastLifetime;
			if ($lifetime === null) {
				$ttl = $frontend->getLifetime();
			} else {
				$ttl = $lifetime;
			}
		} else {
			$ttl = $lifetime;
		}

		/**
		 * Call apc_store in the PHP userland since most of the time it isn't available at compile time
		 */
		apc_store($lastKey, $preparedContent, $ttl);

		$isBuffering = $frontend->isBuffering();

		if ($stopBuffer === true) {
			$frontend->stop();
		}

		if ($isBuffering === true) {
			echo $cachedContent;
		}

		$this->_started = false;
	}

	/**
	 * Increment of a given key, by number $value
	 *
	 * @param  string keyName
	 * @param  long value
	 * @return mixed
	 */
	public function increment($keyName = null, $value = 1)
	{
		
		$prefixedKey = self::PREFIX . $this->_prefix . $keyName;
		$this->_lastKey = $prefixedKey;

		if (function_exists('apc_inc')) {
			$result = apc_inc($prefixedKey, $value);
			return $result;
		} else {
			$cachedContent = apc_fetch($prefixedKey);

			if (is_numeric($cachedContent)) {
				$result = $cachedContent + $value;
				$this->save($keyName, $result);
				return $result;
			} else {
				return false;
			}
		}
	}

	/**
	 * Decrement of a given key, by number $value
	 *
	 * @param  string keyName
	 * @param  long value
	 * @return mixed
	 */
	public function decrement($keyName = null, $value = 1)
	{
		
		$lastKey = self::PREFIX . $this->_prefix . $keyName;
		$this->_lastKey = $lastKey;

		if (function_exists("apc_dec")) {
			return apc_dec($lastKey, $value);
		} else {
			$cachedContent = apc_fetch($lastKey);

			if(is_numeric($cachedContent)) {
				$result = $cachedContent - $value;
				$this->save($keyName, $result);
				return $result;
			} else {
				return false;
			}
		}
	}

	/**
	 * Deletes a value from the cache by its key
	 */
	public function delete($keyName)
	{
		return apc_delete(self::PREFIX . $this->_prefix . $keyName);
	}

	/**
	 * Query the existing cached keys
	 *
	 * @param string prefix
	 * @return array
	 */
	public function queryKeys($prefix = null)
	{
		
		if (!$prefix) {
			$prefixPattern = '/^'.self::PREFIX.'/';
		} else {
			$prefixPattern = '/^'.self::PREFIX.$prefix.'/';
		}

		$keys = array();
		$apc = new \APCIterator('user', $prefixPattern);
		
		$selfPrefixLength = strlen(self::PREFIX);
		foreach(iterator($apc) as $key => $value) {
			$keys[] = substr($key, $selfPrefixLength);
		}
		
		return $keys;
	}

	/**
	 * Checks if cache exists and it hasn't expired
	 *
	 * @param  string|long keyName
	 * @param  long lifetime
	 * @return boolean
	 */
	public function exists($keyName = null, $lifetime = null)
	{
		
		if ($keyName === null) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = self::PREFIX.$this->_prefix . $keyName;
		}

		if ($lastKey) {
			if (apc_exists($lastKey) !== false) {
				return true;
			}
		}

		return false;
	}

	/**
 	 * Immediately invalidates all existing items.
	 */
	public function flush()
	{
		foreach(iterator(new \APCIterator('user')) as $item) {
			apc_delete($item['key']);
		}
		
		return true;
	}
}
