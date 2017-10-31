<?php 
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\Mobile_Detect
	,WpPepVN\TempData
;

class Device extends TempData
{
	public $mobileDetectObject = false;
	
	public function __construct() 
    {
		parent::__construct();
		$this->mobileDetectObject = new Mobile_Detect();
		
	}
    
	public function isMobile() 
    {
		return $this->mobileDetectObject->isMobile();
	}
	
	public function isTablet() 
    {
		return $this->mobileDetectObject->isTablet();
	}
	
	public function is($data) 
    {
		// Alternative to magic methods. $detect->is('iphone');
		return $this->mobileDetectObject->is($data);
	}
	
	public function version($data) 
    {
		// Find the version of component. $detect->version('Android');
		return $this->mobileDetectObject->version($data);
	}
	
	public function match($data) 
    {
		// Additional match method. $detect->match('regex.*here');
		return $this->mobileDetectObject->match($data);
	}
	
	public function get_device_screen_width()
	{
		return wppepvn_get_device_screen_width();
	}
	
}