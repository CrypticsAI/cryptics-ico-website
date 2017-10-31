<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\DependencyInjection
	,WpPepVN\Mvc\Controller as MvcController
	,WPOptimizeByxTraffic\Application\Service\Dashboard
;

class ControllerBase extends MvcController
{
	public function __construct() 
    {
		parent::__construct();
	}
	
	public function init(DependencyInjection $di) 
    {
		parent::init($di);
		
		$this->view->translate = $this->di->getShared('translate');
		
		$translate = $this->view->translate;
		
		$adminNotice = $this->di->getShared('adminNotice');
		
		$options = Dashboard::getOption();
		
		if(isset($options['last_time_plugin_activation']) && $options['last_time_plugin_activation']) {
			$options['last_time_plugin_activation'] = (int)$options['last_time_plugin_activation'];
			if($options['last_time_plugin_activation'] > 0) {
				if($options['last_time_plugin_activation'] <= ( PepVN_Data::$defaultParams['requestTime'] - (DAY_IN_SECONDS * 15))) {
					
					$noticeOption = array();
					$noticeOption['dismiss-control']['id'] = crc32('wp-optimize-by-xtraffic-rating-notice');
					
					$noticeTemp = $translate->_('Sincerely thank you for your trust and use plugin'). ' "'.WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME.'" ! '.'
<br />'.$translate->_('If you liked this plugin').' <a href="https://bit.ly/wp-optimize-by-xtraffic-rating" target="_blank" class="" ><b><u><i>' . $translate->_('you can support us by rating this plugin here'). '</i></u></b></a>. ' . $translate->_('We are grateful for your support :)');
					
					$adminNotice->add_notice($noticeTemp, 'success', $noticeOption);
				}
			}
		}
		
	}
	
	protected function _addNoticeSavedSuccess() 
    {
		$this->view->adminNotice->add_notice($this->view->translate->_('Options were saved successfully.'), 'success');
	}
	
	protected function _doAfterUpdateOptions() 
    {
		$cacheManager = $this->di->getShared('cacheManager');
		$cacheManager->registerCleanCache(',all,');
	}
}