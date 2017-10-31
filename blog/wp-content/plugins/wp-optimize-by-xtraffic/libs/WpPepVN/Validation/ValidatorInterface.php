<?php 
namespace WpPepVN\Validation;

/**
 * WpPepVN\Validation\ValidatorInterface
 *
 * Interface for WpPepVN\Validation\Validator
 */
interface ValidatorInterface
{

	/**
	 * Checks if an option is defined
	 *
	 * @deprecated since 2.1.0
	 * @see \WpPepVN\Validation\Validator::hasOption()
	 */
	public function isSetOption($key);

	/**
	 * Checks if an option is defined
	 */
	public function hasOption($key);

	/**
	 * Returns an option in the validator's options
	 * Returns null if the option hasn't set
	 */
	public function getOption($key, $defaultValue = null);

	/**
	 * Executes the validation
	 */
	public function validate(\WpPepVN\Validation $validation, $attribute);

}