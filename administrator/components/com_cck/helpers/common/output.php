<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: output.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

// CommonHelper
class CommonHelper_Output
{
	// init
	public static function init( $name, $extension, $params )
	{
		// Init
		$name_date	=	$params->get( 'filename_date', '' );
		$tmp_path	=	JFactory::getConfig()->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_' );
		
		// Set
		$output					=	new stdClass;
		$output->name			=	$name;
		$output->suffix			=	( $name_date != '' ) ? '_'.JFactory::getDate()->format( $name_date ) : '';
		$output->path 			= 	$tmp_path.'/'.$tmp_dir;
		$output->root			=	$output->path.'/'.$extension;
		$output->output			=	$params->get( 'output', 0 );
		$output->output_path	=	$params->get( 'output_path', '' );
		$output->compression	=	$params->get( 'compression', 'zip' );
		
		if ( $output->output == 2 && $output->output_path != '' && JFolder::exists( $output->output_path ) ) {
			$output->output_path	=	$output->output_path;
		} elseif ( $output->output_path != '' && $output->output_path != 'tmp/' ) {
			$output->output_path	=	JPATH_SITE.'/'.$output->output_path;
			if ( !JFolder::exists( $output->output_path ) ) {
				jimport( 'cck.base.install.export' );
				CCK_Export::createDir( $output->output_path );
			}
		} else {
			$output->output_path	=	$tmp_path;
		}
		
		return $output;
	}
	
	// finalize
	public static function finalize( $output )
	{
		if ( $output->compression == 'zip' ) {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/pclzip/pclzip.lib.php';
			$tmp		=	$output->path.'/'.$output->name.'.zip';
			$archive	=	new PclZip( $tmp );
			if ( $archive->create( $output->root, PCLZIP_OPT_REMOVE_PATH, $output->root ) == 0 ) {
				return false;
			}
			$ext	=	'.zip';
		} else {
			$ext	=	'.'.$extension;
		}
		
		if ( JFile::exists( $tmp ) ) {
			$file	=	$output->output_path.'/'.$output->name.$output->suffix.$ext;
			JFile::move( $tmp, $file );
			
			if ( JFolder::exists( $output->path ) ) {
				JFolder::delete( $output->path );
			}
			
			return $file;
		}
		
		return false;
	}
}
?>