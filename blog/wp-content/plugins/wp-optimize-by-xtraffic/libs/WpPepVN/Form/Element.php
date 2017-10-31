<?php 

namespace WpPepVN\Form;

use WpPepVN\Tag
	,WpPepVN\Form\Exception
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\MessageInterface
	,WpPepVN\Validation\Message\Group
	,WpPepVN\Validation\ValidatorInterface
;

/**
 * WpPepVN\Forms\Element
 *
 * This is a base class for form elements
 */
abstract class Element implements ElementInterface
{

	protected $_form;

	protected $_name;

	protected $_value;

	protected $_label;

	protected $_attributes;

	protected $_validators;

	protected $_filters;

	protected $_options;

	protected $_messages;

	/**
	 * WpPepVN\Forms\Element constructor
	 *
	 * @param string name
	 * @param array attributes
	 */
	public function __construct($name, $attributes = null)
	{
		$this->_name = $name;
		
		if(is_array($attributes)) {
			$this->_attributes = $attributes;
		}
	}

	/**
	 * Sets the parent form to the element
	 */
	public function setForm(Form $form)
	{
		$this->_form = $form;
		return $this;
	}

	/**
	 * Returns the parent form to the element
	 */
	public function getForm()
	{
		return $this->_form;
	}

	/**
	 * Sets the element name
	 */
	public function setName($name) 
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * Returns the element name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Sets the element filters
	 *
	 * @param array|string filters
	 * @return WpPepVN\Forms\ElementInterface
	 */
	public function setFilters($filters)
	{
		if(!is_string($filters) && !is_array($filters)) {
			throw new Exception("Wrong filter type added");
		}
		$this->_filters = $filters;
		return $this;
	}

	/**
	 * Adds a filter to current list of filters
	 */
	public function addFilter($filter)
	{
		if(is_array($this->_filters)) {
			$this->_filters[] = $filter;
		} else {
			if(is_string($this->_filters)) {
				$this->_filters = array($this->_filters, $filter);
			} else {
				$this->_filters = array($filter);
			}
		}
		
		return $this;
		
	}

	/**
	 * Returns the element filters
	 *
	 * @return mixed
	 */
	public function getFilters()
	{
		return $this->_filters;
	}

	/**
	 * Adds a group of validators
	 *
	 * @param WpPepVN\Validation\ValidatorInterface[]
	 * @return WpPepVN\Forms\ElementInterface
	 */
	public function addValidators($validators, $merge = true)
	{
		if ($merge) {
			if(is_array($this->_validators)) {
				$this->_validators = array_merge($this->_validators, $validators);
			} else {
				$this->_validators = $validators;
			}
		}
		return $this;
	}

	/**
	 * Adds a validator to the element
	 */
	public function addValidator(ValidatorInterface $validator)
	{
		$this->_validators[] = $validator;
		return $this;
	}

	/**
	 * Returns the validators registered for the element
	 */
	public function getValidators()
	{
		return $this->_validators;
	}

	/**
	 * Returns an array of prepared attributes for WpPepVN\Tag helpers
	 * according to the element parameters
	 *
	 * @param array attributes
	 * @param boolean useChecked
	 * @return array
	 */
	public function prepareAttributes($attributes = null, $useChecked = false)
	{
		$name = $this->_name;

		/**
		 * Create an array of parameters
		 */
		if(!is_array($attributes)) {
			$widgetAttributes = array();
		} else {
			$widgetAttributes = $attributes;
		}

		$widgetAttributes[0] = $name;

		/**
		 * Merge passed parameters with default ones
		 */
		$defaultAttributes = $this->_attributes;
		if(is_array($defaultAttributes)) {
			$mergedAttributes = array_merge($defaultAttributes, $widgetAttributes);
		} else {
			$mergedAttributes = $widgetAttributes;
		}

		/**
		 * Get the current element value
		 */
		$value = $this->getValue();

		/**
		 * If the widget has a value set it as default value
		 */
		if ($value !== null) {
			if ($useChecked) {
				/**
				 * Check if the element already has a default value, compare it with the one in the attributes, if they are the same mark the element as checked
				 */
				if(isset($mergedAttributes["value"])) {
					if ($mergedAttributes["value"] == $value) {
						$mergedAttributes["checked"] = "checked";
					}
				} else {
					/**
					 * Evaluate the current value and mark the check as checked
					 */
					if ($value) {
						$mergedAttributes["checked"] = "checked";
					}
					$mergedAttributes["value"] = $value;
				}
			} else {
				$mergedAttributes["value"] = $value;
			}
		}

		return $mergedAttributes;
	}

