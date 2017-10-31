<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Model\WpOptions
	,WPOptimizeByxTraffic\Application\Service\Cronjob as ServiceCronjob
	,WpPepVN\Utils
	,WpPepVN\Hash
	,WpPepVN\DependencyInjection
;

class AjaxHandle
{
	
	protected static $_tempData = array();
	
	protected static $_dataSendForJS = array();
	
	public $di;
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		$priorityLast = WP_PEPVN_PRIORITY_LAST;
		$priorityFirst = WP_PEPVN_PRIORITY_FIRST;
		
		//add_action('wp_footer',  array($this, 'action_wp_footer'), ($priorityFirst - 1));
		add_action('wp_print_footer_scripts',  array($this, 'action_wp_print_footer_scripts'), $priorityFirst);
		
	}
	
	public function action_wp_print_footer_scripts() 
	{
		$wpExtend = $this->di->getShared('wpExtend');
		
		$typeOfCurrentPage = $wpExtend->getTypeOfPage();
		
		if(self::$_dataSendForJS && !empty(self::$_dataSendForJS)) {
			
			echo '<script language="javascript" type="text/javascript" xtraffic-exclude >
if((typeof(window.wppepvn_dtns) === "undefined") || !window.wppepvn_dtns) {window.wppepvn_dtns = [];}
window.wppepvn_dtns.push("'.base64_encode(json_encode(self::$_dataSendForJS)).'");
</script>';

		}
		
		self::$_dataSendForJS = array();
		
	}
	
	public function addDataSendForJS($dataSend) 
    {
		self::$_dataSendForJS = Utils::mergeArrays(array(
			self::$_dataSendForJS
			,$dataSend
		));
	}
    
	public function run() 
    {
		ob_clean();
		header('OK', true, 200);
		
		$wpExtend = $this->di->getShared('wpExtend');
		$hook = $this->di->getShared('hook');
		
		$resultData = array(
			'status' => 1
		);
		
		$dataSent = PepVN_Data::getDataSent();
		
		if($dataSent && isset($dataSent['localTimeSent']) && $dataSent['localTimeSent']) {
			
			if(isset($dataSent['preview_optimize_traffic_modules']) && $dataSent['preview_optimize_traffic_modules']) {
				
				if($wpExtend->is_admin()) {
					if($wpExtend->isCurrentUserCanManagePlugin()) {
						$optimizeTraffic = $this->di->getShared('optimizeTraffic');
						
						$rsOne = $optimizeTraffic->preview_optimize_traffic_modules($dataSent);
						
						$resultData = Utils::mergeArrays(array(
							$resultData
							,$rsOne
						));
						
						unset($rsOne);
					}
				}
				
			}
			
		}
		
		
		if($hook->has_filter('ajax')) {
			$rsOne = $hook->apply_filters('ajax', $dataSent);
			if($rsOne && is_array($rsOne) && !empty($rsOne)) {
				$resultData = Utils::mergeArrays(array(
					$resultData
					,$rsOne
				));
			}
			unset($rsOne);
		}
		
		if(isset($dataSent['cronjob']['status']) && $dataSent['cronjob']['status']) {
			wppepvn_cronjob();
		}
		
		if(
			isset($resultData['notice']['success'])
			&& ($resultData['notice']['success'])
			&& is_array($resultData['notice']['success'])
		) {
			$resultData['notice']['success'] = array_unique($resultData['notice']['success']);
		}
		
		if(
			isset($resultData['notice']['error'])
			&& ($resultData['notice']['error'])
			&& is_array($resultData['notice']['error'])
		) {
			$resultData['notice']['error'] = array_unique($resultData['notice']['error']);
		}
		
		PepVN_Data::encodeResponseData($resultData,true);
		
		unset($resultData);
		
		ob_end_flush();
		
		exit();
		
	}
	
	
}

