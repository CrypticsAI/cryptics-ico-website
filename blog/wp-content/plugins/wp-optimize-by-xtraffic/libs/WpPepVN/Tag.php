<?php 

namespace WpPepVN;

use WpPepVN\Tag\Select
	,WpPepVN\Tag\Exception
	,WpPepVN\Mvc\UrlInterface
	,WpPepVN\DependencyInjection
	,WpPepVN\System
	,WpPepVN\Text\Slug as TextSlug
;

/**
 * WpPepVN\Tag
 *
 * WpPepVN\Tag is designed to simplify building of HTML tags.
 * It provides a set of helpers to generate HTML in a dynamic way.
 * This component is an abstract class that you can extend to add more helpers.
 */
class Tag
{
	/**
	 * Pre-assigned values for components
	 */
	protected static $_displayValues;

	/**
	 * HTML document title
	 */
	protected static $_documentTitle = null;

	protected static $_documentTitleSeparator = null;

	protected static $_documentType = 11;

	/**
	 * Framework Dispatcher
	 */
	protected static $_dependencyInjector;

	protected static $_urlService = null;

	protected static $_dispatcherService = null;

	protected static $_escaperService = null;

	protected static $_autoEscape = true;

	const HTML32 = 1;

	const HTML401_STRICT = 2;

	const HTML401_TRANSITIONAL = 3;

	const HTML401_FRAMESET = 4;

	const HTML5 = 5;

	const XHTML10_STRICT = 6;

	const XHTML10_TRANSITIONAL = 7;

	const XHTML10_FRAMESET = 8;

	const XHTML11 = 9;

	const XHTML20 = 10;

	const XHTML5 = 11;
	
	/**
	 * Obtains the 'escaper' service if required
	 *
	 * @param array params
	 * @return EscaperInterface
	 */
	public static function getEscaper($params)
	{
		
		if(isset($params['escape'])) {
			$autoescape = $params['escape'];
		} else {
			$autoescape = self::$_autoEscape;
		}

		if ($autoescape) {
			$result = self::getEscaperService();
		} else {
			$result = null;
		}

		return $result;
	}

	/**
	 * Renders parameters keeping order in their HTML attributes
	 */
	public static function renderAttributes($code, $attributes)
	{
		$order = array(
			'rel'    => null,
			'type'   => null,
			'for'    => null,
			'src'    => null,
			'href'   => null,
			'action' => null,
			'id'     => null,
			'name'   => null,
			'value'  => null,
			'class'  => null
		);

		$attrs = array();
		
		foreach($order as $key => $value) {
			if(isset($attributes[$key])) {
				$attrs[$key] = $attributes[$key];
			}
		}

		foreach($attributes as $key => $value) {
			if (!isset ($attrs[$key])) {
				$attrs[$key] = $value;
			}
		}

		$escaper = self::getEscaper($attributes);

		unset ($attrs['escape']);

		$newCode = $code;
		foreach($attrs as $key => $value) {
			if(is_string($key) && ($value !== null)) {
				if(is_array($value) || is_resource($value)) {
					throw new Exception('Value at index: \'' . $key . '\' type: \'' . gettype($value) . '\' cannot be rendered');
				}
				if ($escaper) {
					$escaped = $escaper->escapeHtmlAttr($value);
				} else {
					$escaped = $value;
				}
				$newCode .= ' ' . $key . '="' . $escaped . '"';
			}
		}

		return $newCode;
	}

