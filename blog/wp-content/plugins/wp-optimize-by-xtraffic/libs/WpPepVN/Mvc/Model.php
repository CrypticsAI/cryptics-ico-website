<?php
namespace WpPepVN\Mvc;

use WpPepVN\DependencyInjection\Injectable
;

class Model extends Injectable
{
	public static $wpdb = false;
	
	protected static $_tempData = array();
	
	public function __construct() {
		//$this->database = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
	}
	
	public function getWPDB() 
	{
		if(false === self::$wpdb) {
			global $wpdb;
			self::$wpdb = $wpdb;
		}
		
		return self::$wpdb;
	}
}