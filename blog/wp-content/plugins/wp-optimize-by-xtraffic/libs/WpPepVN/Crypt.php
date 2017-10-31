<?php 
namespace WpPepVN;

use WpPepVN\CryptInterface
	,WpPepVN\Crypt\Exception
;

/**
 * WpPepVN\Crypt
 *
 * Provides encryption facilities to phalcon applications
 *
 *<code>
 *	$crypt = new \WpPepVN\Crypt();
 *
 *	$key = 'le password';
 *	$text = 'This is a secret text';
 *
 *	$encrypted = $crypt->encrypt($text, $key);
 *
 *	echo $crypt->decrypt($encrypted, $key);
 *</code>
 */
class Crypt implements CryptInterface
{

	protected $_key;

	protected $_padding = 0;

	protected $_mode = 'cbc';

	protected $_cipher = 'rijndael-256';

	const PADDING_DEFAULT = 0;

	const PADDING_ANSI_X_923 = 1;

	const PADDING_PKCS7 = 2;

	const PADDING_ISO_10126 = 3;

	const PADDING_ISO_IEC_7816_4 = 4;

	const PADDING_ZERO = 5;

	const PADDING_SPACE = 6;

	/**
	* @brief WpPepVN\CryptInterface WpPepVN\Crypt::setPadding(int $scheme)
	*
	* @param int scheme Padding scheme
	*/
	public function setPadding($scheme)
	{
		$this->_padding = (int)$scheme;
		return $this;
	}

	/**
	 * Sets the cipher algorithm
	 */
	public function setCipher($cipher)
	{
		$this->_cipher = $cipher;
		return $this;
	}

	/**
	 * Returns the current cipher
	 */
	public function getCipher()
	{
		return $this->_cipher;
	}

	/**
	 * Sets the encrypt/decrypt mode
	 */
	public function setMode($mode)
	{
		$this->_mode = $mode;
		return $this;
	}

	/**
	 * Returns the current encryption mode
	 */
	public function getMode()
	{
		return $this->_mode;
	}

	/**
	 * Sets the encryption key
	 */
	public function setKey($key)
	{
		$this->_key = $key;
		return $this;
	}

	/**
	 * Returns the encryption key
	 */
	public function getKey() 
	{
		return $this->_key;
	}

	/**
	 * Adds padding @a padding_type to @a text
	 *
	 * @param return_value Result, possibly padded
	 * @param text Message to be padded
	 * @param mode Encryption mode; padding is applied only in CBC or ECB mode
	 * @param block_size Cipher block size
	 * @param padding_type Padding scheme
	 * @see http://www.di-mgt.com.au/cryptopad.html
	 */
	protected function _cryptPadText($text, $mode, $blockSize, $paddingType)
	{
		
		$paddingSize = 0; $padding = null;

		if (($mode === 'cbc') || ($mode === 'ecb')) {

			$paddingSize = $blockSize - (strlen($text) % $blockSize);
			if ($paddingSize >= 256) {
				throw new Exception('Block size is bigger than 256');
			}

			switch ($paddingType) {

				case self::PADDING_ANSI_X_923:
					$padding = str_repeat(chr(0), $paddingSize - 1) . chr($paddingSize);
					break;

				case self::PADDING_PKCS7:
					$padding = str_repeat(chr($paddingSize), $paddingSize);
					break;

				case self::PADDING_ISO_10126:
					$padding = '';
					foreach(range(0, $paddingSize - 2) as $i) {
						$padding .= chr(rand());
					}
					$padding .= chr($paddingSize);
					break;

				case self::PADDING_ISO_IEC_7816_4:
					$padding = chr(0x80) . str_repeat(chr(0), $paddingSize - 1);
					break;

				case self::PADDING_ZERO:
					$padding = str_repeat(chr(0), $paddingSize);
					break;

				case self::PADDING_SPACE:
					$padding = str_repeat(' ', $paddingSize);
					break;

				default:
					$paddingSize = 0;
					break;
			}
		}

		if (!$paddingSize) {
			return $text;
		}

		if ($paddingSize > $blockSize) {
			throw new Exception('Invalid padding size');
		}

		return $text . substr($padding, 0, $paddingSize);
	}

