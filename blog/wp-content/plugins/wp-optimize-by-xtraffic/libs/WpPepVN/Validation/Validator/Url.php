<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\Validator
;

/**
 * WpPepVN\Validation\Validator\Url
 *
 * Checks if a value has a url format
 *
 *<code>
 *use WpPepVN\Validation\Validator\Url as UrlValidator;
 *
 *$validator->add('url', new UrlValidator(array(
 *   'message' => ':field must be a url'
 *)));
 *</code>
 */
class Url extends Validator
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

		if (!filter_var($value, FILTER_VALIDATE_URL)) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Url");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Url"));
			
			return false;
		}

		return true;
	}
}
