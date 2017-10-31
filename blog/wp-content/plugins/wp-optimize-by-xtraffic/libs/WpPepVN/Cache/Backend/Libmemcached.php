<?php 

namespace WpPepVN\Cache\Backend;

use WpPepVN\Cache\Backend
	, WpPepVN\Cache\BackendInterface
	, WpPepVN\Cache\FrontendInterface
	, WpPepVN\Cache\Exception
;

/**
 * WpPepVN\Cache\Backend\Libmemcached
 *
 * Allows to cache output fragments, PHP data or raw data to a libmemcached backend
 *
 * This adapter uses the special memcached key '_PEPVNCMCD' to store all the keys internally used by the adapter
 *
 *<code>
 *
 * // Cache data for 2 days
 * $frontCache = new \WpPepVN\Cache\Frontend\Data(array(
 *    'lifetime' => 172800
 * ));
 *
 * //Create the Cache setting memcached connection options
 * $cache = new \WpPepVN\Cache\Backend\Libmemcached($frontCache, array(
 *     'servers' => array(
 *         array('host' => 'localhost',
 *               'port' => 11211,
 *               'weight' => 1),
 *     ),
 *     'client' => array(
 *         Memcached::OPT_HASH => Memcached::HASH_MD5,
 *         Memcached::OPT_PREFIX_KEY => 'prefix.',
 *     )
 * ));
 *
 * //Cache arbitrary data
 * $cache->save('my-data', array(1, 2, 3, 4, 5));
 *
 * //Get data
 * $data = $cache->get('my-data');
 *
 *</code>
 */
class Libmemcached extends Backend implements BackendInterface
{

	protected $_memcache = null;

	/**
	 * WpPepVN\Cache\Backend\Memcache constructor
	 *
	 * @param	WpPepVN\Cache\FrontendInterface frontend
	 * @param	array options
	 */
	public function __construct(FrontendInterface $frontend, $options = null)
	{
		
		if(!is_array($options)) {
			$options = (array)$options;
		}

		if (!isset($options['servers'])) {
			$servers = array(array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 1));
			$options['servers'] = $servers;
		}
		
		if (!isset($options['statsKey'])) {
			$options['statsKey'] = '_PEPVNCMCD';
		}