	/**
	 * Sets a default attribute for the element
	 *
	 * @param string attribute
	 * @param mixed value
	 * @return WpPepVN\Forms\ElementInterface
	 */
	public function setAttribute($attribute, $value)
	{
		$this->_attributes[$attribute] = $value;
		return $this;
	}

	/**
	 * Returns the value of an attribute if present
	 *
	 * @param string attribute
	 * @param mixed defaultValue
	 * @return mixed
	 */
	public function getAttribute($attribute, $defaultValue = null)
	{
		if(isset($this->_attributes[$attribute])) {
			return $this->_attributes[$attribute];
		}
		return $defaultValue;
	}

	/**
	 * Sets default attributes for the element
	 */
	public function setAttributes($attributes)
	{
		$this->_attributes = $attributes;
		return $this;
	}

	/**
	 * Returns the default attributes for the element
	 */
	public function getAttributes()
	{
		if(!is_array($this->_attributes)) {
			return array();
		}
		
		return $this->_attributes;
	}

	/**
	 * Sets an option for the element
	 *
	 * @param string option
	 * @param mixed value
	 * @return WpPepVN\Forms\ElementInterface
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
	 *
	 * @param array options
	 * @return WpPepVN\Forms\ElementInterface
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
	 * Sets the element label
	 */
	public function setLabel($label) 
	{
		$this->_label = $label;
		return $this;
	}

	/**
	 * Returns the element label
	 */
	public function getLabel()
	{
		return $this->_label;
	}

	/**
	 * Generate the HTML to label the element
	 *
	 * @param array attributes
	 * @return string
	 */
	public function label($attributes = null)
	{
		/**
		 * Check if there is an "id" attribute defined
		 */
		$internalAttributes = $this->getAttributes();
		
		if(!isset($internalAttributes["id"])) {
			$name = $this->_name;
		} else {
			$name = $internalAttributes["id"];
		}
		
		if(is_array($attributes)) {
			if (!isset ($attributes["for"])) {
				$attributes["for"] = $name;
			}
		} else {
			$attributes = array('for'=> $name);
		}

		$code = Tag::renderAttributes('<label', $attributes);

		/**
		 * Use the default label or leave the same name as label
		 */
		$label = $this->_label;
		if ($label) {
			$code .= '>' . $label . '</label>';
		} else {
			$code .= '>' . $name . '</label>';
		}

		return $code;
	}

	/**
	 * Sets a default value in case the form does not use an entity
	 * or there is no value available for the element in _POST
	 *
	 * @param mixed value
	 * @return WpPepVN\Forms\ElementInterface
	 */
	public function setDefault($value) 
	{
		$this->_value = $value;
		return $this;
	}

	/**
	 * Returns the default value assigned to the element
	 *
	 * @return mixed
	 */
	public function getDefault()
	{
		return $this->_value;
	}

	/**
	 * Returns the element value
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		$name = $this->_name;
		$value = null;

		/**
		 * Get the related form
		 */
		$form = $this->_form;
		
		if(is_object($form)) {
			/**
			 * Gets the possible value for the widget
			 */
			$value = $form->getValue($name);

			/**
			 * Check if the tag has a default value
			 */
			if(is_null($value) && Tag::hasValue($name)) {
				$value = Tag::getValue($name);
			}

		}

		/**
		 * Assign the default value if there is no form available
		 */
		if(is_null($value)) {
			$value = $this->_value;
		}

		return $value;
	}

	/**
	 * Returns the messages that belongs to the element
	 * The element needs to be attached to a form
	 */
	public function getMessages() 
	{
		if(is_object($this->_messages)) {
			return $this->_messages;
		}

		$this->_messages = new Group();
		return $this->_messages;
	}

	/**
	 * Checks whether there are messages attached to the element
	 */
	public function hasMessages() 
	{
		/**
		 * Get the related form
		 */
		
		if(is_object($this->_messages)) {
			return count($this->_messages) > 0;
		}

		return false;
	}

	/**
	 * Sets the validation messages related to the element
	 */
	public function setMessages(Group $group) 
	{
		$this->_messages = $group;
		return $this;
	}

	/**
	 * Appends a message to the internal message list
	 */
	public function appendMessage(MessageInterface $message)
	{
		
		if(!is_object($this->_messages)) {
			$this->_messages = new Group();
		}
		
		$this->_messages->appendMessage($message);
		
		return $this;
	}

	/**
	 * Clears every element in the form to its default value
	 */
	public function clear()
	{
		Tag::setDefault($this->_name, null);
		return $this;
	}

	/**
	 * Magic method __toString renders the widget without atttributes
	 */
	public function __toString()
	{
		return $this->{'render'}();
	}
}
