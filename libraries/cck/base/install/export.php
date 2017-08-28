<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: export.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.utilities.simplexml' );
jimport( 'cck.joomla.utilities.xmlelement' );

JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );
JLoader::register( 'JTableMenuType', JPATH_PLATFORM.'/legacy/table/menu/type.php' );
JLoader::register( 'JTableMenu', JPATH_PLATFORM.'/legacy/table/menu.php' );

// Export
class CCK_Export
{
	// clean
	public static function clean( $path )
	{
		$folders	=	JFolder::folders( $path, '.', true, true, array(), array() );
		if ( count( $folders ) ) {
			foreach ( $folders as $folder ) {
				if ( strpos( $folder, '__MACOSX' ) !== false ) {
					JFolder::delete( $file );
				}
			}
		}
		
		$files	=	JFolder::files( $path, '.', true, true, array(), array() );
		if ( count( $files ) ) {
			foreach ( $files as $file ) {
				if ( strpos( $file, '.DS_Store' ) !== false ) {
					JFile::delete( $file );
				}
			}
		}
		
		return true;
	}

	// createFile
	public static function createFile( $path, $buffer )
	{
		JFile::write( $path, $buffer );
		
		return $path;
	}
	
	// createDir	
	public static function createDir( $path )
	{
		if ( ! JFolder::exists( $path ) ) {
			JFolder::create( $path );
			$buffer	=	'<!DOCTYPE html><title></title>';
			JFile::write( $path.'/index.html', $buffer );
		}
		
		return $path;
	}
	
	// getPrefix
	public static function getPrefix( $type )
	{
		switch ( $type ) {
			case 'component': return 'com';
			case 'module': return 'mod';
			case 'plugin': return 'plg';
			case 'template': return 'tpl';
			default: return '';
		}
	}
	
	// update
	public static function update( $path, $copyright )
	{
		$extensions	=	array();

		if ( is_dir( $path ) ) {
			$paths	=	JFolder::files( $path, '(.*)\.(css|ini|js|php|xml)$', true, true );
		} elseif ( is_file( $path ) ) {
			$paths	=	array( 0=>$path );
		} else {
			return;
		}

		if ( count( $paths ) ) {
			$old	=	'2013';

			foreach ( $paths as $k=>$path ) {
				if ( is_file( $path ) ) {
					$isUpToDate		=	true;

					// Copyright
					if ( $copyright ) {
						$buffer		=	file_get_contents( $path );
						$ext		=	JFile::getExt( $path );
						$replace	=	'Copyright (C) 2009 - '.(string)$copyright.' SEBLOD.';

						if ( strpos( $buffer, $replace ) === false ) {
							$search		=	'Copyright (C) 2009 - '.( $copyright - 1 ).' SEBLOD.';
							$search2	=	'Copyright (C) '.$old.' SEBLOD.';

							if ( strpos( $buffer, $search ) !== false ) {
								$buffer	=	str_replace( $search, $replace, $buffer );

								if ( !isset( $extensions[$ext] ) ) {
									$extensions[$ext]	=	0;
								}
								$extensions[$ext]++;
							} elseif ( strpos( $buffer, $search2 ) !== false ) {
								$buffer	=	str_replace( $search2, $replace, $buffer );

								if ( !isset( $extensions[$ext] ) ) {
									$extensions[$ext]	=	0;
								}
								$extensions[$ext]++;
							}

							$isUpToDate	=	false;
						} else {
							if ( !isset( $extensions[$ext] ) ) {
								$extensions[$ext]	=	0;
							}
							$extensions[$ext]++;
						}
					}

					if ( !$isUpToDate ) {
						JFile::write( $path, $buffer );
					}
				}
			}
		}

		return $extensions;
	}

