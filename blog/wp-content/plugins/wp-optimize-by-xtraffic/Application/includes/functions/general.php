<?php 


/**
 * Fake the interpreter function alias if not defined
 */
if ( !function_exists('_') ) {
	if ( function_exists('__') ) {
		function _( $text, $domain='' ) {
			return __($text,$domain);
		}
	} else {
		function _( $text, $domain='' ) {
			return $text;
		}
	}
}

if(!function_exists('http_response_code')) {
	/*
	* http_response_code exist only PHP >= 5.4
	*/
	function http_response_code($code = NULL) 
	{
		if ($code !== NULL) {
			switch ($code) {
				case 100: $text = 'Continue'; break;
				case 101: $text = 'Switching Protocols'; break;
				case 200: $text = 'OK'; break;
				case 201: $text = 'Created'; break;
				case 202: $text = 'Accepted'; break;
				case 203: $text = 'Non-Authoritative Information'; break;
				case 204: $text = 'No Content'; break;
				case 205: $text = 'Reset Content'; break;
				case 206: $text = 'Partial Content'; break;
				case 300: $text = 'Multiple Choices'; break;
				case 301: $text = 'Moved Permanently'; break;
				case 302: $text = 'Moved Temporarily'; break;
				case 303: $text = 'See Other'; break;
				case 304: $text = 'Not Modified'; break;
				case 305: $text = 'Use Proxy'; break;
				case 400: $text = 'Bad Request'; break;
				case 401: $text = 'Unauthorized'; break;
				case 402: $text = 'Payment Required'; break;
				case 403: $text = 'Forbidden'; break;
				case 404: $text = 'Not Found'; break;
				case 405: $text = 'Method Not Allowed'; break;
				case 406: $text = 'Not Acceptable'; break;
				case 407: $text = 'Proxy Authentication Required'; break;
				case 408: $text = 'Request Time-out'; break;
				case 409: $text = 'Conflict'; break;
				case 410: $text = 'Gone'; break;
				case 411: $text = 'Length Required'; break;
				case 412: $text = 'Precondition Failed'; break;
				case 413: $text = 'Request Entity Too Large'; break;
				case 414: $text = 'Request-URI Too Large'; break;
				case 415: $text = 'Unsupported Media Type'; break;
				case 500: $text = 'Internal Server Error'; break;
				case 501: $text = 'Not Implemented'; break;
				case 502: $text = 'Bad Gateway'; break;
				case 503: $text = 'Service Unavailable'; break;
				case 504: $text = 'Gateway Time-out'; break;
				case 505: $text = 'HTTP Version not supported'; break;
				default:
					exit('Unknown http status code "' . htmlentities($code) . '"');
				break;
			}
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' ' . $code . ' ' . $text);
			$GLOBALS['http_response_code'] = $code;
		} else {
			$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
		}
		
		return $code;

	}
}

function wppepvn_cronjob()
{
	global $wpOptimizeByxTraffic;
	if(isset($wpOptimizeByxTraffic) && $wpOptimizeByxTraffic) {
		if(isset($wpOptimizeByxTraffic->di) && $wpOptimizeByxTraffic->di) {
			$wpOptimizeByxTraffic->cronjob_action();
		}
	}
}

function wppepvn_register_clean_cache($data_type = ',common,', $data = array())
{
	global $wpOptimizeByxTraffic;
	if(isset($wpOptimizeByxTraffic) && $wpOptimizeByxTraffic) {
		if(isset($wpOptimizeByxTraffic->di) && $wpOptimizeByxTraffic->di) {
			$cacheManager = $wpOptimizeByxTraffic->di->getShared('cacheManager');
			$cacheManager->registerCleanCache($data_type, $data);
		}
	}
}

function wppepvn_clean_cache($data_type = ',common,', $data = array())
{
	global $wpOptimizeByxTraffic;
	if(isset($wpOptimizeByxTraffic) && $wpOptimizeByxTraffic) {
		if(isset($wpOptimizeByxTraffic->di) && $wpOptimizeByxTraffic->di) {
			$cacheManager = $wpOptimizeByxTraffic->di->getShared('cacheManager');
			$cacheManager->clean_cache($data_type, $data);
		}
	}
}

function wppepvn_get_plugin_version($slug)
{
	$version = 0;
	
	if('wp-optimize-by-xtraffic' === $slug) {
		if(defined('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION')) {
			$version = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION;
		}
	} else if('wp-optimize-speed-by-xtraffic' === $slug) {
		if(defined('WP_OPTIMIZE_SPEED_BY_XTRAFFIC_PLUGIN_VERSION')) {
			$version = WP_OPTIMIZE_SPEED_BY_XTRAFFIC_PLUGIN_VERSION;
		}
	}
	
	return $version;
}

