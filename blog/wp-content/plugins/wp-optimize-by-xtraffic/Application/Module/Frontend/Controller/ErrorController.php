<?php

class Error extends \WPOptimizeByxTraffic\App\Controller 
{
	public function __construct() 
    {
		parent::__construct();
	}
	
	public function index() 
    {
		$this->view->render('layout/error', array('error' => 'error/404'));
	}
}