<?php 
//$_SERVER['SERVER_NAME'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : NULL;
//$_SERVER['HTTP_HOST'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : NULL;


defined('WP_PEPVN_CHMOD') || define('WP_PEPVN_CHMOD', 0755);

if ( ! defined( 'WP_PEPVN_SITE_ID' ) ) {
	
	if(isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
		define( 'WP_PEPVN_SITE_DOMAIN',$_SERVER['HTTP_X_FORWARDED_SERVER']);
		define( 'WP_PEPVN_SITE_ID', md5(WP_PEPVN_NS_SHORT . $_SERVER['HTTP_X_FORWARDED_SERVER']));
	} else if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
		define( 'WP_PEPVN_SITE_DOMAIN',$_SERVER['HTTP_X_FORWARDED_HOST']);
		define( 'WP_PEPVN_SITE_ID', md5(WP_PEPVN_NS_SHORT . $_SERVER['HTTP_X_FORWARDED_HOST']) );
	} else if(isset($_SERVER['SERVER_NAME'])) {
		define( 'WP_PEPVN_SITE_DOMAIN',$_SERVER['SERVER_NAME']);
		define( 'WP_PEPVN_SITE_ID', md5(WP_PEPVN_NS_SHORT . $_SERVER['SERVER_NAME']) ); 
	} else if(isset($_SERVER['HTTP_HOST'])) {
		define( 'WP_PEPVN_SITE_DOMAIN',$_SERVER['HTTP_HOST']);
		define( 'WP_PEPVN_SITE_ID', md5(WP_PEPVN_NS_SHORT . $_SERVER['HTTP_HOST']) ); 
	}
}

if ( ! defined( 'WP_PEPVN_BLOG_ID' ) ) { 
	$tmp = get_current_blog_id();
	$tmp = (int)$tmp;
	$tmp = abs($tmp);
	define( 'WP_PEPVN_BLOG_ID', $tmp);
}

/*
* SALT use for encode data but not sustainably, so don't use for make ID in database (use WP_PEPVN_SITE_ID instead)
*/

if ( ! defined( 'WP_PEPVN_SITE_SALT' ) ) { 
	
    $tmp = array();
    $tmp[] = wppepvn_get_site_salt();
    $tmp[] = WP_PEPVN_NS_SHORT;
    $tmp[] = WP_PEPVN_SITE_ID;
	$tmp[] = WP_PEPVN_BLOG_ID;
    
    $tmp = md5(serialize($tmp));
    
    define( 'WP_PEPVN_SITE_SALT', $tmp); unset($tmp);
}

defined('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_DIR') || define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_DIR', WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes' . DIRECTORY_SEPARATOR . 'storages' . DIRECTORY_SEPARATOR);
defined('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR') || define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR', WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_DIR . 'cache' . DIRECTORY_SEPARATOR );
defined('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_GENERAL_DIR') || define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_GENERAL_DIR', WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_DIR . 'general' . DIRECTORY_SEPARATOR );

//@WP_UPLOADS_PEPVN_DIR : Store images processed by this plugin
$tmp = wp_upload_dir();

defined('WP_PEPVN_SITE_UPLOADS_DIR') || define('WP_PEPVN_SITE_UPLOADS_DIR', $tmp['basedir'] . DIRECTORY_SEPARATOR);
defined('WP_PEPVN_SITE_UPLOADS_URL') || define('WP_PEPVN_SITE_UPLOADS_URL', $tmp['baseurl'] . '/');

defined('WP_UPLOADS_PEPVN_DIR') || define('WP_UPLOADS_PEPVN_DIR', WP_PEPVN_SITE_UPLOADS_DIR . 'pep-vn' . DIRECTORY_SEPARATOR);
defined('WP_UPLOADS_PEPVN_URL') || define('WP_UPLOADS_PEPVN_URL', WP_PEPVN_SITE_UPLOADS_URL . 'pep-vn/');

//@WP_UPLOADS_PEPVN_DIR : Store cache request uri, static files.
defined('WP_CONTENT_PEPVN_DIR') || define('WP_CONTENT_PEPVN_DIR', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'pep-vn' . DIRECTORY_SEPARATOR);
defined('WP_CONTENT_PEPVN_URL') || define('WP_CONTENT_PEPVN_URL', WP_CONTENT_URL . '/pep-vn/');

defined('WP_PEPVN_CACHE_TIMEOUT_NORMAL') || define('WP_PEPVN_CACHE_TIMEOUT_NORMAL', 86400);
defined('WP_PEPVN_CACHE_PREFIX') || define('WP_PEPVN_CACHE_PREFIX', md5(WP_PEPVN_SITE_SALT));
defined('WP_PEPVN_CACHE_TRIGGER_CLEAR_KEY') || define('WP_PEPVN_CACHE_TRIGGER_CLEAR_KEY', md5(WP_PEPVN_SITE_SALT . 'CACHE_TRIGGER_CLEAR'));
defined('WP_PEPVN_KEY_DATA_REQUEST') || define('WP_PEPVN_KEY_DATA_REQUEST', 'wppepvndtecv');