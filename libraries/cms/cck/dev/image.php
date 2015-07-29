<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: image.php lionelratel $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );

// JCckDevImage
class JCckDevImage
{
	protected $_height 		=	0;
	protected $_extension 	=	'';
	protected $_pathinfo 	=	NULL;
	protected $_quality_jpg	=	90;
	protected $_quality_png	=	3;
	protected $_ratio 		=	0;
	protected $_resource 	=	NULL;
	protected $_width 		=	0;

	// __construct
	function __construct( $path )
	{
		$this->_quality_jpg	=	JCck::getConfig_Param( 'media_quality_jpeg', 90 );
		$this->_quality_png	=	JCck::getConfig_Param( 'media_quality_png', 3 );
		
		$this->_pathinfo 	=	pathinfo( $path );
		$this->_extension	=	strtolower( $this->_pathinfo['extension'] );
		
		$this->_resource 	= 	$this->_createResource( $this->_extension, $path );
		list( $this->_width, $this->_height )	=	getimagesize( $path );
		$this->_ratio 		= 	$this->_width / $this->_height;
	}

	// __call
	public function __call( $method, $args )
	{
		$prefix		=	strtolower( substr( $method, 0, 3 ) );
		$property	=	strtolower( substr( $method, 3 ) );
		
		if ( empty( $prefix ) ) {
			return;
		}
		
        if ( $prefix == 'get' ) {
        	$target	=	'_'.$property;

        	if ( isset( $this->$target ) ) {
        		return $this->$target;
        	}
		}
	}

	// createThumb
	public function createThumb($image, $tnumber, $twidth, $theight, $tformat, $watermark = null, $k = 1, $quality = 100) {
		if (!( $twidth && trim($twidth) != '' && is_numeric($twidth) ) && !( $theight && trim($theight) != '' && is_numeric($theight) )) {
			return false;
		}
		//var_dump($image);
		if ($watermark) {
			$watermark_obj = new JCckDevImage($watermark);
		}
		$path		 = $this->_pathinfo['dirname'];
		$resImage	 = $this->_resource;

		// Calcul Thumb Size
		$values = $this->_prepareDimensions($this->_width, $this->_height, $twidth, $theight, $tformat);
		list( $thumbX, $thumbY, $newX, $newY, $thumbWidth, $thumbHeight, $newWidth, $newHeight ) = $values;

		// Add transparence for PNG
		$thumbImage = imageCreateTrueColor($thumbWidth, $thumbHeight);
		if ($this->_extension == 'png') {
			imagealphablending($thumbImage, false);
		}

		// Generate thumb ressource
		imagecopyresampled($thumbImage, $resImage, $thumbX, $thumbY, $newX, $newY, $thumbWidth, $thumbHeight, $newWidth, $newHeight);

		// Set Folder
		// $file_path ='';
		if ($tnumber == 0) {
			$thumbLocation = $path . '/' . $this->_pathinfo['basename'];
		} else {
			JCckDevHelper::createFolder($path . '/_thumb' . $tnumber);
			$thumbLocation = $path . '/_thumb' . $tnumber . '/' . $this->_pathinfo['basename'];
		}

		if ($watermark) {
			$wmImage		 = $watermark_obj->_resource;
			$wm_width		 = $watermark_obj->_width;
			$wm_height		 = $watermark_obj->_height;
			$WmImgKoeff		 = $wm_height / $wm_width;
			$new_wm_width	 = $k * $thumbWidth;
			$new_wm_height	 = $new_wm_width * $WmImgKoeff;

			imagecopyresized($thumbImage, $wmImage, ($thumbWidth - $new_wm_width) / 2, ($thumbHeight - $new_wm_height) / 2, 0, 0, $new_wm_width, $new_wm_height, $wm_width, $wm_height); // Копируем изображение водяного знака на изображение источник
		}
		// Create image
		$this->_generateThumb($this->_extension, $thumbImage, $thumbLocation, $quality);

		return true;
	}

