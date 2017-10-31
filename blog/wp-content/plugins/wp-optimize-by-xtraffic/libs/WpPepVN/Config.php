<?php
namespace WpPepVN;
    
class Config implements \ArrayAccess
{
    private $_configs = array();
    
    public function __construct($configs) 
    {
        $this->setConfigs($configs);
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_configs[] = $value;
        } else {
            $this->_configs[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->_configs[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_configs[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->_configs[$offset]) ? $this->_configs[$offset] : null;
    }
    
    public function setConfigs(&$configs)
    {
        $this->_configs = (array)$configs;
    }
    
    public function extendConfigs($configs)
    {
        $configs = (array)$configs;
        
        $this->_configs = $this->mergeArrays(array(
            $this->_configs
            , $configs
        ));
    }
    
    public function getConfigs()
    {
        return $this->_configs;
    }
    
	private function mergeArrays($data)
	{
		$merged = false;
		
		if(is_array($data)) {
		
			$merged = array_shift($data); // using 1st array as base
			
			foreach($data as $array) {
                
				foreach ($array as $key => $value) {
					
					if(isset($merged[$key]) && is_array($value) && is_array($merged[$key])) {
						
						$merged[$key] = $this->mergeArrays(array($merged[$key], $value));
						
					} else {
						if(is_numeric($key)) {
							$merged[] = $value;
						} else {
							$merged[$key] = $value;
						}
					}
				}
			}
		}
		
		return $merged;
	}
    
}