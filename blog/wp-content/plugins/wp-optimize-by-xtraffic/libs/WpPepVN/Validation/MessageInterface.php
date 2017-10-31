<?php 
namespace WpPepVN\Validation;

use WpPepVN\Validation\Message
;

/**
 * WpPepVN\Validation\Message
 *
 * Interface for WpPepVN\Validation\Message
 */
interface MessageInterface
{

	/**
	 * WpPepVN\Validation\Message constructor
	 *
	 * @param string message
	 * @param string field
	 * @param string type
	 */
	public function __construct($message, $field = null, $type = null);

	/**
	 * Sets message type
	 */
	public function setType($type);

	/**
	 * Returns message type
	 */
	public function getType();

	/**
	 * Sets verbose message
	 */
	public function setMessage($message);

	/**
	 * Returns verbose message
	 *
	 * @return string
	 */
	public function getMessage();

	/**
	 * Sets field name related to message
	 */
	public function setField($field);

	/**
	 * Returns field name related to message
	 *
	 * @return string
	 */
	public function getField();

	/**
	 * Magic __toString method returns verbose message
	 */
	public function __toString();

	/**
	 * Magic __set_state helps to recover messsages from serialization
	 */
	public static function __set_state($message);

}