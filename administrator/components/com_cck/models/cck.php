<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Model
class CCKModelCCK extends JModelLegacy
{
	// batchFolder
	public function batchFolder( $pks, $type )
	{
		$app	=	JFactory::getApplication();
		$folder	=	$app->input->getInt( 'batch_folder', 0 );
		if ( !$folder ) {
			return false;
		}
		
		return JCckDatabase::execute( 'UPDATE #__cck_core_'.$type.' SET folder = '.$folder. ' WHERE id IN ('.$pks.')' );
	}
	
	// prepareExport
	public function prepareExport( $id = 0 )
	{
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		$extension	=	JCckDatabase::loadObject( 'SELECT name, type, element, folder FROM #__extensions WHERE extension_id='.(int)$id );
		if ( !$extension ) {
			return;
		}
		
		jimport( 'cck.base.install.export' );
		$name		=	$extension->element;
		$prefix		=	CCK_Export::getPrefix( $extension->type );
		$src		=	JPATH_SITE.'/plugins/'.$extension->folder.'/'.$extension->element;
		$xml		=	JCckDev::fromXML( $src.'/'.$name.'.xml' );
		$version	=	( isset( $xml->version ) ) ? '_'.$xml->version : '';
		$filename	=	$prefix.'_'.$extension->folder.'_'.$name.$version;
		$path_zip	=	$tmp_path.'/'.$filename.'.zip';
		if ( !$filename ) {
			return;
		}
		
		if ( JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_cck_packager"' ) > 0 ) {
			$params		=	JComponentHelper::getParams( 'com_cck_packager' );
			$copyright	=	$params->get( 'copyright' );
		} else {
			$copyright	=	'';
		}

		if ( $src && JFolder::exists( $src ) ) {
			if ( $copyright ) {
				CCK_Export::update( $src, $copyright );
			}
			JFolder::copy( $src, $path );
			CCK_Export::clean( $path );
		}
		CCK_Export::exportLanguage( $src.'/'.$name.'.xml', JPATH_ADMINISTRATOR, $path, $copyright );
		CCK_Export::findFields( array( $src.'/tmpl/edit.php', $src.'/tmpl/edit2.php' ), $path.'/install' );
		CCK_Export::update( $path.'/install', $copyright );
		
		return CCK_Export::zip( $path, $path_zip );
	}
	
	// prepareLanguages
	public function prepareLanguages( $lang )
	{
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		
		$name		=	'seblod2';
		$filename	=	$lang.'_'.$name;
		$path_zip	=	$tmp_path.'/'.$filename.'.zip';
		if ( !$filename ) {
			return;
		}

		// Core
		jimport( 'cck.base.install.export' );
		$manifest	=	JCckDev::fromXML( JPATH_ADMINISTRATOR.'/manifests/packages/pkg_cck.xml' );
		$extensions	=	array();
		if ( count( @$manifest->files->file ) ) {
			foreach ( $manifest->files->file  as $file ) {
				$id					=	(string)$file;
				$id					=	str_replace( '.zip', '', $id );
				if ( strpos( $id, 'var_cck_' ) !== false ) {
					$id				=	'files_'.$id;
				}
				$extensions[$id]	=	'';
			}
		}
		$extensions['com_cck_core']		=	'';
		$extensions['com_cck_default']	=	'';
		
		// Admin
		$dest	=	$path.'/admin';
		CCK_Export::exportLanguages( JPATH_ADMINISTRATOR.'/language/'.$lang, $dest, $lang, 'admin', 'cck', $extensions );
		CCK_Export::zip( $dest, $path.'/cck_'.$lang.'_admin.zip' );
		
		// Site
		$dest	=	$path.'/site';		
		CCK_Export::exportLanguages( JPATH_SITE.'/language/'.$lang, $dest, $lang, 'site', 'cck|tpl_seb', $extensions );
		CCK_Export::zip( $dest, $path.'/cck_'.$lang.'_site.zip' );
		
		// Manifest
		$package	=	(object)array( 'title'=>'SEBLOD '.$lang, 'name'=>'cck_'.$lang, 'description'=>'SEBLOD 3.x '.$lang.' Language Pack - www.seblod.com' );
		$xml		=	CCK_Export::preparePackage( $package );
		$files		=	$xml->addChild( 'files' );
		$file1		=	$files->addChild( 'file', 'cck_'.$lang.'_admin.zip' );
		$file1->addAttribute( 'type', 'file' );
		$file1->addAttribute( 'src', 'administrator/language/'.$lang );
		$file1->addAttribute( 'id', 'cck_'.$lang.'_admin' );
		$file2		=	$files->addChild( 'file', 'cck_'.$lang.'_site.zip' );
		$file2->addAttribute( 'type', 'file' );
		$file2->addAttribute( 'src', 'language/'.$lang );
		$file2->addAttribute( 'id', 'cck_'.$lang.'_site' );
		
		CCK_Export::clean( $path );
		CCK_Export::createFile( $path.'/pkg_cck_'.$lang.'.xml', '<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML() );
		
		return CCK_Export::zip( $path, $path_zip );
	}
}
?>