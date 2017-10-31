<?php 

namespace WpPepVN\Mvc;

use WpPepVN\DependencyInjection
	,WpPepVN\DependencyInjection\InjectionAwareInterface
	,WpPepVN\Mvc\UrlInterface
	,WpPepVN\Utils
;

/**
 * WpPepVN\Mvc\Url
 *
 * This components helps in the generation of: URIs, URLs and Paths
 *
 *<code>
 *
 * //Generate a URL appending the URI to the base URI
 * echo $url->get('products/edit/1');
 *
 * //Generate a URL for a predefined route
 * echo $url->get(array('for' => 'blog-post', 'title' => 'some-cool-stuff', 'year' => '2012'));
 *
 *</code>
 */
class Url implements UrlInterface, InjectionAwareInterface
{

	protected $_dependencyInjector;

	protected $_baseUri = null;

	protected $_staticBaseUri = null;

	protected $_basePath = null;

	protected $_router;
	
	protected $_request;
	
	protected $_tempData = array();

	/**
	 * Sets the DependencyInjector container
	 */
	public function setDI(DependencyInjection $dependencyInjector)
	{
		$this->_dependencyInjector = $dependencyInjector;
	}

	/**
	 * Returns the DependencyInjector container
	 */
	public function getDI()
	{
		return $this->_dependencyInjector;
	}
	
	/**
	 * Returns the request
	 */
	public function getRequest()
	{
		if(!$this->_request) {
			$this->_request = $this->getDI()->getShared('request');
		}
		return $this->_request;
	}
	
	

	/**
	 * Sets a prefix for all the URIs to be generated
	 *
	 *<code>
	 *	$url->setBaseUri('/invo/');
	 *	$url->setBaseUri('/invo/index.php/');
	 *</code>
	 */
	public function setBaseUri($baseUri)
	{
		$this->_baseUri = $baseUri;
		if ($this->_staticBaseUri === null) {
			$this->_staticBaseUri = $baseUri;
		}
		return $this;
	}

	/**
	 * Sets a prefix for all static URLs generated
	 *
	 *<code>
	 *	$url->setStaticBaseUri('/invo/');
	 *</code>
	 */
	public function setStaticBaseUri($staticBaseUri)
	{
		$this->_staticBaseUri = $staticBaseUri;
		return $this;
	}

	/**
	 * Returns the prefix for all the generated urls. By default /
	 */
	public function getBaseUri()
	{
		if ($this->_baseUri === null) {
			$this->_baseUri = '/';
		}
		return $this->_baseUri;
	}

	/**
	 * Returns the prefix for all the generated static urls. By default /
	 */
	public function getStaticBaseUri()
	{
		if ($this->_staticBaseUri !== null) {
			return $this->_staticBaseUri;
		}
		return $this->getBaseUri();
	}

	/**
	 * Sets a base path for all the generated paths
	 *
	 *<code>
	 *	$url->setBasePath('/var/www/htdocs/');
	 *</code>
	 */
	public function setBasePath($basePath)
	{
		$this->_basePath = $basePath;
		return $this;
	}

	/**
	 * Returns the base path
	 */
	public function getBasePath()
	{
		return $this->_basePath;
	}

	/**
	 * Generates a URL
	 *
	 *<code>
	 * //Generate a URL appending the URI to the base URI
	 * echo $url->get('products/edit/1');
	 *
	 * //Generate a URL for a predefined route
	 * echo $url->get(array('for' => 'blog-post', 'title' => 'some-cool-stuff', 'year' => '2015'));
	 *</code>
	 */
	public function get($uri = null, $args = null, $local = null, $baseUri = null)
	{
		
		if ($local == null) {
			if(is_string($uri)) {
				if(preg_match('#^((http|ftp)s?:)?//.+#', $uri)) {
					$local = false;
				} else {
					$local = true;
				}
			} else {
				$local = true;
			}
		}

		if(!is_string($baseUri)) {
			$baseUri = $this->getBaseUri();
		}
		
		if ($local) {
			$uri = $baseUri . $uri;			
		}

		if ($args) {
			$queryString = http_build_query($args);
			if(is_string($queryString) && (strlen($queryString)>0)) {
				if (strpos($uri, '?') !== false) {
					$uri .= '&' . $queryString;
				} else {
					$uri .= '?' . $queryString;
				}
			}
		}

		return $uri;
	}
	
	public function getFullUri()
	{
		$k = 'gtFlUri';
		
		if(!isset($this->_tempData[$k])) {
			
			$request = $this->getRequest();
			
			$rs = $request->getScheme() . '://';
			
			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
				$rs .= $_SERVER['HTTP_HOST'];
			} else if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
				$rs .= $_SERVER['SERVER_NAME'];
			}
			
			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
				$rs .= $_SERVER['HTTP_HOST'];
			} else if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
				$rs .= $_SERVER['SERVER_NAME'];
			}
			
			if(isset($_SERVER['REQUEST_URI'])) {
				self::$defaultParams['urlFullRequest'] .= $_SERVER['REQUEST_URI'];
			}
			
			
		}
		
		return $this->_tempData[$k];
	}
	
	public function addParamsToUri($uri = null, $args = null)
	{
		$request = $this->getRequest();
		
		if($uri === null) {
			$uri = $request->getFullUri();
		}
		
		if($uri) {
			if($args && is_string($args)) {
				parse_str($args,$args);
			}
			
			$parsedUrl = $request->get_parse_url($uri);
			if(isset($parsedUrl['url_no_parameters']) && $parsedUrl['url_no_parameters']) {
				$uri = $parsedUrl['url_no_parameters'];
				
				if(!isset($parsedUrl['parameters'])) {
					$parsedUrl['parameters'] = array();
				}
				
				$args = Utils::mergeArrays(array($parsedUrl['parameters'],$args));
			}
			
			
			if($args) {
				
				$args = http_build_query($args);
				if(is_string($args) && !empty($args)) {
					if (strpos($uri, '?') !== false) {
						$uri .= '&' . $args;
					} else {
						$uri .= '?' . $args;
					}
				}
			}
		}
		
		return $uri;
	}

	/**
	 * Generates a URL for a static resource
	 *
	 *<code>
	 * // Generate a URL for a static resource
	 * echo $url->getStatic("img/logo.png");
	 *
	 * // Generate a URL for a static predefined route
	 * echo $url->getStatic(array('for' => 'logo-cdn'));
	 *</code>
	 */
	public function getStatic($uri = null)
	{
		return $this->get($uri, null, null, $this->getStaticBaseUri());
	}

	/**
	 * Generates a local path
	 */
	public function path($path = null)
	{
		return $this->_basePath . $path;
	}
}
