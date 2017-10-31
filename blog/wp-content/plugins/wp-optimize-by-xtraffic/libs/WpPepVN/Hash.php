<?php
namespace WpPepVN;

use WpPepVN\System
	,WpPepVN\Exception
;

class Hash 
{
	protected static $_tempData = array();
	
	public static function has_algos($algorithm_name)
	{
		if(!isset(self::$_tempData['hash_algos'])) {
			self::$_tempData['hash_algos'] = array();
			if(System::function_exists('hash_algos')) {
				self::$_tempData['hash_algos'] = hash_algos();
				self::$_tempData['hash_algos'] = array_flip(self::$_tempData['hash_algos']);
			}
		}
		
		return isset(self::$_tempData['hash_algos'][$algorithm_name]);
		
	}
	
	public static function sha256($data, $raw_output = false)
	{
		$algo = 'sha256';
		
		if(!self::has_algos($algo)) {
			throw new Exception(sprintf('ERROR : Hashing algorithms "%s" not exist on your system. Please see details here "%s".', $algo , 'http://php.net/manual/en/function.hash.php'));
			return false;
		}
		
		return hash($algo, (string)$data, (bool)$raw_output);
	}
	
	
	public static function crc32b($data, $raw_output = false)
	{
		$algo = 'crc32b';
		
		return hash($algo, (string)$data, (bool)$raw_output);
		
	}

	/*
	Begin Crc64
	*/
	/**
	* @return array
	*/
	private static function _crc64Table()
	{
		
		if(isset(self::$_tempData['_crc64Table'])) {
			return self::$_tempData['_crc64Table'];
		}
		
		$crc64tab = array();

		// ECMA polynomial
		$poly64rev = (0xC96C5795 << 32) | 0xD7870F42;

		// ISO polynomial
		// $poly64rev = (0xD8 << 56);

		for ($i = 0; $i < 256; $i++) {
			for ($part = $i, $bit = 0; $bit < 8; $bit++) {
				if ($part & 1) {
					$part = (($part >> 1) & ~(0x8 << 60)) ^ $poly64rev;
				} else {
					$part = ($part >> 1) & ~(0x8 << 60);
				}
			}

			$crc64tab[$i] = $part;
		}
		
		self::$_tempData['_crc64Table'] = $crc64tab;

		return $crc64tab;
	}

	/**
	* @param string $string
	* @param string $format
	* @return mixed
	* 
	* Formats:
	*  crc64('php'); // afe4e823e7cef190
	*  crc64('php', '0x%x'); // 0xafe4e823e7cef190
	*  crc64('php', '0x%X'); // 0xAFE4E823E7CEF190
	*  crc64('php', '%d'); // -5772233581471534704 signed int
	*  crc64('php', '%u'); // 12674510492238016912 unsigned int
	*/
	public static function crc64($string, $format = '%x')
	{
		
		$crc64tab = self::_crc64Table();
		
		$crc = 0;

		for ($i = 0; $i < strlen($string); $i++) {
			$crc = $crc64tab[($crc ^ ord($string[$i])) & 0xff] ^ (($crc >> 8) & ~(0xff << 56));
		}

		return sprintf($format, $crc);
	}
	
}
