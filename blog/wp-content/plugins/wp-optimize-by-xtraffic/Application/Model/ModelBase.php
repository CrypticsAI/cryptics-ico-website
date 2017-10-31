<?php 
namespace WPOptimizeByxTraffic\Application\Model;

use WpPepVN\DependencyInjection\Injectable
	,WpPepVN\Mvc\Model as MvcModel
;

class ModelBase extends MvcModel
{
	public function __construct() 
    {
		parent::__construct();
	}
	
}