<?php 
namespace WPOptimizeByxTraffic\Application\Service;

class PepVN_Images 
{
    
	public static function downloadImage($img_src, $file_path = false)
	{
		
	}
	
	public static function fitTextSizeToBoxSize($fontSize, $angle, $fontFile, $text, $box_width, $box_height)
	{
		$box_width = (int)$box_width;
		$box_height = (int)$box_height;
		
		$fontSize = (int)$fontSize;
		
		if($fontSize > 0) {//if $fontSize > 0 :  $fontSize will decrease to fit box size
			
			while($fontSize > 0){
				
				$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text);
				$actualWidth = abs($testbox[6] - $testbox[4]);
				$actualHeight = abs($testbox[1] - $testbox[7]);
				
				$isFontSizeOk = true;
				
				if($box_width>0) {
					if($actualWidth > $box_width) {
						$isFontSizeOk = false;
					}
				}
				
				if($box_height>0) {
					if($actualHeight > $box_height) {
						$isFontSizeOk = false;
					}
				}
				
				if($isFontSizeOk) {
					return $fontSize;
				} else {
					$fontSize--;
				}
			}
		} else {//if $fontSize === 0 :  $fontSize will increase to fit box size
			
			$fontSize = 0;
			
			$actualWidth = 0;
			$actualHeight = 0;
			
			
			$isFontSizeOk = false;
			
			while(!$isFontSizeOk) {
				
				$fontSize++;
				
				$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text);
				$actualWidth = abs($testbox[6] - $testbox[4]);
				$actualHeight = abs($testbox[1] - $testbox[7]);
				
				if($box_width>0) {
					if($actualWidth >= $box_width) {
						$isFontSizeOk = true;
					}
				}
				
				if($box_height>0) {
					if($actualHeight >= $box_height) {
						$isFontSizeOk = true;
					}
				}
			}
			
			return $fontSize;
			
		}
		
		return $fontSize;
	}
	
	public static function calculateText($fontSize, $angle, $fontFile, $text)
	{
		$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text); 
		
		$actualWidth = abs($testbox[6] - $testbox[4]);
		$actualHeight = abs($testbox[1] - $testbox[7]);
		
		$actualWidth = (int)$actualWidth;
		$actualHeight = (int)$actualHeight;
		
		$resultData = array(
			'actualWidth' => $actualWidth
			,'actualHeight' => $actualHeight
		);
		
		return $resultData;
	}
	
	/**
	 * Create Blank Transparent Image Resource
	 *
	 * ### Options
	 *
	 * - integer $width
	 * - integer $height
	 * @return image_resource
	 */
	public static function create_blank_transparent_image_resource($width, $height)
	{
		//create image with specified sizes
		$image = imagecreatetruecolor($width, $height);
		//saving all full alpha channel information
		imagesavealpha($image, true);
		//setting completely transparent color
		$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
		//filling created image with transparent color
		imagefill($image, 0, 0, $transparent);
		
		return $image;
	}
	
	public static function getImageInfo($file, $returnResource = true)
	{
		$resultData = false;
		
		if ($file && is_file($file) && is_readable($file)) { 
			
			$rsGetimagesize = getimagesize($file);
			if(isset($rsGetimagesize['mime']) && $rsGetimagesize['mime']) {
				$rsGetimagesize['mime'] = (string)$rsGetimagesize['mime'];
				$rsGetimagesize['mime'] = strtolower($rsGetimagesize['mime']);
				
				
				if(preg_match_all('#/jpe?g#i', $rsGetimagesize['mime'], $matched1)) {
					$rsGetimagesize['image_type'] = 'jpg';
				} else if(false !== stripos($rsGetimagesize['mime'],'/gif')) {
					$rsGetimagesize['image_type'] = 'gif';
				} else if(false !== stripos($rsGetimagesize['mime'],'/png')) {
					$rsGetimagesize['image_type'] = 'png';
				}
				
				if(isset($rsGetimagesize['image_type']) && $rsGetimagesize['image_type']) {
					$resultData = $rsGetimagesize;
					$resultData['width'] = $rsGetimagesize[0];
					$resultData['height'] = $rsGetimagesize[1];
					$resultData['image_resource'] = false;
					
					if ($returnResource) {
						if('jpg' === $rsGetimagesize['image_type']) {
							$resultData['image_resource'] = @imagecreatefromjpeg($file);
						} elseif('gif' === $rsGetimagesize['image_type']) {
							$resultData['image_resource'] = @imagecreatefromgif($file);
						} elseif('png' === $rsGetimagesize['image_type']) {
							$resultData['image_resource'] = @imagecreatefrompng($file);
						}
					}
				}
			}
		}
		
		return $resultData;
	}
	
	public static function getImageResourceSize($image_resource) 
	{
		$resultData = array(
			'x' => 0
			,'y' => 0
		);
		if($image_resource && is_resource($image_resource)) {
			$resultData['x'] = imagesx($image_resource);
			$resultData['y'] = imagesy($image_resource);
		}
		
		return $resultData;
	}
	
	public static function isAnimation($filesrc)
	{
		
		$checkStatus = false;
		$count = 0;
		
		if($filesrc && file_exists($filesrc)) {
		
			if(($fh = @fopen($filesrc, 'rb'))) {
				while((!feof($fh)) && ($count<2)) {
					$chunk = fread($fh, 1024 * 100);
					$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);
				}
				
				fclose($fh);
				
				if($count<2) {
					$count = self::countFrameAnimation($filesrc);
				}
			}
		}
		
		if($count>1) {
			$checkStatus = true;
		}
		
		return $checkStatus;
	}
	
	public static function countFrameAnimation($filesrc)
	{
	
		$count = 0;
		
		if($filesrc && is_file($filesrc) && is_readable($filesrc)) {
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', file_get_contents($filesrc), $matches1);
		}
		
		return $count;
	}
	
	
}











