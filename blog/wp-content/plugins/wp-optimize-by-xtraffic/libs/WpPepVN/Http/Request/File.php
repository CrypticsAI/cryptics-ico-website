<?php 
namespace WpPepVN\Http\Request;

use WpPepVN\Http\Request\FileInterface;

/**
 * WpPepVN\Http\Request\File
 *
 * Provides OO wrappers to the $_FILES superglobal
 *
 *<code>
 *	class PostsController extends \WpPepVN\Mvc\Controller
 *	{
 *
 *		public function uploadAction()
 *		{
 *			//Check if the user has uploaded files
 *			if ($this->request->hasFiles() == true) {
 *				//Print the real file names and their sizes
 *				foreach ($this->request->getUploadedFiles() as $file){
 *					echo $file->getName(), ' ', $file->getSize(), '\n';
 *				}
 *			}
 *		}
 *
 *	}
 *</code>
 */
class File implements FileInterface
{

	protected $_name;

	protected $_tmp;

	protected $_size;

	protected $_type;

	protected $_realType;

	protected $_error;

	protected $_key;

	protected $_extension;

	/**
	 * WpPepVN\Http\Request\File constructor
	 */
	public function __construct($file, $key = null)
	{
		
		
		if(isset($file['name'])) {
			$this->_name = $file['name'];

			if (defined('PATHINFO_EXTENSION')) {
				$this->_extension = pathinfo($name, PATHINFO_EXTENSION);
			}
		}

		
		if(isset($file['tmp_name'])) {
			$this->_tmp = $file['tmp_name'];
		}

		if(isset($file['size'])) {
			$this->_size = $file['size'];
		}

		if(isset($file['type'])) {
			$this->_type = $file['type'];
		}

		if(isset($file['error'])) {
			$this->_error = $file['error'];
		}

		if ($key) {
			$this->_key = $key;
		}
	}

	/**
	 * Returns the file size of the uploaded file
	 */
	public function getSize() 
	{
		return $this->_size;
	}

	/**
	 * Returns the real name of the uploaded file
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Returns the temporal name of the uploaded file
	 */
	public function getTempName() 
	{
		return $this->_tmp;
	}

	/**
	 * Returns the mime type reported by the browser
	 * This mime type is not completely secure, use getRealType() instead
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Gets the real mime type of the upload file using finfo
	 */
	public function getRealType() 
	{
		
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		if (!is_resource($finfo)) {
			return '';
		}

		$mime = finfo_file($finfo, $this->_tmp);
		finfo_close($finfo);

		return $mime;
	}

	/**
	 * Checks whether the file has been uploaded via Post.
	 */
	public function isUploadedFile() 
	{
		$tmp = $this->getTempName();
		return is_string($tmp) && is_uploaded_file($tmp);
	}

	/**
	 * Moves the temporary file to a destination within the application
	 */
	public function moveTo($destination) 
	{
		return move_uploaded_file($this->_tmp, $destination);
	}
}
