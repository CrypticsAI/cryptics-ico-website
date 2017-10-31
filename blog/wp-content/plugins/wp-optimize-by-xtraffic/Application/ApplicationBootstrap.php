<?php
namespace WPOptimizeByxTraffic\Application;

use WpPepVN\DependencyInjection\FactoryDefault as DIFactoryDefault
	,WpPepVN\Utils
	,WpPepVN\System
;

include_once(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_LIBS_DIR . 'WpPepVN' . DIRECTORY_SEPARATOR . 'Mvc' . DIRECTORY_SEPARATOR . 'Application.php');

class ApplicationBootstrap extends \WpPepVN\Mvc\Application
{
    //@SLUG : Slug of this plugin
	const SLUG = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
    
    //@VERSION : Version of this plugin
    const VERSION = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_VERSION;
    
    
    /*
        @wpIsAdmin : boolean
        This Conditional Tag checks if the Dashboard or the administration panel is attempting to be displayed. 
        It should not be used as a means to verify whether the current user has permission to view the Dashboard or the administration panel (try current_user_can() instead). 
        This is a boolean function that will return true if the URL being accessed is in the admin section, or false for a front-end page.
        
        + is_admin() will return false when trying to access wp-login.php.
        + is_admin() will return true when trying to make an ajax request.
        + is_admin() will return true for calls to load-scripts.php and load-styles.php.
        + is_admin() is not intended to be used for security checks. It will return true whenever the current URL is for a page on the admin side of WordPress. 
            It does not check if the user is logged in, nor if the user even has access to the page being requested. 
            It is a convenience function for plugins and themes to use for various purposes, but it is not suitable for validating secured requests.
        
    */
	
	private $_noticesStore = array();
	
	public $di = false;
	
	public $hook = false;
	
	protected $_defaultModuleName = null;
	
	protected $_isPluginActivationStatus = false;
	
	public $initialized = array();
	
	public $configs = array();
	
    public function __construct() 
    {
		parent::__construct();
	}
    
	public function init() 
    {
        
        $this->registerLoaderDirs(array(
            WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_LIBS_DIR
        ));
        
        $this->registerNamespaces(array(
            'WPOptimizeByxTraffic\\Application\\' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR
			,'WpPepVN\\' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_LIBS_DIR . 'WpPepVN' . DIRECTORY_SEPARATOR
        ));
        
		parent::init();
		
		include_once(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'includes' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'init.php');
		
        include_once(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'configs' . DIRECTORY_SEPARATOR . 'constant.php');
        
        $di = new DIFactoryDefault();
        
        $config = include_once(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'configs' . DIRECTORY_SEPARATOR . 'config.php');
        
        $config = new \WpPepVN\Config($config);
        
        include_once(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_APPLICATION_DIR . 'configs' . DIRECTORY_SEPARATOR . 'service.php');
		
		if(WP_PEPVN_DEBUG) {
			set_error_handler('wppepvn_debug_error_handler', E_ALL);
		}
		
		$this->hook = $di->getShared('hook');
		
        $this->di = $di;
		
		$session = $this->di->getShared('session');
		
		$this->_checkSystemRequirements();
		
		if('success' === $this->initStatus()) {
			
			add_action('init', array($this,'wp_action_init'), WP_PEPVN_PRIORITY_FIRST);
			
			add_action('wp_loaded', array($this,'wp_action_wp_loaded'), WP_PEPVN_PRIORITY_FIRST);
			
			add_action('wp', array($this,'wp_action_wp'), WP_PEPVN_PRIORITY_LAST);
			
			add_action('send_headers', array($this,'wp_action_send_headers'), WP_PEPVN_PRIORITY_LAST);
			
			add_action('the_post', array($this,'wp_action_the_post'), WP_PEPVN_PRIORITY_FIRST, 1);
			
			add_action('wp_footer', array($this,'wp_action_wp_footer'), WP_PEPVN_PRIORITY_LAST);
			
			add_action('shutdown', array($this,'wp_action_shutdown'), WP_PEPVN_PRIORITY_LAST);
			
		}
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if($wpExtend->is_admin()) {
			add_action('admin_notices', array($this, 'wp_action_admin_notices') );
			add_action('network_admin_notices', array($this, 'wp_action_admin_notices') );
		}
		
	}
	
