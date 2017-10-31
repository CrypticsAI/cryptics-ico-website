<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\Utils
	, WpPepVN\DependencyInjection
;

class TempDataAndCacheFile
{
	
	private static $_tempData = array();
	
	private static $_key_salt = 0;
	
	private static $_di = 0;
	
	private static $_group = 'transient';
	
	public static function init(DependencyInjection $di)
	{
		self::$_di = $di;
		self::$_key_salt = hash('crc32b', 'WPOptimizeByxTraffic/Application/Service/TempDataAndCacheFile/init' . WP_PEPVN_SITE_SALT, false );
	}
	
	public static function get_cache($keyCache, $useCachePermanentStatus = false, $useWpCacheStatus = false) 
	{
		$keyCache = Utils::hashKey(array(
			self::$_key_salt
			, $keyCache
		));
		
		$resultData = null;
		
		if(isset(self::$_tempData[$keyCache])) {
			//$resultData = Utils::ungzVar(self::$_tempData[$keyCache]);
			
			$resultData = self::$_tempData[$keyCache];
		}
		
		if(null === $resultData) {
			
			$wp_cache_get_found = false;
			
			if($useWpCacheStatus) {
				$resultData = wp_cache_get($keyCache, self::$_group, false, $wp_cache_get_found );
			}
			
			if(!$wp_cache_get_found) {
				$resultData = null;
			}
			
			if(null !== $resultData) {
				//self::$_tempData[$keyCache] = Utils::gzVar($resultData);
				self::$_tempData[$keyCache] = $resultData;
			}
		}
		
		if(null === $resultData) {
			
			if(false === $useCachePermanentStatus) {
				$resultData = PepVN_Data::$cacheObject->get_cache($keyCache);
			} else {
				$resultData = PepVN_Data::$cachePermanentObject->get_cache($keyCache);
			}
			
			if(null !== $resultData) {
				//self::$_tempData[$keyCache] = Utils::gzVar($resultData);
				self::$_tempData[$keyCache] = $resultData;
				
				if($useWpCacheStatus) {
					wp_cache_set( $keyCache, $resultData, self::$_group, WP_PEPVN_CACHE_TIMEOUT_NORMAL );
				}
				
			}
			
		}
		
		if(null === $resultData) {
			
			$resultData = PepVN_Data::$cacheFileByTagObject->get_cache($keyCache);
			
			if(null !== $resultData) {
				//self::$_tempData[$keyCache] = Utils::gzVar($resultData);
				self::$_tempData[$keyCache] = $resultData;
				
				if($useWpCacheStatus) {
					wp_cache_set( $keyCache, $resultData, self::$_group, WP_PEPVN_CACHE_TIMEOUT_NORMAL );
				}
				
				if(false === $useCachePermanentStatus) {
					PepVN_Data::$cacheObject->set_cache($keyCache, $resultData);
				} else {
					PepVN_Data::$cachePermanentObject->set_cache($keyCache, $resultData);
				}
				
			}
		}
		
		return $resultData;
	}
	
	public static function set_cache($keyCache, $data, $useCachePermanentStatus = false, $useWpCacheStatus = false)
	{
		$keyCache = Utils::hashKey(array(
			self::$_key_salt
			, $keyCache
		));
		
		if(is_object($data)) {
			$data = clone $data;
		}
		
		//self::$_tempData[$keyCache] = Utils::gzVar($data);
		self::$_tempData[$keyCache] = $data;
		
		PepVN_Data::$cacheFileByTagObject->set_cache($keyCache, $data, self::get_current_cache_tags());
		
		if(false === $useCachePermanentStatus) {
			PepVN_Data::$cacheObject->set_cache($keyCache,$data);
		} else {
			PepVN_Data::$cachePermanentObject->set_cache($keyCache,$data);
		}
		
		if($useWpCacheStatus) {
			wp_cache_set( $keyCache, $data, self::$_group, WP_PEPVN_CACHE_TIMEOUT_NORMAL );
		}
		
		return true;
	}
	
	public static function get_current_cache_tags()
	{
		
		$cacheTags = array();
		
		$wpExtend = self::$_di->getShared('wpExtend');
		
		$cacheTags = $wpExtend->getCacheTagsForCurrentRequest();
		
		$cacheTags[] = 'tmpdtcfi';
		
		$cacheTags = array_values($cacheTags);
		$cacheTags = array_unique($cacheTags);
		
		return $cacheTags;
	}
	
}

