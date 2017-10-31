<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\Validator
;

/**
 * WpPepVN\Validation\Validator\Digit
 *
 * Check for numeric character(s)
 *
 *<code>
 *use WpPepVN\Validation\Validator\Digit as DigitValidator;
 *
 *$validator->add('height', new DigitValidator(array(
 *   'message' => ':field must be numeric'
 *)));
 *</code>
 */
class Digit extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field)
	{
		$value = $validation->getValue($field);

		if ($this->isSetOption("allowEmpty") && empty ($value)) {
			return true;
		}

		if (!ctype_digit($value)) {

			$label = $this->getOption("label");
			if (empty($label)) {
				$label = $validation->getLabel($field);
			}
			
			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Digit");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Digit"));
			
			return false;
		}

		return true;
	}
}