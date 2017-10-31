<?php 
namespace WpPepVN\Http;

use WpPepVN\DependencyInjection
	,WpPepVN\FilterInterface
	,WpPepVN\DependencyInjection\InjectionAwareInterface
	,WpPepVN\Http\Request\Exception
	,WpPepVN\Http\Request\File
;

/**
 * WpPepVN\Http\Request
 *
 * Encapsulates request information for easy and secure access from application controllers.
 *
 * The request object is a simple value object that is passed between the dispatcher and controller classes.
 * It packages the HTTP request environment.
 *
 *<code>
 *	$request = new \WpPepVN\Http\Request();
 *	if ($request->isPost() == true) {
 *		if ($request->isAjax() == true) {
 *			echo 'Request was made using POST and AJAX';
 *		}
 *	}
 *</code>
 *
 */

class Request implements RequestInterface, InjectionAwareInterface
{

	protected $_dependencyInjector;

	protected $_rawBody;

	protected $_filter;

	protected $_putCache;
	
	private $_tempData = array();
	
	protected $_postData = null;
	
	/**
	 * Sets the dependency injector
	 */
	public function setDI(DependencyInjection $dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns the internal dependency injector
	 */
	public function getDI()
	{
		if(!$this->_dependencyInjector) {
			$this->_dependencyInjector = DependencyInjection::getDefault();
		}
		return $this->_dependencyInjector;
	}

	/**
	 * Gets a variable from the $_REQUEST superglobal applying filters if needed.
	 * If no parameters are given the $_REQUEST superglobal is returned
	 *
	 *<code>
	 *	//Returns value from $_REQUEST['user_email'] without sanitizing
	 *	$userEmail = $request->get('user_email');
	 *
	 *	//Returns value from $_REQUEST['user_email'] with sanitizing
	 *	$userEmail = $request->get('user_email', 'email');
	 *</code>
	 */
	public function get($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
	{
		return $this->getHelper($_REQUEST, $name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
	}

	/**
	 * Gets a variable from the $_POST superglobal applying filters if needed
	 * If no parameters are given the $_POST superglobal is returned
	 *
	 *<code>
	 *	//Returns value from $_POST['user_email'] without sanitizing
	 *	$userEmail = $request->getPost('user_email');
	 *
	 *	//Returns value from $_POST['user_email'] with sanitizing
	 *	$userEmail = $request->getPost('user_email', 'email');
	 *</code>
	 */
	public function getPost($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
	{
		return $this->getHelper($this->getAllPostData(), $name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
	}
	
	public function getAllPostData() 
	{
		if(null === $this->_postData) {
			if(isset($_POST) && is_array($_POST) && !empty($_POST)) {
				$this->_postData = stripslashes_deep((array)$_POST);
			} else {
				$this->_postData = array();
			}
		}
		
		return $this->_postData;
	}
	

	/**
	 * Gets a variable from put request
	 *
	 *<code>
	 *	//Returns value from $_PUT['user_email'] without sanitizing
	 *	$userEmail = $request->getPut('user_email');
	 *
	 *	//Returns value from $_PUT['user_email'] with sanitizing
	 *	$userEmail = $request->getPut('user_email', 'email');
	 *</code>
	 */
	public function getPut($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
	{
		
		if(!is_array($this->_putCache)) {
			
			$put = array();
			
			parse_str(file_get_contents('php://input'), $put);

			$this->_putCache = $put;
		}

		return $this->getHelper($this->_putCache, $name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
	}

	/**
	 * Gets variable from $_GET superglobal applying filters if needed
	 * If no parameters are given the $_GET superglobal is returned
	 *
	 *<code>
	 *	//Returns value from $_GET['id'] without sanitizing
	 *	$id = $request->getQuery('id');
	 *
	 *	//Returns value from $_GET['id'] with sanitizing
	 *	$id = $request->getQuery('id', 'int');
	 *
	 *	//Returns value from $_GET['id'] with a default value
	 *	$id = $request->getQuery('id', null, 150);
	 *</code>
	 */
	public function getQuery($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
	{
		return $this->getHelper($_GET, $name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
	}

	/**
	 * Helper to get data from superglobals, applying filters if needed.
	 * If no parameters are given the superglobal is returned.
	 */
	protected final function getHelper($source, $name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
	{
		
		if ($name === null) {
			return $source;
		}

		if(!isset($source[$name])) {
			return $defaultValue;
		}
		
		$value = $source[$name];
		
		if ($filters !== null) {
			$filter = $this->_filter;
			
			if(!is_object($filter)) {
				if(!is_object($this->_dependencyInjector)) {
					throw new Exception('A dependency injection object is required to access the \'filter\' service');
				}
				$filter = $this->_dependencyInjector->getShared('filter');
				$this->_filter = $filter;
			}
			
			$value = $filter->sanitize($value, $filters, $noRecursive);
		}

		if (empty ($value) && ($notAllowEmpty === true)) {
			return $defaultValue;
		}

		return $value;
	}

	/**
	 * Gets variable from $_SERVER superglobal
	 */
	public function getServer($name)
	{
		
		if(isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
		
		return null;
	}

	/**
	 * Checks whether $_REQUEST superglobal has certain index
	 */
	public function has($name) 
	{
		return isset ($_REQUEST[$name]);
	}

	/**
	 * Checks whether $_POST superglobal has certain index
	 */
	public function hasPost($name) 
	{
		return isset ($_POST[$name]);
	}

	/**
	 * Checks whether the PUT data has certain index
	 */
	public function hasPut($name) 
	{
		$put = $this->getPut();

		return isset ($put[$name]);
	}

	/**
	 * Checks whether $_GET superglobal has certain index
	 */
	public function hasQuery($name)
	{
		return isset ($_GET[$name]);
	}

	/**
	 * Checks whether $_SERVER superglobal has certain index
	 */
	public final function hasServer($name) 
	{
		return isset ($_SERVER[$name]);
	}

	/**
	 * Gets HTTP header from request data
	 */
	public final function getHeader($header) 
	{
		$name = strtoupper(strtr($header, '-', '_'));

		if(isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
		
		if(isset($_SERVER['HTTP_' . $name])) {
			return $_SERVER['HTTP_' . $name];
		}

		return '';
	}

	/**
	 * Gets HTTP schema (http/https)
	 */
	public function getScheme() 
	{
		$k = 'gtsch';
		
		if(!isset($this->_tempData[$k])) {
			
			$https = $this->getServer('HTTPS');
			
			if ($https) {
				$https = strtolower($https);
				if ($https === 'off') {
					$scheme = 'http';
				} else {
					$scheme = 'https';
				}
			} else {
				$scheme = 'http';
			}
			$this->_tempData[$k] = $scheme;
		}
		
		return $this->_tempData[$k];
	}

	/**
	 * Checks whether request has been made using ajax
	 */
	public function isAjax()
	{
		if(isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
			return true;
		}
		
		return false;
	}

	/**
	 * Checks whether request has been made using SOAP
	 */
	public function isSoapRequested() 
	{
		
		if (isset ($_SERVER['HTTP_SOAPACTION'])) {
			return true;
		} else {
			$contentType = $this->getContentType();
			if (!empty($contentType)) {
				if(false !== strpos($contentType, 'application/soap+xml')) {
					return true;
				}
				
			}
		}
		return false;
	}

	/**
	 * Checks whether request has been made using any secure layer
	 */
	public function isSecureRequest()
	{
		return $this->getScheme() === 'https';
	}

	/**
	 * Gets HTTP raw request body
	 */
	public function getRawBody()
	{
		$rawBody = $this->_rawBody;
		if (empty ($rawBody)) {

			$contents = file_get_contents('php://input');

			/**
			 * We need store the read raw body because it can't be read again
			 */
			$this->_rawBody = $contents;
			return $contents;
		}
		
		return $rawBody;
	}

	/**
	 * Gets decoded JSON HTTP raw request body
	 */
	public function getJsonRawBody($associative = false)
	{
		$rawBody = $this->getRawBody();
		
		if (is_string($rawBody)) {
			return false;
		}

		return json_decode($rawBody, $associative);
	}

	/**
	 * Gets active server address IP
	 */
	public function getServerAddress()
	{
		
		if(isset($_SERVER['SERVER_ADDR'])) {
			return $_SERVER['SERVER_ADDR'];
		}
		
		return gethostbyname('localhost');
	}

	/**
	 * Gets active server name
	 */
	public function getServerName()
	{
		
		if(isset($_SERVER['SERVER_NAME'])) {
			return $_SERVER['SERVER_NAME'];
		}

		return 'localhost';
	}

	/**
	 * Gets information about schema, host and port used by the request
	 */
	public function getHttpHost()
	{
		
		/**
		 * Get the server name from _SERVER['HTTP_HOST']
		 */
		$httpHost = $this->getServer('HTTP_HOST');
		if ($httpHost) {
			return $httpHost;
		}

		/**
		 * Get current scheme
		 */
		$scheme = $this->getScheme();

		/**
		 * Get the server name from _SERVER['SERVER_NAME']
		 */
		$name = $this->getServer('SERVER_NAME');

		/**
		 * Get the server port from _SERVER['SERVER_PORT']
		 */
		$port = $this->getServer('SERVER_PORT');

		/**
		 * If is standard http we return the server name only
		 */
		if (($scheme === 'http') && ($port === 80))  {
			return $name;
		}

		/**
		 * If is standard secure http we return the server name only
		 */
		if (($scheme === 'https') && ($port === '443')) {
			return $name;
		}

		return $name . ':' . $port;
	}

	/**
	 * Gets HTTP URI which request has been made
	 */
	public final function getURI()
	{
		
		if(isset($_SERVER['REQUEST_URI'])) {
			return $_SERVER['REQUEST_URI'];
		}
		
		return '';
	}

	/**
	 * Gets most possible client IPv4 Address. This method search in _SERVER['REMOTE_ADDR'] and optionally in _SERVER['HTTP_X_FORWARDED_FOR']
	 */
	public function getClientAddress($trustForwardedHeader = false)
	{
		$address = null;

		/**
		 * Proxies uses this IP
		 */
		if ($trustForwardedHeader) {
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$address = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if(isset($_SERVER['HTTP_CLIENT_IP']) && ($_SERVER['HTTP_CLIENT_IP'])) {
				$address = $_SERVER['HTTP_CLIENT_IP'];
			}
		}

		if ($address === null) {
			if(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'])) {
				$address = $_SERVER['REMOTE_ADDR'];
			}
		}

		if($address && is_string($address)) {
			if(false !== strpos($address, ',')) {
				/**
				 * The client address has multiples parts, only return the first part
				 */
				$tmp = explode(',', $address);
				return $tmp[0];
			}
			return $address;
		}

		return false;
	}

	/**
	 * Gets HTTP method which request has been made
	 */
	public final function getMethod()
	{
		$resultData = 'GET';
		
		if(isset($_POST) && $_POST && !empty($_POST)) {
			$resultData = 'POST';
		} else if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']) {
			$resultData = $_SERVER['REQUEST_METHOD'];
		}
		
		$resultData = trim($resultData);
		$resultData = strtoupper($resultData);
		
		return $resultData;
	}

	/**
	 * Gets HTTP user agent used to made the request
	 */
	public function getUserAgent()
	{
		
		if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']) {
			return $_SERVER['HTTP_USER_AGENT'];
		}
		
		return '';
	}

	/**
	 * Checks if a method is a valid HTTP method
	 */
	public function isValidHttpMethod(string $method) 
	{
		$method = strtoupper($method);

		switch ($method) {

			case 'GET':
			case 'POST':
			case 'PUT':
			case 'DELETE':
			case 'HEAD':
			case 'OPTIONS':
			case 'PATCH':
				return true;
		}

		return false;
	}

	/**
	 * Check if HTTP method match any of the passed methods
	 * When strict is true it checks if validated methods are real HTTP methods
	 */
	public function isMethod($methods, $strict = false) 
	{
		$httpMethod = $this->getMethod();

		if(is_string($methods)) {
			if ($strict && !$this->isValidHttpMethod($methods)) {
				throw new Exception('Invalid HTTP method: ' . $methods);
			}
			return $methods === $httpMethod;
		}
		
		if(is_array($methods)) {
			foreach($methods as $method) {
				if ($strict && !$this->isValidHttpMethod($method)) {
					if(is_string($method)) {
						throw new Exception('Invalid HTTP method: ' . method);
					} else {
						throw new Exception('Invalid HTTP method: non-string');
					}
				}
				if ($method === $httpMethod) {
					return true;
				}
			}
			return false;
		}

		if ($strict) {
			throw new Exception('Invalid HTTP method: non-string');
		}

		return false;
	}

	/**
	 * Checks whether HTTP method is POST. if _SERVER['REQUEST_METHOD']==='POST'
	 */
	public function isPost()
	{
		return $this->getMethod() === 'POST';
	}

	/**
	 * Checks whether HTTP method is GET. if _SERVER['REQUEST_METHOD']==='GET'
	 */
	public function isGet()
	{
		return $this->getMethod() === 'GET';
	}

	/**
	 * Checks whether HTTP method is PUT. if _SERVER['REQUEST_METHOD']==='PUT'
	 */
	public function isPut() 
	{
		return $this->getMethod() === 'PUT';
	}

	/**
	 * Checks whether HTTP method is PATCH. if _SERVER['REQUEST_METHOD']==='PATCH'
	 */
	public function isPatch() 
	{
		return $this->getMethod() === 'PATCH';
	}

	/**
	 * Checks whether HTTP method is HEAD. if _SERVER['REQUEST_METHOD']==='HEAD'
	 */
	public function isHead() 
	{
		return $this->getMethod() === 'HEAD';
	}

	/**
	 * Checks whether HTTP method is DELETE. if _SERVER['REQUEST_METHOD']==='DELETE'
	 */
	public function isDelete() 
	{
		return $this->getMethod() === 'DELETE';
	}

	/**
	 * Checks whether HTTP method is OPTIONS. if _SERVER['REQUEST_METHOD']==='OPTIONS'
	 */
	public function isOptions() 
	{
		return $this->getMethod() === 'OPTIONS';
	}

	/**
	 * Checks whether request include attached files
	 */
	public function hasFiles($onlySuccessful = false)
	{
		$files = $_FILES;
		
		if(!$files || !is_array($files) || empty($files)) {
			return 0;
		}
		
		foreach($files as $file) {
			
			if(isset($file['error'])) {
				if(!is_array($file['error'])) {
					if (!$file['error'] || !$onlySuccessful) {
						$numberFiles++;
					}
				} else {
					$numberFiles += $this->hasFileHelper($error, $onlySuccessful);
				}
			}
		}

		return $numberFiles;
	}

	/**
	 * Recursively counts file in an array of files
	 */
	protected final function hasFileHelper($data, $onlySuccessful) 
	{
		
		$numberFiles = 0;

		if(!is_array($data)) {
			return 1;
		}
 
		foreach($data as $value) {
			if(!is_array($value)) {
				if (!$value || !$onlySuccessful) {
					$numberFiles++;
				}
			} else {
				$numberFiles += $this->hasFileHelper($alue, $onlySuccessful);
			}
		}

		return $numberFiles;
	}

	/**
	 * Gets attached files as WpPepVN\Http\Request\File instances
	 */
	public function getUploadedFiles($onlySuccessful = false)
	{
		$files = array();

		$superFiles = $_FILES;

		if (!empty($superFiles)) {
			foreach($superFiles as $prefix => $input) {
				if(is_array($input['name'])) {
					$smoothInput = $this->smoothFiles($input['name'], $input['type'], $input['tmp_name'], $input['size'], $input['error'], $prefix);

					foreach($smoothInput as $file) {
						if ($onlySuccessful === false || $file['error'] === UPLOAD_ERR_OK) {
							$dataFile = array(
								'name' => $file['name'],
								'type' => $file['type'],
								'tmp_name' => $file['tmp_name'],
								'size' => $file['size'],
								'error' => $file['error']
							);

							$files[] = new File($dataFile, $file['key']);
						}
					}
				} else {
					if ($onlySuccessful === false || $input['error'] === UPLOAD_ERR_OK) {
						$files[] = new File($input, $prefix);
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Smooth out $_FILES to have plain array with all files uploaded
	 */
	protected final function smoothFiles($names, $types, $tmp_names, $sizes, $errors, $prefix)
	{
		$files = array();

		foreach($names as $idx => $name) {
			$p = $prefix . '.' . $idx;

			if(is_string($name)) {

				$files[] = array(
					'name' => $name,
					'type' => $types[$idx],
					'tmp_name' => $tmp_names[$idx],
					'size' => $sizes[$idx],
					'error' => $errors[$idx],
					'key' => $p
				);
			}

			if(!is_array($name)) {
				$parentFiles = $this->smoothFiles($names[$idx], $types[$idx], $tmp_names[$idx], $sizes[$idx], $errors[$idx], $p);

				foreach($parentFiles as $file) {
					$files[] = $file;
				}
			}
		}

		return $files;
	}

	/**
	 * Returns the available headers in the request
	 */
	public function getHeaders()
	{
		$headers = array();
		$contentHeaders = array('CONTENT_TYPE' => true, 'CONTENT_LENGTH' => true);
		
		foreach($_SERVER as $name => $value) {
			if(0 === strpos($name, 'HTTP_')) {
				$name = ucwords(strtolower(str_replace('_', ' ', substr($name, 5))));
				$name = str_replace(' ', '-', $name);
				$headers[$name] = $value;
			} elseif (isset ($contentHeaders[$name])) {
				$name = ucwords(strtolower(str_replace('_', ' ', $name)));
				$name = str_replace(' ', '-', $name);
				$headers[$name] = $value;
			}
		}

		return $headers;
	}

	/**
	 * Gets web page that refers active request. ie: http://www.google.com
	 */
	public function getHTTPReferer() 
	{
		if(isset ($_SERVER['HTTP_REFERER'])) {
			return $_SERVER['HTTP_REFERER'];
		}
		
		return '';
	}

	/**
	 * Process a request header and return an array of values with their qualities
	 */
	protected final function _getQualityHeader($serverIndex, $name) 
	{
		$returnedParts = array();
		$parts = preg_split('/,\\s*/', $this->getServer($serverIndex), -1, PREG_SPLIT_NO_EMPTY);
		
		foreach($parts as $part) {

			$headerParts = array();
			$tmp = preg_split('/\s*;\s*/', trim($part), -1, PREG_SPLIT_NO_EMPTY);
			foreach($tmp as $headerPart) {
				if (strpos($headerPart, '=') !== false) {
					$split = explode('=', $headerPart, 2);
					if ($split[0] === 'q') {
						$headerParts['quality'] = (double) $split[1];
					} else {
						$headerParts[$split[0]] = $split[1];
					}
				} else {
					$headerParts[$name] = $headerPart;
					$headerParts['quality'] = 1.0;
				}
			}

			$returnedParts[] = $headerParts;
		}

		return $returnedParts;
	}

	/**
	 * Process a request header and return the one with best quality
	 */
	protected final function _getBestQuality($qualityParts, $name)
	{
		$i = 0;
		$quality = 0.0;
		$selectedName = '';

		foreach($qualityParts as $accept) {
			if ($i === 0) {
				$quality = (double) $accept['quality'];
				$selectedName = $accept[$name];
			} else {
				$acceptQuality = (double) $accept['quality'];
				if ($acceptQuality > $quality) {
					$quality = $acceptQuality;
					$selectedName = $accept[$name];
				}
			}
			$i++;
		}
		return $selectedName;
	}

	/**
	 * Gets content type which request has been made
	 */
	public function getContentType() 
	{
		
		if(isset($_SERVER['CONTENT_TYPE'])) {
			return $_SERVER['CONTENT_TYPE'];
		} else {
			/**
			 * @see https://bugs.php.net/bug.php?id=66606
			 */
			
			if(isset($_SERVER['HTTP_CONTENT_TYPE'])) {
				return $_SERVER['HTTP_CONTENT_TYPE'];
			}
		}

		return null;
	}

	/**
	 * Gets an array with mime/types and their quality accepted by the browser/client from _SERVER['HTTP_ACCEPT']
	 */
	public function getAcceptableContent() 
	{
		return $this->_getQualityHeader('HTTP_ACCEPT', 'accept');
	}

	/**
	 * Gets best mime/type accepted by the browser/client from _SERVER['HTTP_ACCEPT']
	 */
	public function getBestAccept() 
	{
		return $this->_getBestQuality($this->getAcceptableContent(), 'accept');
	}

	/**
	 * Gets a charsets array and their quality accepted by the browser/client from _SERVER['HTTP_ACCEPT_CHARSET']
	 */
	public function getClientCharsets() 
	{
		return $this->_getQualityHeader('HTTP_ACCEPT_CHARSET', 'charset');
	}

	/**
	 * Gets best charset accepted by the browser/client from _SERVER['HTTP_ACCEPT_CHARSET']
	 */
	public function getBestCharset()
	{
		return $this->_getBestQuality($this->getClientCharsets(), 'charset');
	}

	/**
	 * Gets languages array and their quality accepted by the browser/client from _SERVER['HTTP_ACCEPT_LANGUAGE']
	 */
	public function getLanguages() 
	{
		return $this->_getQualityHeader('HTTP_ACCEPT_LANGUAGE', 'language');
	}

	/**
	 * Gets best language accepted by the browser/client from _SERVER['HTTP_ACCEPT_LANGUAGE']
	 */
	public function getBestLanguage() 
	{
		return $this->_getBestQuality($this->getLanguages(), 'language');
	}


	/**
	 * Gets auth info accepted by the browser/client from $_SERVER['PHP_AUTH_USER']
	 */
	public function getBasicAuth() 
	{
		
		if (isset ($_SERVER['PHP_AUTH_USER']) && isset ($_SERVER['PHP_AUTH_PW'])) {
			$auth = array();
			$auth['username'] = $_SERVER['PHP_AUTH_USER'];
			$auth['password'] = $_SERVER['PHP_AUTH_PW'];
			return $auth;
		}

		return null;
	}

	/**
	 * Gets auth info accepted by the browser/client from $_SERVER['PHP_AUTH_DIGEST']
	 */
	public function getDigestAuth() 
	{
		$auth = array();
		
		if (isset ($_SERVER['PHP_AUTH_DIGEST'])) {
			$matches = array();
			if (!preg_match_all('#(\\w+)=([\'\"]?)([^\'\" ,]+)\\2#', $_SERVER['PHP_AUTH_DIGEST'], $matches, 2)) {
				return $auth;
			}
			
			if(is_array($matches)) {
				foreach($matches as $match) {
					$auth[$match[1]] = $match[3];
				}
			}
		}

		return $auth;
	}
	
	public function getFullUri()
	{
		$k = 'gtFlUri';
		
		if(!isset($this->_tempData[$k])) {
			
			$rs = $this->getScheme() . '://';
			
			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
				$rs .= $_SERVER['HTTP_HOST'];
			} else if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
				$rs .= $_SERVER['SERVER_NAME'];
			} else {
				$rs = '';
			}
			if($rs) {
				if(isset($_SERVER['REQUEST_URI'])) {
					$rs .= $_SERVER['REQUEST_URI'];
				}
			}
			
			$this->_tempData[$k] = $rs;
		}
		
		return $this->_tempData[$k];
	}
	
	public function parse_url($url)
	{
		
		/*** 
			get the url parts 
			Exam: http://username:password@hostname/path?arg=value#anchor
			
			[scheme] => http
			[host] => hostname
			[user] => username
			[pass] => password
			[path] => /path
			[query] => arg=value
			[fragment] => anchor
			'domain'
			'root'
			'url_no_parameters'
			'parameters'
		***/
		
		$url = trim($url);
		
		if(!$url) {
			return false;
		}
		
		$parts = parse_url($url);
		
		$domain = (isset($parts['host']) ? $parts['host'] : '');
		if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			$parts['domain'] = $regs['domain'];
		}
		
		if(isset($parts['scheme']) && isset($parts['host'])) {
			$parts['root'] = $parts['scheme'].'://'.$parts['host'];
		}
		
		if(isset($parts['root']) && !isset($parts['path'])) {
			$valueTemp = $url;
			$valueTemp = explode('?', $valueTemp, 2);
			$parts['path'] = str_replace($parts['root'], '', trim($valueTemp[0]));
		}
		
		if(isset($parts['root']) && isset($parts['path'])) {
			$parts['url_no_parameters'] = $parts['root'].$parts['path']; 
		}
		
		if(isset($parts['query'])) {
			parse_str($parts['query'], $parseStr);
			$parts['parameters'] = $parseStr;
			unset($parseStr);
		}
		
		/*** return the host domain ***/
		//return $parts['scheme'].'://'.$parts['host'];
		return $parts;
		
	}
	
	public function get_parse_url($url = null)
	{
		if(null === $url) {
			$url = $this->getFullUri();
		}
		
		$k = 'z'.crc32('get_parse_url_'.$url);
		
		if(!isset($this->_tempData[$k])) {
			$this->_tempData[$k] = $this->parse_url($url);
		}
		
		return $this->_tempData[$k];
	}
	
	public function getTopDomain()
	{
		$k = 'getTopDomain';
		
		if(!isset($this->_tempData[$k])) {
			$this->_tempData[$k] = '';
			$parts = $this->get_parse_url();
			if(isset($parts['domain'])) {
				$this->_tempData[$k] = $parts['domain'];
			}
		}
		
		return $this->_tempData[$k];
	}
	
	public function getFullDomain()
	{
		$k = 'getFullDomain';
		
		if(!isset($this->_tempData[$k])) {
			$this->_tempData[$k] = '';
			$parts = $this->get_parse_url();
			if(isset($parts['host'])) {
				$this->_tempData[$k] = $parts['host'];
			}
		}
		
		return $this->_tempData[$k];
	}
	
	public function getRequestTime()
	{
		$k = '_gtrqtm';
		
		if(!isset($this->_tempData[$k])) {
			
			if(isset($_SERVER['REQUEST_TIME']) && $_SERVER['REQUEST_TIME']) {
				$this->_tempData[$k] = $_SERVER['REQUEST_TIME'];
			} else {
				$this->_tempData[$k] = time();
			}
			
			$this->_tempData[$k] = (int)$this->_tempData[$k];
			
		}
		
		return $this->_tempData[$k];
	}
	
}