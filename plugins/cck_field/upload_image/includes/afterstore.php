<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
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
	JError::raiseWarning( '', JText::_( 'WARNINSTALLFILE' ) );
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

JCckDevHelper::createFolder( JPATH_SITE.'/'.$file_path, $permissions );

if ( JFile::upload( $tmp_name, $location ) ) {
	$thumb_count				=	11;
	$image 						=	new JCckDevImage( $location );
	$src_w						=	$image->getWidth();
	$src_h						=	$image->getHeight();
	$value						=	$file_location;
	$fields[$name]->value		=	$value;
	
	$options['thumb0_process']	=	$options['image_process'];
	$options['thumb0_width']	=	$options['image_width'];
	$options['thumb0_height']	=	$options['image_height'];
	
	for ( $i = 0; $i < $thumb_count; $i++ ) {
		$format_name	=	'thumb'.$i.'_process';
		$width_name		=	'thumb'.$i.'_width';
		$height_name	=	'thumb'.$i.'_height';

		if ( $i == 0 && $src_w == $options[$width_name] && $src_h == $options[$height_name] ) {
			continue;
		}
		if ( trim( $options[$format_name] ) ) {
			$image->createThumb( '', $i, $options[$width_name], $options[$height_name], $options[$format_name] );
		}
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