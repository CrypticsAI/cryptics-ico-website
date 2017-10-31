<?php 

namespace WpPepVN\Validation\Validator;

use WpPepVN\Validation
	,WpPepVN\Validation\Message
	,WpPepVN\Validation\Exception
	,WpPepVN\Validation\Validator
;

/**
 * WpPepVN\Validation\Validator\Confirmation
 *
 * Checks that two values have the same value
 *
 *<code>
 *use WpPepVN\Validation\Validator\Confirmation;
 *
 *$validator->add('password', new Confirmation(array(
 *   'message' => 'Password doesn\'t match confirmation',
 *   'with' => 'confirmPassword'
 *)));
 *</code>
 */
class Confirmation extends Validator
{

	/**
	 * Executes the validation
	 */
	public function validate(Validation $validation, $field) 
	{
		$fieldWith = $this->getOption("with");
			$value = $validation->getValue($field);
			$valueWith = $validation->getValue($fieldWith);

		if (!$this->compare($value, $valueWith)) {

			$label = $this->getOption("label");
			if (empty ($label)) {
				$label = $validation->getLabel($field);
			}

			$labelWith = $this->getOption("labelWith");
			if (empty ($labelWith)) {
				$labelWith = $validation->getLabel($fieldWith);
			}
			
			$message = $this->getOption("message");
			$replacePairs = array(':field' => $label, ':with'=>  $labelWith);
			if (empty ($message)) {
				$message = $validation->getDefaultMessage("Confirmation");
			}

			$validation->appendMessage(new Message(strtr($message, $replacePairs), $field, "Confirmation"));
			
			return false;
		}

		return true;
	}

	/**
	 * Compare strings
	 */
	protected final function compare($a, $b)
	{
		if ($this->getOption("ignoreCase", false)) {

			/**
			 * mbstring is required here
			 */
			if (!function_exists("mb_strtolower")) {
				throw new Exception("Extension 'mbstring' is required");
			}

			$a = mb_strtolower($a, "utf-8");
			$b = mb_strtolower($b, "utf-8");
		}

		return $a == $b;
	}
}
