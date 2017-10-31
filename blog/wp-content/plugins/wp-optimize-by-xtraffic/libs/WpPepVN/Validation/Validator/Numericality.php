<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation;
use WpPepVN\Validation\Message;
use WpPepVN\Validation\Validator;

/**
 * WpPepVN\Validation\Validator\Numericality
 *
 * Check for a valid numeric value
 *
 *<code>
 *use WpPepVN\Validation\Validator\Numericality;
 *
 *$validator->add('price', new Numericality(array(
 *   'message' => ':field is not numeric'
 *)));
 *</code>
 */
class Numericality extends Validator
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

		if (!preg_match('/^-?\d+\.?\d*$/', $value)) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Numericality");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Numericality"));
			
			return false;
		}

		return true;
	}
}