	/**
	 * Removes padding @a padding_type from @a text
	 * If the function detects that the text was not padded, it will return it unmodified
	 *
	 * @param return_value Result, possibly unpadded
	 * @param text Message to be unpadded
	 * @param mode Encryption mode; unpadding is applied only in CBC or ECB mode
	 * @param block_size Cipher block size
	 * @param padding_type Padding scheme
	 */
	protected function _cryptUnpadText($text, $mode, $blockSize, $paddingType)
	{
		
		$paddingSize = 0;

		$length = strlen($text);
		if (($length > 0) && ($length % $blockSize == 0) && ($mode === 'cbc' || $mode === 'ecb')) {

			switch ($paddingType) {

				case self::PADDING_ANSI_X_923:
					$last = substr($text, $length - 1, 1);
					$ord = (int) ord($last);
					if ($ord <= $blockSize) {
						$paddingSize = $ord;
						$padding = str_repeat(chr(0), $paddingSize - 1) . $last;
						if (substr($text, $length - $paddingSize) != $padding) {
							$paddingSize = 0;
						}
					}
					break;

				case self::PADDING_PKCS7:
					$last = substr($text, $length - 1, 1);
					$ord = (int) ord($last);
					if ($ord <= $blockSize) {
						$paddingSize = $ord;
						$padding = str_repeat(chr($paddingSize), $paddingSize);
						if (substr($text, $length - $paddingSize) != $padding) {
							$paddingSize = 0;
						}
					}
					break;

				case self::PADDING_ISO_10126:
					$last = substr($text, $length - 1, 1);
					$paddingSize = (int) ord($last);
					break;

				case self::PADDING_ISO_IEC_7816_4:
					$i = $length - 1;
					while (($i > 0) && ($text[$i] == 0x00) && ($paddingSize < $blockSize)) {
						$paddingSize++;
						$i--;
					}
					if ($text[$i] == 0x80) {
						$paddingSize++;
					} else {
						$paddingSize = 0;
					}
					break;

				case self::PADDING_ZERO:
					$i = $length - 1;
					while ($i >= 0 && $text[$i] == 0x00 && $paddingSize <= $blockSize) {
						$paddingSize++;
						$i--;
					}
					break;

				case self::PADDING_SPACE:
					$i = $length - 1;
					while ($i >= 0 && $text[$i] == 0x20 && $paddingSize <= $blockSize) {
						$paddingSize++; $i--;
					}
					break;

				default:
					break;
			}

			if ($paddingSize && $paddingSize <= $blockSize) {

				if ($paddingSize < $length) {
					return substr($text, 0, $length - $paddingSize);
				}
				return '';

			} else {
				$paddingSize = 0;
			}

		}

		if (!$paddingSize) {
			return $text;
		}
	}

	/**
	 * Encrypts a text
	 *
	 *<code>
	 *	$encrypted = $crypt->encrypt('Ultra-secret text', 'encrypt password');
	 *</code>
	 */
	public function encrypt($text, $key = null)
	{
		
		if (!function_exists('mcrypt_get_iv_size')) {
			throw new Exception('mcrypt extension is required');
		}

		if ($key === null) {
			$encryptKey = $this->_key;
		} else {
			$encryptKey = $key;
		}

		if (empty ($encryptKey)) {
			throw new Exception('Encryption key cannot be empty');
		}

		$cipher = $this->_cipher; $mode = $this->_mode;

		$ivSize = mcrypt_get_iv_size($cipher, $mode);

		if (strlen($encryptKey) > $ivSize) {
			throw new Exception('Size of key is too large for this algorithm');
		}

		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
		if(!is_string($iv)) {
			$iv = strval($iv);
		}

		$blockSize = mcrypt_get_block_size($cipher, $mode);
		if(!is_int($blockSize)) {
			$blockSize = intval($blockSize);
		}

		$paddingType = $this->_padding;

		if ($paddingType != 0 && ($mode === 'cbc' || $mode === 'ecb')) {
			$padded = $this->_cryptPadText($text, $mode, $blockSize, $paddingType);
		} else {
			$padded = $text;
		}

		return $iv . mcrypt_encrypt($cipher, $encryptKey, $padded, $mode, $iv);
	}

	/**
	 * Decrypts an encrypted text
	 *
	 *<code>
	 *	echo $crypt->decrypt($encrypted, 'decrypt password');
	 *</code>
	 */
	public function decrypt($text, $key = null)
	{
		
		if (!function_exists('mcrypt_get_iv_size')) {
			throw new Exception('mcrypt extension is required');
		}

		if ($key === null) {
			$decryptKey = $this->_key;
		} else {
			$decryptKey = $key;
		}

		if (empty ($decryptKey)) {
			throw new Exception('Decryption key cannot be empty');
		}

		$cipher = $this->_cipher; $mode = $this->_mode;

		$ivSize = mcrypt_get_iv_size($cipher, $mode);

		$keySize = strlen($decryptKey);
		if ($keySize > $ivSize) {
			throw new Exception('Size of key is too large for this algorithm');
		}

		$length = strlen($text);
		if ($keySize > $length) {
			throw new Exception('Size of IV is larger than text to decrypt');
		}

		$decrypted = mcrypt_decrypt($cipher, $decryptKey, substr($text, $ivSize), $mode, substr($text, 0, $ivSize));

		$blockSize = mcrypt_get_block_size($cipher, $mode);
		$paddingType = $this->_padding;

		if ($mode === 'cbc' || $mode === 'ecb') {
			return $this->_cryptUnpadText($decrypted, $mode, $blockSize, $paddingType);
		}

		return $decrypted;
	}

	/**
	 * Encrypts a text returning the result as a base64 string
	 */
	public function encryptBase64($text, $key = null, $safe = false)
	{
		if ($safe == true) {
			return strtr(base64_encode($this->encrypt($text, $key)), '+/', '-_');
		}
		return base64_encode($this->encrypt($text, $key));
	}

	/**
	 * Decrypt a text that is coded as a base64 string
	 */
	public function decryptBase64($text, $key = null, $safe = false)
	{
		if ($safe == true) {
			return $this->decrypt(base64_decode(strtr($text, '-_', '+/')), $key);
		}
		return $this->decrypt(base64_decode($text), $key);
	}

	/**
	 * Returns a list of available cyphers
	 */
	public function getAvailableCiphers()
	{
		return mcrypt_list_algorithms();
	}

	/**
	 * Returns a list of available modes
	 */
	public function getAvailableModes()
	{
		return mcrypt_list_modes();
	}
}
