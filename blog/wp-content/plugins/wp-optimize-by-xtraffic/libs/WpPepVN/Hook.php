<?php 
namespace WpPepVN;

use WpPepVN\DependencyInjection
	,WpPepVN\DependencyInjection\Injectable
	,WpPepVN\System
	,WpPepVN\Hash
;

class Hook
{
	const HOOK_PREFIX = WP_PEPVN_NS_SHORT;
	
	public function __construct() 
    {
		
	}
	
	
	/**
	 * Add action
	 */
	 
	public static function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1)
	{
		return add_filter(self::HOOK_PREFIX.'_'.$tag, $function_to_add, $priority, $accepted_args);
	}
	
	public static function has_action($tag, $function_to_check = false)
	{
		return has_filter(self::HOOK_PREFIX.'_'.$tag, $function_to_check);
	}
	
	public static function do_action($tag, $arg = '')
	{
		return do_action(self::HOOK_PREFIX.'_'.$tag, $arg);
	}
	
	public static function do_action_ref_array($tag, $args)
	{
		return do_action_ref_array(self::HOOK_PREFIX.'_'.$tag, $args);
	}
	
	public static function did_action($tag)
	{
		return did_action(self::HOOK_PREFIX.'_'.$tag);
	}
	
	public static function remove_action($tag, $function_to_remove, $priority = 10 )
	{
		return remove_filter( self::HOOK_PREFIX.'_'.$tag, $function_to_remove, $priority );
	}
	
	public static function remove_all_actions($tag, $priority = false) 
	{
		return remove_all_filters(self::HOOK_PREFIX.'_'.$tag, $priority);
	}
	
	/**
	 * Add filter
	 */
	 
	public static function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
	{
		return add_filter(self::HOOK_PREFIX.'_'.$tag, $function_to_add, $priority, $accepted_args);
	}
	
	public static function has_filter($tag, $function_to_check = false)
	{
		return has_filter(self::HOOK_PREFIX.'_'.$tag, $function_to_check);
	}
	
	/* $value = apply_filters( 'example_filter', 'filter me', $arg1, $arg2 ); */
	public static function apply_filters($tag, $value)
	{
		return apply_filters(self::HOOK_PREFIX.'_'.$tag, $value);
	}
	
	public static function remove_filter( $tag, $function_to_remove, $priority = 10 ) 
	{
		return remove_filter( self::HOOK_PREFIX.'_'.$tag, $function_to_remove, $priority );
	}
	
	public static function remove_all_filters( $tag, $priority = false ) 
	{
		return remove_all_filters( self::HOOK_PREFIX.'_'.$tag, $priority );
	}
}

