<?php
namespace WPOptimizeByxTraffic\Application\Service;
/*
 * PepVN_Captcha v1.0
 *
 * By PEP.VN
 * http://pep.vn/
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */
 


class PepVN_Captcha 
{
	
	static $defaultParams = false;
	
	
	public function __construct() 
	{
		
		self::setDefaultParams();
	}
	
	
	static function setDefaultParams()
	{
		if(!self::$defaultParams) {
			
			self::$defaultParams['captcha']['charset'] = 'ABCEFGHKLMNPRTWY';
			self::$defaultParams['captcha']['min_length_string'] = 4;
			self::$defaultParams['captcha']['max_length_string'] = 4;
			self::$defaultParams['captcha']['image_width'] = 175;
			self::$defaultParams['captcha']['image_height'] = 50;
			
			self::$defaultParams['captcha']['notice']['error']['wrong_captcha'] = __('You entered the wrong captcha value!',WPOPTIMIZEBYXTRAFFIC_PLUGIN_SLUG);
			self::$defaultParams['captcha']['notice']['error']['not_entered_captcha'] = __('You have not entered the captcha value!',WPOPTIMIZEBYXTRAFFIC_PLUGIN_SLUG);
			self::$defaultParams['captcha']['notice']['error']['unknown'] = __('Captcha failed! Please try another captcha value!',WPOPTIMIZEBYXTRAFFIC_PLUGIN_SLUG);
		}
	}
	
	
	static function checkCaptchaInputValue($captchaInputValue)
	{
		$resultData = array();
		$resultData['status'] = 0; 
		
		if(!isset($_SESSION['pepvn_captcha']['value'])) {
			$_SESSION['pepvn_captcha']['value'] = 0;
		}
		
		$captchaSessionValue = $_SESSION['pepvn_captcha']['value'];
		
		$_SESSION['pepvn_captcha']['value'] = self::createCaptchaString();
		
		$captchaSessionValue = trim($captchaSessionValue);
		$captchaSessionValue = self::cleanCaptchaValue($captchaSessionValue);
		
		$captchaInputValue = (string)$captchaInputValue;
		$captchaInputValue = self::cleanCaptchaValue($captchaInputValue);
		$captchaInputValue = trim($captchaInputValue);
		
		if(strlen($captchaInputValue)>0) {
			if(strlen($captchaSessionValue)>0) {
				$captchaInputValue = md5(strtolower($captchaInputValue));
				$captchaSessionValue = md5(strtolower($captchaSessionValue));
				
				if($captchaInputValue === $captchaSessionValue) {
					$resultData['status'] = 1;
				} else {
					$resultData['notice']['error'][] = self::$defaultParams['captcha']['notice']['error']['wrong_captcha'];
				}
			} else {
				$resultData['notice']['error'][] = self::$defaultParams['captcha']['notice']['error']['unknown'];
			}
		} else {
			$resultData['notice']['error'][] = self::$defaultParams['captcha']['notice']['error']['not_entered_captcha'];
		}
		
		return $resultData;
	}

	
	
	static function createRandomFontPath()
	{
		
		$allFontsPath = WPOPTIMIZEBYXTRAFFIC_PATH.'inc/fonts/captcha/*.ttf'; 
		$arrayFonts = glob($allFontsPath);
		$randomKeyFont = array_rand($arrayFonts,1);
		$randomFont = $arrayFonts[$randomKeyFont];
		
		return $randomFont;
	}
	
	static function cleanCaptchaValue($input_data)
	{
		$input_data = (string)$input_data;
		$input_data = preg_replace('#[\t \s]+#is','',$input_data);
		$input_data = trim($input_data);
		return $input_data;
		
	}
	
	static function createCaptchaString($input_parameters = false)
	{
		$resultData = '';
		
		$string_set = '';
		$min_length = 0;
		$max_length = 0;
		
		if(isset($input_parameters['string_set'])) {
			$string_set = (string)$input_parameters['string_set'];
		}
		
		if(isset($input_parameters['min_length'])) {
			$min_length = (int)$input_parameters['min_length'];
		}
		
		if(isset($input_parameters['max_length'])) {
			$max_length = (int)$input_parameters['max_length'];
		}
		
		$string_set = self::cleanCaptchaValue($string_set);
		if(strlen($string_set)<1) {
			$string_set = self::$defaultParams['captcha']['charset'];
		}
		
		//$string_set = PepVN_Data::strtolower($string_set).$string_set.PepVN_Data::strtoupper($string_set);
		
		if($min_length < 1) {
			$min_length = self::$defaultParams['captcha']['min_length_string'];
		}
		
		if($max_length < 1) {
			$max_length = self::$defaultParams['captcha']['max_length_string'];
		}
		
		if($min_length > $max_length) {
			$min_length = $max_length;
		}
		
		$resultData = PepVN_Data::randomString(array(
			'string_set' => $string_set
			,'min_length' => $min_length
			,'max_length' => $max_length
		));
		
		$resultData = PepVN_Data::strtoupper($resultData);
		
		return $resultData;
	}
	
