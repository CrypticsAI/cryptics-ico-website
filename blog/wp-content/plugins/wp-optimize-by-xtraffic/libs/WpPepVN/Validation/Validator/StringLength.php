<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Validator
	,WpPepVN\Validation\Exception
	,WpPepVN\Validation\Message
;

/**
 * WpPepVN\Validation\Validator\StringLength
 *
 * Validates that a string has the specified maximum and minimum constraints
 * The test is passed if for a string's length L, min<=L<=max, i.e. L must
 * be at least min, and at most max.
 *
 *<code>
 *use WpPepVN\Validation\Validator\StringLength as StringLength;
 *
 *$validation->add('name_last', new StringLength(array(
 *      'max' => 50,
 *      'min' => 2,
 *      'messageMaximum' => 'We don\'t like really long names',
 *      'messageMinimum' => 'We want more than just their initials'
 *)));
 *</code> 
 */
class StringLength extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field) 
	{
		
		/**
		 * At least one of 'min' or 'max' must be set
		 */
		$isSetMin = $this->isSetOption("min");
		$isSetMax = $this->isSetOption("max");

		if ((!$isSetMin) && (!$isSetMax)) {
			throw new Exception("A minimum or maximum must be set");
		}

		$value = $validation->getValue($field);

		if ($this->isSetOption("allowEmpty") && empty ($value)) {
			return true;
		}

		$label = $this->getOption("label");
		if (empty ($label)) {
			$label = $validation->getLabel($field);
		}

		/**
		 * Check if mbstring is available to calculate the correct length
		 */
		if (function_exists("mb_strlen")) {
			$length = mb_strlen($value);
		} else {
			$length = strlen($value);
		}

		/**
		 * Maximum length
		 */
		if ($isSetMax) {

			$maximum = $this->getOption("max");
			if ($length > $maximum) {

				/**
				 * Check if the developer has defined a custom message
				 */
				$message = $this->getOption("messageMaximum");
				$replacePairs = array(':field' => $label, ':max' =>  $maximum);
				if (empty ($message)) {
					$message = $validation->getDefaultMessage("TooLong");
				}

				$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "TooLong"));
				
				return false;
			}
		}

		/**
		 * Minimum length
		 */
		if ($isSetMin) {

			$minimum = $this->getOption("min");
			if ($length < $minimum) {

				/**
				 * Check if the developer has defined a custom message
				 */
				$message = $this->getOption("messageMinimum");
				$replacePairs = array(':field' => $label, ':min' =>  $minimum);
				if (empty ($message)) {
					$message = $validation->getDefaultMessage("TooShort");
				}

				$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "TooShort"));
				
				return false;
			}
		}

		return true;
	}
}