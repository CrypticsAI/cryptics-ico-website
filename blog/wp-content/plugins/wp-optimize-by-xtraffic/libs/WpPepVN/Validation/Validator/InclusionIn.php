<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation;
use WpPepVN\Validation\Validator;
use WpPepVN\Validation\Exception;
use WpPepVN\Validation\Message;

/**
 * WpPepVN\Validation\Validator\InclusionIn
 *
 * Check if a value is included into a list of values
 *
 *<code>
 *use WpPepVN\Validation\Validator\InclusionIn;
 *
 *$validator->add('status', new InclusionIn(array(
 *   'message' => 'The status must be A or B',
 *   'domain' => array('A', 'B')
 *)));
 *</code>
 */
class InclusionIn extends Validator
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

		/**
		 * A domain is an array with a list of valid values
		 */
		$domain = $this->getOption("domain");
		
		if(!is_array($domain)) {
			throw new Exception("Option 'domain' must be an array");
		}
		
		$strict = false;
		if ($this->isSetOption("strict")) {
			$strict = $this->getOption("strict");
		}

		/**
		 * Check if the value is contained by the array
		 */
		if (!in_array($value, $domain, $strict)) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label, ':domain' =>  join(', ', $domain));
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("InclusionIn");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "InclusionIn"));
			
			return false;
		}

		return true;
	}
}
