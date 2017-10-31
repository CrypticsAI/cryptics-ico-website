<?php 
namespace WpPepVN\Cache\Backend;

use WpPepVN\Cache\Exception
	,WpPepVN\Cache\Backend
	,WpPepVN\Cache\FrontendInterface
	,WpPepVN\Cache\BackendInterface
;

/**
 * WpPepVN\Cache\Backend\File
 *
 * Allows to cache output fragments using a file backend
 *
 *<code>
 *	//Cache the file for 2 days
 *	$frontendOptions = array(
 *		'lifetime' => 172800
 *	);
 *
 *  //Create a output cache
 *  $frontCache = \WpPepVN\Cache\Frontend\Output($frontOptions);
 *
 *	//Set the cache directory
 *	$backendOptions = array(
 *		'cacheDir' => '../app/cache/'
 *	);
 *
 *  //Create the File backend
 *  $cache = new \WpPepVN\Cache\Backend\File($frontCache, $backendOptions);
 *
 *	$content = $cache->start('my-cache');
 *	if ($content === null) {
 *  	echo '<h1>', time(), '</h1>';
 *  	$cache->save();
 *	} else {
 *		echo $content;
 *	}
 *</code>
 */
class File extends Backend
{
	/**
	 * Default to false for backwards compatibility
	 *
	 * @var boolean
	 */
	private $_useSafeKey = false;

	/**
	 * WpPepVN\Cache\Backend\File constructor
	 *
	 * @param	WpPepVN\Cache\FrontendInterface frontend
	 * @param	array options
	 */
	public function __construct(FrontendInterface $frontend, $options = null)
	{
		
		if (!isset ($options['cacheDir'])) {
			throw new Exception('Cache directory must be specified with the option cacheDir');
		}

		if (isset($options['safekey'])) {
			if(!is_bool($options['safekey'])) {
				throw new Exception('safekey option should be a boolean.');
			}

			$this->_useSafeKey = $safekey;
		}

		// added to avoid having unsafe filesystem characters in the prefix
		if (isset($options['prefix'])) {
			if ($this->_useSafeKey && preg_match('/[^a-zA-Z0-9_.-]+/', $options['prefix'])) {
				throw new Exception('FileCache prefix should only use alphanumeric characters.');
			}
		}

		parent::__construct($frontend, $options);
	}

