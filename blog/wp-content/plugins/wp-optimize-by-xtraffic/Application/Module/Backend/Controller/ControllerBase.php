<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend\Controller;

use WPOptimizeByxTraffic\Application\Service\ControllerBase as ServiceControllerBase
	,WpPepVN\DependencyInjection
;

class ControllerBase extends ServiceControllerBase
{
	public function __construct() 
    {
		parent::__construct();
		
		
		
	}
	
	public function init(DependencyInjection $di) 
    {
		parent::init($di);
		
		$this->view->adminNotice = $this->di->getShared('adminNotice');
	}
}