<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: image.php lionelratel $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );

// JCckDevImage
class JCckDevImage
{
	protected $_exif 		=	array();
	protected $_extension 	=	'';
	protected $_height 		=	0;
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

		if ( in_array( $this->_extension, array( 'jpg', 'jpeg', 'tiff' ) ) ) {
			if ( function_exists( 'exif_read_data' ) ) {
				$this->_exif 	=	@exif_read_data( $path, 0, true );
			}
		}
		
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
	public function createThumb( $dest, $tnumber, $twidth, $theight, $tformat)
	{
		if ( ! ( $twidth && trim( $twidth ) != '' && is_numeric( $twidth ) ) && ! ( $theight && trim( $theight ) != '' && is_numeric( $theight ) ) ) {
			return false;
		}
		
		$path 			=	( $dest != '' ) ? $dest : $this->_pathinfo['dirname'];
		$resImage 		= 	$this->_resource;
		$info			=	$this->_prepareDimensions( $this->_width, $this->_height, $twidth, $theight, $tformat );

		// Add transparence for PNG
		$thumbImage	=	imageCreateTrueColor( $info['thumbWidth'], $info['thumbHeight'] );
		if ( $this->_extension == 'png' ) {
			imagealphablending( $thumbImage, false );
		}

		// Generate thumb ressource
		imagecopyresampled( $thumbImage, $resImage, $info['thumbX'], $info['thumbY'], $info['newX'], $info['newY'], $info['thumbWidth'], $info['thumbHeight'], $info['newWidth'], $info['newHeight'] );

		// Set Folder
		// $file_path ='';
		if ( $tnumber == 0 ) {
			$thumbLocation	=	$path . '/' . $this->_pathinfo['basename'];
		} else {
			JCckDevHelper::createFolder( $path . '/_thumb'.$tnumber );
			$thumbLocation	=	$path . '/_thumb'.$tnumber . '/' . $this->_pathinfo['basename'];
		}
		
		// Create image
		$this->_generateThumb( $this->_extension, $thumbImage, $thumbLocation);

		return true;
	}

	// rotate
	public function rotate( $degrees = 0 )
	{
		if ( !$degrees && isset( $this->_exif['IFD0']['Orientation'] ) ) {
			switch ( $this->_exif['IFD0']['Orientation'] ) {
				case 8:
					$degrees	=	90;
					break;
				case 3:
					$degrees	=	180;
					break;
				case 6:
					$degrees	=	-90;
					break;
				default:
					$degrees	=	0;
					break;
			}
		}

		if ( $degrees ) {
			$rotate	=	imagerotate( $this->_resource, $degrees, 0 );
			$this->_generateThumb( $this->_extension, $rotate, $this->_pathinfo['dirname'].'/'.$this->_pathinfo['basename'] );
		}
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

		return 	array(
					'thumbX'=>$thumbX,
					'thumbY'=>$thumbY,
					'newX'=>$newX,
					'newY'=>$newY,
					'thumbWidth'=>$thumbWidth,
					'thumbHeight'=>$thumbHeight,
					'newWidth'=>$src_w,
					'newHeight'=>$src_h,
				);
	}
}
