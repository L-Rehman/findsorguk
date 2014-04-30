<?php

namespace Imagecow;

use Imagecow\Image;


class Pas_Image_Magick {
	
	//Set up array of sizes
	protected $_sizes; 
	
	//Create user path string
	protected $_userPath;
	
	//Create original
	protected $_original;
	
	//Allowed extensions
	protected $_extensions	= array( 'jpg', 'jpeg', 'tiff', 'tif');
	
	//Basename of file
	protected $_basename;
	
	//Allowed mime types
	protected $_mimeTypes 	= array( 
		'image/jpeg',
		'image/pjpeg',
		'image/tiff',
		'image/x-tiff'
	); 
	
	//User object
	protected $_user;
	
	//Thumbnail directory
	const THUMB	= 'thumbnails/';
	
	//Small image directory
	const SMALL		= 'small/';
	
	//Medium image directory
	const MEDIUM	= 'medium/';

	//Large image directory
	const LARGE	= 'large/';
	
	//Display image directory
	const DISPLAY 	= 'display/';
	
	//Create directory to store tiffs
	const TIFFS	= 'tiffs/';
	
	const EXT	= '.jpg';
	
	protected $_tiffMimes = array ( 'image/tiff', 'image/x-tiff' );
	
	
	public function __construct( )
	{
		$this->_sizes = array(
			array ( 'destination' => self::THUMB, 'width' => 100, 'height' => 100 ),
			array ( 'destination' => self::SMALL, 'width' => 40, 'height' => 0 ),
			array ( 'destination' => self::MEDIUM, 'width' => 500, 'height' => 0 ),
			array ( 'destination' => self::DISPLAY, 'width' => 0, 'height' => 200 ),
		);
		$this->_user = new Pas_User_Details();
	}

	/** Set up the image
	 * 
	 * @param unknown_type $image
	 */
	public function setImage(  $image ) {
		//Original name
		$this->_original = $image;
		
		//Just get the basename
		$this->_basename = basename( $image );
		return $this;	
	}
	
	public function setImageNumber( $id ) {
		if( is_int( $id ) ) {
			$this->_imageNumber = $id;
		} else {
			throw new Zend_Exception ( 'No file to create', 500);
		}
	}

	public function getImageNumber() {
		return $this->_imageNumber();
	}
	
	/** get the image
	 * 
	 */
	public function getImage()
	{
		//Return the original name
		return $this->_original;
	}
	
	/** get the basename of the image minus extension
	 * 
	 */
	public function getBasename()
	{
		//Return the basename
		return $this->_basename;
	}
	
	/** Get the user's path
	 * 
	 */
	public function getUserPath( )
	{
		//Get the user's imagedir from the person object
		$userDirectory = $this->_user->getPerson()->imagedir;
		//Check if exists
		if(is_null($userDirectory)){
			//If not throw exception
			throw new Zend_Exception('No upload directory for that user');
		} else {
		//Return the path
		$this->_userPath = '.' . $userDirectory;
		} 		
	}
	
	/** Check directories exist for a user
	 * 
	 */
	public function checkDirectories()
	{
		//For each directory in the list, check that the directory exists
		foreach( $this->_sizes as $dir )
		{
			//Set up each directory path
			$directory = $this->getUserPath() . $dir['destination'];
			//Check if directory exists and if not create.
			if(!is_dir( $directory ))
			{
				$this->_makeDirectory( $directory );
			}
		}
		return $this;
	}
	
	/** Create the different sizes of images
	 * 
	 * @param $image
	 */
	
	public function resize( ) 
	{
		$image = $this->getImage();
		if(is_null( $image )) {
			throw new Zend_Exception('You must specify an image', 500); 
		}
		//Original image path. We leave this as is.
		$destination =  $this->getUserPath() . $image ;
		//Check file exists
		if( !file_exists( $destination ) ) {
			throw new Zend_Exception('That image does not exist', 500); 
		}
		//Make directory check
		$this->checkDirectories();
		
		//Loop through each size and create the image
		foreach( $this->_sizes as $resize )
		{
			if($resize['destination'] == self::THUMB ) {
				$newImage = $this->getUserPath() . $resize['destination'] 
				. $this->getImageNumber() . self::EXT;
			} else {
				$newImage = $this->getUserPath() . $resize['destination'] 
				. $this->getBasename() . self::EXT;
			}
			$image = Image::create( $destination, 'Imagick' );
			$mime = $image->getMimeType();
			if( in_array( $mime, $this->_mimeTypes ) )
			{
				$image->resize( $resize['width'], $resize['height'], 1);
				$image->format('jpg');
				$image->save( $newImage );
			}
			if( in_array( $mime, $this->_tiffMimes ) ) {
				//Convert tiff to JPG and repeat above, replace original and save tiff in tiffs folder
				$this->convertTiff( $image );
			} 
		}
		//Create the zooming images
		$this->zoomifyImage( $image );
	}
	
	
	/** Convert tiff to jpeg
	 * 
	 * @param $image
	 */
	public function convertTiff(  )
	{
		//Determine path to Tiff folder
		$tiffPath = $this->getUserPath() . self::TIFFS 
		. $this->getBasename() . self::EXT;
		
		//Where we will be saving the file
		$destination = $this->getUserPath();
		
		//Check if directory exists, if not then make directory
		if( !is_dir( $tiffPath )) {
			$this->_makeDirectory( $tiffPath );
		} 
		
		//Create an instance of the image to save
		$image = Image::create( $tiffPath, 'Imagick' );
		
		//Set the image to save as jpeg file
		$image->format('jpg');
		//Save to original folder as jpeg
		$image->save( $destination );
		//return to the resize function 
		$this->resize();
	}
	
	/** Make directory if does not exist
	 * 
	 * @param $path
	 */
	protected function _makeDirectory( $path )
	{
		//Set permission of directory
		$permission = 0775;
		//Make files recursive to same permission
		$recursive = TRUE;
		//Make the directory
		return mkdir( $path, $permission, $recursive );	
	}
	
}