<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folder.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_COMPONENT.'/helpers/helper_folder.php';

// Model
class CCKModelFolder extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		$form	=	$this->loadForm( CCK_COM.'.folder', 'folder', array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		
		return $form;
	}
	
	// getItem
	public function getItem( $pk = null )
	{
		if ( $item = parent::getItem( $pk ) ) {
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Folder', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	JFactory::getApplication()->getUserState( CCK_COM.'.edit.folder.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// prepareTable2
	protected function prepareTable2( &$table, &$data )
	{
		if ( is_array( $data['elements'] ) ) {
			$data['elements']	=	implode( ',', $data['elements'] );
		} else {
			$data['elements']	=	'';
		}
		if ( !$data['jform']['id'] && !$data['jform']['rules'] ) {
			$data['jform']['rules']	=	array( 'core.create'=>array(),
											   'core.delete'=>array(),
											   'core.delete.own'=>array(),
											   'core.edit'=>array(),
											   'core.edit.state'=>array(),
											   'core.edit.own'=>array()
										);
		}
		if ( $data['jform']['rules'] ) {
			if ( !is_array( $data['jform']['rules'] ) ) {
				$data['jform']['rules']	=	json_decode( $data['jform']['rules'] );
			}
			$rules	=	new JAccessRules( JCckDevHelper::getRules( $data['jform']['rules'] ) );
			$table->setRules( $rules );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- //
	
	// prepareData
	protected function prepareData()
	{
		$app					=	JFactory::getApplication();
		$data					=	JRequest::get( 'post' );
		$data['description']	=	JRequest::getVar( 'description', '', '', 'string', JREQUEST_ALLOWRAW );
		
		if ( ( ( $data['parent_id'] != $data['parent_db'] ) && ( $data['parent_id'] != $data['jform']['id'] ) ) || ( $app->input->getCmd( 'task' ) == 'save2copy' ) ) {
			$limit				=	Helper_Folder::prepareTree( $data['parent_id'], $data['title'] );
			if ( $limit ) {
				$limits			=	explode( '||', $limit );
				$data['lft']	=	$limits[0];
				$data['rgt']	=	$limits[1];
			}
		}
		
		return $data;
	}
	
	// prepareExport
	public function prepareExport( $id = 0, $elements = array(), $dependencies = array(), $options = array() )
	{
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		$folders	=	( isset( $elements['subfolders'] ) ) ? Helper_Folder::getBranch( $id, ',' ) : $id;
		$folders	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_folders WHERE id IN ('.(string)$folders.') ORDER BY lft' );
		if ( !@$folders[0] ) {
			return;
		}
		$isApp		=	false;
		$isCck		=	false;
		$name		=	$folders[0]->name;
		if ( !$name ) {
			return;
		}
		
		// Core
		jimport( 'joomla.filesystem.file' );
		jimport( 'cck.base.install.export' );
		$data										=	array( 'root'=>$path,
															   'root_content'=>$path.'/content',
															   'root_elements'=>$path.'/elements',
															   'root_extensions'=>$path.'/extensions',
															   'root_sql'=>$path.'/sql',
															   'root_category'=>'',
															   'elements'=>array(),
															   'db_prefix'=>$config->get( 'dbprefix' )
															);
		$extensions									=	array( 0=>(object)array( 'type'=>'plugin', 'id'=>'plg_system_blank', 'group'=>'system', '_file'=>'plg_system_blank.zip' ) );
		$data['folders']							=	JCckDatabase::loadObjectList( 'SELECT id, name, path FROM #__cck_core_folders WHERE lft', 'id' );
		$data['folders2']							=	JCckDatabase::loadObjectList( 'SELECT id, name, path FROM #__cck_core_folders WHERE lft', 'name' );
		$data['plugins']							=	CCK_Export::getCorePlugins();
		$data['plugins']['cck_field_live']['stage']	=	true;
		$data['processings']						=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_more_processings', 'id' );
		$data['processings2']						=	JCckDatabase::loadObjectList( 'SELECT folder FROM #__cck_more_processings', 'folder' );
		$data['styles']								=	JCckDatabase::loadObjectList( 'SELECT * FROM #__template_styles', 'id' );
		$data['tables']								=	array_flip( JCckDatabase::loadColumn( 'SHOW TABLES' ) );
		$data['tables_excluded']					=	CCK_Export::getCoreTables();
		$data['variations']							=	array(
															'empty'=>true,
															'joomla'=>true,
															'seb_css3'=>true,
															'seb_css3b'=>true
														);
		
		// Copyright
		if ( JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_cck_packager"' ) > 0 ) {
			$params		=	JComponentHelper::getParams( 'com_cck_packager' );
			$copyright	=	$params->get( 'copyright' );
		} else {
			$copyright	=	'';
		}

		CCK_Export::createDir( $data['root_content'] );
		CCK_Export::createDir( $data['root_elements'] );
		CCK_Export::createDir( $data['root_extensions'] );
		CCK_Export::createDir( $data['root_sql'] );
		
		if ( isset( $dependencies['categories'] ) ) {
			$data['root_category']	=	CCK_Export::exportRootCategory( $folders['0'], $data, $extensions );
		}
		if ( isset( $dependencies['menu'] ) ) {
			$data['root_menu']	=	CCK_Export::exportMenus( $dependencies['menu'], $data, $extensions );
		}
		foreach ( $folders as $i=>$folder ) {
			if ( $i == 0 ) {
				if ( $folder->path && $folder->path != $folder->name ) {
					$branch	=	explode( '/', $folder->path );
					array_pop( $branch );
					if ( count( $branch ) ) {
						$parent_id	=	2;
						foreach ( $branch as $k=>$v ) {
							$elem		=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_folders WHERE name = "'.(string)$v.'" AND parent_id = '.(int)$parent_id );
							$parent_id	=	$elem->id;
							CCK_Export::createDir( $data['root_elements'].'/folder'.'s' );
							CCK_Export::exportElement( 'folder', $elem, $data, $extensions, 0 );
						}
					}
				}
			}
			CCK_Export::exportElements( 'folder', $folders, $data, $extensions, 0, $copyright );
			
			if ( isset( $elements['fields'] ) ) {
				$fields		=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.folder = '.(int)$folder->id );
				CCK_Export::exportElements( 'field', $fields, $data, $extensions, 500, $copyright );
			}
			if ( isset( $elements['templates'] ) ) {
				$templates	=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_templates AS a WHERE a.folder = '.(int)$folder->id );
				CCK_Export::exportElements( 'template', $templates, $data, $extensions, 0, $copyright );
			}
			if ( isset( $elements['types'] ) ) {
				$types		=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_types AS a WHERE a.folder = '.(int)$folder->id );
				if ( count( $types ) ) {
					$isApp	=	true;
				}
				CCK_Export::exportElements( 'type', $types, $data, $extensions, 0, $copyright );
			}
			if ( isset( $elements['searchs'] ) ) {
				$searchs	=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_searchs AS a WHERE a.folder = '.(int)$folder->id );
				if ( count( $searchs ) ) {
					$isApp	=	true;
				}
				CCK_Export::exportElements( 'search', $searchs, $data, $extensions, 0, $copyright );
			}
		}
		
		if ( count( $data['elements']['tables'] ) ) {
			CCK_Export::exportTables( $data );
		}

		if ( count( $data['elements']['processings'] ) ) {
			$isCck	=	true;

			CCK_Export::exportProcessings( $data, $extensions );
		}

		// Name & Root
		if ( $isApp ) {
			$filename				=	'app_cck_'.$name;
		} else {
			if ( $isCck ) {
				$filename			=	'cck_'.$name;
			} else {
				$filename			=	$name;	
			}
			if ( isset( $dependencies['categories'] ) && file_exists( $data['root_content'].'/joomla_category' ) ) {
				$items	=	JFolder::files( $data['root_content'].'/joomla_category', '\.xml$' );
				if ( count( $items ) == 1 && isset( $items[0] ) && $data['root_category'] != ''
				&&  JFile::stripExt( $items[0] ) == $data['root_category'] ) {
					JFolder::delete( $data['root_content'].'/joomla_category' );
				}
			}
		}

		// Elements
		if ( $copyright ) {
			CCK_Export::update( $data['root_elements'], $copyright );
		}

		// Xml
		$folders[0]->description	=	'SEBLOD 3.x '.$folders[0]->title.' App - www.seblod.com';
		$folders[0]->name			=	$filename;
		$folders[0]->title			=	'pkg_'.$folders['0']->name;
		$manifest					=	NULL;
		$xml						=	CCK_Export::preparePackage( $folders[0] );
		
		if ( is_object( $xml ) ) {
			$manifest		=	JPATH_ADMINISTRATOR.'/manifests/packages/pkg_'.$filename.'.xml';

			if ( is_file( $manifest ) ) {
				if ( $copyright ) {
					CCK_Export::update( $manifest, $copyright );
				}

				$manifest	=	JCckDev::fromXML( $manifest );
				$tags		=	array(
								'copyright',
								'creationDate',
								'description',
								'packager',
								'packageurl',
								'version'
							);
				if ( is_object( $manifest ) ) {
					foreach ( $tags as $tag ) {
			 			if ( isset( $manifest->$tag ) && isset( $xml->$tag ) && $manifest->$tag != $xml->$tag ) {
							$xml->$tag	=	(string)$manifest->$tag;
						}
					}
				}
			}
		}

		// Filename
		$path_zip					=	$tmp_path.'/'.$filename;
		if ( isset( $options['filename_version'] ) && $options['filename_version'] ) {
			$path_zip				.=	'_'.( ( isset( $manifest->version ) ) ? $manifest->version : '1.0.0' );
		}
		if ( isset( $options['filename_date'] ) && $options['filename_date'] ) {
			$path_zip				.=	'_'.JFactory::getDate()->format( 'Y_m_d' );
		}
		$path_zip					.=	'.zip';

		// Script
		if ( is_file( JPATH_ADMINISTRATOR.'/manifests/packages/'.$name.'/pkg_script.php' ) ) {
			if ( $copyright ) {
				CCK_Export::update( JPATH_ADMINISTRATOR.'/manifests/packages/'.$name.'/pkg_script.php', $copyright );
			}
			JFile::copy( JPATH_ADMINISTRATOR.'/manifests/packages/'.$name.'/pkg_script.php', $path.'/pkg_script.php' );
		} else {
			JFile::copy( JPATH_SITE.'/libraries/cck/development/apps/script.php', $path.'/pkg_script.php' );
			$buffer					=	file_get_contents( $path.'/pkg_script.php' );
			$buffer					=	str_replace( '%class%', $filename, $buffer );
			JFile::write( $path.'/pkg_script.php', $buffer );
			if ( $copyright ) {
				CCK_Export::update( $path.'/pkg_script.php', $copyright );
			}
		}
		$script						=	$xml->addChild( 'scriptfile', 'pkg_script.php' );

		// Extensions
		$files						=	$xml->addChild( 'files' );
		$files->addAttribute( 'folder', 'extensions' );
		$names						=	array();
		foreach ( $extensions as $ext ) {
			$file					=	$files->addChild( 'file', $ext->_file );
			$names[$ext->_file]		=	'';
			foreach ( $ext as $k=>$v ) {
				if ( $k != '_file' ) {
					$file->addAttribute( $k, $v );
				}
			}
		}
		if ( isset( $manifest->files->file ) && count( $manifest->files->file ) ) {
			foreach ( $manifest->files->file as $f ) {
				$f_name			=	(string)$f;
				if ( !isset( $names[$f_name] ) ) {
					$f_file		=	array(
										'_'=>$f_name,
									);
					$f_client	=	(string)$f->attributes()->client;
					$f_id		=	(string)$f->attributes()->id;
					$f_type		=	(string)$f->attributes()->type;
					if ( $f_type == 'template' ) {
						$f_file['lang_root']=	JPATH_SITE;
						$f_file['src']		=	JPATH_SITE.'/templates/'.( ( strpos( $f_id, 'tpl_' ) !== false && strpos( $f_id, 'tpl_' ) == 0 ) ? substr( $f_id, 4 ) : $f_id );
						$f_file['lang_src']	=	$f_file['src'].'/templateDetails.xml';
					} else {
						// todo
					}
					if ( is_array( $f_file ) && $f_file['src'] != '' ) {
						CCK_Export::exportFile( $f_type, $data, $f_file, array(), $copyright );
						$file				=	$files->addChild( 'file', $f_name );
						$file->addAttribute( 'type', $f_type );
						$file->addAttribute( 'id', $f_id );
						$file->addAttribute( 'client', $f_client );
					}
				}
			}
		}
		
		// Languages
		$dest		=	CCK_Export::createDir( $path.'/languages' );
		$languages	=	JCckDatabase::loadColumn( 'SELECT element FROM #__extensions WHERE type = "language" AND client_id = 0' );
		if ( count( $languages ) ) {
			$lang	=	$xml->addChild( 'languages' );
			$lang->addAttribute( 'folder', 'languages' );
			foreach ( $languages as $language ) {
				if ( is_file( JPATH_SITE.'/language/'.$language.'/'.$language.'.pkg_'.$filename.'.ini' ) ) {
					$l	=	$lang->addChild( 'language', $language.'/'.$language.'.pkg_'.$filename.'.ini' );
					$l->addAttribute( 'tag', $language );
					CCK_Export::createDir( $path.'/languages/'.$language );

					if ( $copyright ) {
						CCK_Export::update( JPATH_SITE.'/language/'.$language.'/'.$language.'.pkg_'.$filename.'.ini', $copyright );
					}
					JFile::copy( JPATH_SITE.'/language/'.$language.'/'.$language.'.pkg_'.$filename.'.ini', $dest.'/'.$language.'/'.$language.'.pkg_'.$filename.'.ini' );
				}
				if ( is_file( JPATH_SITE.'/language/'.$language.'/'.$language.'.pkg_'.$filename.'.sys.ini' ) ) {
					$l	=	$lang->addChild( 'language', $language.'/'.$language.'.pkg_'.$filename.'.sys.ini' );
					$l->addAttribute( 'tag', $language );
					CCK_Export::createDir( $path.'/languages/'.$language );

					if ( $copyright ) {
						CCK_Export::update( JPATH_SITE.'/language/'.$language.'/'.$language.'.pkg_'.$filename.'.sys.ini', $copyright );
					}
					JFile::copy( JPATH_SITE.'/language/'.$language.'/'.$language.'.pkg_'.$filename.'.sys.ini', $dest.'/'.$language.'/'.$language.'.pkg_'.$filename.'.sys.ini' );
				}
			}
		}

		// Media
		if ( file_exists( JPATH_SITE.'/media/cck/apps/'.$name ) ) {
			JFolder::copy( JPATH_SITE.'/media/cck/apps/'.$name, $path.'/media' );	
		}
		
		// Manifest
		JFile::copy( JPATH_LIBRARIES.'/cck/base/install/_plg_system_blank.zip', $path.'/extensions/plg_system_blank.zip' );
		if ( is_object( $manifest ) && isset( $manifest->updateservers ) ) {
			$servers	=	$xml->addChild( 'updateservers' );
			if ( count( $manifest->updateservers->server ) ) {
				foreach ( $manifest->updateservers->server as $server ) {
					$s	=	$servers->addChild( 'server', (string)$server );
					$s->addAttribute( 'type', (string)$server->attributes()->type );
					$s->addAttribute( 'priority', (string)$server->attributes()->priority );
					$s->addAttribute( 'name', (string)$server->attributes()->name );
				}
			}
		}

		CCK_Export::clean( $path );
		CCK_Export::createFile( $path.'/pkg_'.$filename.'.xml', '<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML() );

		if ( $copyright ) {
			CCK_Export::update( $path.'/pkg_'.$filename.'.xml', $copyright );
		}
		return CCK_Export::zip( $path, $path_zip );
	}
	
	// clearACL
	public function clearACL( $pks )
	{
		require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
		
		if ( count( $pks ) ) {
			return Helper_Admin::initACL( array( 'table'=>'folder', 'name'=>'folder',
												 'rules'=>'{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}' ), $pks );
		}
		
		return false;
	}

	// rebuild
	public function rebuild( $cid )
	{
		$lft	=	1;
		
		if ( !$cid ) {
			return false;
		} elseif ( $cid != 2 ) {
			$lft	=	JCckDatabase::loadResult( 'SELECT a.lft FROM #__cck_core_folders AS a WHERE a.id = '.(int)$cid );
		}
		
		Helper_Folder::rebuildTree( $cid, $lft );

		return true;
	}
}
?>