/**
 * Wrapper for PHP's GD Library for easy image manipulation to resize, crop
 * and draw images on top of each other preserving transparency, writing text
 * with stroke and transparency and drawing shapes.
 *
 * @version 0.4
 * @author Blake Kus <blakekus@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright 2014 Blake Kus
 *
 * CHANGELOG:
 * version 0.4 2014-02-27
 * ADD: Image support for image cloning (Thanks @chainat)
 * ADD: Support to use GD commands to manipulate image and then continue using library
 * UPDATE: Private to Protected so library is extendable
 * ADD: Rotate
 *
 * version 0.3 2014-02-12
 * ADD: Examples
 * ADD: Initialise image on class instantiation
 * ADD: Image resize with optional upscaling
 * ADD: Image batch resize
 * ADD: Image crop
 * ADD: Option to save as gif/jpg/png
 * ADD: Snapshot images
 * FIX: Error in `textBox` reported by elbaku https://github.com/kus/php-image/issues/1
 *
 * version 0.2 2013-05-23
 * Add support for remote images
 * Add error handling when reading/writing files
 * Add ability to draw text box and auto fit text and align text
 *
 * version 0.1 2013-04-15
 * Initial release
 */

class PepVN_PHPImage 
{
	/**
	 * Canvas resource
	 *
	 * @var resource
	 */
	protected $img;
	
	public $img_file_path;

	/**
	 * Canvas resource
	 *
	 * @var resource
	 */
	protected $img_copy;

	/**
	 * PNG Compression level: from 0 (no compression) to 9.
	 * JPEG Compression level: from 0 to 100 (no compression).
	 *
	 * @var integer
	 */
	protected $quality = 90;

	/**
	 * Global font file
	 *
	 * @var String
	 */
	protected $fontFile;

	/**
	 * Global font size
	 *
	 * @var integer
	 */
	protected $fontSize = 12;

	/**
	 * Global text vertical alignment
	 *
	 * @var String
	 */
	protected $alignVertical = 'top';

	/**
	 * Global text horizontal alignment
	 *
	 * @var String
	 */
	protected $alignHorizontal = 'left';

	/**
	 * Global font colour
	 *
	 * @var array
	 */
	protected $textColor = array(255, 255, 255);

	/**
	 * Global text opacity
	 *
	 * @var float
	 */
	protected $textOpacity = 1;

	/**
	 * Global text angle
	 *
	 * @var integer
	 */
	protected $textAngle = 0;

	/**
	 * Global stroke width
	 *
	 * @var integer
	 */
	protected $strokeWidth = 0;

	/**
	 * Global stroke colour
	 *
	 * @var array
	 */
	protected $strokeColor = array(0, 0, 0);

	/**
	 * Canvas width
	 *
	 * @var integer
	 */
	protected $width;

	/**
	 * Canvas height
	 *
	 * @var integer
	 */
	protected $height;

	/**
	 * Image type
	 *
	 * @var integer
	 */
	protected $type;

	/**
	 * Default folder mode to be used if folder structure needs to be created
	 *
	 * @var String
	 */
	protected $folderMode = 0777;

	/**
	 * Initialise the image with a file path, or dimensions, or pass no dimensions and
	 * use setDimensionsFromImage to set dimensions from another image.
	 *
	 * @param string|integer $mixed (optional) file or width
	 * @param integer $height (optional)
	 * @return $this
	 */
	public function __construct($mixed=null, $height=null){
		//Check if GD extension is loaded
		if (!extension_loaded('gd') && !extension_loaded('gd2')) {
			$this->handleError('GD is not loaded');
			return false;
		}
		if($mixed !== null && $height !== null){
			$this->initialiseCanvas($mixed, $height);
		} else if($mixed !== null && is_string($mixed)){
			$image = $this->setDimensionsFromImage($mixed);
			$image->draw($mixed);
			$image->img_file_path = $mixed;
			return $image;
		}
	}

	/**
	 * Intialise the canvas
	 *
	 * @param integer $width
	 * @param integer $height
	 * @return $this
	 */
	protected function initialiseCanvas($width, $height, $resource='img'){
		$this->width = $width;
		$this->height = $height;
		unset($this->$resource);
		$this->$resource = imagecreatetruecolor($this->width, $this->height);
		// Set the flag to save full alpha channel information
		imagesavealpha($this->$resource, true);
		// Turn off transparency blending (temporarily)
		imagealphablending($this->$resource, false);
		// Completely fill the background with transparent color
		imagefilledrectangle($this->$resource, 0, 0, $this->width, $this->height, imagecolorallocatealpha($this->$resource, 0, 0, 0, 127));
		// Restore transparency blending
		imagealphablending($this->$resource, true);
		return $this;
	}

	/**
	 * After we update the image run this function
	 */
	protected function afterUpdate(){
		$this->shadowCopy();
	}

	/**
	 * Store a copy of the image to be used for clone
	 */
	protected function shadowCopy(){
		$this->initialiseCanvas($this->width, $this->height, 'img_copy');
		imagecopy($this->img_copy, $this->img, 0, 0, 0, 0, $this->width, $this->height);
	}