function wppepvn_include_once($file_path, $type = 'include') 
{
    static $includedFiles = array();
	
    if (!isset($includedFiles[$file_path])) {
        $includedFiles[$file_path] = true;
		if(is_file($file_path)) {
			if('include' === $type) {
				include_once($file_path);
			} else {
				require_once($file_path);
			}
		}
    }
}

function wppepvn_get_current_user_hash_via_cookie() 
{
	static $current_user_hash = false;
	
	if(false === $current_user_hash) {
		
		$current_user_hash = array();
		
		if(isset($_COOKIE) && !empty($_COOKIE)) {
			foreach($_COOKIE as $key1 => $value1) {
				if(0 === strpos($key1, 'wordpress_logged_in_')) {
					$current_user_hash[$key1] = $value1;
				}
			}
		}
		
		if(empty($current_user_hash)) {
			$current_user_hash = 0;
		} else {
			$current_user_hash = hash('crc32b', serialize($current_user_hash), false);
		}
		
	}
	
	return $current_user_hash;
}

function wppepvn_is_user_logged_in_via_cookie() 
{
	if(0 === wppepvn_get_current_user_hash_via_cookie()) {
		return false;
	} else {
		return true;
	}
}

function set_data_file_php($file_path,$data,$encode_type='serialize') 
{
	if('serialize' === $encode_type) {
		$data = str_replace('\'','\\\'',(serialize($data)));
		$data = '<?php return unserialize(\''.$data.'\');';
	}
	
	if(file_put_contents($file_path, $data)) {
		//@chmod($file_path, 0755);
		return true;
	}
	
	return false;
	
}

function wppepvn_is_ajax() 
{
	$isAjaxStatus = false;
	
	if (defined('DOING_AJAX') && DOING_AJAX) {
		$isAjaxStatus = true;
	}
	
	if(false === $isAjaxStatus) {
		if(isset ($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
			$isAjaxStatus = true;
		}
	}
	
	return $isAjaxStatus;
}

function wppepvn_is_pagenow($input_pagesnow, $script_pagenow = false) 
{
	static $storeData = array();
	
	$k = wppepvn_hash_key(array(
		'wppepvn_is_pagenow'
		,$input_pagesnow
		,$script_pagenow
	));
	
	if(isset($storeData[$k])) {
		return $storeData[$k];
	}
	
	$storeData[$k] = false;
	
	$input_pagesnow = (array)$input_pagesnow;
	
	if(!$script_pagenow) {
		if(isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] && is_string($GLOBALS['pagenow'])) {
			$script_pagenow = trim($GLOBALS['pagenow']);
		} else if(isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] && is_string($_SERVER['PHP_SELF'])) {
			$script_pagenow = trim($_SERVER['PHP_SELF']);
		}
	}
	
	if($script_pagenow) {
		$script_pagenow = trim($script_pagenow);
		$script_pagenow = basename($script_pagenow);
		
		foreach($input_pagesnow as $key1 => $value1) {
			$value1 = trim($value1);
			$value1 = basename($value1);
			if($value1 === $script_pagenow) {
				$storeData[$k] = true;
				break;
			}
		}
		
	}
	
	return $storeData[$k];
}

function wppepvn_is_loginpage() 
{
	$pages = array('wp-login.php', 'wp-register.php');
	
	$status = wppepvn_is_pagenow($pages);
	
	if(!$status) {
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
			$script = wppepvn_parse_url($_SERVER['HTTP_REFERER']);
			if(isset($script['url_no_parameters'])) {
				
				$script = basename($script['url_no_parameters']);
				
				$status = wppepvn_is_pagenow($pages,$script);
			}
			
			
		}
	}
	
	return $status;
}

function wppepvn_request_method()
{
	static $request_method = false;
	
	if(false === $request_method) {
		$request_method = 'GET';
		
		if(isset($_POST) && $_POST && !empty($_POST)) {
			$request_method = 'POST';
		} else if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']) {
			$request_method = $_SERVER['REQUEST_METHOD'];
		} else if(isset($_FILES) && $_FILES && !empty($_FILES)) {
			$request_method = 'POST';
		}
		
		$request_method = trim($request_method);
		$request_method = strtoupper($request_method);
	}
	
	return $request_method;
}

