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
$file_path		=	$process['file_path'];
$file_name		=	$process['file_name'];
$file_title		=	$process['file_title'];
$tmp_name		=	$process['tmp_name'];
$content_folder	=	$process['content_folder'];
$x2k			=	$process['xi'];
$array_x		=	$process['array_x'];
$true_name		=	$process['true_name'];
$options		=	$process['options2'];
$permissions	=	( isset( $options['folder_permissions'] ) && $options['folder_permissions'] ) ? octdec( $options['folder_permissions'] ) : 0755;

if ( !(bool) ini_get( 'file_uploads' ) ) {
	JError::raiseWarning( '', JText::_( 'WARNINSTALLFILE' ) );
}

$doSave			=	0;
$old_path		=	$file_path;
if ( $content_folder && $config['isNew'] ) {
	$file_path		.=	$config['pk'].'/';
	$file_location	=	$file_path.$file_name;
	$location		=	JPATH_SITE.'/'.$file_path.$file_name;
	if ( $file_title == '' ) {
		if ( $x2k > -1 ) {
			if ( $array_x ) { //GroupX (custom!)
				$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.$old_path.$file_name.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.$file_location.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			} else { //FieldX (custom!)
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
				$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$old_path.$file_name.'","file_title":"'.$file_title.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$file_location.'","file_title":"'.$file_title.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			} else { //FieldX (custom!)
				$search		=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$old_path.$file_name.'","file_title":"'.$file_title.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
				$replace	=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$file_location.'","file_title":"'.$file_title.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
			}
		} else {
			$search_v	=	'{"file_location":"'.$old_path.$file_name.'","file_title":"'.$file_title.'"}';
			$replace_v	=	'{"file_location":"'.$file_location.'","file_title":"'.$file_title.'"}';
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
$safeFileOptions		=	array();

if ( $process['forbidden_ext'] ) {
	$forbiddenExtensions	=	array( 'php', 'phps', 'php5', 'php3', 'php4', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py' );
	$safeExtensions			=	JCck::getConfig_Param( 'media_content_forbidden_extensions_whitelist', 'php' );

	if ( $safeExtensions != '' ) {
		$safeExtensions		=	explode( ',', $safeExtensions );

		if ( count( $safeExtensions ) ) {
			$safeExtensions		=	array_diff( $forbiddenExtensions, $safeExtensions );
			$safeFileOptions	=	array(
										'forbidden_extensions'=>$safeExtensions
									);
		}
	}
}
if ( JFile::upload( $tmp_name, $location, false, false, $safeFileOptions ) ) {
	$value					=	$file_location;
	$fields[$name]->value	=	$value;
} else {
	$value					=	'';

	if ( $x2k > -1 ) {
		if ( $array_x ) { //GroupX
			$search		=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$old_path.$file_name.'","file_title":"'.$file_title.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
			$replace	=	'::'.$name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$file_location.'","file_title":"'.$file_title.'"}'.'::/'.$name.'|'.$x2k.'|'.$parent_name.'::';
		} else { //FieldX
			$search		=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$old_path.$file_name.'","file_title":"'.$file_title.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
			$replace	=	'::'.$true_name.'|'.$x2k.'|'.$parent_name.'::'.'{"file_location":"'.$file_location.'","file_title":"'.$file_title.'"}'.'::/'.$true_name.'|'.$x2k.'|'.$parent_name.'::';
		}
	} else {
		$search		=	'||'.$name.'||'.'{"file_location":"'.$old_path.$file_name.'","file_title":"'.$file_title.'"}'.'||/'.$name.'||';
		$replace	=	'||'.$name.'||||/'.$name.'::';
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