<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
;

class PluginManager
{
	const INSTALLED_SUCCESS_STATUS = 1;
	const INSTALLED_ERROR_STATUS = -1;
	
	const ACTIVATED_SUCCESS_STATUS = 2;
	const ACTIVATED_ERROR_STATUS = -2;
	
	const VERSION_SUCCESS_STATUS = 3;
	const VERSION_ERROR_STATUS = -3;
	
	const VALID_SUCCESS_STATUS = 6;
	
	const GET_ACTIVE_PLUGIN_NS = 'wppepvn-active-plugin';
	
	public $di = false;
	
	public static $defaultParams = false;
	
	protected static $_tempData = array();
	
	protected $_pluginPathActived = array();
	
	protected $_currentPluginName = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME;
	protected $_currentPluginSlug = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG;
	
	public function __construct($di) 
	{
		$this->di = $di;
		
		self::setDefaultParams();
	}
	
	public static function setDefaultParams()
	{
		if(false === self::$defaultParams) {
			
			self::$defaultParams['status'] = true;
			
		}
	}
	
	public static function hashKey($data)
	{
		return hash('crc32b', md5(serialize($data)));
	}
	
	public static function init_require_functions()
	{
		if(
			!function_exists('install_plugin_install_status')
			|| !function_exists('plugins_api')
		) {
			require_once (ABSPATH . implode(DIRECTORY_SEPARATOR, array(
				'wp-admin'
				,'includes'
				,'plugin-install.php'
			)));
		}
		
		if(!function_exists('get_plugins')) {
			require_once (ABSPATH . implode(DIRECTORY_SEPARATOR, array(
				'wp-admin'
				,'includes'
				,'plugin.php'
			)));
		}
				
	}
	
	public static function activate_plugin( $plugin ) 
	{
		$current = get_option( 'active_plugins' );
		
		$plugin = plugin_basename( trim( $plugin ) );
		
		if ( !in_array( $plugin, $current ) ) {
			$current[] = $plugin;
			sort( $current );
			do_action( 'activate_plugin', trim( $plugin ) );
			update_option( 'active_plugins', $current );
			do_action( 'activate_' . trim( $plugin ) );
			do_action( 'activated_plugin', trim( $plugin) );
		}
		
		return null;
	}
	
	public static function deactivate_plugin( $plugin )
	{
		$current = get_option( 'active_plugins' );
		$plugin = plugin_basename( trim( $plugin ) );
		
		if ( in_array( $plugin, $current ) ) {
			$plugin = trim($plugin);
			
			foreach($current as $key1 => $value1) {
				if($plugin === $value1) {
					unset($current[$key1]);
				}
			}
			
			sort( $current );
			do_action( 'deactivate_plugin',  $plugin );
			update_option( 'active_plugins', $current );
			do_action( 'deactivate_' . $plugin );
			do_action( 'deactivated_plugin', $plugin );
			
		}
		
		return null;
	}

	
	//install_plugin_install_status($api, $loop = false) {
	public static function install_plugin_install_status($api, $loop = false)
	{
		$keyCache = self::hashKey(array('PluginManager_install_plugin_install_status', $api, $loop));
		
		if(!isset(self::$_tempData[$keyCache])) {
			
			self::$_tempData[$keyCache] = PepVN_Data::$cacheObject->get_cache($keyCache);
			
			if(null === self::$_tempData[$keyCache]) {
				
				self::init_require_functions();
				
				self::$_tempData[$keyCache] = install_plugin_install_status($api, $loop);
				
				PepVN_Data::$cacheObject->set_cache($keyCache, self::$_tempData[$keyCache]);
			}
		}
		
		return self::$_tempData[$keyCache];
	}
	
	
	public function get_plugin_info($input_args)
	{
        
		if(!isset($input_args['fields'])) {
			$input_args['fields'] = array();
		}
		
		$keyCache = self::hashKey(array('PluginManager_get_plugin_info', $input_args));
		
		if(!isset(self::$_tempData[$keyCache])) {
				
			self::$_tempData[$keyCache] = PepVN_Data::$cacheObject->get_cache($keyCache);
			
			if(null === self::$_tempData[$keyCache]) {
				
				$wpExtend = $this->di->getShared('wpExtend');
				
				self::init_require_functions();
				
				$fields = array(
					'short_description' => true,
					'screenshots' => false,
					'changelog' => false,
					'installation' => false,
					'description' => false
				);
				
				$fields = array_merge($fields, (array)$input_args['fields']);
				
				$args = array(
					'slug' => $input_args['slug'],
					'fields' => $fields
				);
				
				self::$_tempData[$keyCache] = $wpExtend->plugins_api('plugin_information', $args);
				
				PepVN_Data::$cacheObject->set_cache($keyCache, self::$_tempData[$keyCache]);
			}
		}
		
        return self::$_tempData[$keyCache];
		
    }
	
	
	
