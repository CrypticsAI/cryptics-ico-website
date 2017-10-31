<?php 
namespace WpPepVN;

/**
 * WpPepVN\Event
 *
 * This class offers contextual information of a fired event in the EventsManager
 */
class Event
{

	/**
	 * Event type
	 *
	 * @var string
	 */
	protected $_type;

	/**
	 * Event source
	 *
	 * @var object
	 */
	protected $_source;

	/**
	 * Event data
	 *
	 * @var mixed
	 */
	protected $_data;

	/**
	 * Is event propagation stopped?
	 *
	 * @var boolean
	 */
	protected $_stopped = false;

	/**
	 * Is event cancelable?
	 *
	 * @var boolean
	 */
	protected $_cancelable = true;

	/**
	 * Phalcon\Events\Event constructor
	 *
	 * @param string type
	 * @param object source
	 * @param mixed data
	 * @param boolean cancelable
	 */
	public function __construct($type, $source, $data = null, $cancelable = true)
	{
		$this->_type = $type;
		$this->_source = $source;

		if ($data !== null) {
			$this->_data = $data;
		}

		if ($cancelable !== true) {
			$this->_cancelable = $cancelable;
		}
	}
	
	/**
	 * Return _type
	 */
	public function getType()
	{
		return $this->_type;
	}
	
	/**
	 * Return _source
	 */
	public function getSource()
	{
		return $this->_source;
	}
	
	/**
	 * Return _source
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Return _source
	 */
	public function getCancelable()
	{
		return $this->_cancelable;
	}
	
	/**
	 * Stops the event preventing propagation
	 */
	public function stop()
	{
		if (!$this->_cancelable) {
			throw new Exception('Trying to cancel a non-cancelable event');
		}

		$this->_stopped = true;
	}
	
	/**
	 * Check whether the event is currently stopped
	 */
	public function isStopped()
	{
		return $this->_stopped;
	}
	
}