	// zip
	public static function zip( $path, $path_zip )
	{
		$trash	=	JFolder::folders( $path, '.', true, true, array(), array() );
		
		if ( is_array( $trash ) && count( $trash ) ) {
			foreach ( $trash as $t ) {
				if ( strpos( $t, '.svn' ) !== false ) {
					if ( JFolder::exists( $t ) ) {
						JFolder::delete( $t );
					}
				}
			}
		}
		
		if ( JFile::exists( $path_zip ) ) {
			if ( !JFile::delete( $path_zip ) ) {
				return false;
			}
		}
		
		require_once JPATH_COMPONENT.'/helpers/pclzip/pclzip.lib.php';
		$zip	=	new PclZip( $path_zip );
		if ( $zip->create( $path, PCLZIP_OPT_REMOVE_PATH, $path ) == 0 ) {
			return;
		}
		JFolder::delete( $path );
		
		return $zip->zipname;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Elements
	
	// findFields
	public static function findFields( $paths, $dest = '' )
	{
		if ( count( $paths ) ) {
			foreach ( $paths as $path ) {
				if ( !JFile::exists( $path ) ) {
					continue;
				}
				$data	=	file_get_contents( $path );
				$ext	=	JFile::getExt( $path );
				$names	=	'';
				if ( $ext == 'xml' ) {
					$regex	=	'#construction=\"([a-z0-9_]*)\"#';
					preg_match_all( $regex, $data, $matches );
					if ( count( $matches[1] ) ) {
						$names	=	'"'.implode( '","', $matches[1] ).'"';
					}
				} else {
					$regex	=	'#(JCckDev::renderForm\(|JCckDev::getForm\(|JCckDev::get\() ?(\$cck\[)?\'([a-z0-9_]*)\'#';
					
					preg_match_all( $regex, $data, $matches );
					if ( count( $matches[3] ) ) {
						$names	=	'"'.implode( '","', $matches[3] ).'"';
					}
				}
				if ( $names != '' ) {
					$fields	=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name IN ('.$names.') AND a.id >= 5000' );
					if ( $dest ) {
						CCK_Export::exportFields( $dest, $fields );
					} else {
						return $fields;
					}
				}
			}
		}
	}
	
	// exportFields
	public static function exportFields( $dest, $fields )
	{
		if ( count( $fields ) ) {
			$dest	=	CCK_Export::createDir( $dest );
			$dest	=	CCK_Export::createDir( $dest.'/fields' );
			
			$folders	=	JCckDatabase::loadObjectList( 'SELECT id, name FROM #__cck_core_folders', 'id' );
			foreach ( $fields as $field ) {
				$name		=	$field->name;
				$field->id	=	0;
				if ( JFile::exists( $dest.'/field_'.$name.'.xml' ) ) {
					continue;
				}
				
				$xml	=	new JCckDevXml( '<cck />' );
				$xml->addAttribute( 'type', 'fields' );
				$xml->addChild( 'author', 'Octopoos' );
				$xml->addChild( 'authorEmail', 'contact@seblod.com' );
				$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
				$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
				$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
				$xml->addChild( 'description', 'SEBLOD 3.x - www.seblod.com' );
				
				$xml_el	=	$xml->addChild( 'field' );				
				foreach ( $field as $k=>$v ) {
					$xml_el->addChild( $k, htmlspecialchars( $v ) );
					// $xml_el->addChild( $k );
					// $xml_el->$k	=	$v;
				}
				if ( isset( $xml->field->folder ) ) {
					$v	=	(string)$xml->field->folder;
					if ( isset( $folders[$v] ) ) {
						$xml->field->folder	=	$folders[$v]->name;
					}
				}
				
				$buffer	=	'<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML();	
				$path	=	$dest.'/field_'.$name.'.xml';			
				JFile::write( $path, $buffer );
			}
		}
	}
	
	// exportElements
	public static function exportElements( $elemtype, $elements, &$data, &$extensions = array(), $protected = 0, $copyright = '' )
	{
		if ( count( $elements ) ) {
			CCK_Export::createDir( $data['root_elements'].'/'.$elemtype.'s' );
			
			foreach ( $elements as $elem ) {
				self::exportElement( $elemtype, $elem, $data, $extensions, $protected, $copyright );
			}
		}
	}
	
	// exportContent
	public static function exportContent( $elemtype, &$elem, &$data, &$extensions, $protected = 0 )
	{
		if ( isset( $data['elements'][$elemtype][$elem->id] ) || ( $elem->id < $protected ) ) {
			return;
		}
		$data['elements'][$elemtype][$elem->id]	=	'';
		
		$file	=	array( '_'=>'', 'src', 'lang_src'=>'', 'lang_root'=>'', );
		
		if ( $elemtype == 'joomla_menu' ) {
			$name	=	$elem->menutype;
		} elseif ( $elemtype == 'joomla_menuitem' ) {
			$name	=	str_replace( '/', '_', $elem->path );
		} elseif ( $elemtype == 'joomla_category' ) {
			$name	=	$elem->name;
		} else {
			$name	=	$elem->name;
		}
		$dest	=	$data['root_content'].'/'.$elemtype;
		
		$xml	=	new JCckDevXml( '<cck />' );
		$xml->addAttribute( 'type', $elemtype );
		$xml->addAttribute( 'version', '3.0' );
		$xml->addAttribute( 'folder', 'content' );
		$xml->addChild( 'author', 'Octopoos' );
		$xml->addChild( 'authorEmail', 'contact@seblod.com' );
		$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
		$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
		$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
		$xml->addChild( 'description', 'SEBLOD 3.x - www.seblod.com' );
		
		// Prepare
		$xml2	=	$xml->addChild( $elemtype );
		foreach ( $elem as $k=>$v ) {
			$xml2->addChild( $k, htmlspecialchars( $v ) );
		}
		
		// Force
		if ( isset( $xml->{$elemtype}->id ) ) {
			$xml->{$elemtype}->id	=	'';
		}
		
		// Prepare2
		$call	=	'export'.$elemtype;
		self::$call( $elemtype, $elem, $xml, $data, $extensions, $file );
		
		// Set
		$buffer	=	'<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML();
		$path	=	$dest.'/'.$name.'.xml';
		JFile::write( $path, $buffer );
	}

	// exportElement
	public static function exportElement( $elemtype, &$elem, &$data, &$extensions, $protected = 0, $copyright = '' )
	{
		if ( isset( $data['elements'][$elemtype][$elem->id] ) || ( $elem->id < $protected ) ) {
			return;
		}
		$data['elements'][$elemtype][$elem->id]	=	'';
		
		$file	=	array( '_'=>'', 'src', 'lang_src'=>'', 'lang_root'=>'', );
		if ( $elemtype == 'folder' ) {
			$name	=	str_replace( '/', '_', $elem->path );
			$folder	=	'parent_id';
			$plural	=	'folders';
		} elseif ( $elemtype == 'category' ) {
			$name	=	$elem->name;
			$folder	=	'folder';
			$plural	=	'categories';
		} else {
			$name	=	$elem->name;
			$folder	=	'folder';
			$plural	=	$elemtype.'s';
		}
		$dest	=	$data['root_elements'].'/'.$plural;
		
		$xml	=	new JCckDevXml( '<cck />' );
		$xml->addAttribute( 'type', $plural );
		$xml->addChild( 'author', 'Octopoos' );
		$xml->addChild( 'authorEmail', 'contact@seblod.com' );
		$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
		$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
		$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
		$xml->addChild( 'description', 'SEBLOD 3.x - www.seblod.com' );
		
		if ( $elemtype == 'field' ) {
			$call	=	'beforeExport'.$elemtype;
			self::$call( $elemtype, $elem, $xml, $data, $extensions, $file );
		}
		
		// Prepare
		$xml2	=	$xml->addChild( $elemtype );
		foreach ( $elem as $k=>$v ) {
			$xml2->addChild( $k, htmlspecialchars( $v ) );
			// $xml2->addChild( $k );
			// $xml2->$k	=	$v;
		}
		
		// Force
		if ( isset( $xml->{$elemtype}->id ) ) {
			$xml->{$elemtype}->id	=	'';
		}
		if ( isset( $xml->{$elemtype}->{$folder} ) ) {
			$v								=	(string)$xml->{$elemtype}->{$folder};
			$xml->{$elemtype}->{$folder}	=	( isset( $data['folders'][$v] ) ) ? $data['folders'][$v]->path : '';
		}
		
		// Prepare2
		$call	=	'export'.$elemtype;
		self::$call( $elemtype, $elem, $xml, $data, $extensions, $file, $copyright );
		
		// Set
		$buffer	=	'<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML();
		$path	=	$dest.'/'.$elemtype.'_'.$name.'.xml';
		JFile::write( $path, $buffer );
	}

	// beforeExportField
	public static function beforeExportField( $elemtype, $elem, &$xml, &$data, &$extensions, &$file )
	{
		if ( file_exists( JPATH_SITE.'/plugins/cck_field/'.$elem->type.'/classes/app.php' ) ) {
			require_once JPATH_SITE.'/plugins/cck_field/'.$elem->type.'/classes/app.php';
			JCck::callFunc_Array( 'plgCCK_Field'.$elem->type.'_App', 'onCCK_FieldExportField', array( &$elem, &$data, &$extensions ) );
		}
	}
	
	// exportField
	public static function exportField( $elemtype, $elem, &$xml, &$data, &$extensions, &$file, $copyright = '' )
	{
		self::exportPlugin( 'cck_field', $elem->type, $data, $extensions );
		
		if ( $elem->storage && $elem->storage != 'none' ) {
			$data['elements']['tables'][$elem->storage_table][$elem->storage_field]	=	'';
		}
		
		if ( $elem->storage ) {
			self::exportPlugin( 'cck_storage', $elem->storage, $data, $extensions );
		}
		
		if ( $elem->storage_location ) {
			self::exportPlugin( 'cck_storage_location', $elem->storage_location, $data, $extensions );
		}
	}
	
	// exportFolder
	public static function exportFolder( $elemtype, $elem, &$xml, &$data, &$extensions, &$file, $copyright = '' )
	{
		$null	=	array( 'asset_id', 'depth', 'lft', 'rgt' );
		foreach ( $null as $n ) {
			if ( isset( $xml->{$elemtype}->{$n} ) ) {
				$xml->{$elemtype}->{$n}	=	'';
			}
		}
		
		$acl	=	( $elem->asset_id ) ? JCckDatabase::loadResult( 'SELECT rules FROM #__assets WHERE id = '.(int)$elem->asset_id ) : '{}';
		$xml->addChild( 'acl', (string)$acl );

		if ( isset( $data['processings2'][$elem->id] ) ) {
			$data['elements']['processings'][$elem->id]	=	'';
		}
	}
	
	// exportTemplate
	public static function exportTemplate( $elemtype, $elem, &$xml, &$data, &$extensions, &$file, $copyright = '' )
	{
		$file['_']			=	'tpl_'.$elem->name.'.zip';
		$file['src']		=	JPATH_SITE.'/'.$elemtype.'s'.'/'.$elem->name;
		$file['lang_src']	=	$file['src'].'/templateDetails.xml';
		$file['lang_root']	=	JPATH_SITE;
		if ( !isset( $extensions[$file['src']] ) ) {
			$extensions[$file['src']]	=	(object)array( 'type'=>'template', 'id'=>'tpl_'.$elem->name, 'client'=>'site', '_file'=>$file['_'] );
		}
		if ( $file['_'] != '' && ! JFile::exists( $data['root_extensions'].'/'.$file['_'] ) ) {
			self::exportFile( 'template', $data, $file, array(), $copyright );
		}
		CCK_Export::findFields( array( $file['src'].'/templateDetails.xml' ), $data['root_elements'] );
	}
	
	// exportTemplate_Style
	public static function exportTemplate_Style( $elemtype, $elem, &$xml, &$data, &$extensions, &$file )
	{
		if ( isset( $xml->{$elemtype}->name ) ) {
			unset( $xml->{$elemtype}->name );
		}
	}
	
	// exportType
	public static function exportType( $elemtype, $elem, &$xml, &$data, &$extensions, &$file, $copyright = '' )
	{
		if ( isset( $xml->{$elemtype}->asset_id ) ) {
			$xml->{$elemtype}->asset_id	=	'';
		}
		
		$acl	=	( $elem->asset_id ) ? JCckDatabase::loadResult( 'SELECT rules FROM #__assets WHERE id = '.(int)$elem->asset_id ) : '{}';
		$xml->addChild( 'acl', (string)$acl );
		
		// Views
		$views		=	array( 'admin', 'site', 'intro', 'content' );
		$templates	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_templates', 'name' );
		$tpl		=	array();
		foreach ( $views as $v ) {
			$e		=	'template_'.$v;
			$s		=	$elem->$e;
			$t		=	'';
			if ( isset( $data['styles'][$s] ) ) {
				$t			=	$data['styles'][$s]->template;
				$tpl[$v]	=	$t;
				if ( isset( $templates[$t] ) ) {
					self::exportElement( 'template', $templates[$t], $data, $extensions, 10 );	
				}
				if ( strpos( $data['styles'][$s]->title, '('.$v.')' ) !== false ) {
					$data['styles'][$s]->name	=	$t.'-'.$elem->name.'-'.$v;
					$t							=	$t.' - '.$elem->name.' ('.$v.')';
					$data['styles'][$s]->title	=	$t;
					self::exportElement( 'template_style', $data['styles'][$s], $data, $extensions );
				}
			} else {
				if ( $s > 0 ) {
					$t		=	'seb_one';
				}
				$tpl[$v]	=	$t;
			}
			if ( isset( $xml->{$elemtype}->{$e} ) ) {
				$xml->{$elemtype}->{$e}	=	$t;
			}
		}
		
		// Fields
		$xml2	=	$xml->addChild( 'fields' );
		$elems	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_'.$elemtype.'_field WHERE '.$elemtype.'id = '.$elem->id.' ORDER BY client ASC, ordering ASC' );
		$fields	=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_fields AS a LEFT JOIN #__cck_core_'.$elemtype.'_field AS b ON b.fieldid = a.id'.
												  ' WHERE b.'.$elemtype.'id = '.$elem->id.' ORDER BY a.id ASC', 'id' );
		$i		=	1;
		foreach ( $elems as $el ) {
			if ( ! isset( $fields[$el->fieldid] ) ) {
				 continue;
			}
			if ( $el->link != '' ) {
				self::exportPlugin( 'cck_field_link', $el->link, $data, $extensions );

				if ( file_exists( JPATH_SITE.'/plugins/cck_field_link/'.$el->link.'/classes/app.php' ) ) {
					require_once JPATH_SITE.'/plugins/cck_field_link/'.$el->link.'/classes/app.php';
					JCck::callFunc_Array( 'plgCCK_Field_Link'.$el->link.'_App', 'onCCK_Field_LinkExportType_Field', array( $fields[$el->fieldid], &$el, &$data, &$extensions ) );
				}
			}
			if ( $el->live != '' ) {
				self::exportPlugin( 'cck_field_live', $el->live, $data, $extensions );
			}
			if ( $el->restriction != '' ) {
				self::exportPlugin( 'cck_field_restriction', $el->restriction, $data, $extensions );
			}
			if ( $el->typo != '' ) {
				self::exportPlugin( 'cck_field_typo', $el->typo, $data, $extensions );
			}
			if ( $el->validation != '' ) {
				self::exportPlugin( 'cck_field_validation', $el->validation, $data, $extensions );
			}
			if ( file_exists( JPATH_SITE.'/plugins/cck_field/'.$fields[$el->fieldid]->type.'/classes/app.php' ) ) {
				require_once JPATH_SITE.'/plugins/cck_field/'.$fields[$el->fieldid]->type.'/classes/app.php';
				JCck::callFunc_Array( 'plgCCK_Field'.$fields[$el->fieldid]->type.'_App', 'onCCK_FieldExportType_Field', array( $fields[$el->fieldid], &$el, &$data, &$extensions ) );
			}
			//
			$e	=	$xml2->addChild( 'field'.$i, $fields[$el->fieldid]->name );
			foreach ( $el as $k => $v ) {
				if ( ! ( $k == $elemtype.'id' || $k == 'fieldid' ) ) {
					$e->addAttribute( $k, $v );
				}
			}
			self::exportElement( 'field', $fields[$el->fieldid], $data, $extensions, 5000 );
			$i++;
		}
		
		// Positions
		$xml2	=	$xml->addChild( 'positions' );
		$elems	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_'.$elemtype.'_position WHERE '.$elemtype.'id = '.$elem->id.' ORDER BY client ASC, position ASC' );
		$i		=	1;
		foreach ( $elems as $el ) {
			$e	=	$xml2->addChild( 'position'.$i, $el->position );
			foreach ( $el as $k => $v ) {
				if ( ! ( $k == $elemtype.'id' || $k == 'position' ) ) {
					$e->addAttribute( $k, $v );
				}
			}
			if ( !( $el->variation == '' || $el->variation == 'none' ) ) {
				self::exportVariation( $el->variation, @$tpl[$el->client], $data, $extensions );
			}
			$i++;
		}
		
		// Sql
		/*
		$sql_table	=	'cck_store_form_'.$elem->name;
		if ( isset( $data['tables'][$data['db_prefix'].$sql_table] ) ) {
			$sql_table	=	'#__cck_store_form_'.$elem->name;
			$sql_path	=	$data['root_sql'].'/'.$sql_table.'.sql';
			$sql_buffer	=	JCckDatabase::getTableCreate( array( $sql_table ) );
			JFile::write( $sql_path, $sql_buffer );
		}
		*/
	}
	
