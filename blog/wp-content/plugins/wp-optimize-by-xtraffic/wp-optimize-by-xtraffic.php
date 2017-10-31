<?php
/*
Plugin Name: WP Optimize By xTraffic
Version: 5.1.6
Plugin URI: http://blog-xtraffic.pep.vn/wordpress-optimize-by-xtraffic/
Author: xTraffic
Author URI: http://blog-xtraffic.pep.vn/
Description: WP Optimize By xTraffic provides automatically optimize your WordPress site.
*/

// If this file is called directly, abort.
if (!defined( 'WPINC' )) {
	header('Status: 403 Forbidden',true,403);
	header('HTTP/1.1 403 Forbidden',true,403);
	die('This file is called directly. You should not try this because it has been blocked!');
	exit();
}

if (
	(defined('WP_INSTALLING') && WP_INSTALLING)
	|| (defined('WP_SETUP_CONFIG') && WP_SETUP_CONFIG)
) {
    return;
}

//Check if plugin is loaded
if ( !defined( 'WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_INIT_STATUS' ) ) {
    define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_INIT_STATUS', true);
	
	global $wpOptimizeByxTraffic;
	$wpOptimizeByxTraffic = false;
	
	define('WP_PEPVN_NS_SHORT', 'wppepvn');	// IMPORTANT : Never change this because it use for many key database. Only this plugin define this constant.
    define('WP_PEPVN_NAMESPACE', 'WpPepVN');
	
    define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION', '5.1.6');
    define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME', 'WP Optimize By xTraffic');
    define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAMESPACE', 'WPOptimizeByxTraffic');
    define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG', 'wp-optimize-by-xtraffic');
	define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT', 'wpopxtf');
	
    
    //Check version PHP
    if(defined('PHP_VERSION') && version_compare(PHP_VERSION, '5.3.0', '>=')) {
        $tmp = microtime(true);
		$tmp = (float)$tmp;
		define('WP_PEPVN_MICROTIME_START', $tmp);
		
		ob_implicit_flush(false);	//TRUE to turn implicit flushing on, FALSE otherwise.
		ob_start();
		
        define('WP_PEPVN_ENV', 'production' );	//production:dev
		
		if('dev' === WP_PEPVN_ENV) {
			define('WP_PEPVN_DEBUG', true);
		} else {
			define('WP_PEPVN_DEBUG', false);
		}
		
		define('WP_PEPVN_CONFIG_KEY', 'WP_OPTIMIZE_BY_XTRAFFIC_CONFIGS');
        
		define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_FILE', __FILE__ );
        define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_DIR', plugin_dir_path( WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_FILE ) );
        define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI', plugins_url( WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG ) . '/' );
        define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_LIBS_DIR', WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_DIR . 'libs' . DIRECTORY_SEPARATOR);
        define('WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR', WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_DIR . 'Application' . DIRECTORY_SEPARATOR);
        
        /* 
        * PRIORITY
        Used to specify the order in which the functions associated with a particular action are executed. 
        Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
        */
        
        define('WP_PEPVN_PRIORITY_FIRST', 9);
        define('WP_PEPVN_PRIORITY_LAST', 90000000);
		
        include_once(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'ApplicationBootstrap.php');
		
		global $wpOptimizeByxTraffic;
        
        $wpOptimizeByxTraffic = new \WPOptimizeByxTraffic\Application\ApplicationBootstrap();
        
        $wpOptimizeByxTraffic->init();
		
		register_activation_hook( __FILE__, array($wpOptimizeByxTraffic, 'wp_register_activation_hook') );
		
		function wp_ajax_wppepvn_preview_processed_image_action()
		{
			global $wpOptimizeByxTraffic;
			$optimizeImages = $wpOptimizeByxTraffic->di->getShared('optimizeImages');
			$optimizeImages->preview_processed_image_action();
		}
		add_action( 'wp_ajax_wppepvn_preview_processed_image_action', 'wp_ajax_wppepvn_preview_processed_image_action');
		
    } else {
        /*
		* If PHP version <= 5.3.2 then can't use this plugin
		*/
		
        if(is_admin()) {
			
            function wpOptimizeByxTraffic_admin_error_notice() {
                $class = 'error';
                $message = 'You need to use <b>PHP version <u>5.3.2</u> or higher</b> to use plugin "<i><b>'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME.'</b></i>"';
                echo '<div class="',$class,'"><p><b>'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME.'</b> : ',$message,'</p></div>'; 
            }
			
            add_action( 'admin_notices', 'wpOptimizeByxTraffic_admin_error_notice' ); 
        }
    }    
}