	private function _checkSystemRequirements()
    {
		
		//http://php.net/manual/en/mbstring.setup.php
		if(
			!function_exists('mb_strtolower')
			|| !function_exists('mb_convert_case')
		) {
			$message = sprintf('Plugin "<b>%s</b>" requires "<b><u>%s</u></b>" on the system to be used. Would you please contact your service provider or system administrator to install and activate this feature. <a href="%s" target="_blank">Details about "<b><u><i>%s</i></u></b>" here.</a>',
				WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME
				,'Multibyte String (PHP)'
				,'http://php.net/manual/en/book.mbstring.php'
				,'Multibyte String (PHP)'
			);
			
			$this->_noticesStore[] = array(
				'text' => $message
				, 'type' => 'error'
			);
		}
		
		if(
			!function_exists('mcrypt_encrypt')
			|| !function_exists('mcrypt_get_iv_size')
			|| !function_exists('mcrypt_decrypt')
		) {
			$message = sprintf('Plugin "<b>%s</b>" requires "<b><u>%s</u></b>" on the system to be used. Would you please contact your service provider or system administrator to install and activate this feature. <a href="%s" target="_blank">Details about "<b><u><i>%s</i></u></b>" here.</a>',
				WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME
				,'Mcrypt library (PHP)'
				,'http://php.net/manual/en/book.mcrypt.php'
				,'Mcrypt library (PHP)'
			);
			
			$this->_noticesStore[] = array(
				'text' => $message
				, 'type' => 'error'
			);
		}
		
		if(
			!function_exists('json_encode')
			|| !function_exists('json_decode')
		) {
			$message = sprintf('Plugin "<b>%s</b>" requires "<b><u>%s</u></b>" on the system to be used. Would you please contact your service provider or system administrator to install and activate this feature. <a href="%s" target="_blank">Details about "<b><u><i>%s</i></u></b>" here.</a>',
				WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME
				,'PHP JSON extension'
				,'http://php.net/manual/en/book.json.php'
				,'PHP JSON extension'
			);
			
			$this->_noticesStore[] = array(
				'text' => $message
				, 'type' => 'error'
			);
		}
		
		if(
			!function_exists('simplexml_load_string')
			|| !class_exists('\SimpleXMLElement')
		) {
			$message = sprintf('Plugin "<b>%s</b>" requires "<b><u>%s</u></b>" on the system to be used. Would you please contact your service provider or system administrator to install and activate this feature. <a href="%s" target="_blank">Details about "<b><u><i>%s</i></u></b>" here.</a>',
				WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME
				,'PHP SimpleXML extension'
				,'http://php.net/manual/en/book.simplexml.php'
				,'PHP SimpleXML extension'
			);
			
			$this->_noticesStore[] = array(
				'text' => $message
				, 'type' => 'error'
			);
		}
	}
	
	public function initStatus()
    {
		foreach($this->_noticesStore as $key => $value) {
			if('error' === $value['type']) {
				return 'error';
			}
		}
		
		return 'success';
	}
    
    /*
    * @wp_action_init_first : add_action@init : load order : 1 (first)
    Runs after WordPress has finished loading but before any headers are sent. Useful for intercepting $_GET or $_POST triggers.
    Typically used by plugins to initialize. The current user is already authenticated by this time.
    Fires after WordPress has finished loading but before any headers are sent.
    Most of WP is loaded at this stage, and the user is authenticated. WP continues to load on the init hook that follows (e.g. widgets), and many plugins instantiate themselves on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
    init is useful for intercepting $_GET or $_POST triggers.
    load_plugin_textdomain calls should be made during init, otherwise users cannot hook into it.
    If you wish to plug an action once WP is loaded, use the wp_loaded hook.         
    */
    public function wp_action_init() 
    {
		$session = $this->di->getShared('session');
		$session->start();
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		include_once WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
		
		System::mkdir(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_STORAGES_CACHE_GENERAL_DIR);
		
		/*
		//Begin schedule events (wp cron jobs). Run every 1 minutes
		add_filter( 'cron_schedules', array($this, 'action_cron_schedules_add_every_minute') );
		
		if(!wp_get_schedule( 'wppepvn_cronjob', array())) {
			if(!wp_next_scheduled( 'wppepvn_cronjob', array())) {
				$current_time = $wpExtend->current_time( 'timestamp' );
				
				wp_schedule_single_event( 
					//((ceil($current_time/60) * 60) + 60)
					$current_time
					, 'wppepvn_every_minute'
					, 'wppepvn_cronjob'
					, array() 
				);
				
			}
		}
		*/
		
		if($wpExtend->is_admin()) {
			if($wpExtend->isCurrentUserCanManagePlugin()) {
				$this->_defaultModuleName = 'Backend';
				$module = new \WPOptimizeByxTraffic\Application\Module\Backend\Module();
				$module->init($this->di);
			}
        } else {
			$this->_defaultModuleName = 'Frontend';
            $module = new \WPOptimizeByxTraffic\Application\Module\Frontend\Module();
            $module->init($this->di);
		}
		
		$this->initialized['wp_init'] = true;

    }
    