function wppepvn_is_request_method($method)
{
	$method = trim($method);
	
	$method = strtoupper($method);
	
	if($method === wppepvn_request_method()) {
		return true;
	}
	
	return false;
}

function wppepvn_is_ssl() 
{
	if ( isset($_SERVER['HTTPS']) ) {
		if ( 'on' == strtolower($_SERVER['HTTPS']) ) {
			return true;
		}
		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} else if ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	} else if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && ( 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) {
		return true;
	}
	
	return false;
}

function wppepvn_request_scheme()
{
	if(wppepvn_is_ssl()) {
		return 'https';
	} else {
		return 'http';
	}
}

function wppepvn_current_uri()
{
	static $uri = false;
	
	if(false === $uri) {
		$uri = wppepvn_request_scheme() . '://';
		
		if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
			$uri .= $_SERVER['HTTP_HOST'];
		} else if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
			$uri .= $_SERVER['SERVER_NAME'];
		} else {
			$uri = '';
		}
		if($uri) {
			if(isset($_SERVER['REQUEST_URI'])) {
				$uri .= $_SERVER['REQUEST_URI'];
			}
		}
	}
	
	return $uri;
}

function wppepvn_get_current_permalink()
{
	global $wpOptimizeByxTraffic;
	
	$permalink = false;
	
	if(isset($wpOptimizeByxTraffic) && $wpOptimizeByxTraffic) {
		if(isset($wpOptimizeByxTraffic->di) && $wpOptimizeByxTraffic->di) {
			if(isset($wpOptimizeByxTraffic->initialized['wp'])) {
				$wpExtend = $wpOptimizeByxTraffic->di->getShared('wpExtend');
				$permalink = $wpExtend->getCurrentPermalink();
			}
		}
	}
	
	if(!$permalink) {
		$permalink = wppepvn_current_uri();
	}
	
	return $permalink;
}

function wppepvn_unlink($filename)
{
	$status = false;
	
	if(is_file($filename)) {
		if(is_writable($filename)) {
			$status = unlink($filename);
			clearstatcache(true, $filename);
		}
	}
	
	return $status;
}

function wppepvn_mkdir($dir) 
{
	$status = true;
	
	if(!is_dir($dir)) {
		$status = @mkdir($dir,0755,true);
	}
	
	return $status;
}

function wppepvn_is_has_apc() 
{
	$status = false;
	
	if(function_exists('apc_exists')) {
		$status = true;
	}
	
	return $status;
}

function wppepvn_is_has_memcached() 
{
	$status = false;
	
	if(class_exists('\Memcached')) {
		$status = true;
	}
	
	return $status;
}

function wppepvn_is_has_memcache() 
{
	$status = false;
	
	if(class_exists('\Memcache')) {
		$status = true;
	}
	
	return $status;
}

function wppepvn_trailingslashdir($dir)
{
	$dir = wppepvn_untrailingslashdir($dir);
	
	$dir .= DIRECTORY_SEPARATOR;
	
	return $dir;
}

function wppepvn_untrailingslashdir($dir)
{
	$dir = preg_replace('#[\\\/]+$#','',$dir);
	
	return $dir;
}

function wppepvn_trailingslashurl($url)
{
	$url = wppepvn_untrailingslashurl($url);
	
	$url .= '/';
	
	return $url;
}

function wppepvn_untrailingslashurl($url)
{
	$url = preg_replace('#[/]+$#','',$url);
	
	return $url;
}

function wppepvn_parse_url($url)
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
	}
			
	/*** return the host domain ***/
	//return $parts['scheme'].'://'.$parts['host'];
	return $parts;
	
}

function wppepvn_get_site_salt()
{
	static $site_salt = false;
	
	if(false === $site_salt) {
		
		$site_salt = array();
		
		$site_salt[] = defined('AUTH_KEY') ? AUTH_KEY : 0;
		$site_salt[] = defined('SECURE_AUTH_KEY') ? SECURE_AUTH_KEY : 0;
		$site_salt[] = defined('LOGGED_IN_KEY') ? LOGGED_IN_KEY : 0;
		$site_salt[] = defined('NONCE_KEY') ? NONCE_KEY : 0;
		$site_salt[] = defined('AUTH_SALT') ? AUTH_SALT : 0;
		$site_salt[] = defined('SECURE_AUTH_SALT') ? SECURE_AUTH_SALT : 0;
		$site_salt[] = defined('LOGGED_IN_SALT') ? LOGGED_IN_SALT : 0;
		$site_salt[] = defined('NONCE_SALT') ? NONCE_SALT : 0;
		
		$tmp = wppepvn_parse_url(wppepvn_current_uri());
		if(isset($tmp['host'])) {
			$site_salt[] = $tmp['host'];
		}
		
		$site_salt = md5(serialize($site_salt));
		
	}
	
	return $site_salt;
}

