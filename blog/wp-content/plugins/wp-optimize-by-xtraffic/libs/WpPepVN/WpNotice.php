<?php 
namespace WpPepVN;

use WpPepVN\Utils
	,WpPepVN\Hook
;

class WpNotice 
{
	
    protected static $_notices = array();
	
    protected static $_statistics = array();
	
	protected static $_tempData = array();
	
	private $_bag = '';
	
	protected static $_sns = 'wppepvn-nt';
	
	protected static $_configs = array();
    
    public function __construct($bag = 0) 
    {
		if(!$bag) {
			$bag = mt_rand(1,900000000);
		}
		
		$this->_bag = $bag;
		
		self::$_notices[$bag] = array();
		
		self::$_sns = WP_PEPVN_NS_SHORT.'-nt';
		
	}
	
	public function init_ajax_backend()
	{
		
		if(!isset(self::$_configs['init-ajax-backend'])) {
			self::$_configs['init-ajax-backend'] = true;
			
			Hook::add_filter('ajax',array($this,'filter_ajax_backend'));
		}
	}
    
    public function add_notice(
        $text   //Notice Text
        , $type //Type of notice : success, error , warning, info
        , $options = array(
			//'dismiss-control' => true
		)   //options : array
    ) {
		$text = trim($text);
		
		if($text) {
			$bag = $this->_bag;
				
			$key = Utils::hashKey(array($bag,$text,$type,$options));
			
			self::$_notices[$this->_bag][$key] = array(
				'text' => $text
				,'type' => $type
				,'options' => $options
			);
			
			if(!isset(self::$_statistics[$bag]['type'][$type])) {
				self::$_statistics[$bag]['type'][$type] = 0;
			}
			
			self::$_statistics[$bag]['type'][$type]++;
		}
		
	}
    
    public function get_all_notice() 
    {
        return self::$_notices[$this->_bag];
    }
    
	public function has_type($type) 
    {
		$bag = $this->_bag;
		return isset(self::$_statistics[$bag]['type'][$type]);
	}
	
	public function count_type($type) 
    {
		$bag = $this->_bag;
		
		if(isset(self::$_statistics[$bag]['type'][$type])) {
			return self::$_statistics[$bag]['type'][$type];
		}
		
		return 0;
	}
    
    public function render($data) 
    {
		$k = crc32($data['text']);
		
		if(isset(self::$_tempData['rendered'][$k])) {
			return '';
		}
		
		self::$_tempData['rendered'][$k] = 1;
		
		if(isset($data['options']['dismiss-control']['id'])) {
			$keyCache = self::$_sns . '-' . $data['options']['dismiss-control']['id'];
			if ( false !== ( $rs = get_transient( $keyCache ) ) ) {
				return '';
			}
		}
		
		$class = '';
		
        if('success' === $data['type']) {
			$class = 'updated';
		} else if('info' === $data['type']) {
			$class = 'updated';
		} else if('warning' === $data['type']) {
			$class = 'update-nag';
		} else if('error' === $data['type']) {
			$class = 'error';
		}
		
		$output = '<div class="wppepvn-notice '.$class.'" style="padding: 1%;"><div>'.$data['text'].'</div>';
		
		if(isset($data['options']['dismiss-control']['id'])) {
			$output .= '<div style="margin-top: 9px;">
	<a href="#" class="wppepvn-dismiss-control" data-ntid="'.$data['options']['dismiss-control']['id'].'"><b>Dismiss this notice</b></a>
</div>';
		}
		
		$output .= '</div>';
		
		return $output;
    }
    
    private function _render_all() 
    {
		$result = '';
		
		$bag = $this->_bag;
		
        foreach(self::$_notices[$bag] as $key => $data) {
			unset(self::$_notices[$bag][$key]);
			$result .= $this->render($data);
			unset($key,$data);
		}
		
		return $result;
    }
    
    public function show_all() 
    {
        echo $this->_render_all();
    }
	
    public function filter_ajax_backend($dataSent) 
    {
		if(isset($dataSent['dismiss-notice']['id'])) {
			$keyCache = self::$_sns . '-' . $dataSent['dismiss-notice']['id'];
			set_transient($keyCache, 1, DAY_IN_SECONDS * 30);
		}
		
		return array();
	}
}