	/*
	*	@wp_action_wp_loaded
	*	This action hook is fired once WordPress, all plugins, and the theme are fully loaded and instantiated.
	*/
	public function wp_action_wp_loaded() 
    {
		add_action( 'wp_ajax_nopriv_wppepvn_ajax_action', array($this,'ajax_action'), WP_PEPVN_PRIORITY_LAST);
		add_action( 'wp_ajax_wppepvn_ajax_action', array($this,'ajax_action'), WP_PEPVN_PRIORITY_LAST);
		
		add_action( 'wp_ajax_nopriv_wppepvn_cronjob', array($this,'cronjob_action'), WP_PEPVN_PRIORITY_LAST);
		add_action( 'wp_ajax_wppepvn_cronjob', array($this,'cronjob_action'), WP_PEPVN_PRIORITY_LAST);
		
		add_action( 'wp_ajax_nopriv_wppepvn_background_cronjob', array($this,'background_cronjob_action'), WP_PEPVN_PRIORITY_LAST);
		add_action( 'wp_ajax_wppepvn_background_cronjob', array($this,'background_cronjob_action'), WP_PEPVN_PRIORITY_LAST);
		
		if(
			is_admin() 
			|| wppepvn_is_ajax() 
			|| defined('DOING_CRON') 
			|| ('GET' !== wppepvn_request_method()) 
			|| wppepvn_is_loginpage()
			|| wppepvn_is_preview()
			|| wppepvn_get_current_user_hash_via_cookie()
		) {
			wppepvn_http_header_template('no-cache');
			defined('WPPEPVN_NOCACHE') || define('WPPEPVN_NOCACHE', true);
		}
		
		$this->initialized['wp_loaded'] = true;
	}
	
	/*
		This action hook is used to add additional headers to the outgoing HTTP response.
	*/
	public function wp_action_send_headers() 
    {
		$wpExtend = $this->di->getShared('wpExtend');
		
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('wp_send_headers')) {
			$hook->do_action('wp_send_headers');
		}
		
		wppepvn_http_headers(array(),'flush');
		
		if(defined('WPPEPVN_NOCACHE')) {
			nocache_headers();
		}
		
