<?php
namespace WPOptimizeByxTraffic\Application\Module\Backend\Controller;

use WPOptimizeByxTraffic\Application\Service\PluginManager
;

class DashboardController extends ControllerBase
{
    
	public function __construct() 
    {
		parent::__construct();
	}
	
	
	private function _dashboard_get_html_blocks_plugins()
	{
		$pluginManager = $this->di->getShared('pluginManager');
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$resultData = '';
	
		$arrayPlugins = array(
			'wp-optimize-speed-by-xtraffic' => array(
				'name' => 'WP Optimize Speed By xTraffic'
				, 'slug' => 'wp-optimize-speed-by-xtraffic'
				, 'wp_plugin_url' => 'https://wordpress.org/plugins/wp-optimize-speed-by-xtraffic/'
				, 'version' => '>=0'
				, 'check' => array(
					'variable_name' => 'wpOptimizeSpeedByxTraffic'
					,'constant_version_name' => 'WP_OPTIMIZE_SPEED_BY_XTRAFFIC_PLUGIN_VERSION'
				)
			)
		);
		
		$rsCheckPluginStatus = $pluginManager->checkPluginStatus(array(
			'plugins' => $arrayPlugins
		));
		
		$currentAdminUrl = $wpExtend->admin_url();
		
		foreach($arrayPlugins as $key1 => $val1) {
			$val1['file_path_key'] = $val1['slug'] . DIRECTORY_SEPARATOR . $val1['slug'].'.php';
			$val1['file_path'] = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $val1['file_path_key'];
			
			$val1['thumb_url'] = 'https://ps.w.org/'.$val1['slug'].'/assets/icon.svg';
			$val1['banner_url'] = 'https://ps.w.org/'.$val1['slug'].'/assets/banner-772x250.png';
			
			$arrayPlugins[$key1] = $val1;
		}
		
		$iNumber = 0;
		
		foreach($arrayPlugins as $key1 => $val1) {

			$get_plugin_info1 = $pluginManager->get_plugin_info(array(
				'slug' => $val1['slug']
				,'fields' => array(
					'short_description' => true
				)
			));
			
			$button['class'] = 'btn btn-primary';
			$button['text'] = '';
			$button['title'] = '';
			$button['href'] = false;
			$button['html'] = '';
			
			$faCircle['class'] = 'fa fa-circle-o';
			$faCircle['title'] = '';
			$faCircle['style'] = 'color:#999;';
			
			if(
				(PluginManager::ACTIVATED_SUCCESS_STATUS === $rsCheckPluginStatus[$key1]['status'])
				|| (PluginManager::VALID_SUCCESS_STATUS === $rsCheckPluginStatus[$key1]['status'])
			) {
				//installed & actived
				$button['class'] = 'btn btn-default';
				$button['text'] = 'Deactivate';
				$button['title'] = 'Click here to deactivate this plugin';
				
				$button['href'] = add_query_arg(array(
					'wppepvn-deactivate-plugin-key' => rawurlencode($val1['file_path_key'])
					,'wppepvn-deactivate-plugin-name' => rawurlencode($val1['name'])
					,'wppepvn-deactivate-plugin-via' => rawurlencode(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG)
					,'page' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT . '_dashboard'
				), $currentAdminUrl.'admin.php');
				
				$faCircle['class'] = 'fa fa-circle';
				$faCircle['title'] = 'Actived';
				$faCircle['style'] = 'color:green;';
				
			} else if(
				(PluginManager::INSTALLED_SUCCESS_STATUS === $rsCheckPluginStatus[$key1]['status'])
				|| (PluginManager::ACTIVATED_ERROR_STATUS === $rsCheckPluginStatus[$key1]['status'])
			) {
				//installed but not active
				
				$button['class'] = 'btn btn-primary';
				$button['text'] = 'Activate';
				$button['title'] = 'Click here to activate this plugin';
				
				
				$button['href'] = add_query_arg(array(
					'wppepvn-active-plugin-key' => rawurlencode($val1['file_path_key'])
					,'wppepvn-active-plugin-name' => rawurlencode($val1['name'])
					,'wppepvn-active-plugin-via' => rawurlencode(WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG)
					,'page' => WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NS_SHORT . '_dashboard'
				), $currentAdminUrl.'admin.php');
				
			} else if(PluginManager::INSTALLED_ERROR_STATUS === $rsCheckPluginStatus[$key1]['status']) {
				//not_installed
				
				$button['class'] = 'btn btn-primary';
				$button['text'] = 'Install';
				$button['title'] = 'Click here to install this plugin';
				
				
				$install_plugin_install_status = $pluginManager->install_plugin_install_status(array(
					'slug' => $val1['slug']
					,'fields' => array()
				));
				
				$button['href'] = $install_plugin_install_status['url'];
			}
			
			$button['html'] = '<a href="'.($button['href'] ? $button['href'] : '#').'" class="'.$button['class'].'" title="'.$button['title'].'" role="button">'.$button['text'].'</a>';
			
			
			$resultData .= '
<div class="col-sm-6 col-md-6 wppepvn-plugins-item">
	<div class="thumbnail">
		<a href="'.$val1['wp_plugin_url'].'" target="_blank"><img src="'.$val1['banner_url'].'" alt="'.$val1['name'].'" /></a>
		<div class="caption">
			<h3 class="wppepvn-plugins-title"><a href="'.$val1['wp_plugin_url'].'" target="_blank">'.$val1['name'].'</a></h3>
			<p class="wppepvn-plugins-desc">'.$get_plugin_info1->short_description.'...<a href="'.$val1['wp_plugin_url'].'" target="_blank">more</a></p>
			<p class="wppepvn-plugins-buttons">
				<a href="#" class="btn btn-default disabled">
					<span class="'.$faCircle['class'].'" title="'.$faCircle['title'].'" style="'.$faCircle['style'].'" ></span> v'.$get_plugin_info1->version.'
				</a> '.$button['html'].'</p>
		</div>
	</div>
</div>
	';
			
		}
		
		return $resultData;
		
	}
	
    
	public function indexAction() 
    {
		
		$pluginManager = $this->di->getShared('pluginManager');
		$pluginManager->checkActionManagePlugins();
		
		$this->view->htmlBlocksPlugins = $this->_dashboard_get_html_blocks_plugins();
	}
	
}