		parent::__construct($frontend, $options);
	}

	/**
	 * Create internal connection to memcached
	 */
	public function _connect()
	{
		$options = $this->_options;
		
		if (!isset($options['servers'])) {
			throw new Exception('Servers must be an array');
		}
		
		if(!is_array($options['servers'])) {
			throw new Exception('Servers must be an array');
		}
		
		$memcache = new \Memcached();
		
		if (!$memcache->addServers($servers)) {
			throw new Exception('Cannot connect to Memcached server');
		}
		
		if (isset($options['client'])) {
			
			if (!is_array($options['client'])) {
				throw new Exception('Client options must be instance of array');
			}
			
			$memcache->setOptions($client);
		}

		$this->_memcache = $memcache;
	}

	/**
	 * Returns a cached content
	 *
	 * @param int|string keyName
	 * @param   long lifetime
	 * @return  mixed
	 */
	public function get($keyName, $lifetime = null)
	{
		
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		$prefixedKey = $this->_prefix . $keyName;
		$this->_lastKey = $prefixedKey;

		$cachedContent = $this->_memcache->get($prefixedKey);
		if (!$cachedContent) {
			return null;
		}

		if (is_numeric($cachedContent)) {
			return $cachedContent;
		} else {
			return $this->_frontend->afterRetrieve($cachedContent);
		}
	}

	/**
	 * Stores cached content into the file backend and stops the frontend
	 *
	 * @param int|string keyName
	 * @param string content
	 * @param long lifetime
	 * @param boolean stopBuffer
	 */
	public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
	{
		
		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $keyName;
		}

		if (!$lastKey) {
			throw new Exception('Cache must be started first');
		}

		$frontend = $this->_frontend;

		/**
		 * Check if a connection is created or make a new one
		 */
		
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		if (!$content) {
			$content = $frontend->getContent();
		}

		/**
		 * Prepare the content in the frontend
		 */
		if (!is_numeric($content)) {
			$preparedContent = $frontend->beforeStore($content);
		}

		if (!$lifetime) {
			$tmp = $this->_lastLifetime;

			if (!$tmp) {
				$tt1 = $frontend->getLifetime();
			} else {
				$tt1 = $tmp;
			}
		} else {
			$tt1 = $lifetime;
		}

		if (is_numeric($content)) {
			$success = $this->_memcache->set($lastKey, $content, $tt1);
		} else {
			$success = $this->_memcache->set($lastKey, $preparedContent, $tt1);
		}

		if (!$success) {
			throw new Exception('Failed storing data in memcached, error code: ' . $this->_memcache->getResultCode());
		}

		$options = $this->_options;
		
		if (!isset($options['statsKey'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		if ($options['statsKey'] != '') {
			/**
			 * Update the stats key
			 */
			$keys = $this->_memcache->get($options['statsKey']);
			
			if(!is_array($keys)) {
				$keys = (array)$keys;
			}

			
			if (!isset($keys['lastKey'])) {
				$keys[$lastKey] = $tt1;
				$this->_memcache->set($options['statsKey'], $keys);
			}
		}

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
	 * Deletes a value from the cache by its key
	 *
	 * @param int|string keyName
	 * @return boolean
	 */
	public function delete($keyName)
	{
		
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		$prefixedKey = $this->_prefix . $keyName;
		$options = $this->_options;

		if (!isset($options['statsKey'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		if ($options['statsKey'] != '') {
			$keys = $this->_memcache->get($options['statsKey']);
			if(is_array($keys)) {
				unset ($keys[$prefixedKey]);
				$this->_memcache->set($options['statsKey'], $keys);
			}
		}

		/**
		 * Delete the key from memcached
		 */
		$ret = $this->_memcache->delete($prefixedKey);
		
		return $ret;
	}

	/**
	 * Query the existing cached keys
	 *
	 * @param string prefix
	 * @return array
	 */
	public function queryKeys($prefix = null)
	{
		
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		$options = $this->_options;

		if (!isset($options['statsKey'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		if ($options['statsKey'] == '') {
			throw new Exception('Cached keys were disabled (options[\'statsKey\'] == \'\'), you shouldn\'t use this function');
		}

		/**
		 * Get the key from memcached
		 */
		$keys = $this->_memcache->get($options['statsKey']);
		
		if(is_array($keys)) {
			$keys = array_keys($keys);
			if ($prefix) {
				foreach($keys as $key) {
					if(0 !== strpos($key, $prefix)) {
						unset ($keys[$key]);
					}
				}
			}
		}

		return $keys;
	}

	/**
	 * Checks if cache exists and it isn't expired
	 *
	 * @param string keyName
	 * @param   long lifetime
	 * @return boolean
	 */
	public function exists($keyName = null, $lifetime = null)
	{
		
		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $keyName;
		}

		if ($lastKey) {
			if(!is_object($this->_memcache)) {
				$this->_connect();
			}
			$value = $this->_memcache->get($lastKey);
			if (!$value) {
				return false;
			}
			return true;
		}

		return false;
	}

	/**
	 * Increment of given $keyName by $value
	 *
	 * @param  string keyName
	 * @param  long lifetime
	 * @return long
	 */
	public function increment($keyName = null, $value = null)
	{
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = $prefix . $keyName;
			$this->_lastKey = $lastKey;
		}

		if (!$value) {
			$value = 1;
		}

		return $this->_memcache->increment($lastKey, $value);
	}

	/**
	 * Decrement of $keyName by given $value
	 *
	 * @param  string keyName
	 * @param  long value
	 * @return long
	 */
	public function decrement($keyName = null, $value = null)
	{
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = $prefix . $keyName;
			$this->_lastKey = $lastKey;
		}

		if (!$value) {
			$value = 1;
		}

		return $this->_memcache->decrement($lastKey, $value);
	}

	/**
	 * Immediately invalidates all existing items.
	 */
	public function flush() 
	{
		if(!is_object($this->_memcache)) {
			$this->_connect();
		}

		$options = $this->_options;
		
		if (!isset($options['statsKey'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		if ($options['statsKey'] == '') {
			throw new Exception('Cached keys were disabled (options[\'statsKey\'] == \'\'), flush of memcached xtraffic-related keys isn\'t implemented for now');
		}
		
		/**
		 * Get the key from memcached
		 */
		$keys = $this->_memcache->get($options['statsKey']);
		
		if(is_array($keys)) {
			
			foreach($keys as $key => $value) {
				unset($keys[$key]);
				$this->_memcache->delete($key);
			}
			
			$this->_memcache->set($options['statsKey'], $keys);
		}

		return true;
	}
}


