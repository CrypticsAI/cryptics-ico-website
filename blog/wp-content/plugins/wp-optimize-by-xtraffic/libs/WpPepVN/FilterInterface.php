<?php 
namespace WpPepVN;

/**
 * WpPepVN\FilterInterface
 *
 * Interface for WpPepVN\Filter
 */
interface FilterInterface
{

	/**
	 * Adds a user-defined filter
	 */
	public function add($name, $handler);

	/**
	 * Sanizites a value with a specified single or set of filters
	 */
	public function sanitize($value, $filters);

	/**
	 * Return the user-defined filters in the instance
	 */
	public function getFilters();
}