	/**
	 * Enable cloning of images in their current state
	 *
	 * $one = clone $image;
	 */
	public function __clone(){
		$this->initialiseCanvas($this->width, $this->height);
		imagecopy($this->img, $this->img_copy, 0, 0, 0, 0, $this->width, $this->height);
	}

	/**
	 * Get image resource (used when using a raw gd command)
	 *
	 * @return resource
	 */
	public function getResource(){
		return $this->img;
	}

	/**
	 * Set image resource (after using a raw gd command)
	 *
	 * @param $resource
	 * @return $this
	 */
	public function setResource($resource){
		$this->img = $resource;
		return $this;
	}

	/**
	 * Set image dimensions from an image source
	 *
	 * @param String $file
	 * @return $this
	 */
	public function setDimensionsFromImage($file){
		if($info = $this->getImageInfo($file, false)){
			$this->initialiseCanvas($info->width, $info->height);
			return $this;
		} else {
			$this->handleError($file . ' is not readable!');
		}
	}

	/**
	 * Check if an image (remote or local) is a valid image and return type, width, height and image resource
	 *
	 * @param string $file
	 * @param boolean $returnResource
	 * @return \stdClass
	 */
	protected function getImageInfo($file, $returnResource=true){
		if (preg_match('#^https?://#i', $file)) {
			$headers = get_headers($file, 1);
			if (is_array($headers['Content-Type'])) {
				// Some servers return an array of content types, Facebook does this
				$contenttype = $headers['Content-Type'][0];
			} else {
				$contenttype = $headers['Content-Type'];
			}
			if (preg_match('#^image/(jpe?g|png|gif)$#i', $contenttype)) {
				switch(true){
					case stripos($contenttype, 'jpeg') !== false:
					case stripos($contenttype, 'jpg') !== false:
						$img = @imagecreatefromjpeg($file);
						$type = IMAGETYPE_JPEG;
						break;
					case stripos($contenttype, 'png') !== false:
						$img = @imagecreatefrompng($file);
						$type = IMAGETYPE_PNG;
						break;
					case stripos($contenttype, 'gif') !== false:
						$img = @imagecreatefromgif($file);
						$type = IMAGETYPE_GIF;
						break;
					default:
						return false;
						break;
				}
				$width = imagesx($img);
				$height = imagesy($img);
				if (!$returnResource) {
					imagedestroy($img);
				}
			} else {
				return false;
			}
		} elseif (is_readable($file)) {
			list($width, $height, $type) = getimagesize($file);
			switch($type){
				case IMAGETYPE_GIF:
					if ($returnResource) {
						$img = @imagecreatefromgif($file);
					}
					break;
				case IMAGETYPE_JPEG:
					if ($returnResource) {
						$img = @imagecreatefromjpeg($file);
					}
					break;
				case IMAGETYPE_PNG:
					if ($returnResource) {
						$img = @imagecreatefrompng($file);
					}
					break;
				default:
					return false;
					break;
			}
		} else {
			return false;
		}
		$info = new \stdClass();
		$info->type = $type;
		if($this->type === null){
			// Assuming the first image you use is the output image type you want
			$this->type = $type;
		}
		$info->width = $width;
		$info->height = $height;
		if ($returnResource) {
			$info->resource = $img;
		}
		return $info;
	}

	/**
	 * Handle errors
	 *
	 * @param String $error
	 *
	 * @throws Exception
	 */
	protected function handleError($error){
		throw new \Exception($error);
	}

