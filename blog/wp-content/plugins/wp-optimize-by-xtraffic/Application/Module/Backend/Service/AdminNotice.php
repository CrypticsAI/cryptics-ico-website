<?php 
namespace WPOptimizeByxTraffic\Application\Module\Backend\Service;

use WpPepVN\WpNotice
;

class AdminNotice extends WpNotice
{
	public function __construct() 
    {
		parent::__construct();
		
		add_action('admin_notices', array($this, 'show_all'));
	}
}