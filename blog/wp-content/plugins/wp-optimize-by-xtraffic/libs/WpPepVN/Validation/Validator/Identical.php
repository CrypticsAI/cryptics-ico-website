<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation\Validator
	,WpPepVN\Validation
	,WpPepVN\Validation\Message
;

/**
 * WpPepVN\Validation\Validator\Identical
 *
 * Checks if a value is identical to other
 *
 *<code>
 *use WpPepVN\Validation\Validator\Identical;
 *
 *$validator->add('terms', new Identical(array(
 *   'accepted' => 'yes',
 *   'message' => 'Terms and conditions must be accepted'
 *)));
 *</code>
 *
 */
class Identical extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field) 
	{
		
		if ($validation->getValue($field) != $this->getOption("accepted")) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Identical");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Identical"));
			
			return false;
		}

		return true;
	}
}
