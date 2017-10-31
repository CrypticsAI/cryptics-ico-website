<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\Validator
;

/**
 * WpPepVN\Validation\Validator\Regex
 *
 * Allows validate if the value of a field matches a regular expression
 *
 *<code>
 *use WpPepVN\Validation\Validator\Regex as RegexValidator;
 *
 *$validator->add('created_at', new RegexValidator(array(
 *   'pattern' => '/^[0-9]{4}[-\/](0[1-9]|1[12])[-\/](0[1-9]|[12][0-9]|3[01])$/',
 *   'message' => 'The creation date is invalid'
 *)));
 *</code>
 */
class Regex extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field)
	{
		
		/**
		 * Regular expression is set in the option 'pattern'
		 * Check if the value match using preg_match in the PHP userland
		 */
		$matches = null;
		$value = $validation->getValue($field);

		if ($this->isSetOption("allowEmpty") && empty ($value)) {
			return true;
		}

		if (preg_match($this->getOption("pattern"), $value, $matches)) {
			$failed = $matches[0] != $value;
		} else {
			$failed = true;
		}

		if ($failed === true) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field'=> $label);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Regex");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Regex"));
			
			return false;
		}

		return true;
	}
}