	// exportSearch
	public static function exportSearch( $elemtype, $elem, &$xml, &$data, &$extensions, &$file, $copyright = '' )
	{
		// Views
		$views		=	array( 'search', 'filter', 'list', 'item' );
		$templates	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_templates', 'name' );
		$tpl		=	array();
		foreach ( $views as $v ) {
			$e		=	'template_'.$v;
			$s		=	$elem->$e;
			$t		=	'';
			if ( isset( $data['styles'][$s] ) ) {
				$t			=	$data['styles'][$s]->template;
				$tpl[$v]	=	$t;
				if ( isset( $templates[$t] ) ) {
					self::exportElement( 'template', $templates[$t], $data, $extensions, 10 );	
				}
				if ( strpos( $data['styles'][$s]->title, '('.$v.')' ) !== false ) {
					$data['styles'][$s]->name	=	$t.'-'.$elem->name.'-'.$v;
					$t							=	$t.' - '.$elem->name.' ('.$v.')';
					$data['styles'][$s]->title	=	$t;
					self::exportElement( 'template_style', $data['styles'][$s], $data, $extensions );
				}
			} else {
				if ( $s > 0 ) {
					$t	=	'seb_one';
				}
				$tpl[$v]	=	$t;
			}
			if ( isset( $xml->{$elemtype}->{$e} ) ) {
				$xml->{$elemtype}->{$e}	=	$t;
			}
		}
		
		// Fields
		$xml2	=	$xml->addChild( 'fields' );
		$elems	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_'.$elemtype.'_field WHERE '.$elemtype.'id = '.$elem->id.' ORDER BY client ASC, ordering ASC' );
		$fields	=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_fields AS a LEFT JOIN #__cck_core_'.$elemtype.'_field AS b ON b.fieldid = a.id'.
												  ' WHERE b.'.$elemtype.'id = '.$elem->id.' ORDER BY a.id ASC', 'id' );
		$i		=	1;
		foreach ( $elems as $el ) {
			if ( ! isset( $fields[$el->fieldid] ) ) {
				 continue;
			}
			if ( $el->link != '' ) {
				self::exportPlugin( 'cck_field_link', $el->link, $data, $extensions );

				if ( file_exists( JPATH_SITE.'/plugins/cck_field_link/'.$el->link.'/classes/app.php' ) ) {
					require_once JPATH_SITE.'/plugins/cck_field_link/'.$el->link.'/classes/app.php';
					JCck::callFunc_Array( 'plgCCK_Field_Link'.$el->link.'_App', 'onCCK_Field_LinkExportSearch_Field', array( $fields[$el->fieldid], &$el, &$data, &$extensions ) );
				}
			}
			if ( $el->live != '' ) {
				self::exportPlugin( 'cck_field_live', $el->live, $data, $extensions );
			}
			if ( $el->restriction != '' ) {
				self::exportPlugin( 'cck_field_restriction', $el->restriction, $data, $extensions );
			}
			if ( $el->typo != '' ) {
				self::exportPlugin( 'cck_field_typo', $el->typo, $data, $extensions );
			}
			if ( $el->validation != '' ) {
				self::exportPlugin( 'cck_field_validation', $el->validation, $data, $extensions );
			}
			if ( file_exists( JPATH_SITE.'/plugins/cck_field/'.$fields[$el->fieldid]->type.'/classes/app.php' ) ) {
				require_once JPATH_SITE.'/plugins/cck_field/'.$fields[$el->fieldid]->type.'/classes/app.php';
				JCck::callFunc_Array( 'plgCCK_Field'.$fields[$el->fieldid]->type.'_App', 'onCCK_FieldExportSearch_Field', array( $fields[$el->fieldid], &$el, &$data, &$extensions ) );
			}
			//
			$e	=	$xml2->addChild( 'field'.$i, $fields[$el->fieldid]->name );
			foreach ( $el as $k => $v ) {
				if ( ! ( $k == $elemtype.'id' || $k == 'fieldid' ) ) {
					$e->addAttribute( $k, $v );
				}
			}
			self::exportElement( 'field', $fields[$el->fieldid], $data, $extensions, 5000 );
			$i++;
		}
		