	/*
	$plugins_params = array(
		'plugins' => array(
			'wp-optimize-speed-by-xtraffic' => array(
				'name' => 'WP Optimize Speed By xTraffic'
				, 'slug' => 'wp-optimize-speed-by-xtraffic'
				, 'wp_plugin_url' => 'https://wordpress.org/plugins/wp-optimize-speed-by-xtraffic/'
				, 'check' => array(
					'variable_name' => 'wpOptimizeSpeedByxTraffic'
					,'constant_version_name' => 'WP_OPTIMIZE_SPEED_BY_XTRAFFIC_PLUGIN_VERSION'
				)
			)
		)
	);
	*/
	
	public function checkPluginStatus($plugins_params)
	{
		$keyCache = self::hashKey(array('checkPluginStatus',$plugins_params));
		
		if(!isset(self::$_tempData[$keyCache])) {
			
			$wpExtend = $this->di->getShared('wpExtend');
			
			$resultData = array();
			
			if($plugins_params) {
					
				foreach($plugins_params['plugins'] as $keyOne => $valueOne) {
					
					if(
						!isset($valueOne['check']['variable_name'])
						|| !isset($valueOne['check']['constant_version_name'])
					) {
						throw new \Exception('Require "variable_name" & "constant_version_name" to check required plugin dependency!');
					}
					
					$valueOne['file_path_key'] = $valueOne['slug']. DIRECTORY_SEPARATOR .$valueOne['slug'].'.php';
					$valueOne['file_path'] = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR .$valueOne['file_path_key'];
					
					$valueOne['version'] = trim($valueOne['version']);
					
					$slug = $valueOne['slug'];
					
					$variable_name = $valueOne['check']['variable_name'];
					$constant_version_name = $valueOne['check']['constant_version_name'];
					
					if(
						file_exists($valueOne['file_path'])
						&& is_file($valueOne['file_path'])
					) {
						$valueOne['status'] = self::INSTALLED_SUCCESS_STATUS;
						
						if(defined($constant_version_name)) {
							$valueOne['status'] = self::ACTIVATED_SUCCESS_STATUS;
							
							$constant_version_value = constant($constant_version_name);
							
							$version_compare_operator = '>=';
							$version_compare_value = false;
							
							preg_match('#^([^0-9\.]+)?([0-9\.]+)$#',$valueOne['version'],$matched1);
							
							if(isset($matched1[1]) && $matched1[1]) {
								$version_compare_operator = $matched1[1];
							}
							
							$version_compare_value = $matched1[2];
							
							if(version_compare($constant_version_value, $version_compare_value, $version_compare_operator)) {
								$valueOne['status'] = self::VERSION_SUCCESS_STATUS;
								global $$variable_name;
								if(isset($$variable_name) && ($$variable_name)) {
									$valueOne['status'] = self::VALID_SUCCESS_STATUS;
								} else {
									$valueOne['status'] = 0;
								}
							} else {
								$valueOne['status'] = self::VERSION_ERROR_STATUS;
							}
							
						} else {
							$valueOne['status'] = self::ACTIVATED_ERROR_STATUS;
						}
						
					} else {
						$valueOne['status'] = self::INSTALLED_ERROR_STATUS;
					}
					
					if($wpExtend->current_user_can('install_plugins')) {
						
						if(
							(self::INSTALLED_ERROR_STATUS === $valueOne['status'])	//not install
							|| (self::VERSION_ERROR_STATUS === $valueOne['status'])	//not right version
						) {
							$install_plugin_install_status = self::install_plugin_install_status(self::get_plugin_info(array(
								'slug' => $slug
								,'fields' => array()
							)), true);
							
							if(isset($install_plugin_install_status['url']) && $install_plugin_install_status['url']) {
								
								if((self::INSTALLED_ERROR_STATUS === $valueOne['status'])) {
									$valueOne['notice']['error'][] = 'Plugin "<u><b>'.$this->_currentPluginName.'</b></u>" requires the following plugin "<u><b>'.$valueOne['name'].'</b></u>"!<br />Please <a href="'.$install_plugin_install_status['url'].'"><u><b><i>click here to install this plugin</i></b></u></a>!';
								} else if((self::VERSION_ERROR_STATUS === $valueOne['status'])) {
									$valueOne['notice']['error'][] = 'Plugin "<u><b>'.$this->_currentPluginName.'</b></u>" requires the following plugin "<u><b>'.$valueOne['name'].'</b></u>" has version "<b><u>' . $version_compare_operator . ' ' . $version_compare_value . '</u></b>"!<br />Please <a href="'.$install_plugin_install_status['url'].'"><u><b><i>click here to update plugin "'.$valueOne['name'].'"</i></b></u></a>!';
								}
								
							}
							
						} else if(
							(self::ACTIVATED_ERROR_STATUS === $valueOne['status'])	//installed but not active
						) {
							
							$addNoticeStatus = true;
							
							if(isset($this->_pluginPathActived[$valueOne['file_path_key']])) {
								$addNoticeStatus = false;
							} else {
								$getActiveKey = self::GET_ACTIVE_PLUGIN_NS.'-key';
								if(isset($_GET[$getActiveKey]) && $_GET[$getActiveKey]) {
									$addNoticeStatus = false;
								}
							}
							
							if($addNoticeStatus) {
								if(is_ssl()) {
									$adminUrl = admin_url( '', 'https' );
								} else {
									$adminUrl = admin_url( '', 'http' );
								}
								
								$valueOne['notice']['error'][] = 'Plugin "<u><b>'.$this->_currentPluginName.'</b></u>" requires the following plugin "<u><b>'.$valueOne['name'].'</b></u>"!<br />Please <a href="'.add_query_arg(array(
									self::GET_ACTIVE_PLUGIN_NS.'-key' => rawurlencode($valueOne['file_path_key'])
									,self::GET_ACTIVE_PLUGIN_NS.'-name' => rawurlencode($valueOne['name'])
									,self::GET_ACTIVE_PLUGIN_NS.'-via' => rawurlencode($this->_currentPluginSlug)
								), $adminUrl.'plugins.php?').'"><u><b><i>click here to activate this plugin</i></b></u></a>!';
							}
							
						} else if(
							(self::VALID_SUCCESS_STATUS !== $valueOne['status'])	//installed but not active
						) {
							$valueOne['notice']['error'][] = 'Plugin "<u><b>'.$this->_currentPluginName.'</b></u>" requires the following plugin "<u><b>'.$valueOne['name'].'</b></u>"! But there is an unspecified error make this plugin can not run. Please <a href="https://www.facebook.com/wpoptimizebyxtraffic" target="_blank"><u><b><i>contact us here for assistance</i></b></u></a>!';
						}
					}
					
					if(isset($valueOne['notice']['error'])) {
						$valueOne['notice']['error'] = array_unique($valueOne['notice']['error']);
					}
					
					$resultData[$keyOne] = $valueOne;
				}
				
			}
			
			self::$_tempData[$keyCache] = $resultData;
		}
		
		return self::$_tempData[$keyCache];
	}
	
