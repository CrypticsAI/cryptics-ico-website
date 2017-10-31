<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\Validator
;

/**
 * WpPepVN\Validation\Validator\Between
 *
 * Validates that a value is between an inclusive range of two values.
 * For a value x, the test is passed if minimum<=x<=maximum.
 *
 *<code>
 *use WpPepVN\Validation\Validator\Between;
 *
 *validator->add('name', new Between(array(
 *   'minimum' => 0,
 *   'maximum' => 100,
 *   'message' => 'The price must be between 0 and 100'
 *)));
 *</code>
 */
class Between extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field) 
	{
		$value = $validation->getValue($field);
		$minimum = $this->getOption("minimum");
		$maximum = $this->getOption("maximum");

		if ($this->isSetOption("allowEmpty") && empty ($value)) {
			return true;
		}

		if (($value < $minimum) || ($value > $maximum)) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label, ':min' => $minimum, ':max' => $maximum);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Between");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Between"));
			
			return false;
		}

		return true;
	}
}