	/**
	 * Sets the dependency injector container.
	 */
	public static function setDI(DependencyInjection $dependencyInjector)
	{
		self::$_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Internally gets the request dispatcher
	 */
	public static function getDI()
	{
		
		if(!is_object(self::$_dependencyInjector)) {
			self::$_dependencyInjector = DependencyInjection::getDefault(null);
		}
		
		return self::$_dependencyInjector;
	}

	/**
	 * Returns a URL service from the default DI
	 */
	public static function getUrlService() 
	{
		$url = self::$_urlService;
		
		if(!is_object($url)) {

			$dependencyInjector = self::getDI();
			
			if(!is_object($dependencyInjector)) {
				throw new Exception('A dependency injector container is required to obtain the \'url\' service');
			}

			$url = $dependencyInjector->getShared('url');
			self::$_urlService = $url;
		}
		return $url;
	}

	/**
	 * Returns an Escaper service from the default DI
	 */
	public static function getEscaperService()
	{
		
		if(!is_object(self::$_escaperService)) {
			
			$dependencyInjector = self::getDI();
			
			if(!is_object($dependencyInjector)) {
				throw new Exception('A dependency injector container is required to obtain the \'escaper\' service');
			}

			self::$_escaperService = $dependencyInjector->getShared('escaper');
		}
		return self::$_escaperService;
	}

	/**
	 * Set autoescape mode in generated html
	 */
	public static function setAutoescape($autoescape)
	{
		self::$_autoEscape = $autoescape;
	}

	/**
	 * Assigns default values to generated tags by helpers
	 *
	 * <code>
	 * //Assigning 'peter' to 'name' component
	 * WpPepVN\Tag::setDefault('name', 'peter');
	 *
	 * //Later in the view
	 * echo WpPepVN\Tag::textField('name'); //Will have the value 'peter' by default
	 * </code>
	 *
	 * @param string id
	 * @param string value
	 */
	public static function setDefault($id, $value) 
	{
		if ($value !== null) {
			if(is_array($value) || is_object($value)) {
				throw new Exception('Only scalar values can be assigned to UI components');
			}
		}
		self::$_displayValues[$id] = $value;
	}

	/**
	 * Assigns default values to generated tags by helpers
	 *
	 * <code>
	 * //Assigning 'peter' to 'name' component
	 * WpPepVN\Tag::setDefaults(array('name' => 'peter'));
	 *
	 * //Later in the view
	 * echo WpPepVN\Tag::textField('name'); //Will have the value 'peter' by default
	 * </code>
	 */
	public static function setDefaults($values, $merge = false) 
	{
		
		if ($merge) {
			$displayValues = self::$_displayValues;
			
			if(is_array($displayValues)) {
				self::$_displayValues = array_merge($displayValues, $values);
			} else {
				self::$_displayValues = $values;
			}
		} else {
			self::$_displayValues = $values;
		}
	}

	/**
	 * Alias of WpPepVN\Tag::setDefault
	 *
	 * @param string id
	 * @param string value
	 */
	public static function displayTo($id, $value)
	{
		return self::setDefault($id, $value);
	}

	/**
	 * Check if a helper has a default value set using WpPepVN\Tag::setDefault or value from _POST
	 *
	 * @param string name
	 * @return boolean
	 */
	public static function hasValue($name) 
	{
		/**
		 * Check if there is a predefined value for it
		 */
		if (isset (self::$_displayValues[$name])) {
			return true;
		}

		/**
		 * Check if there is a post value for the item
		 */
		return isset ($_POST[$name]);
	}

	/**
	 * Every helper calls this function to check whether a component has a predefined
	 * value using WpPepVN\Tag::setDefault or value from _POST
	 *
	 * @param string name
	 * @param array params
	 * @return mixed
	 */
	public static function getValue($name, $params = null)
	{
		
		if (!isset($params['value'])) {
			/**
			 * Check if there is a predefined value for it
			 */
			if (!isset(self::$_displayValues[$name])) {
				/**
				 * Check if there is a post value for the item
				 */
				if (!isset($_POST[$name])) {
					return null;
				} else {
					$value = $_POST[$name];
				}
			} else {
				$value = self::$_displayValues[$name];
			}
		} else {
			$value = $params['value'];
		}

		return $value;
	}

	/**
	 * Resets the request and internal values to avoid those fields will have any default value
	 */
	public static function resetInput()
	{
		self::$_displayValues = array();
		$_POST = array();
	}

	/**
	 * Builds a HTML A tag using framework conventions
	 *
	 *<code>
	 *	echo WpPepVN\Tag::linkTo('signup/register', 'Register Here!');
	 *	echo WpPepVN\Tag::linkTo(array('signup/register', 'Register Here!'));
	 *	echo WpPepVN\Tag::linkTo(array('signup/register', 'Register Here!', 'class' => 'btn-primary'));
	 *	echo WpPepVN\Tag::linkTo('http://phalconphp.com/', 'WpPepVN', FALSE);
	 *	echo WpPepVN\Tag::linkTo(array('http://phalconphp.com/', 'WpPepVN Home', FALSE));
	 *	echo WpPepVN\Tag::linkTo(array('http://phalconphp.com/', 'WpPepVN Home', 'local' =>FALSE));
	 *</code>
	 *
	 * @param array|string parameters
	 * @param string text
	 * @param boolean local
	 * @return string
	 */
	public static function linkTo($parameters, $text = null, $local = true)
	{
		
		if(!is_array($parameters)) {
			$params = array($parameters, $text, $local);
		} else {
			$params = $parameters;
		}
		
		if(isset($params[0])) {
			$action = $params[0];
		} else {
			if(!isset($params['action'])) {
				$action = '';
			} else {
				$action = $params['action'];
				unset ($params['action']);
			}
		}

		if(!isset($params[1])) {
			if(!isset($params['text'])) {
				$text = '';
			} else {
				$text = $params['text'];
				unset ($params['text']);
			}
		} else {
			$text = $params[1];
		}
		
		if(!isset($params[2])) {
			if(!isset($params['local'])) {
				$local = true;
			} else {
				$text = $params['local'];
				unset ($params['local']);
			}
		} else {
			$local = $params[2];
		}

		if(isset($params['query'])) {
			$query = $params['query'];
			unset ($params['query']);
		} else  {
			$query = null;
		}

		$url = self::getUrlService();
		$params['href'] = $url->get($action, $query, $local);
		$code = self::renderAttributes('<a', $params);
		$code .= '>' . $text . '</a>';

		return $code;
	}

	/**
	 * Builds generic INPUT tags
	 *
	 * @param   string type
	 * @param	array parameters
	 * @param 	boolean asValue
	 * @return	string
	 */
	static protected final function _inputField($type, $parameters, $asValue = false)
	{
		$params = array();
		
		if(!is_array($parameters)) {
			$params[] = $parameters;
		} else {
			$params = $parameters;
		}

		if ($asValue === false) {

			if(!isset($params[0])) {
				$params[0] = $params['id'];
			} else {
				$id = $params[0];
			}

			if(isset($params['name'])) {
				$name = $params['name'];
				if (empty ($name)) {
					$params['name'] = $id;
				}
			} else {
				$params['name'] = $id;
			}

			/**
			 * Automatically assign the id if the name is not an array
			 */
			
			if(is_string($id)) {
				if((false === strpos($id,'[')) && !isset ($params['id'])) { 
					$params['id'] = $id;
				}
			}

			$params['value'] = self::getValue($id, $params);

		} else {
			/**
			 * Use the 'id' as value if the user hadn't set it
			 */
			if (!isset ($params['value'])) {
				if (!isset ($params[0])) {
					$value = $params[0];
					$params['value'] = $value;
				}
			}
		}

		$params['type'] = $type;
		$code = self::renderAttributes('<input', $params);

		/**
		 * Check if Doctype is XHTML
		 */
		if (self::$_documentType > self::HTML5) {
			$code .= ' />';
		} else {
			$code .= '>';
		}

		return $code;
	}

	/**
	 * Builds INPUT tags that implements the checked attribute
	 *
	 * @param   string type
	 * @param	array parameters
	 * @return	string
	 */
	static protected final function _inputFieldChecked($type, $parameters)
	{
		
		
		if(!is_array($parameters)) {
			$params = array(parameters);
		} else {
			$params = $parameters;
		}

		if (!isset ($params[0])) {
			$params[0] = $params['id'];
		}

		$id = $params[0];
		if  (!isset ($params['name'])) {
			$params['name'] = $id;
		} else {
			$name = $params['name'];
			if  (empty ($name)) {
				$params['name'] = $id;
			}
		}

		/**
		* Automatically assign the id if the name is not an array
		*/
		if (false === strpos($id, '[')) {
			if (!isset ($params['id'])) {
				$params['id'] = $id;
			}
		}

		/**
		 * Automatically check inputs
		 */
		
		if(isset($params['value'])) {
			$currentValue = $params['value'];
			unset ($params['value']);

			$value = self::getValue($id, $params);

			if ($value && ($currentValue == $value)) {
				$params['checked'] = 'checked';
			}
			$params['value'] = $currentValue;
		} else {
			$value = self::getValue($id, $params);

			/**
			* Evaluate the value in POST
			*/
			if ($value) {
				$params['checked'] = 'checked';
			}

			/**
			* Update the value anyways
			*/
			$params['value'] = $value;
		}

		$params['type'] = $type;
		$code = self::renderAttributes('<input', $params);

		/**
		 * Check if Doctype is XHTML
		 */
		if (self::$_documentType > self::HTML5) {
			$code .= ' />';
		} else {
			$code .= '>';
		}

		return $code;
	}

	/**
	 * Builds a HTML input[type='color'] tag
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function colorField($parameters)
	{
		return self::_inputField('color', $parameters);
	}

	/**
	 * Builds a HTML input[type='text'] tag
	 *
	 * <code>
	 *	echo WpPepVN\Tag::textField(array('name', 'size' => 30));
	 * </code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function textField($parameters)
	{
		return self::_inputField('text', $parameters);
	}

	/**
	 * Builds a HTML input[type='number'] tag
	 *
	 * <code>
	 *	echo WpPepVN\Tag::numericField(array('price', 'min' => '1', 'max' => '5'));
	 * </code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function numericField($parameters)
	{
		return self::_inputField('number', $parameters);
	}


	/**
	* Builds a HTML input[type='range'] tag
	*
	* @param array parameters
	* @return string
	*/
	public static function rangeField($parameters) 
	{
		return self::_inputField('range', $parameters);
	}

	/**
	 * Builds a HTML input[type='email'] tag
	 *
	 * <code>
	 *	echo WpPepVN\Tag::emailField('email');
	 * </code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function emailField($parameters) 
	{
		return self::_inputField('email', $parameters);
	}

	/**
	 * Builds a HTML input[type='date'] tag
	 *
	 * <code>
	 *	echo WpPepVN\Tag::dateField(array('born', 'value' => '14-12-1980'))
	 * </code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function dateField($parameters)
	{
		return self::_inputField('date', $parameters);
	}

	/**
	* Builds a HTML input[type='datetime'] tag
	*
	* @param array parameters
	* @return string
	*/
	public static function dateTimeField($parameters)
	{
		return self::_inputField('datetime', $parameters);
	}

	/**
	* Builds a HTML input[type='datetime-local'] tag
	*
	* @param array parameters
	* @return string
	*/
	public static function dateTimeLocalField($parameters)
	{
		return self::_inputField('datetime-local', $parameters);
	}

	/**
	 * Builds a HTML input[type='month'] tag
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function monthField($parameters)
	{
		return self::_inputField('month', $parameters);
	}

	/**
	 * Builds a HTML input[type='time'] tag
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function timeField($parameters)
	{
		return self::_inputField('time', $parameters);
	}

	/**
	 * Builds a HTML input[type='week'] tag
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function weekField($parameters)
	{
		return self::_inputField('week', $parameters);
	}

	/**
	 * Builds a HTML input[type='password'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::passwordField(array('name', 'size' => 30));
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function passwordField($parameters) 
	{
		return self::_inputField('password', $parameters);
	}

	/**
	 * Builds a HTML input[type='hidden'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::hiddenField(array('name', 'value' => 'mike'));
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function hiddenField($parameters)
	{
		return self::_inputField('hidden', $parameters);
	}

	/**
	 * Builds a HTML input[type='file'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::fileField('file');
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function fileField($parameters) 
	{
		return self::_inputField('file', $parameters);
	}

	/**
	 * Builds a HTML input[type='search'] tag
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function searchField($parameters)
	{
		return self::_inputField('search', $parameters);
	}

	/**
	* Builds a HTML input[type='tel'] tag
	*
	* @param array parameters
	* @return string
	*/
	public static function telField($parameters)
	{
		return self::_inputField('tel', $parameters);
	}

	/**
	 * Builds a HTML input[type='url'] tag
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function urlField($parameters)
	{
		return self::_inputField('url', $parameters);
	}

	/**
	 * Builds a HTML input[type='check'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::checkField(array('terms', 'value' => 'Y'));
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function checkField($parameters)
	{
		return self::_inputFieldChecked('checkbox', $parameters);
	}

	/**
	 * Builds a HTML input[type='radio'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::radioField(array('weather', 'value' => 'hot'))
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ radio_field('Save') }}
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function radioField($parameters)
	{
		return self::_inputFieldChecked('radio', $parameters);
	}

	/**
	 * Builds a HTML input[type='image'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::imageInput(array('src' => '/img/button.png'));
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ image_input('src': '/img/button.png') }}
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function imageInput($parameters)
	{
		return self::_inputField('image', $parameters, true);
	}

	/**
	 * Builds a HTML input[type='submit'] tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::submitButton('Save')
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ submit_button('Save') }}
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function submitButton($parameters)
	{
		return self::_inputField('submit', $parameters, true);
	}

	/**
	 * Builds a HTML SELECT tag using a PHP array for options
	 *
	 *<code>
	 *	echo WpPepVN\Tag::selectStatic('status', array('A' => 'Active', 'I' => 'Inactive'))
	 *</code>
	 *
	 * @param	array parameters
	 * @param   array data
	 * @return	string
	 */
	public static function selectStatic($parameters, $data = null)
	{
		return Select::selectField($parameters, $data);
	}

	/**
	 * Builds a HTML SELECT tag using a WpPepVN\Mvc\Model resultset as options
	 *
	 *<code>
	 *	echo WpPepVN\Tag::select(array(
	 *		'robotId',
	 *		Robots::find('type = 'mechanical''),
	 *		'using' => array('id', 'name')
	 * 	));
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ select('robotId', robots, 'using': ['id', 'name']) }}
	 *</code>
	 *
	 * @param	array parameters
	 * @param   array data
	 * @return	string
	 */
	public static function select($parameters, $data = null)
	{
		return Select::selectField($parameters, $data);
	}

	/**
	 * Builds a HTML TEXTAREA tag
	 *
	 *<code>
	 * echo WpPepVN\Tag::textArea(array('comments', 'cols' => 10, 'rows' => 4))
	 *</code>
	 *
	 * Volt syntax:
	 *<code>
	 * {{ text_area('comments', 'cols': 10, 'rows': 4) }}
	 *</code>
	 *
	 * @param	array parameters
	 * @return	string
	 */
	public static function textArea($parameters)
	{
		
		if(!is_array($parameters)) {
			$params = array($parameters);
		} else {
			$params = $parameters;
		}

		if (!isset ($params[0])) {
			if (isset ($params['id'])) {
				$params[0] = $params['id'];
			}
		}

		$id = $params[0];
		if (!isset ($params['name'])) {
			$params['name'] = $id;
		} else {
			$name = $params['name'];
			if (empty ($name)) {
				$params['name'] = $id;
			}
		}

		if (!isset ($params['id'])) {
			$params['id'] = $id;
		}

		if (isset ($params['value'])) {
			$content = $params['value'];
			unset ($params['value']);
		} else {
			$content = self::getValue($id, $params);
		}

		$code = self::renderAttributes('<textarea', $params);
		$code .= '>' . $content . '</textarea>';

		return $code;
	}

	/**
	 * Builds a HTML FORM tag
	 *
	 * <code>
	 * echo WpPepVN\Tag::form('posts/save');
	 * echo WpPepVN\Tag::form(array('posts/save', 'method' => 'post'));
	 * </code>
	 *
	 * Volt syntax:
	 * <code>
	 * {{ form('posts/save') }}
	 * {{ form('posts/save', 'method': 'post') }}
	 * </code>
	 *
	 * @param array parameters
	 * @return string
	 */
	public static function form($parameters)
	{
		
		if(!is_array($parameters)) {
			$params = array($parameters);
		} else {
			$params = $parameters;
		}
		
		if(isset($params[0])) {
			$paramsAction = $params[0];
		} else if(isset($params['action'])) {
			$paramsAction = $params['action'];
		}

		/**
		 * By default the method is POST
		 */
		if (!isset ($params['method'])) {
			$params['method'] = 'post';
		}

		$action = null;

		if (!empty ($paramsAction)) {
			$action = self::getUrlService()->get($paramsAction);
		}

		/**
		 * Check for extra parameters
		 */
		if(isset($params['parameters'])) {
			$parameters = $params['parameters'];
			$action .= '?' . $parameters;
		}

		if (!empty ($action)) {
			$params['action'] = $action;
		}

		$code = self::renderAttributes('<form', $params);
		$code .= '>';

		return $code;
	}

	/**
	 * Builds a HTML close FORM tag
	 */
	public static function endForm()
	{
		return '</form>';
	}

	/**
	 * Set the title of view content
	 *
	 *<code>
	 * WpPepVN\Tag::setTitle('Welcome to my Page');
	 *</code>
	 */
	public static function setTitle($title) 
	{
		self::$_documentTitle = $title;
	}

	/**
	 * Set the title separator of view content
	 *
	 *<code>
	 * WpPepVN\Tag::setTitleSeparator('-');
	 *</code>
	 */
	public static function setTitleSeparator($titleSeparator)
	{
		self::$_documentTitleSeparator = $titleSeparator;
	}

	/**
	 * Appends a text to current document title
	 */
	public static function appendTitle($title) 
	{
		self::$_documentTitle = self::$_documentTitle . self::$_documentTitleSeparator . $title;
	}

	/**
	 * Prepends a text to current document title
	 */
	public static function prependTitle($title)
	{
		self::$_documentTitle = $title . self::$_documentTitleSeparator . self::$_documentTitle;
	}

	/**
	 * Gets the current document title
	 *
	 * <code>
	 * 	echo WpPepVN\Tag::getTitle();
	 * </code>
	 *
	 * <code>
	 * 	{{ get_title() }}
	 * </code>
	 */
	public static function getTitle($tags = true)
	{
		$documentTitle = self::$_documentTitle;
		if ($tags) {
			return '<title>' . $documentTitle . '</title>' . PHP_EOL;
		}
		return $documentTitle;
	}

	/**
	 * Gets the current document title separator
	 *
	 * <code>
	 *         echo WpPepVN\Tag::getTitleSeparator();
	 * </code>
	 *
	 * <code>
	 *         {{ get_title_separator() }}
	 * </code>
	 */
	public static function getTitleSeparator()
	{
		return self::$_documentTitleSeparator;
	}

	/**
	 * Builds a LINK[rel='stylesheet'] tag
	 *
	 * <code>
	 * 	echo WpPepVN\Tag::stylesheetLink('http://fonts.googleapis.com/css?family=Rosario', false);
	 * 	echo WpPepVN\Tag::stylesheetLink('css/style.css');
	 * </code>
	 *
	 * Volt Syntax:
	 *<code>
	 * 	{{ stylesheet_link('http://fonts.googleapis.com/css?family=Rosario', false) }}
	 * 	{{ stylesheet_link('css/style.css') }}
	 *</code>
	 *
	 * @param	array parameters
	 * @param   boolean local
	 * @return	string
	 */
	public static function stylesheetLink($parameters = null, $local = true)
	{
		
		if(!is_array($parameters)) {
			$params = array($parameters,$local);
		} else {
			$params = $parameters;
		}

		if (isset ($params[1])) {
			$local = (boolean) $params[1];
		} else {
			if (isset ($params['local'])) {
				$local = (boolean) $params['local'];
				unset ($params['local']);
			}
		}

		if (!isset ($params['type'])) {
			$params['type'] = 'text/css';
		}

		if (!isset ($params['href'])) {
			if (isset ($params[0])) {
				$params['href'] = $params[0];
			} else {
				$params['href'] = '';
			}
		}

		/**
		 * URLs are generated through the 'url' service
		 */
		if ($local === true) {
			$params['href'] = self::getUrlService()->getStatic($params['href']);
		}

		if (!isset ($params['rel'])) {
			$params['rel'] = 'stylesheet';
		}

		$code = self::renderAttributes('<link', $params);

		/**
		 * Check if Doctype is XHTML
		 */
		if (self::$_documentType > self::HTML5) {
			$code .= ' />' . PHP_EOL;
		} else {
			$code .= '>' . PHP_EOL;
		}

		return $code;
	}

	/**
	 * Builds a SCRIPT[type='javascript'] tag
	 *
	 * <code>
	 *         echo WpPepVN\Tag::javascriptInclude('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', false);
	 *         echo WpPepVN\Tag::javascriptInclude('javascript/jquery.js');
	 * </code>
	 *
	 * Volt syntax:
	 * <code>
	 * {{ javascript_include('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', false) }}
	 * {{ javascript_include('javascript/jquery.js') }}
	 * </code>
	 *
	 * @param array parameters
	 * @param   boolean local
	 * @return string
	 */
	public static function javascriptInclude($parameters = null, $local = true)
	{
		if(!is_array($parameters)) {
			$params = array($parameters,$local);
		} else {
			$params = $parameters;
		}

		if (isset ($params[1])) {
			$local = (boolean) $params[1];
		} else {
			if (isset ($params['local'])) {
				$local = (boolean) $params['local'];
				unset ($params['local']);
			}
		}

		if (!isset ($params['type'])) {
			$params['type'] = 'text/javascript';
		}

		if (!isset ($params['src'])) {
			if (isset ($params[0])) {
				$params['src'] = $params[0];
			} else {
				$params['src'] = '';
			}
		}

		/**
		 * URLs are generated through the 'url' service
		 */
		if ($local === true) {
			$params['src'] = self::getUrlService()->getStatic($params['src']);
		}
		
		$code = self::renderAttributes('<script', $params);
		$code .= '></script>' . PHP_EOL;

		return $code;
	}

	/**
	 * Builds HTML IMG tags
	 *
	 * <code>
	 *         echo WpPepVN\Tag::image('img/bg.png');
	 *         echo WpPepVN\Tag::image(array('img/photo.jpg', 'alt' => 'Some Photo'));
	 * </code>
	 *
	 * Volt Syntax:
	 * <code>
	 *         {{ image('img/bg.png') }}
	 *         {{ image('img/photo.jpg', 'alt': 'Some Photo') }}
	 *         {{ image('http://static.mywebsite.com/img/bg.png', false) }}
	 * </code>
	 *
	 * @param  array parameters
	 * @param  boolean local
	 * @return string
	 */
	public static function image($parameters = null, $local = true)
	{
		
		if(!is_array($parameters)) {
			$params = array($parameters);
		} else {
			$params = $parameters;
			if (isset ($params[1])) {
				$local = (boolean) $params[1];
			}
		}
		
		if (!isset ($params['src'])) {
			if (isset ($params[0])) {
				$params['src'] = $params[0];
			} else {
				$params['src'] = '';
			}
		}

		/**
		 * Use the 'url' service if the URI is local
		 */
		if ($local) {
			$params['src'] = self::getUrlService()->getStatic($params['src']);
		}

		$code = self::renderAttributes('<img', $params);

		/**
		 * Check if Doctype is XHTML
		 */
		if (self::$_documentType > self::HTML5) {
			$code .= ' />';
		} else {
			$code .= '>';
		}

		return $code;
	}
	
	/**
	 * Converts texts into URL-friendly titles
	 *
	 *<code>
	 * echo WpPepVN\Tag::friendlyTitle('These are big important news', '-')
	 *</code>
	 *
	 * @param string text
	 * @param string separator
	 * @param boolean lowercase
	 * @param mixed replace
	 * @return text
	 */
	public static function friendlyTitle($text, $separator = '-', $lowercase = true, $replace = null)
	{
		return TextSlug::generate($text, $separator, $replace, $lowercase);
	}

	/**
	 * Set the document type of content
	 */
	public static function setDocType($doctype)
	{
		if ($doctype < self::HTML32 || $doctype > self::XHTML5) {
			self::$_documentType = self::HTML5;
		} else {
			self::$_documentType = $doctype;
		}
	}

	/**
	 * Get the document type declaration of content
	 */
	public static function getDocType()
	{
		switch (self::$_documentType)
		{
			case 1:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">' . PHP_EOL;
			/* no break */

			case 2:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\"' . PHP_EOL . ' \"http://www.w3.org/TR/html4/strict.dtd\">' . PHP_EOL;
			/* no break */

			case 3:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"' . PHP_EOL . ' \"http://www.w3.org/TR/html4/loose.dtd\">' . PHP_EOL;
			/* no break */

			case 4:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\"' . PHP_EOL . ' \"http://www.w3.org/TR/html4/frameset.dtd\">' . PHP_EOL;
			/* no break */

			case 6:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"' . PHP_EOL . ' \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">' . PHP_EOL;
			/* no break */

			case 7:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"' . PHP_EOL.' \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">' . PHP_EOL;
			/* no break */

			case 8:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"' . PHP_EOL . ' \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">' . PHP_EOL;
			/* no break */

			case 9:  return '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"' . PHP_EOL . ' \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">' . PHP_EOL;
			/* no break */

			case 10: return '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 2.0//EN\"' . PHP_EOL . ' \"http://www.w3.org/MarkUp/DTD/xhtml2.dtd\">' . PHP_EOL;
			/* no break */

			case 5:
			case 11: return '<!DOCTYPE html>' . PHP_EOL;
			/* no break */
		}

		return '';
	}

	/**
	 * Builds a HTML tag
	 *
	 *<code>
	 *        echo WpPepVN\Tag::tagHtml(name, $parameters, selfClose, onlyStart, eol);
	 *</code>
	 *
	 * @param string tagName
	 * @param array parameters
	 * @param boolean selfClose
	 * @param boolean onlyStart
	 * @param boolean useEol
	 * @return string
	 */
	public static function tagHtml($tagName, $parameters = null, $selfClose = false, $onlyStart = false, $useEol = false)
	{
		if(!is_array($parameters)) {
			$params = array($parameters);
		} else {
			$params = $parameters;
		}

		$localCode = self::renderAttributes('<' . $tagName, $params);

		/**
		 * Check if Doctype is XHTML
		 */
		if (self::$_documentType > self::HTML5) {
			if ($selfClose) {
				$localCode .= ' />';
			} else {
				$localCode .= '>';
			}
		} else {
			if ($onlyStart) {
				$localCode .= '>';
			} else {
				$localCode .= '></' . $tagName . '>';
			}
		}

		if ($useEol) {
			$localCode .= PHP_EOL;
		}

		return $localCode;
	}

	/**
	 * Builds a HTML tag closing tag
	 *
	 *<code>
	 *        echo WpPepVN\Tag::tagHtmlClose('script', true)
	 *</code>
	 */
	public static function tagHtmlClose($tagName, $useEol = false)
	{
		if ($useEol) {
			return '</' . $tagName . '>' . PHP_EOL;
		}
		return '</' . $tagName . '>';
	}
}
