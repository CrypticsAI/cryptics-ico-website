<?php
namespace WpPepVN;

use  WpPepVN\System
;

class Logger 
{
	private $_log_dir = false;
	
	private $_log_filename = false;
	
	private $_log_filepath = false;
	
	private $_configs = array();
	
	private $_logsData = array();
	
	const INFO = 1;
	const DEBUG = 2;
	const NOTICE = 3;
	const WARNING = 4;
	const ERROR = 5;
	const ALERT = 6;
	const CRITICAL = 7;
	const EMERGENCY = 8;
	
	/**
	* Constructor
	* @param String logfile - [optional] Absolute file name/path. Defaults to ubuntu apache log.
	* @return void
	**/        
	public function __construct($configs = array()) 
	{
		$this->_configs = array_merge(array(
			
		), $configs);
		
		if(isset($this->_configs['log_dir'])) {
			$this->_log_dir = $this->_configs['log_dir'];
		}
		
		if(isset($this->_configs['log_filename'])) {
			$this->_log_filename = $this->_configs['log_filename'];
		}
		
		if($this->_log_dir) {
			
			if(!is_dir($this->_log_dir)) {
				System::mkdir($this->_log_dir);
			}
			
			if(is_dir($this->_log_dir) && (is_writable($this->_log_dir) || win_is_writable($this->_log_dir))) {
				
				if(!$this->_log_filename) {
					$this->_log_filename = date('Y-m-d').'.log';
				}
				
				$this->_log_filepath = wppepvn_trailingslashdir($this->_log_dir) . $this->_log_filename;
			}
			
			if(!is_file($this->_log_filepath)){ //Attempt to create log file                
				touch($this->_log_filepath);
			}

			//Make sure we'ge got permissions
			if(!(is_writable($this->_log_filepath) || win_is_writable($this->_log_filepath))) {
				//Cant write to file,
				throw new Exception("LOGGER ERROR: Can't write to log", 1);
			}
			
			add_action('shutdown', array($this,'writeLogToFile'), 900000009);
			
		}
		
	}

	/**
	* d - Log Debug
	* @param String tag - Log Tag
	* @param String message - message to spit out
	* @return void
	**/        
	public function debug($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::DEBUG);
	}

	/**
	* e - Log Error
	* @param String tag - Log Tag
	* @param String message - message to spit out
	* @author 
	**/        
	public function error($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::ERROR);
	}

	/**
	* w - Log Warning
	* @param String tag - Log Tag
	* @param String message - message to spit out
	* @author 
	**/        
	public function warning($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::WARNING);
	}

	/**
	* i - Log Info
	* @param String tag - Log Tag
	* @param String message - message to spit out
	* @return void
	**/        
	public function info($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::INFO);
	}
	
	public function notice($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::NOTICE);
	}
	
	public function critical($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::CRITICAL);
	}

	public function emergency($message, $tag='', $var=NULL)
	{
		$this->log($message,$tag,$var,self::EMERGENCY);
	}
	
	/**
	* log - writes out timestamped message to the log file as 
	* defined by the $log_file class variable.
	*
	* @param String status - "INFO"/"DEBUG"/"ERROR" e.t.c.
	* @param String tag - "Small tag to help find log entries"
	* @param String message - The message you want to output.
	* @return void
	**/        
	public function log($message, $tag='', $var=NULL, $level=1) 
	{
		
		$lv = 'INFO';
		
		if(!$tag) {
			$tag = 'INFO';
		}
		
		if(self::DEBUG === $level) {
			$lv = 'DEBUG';
		} else if(self::NOTICE === $level) {
			$lv = 'NOTICE';
		} else if(self::WARNING === $level) {
			$lv = 'WARNING';
		} else if(self::ERROR === $level) {
			$lv = 'ERROR';
		} else if(self::ALERT === $level) {
			$lv = 'ALERT';
		} else if(self::CRITICAL === $level) {
			$lv = 'CRITICAL';
		} else if(self::EMERGENCY === $level) {
			$lv = 'EMERGENCY';
		}
		
		$date = date('Y-m-d H:i:s');
		
		$message = '[TIME '.$date.'] - ['.$lv.'] : ['.$tag.'] - '.$message.'';
		
		if(NULL !== $var) {
			$message .= ' - '.var_export($var, true);
		}
		
		$this->_logsData[] = $message;
		
	}
    
	public function writeLogToFile() 
	{
		if($this->_log_filepath && is_file($this->_log_filepath) && (is_writable($this->_log_filepath) || win_is_writable($this->_log_filepath))) {
			if($this->_logsData && !empty($this->_logsData)) {
				$glue = PHP_EOL . '------' . PHP_EOL;
				file_put_contents($this->_log_filepath, implode($glue, $this->_logsData).$glue, FILE_APPEND);
			}
			$this->_logsData = array();
		}
	}
}
