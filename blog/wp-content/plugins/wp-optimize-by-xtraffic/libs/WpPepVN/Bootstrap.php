<?php
namespace WpPepVN;

class Bootstrap 
{
    private $_autoloadDirs = array();
    
    private $_configs = array();
    
    private $_registerNamespaces = array();
    
    private $_registerNamespaces_Keys = array();
    
    private $_registerNamespaces_Values = array();
    
    protected $_tempData = array();
	
	private $_patternClassAutoLoad = false;
	
	private static $_tempDataStatic = array();
	
    private static $_autoloadClassLoaded = array();
    
    public function __construct() 
    {
		
	}
	
	public function registerLoaderDirs(array $dirs) 
    {
        $dirs = (array)$dirs;
        foreach($dirs as $dir) {
            $this->_autoloadDirs[] = preg_replace('#[/\s\\\]+$#is','',$dir);
        }
    }
    
    public function registerNamespaces(array $namespaces) 
    {
        $namespaces = (array)$namespaces;
        foreach($namespaces as $namespace => $dir) {
            $this->_registerNamespaces[$namespace] = preg_replace('#[/\s\\\]+$#is','',$dir) . DIRECTORY_SEPARATOR;
        }
    }
    
	public function init() 
    {
        
        $this->_autoloadDirs = array_unique($this->_autoloadDirs);
        
		if(!empty($this->_registerNamespaces)) {
			$this->_registerNamespaces_Keys = array_keys($this->_registerNamespaces);
			$this->_registerNamespaces_Values = array_values($this->_registerNamespaces);
			
			$this->_registerNamespaces = array();
			
			$temp = $this->_registerNamespaces_Keys;
			$temp[] = __NAMESPACE__;
			$temp = implode(';;;;;;',$temp);
			$temp = preg_quote($temp,'#');
			$this->_patternClassAutoLoad = '#('.str_replace(';;;;;;','|',$temp).')#'; unset($temp);
		}
        
        spl_autoload_register(array(&$this, 'autoload_register'));
        
	}
    
    public function autoload_register($class) 
    {
		$keyClass = crc32($class);
        if(!isset(self::$_autoloadClassLoaded[$keyClass])) {
            self::$_autoloadClassLoaded[$keyClass] = true;
			
            if(!class_exists($class)) {
				
				$checkAndLoadStatus = true;
				if(false !== $this->_patternClassAutoLoad) {
					if(!preg_match($this->_patternClassAutoLoad,$class)) {
						$checkAndLoadStatus = false;
					}
				}
				
				if(true === $checkAndLoadStatus) {
					
					$class = preg_replace('#^[\\\]+#','',$class);
					$class = preg_replace('#[\\\]+$#','',$class);
					
					$count1 = 0;
					
					if(!empty($this->_registerNamespaces_Keys)) {
						$class = str_replace($this->_registerNamespaces_Keys,$this->_registerNamespaces_Values,$class,$count1);
					}
					
					if($count1 > 0) {
						$file = sprintf('%s.php', $class);
						$file = str_replace('\\',DIRECTORY_SEPARATOR,$file);
						
						if (file_exists($file) && is_file($file)) {
							
							if(function_exists('wppepvn_include_once')) {
								wppepvn_include_once($file,'require');
							} else {
								require_once $file;
							}
							
						}
						
					} else {
						foreach ($this->_autoloadDirs as $dir) {
							$file = sprintf($dir . DIRECTORY_SEPARATOR . '%s.php', $class);
							$file = str_replace('\\',DIRECTORY_SEPARATOR,$file);
							if (file_exists($file) && is_file($file)) {
								if(function_exists('wppepvn_include_once')) {
									wppepvn_include_once($file,'require');
								} else {
									require_once $file;
								}
							}
						}
					}
					
                }
            }
        }
        
    }
    
    
}