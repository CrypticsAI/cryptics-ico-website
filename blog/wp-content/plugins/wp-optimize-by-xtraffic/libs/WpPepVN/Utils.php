<?php
namespace WpPepVN;

use WpPepVN\Text
	, WpPepVN\Hash
	, WpPepVN\System
;

class Utils 
{
	public static $defaultParams = false;
	
	private static $_tempData = array();
	
    public function __construct() 
    {
        
    }
    
	public static function setDefaultParams()
	{
		if(false === self::$defaultParams) {
			self::$defaultParams['status'] = true;
			
			self::$defaultParams['_has_igbinary'] = false;
			if(function_exists('igbinary_serialize')) {
				self::$defaultParams['_has_igbinary'] = true;
			}
			
			/*
			self::$defaultParams['_hash_algo_long_data_fastest'] = self::getAvailableFastestHashAlgoLongText();//long text
			
			self::$defaultParams['_enum_hash_algo_fastest_8c'] = array(	//return shortest data
				'adler32' => true
				,'fnv132' => true
				,'crc32b' => true
				,'crc32' => true
			);
			*/
			
		}
	}
	
	public static function getAvailableFastestHashAlgoLongText()
	{
		$k = 'gavfhalt';
		
		if(!isset(self::$_tempData[$k])) {
			
			$tmp = 'md5';
			
			if(Hash::has_algos('md4')) {
				$tmp = 'md4';
			}
			
			/*
			if(version_compare(PHP_VERSION, '5.4.0', '>=')) {
				if(Hash::has_algos('adler32')) {
					$tmp = 'adler32';
				} else if(Hash::has_algos('fnv132')) {
					$tmp = 'fnv132';
				} else if(Hash::has_algos('fnv164')) {
					$tmp = 'fnv164';
				} else if(Hash::has_algos('md4')) {
					$tmp = 'md4';
				}
			} else {
				if(Hash::has_algos('md4')) {
					$tmp = 'md4';
				}
			}
			*/
			
			self::$_tempData[$k] = $tmp;
		}
		
		return self::$_tempData[$k];
		
	}
	
	public static function getAvailableFastestHashAlgoHasShortest()
	{
		$k = 'gavfhahs';
		
		if(!isset(self::$_tempData[$k])) {
			
			$tmp = 'crc32';
			
			if(Hash::has_algos('adler32')) {
				$tmp = 'adler32';
			} else if(Hash::has_algos('fnv132')) {
				$tmp = 'fnv132';
			} else if(Hash::has_algos('crc32b')) {
				$tmp = 'crc32b';
			}
			self::$_tempData[$k] = $tmp;
		}
		
		return self::$_tempData[$k];
		
	}
	
	public static function hasIgbinary()
	{
		return self::$defaultParams['_has_igbinary'];
	}
	
