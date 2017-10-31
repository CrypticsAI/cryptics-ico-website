<?php 

namespace WpPepVN;

use WpPepVN\Text\Slug as TextSlug
	,WpPepVN\Utils
	,WpPepVN\System
;


/**
 * WpPepVN\Text
 *
 * Provides utilities to work with texts
 */
abstract class Text
{

	const RANDOM_ALNUM = 0;

	const RANDOM_ALPHA = 1;

	const RANDOM_HEXDEC = 2;

	const RANDOM_NUMERIC = 3;

	const RANDOM_NOZERO = 4;
	
	public static $defaultParams = false;
	
	protected static $_tempData = array();
	
	public static function setDefaultParams()
	{
		if(false === self::$defaultParams) {
			self::$defaultParams['status'] = true;
			
			self::$defaultParams['vietnamese_chars'] = array();
			
			$arrayVietnameseChars = array(
				array('à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'đ' => 'd', 'đ' => 'd', 'Ð' => 'D')	//unicode
				, array('µ' => 'a', '¸' => 'a', '¹' => 'a', '¶' => 'a', '·' => 'a', '©' => 'a', 'Ç' => 'a', 'Ê' => 'a', 'Ë' => 'a', 'È' => 'a', 'É' => 'a', '¨' => 'a', '»' => 'a', '¾' => 'a', 'Æ' => 'a', '¼' => 'a', '½' => 'a', 'Ì' => 'e', 'Ð' => 'e', 'Ñ' => 'e', 'Î' => 'e', 'Ï' => 'e', 'ª' => 'e', 'Ò' => 'e', 'Õ' => 'e', 'Ö' => 'e', 'Ó' => 'e', 'Ô' => 'e', '×' => 'i', 'Ý' => 'i', 'Þ' => 'i', 'Ø' => 'i', 'Ü' => 'i', 'ß' => 'o', 'ã' => 'o', 'ä' => 'o', 'á' => 'o', 'â' => 'o', '«' => 'o', 'å' => 'o', 'è' => 'o', 'é' => 'o', 'æ' => 'o', 'ç' => 'o', '¬' => 'o', 'ê' => 'o', 'í' => 'o', 'î' => 'o', 'ë' => 'o', 'ì' => 'o', 'ï' => 'u', 'ó' => 'u', 'ô' => 'u', 'ñ' => 'u', 'ò' => 'u', '­' => 'u', 'õ' => 'u', 'ø' => 'u', 'ù' => 'u', 'ö' => 'u', '÷' => 'u', 'ú' => 'y', 'ý' => 'y', 'þ' => 'y', 'û' => 'y', 'ü' => 'y', '®' => 'd', '®' => 'd', '#' => 'D')	//TCVN 3 (ABC)
				, array('aø' => 'a', 'aù' => 'a', 'aï' => 'a', 'aû' => 'a', 'aõ' => 'a', 'aâ' => 'a', 'aà' => 'a', 'aá' => 'a', 'aä' => 'a', 'aå' => 'a', 'aã' => 'a', 'aê' => 'a', 'aè' => 'a', 'aé' => 'a', 'aë' => 'a', 'aú' => 'a', 'aü' => 'a', 'eø' => 'e', 'eù' => 'e', 'eï' => 'e', 'eû' => 'e', 'eõ' => 'e', 'eâ' => 'e', 'eà' => 'e', 'eá' => 'e', 'eä' => 'e', 'eå' => 'e', 'eã' => 'e', 'ì' => 'i', 'í' => 'i', 'ò' => 'i', 'æ' => 'i', 'ó' => 'i', 'oø' => 'o', 'où' => 'o', 'oï' => 'o', 'oû' => 'o', 'oõ' => 'o', 'oâ' => 'o', 'oà' => 'o', 'oá' => 'o', 'oä' => 'o', 'oå' => 'o', 'oã' => 'o', 'ô' => 'o', 'ôø' => 'o', 'ôù' => 'o', 'ôï' => 'o', 'ôû' => 'o', 'ôõ' => 'o', 'uø' => 'u', 'uù' => 'u', 'uï' => 'u', 'uû' => 'u', 'uõ' => 'u', 'ö' => 'u', 'öø' => 'u', 'öù' => 'u', 'öï' => 'u', 'öû' => 'u', 'öõ' => 'u', 'yø' => 'y', 'yù' => 'y', 'î' => 'y', 'yû' => 'y', 'yõ' => 'y', 'ñ' => 'd', 'ñ' => 'd', 'Ð' => 'D')	//VNI Window
				, array('à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'đ' => 'd', 'đ' => 'd', 'Ð' => 'D')	//Unicode to hop
				, array('à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'đ' => 'd', 'đ' => 'd', '#' => 'D')	//Window CP 1258
				, array('à' => 'a', 'á' => 'a', 'Õ' => 'a', 'ä' => 'a', 'ã' => 'a', 'â' => 'a', '¥' => 'a', '¤' => 'a', '§' => 'a', '¦' => 'a', 'ç' => 'a', 'å' => 'a', '¢' => 'a', '¡' => 'a', '£' => 'a', 'Æ' => 'a', 'Ç' => 'a', 'è' => 'e', 'é' => 'e', '©' => 'e', 'ë' => 'e', '¨' => 'e', 'ê' => 'e', '«' => 'e', 'ª' => 'e', '®' => 'e', '¬' => 'e', '­' => 'e', 'ì' => 'i', 'í' => 'i', '¸' => 'i', 'ï' => 'i', 'î' => 'i', 'ò' => 'o', 'ó' => 'o', '÷' => 'o', 'ö' => 'o', 'õ' => 'o', 'ô' => 'o', '°' => 'o', '¯' => 'o', 'µ' => 'o', '±' => 'o', '²' => 'o', '½' => 'o', '¶' => 'o', '¾' => 'o', 'þ' => 'o', '·' => 'o', 'Þ' => 'o', 'ù' => 'u', 'ú' => 'u', 'ø' => 'u', 'ü' => 'u', 'û' => 'u', 'ß' => 'u', '×' => 'u', 'Ñ' => 'u', 'ñ' => 'u', 'Ø' => 'u', 'æ' => 'u', 'Ï' => 'y', 'ý' => 'y', 'Ü' => 'y', 'Ö' => 'y', 'Û' => 'y', 'ð' => 'd', 'ð' => 'd', '#' => 'D')	//VISCII
				, array('à' => 'a', 'á' => 'a', 'å' => 'a', 'ä' => 'a', 'ã' => 'a', 'â' => 'a', 'À' => 'a', 'Ã' => 'a', 'Æ' => 'a', 'Ä' => 'a', 'Å' => 'a', 'æ' => 'a', '¢' => 'a', '¡' => 'a', '¥' => 'a', '£' => 'a', '¤' => 'a', 'è' => 'e', 'é' => 'e', 'Ë' => 'e', 'È' => 'e', 'ë' => 'e', 'ê' => 'e', 'Š' => 'e', '‰' => 'e', 'Œ' => 'e', '‹' => 'e', 'Í' => 'e', 'ì' => 'i', 'í' => 'i', 'Î' => 'i', 'Ì' => 'i', 'ï' => 'i', 'ò' => 'o', 'ó' => 'o', '†' => 'o', 'Õ' => 'o', 'õ' => 'o', 'ô' => 'o', 'Ò' => 'o', 'Ó' => 'o', '¶' => 'o', '°' => 'o', '‡' => 'o', 'Ö' => 'o', '©' => 'o', '§' => 'o', '®' => 'o', 'ª' => 'o', '«' => 'o', 'ù' => 'u', 'ú' => 'u', 'ø' => 'u', 'û' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ø' => 'u', 'Ù' => 'u', '¿' => 'u', 'º' => 'u', '»' => 'u', 'ÿ' => 'y', 'š' => 'y', 'œ' => 'y', '›' => 'y', 'Ï' => 'y', 'Ç' => 'd', 'Ç' => 'd', '#' => 'D')	//VPS
				, array('aâ' => 'a', 'aá' => 'a', 'aå' => 'a', 'aã' => 'a', 'aä' => 'a', 'ê' => 'a', 'êì' => 'a', 'êë' => 'a', 'êå' => 'a', 'êí' => 'a', 'êî' => 'a', 'ù' => 'a', 'ùç' => 'a', 'ùæ' => 'a', 'ùå' => 'a', 'ùè' => 'a', 'ùé' => 'a', 'eâ' => 'e', 'eá' => 'e', 'eå' => 'e', 'eã' => 'e', 'eä' => 'e', 'ï' => 'e', 'ïì' => 'e', 'ïë' => 'e', 'ïå' => 'e', 'ïí' => 'e', 'ïî' => 'e', 'ò' => 'i', 'ñ' => 'i', 'õ' => 'i', 'ó' => 'i', 'ô' => 'i', 'oâ' => 'o', 'oá' => 'o', 'oå' => 'o', 'oã' => 'o', 'oä' => 'o', 'ö' => 'o', 'öì' => 'o', 'öë' => 'o', 'öå' => 'o', 'öí' => 'o', 'öî' => 'o', 'ú' => 'o', 'úâ' => 'o', 'úá' => 'o', 'úå' => 'o', 'úã' => 'o', 'úä' => 'o', 'uâ' => 'u', 'uá' => 'u', 'uå' => 'u', 'uã' => 'u', 'uä' => 'u', 'û' => 'u', 'ûâ' => 'u', 'ûá' => 'u', 'ûå' => 'u', 'ûã' => 'u', 'ûä' => 'u', 'yâ' => 'y', 'yá' => 'y', 'yå' => 'y', 'yã' => 'y', 'yä' => 'y', 'à' => 'd', 'à' => 'd', 'Ð' => 'D')	//BK HCM 2
				, array('¿' => 'a', '¾' => 'a', 'Â' => 'a', 'À' => 'a', 'Á' => 'a', 'Ý' => 'a', 'ß' => 'a', 'Þ' => 'a', 'â' => 'a', 'à' => 'a', 'á' => 'a', '×' => 'a', 'Ù' => 'a', 'Ø' => 'a', 'Ü' => 'a', 'Ú' => 'a', 'Û' => 'a', 'Ä' => 'e', 'Ã' => 'e', 'Ç' => 'e', 'Å' => 'e', 'Æ' => 'e', 'ã' => 'e', 'å' => 'e', 'ä' => 'e', 'è' => 'e', 'æ' => 'e', 'ç' => 'e', 'É' => 'i', 'È' => 'i', 'Ì' => 'i', 'Ê' => 'i', 'Ë' => 'i', 'Î' => 'o', 'Í' => 'o', 'Ñ' => 'o', 'Ï' => 'o', 'Ð' => 'o', 'é' => 'o', 'ë' => 'o', 'ê' => 'o', 'î' => 'o', 'ì' => 'o', 'í' => 'o', 'ï' => 'o', 'ñ' => 'o', 'ð' => 'o', 'ô' => 'o', 'ò' => 'o', 'ó' => 'o', 'Ó' => 'u', 'Ò' => 'u', 'Ö' => 'u', 'Ô' => 'u', 'Õ' => 'u', 'õ' => 'u', '÷' => 'u', 'ö' => 'u', 'ú' => 'u', 'ø' => 'u', 'ù' => 'u', 'ü' => 'y', 'û' => 'y', 'ÿ' => 'y', 'ý' => 'y', 'þ' => 'y', '½' => 'd', '½' => 'd', '#' => 'D')	//BK HCM 1
				, array('aì' => 'a', 'aï' => 'a', 'aû' => 'a', 'aí' => 'a', 'aî' => 'a', 'á' => 'a', 'áö' => 'a', 'áú' => 'a', 'áû' => 'a', 'áø' => 'a', 'áù' => 'a', 'à' => 'a', 'àò' => 'a', 'àõ' => 'a', 'àû' => 'a', 'àó' => 'a', 'àô' => 'a', 'eì' => 'e', 'eï' => 'e', 'eû' => 'e', 'eí' => 'e', 'eî' => 'e', 'ã' => 'e', 'ãö' => 'e', 'ãú' => 'e', 'ãû' => 'e', 'ãø' => 'e', 'ãù' => 'e', 'ç' => 'i', 'ê' => 'i', 'ë' => 'i', 'è' => 'i', 'é' => 'i', 'oì' => 'o', 'oï' => 'o', 'oü' => 'o', 'oí' => 'o', 'oî' => 'o', 'ä' => 'o', 'äö' => 'o', 'äú' => 'o', 'äü' => 'o', 'äø' => 'o', 'äù' => 'o', 'å' => 'o', 'åì' => 'o', 'åï' => 'o', 'åü' => 'o', 'åí' => 'o', 'åî' => 'o', 'uì' => 'u', 'uï' => 'u', 'uû' => 'u', 'uí' => 'u', 'uî' => 'u', 'æ' => 'u', 'æì' => 'u', 'æï' => 'u', 'æû' => 'u', 'æí' => 'u', 'æî' => 'u', 'yì' => 'y', 'yï' => 'y', 'yñ' => 'y', 'yí' => 'y', 'yî' => 'y', 'â' => 'd', 'â' => 'd', 'Ð' => 'D')	//Vietware X
				, array('ª' => 'a', 'À' => 'a', 'Á' => 'a', '¶' => 'a', 'º' => 'a', '¡' => 'a', 'Ç' => 'a', 'Ê' => 'a', 'Ë' => 'a', 'È' => 'a', 'É' => 'a', 'Ÿ' => 'a', 'Â' => 'a', 'Å' => 'a', 'Æ' => 'a', 'Ã' => 'a', 'Ä' => 'a', 'Ì' => 'e', 'Ï' => 'e', 'Ñ' => 'e', 'Í' => 'e', 'Î' => 'e', '£' => 'e', 'Ò' => 'e', 'Õ' => 'e', 'Ö' => 'e', 'Ó' => 'e', 'Ô' => 'e', 'Ø' => 'i', 'Û' => 'i', 'Ü' => 'i', 'Ù' => 'i', 'Ú' => 'i', 'ß' => 'o', 'â' => 'o', 'ã' => 'o', 'à' => 'o', 'á' => 'o', '¤' => 'o', 'ä' => 'o', 'ç' => 'o', 'è' => 'o', 'å' => 'o', 'æ' => 'o', '¥' => 'o', 'é' => 'o', 'ì' => 'o', 'í' => 'o', 'ê' => 'o', 'ë' => 'o', 'î' => 'u', 'ò' => 'u', 'ó' => 'u', 'ï' => 'u', 'ñ' => 'u', '§' => 'u', 'ô' => 'u', '÷' => 'u', 'ø' => 'u', 'õ' => 'u', 'ö' => 'u', 'ù' => 'y', 'ü' => 'y', 'ÿ' => 'y', 'ú' => 'y', 'û' => 'y', '¢' => 'd', '¢' => 'd', 'Ð' => 'D')	//Vietware F
				, array('Ã ' => 'a', 'Ã¡' => 'a', 'áº¡' => 'a', 'áº£' => 'a', 'Ã£' => 'a', 'Ã¢' => 'a', 'áº§' => 'a', 'áº¥' => 'a', 'áº­' => 'a', 'áº©' => 'a', 'áº«' => 'a', 'Äƒ' => 'a', 'áº±' => 'a', 'áº¯' => 'a', 'áº·' => 'a', 'áº³' => 'a', 'áºµ' => 'a', 'Ã¨' => 'e', 'Ã©' => 'e', 'áº¹' => 'e', 'áº»' => 'e', 'áº½' => 'e', 'Ãª' => 'e', 'á»' => 'e', 'áº¿' => 'e', 'á»‡' => 'e', 'á»ƒ' => 'e', 'á»…' => 'e', 'Ã¬' => 'i', 'Ã­' => 'i', 'á»‹' => 'i', 'á»‰' => 'i', 'Ä©' => 'i', 'Ã²' => 'o', 'Ã³' => 'o', 'á»' => 'o', 'á»' => 'o', 'Ãµ' => 'o', 'Ã´' => 'o', 'á»“' => 'o', 'á»‘' => 'o', 'á»™' => 'o', 'á»•' => 'o', 'á»—' => 'o', 'Æ¡' => 'o', 'á»' => 'o', 'á»›' => 'o', 'á»£' => 'o', 'á»Ÿ' => 'o', 'á»¡' => 'o', 'Ã¹' => 'u', 'Ãº' => 'u', 'á»¥' => 'u', 'á»§' => 'u', 'Å©' => 'u', 'Æ°' => 'u', 'á»«' => 'u', 'á»©' => 'u', 'á»±' => 'u', 'á»­' => 'u', 'á»¯' => 'u', 'á»³' => 'y', 'Ã½' => 'y', 'á»µ' => 'y', 'á»·' => 'y', 'á»¹' => 'y', 'Ä‘' => 'd', 'Ä‘' => 'd', 'Ã' => 'D')	//UTF-8
				, array('&#224;' => 'a', '&#225;' => 'a', '&#7841;' => 'a', '&#7843;' => 'a', '&#227;' => 'a', '&#226;' => 'a', '&#7847;' => 'a', '&#7845;' => 'a', '&#7853;' => 'a', '&#7849;' => 'a', '&#7851;' => 'a', '&#259;' => 'a', '&#7857;' => 'a', '&#7855;' => 'a', '&#7863;' => 'a', '&#7859;' => 'a', '&#7861;' => 'a', '&#232;' => 'e', '&#233;' => 'e', '&#7865;' => 'e', '&#7867;' => 'e', '&#7869;' => 'e', '&#234;' => 'e', '&#7873;' => 'e', '&#7871;' => 'e', '&#7879;' => 'e', '&#7875;' => 'e', '&#7877;' => 'e', '&#236;' => 'i', '&#237;' => 'i', '&#7883;' => 'i', '&#7881;' => 'i', '&#297;' => 'i', '&#242;' => 'o', '&#243;' => 'o', '&#7885;' => 'o', '&#7887;' => 'o', '&#245;' => 'o', '&#244;' => 'o', '&#7891;' => 'o', '&#7889;' => 'o', '&#7897;' => 'o', '&#7893;' => 'o', '&#7895;' => 'o', '&#417;' => 'o', '&#7901;' => 'o', '&#7899;' => 'o', '&#7907;' => 'o', '&#7903;' => 'o', '&#7905;' => 'o', '&#249;' => 'u', '&#250;' => 'u', '&#7909;' => 'u', '&#7911;' => 'u', '&#361;' => 'u', '&#432;' => 'u', '&#7915;' => 'u', '&#7913;' => 'u', '&#7921;' => 'u', '&#7917;' => 'u', '&#7919;' => 'u', '&#7923;' => 'y', '&#253;' => 'y', '&#7925;' => 'y', '&#7927;' => 'y', '&#7929;' => 'y', '&#273;' => 'd', '&#273;' => 'd', '&#208;' => 'D')	//NCR Decimal
				, array('&#xE0;' => 'a', '&#xE1;' => 'a', '&#x1EA1;' => 'a', '&#x1EA3;' => 'a', '&#xE3;' => 'a', '&#xE2;' => 'a', '&#x1EA7;' => 'a', '&#x1EA5;' => 'a', '&#x1EAD;' => 'a', '&#x1EA9;' => 'a', '&#x1EAB;' => 'a', '&#x103;' => 'a', '&#x1EB1;' => 'a', '&#x1EAF;' => 'a', '&#x1EB7;' => 'a', '&#x1EB3;' => 'a', '&#x1EB5;' => 'a', '&#xE8;' => 'e', '&#xE9;' => 'e', '&#x1EB9;' => 'e', '&#x1EBB;' => 'e', '&#x1EBD;' => 'e', '&#xEA;' => 'e', '&#x1EC1;' => 'e', '&#x1EBF;' => 'e', '&#x1EC7;' => 'e', '&#x1EC3;' => 'e', '&#x1EC5;' => 'e', '&#xEC;' => 'i', '&#xED;' => 'i', '&#x1ECB;' => 'i', '&#x1EC9;' => 'i', '&#x129;' => 'i', '&#xF2;' => 'o', '&#xF3;' => 'o', '&#x1ECD;' => 'o', '&#x1ECF;' => 'o', '&#xF5;' => 'o', '&#xF4;' => 'o', '&#x1ED3;' => 'o', '&#x1ED1;' => 'o', '&#x1ED9;' => 'o', '&#x1ED5;' => 'o', '&#x1ED7;' => 'o', '&#x1A1;' => 'o', '&#x1EDD;' => 'o', '&#x1EDB;' => 'o', '&#x1EE3;' => 'o', '&#x1EDF;' => 'o', '&#x1EE1;' => 'o', '&#xF9;' => 'u', '&#xFA;' => 'u', '&#x1EE5;' => 'u', '&#x1EE7;' => 'u', '&#x169;' => 'u', '&#x1B0;' => 'u', '&#x1EEB;' => 'u', '&#x1EE9;' => 'u', '&#x1EF1;' => 'u', '&#x1EED;' => 'u', '&#x1EEF;' => 'u', '&#x1EF3;' => 'y', '&#xFD;' => 'y', '&#x1EF5;' => 'y', '&#x1EF7;' => 'y', '&#x1EF9;' => 'y', '&#x111;' => 'd', '&#x111;' => 'd', '&#xD0;' => 'D')	//NCR Hex
				, array('à' => 'a', 'á' => 'a', '\x1EA1' => '\x61', '\x1EA3' => '\x61', 'ã' => '\x61', 'â' => '\x61', '\x1EA7' => '\x61', '\x1EA5' => '\x61', '\x1EAD' => '\x61', '\x1EA9' => '\x61', '\x1EAB' => '\x61', '\x103' => '\x61', '\x1EB1' => '\x61', '\x1EAF' => '\x61', '\x1EB7' => '\x61', '\x1EB3' => '\x61', '\x1EB5' => '\x61', 'è' => '\x65', 'é' => '\x65', '\x1EB9' => '\x65', '\x1EBB' => '\x65', '\x1EBD' => '\x65', 'ê' => '\x65', '\x1EC1' => '\x65', '\x1EBF' => '\x65', '\x1EC7' => '\x65', '\x1EC3' => '\x65', '\x1EC5' => '\x65', 'ì' => 'i', 'í' => 'i', '\x1ECB' => 'i', '\x1EC9' => 'i', '\x129' => 'i', 'ò' => 'o', 'ó' => 'o', '\x1ECD' => 'o', '\x1ECF' => 'o', 'õ' => 'o', 'ô' => 'o', '\x1ED3' => 'o', '\x1ED1' => 'o', '\x1ED9' => 'o', '\x1ED5' => 'o', '\x1ED7' => 'o', '\x1A1' => 'o', '\x1EDD' => 'o', '\x1EDB' => 'o', '\x1EE3' => 'o', '\x1EDF' => 'o', '\x1EE1' => 'o', 'ù' => 'u', 'ú' => 'u', '\x1EE5' => 'u', '\x1EE7' => 'u', '\x169' => 'u', '\x1B0' => 'u', '\x1EEB' => 'u', '\x1EE9' => 'u', '\x1EF1' => 'u', '\x1EED' => 'u', '\x1EEF' => 'u', '\x1EF3' => 'y', 'ý' => 'y', '\x1EF5' => 'y', '\x1EF7' => 'y', '\x1EF9' => 'y', '\x111' => '\x64', '\x111' => '\x64', 'Ð' => '\x44')	//Unicode C String
			);
			
			foreach($arrayVietnameseChars as $key1 => $value1) {
				unset($arrayVietnameseChars[$key1]);
				self::$defaultParams['vietnamese_chars'][] = array(
					'mark' => array_keys($value1)
					,'unmark' => array_values($value1)
				);
				unset($key1,$value1);
			}
			unset($arrayVietnameseChars);
			
			self::$defaultParams['mb_internal_encoding'] = mb_internal_encoding();
			
		}
	}
	
	/**
	 * Converts strings to camelize style
	 *
	 * <code>
	 *    echo WpPepVN\Text::camelize('coco_bongo'); //CocoBongo
	 echo WpPepVN\Text::camelize('coco-bongo'); //CocoBongo
	 * </code>
	 */
	public static function camelize($str)
	{
		return str_replace(
            ' ',
            '',
            ucwords(
                preg_replace('/[^a-z0-9]+/i',' ',$str)
            )
        );
	}

	/**
	 * Uncamelize strings which are camelized
	 *
	 * <code>
	 *    echo WpPepVN\Text::uncamelize('CocoBongo'); //coco_bongo
	 * </code>
	 */
	public static function uncamelize($str)
	{
		return strtolower(str_replace(' ', '_',trim(preg_replace('/([A-Z])/',' $1',$str))));
	}

	/**
	 * Adds a number to a string or increment that number if it already is defined
	 *
	 * <code>
	 *    echo WpPepVN\Text::increment('a'); // 'a_1'
	 *    echo WpPepVN\Text::increment('a_1'); // 'a_2'
	 * </code>
	 */
	public static function increment($str, $separator = null) 
	{
		
		if ($separator === null) {
			$separator = '_';
		}

		$parts = explode($separator, $str);

		
		if(isset($parts[1])) {
			$number = $parts[1]+1;
		} else {
			$number = 1;
		}

		return $parts[0] . $separator. $number;
	}

	/**
	 * Generates a random string based on the given type. Type is one of the RANDOM_* constants
	 *
	 * <code>
	 *    echo WpPepVN\Text::random(WpPepVN\Text::RANDOM_ALNUM); //'aloiwkqz'
	 * </code>
	 */
	public static function random($type = 0, $length = 8)
	{
		$str = '';
		
		switch ($type) {

			case Text::RANDOM_ALPHA:
				$pool = array_merge(range('a', 'z'), range('A', 'Z'));
				break;

			case Text::RANDOM_HEXDEC:
				$pool = array_merge(range(0, 9), range('a', 'f'));
				break;

			case Text::RANDOM_NUMERIC:
				$pool = range(0, 9);
				break;

			case Text::RANDOM_NOZERO:
				$pool = range(1, 9);
				break;

			default:
				// Default type \WpPepVN\Text::RANDOM_ALNUM
				$pool = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
				break;
		}

		$end = count($pool) - 1;

		while (strlen($str) < $length) {
			$str .= $pool[mt_rand(0, $end)];
		}

		return $str;
	}

	/**
	 * Lowercases a string, this function makes use of the mbstring extension if available
	 *
	 * <code>
	 *    echo WpPepVN\Text::lower('HELLO'); // hello
	 * </code>
	 */
	public static function lower($str, $encoding = 'UTF-8')
	{
		/**
		 * 'lower' checks for the mbstring extension to make a correct lowercase transformation
		 */
		if (function_exists('mb_strtolower')) {
			return mb_strtolower($str, $encoding);
		}
		return strtolower($str);
	}

	/**
	 * Uppercases a string, this function makes use of the mbstring extension if available
	 *
	 * <code>
	 *    echo WpPepVN\Text::upper('hello'); // HELLO
	 * </code>
	 */
	public static function upper($str, $encoding = 'UTF-8')
	{
		/**
		 * 'upper' checks for the mbstring extension to make a correct lowercase transformation
		 */
		if (function_exists('mb_strtoupper')) {
			return mb_strtoupper($str, $encoding);
		}
		return strtoupper($str);
	}

	/**
	 * Reduces multiple slashes in a string to single slashes
	 *
	 * <code>
	 *    echo WpPepVN\Text::reduceSlashes('foo//bar/baz'); // foo/bar/baz
	 *    echo WpPepVN\Text::reduceSlashes('http://foo.bar///baz/buz'); // http://foo.bar/baz/buz
	 * </code>
	 */
	public static function reduceSlashes($str)
	{
		return preg_replace('#(?<!:)//+#', '/', $str);
	}

	/**
	 * Concatenates strings using the separator only once without duplication in places concatenation
	 *
	 * <code>
	 *    $str = WpPepVN\Text::concat('/', '/tmp/', '/folder_1/', '/folder_2', 'folder_3/');
	 *    echo $str; // /tmp/folder_1/folder_2/folder_3/
	 * </code>
	 *
	 * @param string separator
	 * @param string a
	 * @param string b
	 * @param string ...N
	 */
	//public static function concat(string! separator, string! a, string! b) -> string
	public static function concat()
	{
		
		$separator = func_get_arg(0);
		$a = func_get_arg(1);
		$b = func_get_arg(2);
		
		if (func_num_args() > 3) {
			foreach(array_slice(func_get_args(), 3) as $c) {
				$b = rtrim($b, $separator) . $separator . ltrim($c, $separator);
			}
		}

		return rtrim($a, $separator) . $separator . ltrim($b, $separator);
	}

	/**
	 * Generates random text in accordance with the template
	 *
	 * <code>
	 *    echo WpPepVN\Text::dynamic('{Hi|Hello}, my name is a {Bob|Mark|Jon}!'); // Hi my name is a Bob
	 *    echo WpPepVN\Text::dynamic('{Hi|Hello}, my name is a {Bob|Mark|Jon}!'); // Hi my name is a Jon
	 *    echo WpPepVN\Text::dynamic('{Hi|Hello}, my name is a {Bob|Mark|Jon}!'); // Hello my name is a Bob
	 * </code>
	 */
	public static function dynamic($text, $leftDelimiter = '{', $rightDelimiter = '}', $separator = '|')
	{
		
		if (substr_count($text, $leftDelimiter) !== substr_count($text, $rightDelimiter)) {
			throw new \RuntimeException('Syntax error in string \"' . $text . '\"');
		}

		$ld_s 	= preg_quote($leftDelimiter);
		$rd_s 	= preg_quote($rightDelimiter);
		$pattern = '/' . $ld_s . '([^' . $ld_s . $rd_s . ']+)' . $rd_s . '/';
		$result 	= $text;
		
		while(false !== strpos($result, $leftDelimiter)) {
			$result = preg_replace_callback($pattern, function ($matches) {
				$words = explode('|', $matches[1]);
				return $words[array_rand($words)];
			}, $result);
		}

		return $result;
	}
	
	public static function strtolower($input_text,$input_encoding = 'UTF-8') 
	{
		if(System::function_exists('mb_convert_case')) {
			return mb_convert_case($input_text, MB_CASE_LOWER, $input_encoding);
		} else {
			return strtolower($input_text);
		}
	}
	
	public static function strtoupper($input_text,$input_encoding = 'UTF-8') 
	{
		if(System::function_exists('mb_convert_case')) {
			return mb_convert_case($input_text, MB_CASE_UPPER, $input_encoding);
		} else {
			return strtolower($input_text);
		}
	}
	
	public static function toSlug($string, $separator = '-', $replace = null, $lowercase = true)
	{
		$string = self::decodeText($string);
		
		$string = self::removeVietnameseMark($string);
		
		if(System::function_exists('remove_accents')) {
			$string = remove_accents($string);
		}
		
		return TextSlug::generate($string, $separator, $replace, $lowercase);
	}
	
	public static function reduceSpace($string) 
	{
		$string = preg_replace('#[ \t]+#i', ' ', $string);
		$string = trim($string);
		return $string;
	}
	
	public static function removeSpace($string, $replace = '') 
	{
		$string = preg_replace('#[\s \t]+#i', $replace, $string);
		$string = trim($string);
		return $string;
	}
	
	public static function reduceLine($string) 
	{
		$string = preg_replace('#(\r?\n){2,}#is', PHP_EOL, $string);
		$string = trim($string);
		return $string;
	}
	
	public static function removeLine($string, $replace = '') 
	{
		$string = preg_replace('#(\r?\n)+#is', $replace, $string);
		$string = trim($string);
		return $string;
	}
	
	public static function replaceSpecialChar($input_text, $input_replace_char = ' ', $input_except = '')
	{
		$pattern = '`~!@#$%^&*()-_=+{}[]\\\|;:\'",.<>/?+';
		
		if(!empty($input_except)) {
			$pattern = preg_replace('#['.preg_quote($input_except,'#').']+#is','',$pattern);
		}
		
		return preg_replace('#['.preg_quote($pattern,'#').']+#is',$input_replace_char,$input_text);
	}
	
	public static function removeShortcode($string, $replace = '') 
	{
		$string = preg_replace('#\[([a-z0-9\_]+)[^\]]*\](\[/\1\])?#is', $replace, $string);
		$string = trim($string);
		return $string;
	}
	
	public static function toOneLine($string)
	{
		$string = self::removeLine($string, ' ');
		$string = self::reduceSpace($string, ' ');
		$string = trim($string);
		
		return $string;
	}
	
	
	public static function reduceChars($string, $maximumChars = 250, $moreString = '...') 
	{
		$string = strip_tags($string);
		
		$string = self::toOneLine($string);
		
		$string = str_split($string);
		$string = array_slice($string, 0, ($maximumChars+1));
		if(isset($string[$maximumChars])) {
			unset($string[$maximumChars]);
			$string[($maximumChars-1)] = $moreString;
		}
		
		$string = implode('',$string);
		
		$string = trim($string);
		
		return $string;
	}
	
	public static function reduceWords($string, $maximumWords = 250, $moreString = '...') 
	{
		$string = strip_tags($string);
		
		$string = self::toOneLine($string);
		
		$string = explode(' ',$string, ($maximumWords+1));
		if(isset($string[$maximumWords])) {
			unset($string[$maximumWords]);
			$string[($maximumWords-1)] = $moreString;
		}
		$string = implode(' ',$string);
		
		$string = trim($string);
		
		return $string;
	}
	
	
	public static function removeVietnameseMark($text)
	{
		foreach(self::$defaultParams['vietnamese_chars'] as $key1 => $value1) {
			$text = str_replace($value1['mark'],$value1['unmark'],$text);
			unset($key1,$value1);
		}
		reset(self::$defaultParams['vietnamese_chars']);
		
		return $text;
	}
	
	public static function decodeText($input_text)
	{
		$input_text = rawurldecode($input_text);
		$input_text = html_entity_decode($input_text, ENT_QUOTES, 'UTF-8');
		return $input_text;
	}
	
	public static function removeQuotes($s,$r='')
	{
		$s = (string)$s;
		return preg_replace('#[\'\"]+#is',$r,$s);
	}
	
	
	public static function safeText(
		$s			//Text
		, $e = ''	//exceptSpecialChar
	)
	{
		$s = self::decodeText($s);
		$s = strip_tags($s);
		$s = self::removeQuotes($s);
		$s = self::replaceSpecialChar($s, ' ', $e);
		$s = self::reduceLine($s);
		$s = self::reduceSpace($s);
		
		return $s;
	}
	
	
	public static function similar_text($s1,$s2)
	{
		
		$k = Utils::hashKey(array(
			__CLASS__ . __METHOD__
			,$s1
			,$s2
		));
		
		if(isset(self::$_tempData[$k])) {
			return self::$_tempData[$k];
		} else {
			self::$_tempData[$k] = 0;	//0:not similar, 100 : exact (percent)
			
			$totalPercents = array();
			
			$s1 = (string)$s1;
			$s2 = (string)$s2;
			
			$s1 = self::safeText($s1);
			$s2 = self::safeText($s2);
			
			similar_text($s1,$s2,$percent);
			$totalPercents[] = (float)$percent;
			
			similar_text($s2,$s1,$percent);
			$totalPercents[] = (float)$percent;
			
			$s1_tmp = self::toSlug($s1,' ');
			$s2_tmp = self::toSlug($s2,' ');
			
			similar_text($s1_tmp,$s2_tmp,$percent);
			$totalPercents[] = (float)$percent;
			
			similar_text($s2_tmp,$s1_tmp,$percent);
			$totalPercents[] = (float)$percent;
			
			self::$_tempData[$k] = array_sum($totalPercents) / count($totalPercents);
			
			/*
			$s1_levenshtein = mb_substr($s1,0,255,'UTF-8');
			$s2_levenshtein = mb_substr($s2,0,255,'UTF-8');
			
			$percent = levenshtein($s1_levenshtein, $s2_levenshtein);
			if($percent >= 0) {
			}
			*/
			
			self::$_tempData[$k] = (float)self::$_tempData[$k];
			
			return self::$_tempData[$k];
		}
		
	}
	
	public static function substr($string, $start, $length = NULL, $encoding = NULL)
	{
		if(!$encoding) {
			$encoding = self::$defaultParams['mb_internal_encoding'];
		}
		return mb_substr($string, $start, $length, $encoding);
	}
	
	public static function strlen($string, $encoding = NULL)
	{
		if(!$encoding) {
			$encoding = self::$defaultParams['mb_internal_encoding'];
		}
		
		return mb_strlen($string, $encoding);
	}
	
}

Text::setDefaultParams();
