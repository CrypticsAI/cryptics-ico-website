<?php
namespace WPOptimizeByxTraffic\Application\Service;

use WpPepVN\DependencyInjection
;

class OutputBuffer 
{
	public $di = false;
	
	public function __construct(DependencyInjection $di) 
    {
        $this->di = $di;
    }
    
    public function initFrontend() 
    {
		if(function_exists('ob_start')) {
			ob_start(array($this, 'output_callback'));
		}
	}
	
	public function output_callback($buffer) 
    {
		$hook = $this->di->getShared('hook');
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		if($hook->has_filter('output_buffer_before_return')) {
			$buffer = $hook->apply_filters('output_buffer_before_return', $buffer);
		}
		
		return $buffer;
	}
	
	
}