		// Positions
		$xml2	=	$xml->addChild( 'positions' );
		$elems	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_'.$elemtype.'_position WHERE '.$elemtype.'id = '.$elem->id.' ORDER BY client ASC, position ASC' );
		$i		=	1;
		foreach ( $elems as $el ) {
			$e	=	$xml2->addChild( 'position'.$i, $el->position );
			foreach ( $el as $k => $v ) {
				if ( ! ( $k == $elemtype.'id' || $k == 'position' ) ) {
					$e->addAttribute( $k, $v );
				}
			}
			if ( !( $el->variation == '' || $el->variation == 'none' ) ) {
				self::exportVariation( $el->variation, @$tpl[$el->client], $data, $extensions );
			}
			$i++;
		}
	}
	
	// exportPlugin
	public static function exportPlugin( $type, $name, &$data, &$extensions )
	{
		if ( !isset( $data['plugins'][$type][$name] ) ) {
			$file				=	array();
			$file['_']			=	'plg_'.$type.'_'.$name.'.zip';
			$file['src']		=	JPATH_SITE.'/plugins/'.$type.'/'.$name;
			$file['lang_src']	=	$file['src'].'/'.$name.'.xml';
			$file['lang_root']	=	JPATH_ADMINISTRATOR;
			
			if ( file_exists( $file['src'] ) ) {
				if ( !isset( $extensions[$file['src']] ) ) {
					$extensions[$file['src']]	=	(object)array( 'type'=>'plugin', 'id'=>'plg_'.$type.'_'.$name, 'group'=>$type, '_file'=>$file['_'] );
				}
				if ( $file['_'] != '' && ! JFile::exists( $data['root_extensions'].'/'.$file['_'] ) ) {
					self::exportFile( 'plugin', $data, $file );
				}
			}
		}
	}
	
	// exportVariation
	public static function exportVariation( $name, $template, &$data, &$extensions )
	{
		if ( !isset( $data['variations'][$name] ) ) {
			$file				=	array();
			$file['_']			=	'var_cck_'.$name.'.zip';
			$file['filename']	=	'var_cck_'.$name;
			$file['name']		=	$name;
			$file['src']		=	JPATH_SITE.'/libraries/cck/rendering/variations/'.$name;
			if ( !file_exists( $file['src'] ) ) {
				if ( JFile::exists( $data['root_extensions'].'/tpl_'.$template.'.zip' ) ) {
					return;
				}
				$file['src']	=	JPATH_SITE.'/templates/'.$template.'/variations/'.$name;
			}
			if ( $file['src'] == JPATH_SITE.'/templates/seb_table/variations/heading' ) {
				return;
			}
			if ( file_exists( $file['src'] ) ) {
				if ( !isset( $extensions[$file['src']] ) ) {
					$extensions[$file['src']]	=	(object)array( 'type'=>'file', 'id'=>'var_cck_'.$name, '_file'=>$file['_'] );
				}
				if ( $file['_'] != '' && ! JFile::exists( $data['root_extensions'].'/'.$file['_'] ) ) {
					self::exportFile( 'variation', $data, $file );
				}
			}
		}
	}
	
	// exportFile
	public static function exportFile( $type, &$data, $file, $extensions = array(), $copyright = '' )
	{
		$path	=	$data['root_extensions'].'/_temp';
		if ( $file['src'] && JFolder::exists( $file['src'] ) ) {
			if ( $type == 'variation' || $type == 'processing' ) {
				JFolder::copy( $file['src'], $path.'/'.$file['name'] );
				$manifest	=	JPATH_ADMINISTRATOR.'/manifests/files/'.$file['filename'].'.xml';
				if ( JFile::exists( $manifest ) ) {
					JFile::copy( $manifest, $path.'/'.$file['filename'].'.xml' );
				} else {
					$obj		=	(object)array( 'title'=>$file['name'] );

					if ( $type == 'processing' ) {
						$obj->description	=	'SEBLOD 3.x Processing File - www.seblod.com';
					}
					$xml		=	CCK_Export::prepareFile( $obj );
					$fileset	=	$xml->addChild( 'fileset' );
					$files		=	$fileset->addChild( 'files' );

					if ( $type == 'processing' ) {
						$target	=	'media/cck/processings';
						if ( isset( $extensions[$file['src']]->src ) ) {
							$target	=	$extensions[$file['src']]->src;
						}
					} else {
						$target	=	'libraries/cck/rendering/variations';
					}
					$files->addAttribute( 'target', $target );

					$addfile	=	$files->addChild( 'folder', $file['name'] );
					
					CCK_Export::createFile( $path.'/'.$file['filename'].'.xml', '<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML() );
				}
			} else {
				if ( $copyright ) {
					CCK_Export::update( $file['src'], $copyright );
				}
				JFolder::copy( $file['src'], $path );
				if ( $type == 'plugin' ) {
					CCK_Export::findFields( array( $file['src'].'/tmpl/edit.php', $file['src'].'/tmpl/edit2.php' ), $path.'/install' );
					CCK_Export::update( $path.'/install', $copyright );
				}
			}
			if ( @$file['lang_src'] != '' ) {
				CCK_Export::exportLanguage( $file['lang_src'], $file['lang_root'], $path, $copyright );
			}
			CCK_Export::clean( $path );
			CCK_Export::zip( $path, $data['root_extensions'].'/'.$file['_'] );
		}
	}

	// exportCategory
	public static function exportCategory( $elemtype, $elem, &$xml, &$data, &$extensions, &$file = NULL )
	{
		$null	=	array( 'asset_id', 'parent_id', 'level', 'lft', 'rgt', 'created_time', 'modified_time', 'modified_user_id' );
		foreach ( $null as $n ) {
			if ( isset( $xml->{$elemtype}->{$n} ) ) {
				$xml->{$elemtype}->{$n}	=	'';
			}
		}
		if ( isset( $xml->{$elemtype}->path ) && isset( $xml->{$elemtype}->alias ) ) {	//todo: remove
			$xml->{$elemtype}->path	=	(string)$xml->{$elemtype}->alias;
		}
		if ( isset( $xml->{$elemtype}->hits ) ) {
			$xml->{$elemtype}->hits	=	0;
		}
		
		$acl	=	( $elem->asset_id ) ? JCckDatabase::loadResult( 'SELECT rules FROM #__assets WHERE id = '.(int)$elem->asset_id ) : '{}';
		$xml->addChild( 'acl', (string)$acl );

		$app	=	$elem->name;
		$xml->addChild( 'app', (string)$app );
		
		if ( isset( $xml->{$elemtype}->name ) ) {
			unset( $xml->{$elemtype}->name );
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Dependencies

	// exportMenu
	public static function exportMenus( $menu_id, &$data, $extensions )
	{
		$table	=	JTable::getInstance( 'MenuType' );
		$table->load( $menu_id );

		self::exportContent( 'joomla_menu', $table, $data, $extensions, 0 );

		$items	=	JCckDatabase::loadObjectList( 'SELECT id FROM #__menu WHERE menutype = "'.$table->menutype.'"' );
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$t	=	JTable::getInstance( 'Menu' );
				$t->load( $item->id );
				self::exportContent( 'joomla_menuitem', $t, $data, $extensions, 0 );
			}
		}
		
		return $table->id;
	}

	// exportJoomla_Category
	public static function exportJoomla_Category( $elemtype, $elem, &$xml, &$data, &$extensions, &$file = NULL )
	{
		$null	=	array( 'asset_id', 'parent_id', 'level', 'lft', 'rgt', 'created_time', 'created_user_id', 'modified_time', 'modified_user_id' );
		foreach ( $null as $n ) {
			if ( isset( $xml->{$elemtype}->{$n} ) ) {
				$xml->{$elemtype}->{$n}	=	'';
			}
		}
		if ( isset( $xml->{$elemtype}->path ) && isset( $xml->{$elemtype}->alias ) ) {	//todo: remove
			$xml->{$elemtype}->path	=	(string)$xml->{$elemtype}->alias;
		}
		if ( isset( $xml->{$elemtype}->hits ) ) {
			$xml->{$elemtype}->hits	=	0;
		}
		
		$acl	=	( $elem->asset_id ) ? JCckDatabase::loadResult( 'SELECT rules FROM #__assets WHERE id = '.(int)$elem->asset_id ) : '{}';
		$xml->addChild( 'acl', (string)$acl );

		$app	=	$elem->name;
		$xml->addChild( 'app', (string)$app );
		
		if ( isset( $xml->{$elemtype}->name ) ) {
			unset( $xml->{$elemtype}->name );
		}
	}

	// exportJoomla_Menu
	public static function exportJoomla_Menu( $elemtype, $elem, &$xml, &$data, &$extensions, &$file = NULL )
	{
		if ( isset( $xml->{$elemtype}->asset_id ) ) {
			$xml->{$elemtype}->asset_id	=	'';
		}
		
		$acl	=	( $elem->asset_id ) ? JCckDatabase::loadResult( 'SELECT rules FROM #__assets WHERE id = '.(int)$elem->asset_id ) : '{}';
		$xml->addChild( 'acl', (string)$acl );
	}

	// exportJoomla_MenuItem
	public static function exportJoomla_MenuItem( $elemtype, $elem, &$xml, &$data, &$extensions, &$file = NULL )
	{
		$null	=	array( 'lft', 'rgt' );
		foreach ( $null as $n ) {
			if ( isset( $xml->{$elemtype}->{$n} ) ) {
				$xml->{$elemtype}->{$n}	=	'';
			}
		}

		if ( isset( $xml->{$elemtype}->type ) && $xml->{$elemtype}->type == 'component' &&
			 isset( $xml->{$elemtype}->component_id ) && $xml->{$elemtype}->component_id ) {
			$xml->{$elemtype}->component_id	=	JCckDatabase::loadResult( 'SELECT element FROM #__extensions WHERE extension_id = '.$xml->{$elemtype}->component_id );
		}
		if ( isset( $xml->{$elemtype}->level ) && (int)$xml->{$elemtype}->level > 1 &&
			 isset( $xml->{$elemtype}->parent_id ) && (int)$xml->{$elemtype}->parent_id ) {
			$xml->{$elemtype}->parent_id	=	JCckDatabase::loadResult( 'SELECT alias FROM #__menu WHERE id = '.(int)$xml->{$elemtype}->parent_id );
		}
	}

	// exportJoomla_Module
	public static function exportJoomla_Module( $elemtype, $elem, &$xml, &$data, &$extensions, &$file = NULL )
	{
	}

	// exportProcessings
	public static function exportProcessings( &$data, &$extensions )
	{
		$elemtype	=	'processing';
		$plural		=	$elemtype.'s';
		$dest		=	CCK_Export::createDir( $data['root_elements'].'/'.$plural );
		
		foreach ( $data['processings'] as $k=>$v ) {
			$folder_id	=	$data['processings'][$k]->folder;
			if ( !isset( $data['elements']['processings'][$folder_id] ) ) {
				continue;
			}
			$name		=	$data['processings'][$k]->name;
			$name2		=	$data['processings'][$k]->scriptfile;

			if ( $name2 != '' ) {
				$offset	=	0;

				if ( $name2[0] == '/' ) {
					$offset	=	1;
				}
				$pos	=	strpos( $name2, '.' );

				if ( $pos !== false ) {
					$name2	=	substr( $name2, $offset, $pos - 1 );
				} else {
					$name2	=	substr( $name2, $offset );
				}
			}
			$name2		=	str_replace( '/', '_', $name2 );
			$suffix		=	'';

			if ( $data['processings'][$k]->type != '0' ) {
				$suffix	=	'_'.strtolower( $data['processings'][$k]->type );
			}
			$filename	=	basename( $data['processings'][$k]->scriptfile );
			$filename	=	substr( $filename, 0, strrpos( $filename, '.' ) );
			
			if ( $filename && $name && strpos( $data['processings'][$k]->scriptfile, $filename.'/'.$filename.'.php' ) !== false ) {
				$folder =   str_replace( $filename.'/'.$filename.'.php', '', $data['processings'][$k]->scriptfile );
				if ( $folder && $folder[0] == '/' ) {
					$folder	=	substr( $folder, 1 );
				}

				$xml	=	new JCckDevXml( '<cck />' );
				$xml->addAttribute( 'type', $plural );
				$xml->addChild( 'author', 'Octopoos' );
				$xml->addChild( 'authorEmail', 'contact@seblod.com' );
				$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
				$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
				$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
				$xml->addChild( 'description', 'SEBLOD 3.x - www.seblod.com' );

				$xml2	=	$xml->addChild( $elemtype );
				$xml2->addChild( 'title', htmlspecialchars( $data['processings'][$k]->title ) );
				$xml2->addChild( 'name', $name );
				$app_folder	=	$data['processings'][$k]->folder;
				$app_folder	=	( isset( $data['folders'][$app_folder] ) ) ? $data['folders'][$app_folder]->path : 'quick_folder';
				$xml2->addChild( 'folder', $app_folder );
				$xml2->addChild( 'type', $data['processings'][$k]->type );
				$xml2->addChild( 'description', htmlspecialchars( $data['processings'][$k]->description ) );
				$xml2->addChild( 'options', htmlspecialchars( $data['processings'][$k]->options ) );
				$xml2->addChild( 'ordering', 0 );
				$xml2->addChild( 'published', $data['processings'][$k]->published );
				$xml2->addChild( 'scriptfile', $data['processings'][$k]->scriptfile );

				// Set
				$buffer	=	'<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML();
				$path	=	$dest.'/'.$elemtype.'_'.str_replace( '#__', '', $name2 ).$suffix.'.xml';
				JFile::write( $path, $buffer );

				if ( $folder ) {
					$path	=	JPATH_SITE.'/'.$folder;

					if ( JFolder::exists( $path ) ) {
						$file				=	array();
						$file['_']			=	'pro_cck_'.$name2.'.zip';
						$file['filename']	=	'pro_cck_'.$name2;
						$file['name']		=	$name;
						$file['src']		=	$path.$name;
						$file['lang_src']	=	JPATH_ADMINISTRATOR.'/manifests/files/pro_cck_'.$name2.'.xml';
						$file['lang_root']	=	JPATH_SITE;

						if ( file_exists( $file['src'] ) ) {
							if ( !isset( $extensions[$file['src']] ) ) {
								$extensions[$file['src']]	=	(object)array(
																	'type'=>'file',
																	'id'=>'pro_cck_'.$name2,
																	'_file'=>$file['_'],
																	'src'=>$folder
															);

								if ( $file['_'] != '' && ! JFile::exists( $data['root_extensions'].'/'.$file['_'] ) ) {
									self::exportFile( 'processing', $data, $file, $extensions );
								}
							}
						}
					}
				}
			}
		}
	}

	// exportRootCategory
	public static function exportRootCategory( $app, &$data, $extensions )
	{
		$pk	=	( @$app->name ) ? JCckDatabase::loadResult( 'SELECT pk FROM #__cck_core WHERE app = "'.$app->name.'"' ) : 0;
		
		$table	=	JTable::getInstance( 'Category' );
		if ( $pk > 0 ) {
			$table->load( $pk );
		} else {
			$table->title			=	$app->title;
			$table->alias			=	str_replace( '_', '-', $app->name );
			$table->level			=	0;
			$table->parent_id		=	0;
			$table->published		=	1;
			$table->path			=	$table->alias;
			$table->extension		=	'com_content';
			$table->created_user_id	=	JCck::getConfig_Param( 'integration_user_default_author', 42 );
			$table->created_time	=	JFactory::getDate()->format( 'Y-m-d H:i:s' );
			$table->params			=	'{"category_layout":"","image":""}';
			$table->metadata		=	'{"author":"","robots":""}';
			$table->language		=	'*';
		}
		$table->name				=	$app->name;
		$table->hits				=	0;
		$table->checked_out			=	0;
		$table->checked_out_time	=	'0000-00-00 00:00:00';

		self::exportContent( 'joomla_category', $table, $data, $extensions, 0 );

		return $table->name;
	}
	
	// exportTables
	public static function exportTables( &$data )
	{
		$db			=	JFactory::getDbo();
		$elemtype	=	'table';
		$plural		=	$elemtype.'s';
		$dest		=	CCK_Export::createDir( $data['root_elements'].'/'.$plural );
		
		foreach ( $data['elements']['tables'] as $name=>$fields ) {
			if ( isset( $data['tables'][(str_replace( '#__', $data['db_prefix'], $name ))] ) ) {
				if ( $name && $name != '#__cck_core' && !isset( $data['tables_excluded'][$name] ) ) {
					$table			=	JCckTable::getInstance( $name );
					$table_fields	=	$table->getFields();
					$table_keys		=	$db->getTableKeys( $name );
					$table_pkeys	=	array();
					
					$xml	=	new JCckDevXml( '<cck />' );
					$xml->addAttribute( 'type', $plural );
					$xml->addChild( 'author', 'Octopoos' );
					$xml->addChild( 'authorEmail', 'contact@seblod.com' );
					$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
					$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
					$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
					$xml->addChild( 'description', 'SEBLOD 3.x - www.seblod.com' );
					
					$xml2	=	$xml->addChild( $elemtype );
					$xml2->addChild( 'name', $name );
					
					$xml3	=	$xml->addChild( 'indexes' );
					$i		=	1;
					foreach ( $table_keys as $k=>$v ) {
						if ( $v->Key_name == 'PRIMARY' ) {
							$table_pkeys[]	=	$v->Column_name;
						}
						$index	=	$xml3->addChild( 'index'.$i, $v->Key_name );
						$index->addAttribute( 'column_name', $v->Column_name );
						$index->addAttribute( 'index_type', $v->Index_type );
						$index->addAttribute( 'seq_in_type', $v->Seq_in_index );
						$i++;
					}
					
					$xml4	=	$xml->addChild( 'fields' );
					$i		=	1;
					foreach ( $table_fields as $k=>$v ) {
						if ( isset( $fields[$k] ) || in_array( $k, $table_pkeys ) || $k == 'cck'  ) {
							$field	=	$xml4->addChild( 'field'.$i, $k );
							$field->addAttribute( 'type', $v->Type );
							$field->addAttribute( 'default', $v->Default );
							$i++;
						}
					}
					
					// Set
					$buffer	=	'<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML();
					$path	=	$dest.'/'.$elemtype.'_'.str_replace( '#__', '', $name ).'.xml';
					JFile::write( $path, $buffer );
				}
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Language
	
	// exportLanguage
	public static function exportLanguage( $path, $root, $dest, $copyright = '' )
	{
		if ( !is_file( $path ) ) {
			return;
		}
		$xml	=	JCckDev::fromXML( $path );
		
		if ( ! isset( $xml->languages ) ) {
			return;
		}
		$languages	=	$xml->languages;
		$dest_l		=	$dest.'/'.(string)@$languages->attributes()->folder;
		CCK_Export::createDir( $dest_l );
		if ( isset( $languages->language ) && count( $languages->language ) ) {
			foreach ( $languages->language as $lang ) {
				$tag	=	(string)$lang->attributes()->tag;
				CCK_Export::createDir( $dest_l.'/'.$tag );
				$lang	=	(string)$lang;
				if ( JFile::exists( $root.'/language/'.$lang ) ) {
					if ( $copyright ) {
						CCK_Export::update( $root.'/language/'.$lang, $copyright );
					}
					JFile::copy( $root.'/language/'.$lang, $dest_l.'/'.$lang );
				}
			}
		}
	}
	
	// exportLanguages
	public static function exportLanguages( $path, $dest, $lang, $client, $search, $extensions = array() )
	{
		CCK_Export::createDir( $dest );
		
		$package	=	(object)array( 'name'=>'cck_'.$lang.'_'.$client, 'tag'=>$lang, 'description'=>'SEBLOD 3.x '.$lang.' Language Pack - www.seblod.com',
									   'client'=>( $client == 'admin' ? 'administrator' : $client ) );
		$xml		=	CCK_Export::prepareLanguage( $package );
		$filelist	=	$xml->addChild( 'fileset' );
		$list		=	$filelist->addChild( 'files' );
		$target		=	( $client == 'site' ) ? 'language/'.$lang : 'administrator/language/'.$lang;
		$list->addAttribute( 'target', $target );
		
		$files		=	JFolder::files( $path, $search );
		if ( count( $extensions ) ) {
			foreach ( $files as $file ) {
				$id	=	str_replace( array( $lang.'.', '.sys.ini', '.ini' ), array( '', '', '' ), $file );
				if ( isset( $extensions[$id] ) ) {
					JFile::copy( $path.'/'.$file, $dest.'/'.$file );
					$list->addChild( 'filename', $file );
				}
			}			
		} else {
			foreach ( $files as $file ) {
				JFile::copy( $path.'/'.$file, $dest.'/'.$file );
			}
		}
		
		CCK_Export::createFile( $dest.'/'.$package->name.'.xml', '<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML() );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Package
	
	// prepareFile
	public static function prepareFile( $file )
	{
		$xml	=	new JCckDevXml( '<extension />' );
		$xml->addAttribute( 'type', 'file' );
		$xml->addAttribute( 'version', '2.5' );
		$xml->addAttribute( 'method', 'upgrade' );
		$xml->addChild( 'name', htmlspecialchars( $file->title ) );
		$xml->addChild( 'author', 'Octopoos' );
		$xml->addChild( 'authorEmail', 'contact@seblod.com' );
		$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
		$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
		$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
		$xml->addChild( 'creationDate', date( 'F Y' ) );
		$xml->addChild( 'description', ( @$file->description ) ? htmlspecialchars( $file->description ) : 'SEBLOD 3.x Position Variation - www.seblod.com' );
		$xml->addChild( 'version', '1.0.0' );
		
		return $xml;
	}
	
	// prepareLanguage
	public static function prepareLanguage( $package )
	{
		$xml	=	new JCckDevXml( '<extension />' );
		$xml->addAttribute( 'type', 'file' );
		$xml->addAttribute( 'version', '2.5' );
		$xml->addAttribute( 'method', 'upgrade' );
		$xml->addChild( 'name', htmlspecialchars( $package->name ) );
		$xml->addChild( 'author', 'Octopoos' );
		$xml->addChild( 'authorEmail', 'contact@seblod.com' );
		$xml->addChild( 'authorUrl', 'https://www.seblod.com' );
		$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
		$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
		$xml->addChild( 'creationDate', date( 'F Y' ) );
		$xml->addChild( 'description', ( @$package->description ) ? htmlspecialchars( $package->description ) : 'SEBLOD 3.x Language Pack - www.seblod.com' );
		$xml->addChild( 'version', '1.0.0' );
		
		return $xml;
	}
	
	// preparePackage
	public static function preparePackage( $package )
	{
		$xml	=	new JCckDevXml( '<extension />' );
		$xml->addAttribute( 'type', 'package' );
		$xml->addAttribute( 'version', '2.5' );
		$xml->addAttribute( 'method', 'upgrade' );
		$xml->addChild( 'name', htmlspecialchars( $package->title ) );
		$xml->addChild( 'packagename', htmlspecialchars( $package->name ) );
		$xml->addChild( 'packager', 'Octopoos' );
		$xml->addChild( 'packagerurl', 'https://www.seblod.com' );
		$xml->addChild( 'copyright', 'Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.' );
		$xml->addChild( 'license', 'GNU General Public License version 2 or later.' );
		$xml->addChild( 'creationDate', date( 'F Y' ) );
		$xml->addChild( 'description', ( @$package->description ) ? htmlspecialchars( $package->description ) : 'SEBLOD 3.x App - www.seblod.com' );
		$xml->addChild( 'version', '1.0.0' );
		
		return $xml;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Plugins
	
	// getCorePlugins
	public static function getCorePlugins()
	{
		$core		=	array();
		$manifests	=	array(
							JPATH_ADMINISTRATOR.'/manifests/packages/pkg_cck.xml',
							JPATH_ADMINISTRATOR.'/manifests/packages/pkg_cck_ecommerce.xml',
							JPATH_ADMINISTRATOR.'/manifests/packages/pkg_cck_webservices.xml'
						);

		foreach ( $manifests as $manifest ) {
			if ( is_file( $manifest ) ) {
				$xml	=	JCckDev::fromXML( $manifest );
				if ( ! isset( $xml->files ) ) {
					continue;
				}
				$files	=	$xml->files;
				if ( isset( $files->file ) && count( $files->file ) ) {
					foreach ( $files->file as $file ) {
						if ( (string)$file->attributes()->type == 'plugin' ) {
							$group					=	(string)$file->attributes()->group;
							$name					=	str_replace( 'plg_'.$group.'_', '', (string)$file->attributes()->id );
							$core[$group][$name]	=	true;
						}
					}
				}
			}
		}
		
		return $core;
	}
	
	// getCoreTables
	public static function getCoreTables()
	{
		$core		=	array();
		$plugins	=	JCckDatabase::loadColumn( 'SELECT element FROM #__extensions WHERE folder = "cck_storage_location"' );

		if ( count( $plugins ) ) {
			foreach ( $plugins as $plugin ) {
				if ( $plugin != '' && is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$plugin.'/'.$plugin.'.php' ) ) {
					require_once JPATH_SITE.'/plugins/cck_storage_location/'.$plugin.'/'.$plugin.'.php';
					$properties	=	array( 'table' );
					$properties = JCck::callFunc( 'plgCCK_Storage_Location'.$plugin, 'getStaticProperties', $properties );
					if ( $properties['table'] ) {
						$core[$properties['table']]	=	'';
					}
				}
			}
		}
		
		return $core;
	}
}
?>