	static function createCaptchaImage($input_parameters)
	{
	
		$fontPath = self::createRandomFontPath();
		
		$imageCaptchaWidth = self::$defaultParams['captcha']['image_width'];
		$imageCaptchaHeight = self::$defaultParams['captcha']['image_height'];
		$captchaString = $input_parameters['captcha_string'];
		
		$fontSize = $imageCaptchaHeight * 0.71;
		$fontSize = (int)$fontSize;
		
		$captchaImageResource = imagecreate($imageCaptchaWidth, $imageCaptchaHeight);
		
		/* set the colours */
		$backgroundColor = imagecolorallocate($captchaImageResource, 255, 255, 255);
		
		$textColor = imagecolorallocate($captchaImageResource, 20, 40, 100);
		//$textColor = imagecolorallocate($captchaImageResource, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
		
		$noise_color = imagecolorallocate($captchaImageResource, 100, 120, 180);
		//$noise_color = imagecolorallocate($captchaImageResource, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
		
		
		/* generate random dots in background */
		$numberTempOne = ($imageCaptchaWidth * $imageCaptchaHeight)/3;
		$numberTempOne = ($imageCaptchaWidth * $imageCaptchaHeight)/2;
		for($iOne=0;$iOne<$numberTempOne;++$iOne) {
			imagefilledellipse($captchaImageResource, mt_rand(0,$imageCaptchaWidth), mt_rand(0,$imageCaptchaHeight), 1, 1, $noise_color);
		}
		
		/* generate random lines in background */
		$numberTempOne = ($imageCaptchaWidth * $imageCaptchaHeight)/150;
		$numberTempOne = ($imageCaptchaWidth * $imageCaptchaHeight)/50;
		for($iOne=0;$iOne<$numberTempOne;++$iOne) {
			imageline($captchaImageResource, mt_rand(0,$imageCaptchaWidth), mt_rand(0,$imageCaptchaHeight), mt_rand(0,$imageCaptchaWidth), mt_rand(0,$imageCaptchaHeight), $noise_color);
		}
		
		/* create textbox and add text */
		$textbox = imagettfbbox($fontSize, 0, $fontPath, $captchaString);
		$x = ($imageCaptchaWidth - $textbox[4])/2;
		$y = ($imageCaptchaHeight - $textbox[5])/2;
		imagettftext($captchaImageResource, $fontSize, 0, $x, $y, $textColor, $fontPath , $captchaString);
		
		
		/* output image */
		header("Expires: Sun, 1 Jan 1990 12:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Pragma: no-cache");
		
		header("Content-Type: image/jpeg");
		imagejpeg($captchaImageResource, null, 60);
		
		imagedestroy($captchaImageResource);
		ob_end_flush();exit();
		
	}
	
	
	static function showCaptchaImage()
	{
		$captchaString = false;
		
		if(isset($_SESSION['pepvn_captcha']['value']) && $_SESSION['pepvn_captcha']['value']) {
			if(isset($_SESSION['pepvn_captcha']['lasttime']) && $_SESSION['pepvn_captcha']['lasttime']) {
				$_SESSION['pepvn_captcha']['lasttime'] = (int)$_SESSION['pepvn_captcha']['lasttime'];
				if($_SESSION['pepvn_captcha']['lasttime']>0) {
					if($_SESSION['pepvn_captcha']['lasttime'] <= ( time() - 3)) {	//is timeout
						
					} else {
						$captchaString = $_SESSION['pepvn_captcha']['value'];
					}
				}
				
			}
		}
		
		if(!$captchaString) {
			$captchaString = self::createCaptchaString();
			$_SESSION['pepvn_captcha']['value'] = $captchaString;
			$_SESSION['pepvn_captcha']['lasttime'] = time();
		}
		
		self::createCaptchaImage(array(
			'captcha_string' => $captchaString
		));
	}
	

}//class PepVN_Captcha

PepVN_Captcha::setDefaultParams();


