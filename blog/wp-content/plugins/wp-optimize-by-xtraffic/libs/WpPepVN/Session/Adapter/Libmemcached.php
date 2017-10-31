<?php 
namespace WpPepVN\Session\Adapter;

use WpPepVN\Session\Adapter
	,WpPepVN\Session\Exception
	,WpPepVN\Session\AdapterInterface
	,WpPepVN\Cache\Backend\Libmemcached as CacheBackendLibmemcached
	,WpPepVN\Cache\Frontend\Data as FrontendData
;

/**
 * WpPepVN\Session\Adapter\Libmemcached
 *
 * This adapter store sessions in libmemcached
 *
 *<code>
 * $session = new WpPepVN\Session\Adapter\Libmemcached(array(
 *     'servers' => array(
 *         array('host' => 'localhost', 'port' => 11211, 'weight' => 1),
 *     ),
 *     'client' => array(
 *         Memcached::OPT_HASH => Memcached::HASH_MD5,
 *         Memcached::OPT_PREFIX_KEY => 'prefix.',
 *     ),
 *    'lifetime' => 3600,
 *    'prefix' => 'my_'
 * ));
 *
 * $session->start();
 *
 * $session->set('var', 'some-value');
 *
 * echo $session->get('var');
 *</code>
 */
class Libmemcached extends Adapter implements AdapterInterface
{

	protected $_libmemcached = null;

	protected $_lifetime = 8600;

	/**
	 * WpPepVN\Session\Adapter\Libmemcached constructor
	 */
	public function __construct($options)
	{
		
		if(!isset($options['servers'])) {
			throw new Exception('No servers given in options');
		}
		
		if(isset($options['client'])) {
			$client = $options['client'];
		} else {
			$client = null;
		}
		
		if(isset($options['lifetime'])) {
			$lifetime = $options['lifetime'];
		} else {
			$lifetime = 8600;
		}
		
		$lifetime = (int)$lifetime;

		$this->_lifetime = $lifetime;
		
		if(isset($options['prefix'])) {
			$prefix = $options['prefix'];
		} else {
			$prefix = null;
		}
		
		if(isset($options['statsKey'])) {
			$statsKey = $options['statsKey'];
		} else {
			$statsKey = null;
		}
		
		$this->_libmemcached = new CacheBackendLibmemcached(
			new FrontendData(array(
				'lifetime' => $this->_lifetime
			)),
			array(
				'servers' =>  $options['servers'],
				'client' =>   $client,
				'prefix' =>   $prefix,
				'statsKey' => $statsKey
			)
		);

		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);
		
		parent::__construct($options);
	}

	public function open()
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string sessionId
	 * @return mixed
	 */
	public function read($sessionId)
	{
		return $this->_libmemcached->get($sessionId, $this->_lifetime);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string sessionId
	 * @param string data
	 */
	public function write($sessionId, $data)
	{
		$this->_libmemcached->save($sessionId, $data, $this->_lifetime);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param  string  sessionId
	 * @return boolean
	 */
	public function destroy($sessionId = null)
	{
		if ($sessionId === null) {
			$sessionId = $this->getId();
		}
		return $this->_libmemcached->delete($sessionId);
	}

	/**
	 * {@inheritdoc}
	 */
	public function gc()
	{
		return true;
	}
}

