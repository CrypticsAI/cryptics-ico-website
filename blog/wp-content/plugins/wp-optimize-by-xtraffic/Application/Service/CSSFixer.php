<?php
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	,WpPepVN\Utils
;

class CSSFixer
{

	public $css_url = '';
	
	public function fix($options = false)
	{
		
		if(!$options) {
			$options = array();
		}
		if(!isset($options['minify_status'])) {
			$options['minify_status'] = false;
		}
		
		$keyCache = Utils::hashKey(array(
			__CLASS__
			, __METHOD__
			,$options
		));
		
		$tmp = PepVN_Data::$cacheObject->get_cache($keyCache);
		
		if(null !== $tmp) {
			return $tmp;
		}
		
		if(isset($options['css_url'])) {
			$this->css_url = $options['css_url'];
		}
        
        $options['css_content'] = (array)$options['css_content'];
		
		$options['css_content'] = implode(PHP_EOL,$options['css_content']);
		
		if($options['minify_status']) {
			$options['css_content'] = $this->minify($options['css_content']);
		}
		
		$options['css_content'] = $this->fixPathsInCssContent($options['css_content']);
		
		$options['css_content'] = $this->fixRules($options['css_content']);
		
		PepVN_Data::$cacheObject->set_cache($keyCache, $options['css_content']);
		
		return trim($options['css_content']);
		
	}
	
	
	public function minify($css)
	{
		
		$css = (array)$css;
		
		$css = implode(' ',$css);
		
		$css = pepvn_MinifyCss($css);
		
		return trim($css);
	}
	
	public function fixPathsInCssContent($css){
		$css = preg_replace("/@import\s+[\"\']([^\;\"\'\)]+)[\"\'];/", "@import url($1);", $css);
		return preg_replace_callback("/url\(([^\)]*)\)/", array($this, 'newImgPath'), $css);
	}

	public function fixRules($css){
		$css = $this->fixImportRules($css);
		$css = $this->fixCharset($css);
		return $css;
	}

	public function fixImportRules($css){
		preg_match_all('/@import\s+url\([^\)]+\);/i', $css, $imports);

		if(count($imports[0]) > 0){
			$css = preg_replace('/@import\s+url\([^\)]+\);/i', "/* @import is moved to the top */", $css);
			for ($i = count($imports[0])-1; $i >= 0; $i--) {
				$css = $imports[0][$i]."\n".$css;
			}
		}
		return $css;
	}

	public function fixCharset($css){
		preg_match_all('/@charset.+;/i', $css, $charsets);
		if(count($charsets[0]) > 0){
			$css = preg_replace('/@charset.+;/i', "/* @charset is moved to the top */", $css);
			foreach($charsets[0] as $charset){
				$css = $charset."\n".$css;
			}
		}
		return $css;
	}

	public function newImgPath($matches){
		$matches[1] = str_replace(array("\"","'"), "", $matches[1]);
		if(!$matches[1]){
			$matches[1] = "";
		}else if(preg_match("/^(\/\/|http|\/\/fonts|data:image|data:application)/", $matches[1])){
			$matches[1] = $matches[1];
		}else if(preg_match("/^\//", $matches[1])){
			$homeUrl = str_replace(array("http:", "https:"), "", home_url());
			$matches[1] = $homeUrl.$matches[1];
		}else if(preg_match("/^(?P<up>(\.\.\/)+)(?P<name>.+)/", $matches[1], $out)){
			$count = strlen($out["up"])/3;
			$url = dirname($this->css_url);
			for($i = 1; $i <= $count; $i++){
				$url = substr($url, 0, strrpos($url, "/"));
			}
			$url = str_replace(array("http:", "https:"), "", $url);
			$matches[1] = $url."/".$out["name"];
		}else{
			$url = str_replace(array("http:", "https:"), "", dirname($this->css_url));
			$matches[1] = $url."/".$matches[1];
		}

		return "url(".$matches[1].")";
	}

}