	public function checkActionManagePlugins()
	{
		$resultData = array();
		
		$pluginPathActived = array();
		$pluginPathDeactivated = array();
		
		$keyActiveKey = 'wppepvn-active-plugin-key';
		$keyActiveName = 'wppepvn-active-plugin-name';
		$keyActiveVia = 'wppepvn-active-plugin-via';
		
		$wpExtend = $this->di->getShared('wpExtend');
		$adminNotice = $this->di->getShared('adminNotice');
		
		if(
			isset($_GET[$keyActiveKey]) && $_GET[$keyActiveKey]
		) {
			if(
				isset($_GET[$keyActiveName]) && $_GET[$keyActiveName]
				&& isset($_GET[$keyActiveVia]) && $_GET[$keyActiveVia]
			) {
				if($this->_currentPluginSlug === $_GET[$keyActiveVia]) {
					
					if($wpExtend->is_admin()) {
						if($wpExtend->isCurrentUserCanManagePlugin()) {
							$this->activate_plugin($_GET[$keyActiveKey]);
							$pluginPathActived[$_GET[$keyActiveKey]] = $_GET[$keyActiveKey];
							$adminNotice->add_notice(
								'Plugin "<u><b>'.$_GET[$keyActiveName].'</b></u>" activated successfully!'
								,'success'
							);
						}
						
					}
				}
			}
		}
		
		$keyDeactiveKey = 'wppepvn-deactivate-plugin-key';
		$keyDeactiveName = 'wppepvn-deactivate-plugin-name';
		$keyDeactiveVia = 'wppepvn-deactivate-plugin-via';
		
		if(
			isset($_GET[$keyDeactiveKey]) && $_GET[$keyDeactiveKey]
		) {
			if(
				isset($_GET[$keyDeactiveName]) && $_GET[$keyDeactiveName]
				&& isset($_GET[$keyDeactiveVia]) && $_GET[$keyDeactiveVia]
			) {
				
				if($this->_currentPluginSlug === $_GET[$keyDeactiveVia]) {
					
					if($wpExtend->is_admin()) {
						if($wpExtend->isCurrentUserCanManagePlugin()) {
							$this->deactivate_plugin($_GET[$keyDeactiveKey]);
							$pluginPathDeactivated[$_GET[$keyDeactiveKey]] = $_GET[$keyDeactiveKey];
							
							$adminNotice->add_notice(
								'Plugin "<u><b>'.$_GET[$keyDeactiveName].'</b></u>" deactivated successfully!'
								,'success'
							);
							
						}
						
					}
				}
			}
		}
		
		if(!empty($pluginPathActived) || !empty($pluginPathDeactivated)) {
			wp_cache_delete('alloptions', 'options');
			
			$urlRedirect = $wpExtend->admin_url().'admin.php?page='.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT.'_dashboard';
			ob_clean();
			echo '<script>window.location = "',$urlRedirect,'";</script>';
			ob_end_flush();
			exit();
		}
		
		return $resultData;
	}
	
	
}

PluginManager::setDefaultParams();