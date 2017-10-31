<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation\Validator
	,WpPepVN\Validation
	,WpPepVN\Validation\Exception
	,WpPepVN\Validation\Message
;

/**
 * WpPepVN\Validation\Validator\ExclusionIn
 *
 * Check if a value is not included into a list of values
 *
 *<code>
 *use WpPepVN\Validation\Validator\ExclusionIn;
 *
 *$validator->add('status', new ExclusionIn(array(
 *   'message' => 'The status must not be A or B',
 *   'domain' => array('A', 'B')
 *)));
 *</code>
 */
class ExclusionIn extends Validator
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
			throw new Exception('Option \'domain\' must be an array');
		}

		/**
		 * Check if the value is contained by the array
		 */
		if (in_array($value, $domain)) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label, ':domain' =>  join(', ', $domain));
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("ExclusionIn");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "ExclusionIn"));
			
			return false;
		}

		return true;
	}

}