	// _createResource
	protected function _createResource( $ext, $path )
	{
		if ( $ext == 'gif' ) {
			$res	=	@ImageCreateFromGIF( $path );
		} elseif( $ext == 'jpg' || $ext == 'jpeg' ) {
			$res	=	@ImageCreateFromJPEG( $path );
		} elseif( $ext == 'png' ) {
			$res	=	@ImageCreateFromPNG( $path );
		} else {
			$res 	= 	false;
		}

		return $res;
	}

	// _generateThumb
	protected function _generateThumb( $ext, $resource, $file )
	{
		ob_start();	
		if ( $ext == 'gif' ) {
			imagegif( $resource );
		} elseif ( $ext == 'jpg' || $ext == 'jpeg' ) {
			imagejpeg( $resource, NULL, $this->_quality_jpg );
		} elseif ( $ext == 'png' ) {
			imagesavealpha( $resource, true );
			imagepng( $resource, NULL, $this->_quality_png );
		} else {
			// Bad extension !
		}

		$output	=	ob_get_contents();
		ob_end_clean();

		JFile::write( $file, $output );
	}
	public function createWatermark($image, $watermark, $k, $quality = 100) {

		$path			 = $this->_pathinfo['dirname'];
		$file			 = $this->_pathinfo['basename'];
		$resImage		 = $this->_resource;
		$wmImage		 = $watermark->_resource;
		$thumbWidth		 = $this->_width;
		$thumbHeight	 = $this->_height;
		$wm_width		 = $watermark->_width;
		$wm_height		 = $watermark->_height;
		$WmImgKoeff		 = $wm_height / $wm_width;
		$new_wm_width	 = $k * $thumbWidth;
		$new_wm_height	 = $new_wm_width * $WmImgKoeff;

		imagecopyresized($resImage, $wmImage, ($thumbWidth - $new_wm_width) / 2, ($thumbHeight - $new_wm_height) / 2, 0, 0, $new_wm_width, $new_wm_height, $wm_width, $wm_height); 
		// Create image
		$this->_generateThumb($this->_extension, $resImage, $path . '/' . $file, $quality);


		return true;
	}
	// _prepareDimensions
	protected function _prepareDimensions( $src_w, $src_h, $dest_w, $dest_h, $action ) 
	{
		$src_r 	=	$src_w / $src_h;
		$width	=  	( ! ( $dest_w && trim( $dest_w ) != '' && is_numeric( $dest_w ) ) && $dest_h ) ? round( $dest_h * $src_r ) : $dest_w;
		$height	=  	( $dest_w && ! ( $dest_h && trim( $dest_h ) != '' && is_numeric( $dest_h ) ) ) ? round( $dest_w / $src_r ) : $dest_h;
		$ratio	=	$width / $height;

		$newX	= 	0;
		$newY	=	0;
		$thumbX	=	0;
		$thumbY	=	0;

		switch( $action ) {
			case "addcolor":
				$thumbWidth		=	( $ratio > $src_r ) ? round( $height * $src_r ) : $width;
				$thumbHeight	=	( $ratio < $src_r ) ? round( $width / $src_r ) : $height;
				$thumbX			=	( $width / 2 ) - ( $thumbWidth / 2 );
				$thumbY			=	( $height / 2 ) - ( $thumbHeight / 2 );
				break;

			case "crop":
				if ( $ratio > $src_r ) {
					$zoom		=	$src_w / $width;
					$crop_h		=	floor( $zoom * $height );
					$crop_w		=	$src_w;
					$crop_x		=	0;
					$crop_y		=	floor( 0.5 * ( $src_h - $crop_h ) );
				} else {
					$zoom		=	$src_h / $height;
					$crop_h		=	$src_h;
					$crop_w		=	floor( $zoom * $width );
					$crop_x		=	floor( 0.5 * ( $src_w - $crop_w ) );
					$crop_y		=	0;
				}
				$newX			=	$crop_x;
				$newY			=	$crop_y;
				$src_w		=	$crop_w;
				$src_h		=	$crop_h;
				$thumbWidth		=	$width;
				$thumbHeight	=	$height;
				break;

			case "crop_dynamic":
				if ( $src_w > $src_h ) {
					if ( $ratio > $src_r ) {
						$zoom		=	$src_w / $width;
						$crop_h		=	floor( $zoom * $height );
						$crop_w		=	$src_w;
						$crop_x		=	0;
						$crop_y		=	floor( 0.5 * ( $src_h - $crop_h ) );
					} else {
						$zoom		=	$src_h / $height;
						$crop_h		=	$src_h;
						$crop_w		=	floor( $zoom * $width );
						$crop_x		=	floor( 0.5 * ( $src_w - $crop_w ) );
						$crop_y		=	0;
					}
					$newX			=	$crop_x;
					$newY			=	$crop_y;
					$src_w		=	$crop_w;
					$src_h		=	$crop_h;
					$thumbWidth		=	$width;
					$thumbHeight	=	$height;
				} else {
					if ( $ratio > $src_r ) {
						$zoom		=	$src_w / $width;
						$crop_h		=	floor( $zoom * $height );
						$crop_w		=	$src_w;
						$crop_x		=	0;
						$crop_y		=	floor( 0.5 * ( $src_h - $crop_h ) );
					} else {
						$zoom		=	$src_h / $height;
						$crop_h		=	$src_h;
						$crop_w		=	floor( $zoom * $width );
						$crop_x		=	floor( 0.5 * ( $src_w - $crop_w ) );
						$crop_y		=	0;
					}
					$newX			=	$crop_x;
					$newY			=	$crop_y;
					$src_w		=	$crop_h;
					$src_h		=	$crop_w;
					$thumbWidth		=	$height;
					$thumbHeight	=	$width;
				}
				break;

			case "maxfit":
				$width			=	( $width > $src_w ) ? $src_w : $width;
				$height			=	( $height > $src_h ) ? $src_h : $height;
				$width			=	( $ratio > $src_r ) ? round( $height * $src_r ) : $width;
				$height			=	( $ratio < $src_r ) ? round( $width / $src_r ) : $height;
				$thumbWidth		=	$width;
				$thumbHeight	=	$height;
				break;

			case "shrink":
				$width			=	( $width > $src_w ) ? $src_w : $width;
				$height			=	( $height > $src_h ) ? $src_h : $height;
				$thumbWidth		=	$width;
				$thumbHeight	=	$height;
				break;

			case "shrink_dynamic":
				if ( $src_w > $src_h ) {
					$width			=	( $width > $src_w ) ? $src_w : $width;
					$height			=	( $height > $src_h ) ? $src_h : $height;
					$thumbWidth		=	$width;
					$thumbHeight	=	$height;
				} else {
					$thumbWidth		=	( $height > $src_w ) ? $src_w : $height;
					$thumbHeight	=	( $width > $src_h ) ? $src_h : $width;
				}
				break;

			case "stretch":
				$thumbWidth		=	$width;
				$thumbHeight	=	$height;
				break;

			case "stretch_dynamic":
				if ( $src_w > $src_h ) {
					$thumbWidth		=	$width;
					$thumbHeight	=	$height;
				} else {
					$thumbWidth		=	$height;
					$thumbHeight	=	$width;
				}
				break;

			default:
				break;
		}

		$values 	= 	array();
		$values[] 	=	$thumbX;
		$values[] 	=	$thumbY;
		$values[] 	=	$newX;
		$values[] 	=	$newY;
		$values[] 	=	$thumbWidth;
		$values[] 	=	$thumbHeight;
		$values[] 	=	$src_w;
		$values[] 	=	$src_h;

		return $values;
	}
}
