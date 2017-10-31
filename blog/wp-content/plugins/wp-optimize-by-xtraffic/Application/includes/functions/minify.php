<?php 

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	, WPOptimizeByxTraffic\Application\Service\TempDataAndCacheFile
	, WPOptimizeByxTraffic\Application\Service\JSMin
	, WPOptimizeByxTraffic\Application\Service\CSSmin
	, WPOptimizeByxTraffic\Application\Service\CSSFixer
	, WPOptimizeByxTraffic\Application\Service\Minify_HTML
	, WpPepVN\Utils
;

function pepvn_MinifyJavascript($input_data) 
{
	$input_data = (array)$input_data;
	$input_data = implode(PHP_EOL,$input_data);
	$input_data = (string)$input_data;
	$input_data = trim($input_data);
	
	$keyCache1 = Utils::hashKey(array(
		'pepvn_MinifyJavascript'
		,$input_data
	));
	
	$tmp = TempDataAndCacheFile::get_cache($keyCache1, true);
	
	if(null !== $tmp) {
		return $tmp;
	}
	
	$rsOne = PepVN_Data::escapeByPattern($input_data,array(
		'pattern' => '#[\+\-]+[ \t\s]+[\+\-]+#is'
		,'target_patterns' => array(
			0
		)
		,'wrap_target_patterns' => '+'
	));
	$input_data = $rsOne['content'];
	unset($rsOne['content']);
	
	
	/*
	$pepVN_JavaScriptPacker = null;$pepVN_JavaScriptPacker = new PepVN_JavaScriptPacker($input_data, 'Normal', true, false);
	$input_data = $pepVN_JavaScriptPacker->pack();
	$pepVN_JavaScriptPacker=0;unset($pepVN_JavaScriptPacker);
	*/
	
	$input_data = JSMin::minify($input_data);
	
	if(!PepVN_Data::isEmptyArray($rsOne['patterns'])) {
		$input_data = str_replace(array_values($rsOne['patterns']),array_keys($rsOne['patterns']),$input_data);
	}
	unset($rsOne);
	
	$input_data = trim($input_data);
	
	TempDataAndCacheFile::set_cache($keyCache1, $input_data, true);
	
	return $input_data;
	
}

function pepvn_MinifyCss($input_data)
{
	$input_data = (array)$input_data;
	$input_data = implode(PHP_EOL,$input_data);
	$input_data = (string)$input_data;
	$input_data = trim($input_data);
	
	$keyCache1 = Utils::hashKey(array(
		'pepvn_MinifyCss_Ref'
		,$input_data
	));
	
	$tmp = TempDataAndCacheFile::get_cache($keyCache1, true);

	if(null !== $tmp) {
		return $tmp;
	}
	
	$cssMin = new CSSmin();
	$input_data = $cssMin->run($input_data,FALSE);
	$input_data = trim($input_data);
	unset($cssMin);
	
	TempDataAndCacheFile::set_cache($keyCache1, $input_data, true);
	
	return $input_data;
}

function pepvn_MinifyHtml($input_data)
{
	$input_data = (array)$input_data;
	$input_data = implode(PHP_EOL,$input_data);
	$input_data = (string)$input_data;
	$input_data = trim($input_data);
	
	$keyCache1 = Utils::hashKey(array(
		'pepvn_MinifyHtml'
		,$input_data
	));
	
	$tmp = TempDataAndCacheFile::get_cache($keyCache1);

	if(null !== $tmp) {
		return $tmp;
	}
	
	$findAndReplace1 = array();
	$findAndReplace2 = array();
	
	$patternsEscaped1 = array();
	
	//Escape all scripts not javascript
	preg_match_all('#<script[^><]*>.*?</script>#is',$input_data,$matched1);
	if(isset($matched1[0]) && !PepVN_Data::isEmptyArray($matched1[0])) {
		$matched1 = $matched1[0];
		foreach($matched1 as $key1 => $value1) {
			unset($matched1[$key1]);
			
			if($value1) {
				
				$checkStatus1 = true;
				
				if(preg_match('#type=(\'|")([^"\']+)\1#i',$value1,$matched2)) {
					if(isset($matched2[2]) && $matched2[2]) {
						$matched2 = trim($matched2[2]);
						if($matched2) {
							$checkStatus1 = false;
							if(false !== stripos($matched2,'javascript')) {
								$checkStatus1 = true;
							} else if(false !== stripos($matched2,'text/html')) {
								$findAndReplace2[$value1] = preg_replace('#[\s \t]+#is',' ',$value1);
							}
						}
					}
				}
				
				if(!$checkStatus1) {
					$patternsEscaped1[$value1] = '__'.md5($value1).'__';
				}
				
			}
			
			unset($key1,$value1);
		}
	}
	
	if(!empty($patternsEscaped1)) {
		$input_data = str_replace(array_keys($patternsEscaped1),array_values($patternsEscaped1),$input_data);
	}
	
	$rsOne = PepVN_Data::escapeSpecialElementsInHtmlPage($input_data);
	$input_data = $rsOne['content'];
	if(!empty($rsOne['patterns'])) {
		$findAndReplace1 = array_merge($findAndReplace1, $rsOne['patterns']);
	}
	unset($rsOne);
	
	$rsOne = PepVN_Data::escapeHtmlTagsAndContents($input_data,'pre;code;textarea;input');
	$input_data = $rsOne['content'];
	
	if(!empty($rsOne['patterns'])) {
		$findAndReplace1 = array_merge($findAndReplace1, $rsOne['patterns']);
	}
	
	unset($rsOne);
	
	$input_data = Minify_HTML::minify($input_data, array(
		'jsCleanComments' => true
		,'cssMinifier' => 'pepvn_MinifyCss'
		,'jsMinifier' => 'pepvn_MinifyJavascript'
	));
	
	if(!empty($patternsEscaped1)) {
		$input_data = str_replace(array_values($patternsEscaped1),array_keys($patternsEscaped1),$input_data);
	}
	$patternsEscaped1 = array();
	
	if(!empty($findAndReplace2)) {
		$input_data = str_replace(array_keys($findAndReplace2),array_values($findAndReplace2),$input_data);
	}
	$findAndReplace2 = array();
	
	$findAndReplaceTmp1 = array();
	preg_match_all('#<(script|style)[^><]*?>.*?</\1>#is',$input_data,$matched1);
    if(!empty($matched1[0])) {
		$matched1 = $matched1[0];
        foreach($matched1 as $key1 => $value1) {
			unset($matched1[$key1]);
			
            $findAndReplaceTmp1[$value1] = '__'.hash('crc32b',md5($value1)).'__';
        }
    }
	$matched1 = 0;
	
	if(!empty($findAndReplaceTmp1)) {
		$input_data = str_replace(array_keys($findAndReplaceTmp1),array_values($findAndReplaceTmp1),$input_data); 
		$findAndReplace1 = array_merge($findAndReplace1,$findAndReplaceTmp1);
	}
	unset($findAndReplaceTmp1);
	
	$patterns1 = array(
		'#>[\s \t]+<#is' => '> <'
		, '#[\s \t]+#is' => ' '
		, '#<!--(.|\s)*?-->#is' => ''
	);
	
	$input_data = preg_replace(array_keys($patterns1),array_values($patterns1), $input_data);
	unset($patterns1);
	
	if(!empty($findAndReplace1)) {
		$input_data = str_replace(array_values($findAndReplace1),array_keys($findAndReplace1),$input_data);
	}
	unset($findAndReplace1);
	
	$input_data = trim($input_data);
	
	TempDataAndCacheFile::set_cache($keyCache1, $input_data);
	
	return $input_data;
	
}