		$this->initialized['send_headers'] = true;
		
	}
	
	/*
		Executes after the query has been parsed and post(s) loaded, but before any template execution
			, inside the main WordPress function wp(). 
		Useful if you need to have access to post data but can't use templates for output. 
		Action function argument: WP object ($wp) by reference.
		Run on front-end only, not run in backend
	*/
	public function wp_action_wp() 
    {
		$this->initialized['wp'] = true;
	}
	
	/*
		The 'the_post' action hook allows developers to modify the post object immediately after being queried and setup.

		The post object is passed to this hook by reference so there is no need to return a value.
	*/
	public function wp_action_the_post($post_object) 
    {
		$this->initialized['the_post'] = true;
	}
	
	
	/*
		The wp_footer action is triggered near the </body> tag of the user's template by the wp_footer() function.
		Although this is theme-dependent, it is one of the most essential theme hooks, so it is fairly widely supported.
	*/
	public function wp_action_wp_footer() 
    {
		
		wppepvn_http_headers(array(),'flush');
		
		$response = $this->di->getShared('response');
		
		if(!$response->isSent()) {
			$response->send();
		}
		
		$this->initialized['wp_footer'] = true;
	}
	
	/*
	*	@wp_action_shutdown
	*	This action hook is fired once WordPress, all plugins, and the theme are fully loaded and instantiated.
	*/
	
	public function wp_action_shutdown() 
    {
		$this->wp_plugin_activation_hook();
		
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('wp_shutdown')) {
			$hook->do_action('wp_shutdown');
		}
		
		$wpActionManager = $this->di->getShared('wpActionManager');
		$wpActionManager->wp_action_shutdown();
		
		$this->initialized['shutdown'] = true;
	}
	
	public function action_cron_schedules_add_every_minute( $schedules ) 
	{
		// Adds once weekly to the existing schedules.
		$schedules['wppepvn_every_minute'] = array(
			'interval' => 60
			, 'display' => __( 'Every Minute' )
		);
		
		return $schedules;
	}
	
	public function ajax_action() 
    {
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if($wpExtend->isWpAjax()) {
			
			$ajaxHandle = $this->di->getShared('ajaxHandle');
			$ajaxHandle->run();
			
			wp_die();
			exit();
		}
	}
	
	public function cronjob_action() 
    {
		echo '/* <h2>Cronjob Registered : '.time().' - '.date('Y-m-d H:i:s').'</h2> */' . PHP_EOL;
		
		$backgroundQueueJobsManager = $this->di->getShared('backgroundQueueJobsManager');
		$backgroundQueueJobsManager->registerRequest();
		
		wp_die();
		exit();
	
	}
	
	public function background_cronjob_action() 
    {
		echo '/* <h2>Background Cronjob Begin : '.time().' - '.date('Y-m-d H:i:s').'</h2> */' . PHP_EOL;
		
		$backgroundQueueJobsManager = $this->di->getShared('backgroundQueueJobsManager');
		$backgroundQueueJobsManager->receive();
		
		$cronjob = $this->di->getShared('cronjob');
		$cronjob->run();
		
		echo '/* <h2>Background Cronjob End : '.time().' - '.date('Y-m-d H:i:s').'</h2> */';
		
		wp_die();
		exit();
	}
	
	
	public function wp_plugin_activation_hook() 
    {
		$sessionKey = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-plugin-activation-status';
		
		$session = $this->di->getShared('session');
		
		if(
			($session->has($sessionKey) && ('y' === $session->get($sessionKey)))
			|| $this->_isPluginActivationStatus
		) {
		
			$session->set($sessionKey, 'n');
			$session->remove($sessionKey);
			
			$this->_isPluginActivationStatus = true;
			
			$dashboard = $this->di->getShared('dashboard');
			$dashboard->on_this_plugin_activation();
			$dashboard->on_plugin_activation();
			
			$headerFooter = $this->di->getShared('headerFooter');
			$headerFooter->migrateOptions();
			
			$optimizeLinks = $this->di->getShared('optimizeLinks');
			$optimizeLinks->migrateOptions();
			
			$optimizeImages = $this->di->getShared('optimizeImages');
			$optimizeImages->on_plugin_activation();
			
			$analyzeText = $this->di->getShared('analyzeText');
			$analyzeText->add_db_index();
			
			$cacheManager = $this->di->getShared('cacheManager');
			$cacheManager->clean_cache(',all,');
			
			wp_clear_scheduled_hook('wppepvn_cronjob', array());
			
			wp_clear_scheduled_hook('wppepvn_cronjob');
			
		}
		
	}
	
	public function wp_register_activation_hook() 
    {
		
		$session = $this->di->getShared('session');
		
		$sessionKey = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-plugin-activation-status';
		$session->set($sessionKey, 'y');
		
		$this->_isPluginActivationStatus = true;
		
	}
	
	
	public function wp_action_admin_notices() 
    {
		if(!$this->_isPluginActivationStatus) {
			
			foreach($this->_noticesStore as $keyOne => $valueOne) {
				
				unset($this->_noticesStore[$keyOne]);
				
				$class = '';
				
				if('success' === $valueOne['type']) {
					$class = 'updated';
				} else if('info' === $valueOne['type']) {
					$class = 'updated';
				} else if('warning' === $valueOne['type']) {
					$class = 'update-nag';
				} else if('error' === $valueOne['type']) {
					$class = 'error';
				}
				
				echo '<div class="'.$class.'" style="padding: 1%;">'.$valueOne['text'].'</div>';
			}
			
		}
	}
	
}