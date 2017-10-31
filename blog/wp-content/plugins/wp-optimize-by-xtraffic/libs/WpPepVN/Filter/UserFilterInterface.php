<?php 
namespace WpPepVN\Filter;

/**
 * WpPepVN\Filter\UserFilterInterface
 *
 * Interface for WpPepVN\Filter user-filters
 */
interface UserFilterInterface
{

	/**
	 * Filters a value
	 */
	public function filter($value);
}
