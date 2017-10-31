<?php 

namespace WpPepVN;

use WpPepVN\EscaperInterface
	,WpPepVN\Exception
	,WpPepVN\System
;

/**
 * WpPepVN\Escaper
 *
 * Escapes different kinds of text securing them. By using this component you may
 * prevent XSS attacks.
 *
 * This component only works with UTF-8. The PREG extension needs to be compiled with UTF-8 support.
 *
 *<code>
 *	$escaper = new \WpPepVN\Escaper();
 *	$escaped = $escaper->escapeCss("font-family: <Verdana>");
 *	echo $escaped; // font\2D family\3A \20 \3C Verdana\3E
 *</code>
 */
class Escaper implements EscaperInterface
{

	protected $_encoding = "utf-8";

	protected $_htmlEscapeMap = null;

	protected $_htmlQuoteType = 3;

	/**
	 * Sets the encoding to be used by the escaper
	 *
	 *<code>
	 * $escaper->setEncoding('utf-8');
	 *</code>
	 */
	public function setEncoding($encoding)
	{
		$this->_encoding = $encoding;
	}

	/**
	 * Returns the internal encoding used by the escaper
	 */
	public function getEncoding()
	{
		return $this->_encoding;
	}

	/**
	 * Sets the HTML quoting type for htmlspecialchars
	 *
	 *<code>
	 * $escaper->setHtmlQuoteType(ENT_XHTML);
	 *</code>
	 */
	public function setHtmlQuoteType($quoteType)
	{
		$this->_htmlQuoteType = (int) $quoteType;
	}

	/**
	 * Detect the character encoding of a string to be handled by an encoder
	 * Special-handling for chr(172) and chr(128) to chr(159) which fail to be detected by mb_detect_encoding()
	 */
	public final function detectEncoding($str)
	{
		/**
		* We require mbstring extension here
		*/
		if (!System::function_exists("mb_detect_encoding")) {
			return null;
		}

		/**
		 * Strict encoding detection with fallback to non-strict detection.
		 * Check encoding
		 */
		$arrayTemp = array("UTF-32", "UTF-8", "ISO-8859-1", "ASCII");
		foreach($arrayTemp as $charset) {
			if (mb_detect_encoding($str, $charset, true)) {
				return $charset;
			}
		}

		/**
		 * Fallback to global detection
		 */
		return mb_detect_encoding($str);
	}

	/**
	 * Utility to normalize a string's encoding to UTF-32.
	 */
	public final function normalizeEncoding($str)
	{
		/**
		 * mbstring is required here
		 */
		if (!System::function_exists("mb_convert_encoding")) {
			throw new Exception("Extension \'mbstring\' is required");
		}

		/**
		 * Convert to UTF-32 (4 byte characters, regardless of actual number of bytes in
		 * the character).
		 */
		return mb_convert_encoding($str, "UTF-32", $this->detectEncoding($str));
	}

	/**
	 * Escapes a HTML string. Internally uses htmlspecialchars
	 */
	public function escapeHtml($text) 
	{
		return htmlspecialchars($text, $this->_htmlQuoteType, $this->_encoding);
	}

	/**
	 * Escapes a HTML attribute string
	 */
	public function escapeHtmlAttr($attribute) 
	{
		return htmlspecialchars($attribute, ENT_QUOTES, $this->_encoding);
	}

	/**
	 * Escape CSS strings by replacing non-alphanumeric chars by their hexadecimal escaped representation
	 */
	public function escapeCss($css)
	{
		/**
		 * Normalize encoding to UTF-32
		 * Escape the string
		 */
		return $this->normalizeEncoding($css);
	}

	/**
	 * Escape javascript strings by replacing non-alphanumeric chars by their hexadecimal escaped representation
	 */
	public function escapeJs($js)
	{
		/**
		 * Normalize encoding to UTF-32
		 * Escape the string
		 */
		return $this->normalizeEncoding($js);
	}

	/**
	 * Escapes a URL. Internally uses rawurlencode
	 */
	public function escapeUrl($url)
	{
		return rawurlencode($url);
	}
}
