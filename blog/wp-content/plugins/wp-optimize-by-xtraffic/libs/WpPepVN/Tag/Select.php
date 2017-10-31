<?php 

namespace WpPepVN\Tag;

use WpPepVN\Tag\Exception;
use WpPepVN\Tag as BaseTag;

/**
 * WpPepVN\Tag\Select
 *
 * Generates a SELECT html tag using a static array of values or a WpPepVN\Mvc\Model resultset
 */
abstract class Select
{

	/**
	 * Generates a SELECT tag
	 *
	 * @param array parameters
	 * @param array data
	 */
	public static function selectField($parameters, $data = null)
	{
		if(!is_array($parameters)) {
			$params = array($parameters, $data);
		} else {
			$params = $parameters;
		}
		
		$id = null;
		
		if(isset($params[0])) {
			$id = $params[0];
		} else if(isset($params['id'])) {
			$params[0] = $params['id'];
			$id = $params[0];
		}

		/**
		 * Automatically assign the id if the name is not an array
		 */
		if (false === strpos($id, '[')) {
			if (!isset ($params['id'])) {
				$params['id'] = $id;
			}
		}

		if(!isset($params['name'])) {
			$params['name'] = $id;
		} else {
			$name = $params['name'];
			if (!$name) {
				$params['name'] = $id;
			}
		}

		if(!isset($params['value'])) {
			$value = BaseTag::getValue($id, $params);
		} else {
			$value = $params['value'];
			unset ($params['value']);
		}
		
		$useEmpty = false;

		if(isset($params['useEmpty'])) {
			$useEmpty = $params['useEmpty'];
			
			if(!isset($params['emptyValue'])) {
				$emptyValue = '';
			} else {
				$emptyValue = $params['emptyValue'];
				unset ($params['emptyValue']);
			}

			if(!isset($params['emptyText'])) {
				$emptyText = 'Choose...';
			} else {
				$emptyText = $params['emptyText'];
				unset ($params['emptyText']);
			}

			unset ($params['useEmpty']);
		}

		if(!isset($params[1])) {
			$options = $data;
		} else {
			$options = $params[1];
		}

		if(is_object($options)) {

			/**
			 * The options is a resultset
			 */
			if(!isset($params['using'])) {
				throw new Exception('The \'using\' parameter is required');
			} else {
				$using = $params['using'];
				
				if(!is_array($using) && !is_object($using)) {
					throw new Exception('The \'using\' parameter should be an array');
				}
			}
		}

		unset ($params['using']);

		$code = BaseTag::renderAttributes('<select', $params) . '>' . PHP_EOL;

		if ($useEmpty) {
			/**
			 * Create an empty value
			 */
			$code .= '<option value="' . $emptyValue . '">' . $emptyText . '</option>' . PHP_EOL;
		}

		if(is_object($options)) {

			/**
			 * Create the SELECT's option from a resultset
			 */
			$code .= self::_optionsFromResultset($options, $using, $value, '</option>' . PHP_EOL);

		} else if(is_array($options)) {

			/**
			 * Create the SELECT's option from an array
			 */
			$code .= self::_optionsFromArray($options, $value, '</option>' . PHP_EOL);
		} else {
			throw new Exception('Invalid data provided to SELECT helper');
		}
		

		$code .= '</select>';

		return $code;
	}

	/**
	 * Generate the OPTION tags based on a resultset
	 *
	 * @param WpPepVN\Mvc\Model\Resultset resultset
	 * @param array using
	 * @param mixed value
	 * @param string closeOption
	 */
	private static function _optionsFromResultset($resultset, $using, $value, $closeOption)
	{
		$code = '';
		$params = null;
		
		$usingIsArray = false;
		$usingIsObject = false;
		if(is_array($using)) {
			$usingIsArray = true;
		} else if(is_object($using)) {
			$usingIsObject = true;
		}
		
		$valueIsArray = false;
		if(is_array($value)) {
			$valueIsArray = true;
		}
		
		if($usingIsArray) {
			if (count($using) != 2) {
				throw new Exception('Parameter \'using\' requires two values');
			}
			$usingZero = $using[0]; $usingOne = $using[1];
		}

		foreach(iterator($resultset) as $option) {

			if($usingIsArray) {

				if(is_object($option)) {
					if (method_exists($option, 'readAttribute')) {
						$optionValue = $option->readAttribute($usingZero);
						$optionText = $option->readAttribute($usingOne);
					} else {
						$optionValue = $option->usingZero;
						$optionText = $option->usingOne;
					}
				} else {
					if(is_array($option)) {
						$optionValue = $option[$usingZero];
						$optionText = $option[$usingOne];
					} else {
						throw new Exception('Resultset returned an invalid value');
					}
				}

				/**
				 * If the value is equal to the option's value we mark it as selected
				 */
				
				if($valueIsArray) {
					if (in_array($optionValue, $value)) {
						$code .= '<option selected="selected" value="' . $optionValue . '">' . $optionText . $closeOption;
					} else {
						$code .= '<option value="' . $optionValue . '">' . $optionText . $closeOption;
					}
				} else {
					$strOptionValue = (string) $optionValue;
					$strValue = (string) $value;
					if ($strOptionValue === $strValue) {
						$code .= '<option selected="selected" value="' . $strOptionValue . '">' . $optionText . $closeOption;
					} else {
						$code .= '<option value="' . $strOptionValue . '">' . $optionText . $closeOption;
					}
				}
			} else {

				/**
				 * Check if using is a closure
				 */
				if ($usingIsObject) {
					if ($params === null) {
						$params = array();
					}
					$params[0] = $option;
					$code .= call_user_func_array($using, $params);
				}
			}
		}

		return $code;
	}

	/**
	 * Generate the OPTION tags based on an array
	 *
	 * @param array data
	 * @param mixed value
	 * @param string closeOption
	 */
	private static function _optionsFromArray($data, $value, $closeOption)
	{
		$code = '';

		foreach($data as $optionValue => $optionText) {

			$escaped = htmlspecialchars($optionValue);

			if(is_array($optionText)) {
				$code .= '<optgroup label="' . $escaped . '">' . PHP_EOL . self::_optionsFromArray($optionText, $value, $closeOption) . '</optgroup>' . PHP_EOL;
				continue;
			}

			if(is_array($value)) {
				if (in_array($optionValue, $value)) {
					$code .= '<option selected="selected" value="' . $escaped . '">' . $optionText . $closeOption;
				} else {
					$code .= '<option value="' . $escaped . '">' . $optionText . $closeOption;
				}
			} else {

				$strOptionValue = (string) $optionValue;
				$strValue = (string) $value;

				if ($strOptionValue === $strValue) {
					$code .= '<option selected="selected" value="' . $escaped . '">' . $optionText . $closeOption;
				} else {
					$code .= '<option value="' . $escaped . '">' . $optionText . $closeOption;
				}
			}
		}

		return $code;
	}
}
