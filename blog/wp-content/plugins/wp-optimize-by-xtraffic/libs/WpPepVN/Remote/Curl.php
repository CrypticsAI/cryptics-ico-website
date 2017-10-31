<?php 
namespace WpPepVN\Remote;

/**
 * WpPepVN\Remote\Curl
 */

class Curl
{
	
	private $_cacheFile = false;
	
	private $_http_UserAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36';
	
	private $_http_Headers = array();
	
	public function __construct() 
	{
		
	}
	
	public function setUserAgent($userAgent) 
	{
		$this->_http_UserAgent = $userAgent;
	}
	
	public function setHeaders($headers) 
	{
		$this->_http_Headers = $headers;
	}
	
	public function get($input_url, $input_args = false) 
	{
		
		$resultData = false;
		
		if(function_exists('curl_init')) {
			$connect_timeout = 6;
			if(isset($input_args['request_timeout']) && $input_args['request_timeout']) {
				$connect_timeout = $input_args['request_timeout'];
			}
			
			if(isset($input_args['referer_url'])) {
				$opts_referer_url = $input_args['referer_url'];
			} else {
				$opts_referer_url = $input_url;
			}
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $input_url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $this->_http_UserAgent);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array_values($this->_http_Headers));
			curl_setopt($ch, CURLOPT_TIMEOUT, $connect_timeout);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			//curl_setopt($ch, CURLOPT_MAXREDIRS, 9); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
			curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			
			if(isset($input_args['cookies']) && ($input_args['cookies'])) {
				$cookiesTemp = array();
				foreach($input_args['cookies'] as $key1 => $val1) {
					$cookiesTemp[] = $key1.'='.$val1;
				}
				if(!empty($cookiesTemp)) {
					curl_setopt($ch, CURLOPT_COOKIE, implode(';',$cookiesTemp));
				}
				unset($cookiesTemp);
			}
			
			if($opts_referer_url) {
				curl_setopt($ch, CURLOPT_REFERER, $opts_referer_url);
			}
			
			$resultData = curl_exec($ch);
			
			if(curl_errno($ch) || !$resultData) {
				$resultData = false;
			}
			curl_close($ch);
			unset($ch);
			
		}
		
		return $resultData;
		
	}

}