function wppepvn_crc32b($data)
{
	return hash('crc32b', (string)$data, false);
}

function wppepvn_get_cookies_not_cache($data = array())
{
	
	$data = (array)$data;
	
	$data = array_merge(array(
		'comment_author'
		,'wp-postpass'
		,'wptouch_switch_toggle'
		,'wordpress_logged_in'
		,'woocommerce_cart_'
	),$data);
	
	$data = array_unique($data);
	
	return $data;
}

function wppepvn_get_uri_not_cache($data = array())
{
	
	$data = (array)$data;
	
	$data = array_merge(array(
		's='
		,'submit='
		,'wp-admin'
		,'wp-content'
		,'wp-includes'
		,'.php'

		//woocommerce : http://docs.woothemes.com/document/configuring-caching-plugins/
		,'/cart/'
		,'/my-account/'
		,'/checkout/'
		,'/addons/'
		,'add-to-cart='
	),$data);
	
	$data = array_unique($data);
	
	return $data;
}

function wppepvn_clean_array($input_data) 
{
	$resultData = array();
	
	$input_data = (array)$input_data;
	foreach($input_data as $value1) {
		$value1 = trim($value1);
		if(isset($value1[0])) {
			$resultData[] = $value1;
		}
	}
	
	$resultData = array_unique($resultData);
	
	return $resultData;
}

function wppepvn_clean_preg_patterns_array($input_data) 
{
	
	$input_data = wppepvn_clean_list_array($input_data);
	
	foreach($input_data as $keyOne => $valueOne) {
		$input_data[$keyOne] = preg_quote($valueOne, '#');
	}
	
	return $input_data;
	
}

function wppepvn_clean_list_array($input_data) 
{
	$input_data = (array)$input_data;
	$input_data = implode(';',$input_data);
	$input_data = preg_replace('#[\,\;]+#',';',$input_data);
	$input_data = explode(';',$input_data);
	$input_data = wppepvn_clean_array($input_data);
	
	return $input_data;
}

function wppepvn_is_has_igbinary() 
{
	static $status = null;
	
	if(null === $status) {
		if(function_exists('igbinary_serialize')) {
			$status = true;
		} else {
			$status = false;
		}
	}
	
	return $status;
}

function wppepvn_serialize($data) 
{
	if(true === wppepvn_is_has_igbinary()) {
		return igbinary_serialize($data);
	} else {
		return serialize($data);
	}
}

function wppepvn_unserialize($data) 
{
	if(true === wppepvn_is_has_igbinary()) {
		return igbinary_unserialize($data);
	} else {
		return unserialize($data);
	}
}

function wppepvn_hash_key($input_data)
{
	return hash('crc32b', md5(wppepvn_serialize($input_data)), false);
}

function wppepvn_http_headers($headers, $action = 'add')
{
	if (headers_sent()) {
		return false;
	}
	
	static $arrHeaders = array();
	
	if(!empty($headers)) {
		$arrHeaders = array_merge($arrHeaders, $headers);
	}
	
	if('flush' === $action) {
		
		if(!empty($arrHeaders)) {
			$arrHeaders = array_values($arrHeaders);
			$arrHeaders = array_unique($arrHeaders);
			
			foreach($arrHeaders as $key => $value) {
				header($value,true);
			}
		}
		
		$arrHeaders = array();
	}
}

function wppepvn_get_server_protocol()
{
	static $server_protocol = false;
	
	if(false === $server_protocol) {
		$server_protocol = 'HTTP/1.0';
		if(isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL']) {
			$server_protocol = $_SERVER['SERVER_PROTOCOL'];
		} else if(isset($_ENV['SERVER_PROTOCOL']) && $_ENV['SERVER_PROTOCOL']) {
			$server_protocol = $_ENV['SERVER_PROTOCOL'];
		}
	}
	
	return $server_protocol;
}

function wppepvn_http_header_template($template, $data = false)
{
	$headers = array();
	
	if('no-cache' === $template) {
		$headers[] = 'Expires: 0';
		$headers[] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT';
		$headers[] = 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0, s-maxage=0';
		$headers[] = 'Expires: on, 01 Jan 1970 00:00:00 GMT';
		$headers[] = 'Cache-Control: post-check=0, pre-check=0';
		$headers[] = 'Pragma: no-cache';
	}
	
	if(!empty($headers)) {
		wppepvn_http_headers($headers);
	}
}

