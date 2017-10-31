<?php 
namespace WpPepVN;

use WpPepVN\Utils
	,WpPepVN\Hash
	,WpPepVN\Exception
	
;

class System
{
	protected static $_tempData = array();
	
	public function __construct() 
    {
		
	}
	
	public static function extension_loaded($name)
	{
		$k = 'z'.crc32('extension_loaded' . $name);
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		} else {
			self::$_tempData[$k] = extension_loaded($name);
			return self::$_tempData[$k];
		}
	}
	
	public static function function_exists($name)
	{
		$k = 'z'.crc32('function_exists_' . $name);
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		} else {
			self::$_tempData[$k] = function_exists($name);
			return self::$_tempData[$k];
		}
	}
	
	public static function class_exists($name)
	{
		$k = 'z'.crc32('class_exists' . $name);
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		} else {
			self::$_tempData[$k] = class_exists($name);
			return self::$_tempData[$k];
		}
	}
	
	public static function file_exists($path)
	{
		return file_exists($path);
	}
	
	public static function getWebServerSoftwareName()
	{
		$k = 'gtwbsvswnm';
		
		if(!isset(self::$_tempData[$k])) {
			
			self::$_tempData[$k] = '';
			
			if(isset($_SERVER['SERVER_SOFTWARE']) && $_SERVER['SERVER_SOFTWARE']) {
				$tmp = $_SERVER['SERVER_SOFTWARE'];
				if(preg_match('#nginx#i',$tmp)) {
					self::$_tempData[$k] = 'nginx';
				} else if(preg_match('#apache#i',$tmp)) {
					self::$_tempData[$k] = 'apache';
				}
			}
		}
		
		return self::$_tempData[$k];
	}
	
	/*
	* 	#is_writable
		(PHP 4, PHP 5, PHP 7)
		is_writable — Tells whether the filename is writable
		Returns TRUE if the filename exists and is writable. The filename argument may be a directory name allowing you to check if a directory is writable.
		Keep in mind that PHP may be accessing the file as the user id that the web server runs as (often 'nobody'). 
		Safe mode limitations are not taken into account.
	*/
	public static function is_writable($filename)
	{
		return is_writable($filename);
	}
	
	public static function countElementsInDir($dir, $clearstatcacheStatus = true) 
	{
		$objects = false;
		
		if(is_dir($dir)) {
			if(is_readable($dir)) {
				
				if($clearstatcacheStatus) {
					clearstatcache(true, $dir);
				}
				
				$objects = scandir($dir);
				
				if(is_array($objects)) {
					$objects = array_diff($objects, array('.','..')); 
					$objects = count($objects);
				}
			}
		}
		
		return $objects;
	}
	
	public static function unlink($filename) 
	{
		return wppepvn_unlink($filename);
	}
	
	public static function rmdir($dirname) 
	{
		$status = false;
		
		$numCountElementsInDir = self::countElementsInDir($dirname, true);
		if(false !== $numCountElementsInDir) {
			if($numCountElementsInDir < 1) {
				if(is_writable($dirname)) {
					$status = rmdir($dirname);
					clearstatcache(true, $dirname);
				}
			}
		}
		
		return $status;
	}
	
	public static function scandir($dir, $clearstatcacheStatus = true) 
	{
		
		if($clearstatcacheStatus) {
			clearstatcache();
		}
		
		$objects = array();
		
		if($dir) {
			if (is_dir($dir)) {
				if(is_readable($dir)) {
					$objects = scandir($dir);
				}
			}
		}
		
		if(!empty($objects)) {
			$objects = array_diff($objects, array('.','..')); 
			if(!empty($objects)) {
				foreach ($objects as $key => $value) {
					$objects[$key] = $dir . DIRECTORY_SEPARATOR . $value;
				}
			}
		}
		
		return $objects;
	}
	
	/* 
		#rmdirR()
		Remove dir recursive. This method remove dir and all files & subfolders in dir
	
	*/
	public static function rmdirR($dir, $clearstatcacheStatus = true) 
	{
		$resultData = array(
			'files' => 0
			,'dirs' => 0
			,'total' => 0
			,'error' => 0
		);
		
		if($clearstatcacheStatus) {
			clearstatcache();
		}
		
		if($dir) {
			
			if(is_writable($dir)) {
				
				if (is_dir($dir)) {
					
					$objects = false;
					
					if(is_readable($dir)) {
						$objects = scandir($dir);
					}
					
					if(is_array($objects)) {
						$objects = array_diff($objects, array('.','..')); 
						if(!empty($objects)) {
							foreach ($objects as $obj) {
								$objPath = $dir . DIRECTORY_SEPARATOR . $obj;
								if (is_dir($objPath)) {
									$rsOne = self::rmdirR($objPath, false);
									$resultData['files'] += $rsOne['files'];
									$resultData['dirs'] += $rsOne['dirs'];
									$resultData['total'] += $rsOne['total'];
									$resultData['error'] += $rsOne['error'];
								} else {
									$status = self::unlink($objPath);
									if($status) {
										$resultData['files']++;
									} else {
										$resultData['error']++;
									}
								}
							}
						}
							
						$status = self::rmdir($dir);
						if($status) {
							$resultData['dirs']++;
						} else {
							$resultData['error']++;
						}
					} else {
						$resultData['error']++;
					}
					
				} else {
					$status = self::unlink($dir);
					if($status) {
						$resultData['files']++;
					} else {
						$resultData['error']++;
					}
				}
			} else {
				$resultData['error']++;
			}
		} else {
			$resultData['error']++;
		}
		
		return $resultData;
	}
	
    public static function scandirR($dir, $matchPattern = false) 
	{
		$resultData = array(
			'files' => array()
			,'dirs' => array()
		);
		
		if($dir) {
			
			if(is_readable($dir)) {
				
				if (is_dir($dir)) {
					
					$dir = Utils::trailingslashdir($dir);
					
					$objects = scandir($dir);
					
					if(is_array($objects)) {
						
						$objects = array_diff($objects, array('.','..')); 
						
						if(!empty($objects)) {
							
							foreach ($objects as $obj) {
								
								$objPath = $dir . $obj;
								
								if (is_dir($objPath)) {
									
									$rsOne = self::scandirR($objPath,$matchPattern);
									
									$resultData['files'] = array_merge($resultData['files'],$rsOne['files']);unset($rsOne['files']);
									
									$resultData['dirs'] = array_merge($resultData['dirs'],$rsOne['dirs']);unset($rsOne['dirs']);
									
									unset($rsOne);
									
									if($matchPattern) {
										if(preg_match($matchPattern,$objPath)) {
											$resultData['dirs'][] = $objPath;
										}
									} else {
										$resultData['dirs'][] = $objPath;
									}
								} else {
									
									if($matchPattern) {
										if(preg_match($matchPattern,$objPath)) {
											$resultData['files'][] = $objPath;
										}
									} else {
										$resultData['files'][] = $objPath;
									}
								}
							}
						}
						
					}
					
				} else {
					
					if($matchPattern) {
						if(preg_match($matchPattern,$dir)) {
							$resultData['files'][] = $dir;
						}
					} else {
						$resultData['files'][] = $dir;
					}
				}
			}
		}
		
		return $resultData;
	}
    
	public static function mkdir($dir) 
	{
		return wppepvn_mkdir($dir);
	}
	
	public static function hasAPC()
	{
		$k = 'hasAPC';
		
		if(!isset(self::$_tempData[$k])) {
			self::$_tempData[$k] = wppepvn_is_has_apc();
		}
		
		return self::$_tempData[$k];
	}
	
	// http://php.net/manual/en/class.memcached.php
	public static function hasMemcached()
	{
		$k = 'hasMemcached';
		
		if(!isset(self::$_tempData[$k])) {
			self::$_tempData[$k] = wppepvn_is_has_memcached();
		}
		
		return self::$_tempData[$k];
	}
	
	// http://php.net/manual/en/book.memcache.php
	public static function hasMemcache()
	{
		$k = 'hasMemcache';
		
		if(!isset(self::$_tempData[$k])) {
			self::$_tempData[$k] = wppepvn_is_has_memcache();
		}
		
		return self::$_tempData[$k];
	}
	
	public static function cleanServerConfigs($config_key,$text)
	{
		if($config_key) {
			$text = preg_replace('/(###### END '.$config_key.' ######)\s+/s','\1' . PHP_EOL,$text);
			$text = preg_replace('/\s*###### BEGIN '.preg_quote($config_key,'/').' ######.+###### END '.$config_key.' ######/s','',$text);
		}
		
		$text = trim($text);
		
		return $text;
	}
	
	public static function setServerConfigs($input_parameters)
	{
		if(!isset($input_parameters['ROOT_PATH']) || !$input_parameters['ROOT_PATH']) {
			throw new Exception('"ROOT_PATH" is required');
		} else {
			$input_parameters['ROOT_PATH'] = rtrim($input_parameters['ROOT_PATH'],'/');
			$input_parameters['ROOT_PATH'] = rtrim($input_parameters['ROOT_PATH'],DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		}
		
		if(!isset($input_parameters['CONFIG_KEY']) || !$input_parameters['CONFIG_KEY']) {
			throw new Exception('"CONFIG_KEY" is required');
		} else {
			$input_parameters['CONFIG_KEY'] = strtoupper($input_parameters['CONFIG_KEY']);
		}
		
		$status = false;
		
		$webServerSoftwareName = self::getWebServerSoftwareName();
		
		if('apache' === $webServerSoftwareName) {
			
			if(!isset($input_parameters['htaccess']) || !$input_parameters['htaccess']) {
				throw new Exception('"htaccess" is required for Apache Web Server');
			}
			
			$pathFileConfig = $input_parameters['ROOT_PATH'].'.htaccess';
			
			$configContent = false;
			
			if(is_file($pathFileConfig) && is_readable($pathFileConfig) && is_writable($pathFileConfig)) {
				$configContent = file_get_contents($pathFileConfig);
				
			} else if(is_writable($input_parameters['ROOT_PATH'])) {
				file_put_contents($pathFileConfig,'');
				if(is_file($pathFileConfig) && is_readable($pathFileConfig) && is_writable($pathFileConfig)) {
					$configContent = file_get_contents($pathFileConfig);
				}
			}
			
			if(false !== $configContent) {
				$configContent = self::cleanServerConfigs($input_parameters['CONFIG_KEY'],$configContent);
				
				$configContent = trim($configContent);
				
				$input_parameters['htaccess'] = preg_replace('/###### BEGIN '.preg_quote($input_parameters['CONFIG_KEY'],'/').' ######/',' ',$input_parameters['htaccess']);
				$input_parameters['htaccess'] = preg_replace('/###### END '.preg_quote($input_parameters['CONFIG_KEY'],'/').' ######/',' ',$input_parameters['htaccess']);
				
				$input_parameters['htaccess'] = preg_replace('#\#[^\r\n]+#is','',$input_parameters['htaccess']);
				$input_parameters['htaccess'] = preg_replace('#[\r\n]{2,}#is',PHP_EOL . PHP_EOL,$input_parameters['htaccess']);
				
				$input_parameters['htaccess'] = trim($input_parameters['htaccess']);
				
				$input_parameters['htaccess'] = PHP_EOL . '###### BEGIN ' . $input_parameters['CONFIG_KEY'] . ' ######' . PHP_EOL . $input_parameters['htaccess'] . PHP_EOL . '###### END ' . $input_parameters['CONFIG_KEY'] . ' ######' . PHP_EOL;
				
				$newConfigContent = '';
				
				if(!$newConfigContent) {
					if(isset($input_parameters['AFTER_CONFIG_KEY']) && ($input_parameters['AFTER_CONFIG_KEY'])) {
						$tmp = (array)$input_parameters['AFTER_CONFIG_KEY'];
						foreach($tmp as $key1 => $value1) {
							if($value1) {
								$value1 = '###### END ' . $value1 . ' ######';
								$tmp2 = str_replace($value1, $value1 . PHP_EOL . trim($input_parameters['htaccess']), $configContent, $count1);
								if($count1 && ($count1>0)) {
									$newConfigContent = $tmp2;
									break;
								}
							}
						}
					}
				}
				
				if(!$newConfigContent) {
					if(isset($input_parameters['BEFORE_CONFIG_KEY']) && ($input_parameters['BEFORE_CONFIG_KEY'])) {
						$tmp = (array)$input_parameters['BEFORE_CONFIG_KEY'];
						foreach($tmp as $key1 => $value1) {
							if($value1) {
								$value1 = '###### BEGIN ' . $value1 . ' ######';
								$tmp2 = str_replace($value1, trim($input_parameters['htaccess']) . PHP_EOL . $value1, $configContent, $count1);
								if($count1 && ($count1>0)) {
									$newConfigContent = $tmp2;
									break;
								}
							}
						}
					}
				}
				
				if(!$newConfigContent) {
					
					if(isset($input_parameters['append_status']) && ($input_parameters['append_status'])) {
						$newConfigContent = trim($configContent) . PHP_EOL . trim($input_parameters['htaccess']);
					} else {
						$newConfigContent = trim($input_parameters['htaccess']) . PHP_EOL . trim($configContent);
					}
					
				}
				
				if($newConfigContent) {
					$status = file_put_contents($pathFileConfig, trim($newConfigContent));
				}
				
			}
			
		} else if('nginx' === $webServerSoftwareName) {
			
			if(!isset($input_parameters['nginx']) || !$input_parameters['nginx']) {
				throw new Exception('"nginx" is required for Nginx Web Server');
			}
			
			$pathFileConfig = $input_parameters['ROOT_PATH'].'xtraffic-nginx.conf';
			
			$configContent = false;
			
			if(is_file($pathFileConfig) && is_readable($pathFileConfig) && is_writable($pathFileConfig)) {
				$configContent = file_get_contents($pathFileConfig);
				
			} else if(is_writable($input_parameters['ROOT_PATH'])) {
				file_put_contents($pathFileConfig,'');
				if(is_file($pathFileConfig) && is_readable($pathFileConfig) && is_writable($pathFileConfig)) {
					$configContent = file_get_contents($pathFileConfig);
				}
			}
			
			if(false !== $configContent) {
				$configContent = self::cleanServerConfigs($input_parameters['CONFIG_KEY'],$configContent);
				
				$configContent = trim($configContent);
				
				$input_parameters['nginx'] = preg_replace('/###### BEGIN '.$input_parameters['CONFIG_KEY'].' ######/',' ',$input_parameters['nginx']);
				$input_parameters['nginx'] = preg_replace('/###### END '.$input_parameters['CONFIG_KEY'].' ######/',' ',$input_parameters['nginx']);
				
				$input_parameters['nginx'] = preg_replace('#\#[^\r\n]+#is','',$input_parameters['nginx']);
				$input_parameters['nginx'] = preg_replace('#([\;\{\}]+)\s+#is','$1 ',$input_parameters['nginx']);
				$input_parameters['nginx'] = preg_replace('#\s+([\;\{\}]+)#is',' $1',$input_parameters['nginx']);
				
				$input_parameters['nginx'] = trim($input_parameters['nginx']);
				
				$input_parameters['nginx'] = PHP_EOL . '###### BEGIN ' . $input_parameters['CONFIG_KEY'] . ' ######' . PHP_EOL . $input_parameters['nginx'] . PHP_EOL . '###### END ' . $input_parameters['CONFIG_KEY'] . ' ######' . PHP_EOL;
				
				$newConfigContent = '';
				
				if(!$newConfigContent) {
					if(isset($input_parameters['AFTER_CONFIG_KEY']) && ($input_parameters['AFTER_CONFIG_KEY'])) {
						$tmp = (array)$input_parameters['AFTER_CONFIG_KEY'];
						foreach($tmp as $key1 => $value1) {
							if($value1) {
								$value1 = '###### END ' . $value1 . ' ######';
								$tmp2 = str_replace($value1, $value1 . PHP_EOL . trim($input_parameters['nginx']), $configContent, $count1);
								if($count1 && ($count1>0)) {
									$newConfigContent = $tmp2;
									break;
								}
							}
						}
					}
				}
				
				if(!$newConfigContent) {
					if(isset($input_parameters['BEFORE_CONFIG_KEY']) && ($input_parameters['BEFORE_CONFIG_KEY'])) {
						$tmp = (array)$input_parameters['BEFORE_CONFIG_KEY'];
						foreach($tmp as $key1 => $value1) {
							if($value1) {
								$value1 = '###### BEGIN ' . $value1 . ' ######';
								$tmp2 = str_replace($value1, trim($input_parameters['nginx']) . PHP_EOL . $value1, $configContent, $count1);
								if($count1 && ($count1>0)) {
									$newConfigContent = $tmp2;
									break;
								}
							}
						}
					}
				}
				
				if(!$newConfigContent) {
					
					if(isset($input_parameters['append_status']) && ($input_parameters['append_status'])) {
						$newConfigContent = trim($configContent) . PHP_EOL . trim($input_parameters['nginx']);
					} else {
						$newConfigContent = trim($input_parameters['nginx']) . PHP_EOL . trim($configContent);
					}
					
				}
				
				if($newConfigContent) {
					$status = file_put_contents($pathFileConfig, trim($newConfigContent));
				}
				
			}
		}
		
		return $status;
	}
	
	public static function ini_get($name)
	{
		$k = crc32('ini_get'.$name);
		
		if(!isset(self::$_tempData[$k])) {
			self::$_tempData[$k] = ini_get($name);
		}
		
		return self::$_tempData[$k];
	}
	
	public static function isDisableFunction($func)
	{
		$k = crc32('isDisableFunction'.$func);
		
		if(!isset(self::$_tempData[$k])) {
			
			$disable_functions = self::ini_get('disable_functions');
			$suhosin_blacklist = self::ini_get('suhosin.executor.func.blacklist');
			
			$func = preg_quote($func,'#');
			
			$pattern = '#([\s,]+'.$func.'|^'.$func.')#';
			
			if(preg_match($pattern, $disable_functions) || preg_match($pattern, $suhosin_blacklist)) {
				self::$_tempData[$k] = true;
			} else {
				self::$_tempData[$k] = false;
			}
			
		}
		
		return self::$_tempData[$k];
	}
	
	
	public static function isSafeMode()
	{
		$k = 'isSafeMode';
		
		if(!isset(self::$_tempData[$k])) {
			
			$safe_mode = self::ini_get('safe_mode');
			$safe_mode = strtolower($safe_mode);
			
			if('on' === $safe_mode) {
				self::$_tempData[$k] = true;
			} else {
				self::$_tempData[$k] = false;
			}
		}
		
		return self::$_tempData[$k];
	}
	
	public static function fileperms($path)
	{
		$perms = substr(sprintf('%o', fileperms($path)), -4);
		$perms = (string)$perms;
		
		return $perms;
	}
	
	public static function setMaxHeavyExecution()
	{
		$k = 'setMaxHeavyExecution';
		
		if(!isset(self::$_tempData[$k])) {
			
			self::$_tempData[$k] = true;
			
			if(self::function_exists('ignore_user_abort')) {
				@ignore_user_abort(true);
			}
			
			if(self::function_exists('set_time_limit')) {
				@set_time_limit(0);
			}
			
			if(self::function_exists('ini_set')) {
				@ini_set('memory_limit', -1);
				@ini_set('max_execution_time',0);
			}
			
		}
	}
	
	public static function isOS32b()
	{
		$k = 'isOS32b';
		
		if(!isset(self::$_tempData[$k])) {
			
			if(4 === PHP_INT_SIZE) {
				self::$_tempData[$k] = true;
			} else {
				self::$_tempData[$k] = false;
			}
		}
		
		return self::$_tempData[$k];
	}
	
	public static function isOS64b()
	{
		$k = 'isOS64b';
		
		if(!isset(self::$_tempData[$k])) {
			
			if(8 === PHP_INT_SIZE) {
				self::$_tempData[$k] = true;
			} else {
				self::$_tempData[$k] = false;
			}
		}
		
		return self::$_tempData[$k];
	}
}