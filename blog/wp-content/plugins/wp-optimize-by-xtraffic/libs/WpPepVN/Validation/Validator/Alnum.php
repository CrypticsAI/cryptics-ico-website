<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Validator
	,WpPepVN\Validation\Message
;

/**
 * WpPepVN\Validation\Validator\Alnum
 *
 * Check for alphanumeric character(s)
 *
 *<code>
 *use WpPepVN\Validation\Validator\Alnum as AlnumValidator;
 *
 *$validator->add('username', new AlnumValidator(array(
 *   'message' => ':field must contain only alphanumeric characters'
 *)));
 *</code>
 */
class Alnum extends Validator
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

		if (!ctype_alnum($value)) {
			
			$label = $this->getOption('label');
			if (empty($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption('message');
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage('Alnum');
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, 'Alnum'));
			
			return false;
		}

		return true;
	}
}
