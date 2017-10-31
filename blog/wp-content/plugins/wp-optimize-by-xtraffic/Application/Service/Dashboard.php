<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WpPepVN\Utils
	,WpPepVN\DependencyInjection
	,WpPepVN\System
	,WPOptimizeByxTraffic\Application\Service\PepVN_Data
;

class Dashboard
{
	const OPTION_NAME = 'dashboard';
	
	protected static $_tempData = array();
	
	public $di;
	
    public function __construct(DependencyInjection $di) 
    {
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		
		$this->di = $di;
		
		$hook = $this->di->getShared('hook');
		
		$hook->add_action('activated_plugin',array($this,'on_activated_any_plugin'),$priorityLast);
		$hook->add_action('deactivated_plugin',array($this,'on_deactivated_any_plugin'),$priorityLast);
		
	}
    
	public function initFrontend() 
    {
        
	}
	
	
	public function initBackend() 
    {
		
	}
	
	public static function getDefaultOption()
	{
		return array(
			'last_time_plugin_activation' => 0
		);
	}
	
	public static function getOption($cache_status = true)
	{
	
		return WpOptions::get_option(self::OPTION_NAME,self::getDefaultOption(),array(
			'cache_status' => $cache_status
		));
	}
	
	public static function updateOption($data)
	{
		return WpOptions::update_option(self::OPTION_NAME,$data);
	}
	
	public function on_plugin_activation()
	{
		
	}
	
	public function on_this_plugin_activation()
	{
		self::updateOption(array(
			'last_time_plugin_activation' => PepVN_Data::$defaultParams['requestTime']
		));
		
		$this->set_server_configs();
	}
	
	public function on_activated_any_plugin($params)
	{
		$this->set_server_configs();
	}
	
	public function on_deactivated_any_plugin($params)
	{
		$this->set_server_configs();
	}
	
	public function set_server_configs()
	{
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$siteWpRootPath = $wpExtend->getABSPATH();
		
		$options = self::getOption();
		
		$pluginsSlugsNotAllowWebAccess = array();
		
		$arrSlugsConstKeys = array(
			'WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG'
			,'WP_OPTIMIZE_SPEED_BY_XTRAFFIC_PLUGIN_SLUG'
		);
		
		foreach($arrSlugsConstKeys as $key1 => $value1) {
			if(defined($value1)) {
				$pluginsSlugsNotAllowWebAccess[] = constant($value1);
			}
		}
		
		$pluginsPathNotAllowWebAccess = array(
			'Application','includes','libs'
		);
		
		$pluginsSlugsNotAllowWebAccess = array_unique($pluginsSlugsNotAllowWebAccess);
		$pluginsPathNotAllowWebAccess = array_unique($pluginsPathNotAllowWebAccess);
		
		$webServerSoftwareName = System::getWebServerSoftwareName();
		
		if('apache' === $webServerSoftwareName) {
			
			$myHtaccessConfig = 
'
Options -Indexes
';
			
			$setServerConfigsOptions = array(
				'ROOT_PATH' => $siteWpRootPath
				,'CONFIG_KEY' => WP_PEPVN_CONFIG_KEY.'_'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG
				,'htaccess' => $myHtaccessConfig
			);
			
			System::setServerConfigs($setServerConfigsOptions);
			unset($setServerConfigsOptions);
			
		} else if('nginx' === $webServerSoftwareName) {
			
			$myConfigContent = 
'
# Deny access to any files with a .php extension in the uploads directory
# Works in sub-directory installs and also in multisite network
# Keep logging the requests to parse later (or to pass to firewall utilities such as fail2ban)
location ~* /(?:uploads)/.*\.php$ {
	deny all;
	return 403;
}

# Deny all attempts to access hidden files such as .htaccess, .htpasswd, .DS_Store (Mac).
# Keep logging the requests to parse later (or to pass to firewall utilities such as fail2ban)
location ~* /\. {
	deny all;
	return 403;
}

location ~* /xtraffic-nginx\.conf {
   deny all;
   return 403;
}

location ~* /('.implode('|',$pluginsSlugsNotAllowWebAccess).')/('.implode('|',$pluginsPathNotAllowWebAccess).')/ {
   deny all;
   return 403;
}

';
			
			System::setServerConfigs(array(
				'ROOT_PATH' => $siteWpRootPath
				,'CONFIG_KEY' => WP_PEPVN_CONFIG_KEY.'_'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG
				,'nginx' => $myConfigContent
			));
			
			unset($myConfigContent);
			
		}
		
	}
	
}
