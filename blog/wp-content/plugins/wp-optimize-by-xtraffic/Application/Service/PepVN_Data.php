<?php
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\Utils
	,WpPepVN\System
	,WpPepVN\Hash
	,WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
;

class PepVN_Data
{
	public static $defaultParams = false;
	
	public static $params = array();
	
	public static $cacheData = array();	//store data cache for each request only
	
	public static $cacheObject = false;
    
	public static $cachePermanentObject = false;
    
	public static $cacheByTagObject = false;
	
	public static $cacheFileByTagObject = false;
	
	public static $cacheMultiObject = false;	//use multi methods : apc, memcached, files,.... Only used to store cache with query frequently and less data.
	
	public static $staticVarDataFileObject = false;
    
	public function __construct()
	{
		self::setDefaultParams(); 
	}
	
	public static function setDefaultParams()
	{
		if(false === self::$defaultParams) {
			
			self::$defaultParams['status'] = 1;
			
			self::$params['optimize_images']['number_images_processed_request'] = 0;
			
			unset($arrayVietnameseChar);
			$arrayVietnameseChar = array(
				'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'đ' => 'd', 'đ' => 'd', 'Ð' => 'D'
			);
			
			$arrayVietnameseHasSign = explode(',',(trim(self::strtoupper(trim(implode(',',(array_keys($arrayVietnameseChar))))))));
			$arrayVietnameseNoSign = explode(',',(trim(strtoupper(trim(implode(',',(array_values($arrayVietnameseChar))))))));
			
			self::$defaultParams['char']['vietnamese'] = array_merge($arrayVietnameseChar, array_combine($arrayVietnameseHasSign, $arrayVietnameseNoSign));
			unset($arrayVietnameseHasSign, $arrayVietnameseNoSign, $arrayVietnameseChar);
			
			
			self::$defaultParams['string']['alphabet'] = 'abcdefghijklmnopqrstuvwxyz';
			self::$defaultParams['string']['number'] = '0123456789';
			
			/*
			self::$defaultParams['http']['user-agent'] = 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36';
			$valueTemp1['user-agent'] = 'User-Agent: '.self::$defaultParams['http']['user-agent'];
			$valueTemp1['accept'] = 'Accept: * /*;';
			//$valueTemp1['accept-language'] = 'Accept-Language: en-us,en;q=0.9,de;q=0.8,ja;q=0.8,zh;q=0.7,zh-cn;q=0.6,nl;q=0.5,fr;q=0.5,it;q=0.4,ko;q=0.3,es;q=0.2,ru;q=0.2,pt;q=0.1';
			$valueTemp1['accept-encoding'] = 'Accept-Encoding: gzip,deflate';
			$valueTemp1['accept-charset'] = 'Accept-Charset: UTF-8,*;';
			$valueTemp1['keep-alive'] = 'Keep-Alive: 300';
			$valueTemp1['connection'] = 'Connection: keep-alive';
			*/
			//$opts_Headers = array();
			
			//self::$defaultParams['http']['headers'] = $valueTemp1;
			
            self::$defaultParams['wp_cookies_not_cache'] = wppepvn_get_cookies_not_cache();
            
            self::$defaultParams['wp_request_uri_not_cache'] = wppepvn_get_uri_not_cache();
			
			self::$defaultParams['urlProtocol'] = 'http:';
            
			if(self::is_ssl()) {
				self::$defaultParams['urlProtocol'] = 'https:';
			}
			
			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
				self::$defaultParams['urlFullRequest'] = self::$defaultParams['urlProtocol'].'//'.$_SERVER['HTTP_HOST'];
			} else {
				self::$defaultParams['urlFullRequest'] = self::$defaultParams['urlProtocol'].'//'.$_SERVER['SERVER_NAME'];
			}
			
			if(isset($_SERVER['REQUEST_URI'])) {
				self::$defaultParams['urlFullRequest'] .= $_SERVER['REQUEST_URI'];
			}
			
			self::$defaultParams['parseedUrlFullRequest'] = Utils::parse_url(self::$defaultParams['urlFullRequest']);
			
			self::$defaultParams['fullDomainName'] = '';
			self::$defaultParams['domainName'] = '';
			//$parseUrl = parse_url(self::$defaultParams['urlFullRequest']);
			if(isset(self::$defaultParams['parseedUrlFullRequest']['host']) && self::$defaultParams['parseedUrlFullRequest']['host']) {
				self::$defaultParams['fullDomainName'] = self::$defaultParams['parseedUrlFullRequest']['host'];
			}
			if(isset(self::$defaultParams['parseedUrlFullRequest']['domain']) && self::$defaultParams['parseedUrlFullRequest']['domain']) {
				self::$defaultParams['domainName'] = self::$defaultParams['parseedUrlFullRequest']['domain'];
			}
			
			self::$defaultParams['serverSoftware'] = '';
			if(isset($_SERVER['SERVER_SOFTWARE']) && $_SERVER['SERVER_SOFTWARE']) {
				$valueTemp = $_SERVER['SERVER_SOFTWARE'];
				$valueTemp = trim($valueTemp);
				if(preg_match('#nginx#i',$valueTemp)) {
					self::$defaultParams['serverSoftware'] = 'nginx';
				} else if(preg_match('#apache#i',$valueTemp)) {
					self::$defaultParams['serverSoftware'] = 'apache';
				}
			}
			
			self::$defaultParams['_has_igbinary'] = false;
			if(function_exists('igbinary_serialize')) {
				self::$defaultParams['_has_igbinary'] = true;
			}
			
			self::$defaultParams['requestTime'] = 0;
			
			if(isset($_SERVER['REQUEST_TIME']) && $_SERVER['REQUEST_TIME']) {
				self::$defaultParams['requestTime'] = $_SERVER['REQUEST_TIME'];
			} else {
				self::$defaultParams['requestTime'] = time();
			}
			self::$defaultParams['requestTime'] = (int)self::$defaultParams['requestTime'];
			
			self::initCacheObject();
			
			self::$defaultParams['microtime_start'] = microtime(true);
			self::$defaultParams['microtime_start'] = (float)self::$defaultParams['microtime_start'];
			
			self::$defaultParams['allow_html_tags']['text'] = array(
				'p'
				,'span'
				,'strong'
				,'div'
				
				//table 
				,'table'
				,'caption'
				,'th'
				,'tr'
				,'td'
				,'thead'
				,'tbody'
				,'tfoot'
				,'col'
				,'colgroup'
				
				,'a'
				
				,'em'
				,'b'
				,'u'
				,'i'
				,'h1'
				,'h2'
				,'h3'
				,'h4'
				,'h5'
				,'h6'
				,'ul'
				,'ol'
				,'li'
				,'pre'
				
				,'article'
				,'aside'
				
				,'center'
				,'cite'
				,'code'
				,'dd'
				,'del'
				,'dfn'
				,'dl'
				,'dt'
				,'figcaption'
				,'figure'
				,'footer'
				,'header'
				,'s'
				,'small'
				,'sub'
				,'summary'
				,'time'
				,'section'
				,'details'
				,'summary'
				
			);
			
			self::$defaultParams['allow_html_tags']['non_text'] = array(
				'br'
				,'img'
				,'hr'
				
				,'video'
				,'source'
				,'audio'
			);
			
			self::$defaultParams['allow_html_attributes'] = array(
				'src'
				,'alt'
				,'title'
				,'href'
				
				,'align'
				,'border'
				,'cellpadding'
				,'cellspacing'
				
				,'height'
				,'width'
				,'border'
				,'type'
				,'controls'
				
				,'frameborder'
				,'allowfullscreen'
			);
			
		}
	}
	
	public static function initCacheObject() 
	{
		
		//cacheObject : store cache for short time (less than 1 day)
		$pepvnDirCachePathTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR.'s'.DIRECTORY_SEPARATOR;

		if(!is_dir($pepvnDirCachePathTemp)) {
			System::mkdir($pepvnDirCachePathTemp);
		}

		if(is_dir($pepvnDirCachePathTemp) && is_readable($pepvnDirCachePathTemp) && is_writable($pepvnDirCachePathTemp)) {
			
			$pepvnCacheHashKeySaltTemp = self::$defaultParams['fullDomainName'] . $pepvnDirCachePathTemp;
			
			if(defined('WP_PEPVN_SITE_SALT')) {
				$pepvnCacheHashKeySaltTemp .= '_'.WP_PEPVN_SITE_SALT;
			}
			
			self::$cacheObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile(array(
				'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL				//seconds
				,'hash_key_method' => 'crc32b'	//crc32b is best
				,'hash_key_salt' => hash('crc32b',md5($pepvnCacheHashKeySaltTemp))
				,'gzcompress_level' => 5 	//should be 0 to achieve the best performance (CPU speed)
				,'key_prefix' => 'dts_'
				,'cache_dir' => $pepvnDirCachePathTemp
			));
		} else {
			self::$cacheObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile(array()); 
		}
		
		//cachePermanentObject : store cache for long time (>6 days)
		$pepvnDirCachePathTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR.'pm'.DIRECTORY_SEPARATOR; 

		if(!is_dir($pepvnDirCachePathTemp)) {
			System::mkdir($pepvnDirCachePathTemp);
		}

		if(is_dir($pepvnDirCachePathTemp) && is_readable($pepvnDirCachePathTemp) && is_writable($pepvnDirCachePathTemp)) {
			
			$pepvnCacheHashKeySaltTemp = self::$defaultParams['fullDomainName'] . $pepvnDirCachePathTemp;
			
			if(defined('WP_PEPVN_SITE_SALT')) {
				$pepvnCacheHashKeySaltTemp .= '_'.WP_PEPVN_SITE_SALT;
			}
			
			$pepvnCacheTimeoutTemp = WP_PEPVN_CACHE_TIMEOUT_NORMAL * 6;
			
			PepVN_Data::$cachePermanentObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile(array(
				'cache_timeout' => $pepvnCacheTimeoutTemp				//seconds
				,'hash_key_method' => 'crc32b' //crc32b is best
				,'hash_key_salt' => hash('crc32b',md5($pepvnCacheHashKeySaltTemp))
				,'gzcompress_level' => 5	//should be greater than 0 (>0, 2 is best value) to save HDD for long time.
				,'key_prefix' => 'dtpm_'
				,'cache_dir' => $pepvnDirCachePathTemp
			));
		} else {
			PepVN_Data::$cachePermanentObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile(array()); 
		}
		
		//cacheByTagObject : store cache by tags
		$pepvnDirCachePathTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR.'ctags'.DIRECTORY_SEPARATOR;

		if(!is_dir($pepvnDirCachePathTemp)) {
			System::mkdir($pepvnDirCachePathTemp);
		}

		if(is_dir($pepvnDirCachePathTemp) && is_readable($pepvnDirCachePathTemp) && is_writable($pepvnDirCachePathTemp)) {
			
			$pepvnCacheHashKeySaltTemp = self::$defaultParams['fullDomainName'] . $pepvnDirCachePathTemp;
			
			if(defined('WP_PEPVN_SITE_SALT')) {
				$pepvnCacheHashKeySaltTemp .= '_'.WP_PEPVN_SITE_SALT;
			}
			
			$hash_key_salt = Hash::crc32b($pepvnCacheHashKeySaltTemp);
			self::$cacheByTagObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_Cache(array(
				'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL		//seconds
				,'hash_key_method' => 'crc32b'		//best is crc32b
				,'hash_key_salt' => crc32($hash_key_salt)
				,'gzcompress_level' => 5	// should be greater than 0 (>0, 2 is best) to save RAM in case of using Memcache, APC, ...
				,'key_prefix' => 'mtc_'
				,'cache_methods' => array(
					'file' => array(
						'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
						, 'cache_dir' => $pepvnDirCachePathTemp 
					)
				)
			));
			
		} else {
			self::$cacheByTagObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_Cache(array()); 
		}
		
		//cacheFileByTagObject : store cache in file & manager by tags
		$pepvnDirCachePathTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR.'cfltags'.DIRECTORY_SEPARATOR;

		if(!is_dir($pepvnDirCachePathTemp)) {
			System::mkdir($pepvnDirCachePathTemp);
		}
		
		if(is_dir($pepvnDirCachePathTemp) && is_readable($pepvnDirCachePathTemp) && is_writable($pepvnDirCachePathTemp)) {
			
			$pepvnCacheHashKeySaltTemp = self::$defaultParams['fullDomainName'] . $pepvnDirCachePathTemp;
			
			if(defined('WP_PEPVN_SITE_SALT')) {
				$pepvnCacheHashKeySaltTemp .= '_'.WP_PEPVN_SITE_SALT;
			}
			
			$hash_key_salt = Hash::crc32b($pepvnCacheHashKeySaltTemp);
			self::$cacheFileByTagObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_Cache(array(
				'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL		//seconds
				,'hash_key_method' => 'crc32b'		//best is crc32b
				,'hash_key_salt' => crc32($hash_key_salt)
				,'gzcompress_level' => 5	// should be greater than 0 (>0, 2 is best) to save RAM in case of using Memcache, APC, ...
				,'key_prefix' => 'mtc_'
				,'cache_methods' => array(
					'file' => array(
						'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
						, 'cache_dir' => $pepvnDirCachePathTemp 
					)
				)
			));
			
		} else {
			self::$cacheFileByTagObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_Cache(array()); 
		}
		
		
		//cacheMultiObject : store cache multi methods
		$pepvnDirCachePathTemp = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR.'chmlt'.DIRECTORY_SEPARATOR;

		if(!is_dir($pepvnDirCachePathTemp)) {
			System::mkdir($pepvnDirCachePathTemp);
		}

		if(is_dir($pepvnDirCachePathTemp) && is_readable($pepvnDirCachePathTemp) && is_writable($pepvnDirCachePathTemp)) {
			
			$pepvnCacheHashKeySaltTemp = self::$defaultParams['fullDomainName'] . $pepvnDirCachePathTemp;
			
			if(defined('WP_PEPVN_SITE_SALT')) {
				$pepvnCacheHashKeySaltTemp .= '_'.WP_PEPVN_SITE_SALT;
			}
			
			$hash_key_salt = Hash::crc32b($pepvnCacheHashKeySaltTemp);
			self::$cacheMultiObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_Cache(array(
				'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL		//seconds
				,'hash_key_method' => 'crc32b'		//best is crc32b
				,'hash_key_salt' => crc32($hash_key_salt)
				,'gzcompress_level' => 5	// should be greater than 0 (>0, 2 is best) to save RAM in case of using Memcache, APC, ...
				,'key_prefix' => 'mtc_'
				,'cache_methods' => array(
					'file' => array(
						'cache_timeout' => WP_PEPVN_CACHE_TIMEOUT_NORMAL
						, 'cache_dir' => $pepvnDirCachePathTemp 
					)
				)
			));
			
		} else {
			self::$cacheMultiObject = new \WPOptimizeByxTraffic\Application\Service\PepVN_Cache(array()); 
		}
		
	}
	
	public static function function_exists($name) 
	{
		$k = 'fnc_exs_'.$name;
		
		if(!isset(self::$cacheData[$k])) {
			if(function_exists($name)) {
				self::$cacheData[$k] = true;
			} else {
				self::$cacheData[$k] = false;
			}
		}
		
		return self::$cacheData[$k];
	}
	
	public static function serialize($data) 
	{
		if(true === self::$defaultParams['_has_igbinary']) {
			return igbinary_serialize($data);
		} else {
			return serialize($data);
		}
	}
	
	public static function unserialize($data) 
	{
		if(true === self::$defaultParams['_has_igbinary']) {
			return igbinary_unserialize($data);
		} else {
			return unserialize($data);
		}
	}
	
	public static function strtolower($input_text,$input_encoding = 'UTF-8') 
	{
		if(System::function_exists('mb_convert_case')) {
			return mb_convert_case($input_text, MB_CASE_LOWER, $input_encoding);
		} else {
			return strtolower($input_text);
		}
	}
	
	public static function strtoupper($input_text,$input_encoding = 'UTF-8') 
	{
		if(System::function_exists('mb_convert_case')) {
			return mb_convert_case($input_text, MB_CASE_UPPER, $input_encoding);
		} else {
			return strtolower($input_text);
		}
	}
	
	public static function gmdate_gmt($input_timestamp)
	{
		$input_timestamp = (int)$input_timestamp;
		$formatStringGMDate = 'D, d M Y H:i:s';
		$resultData = gmdate($formatStringGMDate, $input_timestamp).' GMT';
		return $resultData;
	}
	
	public static function session_start()
	{
		if(
			isset(self::$defaultParams['session_started_status'])
			&& self::$defaultParams['session_started_status']
		) {
		} else {
			
			self::$defaultParams['session_started_status'] = true;
			
			if(session_id() == '') {
				if(!headers_sent()) {
					@session_start();
				}
			}
		}
	}
	
	public static function randomHash() 
	{

		$rsData = self::$defaultParams['requestTime'].mt_rand();
		
		$rsData = strtolower(md5($rsData));

		return $rsData;

	}
	
	public static function rgb2hex($rgb) 
	{
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

		return $hex; // returns the hex value including the number sign (#)
		
	}

	public static function hex2rgb($hex) 
	{
		$hex = str_replace("#", "", $hex); 

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		
		$rgb = array($r, $g, $b);
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}
	
	public static function mb_substr($input_str, $input_start, $input_length = 0, $input_encoding = 'UTF-8')
	{
		return mb_substr($input_str, $input_start, $input_length, $input_encoding);
	}
	
	public static function mb_strlen($input_str, $input_encoding = 'UTF-8')
	{
		return mb_strlen($input_str, $input_encoding);
	}
	
	public static function countWords($input_str) 
	{
		$input_str = trim($input_str);
		$input_str = explode(' ',$input_str);
		return count($input_str);
	}
	
	public static function randomString($input_parameters) 
	{
		$resultData = '';
		
		if(isset($input_parameters['string_set'])) {
			$stringSet = $input_parameters['string_set'];
		} else {
			$stringSet = self::$defaultParams['string']['alphabet'].self::$defaultParams['string']['number'].strtoupper(self::$defaultParams['string']['alphabet']);
		}
		
		$lengthStringSet = strlen($stringSet);
		
		$minLength = 1;
		$maxLength = 9;
		
		if(isset($input_parameters['length'])) {
			$minLength = $input_parameters['length'];
			$maxLength = $input_parameters['length'];
		} else {
			if(isset($input_parameters['min_length'])) {
				$minLength = $input_parameters['min_length'];
			}
			
			if(isset($input_parameters['max_length'])) {
				$maxLength = $input_parameters['max_length'];
			}
		}
		
		$minLength = abs((int)$minLength);
		$maxLength = abs((int)$maxLength);
		
		if($minLength > $maxLength) {
			$minLength = $maxLength;
		}
		
		$randomLength = mt_rand($minLength, $maxLength);
		
		$lengthStringSetSubOne = $lengthStringSet - 1;
		
		if($lengthStringSetSubOne>0) {
			for($iOne=0;$iOne<$randomLength;++$iOne) {
				$resultData .= substr($stringSet, mt_rand(0, $lengthStringSetSubOne), 1);
			}
		}
		
		return $resultData;
	}
	
	public static function removeCommentInCss($input_data)
	{
		$patterns = array(
			'#(\/\*)(.*?)(\*\/)#is' => ' '// Remove all comments
		);
		
		$input_data = preg_replace(array_keys($patterns), array_values($patterns), $input_data);
		
		return $input_data;
		
	}
	
	public static function hashMd5($input_data)
	{
		$input_data = self::serialize($input_data);
		$input_data = (string)$input_data;
		$input_data = preg_replace('#\s+#is','',$input_data);
		$input_data = md5($input_data);
		return $input_data;
	}
	
	public static function mcrc32($str)
	{
        $str = dechex(crc32($str));

        $str = preg_replace('/[^a-z0-9]+/i','r',$str); 

        $str = (string)$str;
        $str = trim($str);
        $str = 'm'.$str;

		return $str;
	}
	
	public static function mhash($str,$length = 8)
	{
		$length1 = $length * 2;
		
		$resultData = md5($str);
		
		while(strlen($resultData) < $length1) {
			
			$resultData .= md5($resultData);
			
		}
		
		$resultData .= md5($resultData);
		$resultData .= md5($resultData);
		
		$totalChars = strlen($resultData);
		$totalChars = (int)$totalChars;
		$stepNum = ceil($totalChars / $length);
		
		$valueTemp = str_split($resultData,1);
		$valueTemp_Count = count($valueTemp);
		$valueTemp_Count = (int)$valueTemp_Count;
		
		$resultData = '';
		
		for($i=0;$i<$valueTemp_Count;$i++) {
			if(0 === ($i % $stepNum)) {
				$resultData .= $valueTemp[$i];
				if(strlen($resultData) >= $length) {
					break;
				}
			}
		}
		
		return $resultData; 
		
	}
	
	public static function fHash($str)
	{
		$str = (string)$str;
		$n = 0;
		
		for ($c=0; $c < strlen($str); $c++) {
			$n += ord($str[$c]);
		}
		
		return $n;
	}
	
	public static function hashHashids($input_data, $input_options = false) 
	{
		if(!$input_options) {
			$input_options = array();
		}
		$input_options = (array)$input_options;
		
		if(!isset($input_options['level'])) {
			$input_options['level'] = 3;
		}
		$input_options['level'] = abs((int)$input_options['level']);
		if($input_options['level'] < 2) {
			$input_options['level'] = 2;
		}
		
		if(!isset($input_options['hashids_salt'])) {
			$input_options['hashids_salt'] = '';
		}
		if(!isset($input_options['hashids_min_hash_length'])) {
			$input_options['hashids_min_hash_length'] = 0;
		}
		
		$input_options['hashids_min_hash_length'] = abs((int)$input_options['hashids_min_hash_length']);
		
		if(!isset($input_options['hashids_alphabet'])) {
			$input_options['hashids_alphabet'] = '';
		}
		
		$pepVN_Hashids = new PepVN_Hashids($input_options['hashids_salt'],$input_options['hashids_min_hash_length'],$input_options['hashids_alphabet']);
		
		$inputDataToInterger = array();
		
		$input_data = md5($input_data);
		
		for($i = 0; $i < $input_options['level']; ++$i) {
			$input_data = md5($input_data);
			$valueTemp = crc32($input_data);
			$valueTemp = abs((int)$valueTemp);
			if($valueTemp > 999999999) {
				$valueTemp = 999999999;
			}
			$inputDataToInterger[] = $valueTemp;
		}
		
		$input_data = $pepVN_Hashids->encode($inputDataToInterger);
		$input_data = (string)$input_data;
		$input_data = trim($input_data);
		
		return $input_data;
		
	}
	
	public static function encodePasswordForEncryptData($input_password, $input_options = false)
	{
		if(!$input_options) {
			$input_options = array();
		}
		$input_options = (array)$input_options;
		
		if(!isset($input_options['length'])) {
			$input_options['length'] = 9;
		}
		$input_options['length'] = abs((int)$input_options['length']);
		if($input_options['length'] < 8) {
			$input_options['length'] = 8;
		}
		
		$input_password = (string)$input_password;
		
		$lengthTemp = $input_options['length'] + 1;
		
		$input_password = self::hashHashids($input_password, array('hashids_min_hash_length' => $lengthTemp));
		$input_password = substr($input_password,0,$input_options['length']);
		
		return $input_password;
	}
	
    public static function encryptData_Rijndael256($input_data, $input_pass)
	{
		if(!$input_data || !$input_pass) {
			return false;
		}
		
		$input_pass = self::encodePasswordForEncryptData($input_pass, array('length' => 24));
		
		$input_data = base64_encode($input_data);
		
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $input_data = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $input_pass, $input_data, MCRYPT_MODE_ECB, $iv);
		
		$input_data = base64_encode($input_data);
		
        return $input_data;
    }

    public static function decryptData_Rijndael256($input_data, $input_pass)
	{
        if(!$input_data || !$input_pass) {
			return false;
		}
		
		$input_pass = self::encodePasswordForEncryptData($input_pass, array('length' => 24));
		
		$input_data = base64_decode($input_data);
		
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $input_data = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $input_pass, $input_data, MCRYPT_MODE_ECB, $iv);
		
		$input_data = base64_decode($input_data);
		
        return $input_data;
    }
    
    public static  function encryptData_AES($input_data, $input_pass, $input_bit_key = 256)
    {
		if(!$input_data || !$input_pass) {
			return false;
		}
		
		$input_pass = self::encodePasswordForEncryptData($input_pass, array('length' => 18));
		
		$input_data = base64_encode($input_data);
		
        $input_data = PepVN_AESCtr::encrypt($input_data, $input_pass, $input_bit_key);
        
		$input_data = base64_encode($input_data);
		
        return $input_data;
    }

    public static  function decryptData_AES($input_data, $input_pass, $input_bit_key = 256)
    {
		if(!$input_data || !$input_pass) {
			return false;
		}
		
		$input_pass = self::encodePasswordForEncryptData($input_pass, array('length' => 18));
		
        $input_data = base64_decode($input_data);
		
        $input_data = PepVN_AESCtr::decrypt($input_data, $input_pass, $input_bit_key);
		
		$input_data = base64_decode($input_data);
		
		return $input_data;
    }
	
	public static function benchmarkStart()
	{
		self::$defaultParams['benchmark']['start_time'] = microtime(true);
		self::$defaultParams['benchmark']['start_time'] = (float)self::$defaultParams['benchmark']['start_time'];
	}
	
	public static function benchmarkEnd()
	{
		$endTime = microtime(true);
		$endTime = (float)$endTime;
		$periodTime = $endTime - self::$defaultParams['benchmark']['start_time'];
		$periodTime = (float)$periodTime;
		
		return $periodTime;
		
	}
	
	public static function createKey($input_data)
	{
		//important : don't change here, it make id for data (images url,...)
		return md5(preg_replace('#[\s \t]+#is','',serialize($input_data)));
	}
	
	public static function toTitleUrl($input_string)
	{
		$input_string = self::removeVietnameseSign($input_string);
		$input_string = self::strtolower($input_string);
		$input_string = preg_replace('#[^a-z0-9]+#is',' ',$input_string);
		$input_string = self::reduceSpace($input_string);
		$input_string = preg_replace('#\s#is','-',$input_string);
		return $input_string;
	}
	
		
	/**
	 * Determine if SSL is used.
	 *
	 * @return bool True if SSL, false if not used.
	 */
	public static function is_ssl() 
	{
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS']) )
				return true;
			if ( '1' == $_SERVER['HTTPS'] )
				return true;
		} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			return true;
		}
		return false;
	}
	
	public static function removeVietnameseSign($input_text)
	{
		$self_Char_Vietnamese = self::$defaultParams['char']['vietnamese'];
		return str_replace(array_keys($self_Char_Vietnamese), array_values($self_Char_Vietnamese), $input_text);
		
	}//End Function
	
	public static function explode($input_delimiter, $input_data) 
	{
		$input_delimiter = (string)$input_delimiter;
		
		if(preg_match('#[\,\;]+#is',$input_delimiter)) {
			
			$input_delimiter = ';';
			
			$input_data = (array)$input_data;
			$input_data = implode(';',$input_data);
			
			$input_data = preg_replace('#[\;\,]+#is',';',$input_data);
		}
		
		$input_data = (string)$input_data;
		
		$input_data = explode($input_delimiter,$input_data);
		
		return $input_data;
		
	}
	
	public static function splitAndCleanKeywords($input_data) 
	{
		$resultData = array();
		$input_data = (array)$input_data;
		$input_data = implode(';',$input_data);
		$input_data = preg_replace('#[;,]+#i',';',$input_data);
		$input_data = preg_replace('#\s+#i',' ',$input_data);
		$input_data = explode(';',$input_data);
		foreach($input_data as $valueOne) {
			$valueOne = trim($valueOne);
			if($valueOne) {
				$resultData[] = $valueOne;
			}
		}
		
		return $resultData;
		
	}
	
	public static function appendTextToTagHeadOfHtml($input_text,$input_html) 
	{
		return preg_replace('#([\s \t]*</head>[\s \t]*)#is', $input_text.'</head>',$input_html,1); 
	}
	
	public static function appendTextToTagBodyOfHtml($input_text,$input_html) 
	{
		return preg_replace('#([\s \t]*</body>[\s \t]*</html>[\s \t]*)#is', $input_text.' \1',$input_html,1);
	}
	
	public static function reduceSpace($input_data) 
	{
		$input_data = preg_replace('#[ \t]+#i',' ',$input_data);
		$input_data = trim($input_data);
		return $input_data;
	}
	
	public static function cleanKeyword($input_data) 
	{
		$input_data = preg_replace('#[\'\"]+#i',' ',$input_data);
		$input_data = self::reduceSpace($input_data);
		return $input_data;
	}
	
	public static function cleanPregPatternsArray($input_data) 
	{
		return wppepvn_clean_preg_patterns_array($input_data);
	}
	
	public static function cleanListArray($input_data) 
	{
		return wppepvn_clean_list_array($input_data);
	}
	
	public static function removeProtocolUrl($input_url) 
	{
		$input_url = trim($input_url);
		
		$input_url = preg_replace('#^https?://#i','',$input_url);
		$input_url = preg_replace('#^:?//#i','',$input_url);
		
		return $input_url;
		
	}
	
	public static function is_writable($input_path) 
	{
		$resultData = false;
		
		if($input_path) {
			if(file_exists($input_path)) {
				if(is_writable($input_path)) {
					$resultData = true;
				}
			}
		}
		
		return $resultData;
	}
	
	public static function is_readable($input_path) 
	{
		$resultData = false;
		
		if($input_path) {
			if(file_exists($input_path)) {
				if(is_readable($input_path)) {
					$resultData = true;
				}
			}
		}
		
		return $resultData;
	}
	
	//http://detectmobilebrowsers.com
	public static function isMobileDevice($useragent='') 
	{
		$resultData = false;
		
		if(!$useragent) {
			if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']) {
				$useragent = $_SERVER['HTTP_USER_AGENT'];
			}
		}
		
		$useragent = trim($useragent);
		
		if($useragent) {
			if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
				$resultData = true;
			}
		}
		
		return $resultData;
	}
	
	public static function asyncTouchUrl($url, $input_options = false)
	{
		
		$rsParseUrl = Utils::parse_url($url);
		
		if(isset($rsParseUrl['host']) && $rsParseUrl['host']) {
			
			if(isset($rsParseUrl['port']) && $rsParseUrl['port']) {
				
			} else {
				$rsParseUrl['port'] = 0;
			}
			$rsParseUrl['port'] = (int)$rsParseUrl['port'];
			if(!$rsParseUrl['port']) {
				$rsParseUrl['port'] = 80;
			}
			
			if(!isset($input_options['timeout'])) {
				$input_options['timeout'] = 0;
			}
			
			$input_options['timeout'] = abs((int)$input_options['timeout']);
			if($input_options['timeout']<1) {
				$input_options['timeout'] = 1;
			}
			
			$fp = @fsockopen($rsParseUrl['host'], $rsParseUrl['port'], $errno, $errstr, $input_options['timeout']);
			
			if($fp) {
				
				$crlf = "\r\n";
				
				$out = '';
				
				$uri = '/';
				
				$post_string = '';
				$cookie_string = '';
				
				if(isset($rsParseUrl['path'])) {
					$uri = $rsParseUrl['path'];
				}
				
				if(isset($rsParseUrl['query'])) {
					$uri .= '?'.$rsParseUrl['query'];
				}
				
				$input_options['data_send']['post']['m_ntv_p__tms'] = self::$defaultParams['requestTime'].mt_rand();
				
				if(isset($input_options['data_send']['post']) && is_array($input_options['data_send']['post'])) {
					$parametersTemp = $input_options['data_send']['post'];
					$post_string = http_build_query($parametersTemp);
				}
				
				if(isset($input_options['data_send']['cookie']['data']) && is_array($input_options['data_send']['cookie']['data'])) {
					$parametersTemp = $input_options['data_send']['cookie']['data'];
					$cookie_string = http_build_query($parametersTemp, '', '; '); 
					
				}
				
				$out .= 'POST '.$uri.' HTTP/1.1'.$crlf;
				$out .= 'Host: '.$rsParseUrl['host'].$crlf;
				
				if($cookie_string) {
					$out .= 'Cookie: '. $cookie_string . $crlf; 
				}
				
				if($post_string) {
					$out .= 'Content-Type: application/x-www-form-urlencoded'.$crlf;
					$out .= 'Content-Length: '.strlen($post_string).$crlf;
				}
				
				$out .= 'Connection: Close'.$crlf.$crlf;
				if($post_string) {
					$out .= $post_string;
				}
				
				fwrite($fp, $out);
				fclose($fp);
			}
			
			$fp = 0;
			unset($fp);
			
		}
	}
	
	public static function addParamStringToUrl($input_url, $input_param_string)
    {
		$resultData = $input_url;
		
		if($input_url && $input_param_string) {
			
			$rsParseUrl = Utils::parse_url($input_url);
			
			if(is_array($rsParseUrl)) {
				
				if(isset($rsParseUrl['parameters']) && is_array($rsParseUrl['parameters'])) {
					$input_url .= '&';
				} else {
					if(false === stripos($input_url,'?')) {
						$input_url .= '?';
					}
				}
				
				$input_url .= $input_param_string;
				
				$resultData = $input_url;
			}
		}
		
		return $resultData;
	}
	
	public static function addParamsToUrl($input_url, $input_params)
    {
		$resultData = $input_url;
		
		if($input_url && $input_params) {
			
			$rsParseUrl = Utils::parse_url($input_url);
			
			if(is_array($rsParseUrl)) {
				$params = array();
				
				if(isset($rsParseUrl['parameters']) && is_array($rsParseUrl['parameters'])) {
					$params = array_merge($params, $rsParseUrl['parameters']);
				}
				
				if(is_array($input_params)) {
					$params = array_merge($params, $input_params);
				}
				
				$resultData = $rsParseUrl['url_no_parameters'].'?'.http_build_query($params);
				
			}
		}
		
		return $resultData;
	}
	
	public static function isEmptyArray($input_data) 
	{
		$resultData = true;
		
		if($input_data) {
			if(is_array($input_data)) {
				if(!empty($input_data)) {
					$resultData = false;
				}
			}
		}
		
		return $resultData;
	}
	
	public static function cleanArray($input_data) 
	{
		return wppepvn_clean_array($input_data);
	}
	
	public static function ref_sort_array_by_key(&$array, $input_key, $sort_type = 'desc') 
	{
		
		$sorter = array();
		
		$ret = array();
		
		reset($array);
		
		foreach ($array as $key1 => $val1) {
			if(isset($val1[$input_key])) {
				$sorter[$key1] = $val1[$input_key];
			} else {
				$sorter[$key1] = null;
			}
		}
		
		if('desc' === $sort_type) {
			arsort($sorter);	//desc
		} else {
			asort($sorter);	//asc
		}
		
		foreach ($sorter as $key1 => $val1) {
			unset($sorter[$key1]);
			$ret[$key1] = $array[$key1];
			unset($array[$key1]);
			$key1 = 0; $val1 = 0;
		}
		
		$array = $ret;
		$ret = 0;
		
	}
	
	public static function escapeByPattern($input_content, $input_options) 
	{
		$input_content = (string)$input_content;
		
		if(!isset($input_options['pattern'])) {
			$input_options['pattern'] = '';
		}
		
		if(!isset($input_options['target_patterns'])) {
			$input_options['target_patterns'] = array();
		}
		$input_options['target_patterns'] = (array)$input_options['target_patterns'];
		
		if(!isset($input_options['wrap_target_patterns'])) {
			$input_options['wrap_target_patterns'] = '';
		}
        
		$resultData = array(
			'content' => $input_content
			,'patterns' => array()
		);
		
		$checkStatus1 = false;
		if($input_options['pattern']) {
			if(!self::isEmptyArray($input_options['target_patterns'])) { 
				$checkStatus1 = true;
			}
		}
		
		if(!$checkStatus1) {
			return $resultData;
		}
		
		$patternsEscape1 = array();
		
		$matched1 = false;
		preg_match_all($input_options['pattern'],$input_content,$matched1);
		foreach($input_options['target_patterns'] as $keyOne => $valueOne) {
			if(isset($matched1[$valueOne]) && $matched1[$valueOne]) {
				if(!empty($matched1[$valueOne])) {
					foreach($matched1[$valueOne] as $keyTwo => $valueTwo) {
						$search1 = $valueTwo;
						$replace1 = md5($valueTwo);
						//$replace1 = str_split($replace1);
						//$replace1 = implode('_',$replace1);

						$patternsEscape1[$search1] = $input_options['wrap_target_patterns'].'_'.$replace1.'_'.$input_options['wrap_target_patterns'];
					}
				}
			}
		}
		
		if(!empty($patternsEscape1)) {
			$input_content = str_replace(array_keys($patternsEscape1),array_values($patternsEscape1),$input_content);
			$resultData['content'] = $input_content;
			$resultData['patterns'] = $patternsEscape1;
		}
        
		return $resultData;
	}
	
	public static function escapeHtmlTags($input_content) 
	{
		$input_content = (string)$input_content;
		
		$keyCache1 = Utils::hashKey(array(
			'PepVN_Data_escapeHtmlTags'
			,$input_content
		));

		$resultData = TempDataAndCacheFile::get_cache($keyCache1);

		if(null === $resultData) {
			
			$resultData = array(
				'content' => $input_content
				,'patterns' => array()
			);

			$patternsEscape1 = array();

			$matched1 = false;
			preg_match_all('/<a(\s+[^><]*?)?>.*?<\/a>/is',$input_content,$matched1);
			if(isset($matched1[0]) && $matched1[0]) {
				if(!empty($matched1[0])) {
					foreach($matched1[0] as $key1 => $value1) {
						$search1 = $value1;
						$replace1 = md5($value1);
						//$replace1 = str_split($replace1);
						//$replace1 = implode('_',$replace1);

						$patternsEscape1[$search1] = '_'.$replace1.'_';
					}
				}
			}
			
			$matched1 = false;
			preg_match_all('#<[^><]+>#i',$input_content,$matched1);
			if(isset($matched1[0]) && $matched1[0]) {
				if(!empty($matched1[0])) {
					foreach($matched1[0] as $key1 => $value1) {
						$search1 = $value1;
						$replace1 = md5($value1);
						//$replace1 = str_split($replace1);
						//$replace1 = implode('_',$replace1);

						$patternsEscape1[$search1] = '_'.$replace1.'_';
					}
				}
			}
            
			if(!empty($patternsEscape1)) {
				$input_content = str_replace(array_keys($patternsEscape1),array_values($patternsEscape1),$input_content);
				$resultData['content'] = $input_content;
				$resultData['patterns'] = $patternsEscape1;
			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $resultData);
		}
		
		return $resultData;
	}
	
	public static function escapeHtmlTagsAndContents($input_content, $input_tags) 
	{
		$input_content = (string)$input_content;
		
		$input_tags = (array)$input_tags;
		$input_tags = implode(';',$input_tags);
		$input_tags = preg_replace('#[\,\;]+#',';',$input_tags);
		$input_tags = explode(';',$input_tags);
		$input_tags = self::cleanArray($input_tags);
		
		$keyCache1 = Utils::hashKey(array(
			'PepVN_Data_escapeHtmlTagsAndContents'
			,$input_content
			,$input_tags
		));
		
		$resultData = TempDataAndCacheFile::get_cache($keyCache1);
		
		if(null === $resultData) {
			
			$resultData = array(
				'content' => $input_content
				,'patterns' => array()
			);

			$patternsEscape1 = array();
			
			if(!empty($input_tags)) {
				foreach($input_tags as $tagName) {
					$tagName = preg_replace('#[^a-z0-9]+#','',$tagName);
					$tagName = trim($tagName);
					if($tagName) {
						$matched1 = false;
						preg_match_all('#<('.self::preg_quote($tagName).')(\s+[^><]*?)?>(.*?</\1>)?#is',$input_content,$matched1);
						if(isset($matched1[0]) && $matched1[0]) {
							if(!empty($matched1[0])) {
								foreach($matched1[0] as $key1 => $value1) {
									$search1 = $value1;
									$replace1 = hash('crc32b',md5($value1));
									//$replace1 = str_split($replace1);
									//$replace1 = implode('_',$replace1);
									
									$patternsEscape1[$search1] = '_'.$replace1.'_';
								}
							}
						}
					}
				}
			}
			
			if(!empty($patternsEscape1)) {
				$resultData['content'] = $input_content;
				unset($input_content);
				$resultData['content'] = str_replace(array_keys($patternsEscape1),array_values($patternsEscape1),$resultData['content']); 
				$resultData['patterns'] = $patternsEscape1;
				unset($patternsEscape1);
			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $resultData);
			
		}
        
		return $resultData;
	}
	
	public static function escapeSpecialElementsInHtmlPage($input_content) 
	{

		$input_content = (string)$input_content;
		
		$keyCache1 = Utils::hashKey(array(
			'PepVN_Data_escapeSpecialElementsInHtmlPage'
			,$input_content
		));

		$resultData = TempDataAndCacheFile::get_cache($keyCache1);

		if(null === $resultData) {
			
			$resultData = array(
				'content' => $input_content
				,'patterns' => array()
			);

			$patternsEscape1 = array();

			$matched1 = false;
			
			preg_match_all('/<\!--\s*\[\s*if[^>]+>(.*?)<\!\s*\[\s*endif\s*\]\s*-->/si', $input_content, $matched1);
			if(isset($matched1[0]) && $matched1[0]) {
				if(!empty($matched1[0])) {
					foreach($matched1[0] as $key1 => $value1) {
						$search1 = $value1;
						$replace1 = md5($value1);
						//$replace1 = str_split($replace1);
						//$replace1 = implode('_',$replace1);

						$patternsEscape1[$search1] = '_'.$replace1.'_';
					}
				}
			}
			
			if(!empty($patternsEscape1)) {
				$input_content = str_replace(array_keys($patternsEscape1),array_values($patternsEscape1),$input_content);
				$resultData['content'] = $input_content;
				$resultData['patterns'] = $patternsEscape1;

			}
			
			TempDataAndCacheFile::set_cache($keyCache1, $resultData);
		}
		
		return $resultData;
	}
	
	public static function countSubDirsAndFilesInsideDir($dir) 
	{
		$resultData = false;
		
		if($dir) {
			if(file_exists($dir) && is_writable($dir)) {
				if (is_dir($dir)) {
					$resultData = 0;
					$objects = scandir($dir);
					foreach ($objects as $obj) {
						if (($obj !== '.') && ($obj !== '..')) {
							++$resultData;
						}
					}
				}
			}
		}
		
		return $resultData;
	}
	
	public static function isSameHost($input_link1,$input_link2)
	{
		$input_link1 = 'http://'.self::removeProtocolUrl($input_link1);
		$input_link2 = 'http://'.self::removeProtocolUrl($input_link2);
		$parseUrl1 = parse_url($input_link1);
		if(isset($parseUrl1['host']) && $parseUrl1['host']) {
			$parseUrl2 = parse_url($input_link2);
			if(isset($parseUrl2['host']) && $parseUrl2['host']) {
				$parseUrl1['host'] = self::strtolower(trim($parseUrl1['host']));
				$parseUrl2['host'] = self::strtolower(trim($parseUrl2['host']));
				if($parseUrl2['host'] === $parseUrl1['host']) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	static function toNumber($input_data)
	{
		$input_data = (string)$input_data;
		$input_data = trim($input_data);
		
		if($input_data) {
			$input_data = preg_replace('/[^0-9\.\+\-]+/', '', $input_data);
		}
		
		$input_data = (float)$input_data;
		
		return $input_data;
	}
	
	public static function formatFileSize($input_file_size_kb) //interger - Kilobytes - KB
	{
		$totalFileSize = self::toNumber($input_file_size_kb);
		$totalFileSize = round($totalFileSize/1,2);
		
		$totalFileSizeString = '0 KB';
		
		if(($totalFileSize>0) && ($totalFileSize<1024)) {
			$totalFileSizeString = number_format((float)$totalFileSize,2,'.',',').' KB';
		} else {
			$totalFileSize = round($totalFileSize/1024,2);
			if(($totalFileSize>=1) && ($totalFileSize<1024)) {
				//$totalFileSizeString = $totalFileSize.' MB';
				$totalFileSizeString = number_format((float)$totalFileSize,2,'.',',').' MB';
			} else {
				$totalFileSize = round($totalFileSize/1024,2);
				if(($totalFileSize>=1) && ($totalFileSize<1024)) {
					//$totalFileSizeString = $totalFileSize.' GB';
					$totalFileSizeString = number_format((float)$totalFileSize,2,'.',',').' GB';
				} else {
					$totalFileSize = round($totalFileSize/1024,2);
					if(($totalFileSize>=1) && ($totalFileSize<1024)) {
						//$totalFileSizeString = $totalFileSize.' TB';
						$totalFileSizeString = number_format((float)$totalFileSize,2,'.',',').' TB';
					}
				}
			}
		}
		
		$totalFileSizeString = trim($totalFileSizeString); 
		
		return $totalFileSizeString;
	}
	
	public static function encodeVar($input_data)
	{
		$input_data = json_encode($input_data);
		
		$input_data = utf8_encode($input_data);
		
		$input_data = self::base64Encode($input_data);
		
		return $input_data;
		
	}
	
	public static function decodeVar($input_data)
	{
		
		$input_data = self::base64Decode($input_data);
		
		$input_data = utf8_decode($input_data);
		
		$input_data = json_decode($input_data, true, 99999);
		
		return $input_data;
		
	}
	
	public static function encodeVarForBackstageSecurePHP($input_data)
	{
		return self::base64Encode(serialize($input_data));
	}
	
	public static function decodeVarForBackstageSecurePHP($input_data)
	{
		return unserialize(self::base64Decode($input_data));
	}
	
	public static function getDataSent()
	{
		$keyCache = '_pepvndata_get_data_sent';
		
		if(!isset(self::$cacheData[$keyCache])) {
			
			$resultData = false;
			
			$keyDataRequest = WP_PEPVN_KEY_DATA_REQUEST;
			
			if(isset($_GET[$keyDataRequest]) && $_GET[$keyDataRequest]) {
				$rsOne = Utils::decodeVar($_GET[$keyDataRequest]);
				if($rsOne && isset($rsOne['localTimeSent']) && $rsOne['localTimeSent']) {
					if(!$resultData) {
						$resultData = array();
					}
					$resultData = Utils::mergeArrays(array(
						$resultData
						,$rsOne
					));
				}
				$rsOne = false;
			}
			
			if(isset($_POST[$keyDataRequest]) && $_POST[$keyDataRequest]) {
				$rsOne = Utils::decodeVar($_POST[$keyDataRequest]);
				
				if($rsOne && isset($rsOne['localTimeSent']) && $rsOne['localTimeSent']) {
					if(!$resultData) {
						$resultData = array();
					}
					$resultData = Utils::mergeArrays(array(
						$resultData
						,$rsOne
					));
				}
				$rsOne = false;
			}
			
			self::$cacheData[$keyCache] = $resultData;
			$resultData = 0;
		}
		
		return self::$cacheData[$keyCache];
	}
    
	
	public static function encodeResponseData($input_data, $echo_status = false)
	{
		
		$keyDataRequest = WP_PEPVN_KEY_DATA_REQUEST;
		
		$callback = false;

		$resultData = array();
		
		$resultData[$keyDataRequest] = Utils::encodeVar($input_data);

		if(isset($_GET['jsoncallback'])) {
			$callback = trim($_GET['jsoncallback']);
		} elseif(isset($_GET['callback'])) {
			$callback = trim($_GET['callback']);
		}
		
		if($callback) {
			$resultData = $callback.'('.json_encode($resultData).')';
		} else {
			$resultData = json_encode($resultData);
		}
		
		if($echo_status) {
			if(!headers_sent()) {
				header('Content-type: application/json; charset=UTF-8');
			}
			echo $resultData;
		} else {
			return $resultData;
		}
	}
    
	public static function replaceSpecialChar($input_text, $input_replace_char = ' ')
	{
		return preg_replace('#['.preg_quote('`~!@#$%^&*()-_=+{}[]\\\|;:\'",.<>/?+“”‘’','#').']+#i',$input_replace_char,$input_text);
	}
	
	public static function fixPath($input_path)
	{
		$input_path = preg_replace('/(\\\|\/)+/i',DIRECTORY_SEPARATOR,$input_path);
		$input_path = preg_replace('#/+$#i','',$input_path);
		$input_path = preg_replace('#^/+#i',DIRECTORY_SEPARATOR,$input_path);
		return $input_path;
	}
	
	public static function createFolder($input_path, $input_chmod = '')
	{
		$resultData = '';
		
		$chmod = '';
		if($input_chmod) {
			$chmod = $input_chmod;
		}
		
		if(!$chmod) {
			$chmod = defined('WP_PEPVN_CHMOD') ? WP_PEPVN_CHMOD : 0755;
		}
		
		$pathRoot = '';
        
		if(defined('ABSPATH')) {
			$pathRoot = ABSPATH;
		}
        
		if($pathRoot) {
			$pathRoot = preg_replace('#/+$#i','',$pathRoot);
			$pathRoot .= DIRECTORY_SEPARATOR;
		}
		
		if($pathRoot) {
			$input_path = preg_replace('#^'.self::preg_quote($pathRoot).'#','',$input_path,1);
		}
		
		$input_path = self::fixPath($input_path);
		
		$pathTemp1 = $pathRoot.$input_path;
		
		if($pathTemp1 && file_exists($pathTemp1)) {
			$resultData = $pathTemp1;
			return $resultData;
		}
		
		$pathInfo = pathinfo($input_path);
		
		$arrayPath = explode(DIRECTORY_SEPARATOR, $input_path);
		if(isset($pathInfo['extension'])) {
			array_pop($arrayPath);
		}
		
		$folderPath = $pathRoot;
		$folderPath = preg_replace('#/+$#i','',$folderPath);
		
		foreach($arrayPath as $path1) {
			$folderPath .= DIRECTORY_SEPARATOR . $path1;
			$pathTemp1 = $folderPath;
			
			if($pathTemp1 && file_exists($pathTemp1)) {
			} else {
				
				@mkdir($folderPath);
				$pathTemp1 = $folderPath;
				
				if($pathTemp1 && file_exists($pathTemp1)) {
				} else {
					return $resultData;
				}
			}
		}
		
		if($folderPath) {
			
			if($folderPath && file_exists($folderPath)) {
				$resultData = $folderPath . DIRECTORY_SEPARATOR; 
				if(isset($pathInfo['extension'])) {
					if(isset($pathInfo['basename'])) {
						$resultData .= $pathInfo['basename'];
					}
					
				}
			}
		}
		
		return $resultData;
		
	}//End Function
	
	public static function checkChmod($input_path, $input_chmod)
	{
		$resultData = false;
		
		if($input_path && file_exists($input_path)) {
			$pathPerms = substr(sprintf('%o', fileperms($input_path)), -4);
			
			$pathPerms = (int)$pathPerms;
			
			$input_chmod = (int)$input_chmod;
			
			if($pathPerms == $input_chmod) {
				$resultData = true;
			}
		}
		
		return $resultData;
	}
	
	public static function isAllAllowReadAndWriteIfExists($input_path,$input_from_path='')
	{
		$resultData = true;
		
		$input_path = self::fixPath($input_path);
		$input_from_path = self::fixPath($input_from_path);
		
		$pathNeedChmod = $input_from_path;
		
		if($input_from_path) {
			$input_path = preg_replace('#^'.preg_quote($input_from_path,'#').'#','',$input_path);
			$input_path = self::fixPath($input_path);
		}
		
		$arrayPath = explode(DIRECTORY_SEPARATOR, $input_path);
		
		foreach($arrayPath as $path1) {
			$pathNeedChmod .= DIRECTORY_SEPARATOR . $path1;
			$pathTemp1 = $pathNeedChmod;
			
			if($pathTemp1 && file_exists($pathTemp1)) {
				if(is_readable($input_path) && is_writable($input_path)) {
					
				} else {
					$resultData = false;
				}
			}
			
			if(!$resultData) {
				break;
			}
		}
		
		return $resultData;
	}
	
	public static function getFolderPath($input_path)
	{
		$resultData = '';
		
		$input_path = self::fixPath($input_path);
		
		$pathInfo = pathinfo($input_path);
		
		
		$arrayPath = explode(DIRECTORY_SEPARATOR, $input_path);
		if(isset($pathInfo['extension'])) {
			array_pop($arrayPath);
		}
		
		$resultData = implode(DIRECTORY_SEPARATOR,$arrayPath);
		
		return $resultData;
	}
	
	public static function isAllowReadAndWrite($input_path)
	{
		$resultData = false;
		
		if(self::isAllowRead($input_path)) {
			if(is_writable($input_path)) {
				$resultData = true;
			}
		}
		
		return $resultData;
	}
	
	public static function isAllowRead($input_path)
	{
		$resultData = false;
		
		if($input_path) {
			if(file_exists($input_path)) {
				if(is_readable($input_path)) {
					$resultData = true;
				}
			}
		}
		
		return $resultData;
	}
	
	public static function getHeadersListEncoded($method_encode='serialize')
	{
		$keyCache = self::mcrc32('getHeadersListEncoded_' . $method_encode);
		
		if(!isset(self::$cacheData[$keyCache])) {
			self::$cacheData[$keyCache] = headers_list();
			self::$cacheData[$keyCache] = serialize(self::$cacheData[$keyCache]);
		}
		
		return self::$cacheData[$keyCache];
	}
	
	public static function getRequestMethod()
	{
		$keyCache = '_g_rq_mt';
		
		if(!isset(self::$cacheData[$keyCache])) {
			$resultData = 'get';
			
			if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']) {
				$resultData = $_SERVER['REQUEST_METHOD'];
			} else {
				if(isset($_POST) && $_POST && !empty($_POST)) {
					$resultData = 'post';
				}
			}
			
			$resultData = trim($resultData);
			$resultData = strtoupper($resultData);
			
			self::$cacheData[$keyCache] = $resultData;
		}
		
		
		return self::$cacheData[$keyCache];
	}
	
	public static function getContentOfHeadTagHtml($text)
	{
		preg_match_all('#<head>(.+)</head>#is',$text,$matched1);
		
		if(isset($matched1[1][0]) && $matched1[1][0]) {
			return trim($matched1[1][0]);
		}
		
		return false;
	}
	
	public static function setTagHtml($input_params)
	{
		//$originalFullText = $input_params['text'];
		
		$patternsSearchAndReplace = array();
		$textAppendToTagHeadOfHtml = '';
		
		$targetText = '';
		
		if(isset($input_params['find_in_head_status'])) {
			
			preg_match_all('#<head>(.+)</head>#is',$input_params['text'],$matched1);
			
			if(isset($matched1[1][0]) && $matched1[1][0]) {
				$targetText = trim($matched1[1][0]);
			}
			
			unset($matched1);
			
		} else {
			$targetText = $input_params['text'];
		}
		
		if($targetText) {
			
			preg_match_all('#(<'.preg_quote($input_params['tag_name'],'#').'[\s \t]+[^>]+/?>)#is',$targetText,$matched1);
			
			if(
				isset($matched1[1][0])
			) {
				
				$matched1 = $matched1[1];
				
				foreach($matched1 as $key1 => $value1) {
					
					unset($matched1[$key1]);
					
					foreach($input_params['set_tags'] as $key2 => $value2) {
						
						if(preg_match('#'.preg_quote($value2['search_key'],'#').'=(\'|\")[^\'\"]*'.preg_quote($value2['search_value'],'#').'[^\'\"]*\1#is',$value1)) {
							if(isset($value2['remove_status'])) {
								$patternsSearchAndReplace[$value1] = '';
							} else {
								if(preg_match('#('.preg_quote($value2['set_key'],'#').'=(\'|\")([^\'\"]*)\2)#is',$value1,$matched2)) {
									
									if(isset($matched2[3]) && $matched2[3] && (false !== stripos($matched2[3],$value2['set_value']))) {
										unset($input_params['set_tags'][$key2]);
									} else {
										if(isset($value2['replace_status'])) {
											$patternsSearchAndReplace[$value1] = preg_replace('#('.preg_quote($value2['set_key'],'#').')=(\'|\")([^\'\"]*)\2#is','\1=\2'.$value2['set_value'].'\2',$value1);
										} else {
											$patternsSearchAndReplace[$value1] = preg_replace('#('.preg_quote($value2['set_key'],'#').'=(\'|\")([^\'\"]*))#is','\1 '.$value2['set_value'],$value1);
										}
										unset($input_params['set_tags'][$key2]);
									}
								}
								unset($matched2);
							}
						}
					}
				}
			}
			
			unset($matched1);
		}
		
		foreach($input_params['set_tags'] as $key1 => $value1) {
			unset($input_params['set_tags'][$key1]);
			if(isset($value1['full_set'])) {
				$textAppendToTagHeadOfHtml .= $value1['full_set'];
			}
		}
		unset($input_params['set_tags']);
		
		if(!empty($patternsSearchAndReplace)) {
			$input_params['text'] = str_replace(array_keys($patternsSearchAndReplace),array_values($patternsSearchAndReplace),$input_params['text']);
		}
		unset($patternsSearchAndReplace);
		
		if($textAppendToTagHeadOfHtml) {
			$input_params['text'] = self::appendTextToTagHeadOfHtml($textAppendToTagHeadOfHtml, $input_params['text']);
		}
		unset($textAppendToTagHeadOfHtml);
		
		return $input_params['text'];
	}
	
	public static function isAjaxRequest()
	{
		$keyCache = '_i_aj_rq';
		
		if(!isset(self::$cacheData[$keyCache])) {
			
			$resultData = false;
			
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
					$resultData = true;
				}
			}
			
			self::$cacheData[$keyCache] = $resultData;
		}
		
		
		return self::$cacheData[$keyCache];
	}
	
	
	
	public static function isHttpResponseCode($response_code)
	{
		$response_code = (int)$response_code;
		
		$keyCache = '_i_htp_rp_cd_'.$response_code;
		
		if(!isset(self::$cacheData[$keyCache])) {
			
			$resultData = false;
			
			$httpResponseCode = http_response_code();
			if($httpResponseCode) {
				$httpResponseCode = (int)$httpResponseCode;
				if($httpResponseCode === $response_code) {
					$resultData = true;
				}
			}
			
			self::$cacheData[$keyCache] = $resultData;
		}
		
		
		return self::$cacheData[$keyCache];
	}
	
	public static function preg_quote($input_text)
	{
		return preg_quote($input_text,'#');
	}
	
	
	public static function parse_load_html_scripts_by_tag($input_parameters) 
	{
		$resultData = '';
		
		if(isset($input_parameters['url']) || isset($input_parameters['code'])) {
			
			if(isset($input_parameters['url'])) {
				$input_parameters['url'] = self::removeProtocolUrl($input_parameters['url']);
				if(!isset($input_parameters['id'])) {
					$input_parameters['id'] = Hash::crc32b($input_parameters['url']);
				}
			} else if(isset($input_parameters['code'])) {
				if(!isset($input_parameters['id'])) {
					$input_parameters['id'] = Hash::crc32b(md5($input_parameters['code']));
				}
			}
			
			$loaderId = Hash::crc32b($input_parameters['id'].'_loader');
			
			$loadTimeDelay = 5;
			if('js' === $input_parameters['type']) {
				$loadTimeDelay = 5;
			} else {
				if(!isset($input_parameters['media'])) {
					$input_parameters['media'] = 'all';
				}
			}
			
			$loadTimeDelay = (int)$loadTimeDelay;
			if($loadTimeDelay < 1) {
				$loadTimeDelay = 1;
			}
			
			if('js' === $input_parameters['load_by']) {
				
				if('js' === $input_parameters['type']) {
					$resultData = ' <script data-cfasync="false" language="javascript" type="text/javascript" id="'.$loaderId.'" defer async>
/*<![CDATA[*/
setTimeout(function() {
(function(e) { var t, n, r, s, i = "'.$input_parameters['id'].'"; if(e.getElementById(i)) { return 0; } t = document.location.protocol; if(-1 !== t.indexOf("https")) { n = "https:"; } else { n = "http:"; } r = e.createElement("script"); r.setAttribute("data-cfasync","false"); r.id = i; r.setAttribute("language","javascript"); r.setAttribute("type","text/javascript"); r.async = true; r.src = n + "//'.$input_parameters['url'].'"; s = e.getElementById("'.$loaderId.'"); s.parentNode.insertBefore(r, s); s.parentNode.removeChild(s); })(document);
}, '.$loadTimeDelay.');
/*]]>*/
</script> ';
				} else if('css' === $input_parameters['type']) { 
					if(!isset($input_parameters['append_to'])) {
						$input_parameters['append_to'] = 'head';
					}
					
					if('head' === $input_parameters['append_to']) {
					
						$resultData = ' <script language="javascript" type="text/javascript" id="'.$loaderId.'" defer async>
/*<![CDATA[*/
setTimeout(function() {
(function(e) { var t, n, r, s, hd = document.getElementsByTagName("head")[0], i = "'.$input_parameters['id'].'"; if(e.getElementById(i)) { return 0; } t = document.location.protocol; if(-1 !== t.indexOf("https")) { n = "https:"; } else { n = "http:"; } r = e.createElement("link"); r.id = i; r.setAttribute("rel","stylesheet"); r.setAttribute("type","text/css"); r.setAttribute("media","'.$input_parameters['media'].'"); r.async = true; r.href = n + "//'.$input_parameters['url'].'"; hd.appendChild(r); s = e.getElementById("'.$loaderId.'"); s.parentNode.removeChild(s); })(document);
}, '.((1 * $loadTimeDelay) + 2).');
/*]]>*/
</script> ';

					} else {
						$resultData = ' <script language="javascript" type="text/javascript" id="'.$loaderId.'">
/*<![CDATA[*/
setTimeout(function() {
(function(e) { var t, n, r, s, i = "'.$input_parameters['id'].'"; if(e.getElementById(i)) { return 0; } t = document.location.protocol; if(-1 !== t.indexOf("https")) { n = "https:"; } else { n = "http:"; } r = e.createElement("link"); r.id = i; r.setAttribute("rel","stylesheet"); r.setAttribute("type","text/css"); r.setAttribute("media","'.$input_parameters['media'].'"); r.async = true; r.href = n + "//'.$input_parameters['url'].'"; s = e.getElementById("'.$loaderId.'"); s.parentNode.insertBefore(r, s); s.parentNode.removeChild(s); })(document);
}, '.$loadTimeDelay.');
/*]]>*/
</script> ';
					}
					
				}
				
				
			} else if(
				('div_tag' === $input_parameters['load_by'])
				|| ('js_data' === $input_parameters['load_by'])
			) {
				
				$configs = array(
					'delay' => $loadTimeDelay
					,'loader_id' => $loaderId
					,'id' => $input_parameters['id']
					,'type' => $input_parameters['type']
				);
				
				if(isset($input_parameters['url'])) {
					$configs['url'] = $input_parameters['url'];
				} else if(isset($input_parameters['code'])) {
					$configs['code'] = $input_parameters['code'];
				}
				
				if(isset($input_parameters['media'])) {
					$configs['media'] = $input_parameters['media'];
				}
				
				if(
					('div_tag' === $input_parameters['load_by'])
				) {
					$resultData = ' <div class="wp-optimize-speed-by-xtraffic-loader-data-'.$input_parameters['type'].'" id="'.$loaderId.'" data-pepvn-configs="'.Utils::encodeVar($configs).'" style="display:none;"></div> ';  
				} else if(
					('js_data' === $input_parameters['load_by'])
				) {
					$keyStoreJs = 'window.wppepvnloaderdata'.$input_parameters['type'];
					
					$resultData = ' <script language="javascript" type="text/javascript" id="'.$loaderId.'">
if(typeof('.$keyStoreJs.') === "undefined") { '.$keyStoreJs.' = new Array(); }
'.$keyStoreJs.'.push("'.Utils::encodeVar($configs).'");
</script> ';
				}
			}
			
		}
		
		return $resultData;
	}
	
	
	
	
	static function removeBOM($input_text)
	{
		
		if(false !== stripos($input_text,"\xEF\xBB\xBF")) {
			$input_text = str_replace("\xEF\xBB\xBF","",$input_text);
		}
		
		return $input_text;  
	}
	
	
	static function fixHtmlPage($input_html_page_text)
	{
		
		$resultData = trim($input_html_page_text);
		
		if($resultData) {
			
			$input_html_page_text = self::removeBOM($input_html_page_text); 
			
			$doc = new \DOMDocument;
			libxml_use_internal_errors(TRUE); 
			
			$hackstring = ' <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> ';
			
			$input_html_page_text = preg_replace('/<html[^>]*>\s*<head[^>]*>/i',' <html xmlns="http://www.w3.org/1999/xhtml"> <head> '.$hackstring, $input_html_page_text, 1, $counts);
			
			if(!$counts) {
				$input_html_page_text = $hackstring.' '.$input_html_page_text;
			}
			
			$doc->loadHTML($input_html_page_text);
			
			$input_html_page_text = $doc->saveHTML();
			if($input_html_page_text) {
				$input_html_page_text = trim($input_html_page_text);
				if($input_html_page_text) {
					$resultData = $input_html_page_text;
				}
			}
			libxml_clear_errors();
			$doc = NULL;unset($doc);
		}
		
		
		return $resultData;
	}
	
	
	static function getAllImagesTags($text,$strictStatus = false)
	{
		$resultData = array();
		
		if(preg_match_all('#<img[^>]+\\\?>#i', $text, $matched1)) {
			
			if(isset($matched1[0]) && is_array($matched1[0]) && (!empty($matched1[0]))) {
				
				$matched1 = $matched1[0];
				
				foreach($matched1 as $key1 => $value1) {
					unset($matched1[$key1]);
					
					$rsOne = Utils::parseAttributesHtmlTag($value1);
					
					if(isset($rsOne['attributes'])) {
						
						if($strictStatus) {
							if(isset($rsOne['attributes']['src']) && $rsOne['attributes']['src'] && Utils::isUrl($rsOne['attributes']['src'])) {
								$resultData[$value1] = array(
									'full' => $value1
									,'attributes' => $rsOne['attributes']
								);
							}
						} else {
							$resultData[$value1] = array(
								'full' => $value1
								,'attributes' => $rsOne['attributes']
							);
						}
						
					}
					
					
					unset($key1,$value1,$rsOne);
				}
			}
		}
		
		return $resultData;
	}
	
	
	static function getRoundUpTime($round)
	{
		$round = (int)$round;
		$rs = ceil(time() / $round) * $round;
		return $rs;
	}
	
}//class PepVN_Data

PepVN_Data::setDefaultParams();


