<?php 

namespace WpPepVN\Validation\Message;

use WpPepVN\Validation\Message
	,WpPepVN\Validation\Exception
	,WpPepVN\Validation\MessageInterface
	,WpPepVN\Validation\Message\Group
	,Iterator
;

/**
 * WpPepVN\Validation\Message\Group
 *
 * Represents a group of validation messages
 */
class Group implements \Countable, \ArrayAccess, \Iterator
{

	protected $_position;

	protected $_messages;

	/**
	 * WpPepVN\Validation\Message\Group constructor
	 *
	 * @param array messages
	 */
	public function __construct($messages = null)
	{
		if(is_array($messages)) {
			$this->_messages = $messages;
		}
	}

	/**
	 * Gets an attribute a message using the array syntax
	 *
	 *<code>
	 * print_r($messages[0]);
	 *</code>
	 *
	 * @param int index
	 * @return WpPepVN\Validation\Message
	 */
	public function offsetGet($index)
	{
		
		if(isset($this->_messages[$index])) {
			return $this->_messages[$index];
		}
		
		return false;
	}

	/**
	 * Sets an attribute using the array-syntax
	 *
	 *<code>
	 * $messages[0] = new \WpPepVN\Validation\Message('This is a message');
	 *</code>
	 *
	 * @param int index
	 * @param WpPepVN\Validation\Message message
	 */
	public function offsetSet($index, $message)
	{
		
		if(!is_object($message)) {
			throw new Exception("The message must be an object");
		}
		$this->_messages[$index] = $message;
	}

	/**
	 * Checks if an index exists
	 * @param int index
	 * @return boolean
	 */
	public function offsetExists($index) 
	{
		return isset ($this->_messages[$index]);
	}

	/**
	 * Removes a message from the list
	 *
	 *<code>
	 * unset($message['database']);
	 *</code>
	 *
	 * @param string index
	 */
	public function offsetUnset($index)
	{
		if (isset ($this->_messages[$index])) {
			unset ($this->_messages[$index]);
		}
		return false;
	}

	/**
	 * Appends a message to the group
	 *
	 *<code>
	 * $messages->appendMessage(new \WpPepVN\Validation\Message('This is a message'));
	 *</code>
	 */
	public function appendMessage(MessageInterface $message)
	{
		$this->_messages[] = $message;
	}

	/**
	 * Appends an array of messages to the group
	 *
	 *<code>
	 * $messages->appendMessages($messagesArray);
	 *</code>
	 *
	 * @param WpPepVN\Validation\MessageInterface[] messages
	 */
	public function appendMessages($messages)
	{
		
		if(!is_array($messages) && !is_object($messages)) {
			throw new Exception("The messages must be array or object");
		}
		
		if(is_array($messages)) {

			/**
			 * An array of messages is simply merged into the current one
			 */
			if(is_array($this->_messages)) {
				$this->_messages = array_merge($this->_messages, $messages);
			} else {
				$this->_messages = $messages;
			}
			
		} else {

			/**
			 * A group of messages is iterated and appended one-by-one to the current list
			 */
			
			foreach($messages as $message) {
				$this->appendMessage($message);
			}
		}
	}

	/**
	 * Filters the message group by field name
	 *
	 * @param string fieldName
	 * @return array
	 */
	public function filter($fieldName)
	{
		$filtered = array();
		$messages = $this->_messages;
		
		if(is_array($messages)) {

			/**
			 * A group of messages is iterated and appended one-by-one to the current list
			 */
			
			foreach($messages as $message) {

				/**
				 * Get the field name
				 */
				if (method_exists($message, 'getField')) {
					if ($fieldName === $message->getField()) {
						$filtered[] = $message;
					}
				}
			}
		}

		return $filtered;
		
	}

	/**
	 * Returns the number of messages in the list
	 */
	public function count()
	{
		return count($this->_messages);
	}

	/**
	 * Rewinds the internal iterator
	 */
	public function rewind()
	{
		$this->_position = 0;
	}

	/**
	 * Returns the current message in the iterator
	 *
	 * @return WpPepVN\Validation\Message
	 */
	public function current()
	{
		
		if(isset($this->_messages[$this->_position])) {
			return $this->_messages[$this->_position];
		}
		return false;
	}

	/**
	 * Returns the current position/key in the iterator
	 */
	public function key()
	{
		return $this->_position;
	}

	/**
	 * Moves the internal iteration pointer to the next position
	 */
	public function next()
	{
		$this->_position++;
	}

	/**
	 * Check if the current message in the iterator is valid
	 */
	public function valid()
	{
		return isset ($this->_messages[$this->_position]);
	}

	/**
	 * Magic __set_state helps to re-build messages variable when exporting
	 *
	 * @param array group
	 * @return WpPepVN\Validation\Message\Group
	 */
	public static function __set_state($group)
	{
		return new self($group['_messages']);
	}
}