	/**
	 * Returns a cached content
	 *
	 * @param int|string keyName
	 * @param   int lifetime
	 * @return  mixed
	 */
	public function get($keyName, $lifetime = null)
	{
		
		$prefixedKey =  $this->_prefix . $this->getKey($keyName);
		$this->_lastKey = $prefixedKey;

		if (!isset($this->_options['cacheDir'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		$cacheFile = $this->_options['cacheDir'] . $prefixedKey;

		if (
			(file_exists($cacheFile) === true)
		) {

			/**
			 * Take the lifetime from the frontend or read it from the set in start()
			 */
			if (!$lifetime) {
				$lastLifetime = $this->_lastLifetime;
				if (!$lastLifetime) {
					$ttl = (int) $this->_frontend->getLifeTime();
				} else {
					$ttl = (int) $lastLifetime;
				}
			} else {
				$ttl = (int) $lifetime;
			}

			$modifiedTime = (int) filemtime($cacheFile);

			/**
			 * Check if the file has expired
			 * The content is only retrieved if the content has not expired
			 */
			if (!(time() - $ttl > $modifiedTime)) {

				/**
				 * Use file-get-contents to control that the openbase_dir can't be skipped
				 */
				$cachedContent = file_get_contents($cacheFile);
				if ($cachedContent === false) {
					throw new Exception('Cache file '. $cacheFile. ' could not be opened');
				}

				if (is_numeric($cachedContent)) {
					return $cachedContent;
				} else {
					/**
					 * Use the frontend to process the content of the cache
					 */
					$cachedContent = $this->_frontend->afterRetrieve($cachedContent);
					return $cachedContent;
				}
			}
		}
	}

	/**
	 * Stores cached content into the file backend and stops the frontend
	 *
	 * @param int|string keyName
	 * @param string content
	 * @param int lifetime
	 * @param boolean stopBuffer
	 */
	public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
	{
		
		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $this->_prefix . $this->getKey($keyName);
		}

		if (!$lastKey) {
			throw new Exception('Cache must be started first');
		}
		
		if(!isset($this->_options['cacheDir'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		$cacheFile = $this->_options['cacheDir'] . $lastKey;

		if (null === $content) {
			$content = $this->_frontend->getContent();
		}
		
		/**
		 * We use file_put_contents to respect open-base-dir directive
		 */
		if (!is_numeric($content)) {
			$status = file_put_contents($cacheFile, $this->_frontend->beforeStore($content));
		} else {
			$status = file_put_contents($cacheFile, $content);
		}

		if ($status === false) {
			throw new Exception('Cache file '. $cacheFile . ' could not be written');
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
	 * @param int|string keyName
	 * @return boolean
	 */
	public function delete($keyName)
	{
		if(!isset($this->_options['cacheDir'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		$cacheFile = $this->_options['cacheDir'] . $this->_prefix . $this->getKey($keyName);
		if (true === file_exists($cacheFile)) {
			return unlink($cacheFile);
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
		
		$keys = array();

		if(!isset($this->_options['cacheDir'])) {
			throw new Exception('Unexpected inconsistency in options');
		}

		/**
		 * We use a directory iterator to traverse the cache dir directory
		 */
		$dirItems = new \DirectoryIterator($this->_options['cacheDir']);
		foreach($dirItems as $item) {

			if ($item->isDir() === false) {
				$key = $item->getFileName();
				if ($prefix !== null) {
					if (0 === strpos($key, $prefix)) {
						$keys[] = $key;
					}
				} else {
					$keys[] = $key;
				}
			}
		}

		return $keys;
	}

	/**
	 * Checks if cache exists and it isn't expired
	 *
	 * @param string|int keyName
	 * @param   int lifetime
	 * @return boolean
	 */
	public function exists($keyName = null, $lifetime = null)
	{
		
		if (!$keyName) {
			$lastKey = $this->_lastKey;
		} else {
			$prefix = $this->_prefix;
			$lastKey = $prefix . $this->getKey($keyName);
		}

		if ($lastKey) {

			$cacheFile = $this->_options['cacheDir'] . $lastKey;

			if (true === file_exists($cacheFile)) {

				/**
				 * Check if the file has expired
				 */
				if (!$lifetime) {
					$ttl = (int) $this->_frontend->getLifeTime();
				} else {
					$ttl = (int) $lifetime;
				}

				if ((filemtime($cacheFile) + $ttl) > time()) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Increment of a given key, by number $value
	 *
	 * @param  string|int keyName
	 * @param  int value
	 * @return mixed
	 */
	public function increment($keyName = null, $value = 1)
	{
		
		$prefixedKey = $this->_prefix . $this->getKey($keyName);
		$this->_lastKey = $prefixedKey;
		$cacheFile = $this->_options['cacheDir'] . $prefixedKey;

		if (file_exists($cacheFile)) {

			$frontend = $this->_frontend;

			/**
			 * Check if the file has expired
			 */
			$timestamp = time();

			/**
			 * Take the lifetime from the frontend or read it from the set in start()
			 */
			$lifetime = $this->_lastLifetime;
			if (!$lifetime) {
				$ttl = $frontend->getLifeTime();
			} else {
				$ttl = $lifetime;
			}

			/**
			 * The content is only retrieved if the content has not expired
			 */
			if (($timestamp - $ttl) < filemtime($cacheFile)) {

				/**
				 * Use file-get-contents to control that the openbase_dir can't be skipped
				 */
				$cachedContent = file_get_contents($cacheFile);

				if ($cachedContent === false) {
					throw new Exception('Cache file ' . $cacheFile . ' could not be opened');
				}

				if (is_numeric($cachedContent)) {

					$result = $cachedContent + $value;
					if (file_put_contents($cacheFile, $result) === false) {
						throw new Exception('Cache directory could not be written');
					}

					return $result;
				}
			}
		}
	}

	/**
	 * Decrement of a given key, by number $value
	 *
	 * @param  string|int keyName
	 * @param  int value
	 * @return mixed
	 */
	public function decrement($keyName = null, $value = 1)
	{
		$prefixedKey = $this->_prefix . $this->getKey(keyName);
		$this->_lastKey = $prefixedKey;
		$cacheFile = $this->_options['cacheDir'] . $prefixedKey;

		if (file_exists($cacheFile)) {

			/**
			 * Check if the file has expired
			 */
			$timestamp = time();

			/**
			 * Take the lifetime from the frontend or read it from the set in start()
			 */
			$lifetime = $this->_lastLifetime;
			if (!$lifetime) {
				$ttl = $this->_frontend->getLifeTime();
			} else {
				$ttl = $lifetime;
			}

			/**
			 * The content is only retrieved if the content has not expired
			 */
			if (($timestamp - $ttl) < filemtime($cacheFile)) {

				/**
				 * Use file-get-contents to control that the openbase_dir can't be skipped
				 */
				$cachedContent = file_get_contents($cacheFile);

				if ($cachedContent === false) {
					throw new Exception('Cache file ' . $cacheFile . ' could not be opened');
				}

				if (is_numeric($cachedContent)) {

					$result = $cachedContent - $value;
					if (file_put_contents($cacheFile, $result) === false) {
						throw new Exception('Cache directory can\'t be written');
					}

					return $result;
				}
			}
		}
	}

	/**
	 * Immediately invalidates all existing items.
	 */
	public function flush()
	{
		$prefix = $this->_prefix;

		if(!isset($this->_options['cacheDir'])) {
			throw new Exception('Unexpected inconsistency in options');
		}
		
		$dirItems = new \DirectoryIterator($this->_options['cacheDir']);
		foreach($dirItems as $item) {

			if ($item->isFile() == true) {
				$key = $item->getFileName();
				$cacheFile = $item->getPathName();

				if (empty($prefix) || (0 === strpos($key, $prefix))) {
					if  (!unlink($cacheFile)) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Return a file-system safe identifier for a given key
	 */
	public function getKey($key)
	{
		if ($this->_useSafeKey === true) {
			return md5($key);
		}

		return $key;
	}

	/**
	 * Set whether to use the safekey or not
	 *
	 * @return this
	 */
	public function useSafeKey($useSafeKey)
	{
		$this->_useSafeKey = $useSafeKey;

		return $this;
	}
	
}