	public static function mergeArrays($input_parameters)
	{
		$merged = false;
		
		if(is_array($input_parameters)) {
		
			$merged = array_shift($input_parameters); // using 1st array as base
			
			foreach($input_parameters as $array) {
				foreach ($array as $key => $value) {
					
					if(isset($merged[$key]) && is_array($value) && is_array($merged[$key])) {
						
						$merged[$key] = self::mergeArrays(array($merged[$key], $value));
						
					} else {
                        if(is_numeric($key)) {
							$merged[] = $value;
						} else {
							$merged[$key] = $value;
						}
					}
				}
			}
		}
		
		return $merged;
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
	
	/*
	* This method create fastest * shortest key for short time. Don't use this method to create ID for store in database (use method makeID instead)
	*/
	public static function hashKey($input_data)
	{
		return hash('crc32b', md5(self::serialize($input_data)), false);
		
		$algo = self::$defaultParams['_hash_algo_long_data_fastest'];
		
		if(isset(self::$defaultParams['_enum_hash_algo_fastest_8c'][$algo])) {
			return hash(
				$algo
				, self::serialize($input_data)
				, false
			);
		} else {
			return hash(
				'crc32b'
				, hash(
					$algo
					, self::serialize($input_data)
					, false
				)
				, false
			);
		}
	}
	
	public static function randomHash()
	{
		return md5(time() + mt_rand());
	}
	
	/*
	* IMPORTANT : This method create ID for store in database. Don't change anything in this method because it affects many serious issues
	*/
	public static function makeID($input_data, $strict_status = false)
	{
		$input_data = serialize($input_data);
		
		if(false === $strict_status) {
			$input_data = preg_replace('#[\s \t]+#is', '', $input_data);
		}
		
		return md5($input_data);
	}
	
	public static function base64_encode($input_data)
	{
		return str_replace(array('+','/','='), array('-','_','.'), base64_encode($input_data));
	}
	
	public static function base64_decode($input_data)
	{
		return base64_decode(str_replace(array('-','_','.'), array('+','/','='), $input_data));
	}
	
	public static function encodeVar($input_data)
	{
        return self::base64_encode(json_encode($input_data));
	}
	
	public static function decodeVar($input_data)
	{
		return json_decode(self::base64_decode($input_data), true);
	}
	
	public static function gzVar($input_data, $gzip_level = 2)	//gzip_level >= 0 && gzip_level <= 9
	{
		$isBool = is_bool($input_data);
		
		$input_data = array(
			'c' => false //compress status
			,'d' => self::serialize($input_data) 	//data
		);
		
		if($gzip_level > 0) {
			if(!$isBool) {
				$input_data['c'] = true;
				$input_data['d'] = gzcompress($input_data['d'], $gzip_level);
			}
		}
		
		return $input_data;
	}
	
	public static function ungzVar($input_data)
	{
		if(true === $input_data['c']) {
			$input_data['d'] = gzuncompress($input_data['d']);
		}
		
		$input_data['d'] = self::unserialize($input_data['d']);
		
		return $input_data['d']; 
	}
	
	public static function preg_quote($input_text, $delimiter = '#')
	{
		return preg_quote($input_text, $delimiter);
	}
		
	/**
	 * Determine if SSL is used.
	 *
	 * @return bool True if SSL, false if not used.
	 */
	public static function is_ssl() 
	{
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS']) ) {
				return true;
			}
			if ( '1' == $_SERVER['HTTPS'] ) {
				return true;
			}
		} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			return true;
		}
		
		return false;
	}
	
	public static function parse_url($url)
	{
		return wppepvn_parse_url($url);
	}
	
	public static function fixPath($input_path)
	{
		$input_path = preg_replace('#[/\\\]+#i',DIRECTORY_SEPARATOR,$input_path);
		
		$input_path = trim($input_path, DIRECTORY_SEPARATOR);
		
		return $input_path;
	}
	
	public static function remove_special_chars_filename($filename)
	{
		$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
		
		$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename);
		$filename = str_replace( $special_chars, '', $filename);
		$filename = str_replace( array( '%20', '+' ), '-', $filename );
		$filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
		$filename = trim( $filename, '.-_' );
		
		$filename = trim($filename);
		
		return $filename;
	}
	
	public static function safeFileName($filename)
	{
		$pathInfo = pathinfo($filename);
		
		if(isset($pathInfo['extension']) && isset($pathInfo['filename'])) {
			
			$pathInfo['filename'] = preg_replace('#[\.]+#i',' ',$pathInfo['filename']);
			$pathInfo['extension'] = preg_replace('#[\.]+#i','.',$pathInfo['extension']);
			
			$pathInfo['extension'] = trim($pathInfo['extension']);
			$pathInfo['filename'] = trim($pathInfo['filename']);
			
			$pathInfo['extension'] = self::remove_special_chars_filename($pathInfo['extension']);
			$pathInfo['filename'] = self::remove_special_chars_filename($pathInfo['filename']);
			
			$pathInfo['extension'] = trim($pathInfo['extension']);
			$pathInfo['filename'] = trim($pathInfo['filename']);
			
			$pathInfo['filename'] = Text::toSlug($pathInfo['filename']);
			
			if(isset($pathInfo['dirname'])) {
				$filename = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $pathInfo['extension'];
			} else {
				$filename = $pathInfo['filename'] . '.' . $pathInfo['extension'];
			}
		} else {
			$filename = Text::toSlug($filename);
		}
		
		return $filename;
	}
	
	public static function isUrl($input_data)
	{
		$resultData = false;
		
		if(preg_match('#^https?://.+$#i',$input_data)) {
			$resultData = true;
		}
		
		return $resultData; 
	}
	
	public static function removeScheme($input_url) 
	{
		$input_url = trim($input_url);
		
		$input_url = preg_replace('#^(https?:)?//#i','//',$input_url);
		
		return $input_url;
	}
	
	public static function getHeadersList($strict_status = false) 
	{
		
		$k = self::hashKey(array('getHeadersList', $strict_status));
		if(!isset(self::$_tempData[$k])) {
			self::$_tempData[$k] = array();
			
			$tmp = headers_list();
			if($tmp) {
				foreach($tmp as $key1 => $value1) {
					unset($tmp[$key1]);
					$value1 = explode(':',$value1,2);
					$value1[0] = trim($value1[0]);
					if(!$strict_status) {
						$value1[0] = strtolower($value1[0]);
					}
					if(isset($value1[1])) {
						
						$value1[1] = trim($value1[1]);
						
						if(!$strict_status) {
							$value1[1] = strtolower($value1[1]);
						}
						
						self::$_tempData[$k][$value1[0]] = $value1[1];
						
					} else {
						self::$_tempData[$k][$value1[0]] = '';
					}
				}
			}
		}
		
		
	}
	
	public static function getContentTypeHeadersList() 
	{
		$headersList = self::getHeadersList();
		if(isset($headersList['content-type'])) {
			return $headersList['content-type'];
		}
		
		return '';
	}
	
	public static function removeQuotes($s)
	{
		$s = (string)$s;
		return preg_replace('#[\'\"]+#is','',$s);
	}
	
	public static function isImageFilePath($filePath)
	{
		$resultData = false;
		
		if(preg_match('#^.+\.(gif|jpeg|jpg|png)$#i',$filePath)) {
			$resultData = true;
		}
		
		return $resultData;
	}
	
	public static function isImageUrl($url)
	{
		$resultData = false;
		
		if(preg_match('#^(https?:)?//.+\.(gif|jpeg|jpg|png)\??.*$#i',$url)) {
			$resultData = true;
		}
		
		return $resultData;
	}
	
	public static function isUrlSameHost($url, $host)
	{
		$resultData = false;
		
		if(preg_match('#^(https?:)?//'.preg_quote($host,'#').'/?#i',$url)) {
			$resultData = true;
		}
		
		return $resultData;
	}
	
	public static function ipVersion($ip)
	{
		if(self::isIPv4($ip)) {
			return 'v4';
		} else if(self::isIPv6($ip)) {
			return 'v6';
		} else {
			return false;
		}
	}
	
	public static function isIPv4($ip)
	{
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return true;
		}
		
		return false;
	}
	
	
	public static function isIPv6($ip)
	{
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return true;
		}
		
		return false;
	}
	
	public static function isUrlSameDomain($url, $domain, $strict_status = true)
	{
		$resultData = false;
		
		if(true === $strict_status) {
			if(preg_match('#^(https?:)?//'.preg_quote($domain,'#').'/?#i',$url)) {
				$resultData = true;
			}
		} else {
			if(preg_match('#^(https?:)?//[^/\?]*'.preg_quote($domain,'#').'/?#i',$url)) {
				$resultData = true;
			}
		}
		
		return $resultData;
	}
	
	public static function xml2Array(
		$data	//text, xml string
	) {
		
		$data = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_ERR_NONE);
		
		if($data) {
			$data = json_encode($data);
			if($data) {
				$data = json_decode($data, true);
				if($data) {
					return $data;
				}
			}
		}
		
		return false;
	}
	
	public static function parseAttributeNameAndValue($text)
	{
		$k = crc32('parseAttributeNameAndValue_'.$text);
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		}
		
		self::$_tempData[$k] = array();
		
		preg_match('#([a-z0-9\-\_]+)(=(\"|\')([^\'\"]+)\3)?#is',$text,$matched1);
		
		if(isset($matched1[1]) && $matched1[1]) {
			if(isset($matched1[4]) && $matched1[4]) {
				self::$_tempData[$k][$matched1[1]] = $matched1[4];
			} else {
				self::$_tempData[$k][$matched1[1]] = '';
			}
		}
		
		return self::$_tempData[$k];
	}
	
	public static function parseAttributesNamesAndValues($text)
	{
		$k = crc32('parseAttributesNamesAndValues_'.$text);
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		}
		
		self::$_tempData[$k] = array();
		
		$text = ' '.$text.' ';
		
		preg_match_all('#\s+([a-z0-9\-\_]+)(=(\"|\')[^\'\"]+\3)?#is',$text,$matched2);
		
		$matched2 = $matched2[0];
		
		foreach($matched2 as $key1 => $value1) {
			
			unset($matched2[$key1]);
			
			$value1 = self::parseAttributeNameAndValue($value1);
			
			if(!empty($value1)) {
				self::$_tempData[$k] = array_merge(self::$_tempData[$k],$value1);
			}
			
			unset($key1,$value1);
		}
		
		return self::$_tempData[$k];
	}
	
	public static function parseAttributesHtmlTag($text)
	{
		$k = crc32('prsatthttg'.$text);
		
		if(isset(self::$_tempData[$k])) {
			
			return self::$_tempData[$k];
			
		} else {
			
			self::$_tempData[$k] = array();
			
			preg_match('#<([^\s \t/>]+)([^>]*)/?>#is',$text,$matched1);
			
			if(isset($matched1[1]) && $matched1[1]) {
				self::$_tempData[$k]['tag_name'] = $matched1[1];
			}
			
			if(isset($matched1[2]) && $matched1[2]) {
				
				$matched1 = self::parseAttributesNamesAndValues($matched1[2]);
				
				if(!empty($matched1)) {
					self::$_tempData[$k]['attributes'] = $matched1;
				}
			}
			
			return self::$_tempData[$k];
		}
	}
	
	
	public static function setAttributeHtmlTag(
		$text
		, $attr_name
		, $attr_value
		, $replaceAttrStatus = false
	) {
		$rsParseAttributesHtmlTag = self::parseAttributesHtmlTag($text);
		
		if(null === $attr_value) {
			$text = preg_replace('#\s+'.preg_quote($attr_name,'#').'(=(\"|\')[^\'\"]+\2)?#is',' ',$text);
		} else {
			
			$newAttrValue = '';
			
			if(!$replaceAttrStatus) {
				preg_match('#\s+('.preg_quote($attr_name,'#').')(=(\"|\')([^\'\"]+)\3)?#is',$text,$matched1);
				if(isset($matched1[1]) && $matched1[1]) {
					if(isset($matched1[4]) && $matched1[4]) {
						$newAttrValue = ' '.$matched1[4].' ';
					}
				}
			}
			
			if($newAttrValue) {
				$newAttrValue = preg_replace('#\s+'.preg_quote($attr_value,'#').'\s+#is',' ',$newAttrValue);
			}
			
			$newAttrValue .= ' '.$attr_value.' ';
			$newAttrValue = Text::removeSpace($newAttrValue,' ');
			$newAttrValue = Text::reduceSpace($newAttrValue);
			
			$text = preg_replace('#\s+'.preg_quote($attr_name,'#').'(=(\"|\')[^\'\"]+\2)?#is',' ',$text);
			$text = preg_replace('#<'.preg_quote($rsParseAttributesHtmlTag['tag_name'],'#').'\s+#is','<'.$rsParseAttributesHtmlTag['tag_name'].' '.$attr_name.'="'.esc_attr($newAttrValue).'"',$text);
		}
		
		return $text;
	}
	
	public static function trailingslashdir($dir)
	{
		return wppepvn_trailingslashdir($dir);
	}
	
	public static function untrailingslashdir($dir)
	{
		return wppepvn_untrailingslashdir($dir);
	}
	
	public static function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

		/* queries and anchors */
		if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

		/* parse base URL and convert to local variables:
		   $scheme, $host, $path */
		extract(parse_url($base));

		/* remove non-directory element from path */
		$path = preg_replace('#/[^/]*$#', '', $path);

		/* destroy path if relative url points to root */
		if ($rel[0] == '/') $path = '';

		/* dirty absolute URL */
		$abs = "$host$path/$rel";

		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

		/* absolute URL is ready! */
		return $scheme.'://'.$abs;
	}
}

Utils::setDefaultParams();
