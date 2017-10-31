<?php 
namespace WpPepVN\Form;

use WpPepVN\DependencyInjection
	,WpPepVN\FilterInterface
	,WpPepVN\DependencyInjection\Injectable
	,WpPepVN\Form\Exception
	,WpPepVN\Form\ElementInterface
	,WpPepVN\Validation\Message\Group
	,WpPepVN\Text
;

/**
 * WpPepVN\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class Form extends Injectable implements \Countable, \Iterator
{

	protected $_position;

	protected $_entity;

	protected $_options;

	protected $_data;
	
	protected $_dataFiltered;
	
	protected $_elements;

	protected $_elementsIndexed;

	protected $_messages;

	protected $_action;

	protected $_validation;

	/**
	 * WpPepVN\Forms\Form constructor
	 *
	 * @param object entity
	 * @param array userOptions
	 */
	public function __construct($entity = null, $userOptions = null)
	{
		
		if(!is_null($entity)) {
			if(!is_object($entity)) {
				throw new Exception("The base entity is not valid");
			}
			$this->_entity = $entity;
		}

		/**
		 * Update the user options
		 */
		
		if(is_array($userOptions)) {
			$this->_options = $userOptions;
		}

		/**
		 * Check for an 'initialize' method and call it
		 */
		if (method_exists($this, 'initialize')) {
			$this->{'initialize'}($entity, $userOptions);
		}
	}

	/**
	 * Sets the form's action
	 *
	 * @param string action
	 * @return WpPepVN\Forms\Form
	 */
	public function setAction($action) 
	{
		$this->_action = $action;
		return $this;
	}

	/**
	 * Returns the form's action
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * Sets an option for the form
	 *
	 * @param string option
	 * @param mixed value
	 * @return WpPepVN\Forms\Form
	 */
	public function setUserOption($option, $value)
	{
		$this->_options[$option] = $value;
		return $this;
	}

	/**
	 * Returns the value of an option if present
	 *
	 * @param string option
	 * @param mixed defaultValue
	 * @return mixed
	 */
	public function getUserOption($option, $defaultValue = null)
	{
		
		if(isset($this->_options[$option])) {
			return $this->_options[$option];
		}
		
		return $defaultValue;
	}

	/**
	 * Sets options for the element
	 */
	public function setUserOptions($options)
	{
		$this->_options = $options;
		return $this;
	}

	/**
	 * Returns the options for the element
	 *
	 * @return array
	 */
	public function getUserOptions()
	{
		return $this->_options;
	}

	/**
	 * Sets the entity related to the model
	 *
	 * @param object entity
	 * @return WpPepVN\Forms\Form
	 */
	public function setEntity($entity)
	{
		$this->_entity = $entity;
		return $this;
	}

	/**
	 * Returns the entity related to the model
	 *
	 * @return object
	 */
	public function getEntity()
	{
		return $this->_entity;
	}

	/**
	 * Returns the form elements added to the form
	 */
	public function getElements() 
	{
		return $this->_elements;
	}
	
	/**
	 * Returns the Validation
	 */
	public function getValidation() 
	{
		if(!$this->_validation) {
			//$this->_validation = $this->getDI()->getShared('validation');
		}
		return $this->_validation;
	}
	
	

	/**
	 * Binds data to the entity
	 *
	 * @param array data
	 * @param object entity
	 * @param array whitelist
	 * @return WpPepVN\Forms\Form
	 */
	public function bind($data, $entity, $whitelist = null)
	{
		$elements = $this->_elements;
		
		if(!is_array($elements)) {
			throw new Exception("There are no elements in the form");
		}

		$filter = null;
		
		foreach($data as $key => $value) {

			/**
			 * Get the element
			 */
			
			if(!isset($elements[$key])) {
				continue;
			}
			
			/**
			 * Check if the item is in the whitelist
			 */
			
			if(is_array($whitelist)) {
				if (!in_array($key, $whitelist)) {
					continue;
				}
			}
			
			$element = $elements[$key];

			/**
			 * Check if the method has filters
			 */
			$filters = $element->getFilters();

			if ($filters) {

				if(!is_object($filter)) {
					$dependencyInjector = $this->getDI();
					$filter = $dependencyInjector->getShared("filter");
				}

				/**
				 * Sanitize the filters
				 */
				$filteredValue = $filter->sanitize($value, $filters);
			} else {
				$filteredValue = $value;
			}

			/**
			 * Use the setter if any available
			 */
			$method = 'set' . Text::camelize($key);
			if ($entity && method_exists($entity, $method)) {
				$entity->{$method}($filteredValue);
				continue;
			}

			/**
			 * Use the public property if it doesn't have a setter
			 */
			if($entity) {
				$entity->{$key} = $filteredValue;
			}
			
			$this->_dataFiltered[$key] = $filteredValue;
		}

		$this->_data = $data;

		return $this;
	}

	/**
	 * Validates the form
	 *
	 * @param array data
	 * @param object entity
	 * @return boolean
	 */
	public function isValid($data = null, $entity = null)
	{
		$elements = $this->_elements;
		
		if(!is_array($elements)) {
			return true;
		}

		/**
		 * If the data is not an array use the one passed previously
		 */
		if(!is_array($data)) {
			$data = $this->_data;
		}

		/**
		 * If the user doesn't pass an entity we use the one in this_ptr->_entity
		 */
		
		if(is_object($entity)) {
			$this->bind($data, $entity);
		} elseif(is_object($this->_entity)) {
			$this->bind($data, $this->_entity);
		}

		/**
		 * Check if there is a method 'beforeValidation'
		 */
		if (method_exists($this, "beforeValidation")) {
			if ($this->{"beforeValidation"}($data, $entity) === false) {
				return false;
			}
		}

		$notFailed = true;
		$messages = array();

		foreach($elements as $element) {

			$validators = $element->getValidators();
			
			if(is_array($validators)) {
				if (!empty($validators)) {

					/**
					 * Element's name
					 */
					$name = $element->getName();

					/**
					 * Prepare the validators
					 */
					$preparedValidators = array();

					foreach($validators as $validator) {
						$preparedValidators[] = array($name, $validator);
					}

					$validation = $this->getValidation();
					
					if(is_object($validation)) {
						if ($validation instanceof \WpPepVN\Validation) {
							/**
							 * Set the validators to the validation
							 */
							$validation->setValidators($preparedValidators);
						}
					} else {
						/**
						 * Create an implicit validation
						 */
						$validation = new \WpPepVN\Validation($preparedValidators);
					}

					/**
					 * Get filters in the element
					 */
					$filters = $element->getFilters();

					/**
					 * Assign the filters to the validation
					 */
					
					if(is_array($filters)) {
						$validation->setFilters($element->getName(), $filters);
					}

					/**
					 * Perform the validation
					 */
					$elementMessages = $validation->validate($data, $entity);
					if (!empty($elementMessages)) {
						$messages[$element->getName()] = $elementMessages;
						$notFailed = false;
					}

				}

			}
		}

		/**
		 * If the validation fails update the messages
		 */
		if (!$notFailed) {
			$this->_messages = $messages;
		}

		/**
		 * Check if there is a method 'afterValidation'
		 */
		if (method_exists($this, 'afterValidation')) {
			$this->{"afterValidation"}($messages);
		}

		/**
		 * Return the validation status
		 */
		return $notFailed;
	}

	/**
	 * Returns the messages generated in the validation
	 */
	public function getMessages($byItemName = false)
	{
		$messages = $this->_messages;
		if ($byItemName) {
			if(!is_array($messages)) {
				return new Group();
			}
			return $messages;
		}

		$group = new Group();
		
		if(is_array($messages)) {
			
			foreach($messages as $elementMessages) {
				$group->appendMessages($elementMessages);
			}
		}
		
		return $group;
	}

	/**
	 * Returns the messages generated for a specific element
	 *
	 * @param string name
	 * @return WpPepVN\Validation\Message\Group
	 */
	public function getMessagesFor($name)
	{
		
		if(isset($this->_messages[$name])) {
			return $this->_messages[$name];
		}
		
		$this->_messages[$name] = new Group();
		
		return $this->_messages[$name];
	}

	/**
	 * Check if messages were generated for a specific element
	 *
	 * @param string name
	 * @return boolean
	 */
	public function hasMessagesFor($name)
	{
		return isset ($this->_messages[$name]);
	}

	/**
	 * Adds an element to the form
	 *
	 * @param WpPepVN\Forms\ElementInterface element
	 * @param string $postion
 	 * @param bool $type If $type is TRUE, the element wile add before $postion, else is after
	 * @return WpPepVN\Forms\Form
	 */
	public function add(ElementInterface $element, $postion = null, $type = null)
	{
		

		/**
		 * Gets the element's name
		 */
		$name = $element->getName();

		/**
		 * Link the element to the form
		 */
		$element->setForm($this);

		if (($postion === null) || !is_array($this->_elements)) {
		
			/**
			 * Append the element by its name
			 */
			$this->_elements[$name] = $element;
		} else {
			$elements = array();
			/**
			 * Walk elements and add the element to a particular position
			 */
			
			foreach($this->_elements as $key => $value) {
				if ($key === $postion) {
					if ($type) {
						/**
						 * Add the element before position
						 */
						$elements[$name] = $element;
						$elements[$key] = $value;
					} else {
						/**
						 * Add the element after position
						 */
						$elements[$key] = $value;
						$elements[$name] = $element;
					}
				} else {
					/**
					 * Copy the element to new array
					 */
					$elements[$key] = $value;
				}
			}
			$this->_elements = $elements;
		}
		return $this;
	}

	/**
	 * Renders a specific item in the form
	 *
	 * @param string name
	 * @param array attributes
	 * @return string
	 */
	public function render($name, $attributes = null)
	{
		if(!isset($this->_elements[$name])) {
			throw new Exception("Element with ID=" . $name . " is not part of the form");
		}

		return $this->_elements[$name]->render($attributes);
	}

	/**
	 * Returns an element added to the form by its name
	 */
	public function get($name) 
	{
		
		if(isset($this->_elements[$name])) {
			return $this->_elements[$name];
		}

		throw new Exception("Element with ID=" . $name . " is not part of the form");
	}

	/**
	 * Generate the label of a element added to the form including HTML
	 */
	public function label($name, $attributes = null) 
	{
		if(isset($this->_elements[$name])) {
			return $this->_elements[$name]->label($attributes);
		}

		throw new Exception("Element with ID=" . $name . " is not part of the form");
	}

	/**
	 * Returns a label for an element
	 */
	public function getLabel($name) 
	{
		
		if(!isset($this->_elements[$name])) {
			throw new Exception("Element with ID=" . $name . " is not part of the form");
		}

		$label = $this->_elements[$name]->getLabel();

		/**
		 * Use the element's name as label if the label is not available
		 */
		if (!$label) {
			return $name;
		}

		return $label;
	}

	/**
	 * Gets a value from the internal related entity or from the default value
	 *
	 * @param string name
	 * @return mixed
	 */
	public function getValue($name)
	{
		$entity = $this->_entity;
		$data = $this->_data;

		/**
		 * Check if form has a getter
		 */
		if (method_exists($this, "getCustomValue")) {
			return $this->{"getCustomValue"}($name, $entity, $data);
		}
		
		if(is_object($entity)) {

			/**
			 * Check if the entity has a getter
			 */
			$method = "get" . Text::camelize($name);
			if (method_exists($entity, $method)) {
				return $entity->{$method}();
			}

			/**
			 * Check if the entity has a public property
			 */
			if(isset($entity->{$name})) {
				return $entity->{$name};
			}
		}
		
		if(is_array($data)) {

			/**
			 * Check if the data is in the data array
			 */
			if(isset($data[$name])) {
				return $value;
			}
		}

		/**
		 * Check if form has a getter
		 */
		$method = "get" . Text::camelize($name);
		if (method_exists($this, $method)) {
			return $this->{$method}();
		}

		return null;
	}
	
	/**
	 * Gets a value from the internal related entity or from the default value
	 *
	 * @param string name
	 * @return mixed
	 */
	public function getValueFiltered($name = null)
	{
		if(null !== $name) {
			if(isset($this->_dataFiltered[$name])) {
				return $this->_dataFiltered[$name];
			}
		} else {
			return $this->_dataFiltered;
		}
		
		return null;
	}

	/**
	 * Check if the form contains an element
	 */
	public function has($name) 
	{
		/**
		 * Checks if the element is in the form
		 */
		return isset ($this->_elements[$name]);
	}

	/**
	 * Removes an element from the form
	 */
	public function remove($name) 
	{
		
		/**
		 * Checks if the element is in the form
		 */
		if (isset ($this->_elements[$name])) {
			unset ($this->_elements[$name]);
			return true;
		}

		/**
		 * Clean the iterator index
		 */
		$this->_elementsIndexed = null;

		return false;
	}

	/**
	 * Clears every element in the form to its default value
	 *
	 * @param array fields
	 * @return WpPepVN\Forms\Form
	 */
	public function clear($fields = null) 
	{
		
		if(is_array($this->_elements)) {
			foreach($this->_elements as $element) {
				if(!is_array($fields)) {
					$element->clear();
				} else if (in_array($element->getName(), $fields)) {
					$element->clear();
				}
			}
		}
		
		return $this;
	}

	/**
	 * Returns the number of elements in the form
	 */
	public function count()
	{
		return count($this->_elements);
	}

	/**
	 * Rewinds the internal iterator
	 */
	public function rewind() 
	{
		$this->_position = 0;
		$this->_elementsIndexed = array_values($this->_elements);
	}

	/**
	 * Returns the current element in the iterator
	 */
	public function current()
	{ 
		if (isset ($this->_elementsIndexed[$this->_position])) {
			return $this->_elementsIndexed[$this->_position];
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
	 * Check if the current element in the iterator is valid
	 */
	public function valid()
	{
		return isset ($this->_elementsIndexed[$this->_position]);
	}
}