	/**
	 * Rotate image
	 *
	 * @param $angle
	 * @param int $bgd_color
	 * @param int $ignore_transparent
	 * @return $this
	 */
	public function rotate($angle, $bgd_color=0, $ignore_transparent=0){
		$this->img = imagerotate($this->img, $angle, 0);
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Crop an image
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @return $this
	 */
	public function crop($x, $y, $width, $height){
		$tmp = $this->img;
		$this->initialiseCanvas($width, $height);
		imagecopyresampled($this->img, $tmp, 0, 0, $x, $y, $width, $height, $width, $height);
		imagedestroy($tmp);
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Batch resize and save
	 *
	 * Usage: $image->batchResize('/path/to/img/test_%dx%d.jpg', array(array(100, 100, 'C', true),array(50, 50)));
	 *
	 * Will result in two images being saved (test_100x100.jpg and test_50x50.jpg) with 100x100 being cropped to the center.
	 *
	 * @param String $path
	 * @param array $dimensions Array of `resize` arguments to run and save ie: array(100, 100, true, true)
	 * @return $this
	 */
	public function batchResize($path, $dimensions=array()){
		if(is_array($dimensions) && count($dimensions) > 0){
			$width = $this->width;
			$height = $this->height;
			$copy = imagecreatetruecolor($width, $height);
			imagecopy($copy, $this->img, 0, 0, 0, 0, $width, $height);
			foreach($dimensions as $args){
				$this->initialiseCanvas($width, $height);
				imagecopy($this->img, $copy, 0, 0, 0, 0, $width, $height);
				call_user_func_array(array($this, 'resize'), $args);
				$this->save(sprintf($path, $args[0], $args[1]));
			}
			$this->initialiseCanvas($width, $height);
			imagecopy($this->img, $copy, 0, 0, 0, 0, $width, $height);
			imagedestroy($copy);
		}
		return $this;
	}

	/**
	 * Resize image to desired dimensions.
	 *
	 * Optionally crop the image using the quadrant.
	 *
	 * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
	 * remaining overflow using the quadrant to get the image to be the size specified.
	 *
	 * The quadrants available are Top, Bottom, Center (default if crop = true), Left, and Right:
	 *
	 * +---+---+---+
	 * |   | T |   |
	 * +---+---+---+
	 * | L | C | R |
	 * +---+---+---+
	 * |   | B |   |
	 * +---+---+---+
	 *
	 * @param integer $targetWidth
	 * @param integer $targetHeight
	 * @param boolean|String $crop T, B, C, L, R
	 * @param boolean $upscale
	 * @return $this
	 */
	public function resize($targetWidth, $targetHeight, $crop=false, $upscale=false){
		$width = $this->width;
		$height = $this->height;
		$canvasWidth = $targetWidth;
		$canvasHeight = $targetHeight;
		$r = $width / $height;
		$x = 0;
		$y = 0;
		if ($crop !== false) {
			if($crop === true){
				$crop = 'C';
			}
			if ($targetWidth/$targetHeight > $r) {
				// crop top/bottom
				$newheight = intval($targetWidth/$r);
				$newwidth = $targetWidth;
				switch($crop){
					case 'T':
						$y = 0;
						break;
					case 'B':
						$y = intval(($newheight - $targetHeight) * ($height / $newheight));
						break;
					case 'C':
					default:
						$y = intval((($newheight - $targetHeight) / 2) * ($height / $newheight));
						break;
				}
			} else {
				// crop sides
				$newwidth = intval($targetHeight*$r);
				$newheight = $targetHeight;
				switch($crop){
					case 'L':
						$x = 0;
						break;
					case 'R':
						$x = intval(($newwidth - $targetWidth) * ($width / $newwidth));
						break;
					case 'C':
					default:
						$x = intval((($newwidth - $targetWidth) / 2) * ($width / $newwidth));
						break;
				}
			}
			if($upscale === false){
				if($newwidth > $width){
					$x = 0;
					$newwidth = $width;
					$canvasWidth = $newwidth;
				}
				if($newheight > $height){
					$y = 0;
					$newheight = $height;
					$canvasHeight = $newheight;
				}
			}
		} else {
			if ($targetWidth/$targetHeight > $r) {
				$newwidth = intval($targetHeight*$r);
				$newheight = $targetHeight;
			} else {
				$newheight = intval($targetWidth/$r);
				$newwidth = $targetWidth;
			}
			if($upscale === false){
				if($newwidth > $width){
					$newwidth = $width;
				}
				if($newheight > $height){
					$newheight = $height;
				}
			}
			$canvasWidth = $newwidth;
			$canvasHeight = $newheight;
		}
		$tmp = $this->img;
		$this->initialiseCanvas($canvasWidth, $canvasHeight);
		imagecopyresampled($this->img, $tmp, 0, 0, $x, $y, $newwidth, $newheight, $width, $height);
		imagedestroy($tmp);
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Shows the resulting image
	 */
	public function show(){
		header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-type: image/png');
		switch($this->type){
			case IMAGETYPE_GIF:
				imagegif($this->img, null);
				break;
			case IMAGETYPE_PNG:
				imagepng($this->img, null, $this->quality);
				break;
			default:
				imagejpeg($this->img, null, $this->quality);
				break;
		}
		$this->cleanup();
		die();
	}

	/**
	 * Cleanup
	 */
	public function cleanup(){
		if($this->img && is_resource($this->img)) {
			imagedestroy($this->img);
		}
		$this->img = false;
	}

	/**
	 * Save the image
	 *
	 * @param String $path
	 * @param boolean $show
	 * @param boolean $destroy
	 * @return $this
	 */
	public function save($path, $show=false, $destroy=true)
	{
		
		$this->quality = (int)$this->quality;
		if(($this->quality > 0) && ($this->quality <= 100)) {
		} else {
			$this->quality = 100;
		}
		
		
		if (!is_writable(dirname($path))) {
			if (!mkdir(dirname($path), $this->folderMode, true)) {
				$this->handleError(dirname($path) . ' is not writable and failed to create directory structure!');
			}
		}
		if (is_writable(dirname($path))) {
			switch($this->type){
				case IMAGETYPE_GIF:
					imagegif($this->img, $path);
					//$this->optimize_image_file_by_imagick($path,$path,'gif',$this->quality); //imagick not effect with gif
					break;
				case IMAGETYPE_PNG:
					$imageQuality = $this->quality;
					// *** Scale quality from 0-100 to 0-9
					$scaleQuality = floor(($imageQuality/100) * 9);
		 
					// *** Invert quality setting as 0 is best, not 9
					$invertScaleQuality = 9 - $scaleQuality; 
					
					imagepng($this->img, $path, 9);
					//$this->optimize_image_file_by_imagick($path,$path,'png',$this->quality); //imagick make png file bigger size
					break;
				default:
					imagejpeg($this->img, $path, $this->quality);
					//$this->optimize_image_file_by_imagick($path,$path,'jpg',$this->quality);
					break;
			}
		} else {
			$this->handleError(dirname($path) . ' is not writable!');
		}
		if($show){
			$this->show();
			return;
		}
		if($destroy){
			$this->cleanup();
		}else{
			return $this;
		}
	}
	
	
	/**
	* Optimizes PNG file with pngquant 1.8 or later (reduces file size of 24-bit/32-bit PNG images).
	*
	* You need to install pngquant 1.8 on the server (ancient version 1.0 won't work).
	* There's package for Debian/Ubuntu and RPM for other distributions on http://pngquant.org
	*
	* @param $path_to_png_file string - path to any PNG file, e.g. $_FILE['file']['tmp_name']
	* @param $max_quality int - conversion quality, useful values from 60 to 100 (smaller number = smaller file)
	* @return string - content of PNG file after conversion
	*/
	private function _compress_png_by_pngquant($path_to_png_file, $max_quality = 80)
	{
		if (!$path_to_png_file || !file_exists($path_to_png_file) || !is_file($path_to_png_file)) {
			//throw new Exception("File does not exist: $path_to_png_file");
			return false;
		}
		
		

		// guarantee that quality won't be worse than that.
		$min_quality = 30;

		// '-' makes it use stdout, required to save to $compressed_png_content variable
		// '<' makes it read from the given file path
		// escapeshellarg() makes this safe to use with any path
		$compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));

		if (!$compressed_png_content) {
			//throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
			return false; 
		}

		return $compressed_png_content;
	}
	
	
	public function optimize_image_file_by_imagick($image_source_path,$image_des_path,$image_type,$image_quality)
	{
		if(class_exists('Imagick')) {
			if($image_source_path && is_file($image_source_path)) {
				if(!$image_des_path) {
					$image_des_path = $image_source_path;
				}
				if(!$image_type) {
					$image_type = 'jpg';
				}
				
				$im = new Imagick($image_source_path);
				if($im) {
					
					if('gif' === $image_type) {
						$im->setImageCompression(Imagick::COMPRESSION_LZW);
					} else if('png' === $image_type) {
						$im->setImageCompression(Imagick::COMPRESSION_UNDEFINED);
					} else {
						$im->setImageCompression(Imagick::COMPRESSION_JPEG);
					}
					
					$image_quality = (int)$image_quality;
					$im->setImageCompressionQuality($image_quality);
					$im->stripImage();
					
					if(method_exists($im,'trimImage')) {
						$im->trimImage(0.1);
					}
					
					if(is_file($image_des_path)) {
						@unlink($image_des_path);
					}
					
					$im->writeImage($image_des_path);
					
					$im->clear(); 
					$im->destroy();
				}
				$im = 0; unset($im);
				
			}
		}
	}
	
	
	

	/**
	 * Save the image and return object to continue operations
	 *
	 * @param string $path
	 * @return $this
	 */
	public function snapshot($path){
		return $this->save($path, false, false);
	}

	/**
	 * Save the image and show it
	 *
	 * @param string $path
	 */
	public function showAndSave($path){
		$this->save($path, true);
	}

	/**
	 * Draw a line
	 *
	 * @param integer $x1
	 * @param integer $y1
	 * @param integer $x2
	 * @param integer $y2
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $dashed
	 * @return $this
	 */
	public function line($x1=0, $y1=0, $x2=100, $y2=100, $colour=array(0, 0, 0), $opacity=1.0, $dashed=false){
		if($dashed === true){
			imagedashedline($this->img, $x1, $y1, $x2, $y2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imageline($this->img, $x1, $y1, $x2, $y2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Draw a rectangle
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $outline
	 * @see http://www.php.net/manual/en/function.imagefilledrectangle.php
	 * @return $this
	 */
	public function rectangle($x=0, $y=0, $width=100, $height=50, $colour=array(0, 0, 0), $opacity=1.0, $outline=false){
		if($outline === true){
			imagerectangle($this->img, $x, $y, $x + $width, $y + $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imagefilledrectangle($this->img, $x, $y, $x + $width, $y + $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Draw a square
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $outline
	 * @see http://www.php.net/manual/en/function.imagefilledrectangle.php
	 * @return $this
	 */
	public function square($x=0, $y=0, $width=100, $colour=array(0, 0, 0), $opacity=1.0, $outline=false){
		return $this->rectangle($x, $y, $width, $width, $colour, $opacity, $outline);
	}

	/**
	 * Draw an ellipse
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $outline
	 * @see http://www.php.net/manual/en/function.imagefilledellipse.php
	 * @return $this
	 */
	public function ellipse($x=0, $y=0, $width=100, $height=50, $colour=array(0, 0, 0), $opacity=1.0, $outline=false){
		if($outline === true){
			imageellipse($this->img, $x, $y, $width, $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imagefilledellipse($this->img, $x, $y, $width, $height, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Draw a circle
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $outline
	 * @see http://www.php.net/manual/en/function.imagefilledellipse.php
	 * @return $this
	 */
	public function circle($x=0, $y=0, $width=100, $colour=array(0, 0, 0), $opacity=1.0, $outline=false){
		return $this->ellipse($x, $y, $width, $width, $colour, $opacity, $outline);
	}

	/**
	 * Draw a polygon
	 *
	 * @param array $points
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $outline
	 * @see http://www.php.net/manual/en/function.imagefilledpolygon.php
	 * @return $this
	 */
	public function polygon($points=array(), $colour=array(0, 0, 0), $opacity=1.0, $outline=false){
		if(count($points) > 0){
			if($outline === true){
				imagepolygon($this->img, $points, count($points) / 2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
			}else{
				imagefilledpolygon($this->img, $points, count($points) / 2, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
			}
			$this->afterUpdate();
		}
		return $this;
	}

	/**
	 * Draw an arc
	 *
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @param integer $start
	 * @param integer $end
	 * @param array $colour
	 * @param float $opacity
	 * @param boolean $outline
	 * @see http://www.php.net/manual/en/function.imagefilledarc.php
	 * @return $this
	 */
	public function arc($x=0, $y=0, $width=100, $height=50, $start=0, $end=180, $colour=array(0, 0, 0), $opacity=1.0, $outline=false){
		if($outline === true){
			imagearc($this->img, $x, $y, $width, $height, $start, $end, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127));
		}else{
			imagefilledarc($this->img, $x, $y, $width, $height, $start, $end, imagecolorallocatealpha($this->img, $colour[0], $colour[1], $colour[2], (1 - $opacity) * 127), IMG_ARC_PIE);
		}
		$this->afterUpdate();
		return $this;
	}
	
	/**
	 * Draw an image from file
	 *
	 * Accepts x/y properties from CSS background-position (left, center, right, top, bottom, percentage and pixels)
	 *
	 * @param String $file
	 * @param String|integer $x
	 * @param String|integer $y
	 * @see http://www.php.net/manual/en/function.imagecopyresampled.php
	 * @see http://www.w3schools.com/cssref/pr_background-position.asp
	 * @return $this
	 */
	public function draw($file, $x='50%', $y='50%'){
		if($info = $this->getImageInfo($file)){
			$image = $info->resource;
			$width = $info->width;
			$height = $info->height;
			// Defaults if invalid values passed
			if(strpos($x, '%') === false && !is_numeric($x) && !in_array($x, array('left', 'center', 'right'))){
				$x = '50%';
			}
			if(strpos($y, '%') === false && !is_numeric($y) && !in_array($y, array('top', 'center', 'bottom'))){
				$y = '50%';
			}
			// If word passed, convert it to percentage
			switch($x){
				case 'left':
					$x = '0%';
					break;
				case 'center':
					$x = '50%';
					break;
				case 'right':
					$x = '100%';
					break;
			}
			switch($y){
				case 'top':
					$y = '0%';
					break;
				case 'center':
					$y = '50%';
					break;
				case 'bottom':
					$y = '100%';
					break;
			}
			// Work out offset
			if(strpos($x, '%') > -1){
				$x = str_replace('%', '', $x);
				$x = ceil(($this->width - $width) * ($x / 100));
			}
			if(strpos($y, '%') > -1){
				$y = str_replace('%', '', $y);
				$y = ceil(($this->height - $height) * ($y / 100));
			}
			// Draw image
			imagecopyresampled(
				$this->img,
				$image,
				$x,
				$y,
				0,
				0,
				$width,
				$height,
				$width,
				$height
			);
			imagedestroy($image);
			$this->afterUpdate(); 
			return $this;
		} else {
			$this->handleError($file . ' is not a valid image!');
		}
	}
	
	
	
	
	/**
	 * Draw an image from resource
	 *
	 * Accepts x/y properties from CSS background-position (left, center, right, top, bottom, percentage and pixels)
	 *
	 * @param resource $image
	 * @param String|integer $x
	 * @param String|integer $y
	 * @see http://www.php.net/manual/en/function.imagecopyresampled.php
	 * @see http://www.w3schools.com/cssref/pr_background-position.asp
	 * @return $this
	 */
	public function drawFromResource($image, $input_options = false)
	{
		
		if($image) {
			
			$rsGetImageResourceSize = PepVN_Images::getImageResourceSize($image);
			
			$width = (int)$rsGetImageResourceSize['x'];
			$height = (int)$rsGetImageResourceSize['y'];
			
			if(($width>0) && ($height>0)) {
				
				$x=0;
				$y=0;
				
				if(isset($input_options['x'])) {
					$x=$input_options['x'];
				}
				if(isset($input_options['y'])) {
					$y=$input_options['y'];
				}
				
				$x = (int)$x;
				$y = (int)$y;
								
				imagecopyresampled(
					$this->img,
					$image,
					$x,
					$y,
					0,
					0,
					$width,
					$height,
					$width,
					$height
				);
				
				
				//imagedestroy($image);
				$this->afterUpdate(); 
				return $this;
				
				
			}
		}
		
	}
	
	

	/**
	 * Draw text
	 *
	 * ### Options
	 *
	 * - integer $fontSize
	 * - integer $x
	 * - integer $y
	 * - integer $angle
	 * - integer $strokeWidth
	 * - float $opacity
	 * - array $fontColor
	 * - array $strokeColor
	 * - String $fontFile
	 *
	 * @param String $text
	 * @param array $options
	 * @see http://www.php.net/manual/en/function.imagettftext.php
	 * @return $this
	 */
	public function text($text, $options=array())
	{
		// Unset null values so they inherit defaults
		foreach($options as $k => $v){
			if($options[$k] === null){
				unset($options[$k]);
			}
		}
		$defaults = array(
			'fontSize' => $this->fontSize,
			'fontColor' => $this->textColor,
			'opacity' => $this->textOpacity,
			'x' => 0,
			'y' => 0,
			'width' => null,//box width
			'height' => null,//box height
			
			'boxColor' => null,
			'boxOpacity' => 0.66,
			'boxPaddingX' => 0,//px/%
			'boxPaddingY' => 0,//px/%
			
			
			'alignHorizontal' => $this->alignHorizontal,
			'alignVertical' => $this->alignVertical,
			'angle' => $this->textAngle,
			'strokeWidth' => $this->strokeWidth,
			'strokeColor' => $this->strokeColor,
			'fontFile' => $this->fontFile,
			'autoFit' => true,
			'debug' => false
		);
		
		extract(array_merge($defaults, $options), EXTR_OVERWRITE);
		if($fontFile === null){
			$this->handleError('No font file set!');
		}
		if(is_int($width) && $autoFit){
			$fontSize = $this->fitToWidth($fontSize, $angle, $fontFile, $text, $width);
		}
		// Get Y offset as it 0 Y is the lower-left corner of the character
		$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text);
		$offsety = abs($testbox[7]);
		$offsetx = 0;
		$actualWidth = abs($testbox[6] - $testbox[4]);
		$actualHeight = abs($testbox[1] - $testbox[7]);
		
		
		$actualWidth = (int)$actualWidth;
		$actualHeight = (int)$actualHeight;
		
		$boxOpacity = (float)$boxOpacity;
		$boxOpacity = abs($boxOpacity);
		
		if(!$width) {
			$width = $actualWidth;
		}
		if(!$height) {
			$height = $actualHeight;
		}
		$width = (int)$width;
		$height = (int)$height;
		
		
		
		if($boxPaddingX) {
			if(false !== stripos($boxPaddingX,'%')) {
				$boxPaddingX = preg_replace('#[^0-9]+#','',$boxPaddingX);
				$valueTemp = (int)$boxPaddingX;
				$valueTemp = abs($valueTemp);
				$valueTemp = $width * ($valueTemp / 100);
				$boxPaddingX = ceil($valueTemp);
			}
		}
		
		if($boxPaddingY) {
			if(false !== stripos($boxPaddingY,'%')) {
				$boxPaddingY = preg_replace('#[^0-9]+#','',$boxPaddingY);
				$valueTemp = (int)$boxPaddingY;
				$valueTemp = abs($valueTemp);
				$valueTemp = $height * ($valueTemp / 100);
				$boxPaddingY = ceil($valueTemp);
			}
		}
		$boxPaddingX = preg_replace('#[^0-9]+#','',$boxPaddingX);
		$boxPaddingY = preg_replace('#[^0-9]+#','',$boxPaddingY);
		
		$boxPaddingX = abs((int)$boxPaddingX);
		$boxPaddingY = abs((int)$boxPaddingY);
		
		if($boxPaddingX > 0) {
			$width = $width + ceil($boxPaddingX * 2);
		}
		if($boxPaddingY > 0) {
			$height = $height + ceil($boxPaddingY * 2);
		}
		
		
		$strokeWidth = (int)$strokeWidth;
		$strokeWidth = abs($strokeWidth);
		if($strokeWidth > 0){
			$width = $width + ceil($strokeWidth * 2);
			$height = $height + ceil($strokeWidth * 2);
		}
		
		
		
		//create image with specified sizes
		$imgText = PepVN_Images::create_blank_transparent_image_resource($width, $height);
		
		if($boxColor) {
			imagefilledrectangle($imgText, 0, 0, $width, $height, imagecolorallocatealpha($imgText, $boxColor[0], $boxColor[1], $boxColor[2], floor((1 - $boxOpacity) * 127) ));
		}
		
		$imgText_TextX = floor(abs($width - $actualWidth)/2);
		$imgText_TextY = floor(($height + $actualHeight)/2);
		
		// Draw stroke
		if($strokeWidth > 0) {
			
			$strokeColor = imagecolorallocatealpha($imgText, $strokeColor[0], $strokeColor[1], $strokeColor[2], floor((1 - $opacity) * 127) );
			
			for($sx = ($imgText_TextX-abs($strokeWidth)); $sx <= ($imgText_TextX+abs($strokeWidth)); $sx++){
				for($sy = ($imgText_TextY-abs($strokeWidth)); $sy <= ($imgText_TextY+abs($strokeWidth)); $sy++){
					imagettftext($imgText, $fontSize, $angle, $sx, $sy, $strokeColor, $fontFile, $text);
				}
			}
			
			
		}
		
		
		
		// Draw text  
		imagettftext($imgText, $fontSize, $angle, $imgText_TextX, $imgText_TextY, imagecolorallocatealpha($imgText, $fontColor[0], $fontColor[1], $fontColor[2], floor((1 - $opacity) * 127)), $fontFile, $text);
		
		$dest_x = $x;
		$dest_y = $y;
		$src_x = 0;
		$src_y = 0;
		
		if($boxColor) {
			imagecopymerge($this->img, $imgText, $dest_x, $dest_y, $src_x, $src_y, (ceil($width * 1)), (ceil($height * 1)), floor($boxOpacity * 100));
		} else {
			imagecopy($this->img, $imgText, $dest_x, $dest_y, $src_x, $src_y, (ceil($width * 1)), (ceil($height * 1)));
		}
		
		
		
		
		if($imgText) {
			imagedestroy($imgText); $imgText = false;
		}
		
		$this->afterUpdate();
		return $this;
	}

	/**
	 * Reduce font size to fit to width
	 *
	 * @param integer $fontSize
	 * @param integer $angle
	 * @param String $fontFile
	 * @param String $text
	 * @param integer $width
	 * @return integer
	 */
	protected function fitToWidth($fontSize, $angle, $fontFile, $text, $width)
	{
		$fontSize = (int)$fontSize;
		
		while($fontSize > 0){
			$testbox = imagettfbbox($fontSize, $angle, $fontFile, $text);
			$actualWidth = abs($testbox[6] - $testbox[4]);
			if($actualWidth <= $width){
				return $fontSize;
			}else{
				$fontSize--;
			}
		}
		
		return $fontSize;
	}

	/**
	 * Draw multi-line text box and auto wrap text
	 *
	 * @param String $text
	 * @param array $options
	 * @return $this
	 */
	//public function textBox($text, $width=100, $fontSize=12, $x=0, $y=0, $angle=null, $strokeWidth=null, $opacity=null, $fontColor=null, $strokeColor=null, $fontFile=null){
	public function textBox($text, $options=array()){
		$defaults = array(
			'fontSize' => $this->fontSize,
			'fontColor' => $this->textColor,
			'opacity' => $this->textOpacity,
			'x' => 0,
			'y' => 0,
			'width' => 100,
			'height' => 100,
			'angle' => $this->textAngle,
			'strokeWidth' => $this->strokeWidth,
			'strokeColor' => $this->strokeColor,
			'fontFile' => $this->fontFile
		);
		extract(array_merge($defaults, $options), EXTR_OVERWRITE);
		return $this->text($this->wrap($text, $width, $fontSize, $angle, $fontFile), array('fontSize' => $fontSize, 'x' => $x, 'y' => $y, 'angle' => $angle, 'strokeWidth' => $strokeWidth, 'opacity' => $opacity, 'fontColor' => $fontColor, 'strokeColor' => $strokeColor, 'fontFile' => $fontFile));
	}

	/**
	 * Helper to wrap text
	 *
	 * @param String $text
	 * @param integer $width
	 * @param integer $fontSize
	 * @param integer $angle
	 * @param String $fontFile
	 * @return String
	 */
	protected function wrap($text, $width=100, $fontSize=12, $angle=0, $fontFile=null){
		if($fontFile === null){
			$fontFile = $this->fontFile;
		}
		$ret = "";
		$arr = explode(' ', $text);
		foreach ($arr as $word){
			$teststring = $ret . ' ' . $word;
			$testbox = imagettfbbox($fontSize, $angle, $fontFile, $teststring);
			if ($testbox[2] > $width){
				$ret .= ($ret == "" ? "" : "\n") . $word;
			} else {
				$ret .= ($ret == "" ? "" : ' ') . $word;
			}
		}
		return $ret;
	}

	/**
	 * Check quality is correct before save
	 *
	 * @return $this
	 */
	public function checkQuality(){
		switch($this->type){
			case IMAGETYPE_PNG:
				if($this->type > 9){
					$this->quality = 3;
				}
				break;
		}
		return $this;
	}

	/**
	 * Set's global folder mode if folder structure needs to be created
	 *
	 * @param integer $mode
	 * @return $this
	 */
	public function setFolderMode($mode=0755){
		$this->folderMode = $mode;
		return $this;
	}

	/**
	 * Set's global text size
	 *
	 * @param integer $size
	 * @return $this
	 */
	public function setFontSize($size=12){
		$this->fontSize = $size;
		return $this;
	}

	/**
	 * Set's global text vertical alignment
	 *
	 * @param String $align
	 * @return $this
	 */
	public function setAlignVertical($align='top'){
		$this->alignVertical = $align;
		return $this;
	}

	/**
	 * Set's global text horizontal alignment
	 *
	 * @param String $align
	 * @return $this
	 */
	public function setAlignHorizontal($align='left'){
		$this->alignHorizontal = $align;
		return $this;
	}

	/**
	 * Set's global text colour using RGB
	 *
	 * @param array $colour
	 * @return $this
	 */
	public function setTextColor($colour=array(255, 255, 255)){
		$this->textColor = $colour;
		return $this;
	}

	/**
	 * Set's global text angle
	 *
	 * @param integer $angle
	 * @return $this
	 */
	public function setTextAngle($angle=0){
		$this->textAngle = $angle;
		return $this;
	}

	/**
	 * Set's global text stroke
	 *
	 * @param integer $strokeWidth
	 * @return $this
	 */
	public function setStrokeWidth($strokeWidth=0){
		$this->strokeWidth = $strokeWidth;
		return $this;
	}

	/**
	 * Set's global text opacity
	 *
	 * @param float $opacity
	 * @return $this
	 */
	public function setTextOpacity($opacity=1.0){
		$this->textOpacity = $opacity;
		return $this;
	}

	/**
	 * Set's global stroke colour
	 *
	 * @param array $colour
	 * @return $this
	 */
	public function setStrokeColor($colour=array(0, 0, 0)){
		$this->strokeColor = $colour;
		return $this;
	}

	/**
	 * Set's global font file for text from .ttf font file (TrueType)
	 *
	 * @param string $fontFile
	 * @return $this
	 */
	public function setFont($fontFile){
		$this->fontFile = $fontFile;
		return $this;
	}

	/**
	 * Set's global quality for PNG output
	 *
	 * @param string $quality
	 * @return $this
	 */
	public function setQuality($quality){
		$this->quality = $quality;
		return $this;
	}

	/**
	 * Set's global output type
	 *
	 * @param String $type
	 * @param String $quality
	 * @return $this
	 */
	public function setOutput($type, $quality = null){
		switch(strtolower($type)){
			case 'gif':
				$this->type = IMAGETYPE_GIF;
				break;
			case 'jpg':
				$this->type = IMAGETYPE_JPEG;
				break;
			case 'png':
				$this->type = IMAGETYPE_PNG; 
				break;
		}
		if($quality !== null){
			$this->setQuality($quality);
		}
		return $this;
	}
}



