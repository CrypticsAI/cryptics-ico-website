<?php 

namespace WpPepVN;

/**
 * WpPepVN\Translate
 *
 * Translate component allows the creation of multi-language applications using
 * different adapters for translation lists.
 */
class Translate
{
	public $domain = false;
	
	public function __construct() 
    {
		
	}
	
	/*
	* Loads the plugin's translated strings.
	* If the path is not given then it will be the root of the plugin directory. 
	* The .mo file should be named based on the domain followed by a dash, and then the locale exactly. 
	* For example, the locale for German is 'de_DE', and the locale for Danish is 'da_DK'. 
	* If your plugin's text domain is "my-plugin" the Danish .mo and.po files should be named "my-plugin-da_DK.mo" and "my-plugin-da_DK.po" Call this function in your plugin as early as the plugins_loaded action.
	* If you call load_plugin_textdomain multiple times for the same domain, the translations will be merged. If both sets have the same string, the translation from the original value will be taken.
	*/
	
	/*
		Parameters
		$domain
			(string) (required) Unique identifier for retrieving translated strings.
			Default: None
		$abs_rel_path
			(string) (optional) Relative path to ABSPATH of a folder, where the .mo file resides. Deprecated, but still functional until 2.7
			Default: false
		$plugin_rel_path
			(string) (optional) Relative path to WP_PLUGIN_DIR. This is the preferred argument to use. It takes precedence over $abs_rel_path
			Default: false
	*/

	public function load_plugin_textdomain($domain, $plugin_rel_path) 
	{
		
		// Localization
		$this->domain = $domain;
		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	}
	
	public function _($text, $domain = false) 
	{
		if(false === $domain) {
			$domain = $this->domain;
		}
		
		return __($text,$domain);
	}
	
	public function e($text, $domain = false) 
	{
		if(false === $domain) {
			$domain = $this->domain;
		}
		
		_e($text,$domain);
	}
}