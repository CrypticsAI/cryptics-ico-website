<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\Validator
;

/**
 * WpPepVN\Validation\Validator\Email
 *
 * Checks if a value has a correct e-mail format
 *
 *<code>
 *use WpPepVN\Validation\Validator\Email as EmailValidator;
 *
 *$validator->add('email', new EmailValidator(array(
 *   'message' => 'The e-mail is not valid'
 *)));
 *</code>
 */
class Email extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field) 
	{
		$value = $validation->getValue($field);

		if ($this->isSetOption('allowEmpty') && empty ($value)) {
			return true;
		}

		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {

			$label = $this->getOption('label');
			if (empty($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption('message');
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage('Email');
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, 'Email'));
			
			return false;
		}

		return true;
	}
}