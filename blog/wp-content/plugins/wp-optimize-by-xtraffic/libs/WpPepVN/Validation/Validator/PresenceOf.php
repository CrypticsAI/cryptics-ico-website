<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation;
use WpPepVN\Validation\Message;
use WpPepVN\Validation\Validator;

/**
 * WpPepVN\Validation\Validator\PresenceOf
 *
 * Validates that a value is not null or empty string
 *
 *<code>
 *use WpPepVN\Validation\Validator\PresenceOf;
 *
 *$validator->add('name', new PresenceOf(array(
 *   'message' => 'The name is required'
 *)));
 *</code>
 */
class PresenceOf extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field)
	{
		$value = $validation->getValue($field);
		
		if ($value === null || $value === "") {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption('message');
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("PresenceOf");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "PresenceOf"));
			
			return false;
		}

		return true;
	}

}