function wppepvn_gmdate_gmt($input_timestamp)
{
	$input_timestamp = (int)$input_timestamp;
	$formatStringGMDate = 'D, d M Y H:i:s';
	$resultData = gmdate($formatStringGMDate, $input_timestamp).' GMT';
	return $resultData;
}

function wppepvn_get_device_screen_width()
{
	static $mobileDetectObject = false;
	
	static $device_screen_width = false;
	
	if(false === $mobileDetectObject) {
		$mobileDetectObject = new \WPOptimizeByxTraffic\Application\Service\Mobile_Detect();
	}
	
	if(false === $device_screen_width) {
		
		$device_screen_width = 0;	//pixel
		
		$cookieKey = 'xtrdvscwd';
		$screenWidthCookie = false;
		if(isset($_COOKIE[$cookieKey]) && $_COOKIE[$cookieKey]) {
			$screenWidthCookie = $_COOKIE[$cookieKey];
			$screenWidthCookie = (int)$screenWidthCookie;
		}
		
		if(
			$screenWidthCookie
			&& ($screenWidthCookie>0)
		) {
			$device_screen_width = $screenWidthCookie;
		} else {
			
			$httpUserAgent = '';
			
			if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']) {
				$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
			}
			
			$deviceVersion = false;
			
			$isGooglePageSpeedStatus = false;
			if(false !== stripos($httpUserAgent, 'Google Page Speed')) {
				$isGooglePageSpeedStatus = true;
			}
			
			if ( $mobileDetectObject->isMobile() ) { //mobile or tablet
				if($mobileDetectObject->isTablet()) {
					
					$device_screen_width = 960;
					
					if($mobileDetectObject->is('Kindle')) {	//Kindle
						$device_screen_width = 1024;
					} else if($mobileDetectObject->is('iPad')) {	//iPad
						$device_screen_width = 1024;
						
						$deviceVersion = $mobileDetectObject->version('iPad');
						
						if($deviceVersion) {
							if(0 === stripos($deviceVersion,'4_')) {	//iPad 1/2/mini
								$device_screen_width = 2048;
							}
						}
					} else if(false !== stripos($httpUserAgent,'Nexus 10')) { //Nexus 10
						$device_screen_width = 2560;
					} else if(false !== stripos($httpUserAgent,'Nexus 7')) { //Nexus 7
						$device_screen_width = 1280;
					}
					
				} else {	//is mobile phone
					
					$device_screen_width = 320;
					
					if($isGooglePageSpeedStatus) {
						$device_screen_width = 320;
					} else if($mobileDetectObject->is('iPhone')) {
						
						$device_screen_width = 320;
						
						$deviceVersion = $mobileDetectObject->version('iPhone');
						if($deviceVersion) {
							if(0 === stripos($deviceVersion,'8_')) { //iPhone 6
								$device_screen_width = 750;
							} else if(0 === stripos($deviceVersion,'4_')) { //iPhone 4
								$device_screen_width = 640;
							}
						}
					} else if(false !== stripos($httpUserAgent,'BB10; Touch')) { //BlackBerry Z10
						$device_screen_width = 768; 
					} else if(false !== stripos($httpUserAgent,'Nexus 4')) { //Nexus 4
						$device_screen_width = 768;
					} else if(false !== stripos($httpUserAgent,'Nexus 5')) { //Nexus 5
						$device_screen_width = 1080;
					} else if(false !== stripos($httpUserAgent,'Nexus S')) { //Nexus S
						$device_screen_width = 480;
					} else if(false !== stripos($httpUserAgent,'Nokia')) { //Nokia
						$device_screen_width = 360;
						if(false !== stripos($httpUserAgent,'Lumia')) { //Nokia Lumia
							$device_screen_width = 480;
						}
					}
				}
				
			} else {	//desktop
				if($isGooglePageSpeedStatus) {
					$device_screen_width = 1024;
				}
			}
			
		}
		
		$device_screen_width = (int)$device_screen_width;
	}
	
	return $device_screen_width;
}

