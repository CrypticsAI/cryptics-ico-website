<?php 
namespace WpPepVN\Session;

/**
 * WpPepVN\Session\Adapter
 *
 * Base class for WpPepVN\Session adapters
 */
abstract class Adapter
{

	const SESSION_ACTIVE = 2;

	const SESSION_NONE = 1;

	const SESSION_DISABLED = 0;

	protected $_uniqueId;

	protected $_started = false;

	protected $_options;
	
	private static $_sessionStartedStatus = false;

	/**
	 * WpPepVN\Session\Adapter constructor
	 *
	 * @param array options
	 */
	public function __construct($options = null)
	{
		if(is_array($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * Starts the session (if headers are already sent the session will not be started)
	 */
	public function start()
	{
		if(false === self::$_sessionStartedStatus) {
			self::$_sessionStartedStatus = true;
			if (!headers_sent()) {
				if (!$this->_started && ($this->status() !== self::SESSION_ACTIVE)) {
					if(!session_id()) {
						session_start();
						$this->_started = true;
						return true;
					}
				}
			}
		}
		
		return false;
	}

	/**
	 * Sets session's options
	 *
	 *<code>
	 *	$session->setOptions(array(
	 *		'uniqueId' => 'my-private-app'
	 *	));
	 *</code>
	 */
	public function setOptions($options)
	{
		if(isset($options["uniqueId"])) {
			$this->_uniqueId = $options["uniqueId"];
		}

		$this->_options = $options;
	}

	/**
	 * Get internal options
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Set session name
	 */
	public function setName($name)
	{
	    session_name($name);
	}

	/**
	 * Get session name
	 */
	public function getName()
	{
	    return session_name();
	}

	/**
	 * Gets a session variable from an application context
	 *
	 * @param string index
	 * @param mixed defaultValue
	 * @param boolean remove
	 * @return mixed
	 */
	public function get($index, $defaultValue = null, $remove = false)
	{
		$key = $this->_uniqueId . $index;
		if(isset($_SESSION[$key])) {
			$value = $_SESSION[$key];
			if($remove) {
				unset ($_SESSION[$key]);
			}
			return $value;
		}
		
		return $defaultValue;
	}

	/**
	 * Sets a session variable in an application context
	 *
	 *<code>
	 *	$session->set('auth', 'yes');
	 *</code>
	 *
	 * @param string index
	 * @param string value
	 */
	public function set($index, $value)
	{
		$_SESSION[$this->_uniqueId . $index] = $value;
	}

	/**
	 * Check whether a session variable is set in an application context
	 */
	public function has($index)
	{
		return isset ($_SESSION[$this->_uniqueId . $index]);
	}

	/**
	 * Removes a session variable from an application context
	 *
	 *<code>
	 *	$session->remove('auth');
	 *</code>
	 */
	public function remove($index)
	{
		unset ($_SESSION[$this->_uniqueId . $index]);
	}

	/**
	 * Returns active session id
	 *
	 *<code>
	 *	echo $session->getId();
	 *</code>
	 */
	public function getId()
	{
		return session_id();
	}

	/**
	 * Set the current session id
	 *
	 *<code>
	 *	$session->setId($id);
	 *</code>
	 */
	public function setId($id)
	{
		session_id($id);
	}

	/**
	 * Check whether the session has been started
	 */
	public function isStarted()
	{
		return $this->_started;
	}

	/**
	 * Destroys the active session
	 */
	public function destroy()
	{
		$this->_started = false;
		self::$_sessionStartedStatus = false;
		return session_destroy();
	}

	/**
	 * Returns the status of the current session. For PHP 5.3 this function will always return SESSION_NONE
	 *
	 *<code>
	 *
	 *  // PHP 5.4 and above will give meaningful messages, 5.3 gets SESSION_NONE always
	 *  if ($session->status() !== $session::SESSION_ACTIVE) {
	 *      $session->start();
	 *  }
	 *</code>
	 */
	public function status()
	{
		if ( 0 !== strpos(PHP_VERSION,'5.3')) {
			if(function_exists('session_status')) {
				$status = session_status();

				switch ($status) {
					case PHP_SESSION_DISABLED:
						return self::SESSION_DISABLED;

					case PHP_SESSION_ACTIVE:
						return self::SESSION_ACTIVE;
				}
			}
		}
		
		return self::SESSION_NONE;
	}

	/**
	 * Alias: Gets a session variable from an application context
	 *
	 * @param string index
	 * @return mixed
	 */
	public function __get($index)
	{
		return $this->get($index);
	}

	/**
	 * Alias: Sets a session variable in an application context
	 *
	 * @param string index
	 * @param string value
	 */
	public function __set($index, $value)
	{
		return $this->set($index, $value);
	}

	/**
	 * Alias: Check whether a session variable is set in an application context
	 */
	public function __isset($index)
	{
		return $this->has($index);
	}

	/**
	 * Alias: Removes a session variable from an application context
	 */
	public function __unset($index)
	{
		return $this->remove($index);
	}
}
