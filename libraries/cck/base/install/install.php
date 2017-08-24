<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: install.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
define( 'CCK_COM', 'com_cck' );

jimport( 'joomla.filesystem.folder' );

// Install
class CCK_Install
{
	// init
	public static function init( &$parent )
	{
		$cck		=	new stdClass;
		$cck->xml	=	$parent->getParent()->getManifest();
		$files		=	$cck->xml->files;
		
		$cck->type	=	strtolower( (string)$cck->xml->attributes()->type );
		if ( $cck->type == 'component' ) {
			$cck->element	=	strtolower( (string)$cck->xml->name );
		} elseif ( $cck->type == 'plugin' ) {
			$cck->group	=	strtolower( (string)$cck->xml->attributes()->group );
			if ( count( $files->children() ) ) {
				foreach ( $files->children() as $file ) {
					if ( (string)$file->attributes()->plugin ) {
						$cck->element	=	strtolower( (string)$file->attributes()->plugin );
						break;
					}
				}
			}
		}

		return $cck;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Import
	
	// import
	public static function import( &$parent, $folder = 'elements', $extension = NULL )
	{
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$folders	=	JFolder::folders( $tmp_path );
		$root		=	$parent->getParent()->getPath( 'source' );
		if ( $folder == 'elements' && !$extension ) {
			$root	=	substr( $root, 0, strrpos( $root, DIRECTORY_SEPARATOR ) );
			$root	=	substr( $root, 0, strrpos( $root, DIRECTORY_SEPARATOR ) );
		}
		$path	=	$root.'/'.$folder;
		
		// Core
		jimport( 'cck.base.install.import' );
		$config				=	array( 'isApp'=>false, 'isUpgrade'=>false );
		if ( isset( $extension->isApp ) && $extension->isApp ) {
			$config['isApp']		=	true;
			$config['isUpgrade']	=	$extension->isUpgrade;
		}
		$data				=	array( 'base'=>$root, 'root'=>$path, 'root_category'=>0, 'categories'=>array(), 'fields'=>array(), 'styles'=>array() );
		$data['elements']	=	array( 'folder'=>'folders', 'field'=>'fields', 'type'=>'types', 'search'=>'searchs', 'template'=>'templates', 'template_style'=>'template_styles', 'category'=>'categories' );
		$data['folders']	=	JCckDatabase::loadObjectList( 'SELECT id, name FROM #__cck_core_folders WHERE lft', 'name' );
		$data['folders2']	=	JCckDatabase::loadObjectList( 'SELECT id, path FROM #__cck_core_folders WHERE lft', 'path' );
		
		// Content
		// if ( $isNewApp ) {}
		self::_import( 'content', $data, $config );
		
		// Elements
		self::_importMore( 'category', $data, $config ); //todo >> self::_import( 'content', 'joomla_category', $data );
		
		self::_importElements( 'folder', $data, $config );
		$data['folders']	=	JCckDatabase::loadObjectList( 'SELECT id, name FROM #__cck_core_folders WHERE lft', 'name' );
		$data['folders2']	=	JCckDatabase::loadObjectList( 'SELECT id, path FROM #__cck_core_folders WHERE lft', 'path' );
		
		self::_importElements( 'template', $data, $config );
		self::_importMore( 'template_style', $data, $config );
		$data['styles']		=	array( 'default'=>JCckDatabase::loadObjectList( 'SELECT id, template FROM #__template_styles GROUP BY template ORDER BY id ASC', 'template' ),
									   'custom'	=>JCckDatabase::loadObjectList( 'SELECT id, title FROM #__template_styles WHERE title LIKE "%\)"', 'title' ) );
		
		self::_importElements( 'field', $data, $config );
		$data['fields']		=	JCckDatabase::loadObjectList( 'SELECT id, name, type, divider FROM #__cck_core_fields', 'name' );
		
		self::_importElements( 'type', $data, $config );
		self::_importElements( 'search', $data, $config );
		
		// Media
		if ( file_exists( $root.'/media' ) ) {
			$xml	=	$parent->getParent()->getManifest();
			if ( isset( $extension->type ) && $extension->type == 'package' ) {
				$pos	=	strpos( $extension->xml->packagename, 'app_cck_' );
				if ( $pos !== false && $pos == 0 ) {
					$name	=	substr( $extension->xml->packagename, 8 );
					JFolder::copy( $root.'/media', JPATH_SITE.'/media/cck/apps/'.$name, '', true );
				}
			}
			
		}

		// Processings
		CCK_Import::importProcessings( $data );

		// SQL
		CCK_Import::importSQL( $root.'/sql' );
		
		// Tables
		CCK_Import::importTables( $data );
		
		// Clean
		if ( is_object( $extension ) ) {
			if ( isset( $extension->type ) ) {
				$path	=	'';
				if ( $extension->type == 'plugin' ) {
					$path	=	JPATH_SITE.'/plugins/'.$extension->group.'/'.$extension->element.'/install/fields';
				} elseif ( $extension->type == 'component' ) {
					$path	=	JPATH_ADMINISTRATOR.'/components/'.$extension->element.'/install/fields';
				}
				if ( $path != '' && JFolder::exists( $path ) ) {
					JFolder::delete( $path );
				}
			}
		}
	}
	
	// _import
	protected static function _import( $folder, &$data, $config = array() )
	{
		$call	=	'import'.$folder;
		$root	=	$data['base'].'/'.$folder;
		
		if ( file_exists( $root ) ) {
			$folders	=	JFolder::folders( $root );
			if ( count( $folders ) ) {
				foreach ( $folders as $folder ) {
					$path	=	$root.'/'.$folder;
					$items	=	JFolder::files( $path, '\.xml$' );
					if ( count( $items ) ) {
						CCK_Import::$call( $folder, $path.'/', $items, $data, $config );
					}
				}
			}
		}
	}
	
	// importElements
	public static function _importElements( $elemtype, &$data, $config = array() )
	{
		if ( file_exists( $data['root'].'/'.$data['elements'][$elemtype] ) ) {
			$items	=	JFolder::files( $data['root'].'/'.$data['elements'][$elemtype], '\.xml$' );
			if ( count( $items ) ) {
				CCK_Import::importElements( $elemtype, $data['root'].'/'.$data['elements'][$elemtype].'/', $items, $data, $config );
			}
		}
	}
	
	// _importMore
	public static function _importMore( $elemtype, &$data, $config = array() )
	{
		if ( file_exists( $data['root'].'/'.$data['elements'][$elemtype] ) ) {
			$items	=	JFolder::files( $data['root'].'/'.$data['elements'][$elemtype], '\.xml$' );
			if ( count( $items ) ) {
				CCK_Import::importMore( $elemtype, $data['root'].'/'.$data['elements'][$elemtype].'/', $items, $data );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Manage
	
	// manageAddon
	public static function manageAddon( $event, $addon )
	{
		$db		=	JFactory::getDbo();
		
		if ( $event == 'install' ) {
			$query	=	'SELECT id FROM #__menu WHERE link = "index.php?option=com_'.$addon['name'].'"';
			$db->setQuery( $query );
			$id		=	$db->loadResult();
			
			if ( $id > 0 ) {
				JLoader::register( 'JTableMenu', JPATH_PLATFORM.'/joomla/database/table/menu.php' );
				$table	=	JTable::getInstance( 'Menu' );
				$table->load( $id );
				
				$query		=	'SELECT id FROM #__menu WHERE link = "index.php?option=com_cck"';
				$db->setQuery( $query );
				$seblod_id		=	$db->loadResult();
				
				if ( $seblod_id > 0 ) {
					$data	=	array(
									'alias'=>ucfirst( $addon['title'] ),
									'parent_id'=>$seblod_id,
									'title'=>'com_'.$addon['name'].'_title'
								);

					$table->setLocation( $seblod_id, 'last-child' );
					$table->bind( $data );
					$table->check();
					$table->store();

					$db->setQuery( 'UPDATE #__menu SET alias = "'.$addon['title'].'", path = "SEBLOD/'.$addon['title'].'" WHERE id = '.(int)$table->id. ' AND client_id = 1' );
					$db->execute();
				}
			}
		} else {
			$db->setQuery( 'DELETE FROM #__menu WHERE link = "index.php?option=com_'.$addon['name'].'" AND parent_id = 1' );
			$db->execute();
		}
	}
}
?>