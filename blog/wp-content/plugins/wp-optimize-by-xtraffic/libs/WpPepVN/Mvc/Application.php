<?php
namespace WpPepVN\Mvc;

include_once(__DIR__ . '/../Bootstrap.php');

class Application extends \WpPepVN\Bootstrap
{
    public $di = false;
    
    /*
        @_cacheData : array()
        Store Temporary Data 
        
        array(
            key => value
        )
    */
    protected $_cacheData = array();
    
	public function __construct() 
    {
		parent::__construct();
	}
    
    public function init() 
    {
        parent::init();
    }
    
}