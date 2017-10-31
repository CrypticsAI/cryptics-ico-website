<?php 
namespace WpPepVN\Text;

use WpPepVN\System
;

class Slug
{
    /**
     * Creates a slug to be used for pretty URLs.
     *
     * @link http://pepvn.com/the-perfect-php-clean-url-generator
     * @param                     $string
     * @param  array              $replace
     * @param  string             $separator
     * @return mixed
     * @throws \WpPepVN\Exception
     */
    public static function generate($string, $separator = '-', $replace = null, $lowercase = true)
    {
		
		if (System::extension_loaded('iconv')) {
			/**
			 * Save the old locale and set the new locale to UTF-8
			 */
			$oldLocale = setlocale(LC_ALL, 'en_US.UTF-8');
			$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		}

		if ($replace) {
			if (is_array($replace) || is_string($replace)) {
				$string = str_replace((array) $replace, ' ', $string);
			}
		}
		
		$string = preg_replace('#[^a-zA-Z0-9/_\|\+ \-]+#is', ' ', $string);
		
		if ($lowercase) {
			$string = strtolower($string);
		}
		
		$string = trim($string);

		$string = preg_replace('#[\s \t\-/_\|\+]+#', $separator, $string);
		
		$string = trim($string, $separator);

		if (System::extension_loaded('iconv')) {
			/**
			 * Revert back to the old locale
			 */
			setlocale(LC_ALL, $oldLocale);
		}
		
		return $string;
    }
}