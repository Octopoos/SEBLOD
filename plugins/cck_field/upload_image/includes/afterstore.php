<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$name			=	$process['field_name'];
$parent_name	=	$process['parent_name'];
$type			=	$process['field_type'];
$file_path		=	$process['file_path'];
$file_name		=	$process['file_name'];
$file_title		=	$process['file_title'];
$file_descr		=	$process['file_descr'];
$tmp_name		=	$process['tmp_name'];
$content_folder	=	$process['content_folder'];
$x2k			=	$process['xi'];
$array_x		=	$process['array_x'];
$true_name		=	$process['true_name'];
$options		=	$process['options2'];
$color			=	( @$options['image_color'] != '' ) ? $options['image_color'] : '#ffffff';
$permissions		=	( isset( $options['folder_permissions'] ) && $options['folder_permissions'] ) ? octdec( $options['folder_permissions'] ) : 0755;

if ( !(bool) ini_get( 'file_uploads' ) ) {
	JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'WARNINSTALLFILE' ) );
}

$doSave			=	0;
$field_type		=	$type;
$old_path		=	$file_path;
if ( $content_folder && $config['isNew'] ) {
	$file_path		.=	$config['pk'].'/';
	$file_location	=	$file_path.$file_name;
	$location		=	JPATH_SITE.'/'.$file_path.$file_name;
	$file_title =	( $file_title ) ? $file_title : '';
	$file_descr	=	( $file_descr ) ? $file_descr : '';
	if ( $file_title == '' && $file_descr == '' ) {
		if ( $x2k > -1 ) {
			if ( $array_x ) { //GroupX (custom!)
				$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.$old_path.$file_name.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.$file_location.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			} else { //FieldX
				$search		=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.$old_path.$file_name.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.$file_location.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
			}
		} else {
			$search_v	=	$old_path.$file_name;
			$replace_v	=	$file_location;
		}
	} else {
		if ( $x2k > -1 ) {
			if ( $array_x ) { //GroupX (custom!)
				$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$old_path.$file_name.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$file_location.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			} else { //FieldX
				$search		=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$old_path.$file_name.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$file_location.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
			}
		} else {
			$search_v	=	'{"image_location":"'.$old_path.$file_name.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}';
			$replace_v	=	'{"image_location":"'.$file_location.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}';
		}
	}
	$doSave	=	1;
} else {
	$file_location	=	$file_path.$file_name;
	$location		=	JPATH_SITE.'/'.$file_path.$file_name;
}