function wppepvn_get_cachetags_current_request($strict_status = false)
{
	global $wpOptimizeByxTraffic;
	
	static $_store = array();
	
	$result = array();
	
	if(isset($wpOptimizeByxTraffic) && $wpOptimizeByxTraffic) {
		if(isset($wpOptimizeByxTraffic->di) && $wpOptimizeByxTraffic->di) {
			if(isset($wpOptimizeByxTraffic->initialized['wp'])) {
				$wpExtend = $wpOptimizeByxTraffic->di->getShared('wpExtend');
				$result = $wpExtend->getCacheTagsForCurrentRequest();
			}
		}
	}
	
	if(!empty($result)) {
		$_store = array_merge($_store,$result);
		$_store = array_unique($_store);
		$result = $_store;
	}
	
	if(!empty($result)) {
		if($strict_status) {
			foreach($result as $key1 => $value1) {
				if(!preg_match('#^(psid|tmid|tp|psautid|autid|pmlh)\-#', $value1)) {
					unset($result[$key1]);
				}
			}
		}
	} else {
		$result = array('tp-others');
	}
	
	return $result;
}

function wppepvn_is_preview()
{
	static $result = false;
	
	if(false === $result) {
		
		global $wp_query;
		
		if(function_exists('is_preview') && isset($wp_query)) {
			$result = is_preview();
		} else {
			if(isset($_GET['preview'])) {
				$result = true;
			}
		}
		
	}
	
	return $result;
}

function wppepvn_debug_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
	
    $errno = $errno & error_reporting();
    
    if(!defined('E_STRICT')) {define('E_STRICT', 2048);}
    if(!defined('E_RECOVERABLE_ERROR')) {define('E_RECOVERABLE_ERROR', 4096);}
	
	$message = '';
    $message .= '<pre>'. PHP_EOL .'<b>';
    switch($errno) {
        case E_ERROR:               print "Error";                  break;
        case E_WARNING:             print "Warning";                break;
        case E_PARSE:               print "Parse Error";            break;
        case E_NOTICE:              print "Notice";                 break;
        case E_CORE_ERROR:          print "Core Error";             break;
        case E_CORE_WARNING:        print "Core Warning";           break;
        case E_COMPILE_ERROR:       print "Compile Error";          break;
        case E_COMPILE_WARNING:     print "Compile Warning";        break;
        case E_USER_ERROR:          print "User Error";             break;
        case E_USER_WARNING:        print "User Warning";           break;
        case E_USER_NOTICE:         print "User Notice";            break;
        case E_STRICT:              print "Strict Notice";          break;
        case E_RECOVERABLE_ERROR:   print "Recoverable Error";      break;
        default:                    print "Unknown error ($errno)"; break;
    }
    
	$message .= ':</b> <i>'.$errstr.'</i> in <b>'.$errfile.'</b> on line <b>'.$errline.'</b>';
	
	$message .= ' - errcontext : ' . var_export($errcontext, true);
	
    $message .= PHP_EOL . '</pre>';
    
	wppepvn_debug_log($message);
	
	echo $message;
	
}

function wppepvn_debug_log($message)
{
	static $loggerObj = false;
	
	if(false === $loggerObj) {
		if(function_exists('site_url') && class_exists('\\WpPepVN\\Logger')) {
			$domain = strtolower(parse_url( site_url(), PHP_URL_HOST ));
			if($domain) {
				$loggerObj = new \WpPepVN\Logger(array(
					'log_dir' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_DIR . 'logs' . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR
					,'log_filename' => $domain . '.log'
				));
			}
		}
	}
	
	if(!$loggerObj) {
		return false;
	}
	
	if(is_object($message) || is_array($message)) {
		$message = var_export($message,true);
	}
	
    if(function_exists('debug_backtrace')){
        
        $backtrace = debug_backtrace();
        array_shift($backtrace);
		
        foreach($backtrace as $i => $l) {
			
            $message .= PHP_EOL . '['.$i.'] in function <b>';
			
			if(isset($l['class'])) {
				$message .= $l['class'];
			}
			if(isset($l['type'])) {
				$message .= $l['type'];
			}
			if(isset($l['function'])) {
				$message .= $l['function'];
			}
			$message .= '</b>';
			
            if(isset($l['file']) && $l['file']) {$message .= ' in <b>'.$l['file'].'</b>';}
            if(isset($l['line']) && $l['line']) {$message .= ' on line <b>'.$l['line'].'</b>';}
			//if(isset($l['args']) && $l['args']) {$message .= ' with args <b>'.var_export($l['args'],true).'</b>';}
			
        }
    }
	
	$message .= '<hr/>';
	
	$loggerObj->debug(strip_tags($message));
	
	if(WP_PEPVN_DEBUG) {
		//echo str_replace(PHP_EOL,'<br/>',$message);
	}
}