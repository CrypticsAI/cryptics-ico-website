<?php
namespace WpPepVN;

use WpPepVN\Utils
;

class TempData
{
	
	protected static $_tempData = array();
	
	private $_has_igbinary_status = false;
	
	protected $_bag = 0;
	
	public function __construct() 
    {
		if(function_exists('igbinary_serialize')) {
			$this->_has_igbinary_status = true;
		}
		
		$bag = Utils::randomHash();
		
		$this->_bag = crc32($bag);
		
	}
	
	private function _tempData_serialize($data) 
	{
		if(true === $this->_has_igbinary_status) {
			return igbinary_serialize($data);
		} else {
			return serialize($data);
		}
	}
	
	private function _tempData_unserialize($data) 
	{
		if(true === $this->_has_igbinary_status) {
			return igbinary_unserialize($data);
		} else {
			return unserialize($data);
		}
	}
	
	protected function _tempData_hashKey($input_data)
	{
		return hash('crc32b', md5($this->_tempData_serialize($input_data)));
	}
	
}