<?php 
namespace WpPepVN\Validation;

use WpPepVN\Validation\Exception
	, WpPepVN\Validation\ValidatorInterface
;

/**
 * WpPepVN\Validation\Validator
 *
 * This is a base class for validators
 */
abstract class Validator implements ValidatorInterface
{
	protected $_options;

	/**
	 * WpPepVN\Validation\Validator constructor
	 */
	public function __construct($options = null)
	{
		
		if(!is_array($options) && !is_null($options)) {
			throw new Exception("Options must be an array");
		} else {
			$this->_options = $options;
		}
	}

	/**
	 * Checks if an option is defined

	 * @deprecated since 2.1.0
	 * @see \WpPepVN\Validation\Validator::hasOption()
	 */
	public function isSetOption($key)
	{
		return isset ($this->_options[$key]);
	}

	/**
	 * Checks if an option is defined
	 */
	public function hasOption($key)
	{
		return isset ($this->_options[$key]);
	}

	/**
	 * Returns an option in the validator's options
	 * Returns null if the option hasn't set
	 */
	public function getOption($key, $defaultValue = null) 
	{
		
		if(isset($this->_options[$key])) {
			return $this->_options[$key];
		}
		
		return $defaultValue;
	}

	/**
	 * Sets an option in the validator
	 */
	public function setOption($key, $value) 
	{
		$this->_options[$key] = $value;
	}

    /**
     * Executes the validation
     */
     abstract public function validate(\WpPepVN\Validation $validation, $attribute);
}