if ( ! JFolder::exists( JPATH_SITE.'/'.$file_path ) ) {
	JFolder::create( JPATH_SITE.'/'.$file_path, $permissions );
	$file_body	=	'<!DOCTYPE html><title></title>';
	JFile::write( JPATH_SITE.'/'.$file_path.'/index.html', $file_body );
}
if ( JFile::upload( $tmp_name, $location ) ) {
	$value	=	$file_location;
	$fields[$name]->value	=	$value;
	if ( $field_type == 'upload_image' ) {
		$newSize	=	getimagesize($location);
		$newWidth	=	$newSize[0];
		$newHeight	=	$newSize[1];
		$newRatio	=	$newWidth / $newHeight;	
		$newExt		=	substr( strrchr( $location, "." ), 1 );
		$waterI		=	''; //$options['image_watermark'];
		$waterExtI	=	substr( strrchr( $waterI, "." ), 1 );
		
		switch( $newExt ) {
			case 'gif':
			case 'GIF':
				$resImage	=	@ImageCreateFromGIF( $location );
				break;
			case 'jpg':
			case 'JPG':
			case 'jpeg': 
			case 'JPEG': 
				$resImage	=	@ImageCreateFromJPEG( $location );
				break;
			case 'png':
			case 'PNG':
				$resImage	=	@ImageCreateFromPNG( $location );
				break;
			default:
				break;
		}
		if ( ! $resImage ) {
			//...
		}
		//umask(0002);
		$options['thumb0_process']	=	$options['image_process'];
		$options['thumb0_width']	=	$options['image_width'];
		$options['thumb0_height']	=	$options['image_height'];
		if ( count( $options ) ) {
			$thumb_count	=	11;
			ob_start();
			for ( $i = 0; $i < $thumb_count; $i++ ) {
				$newWidth		=	$newSize[0];
				$newHeight		=	$newSize[1];
				$format_name	=	'thumb'.$i.'_process';
				$width_name		=	'thumb'.$i.'_width';
				$height_name	=	'thumb'.$i.'_height';
				if ( $i == 0 && $newWidth == $options[$width_name] && $newHeight == $options[$height_name] ) {
					continue;
				}
				if ( trim( $options[$format_name] ) ) {
					$newX	= 	0;
					$newY	=	0;
					$thumbX	=	0;
					$thumbY	=	0;
					if ( ! self::_available_img_dim ( $options[$width_name] ) && ! self::_available_img_dim ( $options[$height_name] ) ) {
						break;
					}
					$width	=  ( ! self::_available_img_dim ( $options[$width_name] ) && $options[$height_name] ) ? round( $options[$height_name] * $newRatio ) : $options[$width_name];
					$height	=  ( $options[$width_name] && ! self::_available_img_dim( $options[$height_name] ) ) ? round( $options[$width_name] / $newRatio ) : $options[$height_name];
					$ratio	=	$width / $height;
					switch( $options[$format_name] )
					{
						case "addcolor":
							$thumbWidth		=	( $ratio > $newRatio ) ? round( $height * $newRatio ) : $width;
							$thumbHeight	=	( $ratio < $newRatio ) ? round( $width / $newRatio ) : $height;
							$thumbX			=	( $width / 2 ) - ( $thumbWidth / 2 );
							$thumbY			=	( $height / 2 ) - ( $thumbHeight / 2 );
							break;
						case "crop":
							if ( $ratio > $newRatio ) {
								$zoom		=	$newWidth / $width;
								$crop_h		=	floor( $zoom * $height );
								$crop_w		=	$newWidth;
								$crop_x		=	0;
								$crop_y		=	floor( 0.5 * ( $newHeight - $crop_h ) );
							} else {
								$zoom		=	$newHeight / $height;
								$crop_h		=	$newHeight;
								$crop_w		=	floor( $zoom * $width );
								$crop_x		=	floor( 0.5 * ( $newWidth - $crop_w ) );
								$crop_y		=	0;
							}
							$newX			=	$crop_x;
							$newY			=	$crop_y;
							$newWidth		=	$crop_w;
							$newHeight		=	$crop_h;
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							$thumbX			=	0;
							$thumbY			=	0;
							break;
						case "crop_dynamic":
							if ( $newWidth > $newHeight ) {
								if ( $ratio > $newRatio ) {
									$zoom		=	$newWidth / $width;
									$crop_h		=	floor( $zoom * $height );
									$crop_w		=	$newWidth;
									$crop_x		=	0;
									$crop_y		=	floor( 0.5 * ( $newHeight - $crop_h ) );
								} else {
									$zoom		=	$newHeight / $height;
									$crop_h		=	$newHeight;
									$crop_w		=	floor( $zoom * $width );
									$crop_x		=	floor( 0.5 * ( $newWidth - $crop_w ) );
									$crop_y		=	0;
								}
								$newX			=	$crop_x;
								$newY			=	$crop_y;
								$newWidth		=	$crop_w;
								$newHeight		=	$crop_h;
								$thumbWidth		=	$width;
								$thumbHeight	=	$height;
								$thumbX			=	0;
								$thumbY			=	0;
							} else {
								if ( $ratio > $newRatio ) {
									$zoom		=	$newWidth / $width;
									$crop_h		=	floor( $zoom * $height );
									$crop_w		=	$newWidth;
									$crop_x		=	0;
									$crop_y		=	floor( 0.5 * ( $newHeight - $crop_h ) );
								} else {
									$zoom		=	$newHeight / $height;
									$crop_h		=	$newHeight;
									$crop_w		=	floor( $zoom * $width );
									$crop_x		=	floor( 0.5 * ( $newWidth - $crop_w ) );
									$crop_y		=	0;
								}
								$newX			=	$crop_x;
								$newY			=	$crop_y;
								$newWidth		=	$crop_h;
								$newHeight		=	$crop_w;
								$thumbWidth		=	$height;
								$thumbHeight	=	$width;
								$width			=	$thumbWidth;
								$height			=	$thumbHeight;
								$thumbX			=	0;
								$thumbY			=	0;
							}
							break;
						case "maxfit":
							$width			=	( $width > $newWidth ) ? $newWidth : $width;
							$height			=	( $height > $newHeight ) ? $newHeight : $height;
							$width			=	( $ratio > $newRatio ) ? round( $height * $newRatio ) : $width;
							$height			=	( $ratio < $newRatio ) ? round( $width / $newRatio ) : $height;
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							break;
						case "shrink":
							$width			=	( $width > $newWidth ) ? $newWidth : $width;
							$height			=	( $height > $newHeight ) ? $newHeight : $height;
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							break;
						case "shrink_dynamic":
							if ( $newWidth > $newHeight ) {
								$width			=	( $width > $newWidth ) ? $newWidth : $width;
								$height			=	( $height > $newHeight ) ? $newHeight : $height;
								$thumbWidth		=	$width;
								$thumbHeight	=	$height;
							} else {
								$thumbWidth		=	( $height > $newWidth ) ? $newWidth : $height;
								$thumbHeight	=	( $width > $newHeight ) ? $newHeight : $width;
								$width			=	$thumbWidth;
								$height			=	$thumbHeight;
							}
							break;
						case "stretch":
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							break;
						case "stretch_dynamic":
							if ( $newWidth > $newHeight ) {
								$thumbWidth		=	$width;
								$thumbHeight	=	$height;
							} else {
								$thumbWidth		=	$height;
								$thumbHeight	=	$width;
								$width			=	$thumbWidth;
								$height			=	$thumbHeight;
							}
							break;
						default:
							break;
					}
					$thumbImage	=	imageCreateTrueColor( $width, $height );
					if ( $newExt == 'png' || $newExt == 'PNG' ) {
						imagealphablending( $thumbImage, false );
					}
					//add color
					if ( $options[$format_name] == 'addcolor' ) {
						$r		=	hexdec( substr( $options['image_color'], 1, 2 ) );
						$g		=	hexdec( substr( $options['image_color'], 3, 2 ) );
						$b		=	hexdec( substr( $options['image_color'], 5, 2 ) );
						$color	=	imagecolorallocate( $thumbImage, $r, $g, $b );
						imagefill( $thumbImage, 0, 0, $color );
					}
					//
					imagecopyresampled( $thumbImage, $resImage, $thumbX, $thumbY, $newX, $newY, $thumbWidth, $thumbHeight, $newWidth, $newHeight );
					if ( $i == 0 ) {
						//add mask
						if ( $options[$format_name] == 'maxfit' && $newHeight > $newWidth && JFile::exists( JPATH_SITE.'/'.str_replace( '.'.$waterExtI, '2.'.$waterExtI, $waterI ) ) ) {
							$maskImage	=	ImageCreateFromPNG( JPATH_SITE.'/'.str_replace( '.'.$waterExtI, '2.'.$waterExtI, $waterI ) );
							imagealphablending( $maskImage, 1 );
							imagecopy( $thumbImage, $maskImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight );
						} else {										
							if ( JFile::exists( JPATH_SITE.'/'.$waterI ) ) {
								$maskImage	=	ImageCreateFromPNG( JPATH_SITE.'/'.$waterI );
								imagealphablending( $maskImage, 1 );
								imagecopy( $thumbImage, $maskImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight );
							}
						}
						//
						$thumbLocation	=	$location;
						if ( JFile::exists( $location ) ) {
							JFile::delete( $location );
						}
					} else {
						if ( ! JFolder::exists( JPATH_SITE.'/'.$file_path.'_thumb'.$i ) ) {
							JFolder::create( JPATH_SITE.'/'.$file_path.'_thumb'.$i );
							$file_body	=	'<!DOCTYPE html><title></title>';
							JFile::write( JPATH_SITE.'/'.$file_path.'_thumb'.$i.'/index.html', $file_body );
						}
						$thumbLocation	=	JPATH_SITE.'/'.$file_path.'_thumb'.$i.'/'.$file_name;
					}
					switch( $newExt ) {
						case 'gif':
						case 'GIF':
							imagegif( $thumbImage );
							break;
						case 'jpg':
						case 'JPG':
						case 'jpeg': 
						case 'JPEG': 
							imagejpeg( $thumbImage, NULL, $process['quality_jpeg'] );
							break;
						case 'png':
						case 'PNG':
							imagesavealpha( $thumbImage, true );
							imagepng( $thumbImage, NULL, $process['quality_png'] );
							break;
						default:
							break;
					}
					$output = ob_get_contents();
					ob_flush();
					JFile::write($thumbLocation, $output); 
				}
			}
			ob_end_clean(); 
		}
		// -- Image Process End
	}
} else {
	$value	=	'';
	if ( $file_title == '' && $file_descr == '' ) {
		if ( $x2k > -1 ) {
			if ( $array_x ) { //GroupX
				$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.$old_path.$file_name.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.$file_location.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			} else { //FieldX
				$search		=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.$old_path.$file_name.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.$file_location.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
			}
		} else {
			$search		=	'||'.$name.'||'.$old_path.$file_name.'||/'.$name.'||';
			$replace	=	'||'.$name.'||||/'.$name.'::';
		}
	} else {
		if ( $x2k > -1 ) {
			if ( $array_x ) { //GroupX
				$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$old_path.$file_name.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$file_location.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			} else { //FieldX
				$search		=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$old_path.$file_name.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"image_location":"'.$file_location.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
			}
		} else {
			$search		=	'||'.$name.'||'.'{"image_location":"'.$old_path.$file_name.'","image_title":"'.$file_title.'","image_description":"'.$file_descr.'"}'.'||/'.$name.'||';
			$replace	=	'||'.$name.'||||/'.$name.'::';
		}
	}
	$doSave	=	1;
}

if ( $doSave ) {
	// Update
	$storage	=	$process['storage'];
	$table		=	$process['storage_table'];
	$field		=	$process['storage_field'];
	if ( !( isset( $search ) && isset( $replace ) ) ) {
		$field2		=	$process['storage_field2'];
		$search		=	JCck::callFunc_Array( 'plgCCK_Storage'.$storage, '_format', array( $field2, $search_v ) );
		$replace	=	JCck::callFunc_Array( 'plgCCK_Storage'.$storage, '_format', array( $field2, $replace_v ) );
	}
	JCckPluginLocation::g_onCCK_Storage_LocationUpdate( $config['pk'], $table, $field, $search, $replace );
}
?>