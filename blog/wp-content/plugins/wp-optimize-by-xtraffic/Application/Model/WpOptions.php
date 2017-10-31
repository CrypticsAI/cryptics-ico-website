<?php 
namespace WPOptimizeByxTraffic\Application\Model;

use WPOptimizeByxTraffic\Application\Model\ModelBase
	,WPOptimizeByxTraffic\Application\Service\PepVN_Data
	,WpPepVN\Utils
	,WpPepVN\Exception
	,WpPepVN\Hook
;
/*
	This class use for get and update option plugin. If you want to get others options, use \WpExtend or function instead
*/
class WpOptions extends ModelBase
{
	private static $_prefix = '';
	
	public function __construct() 
    {
		parent::__construct();
		
	}
	
	public static function init() 
	{
		self::$_prefix = WP_PEPVN_NS_SHORT.'_';
	}
	
	public static function cleanCache() 
	{
		self::$_tempData = array();
	}
	
	public static function wp_cache_delete() 
	{
		wp_cache_delete('alloptions', 'options');
	}
	
	public static function get($option_name, $default_data = null) 
    {
		return self::get_option($option_name, $default_data, array(
			'cache_status' => true
		));
	}
	
	public static function get_option($option_name, $default_data = null, $configs = false) 
    {
		if(!$configs) {
			$configs = array();
		}
		
		$k = Utils::hashKey(array('WPOptimizeByxTraffic_WpOptions_get_option', $option_name, $default_data));
		
		if(isset($configs['cache_status']) && $configs['cache_status']) {
			
			if(isset(self::$_tempData[$k])) {
				return self::$_tempData[$k];
			} else {
				$tmp = PepVN_Data::$cacheObject->get_cache($k);
				if($tmp !== null) {
					self::$_tempData[$k] = $tmp;
					return self::$_tempData[$k];
				}
			}
		}
		
		$maximumLengthOptionName = 64 - strlen(self::$_prefix);
		
		if(strlen($option_name) > $maximumLengthOptionName) {
			throw new Exception('Option\'s name is too long. Maximum length is "'.$maximumLengthOptionName.' characters".');
		}
		
		self::$_tempData[$k] = get_option(self::$_prefix.$option_name, $default_data);
		
		if(isset($configs['cache_status']) && $configs['cache_status']) {
			PepVN_Data::$cacheObject->set_cache($k, self::$_tempData[$k]);
		}
		
		return self::$_tempData[$k];
		
	}
	
	public static function update_option($option_name, $value, $autoload = false) 
    {
		$maximumLengthOptionName = 64 - strlen(self::$_prefix);
		
		if(strlen($option_name) > $maximumLengthOptionName) {
			throw new Exception('Option\'s name is too long. Maximum length is "'.$maximumLengthOptionName.' characters".');
		}
		
		self::wp_cache_delete();
		$status = update_option(self::$_prefix.$option_name, $value, $autoload);
		self::wp_cache_delete();
		
		if(Hook::has_action('update_option')) {
			Hook::do_action('update_option', $option_name, $value);
		}
		
		return $status;
	}
	
}

WpOptions::init();
