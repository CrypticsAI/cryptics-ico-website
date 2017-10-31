<?php 

namespace WpPepVN\Cache\Backend;

use WpPepVN\Cache\Backend
	,WpPepVN\Cache\BackendInterface
	,WpPepVN\Cache\Exception
;

/**
 * WpPepVN\Cache\Backend\Memory
 *
 * Stores content in memory. Data is lost when the request is finished
 *
 *<code>
 *	//Cache data
 *	$frontCache = new \WpPepVN\Cache\Frontend\Data();
 *
 *  $cache = new \WpPepVN\Cache\Backend\Memory($frontCache);
 *
 *	//Cache arbitrary data
 *	$cache->save('my-data', array(1, 2, 3, 4, 5));
 *
 *	//Get data
 *	$data = $cache->get('my-data');
 *
 *</code>
 */
class Memory extends Backend implements BackendInterface, \Serializable
{

	protected $_data;

	/**
	 * Returns a cached content
	 *
	 * @param 	string keyName
	 * @param   long lifetime
	 * @return  mixed
	 */
	public function get($keyName, $lifetime = null)
	{

		if ($keyName === null) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $keyName; $this->_lastKey = $lastKey;
		}
		
		if(!isset($this->_data[$lastKey])) {
			return null;
		} else if($this->_data[$lastKey] === null) {
			return null;
		}
		
		return $this->_frontend->afterRetrieve($this->_data[$lastKey]);
	}

	/**
	 * Stores cached content into the backend and stops the frontend
	 *
	 * @param string keyName
	 * @param string content
	 * @param long lifetime
	 * @param boolean stopBuffer
	 */
	public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
	{
		if ($keyName === null) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $keyName;
		}

		if (!$lastKey) {
			throw new Exception("Cache must be started first");
		}
		
		if ($content === null) {
			$content = $this->_frontend->getContent();
		}
		
		if($this->_lastLifetime || $lifetime) {
			$this->_data[$lastKey] = $this->_frontend->beforeStore($content);
		}

		$isBuffering = $this->_frontend->isBuffering();

		if ($stopBuffer === true) {
			$this->_frontend->stop();
		}

		if ($isBuffering === true) {
			echo $content;
		}

		$this->_started = false;
	}

	/**
	 * Deletes a value from the cache by its key
	 *
	 * @param string keyName
	 * @return boolean
	 */
	public function delete($keyName)
	{
		$key = $this->_prefix . $keyName;
		
		if (isset ($this->_data[$key])) {
			unset ($this->_data[$key]);
			return true;
		}

		return false;
	}

	/**
	 * Query the existing cached keys
	 *
	 * @param string|int prefix
	 * @return array
	 */
	public function queryKeys($prefix = null)
	{
		
		if(is_array($this->_data)) {
			if (!$prefix) {
				$keys = (array) array_keys($this->_data);
			} else {
			    $keys = array();
				
				foreach($this->_data as $index => $value) {
					$keys[] = $index;
				}
			}
		}
		
		return $keys;
	}

	/**
	 * Checks if cache exists and it hasn't expired
	 *
	 * @param  string|int keyName
	 * @param  long lifetime
	 * @return boolean
	 */
	public function exists($keyName = null, $lifetime = null)
	{
		
		if ($keyName === null) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $keyName;
		}

		if ($lastKey) {
			if (isset ($this->_data[$lastKey])) {
				return true;
			}
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
		
		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = $prefix . $keyName;
			$this->_lastKey = $lastKey;
		}
		
		if(!isset($this->_data[$lastKey])) {
			return null;
		} else if($this->_data[$lastKey] === null) {
			return null;
		}
		
		if (!$value) {
			$value = 1;
		}
		
		$this->_data[$lastKey] = $this->_data[$lastKey] + $value;

		return $this->_data[$lastKey];
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
		
		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = $prefix . $keyName;
			$this->_lastKey = $lastKey;
		}
		
		if(!isset($this->_data[$lastKey])) {
			return null;
		} else if($this->_data[$lastKey] === null) {
			return null;
		}
		
		if (!$value) {
			$value = 1;
		}
		
		$this->_data[$lastKey] = $this->_data[$lastKey] - $value;

		return $this->_data[$lastKey];
	}

	/**
	 * Immediately invalidates all existing items.
	 */
	public function flush()
	{
		$this->_data = null;
		return true;
	}

	/**
	 * Required for interface \Serializable
	 */
	public function serialize()
	{
		return serialize(array(
			'frontend' => $this->_frontend
		));
	}

	/**
	 * Required for interface \Serializable
	 */
	public function unserialize($data)
	{
		
		$data = unserialize($data);
		
		if(!isset($data['frontend'])) {
			throw new \Exception("Unserialized data must be an array");
		}

		$this->_frontend = $data['frontend'];
	}
}
