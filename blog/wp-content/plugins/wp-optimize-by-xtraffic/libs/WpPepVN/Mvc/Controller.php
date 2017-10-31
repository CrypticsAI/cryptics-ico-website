<?php
namespace WpPepVN\Mvc;

use WpPepVN\DependencyInjection\Injectable
	, WpPepVN\DependencyInjection
	, WpPepVN\Tag
;

abstract class Controller extends Injectable
{
	public $di;
	
	public function __construct() 
    {
		
	}
	
	public function init(DependencyInjection $di)
	{
		$this->di = $di;
		$this->_dependencyInjector = $this->di;
		$this->setDI($this->di);
		
		Tag::setDI($this->di);
		
		$this->view = $this->di->getShared('view');
		//$this->view->adminNotice = $this->di->getShared('adminNotice');
		$this->view->translate = $this->di->getShared('translate');
		
		$this->request = $this->di->getShared('request');
	}
}