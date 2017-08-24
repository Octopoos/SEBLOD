<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: import.php sebastienheraud $
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

JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );
JLoader::register( 'JTableMenuType', JPATH_PLATFORM.'/legacy/table/menu/type.php' );
JLoader::register( 'JTableMenu', JPATH_PLATFORM.'/legacy/table/menu.php' );

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_folder.php';

// Import
class CCK_Import
{
	// importContent
	public static function importContent( $type, $base, $items, &$data, $config = array() )
	{
		foreach ( $items as $name ) {
			$path	=	$base.$name;
			$xml	=	JCckDev::fromXML( $path );
			if ( !$xml || (string)$xml->attributes()->type != $type ) {
				return;
			}
			if ( $type == 'joomla_menu' ) {
				$item	=	JTable::getInstance( 'MenuType' );
			} elseif ( $type == 'joomla_menuitem' ) {
				$item	=	JTable::getInstance( 'Menu' );
			} elseif ( $type == 'joomla_category' ) {
				$item	=	JTable::getInstance( 'Category' );
			} else {
				return;
			}
			$root	=	$xml->{$type};
			
			foreach ( $item as $k => $v ) {
				if ( isset( $root->{$k} ) ) {
					$item->$k	=	(string)$root->{$k};
				}
			}
			
			// Store
			$call	=	'beforeImport'.$type;
			$pk		=	self::$call( $type, $item, $data, $config );
			if ( $pk ) {
				continue;
			}
			$item->check();
			$item->store();
			$call	=	'afterImport'.$type;
			self::$call( $type, $item, $xml, $data );
		}
	}
	
	// importElements
	public static function importElements( $elemtype, $path, $items, &$data, $config = array() )
	{
		require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/tables/'.$elemtype.'.php';
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				CCK_Import::importElement( $elemtype, $path.$item, $data, $config );
			}
		}
	}
	
	// importElement
	public static function importElement( $elemtype, $path, &$data, $config = array() )
	{
		$xml	=	JCckDev::fromXML( $path );
		if ( !$xml || (string)$xml->attributes()->type != $elemtype.'s' ) {
			return;
		}
		$item	=	JTable::getInstance( ucfirst( $elemtype ), 'CCK_Table' );
		$root	=	$xml->{$elemtype};
		
		foreach ( $item as $k => $v ) {
			if ( isset( $root->{$k} ) ) {
				$item->$k	=	(string)$root->{$k};
			}
		}
		if ( ! ( isset( $item->name ) && $item->name != '' ) ) {
			return;
		}
		
		// Folder
		if ( isset( $item->folder ) ) {
			$idx	=	$item->folder;
			if ( isset( $data['folders2'][$idx] ) ) {
				$item->folder	=	$data['folders2'][$idx]->id;
			} elseif ( isset( $data['folders'][$idx] ) ) {
				$item->folder	=	$data['folders'][$idx]->id;
			} else {
				$item->folder	=	7;
			}
		}
		
		// Store
		$call	=	'beforeImport'.$elemtype;
		$pk		=	self::$call( $elemtype, $item, $data, $config );
		if ( $pk != -1 ) {
			if ( $pk > 0 ) {
				$item->id	=	$pk;
			}
			$item->store();
			$call	=	'afterImport'.$elemtype;
			self::$call( $xml, $elemtype, $item, $data );
		}
	}
	
	// beforeImportFolder
	public static function beforeImportFolder( $elemtype, &$item, $data, $config = array() )
	{
		if ( isset( $item->parent_id ) ) {
			$idx	=	$item->parent_id;
			if ( isset( $item->path ) && isset( $data['folders2'][$idx] ) ) {
				$item->parent_id	=	$data['folders2'][$idx]->id;
			} elseif ( isset( $data['folders'][$idx] ) ) {
				$item->parent_id	=	$data['folders'][$idx]->id;
			} else {
				$item->parent_id	=	7;
			}
		}

		return JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_'.$elemtype.'s WHERE name = "'.(string)$item->name.'"' );
	}
	
	// afterImportFolder
	public static function afterImportFolder( &$xml, $elemtype, $item, &$data )
	{
		$acl	=	(string)$xml->acl;
		if ( $acl ) {
			JCckDatabase::execute( 'UPDATE #__assets SET rules = "'.JFactory::getDbo()->escape( $acl ).'" WHERE name = "com_cck.folder.'.$item->id.'"' );
		}
		
		Helper_Folder::rebuildTree( 2, 1 );
		if ( !$item->path ) {
			Helper_Folder::rebuildBranch( $item->id );
			$item->path	=	JCckDatabase::loadResult( 'SELECT a.path FROM #__cck_core_folders AS a WHERE a.id = '.(int)$item->id );
		}
		
		if ( !isset( $data['folders2'][$item->path] ) ) {
			$data['folders'][$item->name]			=	new stdClass;
			$data['folders'][$item->name]->id		=	$item->id;
			$data['folders'][$item->name]->name		=	$item->name;
			
			$data['folders2'][$item->path]			=	new stdClass;
			$data['folders2'][$item->path]->id		=	$item->id;
			$data['folders2'][$item->path]->path	=	$item->path;
		}
	}
	
	// beforeImportField
	public static function beforeImportField( $elemtype, &$item, $data, $config = array() )
	{
		$pk	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_'.$elemtype.'s WHERE name = "'.(string)$item->name.'"' );

		if ( $pk > 0 && $config['isApp'] && $config['isUpgrade'] && $item->storage != 'dev' ) {
			return -1;
		}
		if ( file_exists( JPATH_SITE.'/plugins/cck_field/'.$item->type.'/classes/app.php' ) ) {
			require_once JPATH_SITE.'/plugins/cck_field/'.$item->type.'/classes/app.php';
			JCck::callFunc_Array( 'plgCCK_Field'.$item->type.'_App', 'onCCK_FieldImportField', array( &$item, $data ) );
		}

		return $pk;
	}
	
	// afterImportField
	public static function afterImportField( &$xml, $elemtype, $item, &$data )
	{
	}
	
	// beforeImportTemplate
	public static function beforeImportTemplate( $elemtype, &$item, $data, $config = array() )
	{
		return JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_'.$elemtype.'s WHERE name = "'.(string)$item->name.'"' );
	}
	
	// afterImportTemplate
	public static function afterImportTemplate( &$xml, $elemtype, $item, &$data )
	{
	}
	
	// beforeImportType
	public static function beforeImportType( $elemtype, &$item, $data, $config = array() )
	{
		$pk		=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_'.$elemtype.'s WHERE name = "'.(string)$item->name.'"' );

		if ( $pk > 0 && $config['isApp'] && $config['isUpgrade'] ) {
			return -1;
		}
		self::_setStyle( array( 'admin', 'site', 'intro', 'content' ), $item, $data );

		return $pk;
	}
	
	// afterImportType
	public static function afterImportType( &$xml, $elemtype, $item, &$data )
	{
		JCckDatabase::execute( 'DELETE IGNORE a.*, b.* FROM #__cck_core_'.$elemtype.'_field AS a'
							 . ' LEFT JOIN #__cck_core_'.$elemtype.'_position AS b ON b.'.$elemtype.'id = a.'.$elemtype.'id'
							 . ' WHERE a.'.$elemtype.'id = '.(int)$item->id );
		
		$db		=	JFactory::getDbo();
		$acl	=	(string)$xml->acl;
		JCckDatabase::execute( 'UPDATE #__assets SET rules = "'.$db->escape( $acl ).'" WHERE name = "com_cck.form.'.$item->id.'"' );

		if ( !isset( $data['tables_columns']['#__cck_core_'.$elemtype.'_field'] ) ) {
			$table	=	'#__cck_core_'.$elemtype.'_field';
			$data['tables_columns'][$table]	=	$db->getTableColumns( $table );
			unset( $data['tables_columns'][$table][$elemtype.'id'] );
			unset( $data['tables_columns'][$table]['fieldid'] );
			
			$table	=	'#__cck_core_'.$elemtype.'_position';
			$data['tables_columns'][$table]	=	$db->getTableColumns( $table );
			unset( $data['tables_columns'][$table][$elemtype.'id'] );
			unset( $data['tables_columns'][$table]['position'] );
		}
		self::_importJoined( 'field', $xml->fields->children(), $elemtype, $item, $data );
		self::_importJoined( 'position', $xml->positions->children(), $elemtype, $item, $data );
	}
	
	// beforeImportSearch
	public static function beforeImportSearch( $elemtype, &$item, $data, $config = array() )
	{
		$pk		=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_'.$elemtype.'s WHERE name = "'.(string)$item->name.'"' );

		if ( $pk > 0 && $config['isApp'] && $config['isUpgrade'] ) {
			return -1;
		}
		self::_setStyle( array( 'search', 'filter', 'list', 'item' ), $item, $data );

		return $pk;
	}
	
	// afterImportSearch
	public static function afterImportSearch( &$xml, $elemtype, $item, &$data )
	{
		$db		=	JFactory::getDbo();

		JCckDatabase::execute( 'DELETE IGNORE a.*, b.* FROM #__cck_core_'.$elemtype.'_field AS a'
							 . ' LEFT JOIN #__cck_core_'.$elemtype.'_position AS b ON b.'.$elemtype.'id = a.'.$elemtype.'id'
							 . ' WHERE a.'.$elemtype.'id = '.(int)$item->id );
		
		if ( !isset( $data['tables_columns']['#__cck_core_'.$elemtype.'_field'] ) ) {
			$table	=	'#__cck_core_'.$elemtype.'_field';
			$data['tables_columns'][$table]	=	$db->getTableColumns( $table );
			unset( $data['tables_columns'][$table][$elemtype.'id'] );
			unset( $data['tables_columns'][$table]['fieldid'] );
			
			$table	=	'#__cck_core_'.$elemtype.'_position';
			$data['tables_columns'][$table]	=	$db->getTableColumns( $table );
			unset( $data['tables_columns'][$table][$elemtype.'id'] );
			unset( $data['tables_columns'][$table]['position'] );
		}
		self::_importJoined( 'field', $xml->fields->children(), $elemtype, $item, $data );
		self::_importJoined( 'position', $xml->positions->children(), $elemtype, $item, $data );
	}
	
	// _importJoined
	protected static function _importJoined( $type, $joined, $elemtype, $item, &$data )
	{
		$db		=	JFactory::getDbo();
		$str	=	'';
		$table	=	'#__cck_core_'.$elemtype.'_'.$type;
		
		foreach ( $joined as $j ) {
			$name			=	(string)$j;
			if ( $type == 'field' ) {
				if ( isset( $data['fields'][$name] ) ) {
					if ( file_exists( JPATH_SITE.'/plugins/cck_field/'.$data['fields'][$name]->type.'/classes/app.php' ) ) {
						require_once JPATH_SITE.'/plugins/cck_field/'.$data['fields'][$name]->type.'/classes/app.php';
						JCck::callFunc_Array( 'plgCCK_Field'.$data['fields'][$name]->type.'_App', 'onCCK_FieldImport'.$elemtype.'_Field', array( $data['fields'][$name], &$j, $data ) );
					}
					$name	=	$data['fields'][$name]->id;
				} else {
					$name	=	'';
				}
			}
			
			if ( $name ) {
				$str2		=	$item->id.', "'.$name.'", ';
				$attributes	=	$j->attributes();
				
				if ( (string)$attributes->link != '' && isset( $data['fields'][$name] ) ) {
					if ( file_exists( JPATH_SITE.'/plugins/cck_field_link/'.(string)$attributes->link.'/classes/app.php' ) ) {
						require_once JPATH_SITE.'/plugins/cck_field_link/'.(string)$attributes->link.'/classes/app.php';
						JCck::callFunc_Array( 'plgCCK_Field_Link'.(string)$attributes->link.'_App', 'onCCK_Field_LinkImport'.$elemtype.'_Field', array( $data['fields'][$name], &$attributes, $data ) );
					}
				}
				
				foreach ( $data['tables_columns'][$table] as $key=>$val ) {
					if ( isset( $attributes[$key] ) ) {
						$str2	.=	'"'.$db->escape( (string)$attributes[$key] ).'", ';
					} else {
						$str2	.=	'"", ';
					}
				}
				if ( $str2 != '' ) {
					$str2	=	substr( trim( $str2 ), 0, -1 );
					$str	.=	'(' . $str2 . '), ';
				}
			}
		}
		if ( strlen( $str ) > 1 ) {
			$str	=	substr( trim( $str ), 0, -1 );
			JCckDatabase::execute( 'INSERT INTO '.$table.' VALUES '.$str );
		}
	}
	
	// _setStyle
	protected static function _setStyle( $views, &$item, $data )
	{
		foreach ( $views as $v ) {
			$e	=	'template_'.$v;
			$t	=	$item->$e;
			$s	=	0;
			if ( strpos( $t, '('.$v.')' ) !== false ) {
				if ( isset( $data['styles']['custom'][$t] ) ) {
					$s	=	$data['styles']['custom'][$t]->id;
				}
			} else {
				if ( isset( $data['styles']['default'][$t] ) ) {
					$s	=	$data['styles']['default'][$t]->id;
				}
			}
			$item->$e	=	$s;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Content

	// beforeImportJoomla_Category
	public static function beforeImportJoomla_Category( $type, &$table, &$data, $config = array() )
	{
		if ( $config['isApp'] && $config['isUpgrade'] ) { // todo: improve (import only new categories)
			return -1;
		}

		return 0;
	}
	
	// afterImportJoomla_Category
	public static function afterImportJoomla_Category( $type, &$table, $xml, &$data )
	{
		self::afterImportCategory( $type, $table, $xml, $data );
	}

	// beforeImportJoomla_Menu
	public static function beforeImportJoomla_Menu( $type, &$table, &$data, $config = array() )
	{
		if ( $config['isApp'] && $config['isUpgrade'] ) {
			return -1;
		}

		return 0;
	}
	
	// afterImportJoomla_Menu
	public static function afterImportJoomla_Menu( $type, &$table, $xml, &$data )
	{
	}

	// beforeImportJoomla_MenuItem
	public static function beforeImportJoomla_MenuItem( $type, &$table, &$data, $config = array() )
	{
		if ( $config['isApp'] && $config['isUpgrade'] ) {
			return -1;
		}

		if ( $table->type == 'component' ) {
			$table->component_id	=	JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "'.$table->component_id.'"' );
		}
		if ( $table->level > 1 ) {
			$table->parent_id		=	JCckDatabase::loadResult( 'SELECT id FROM #__menu WHERE alias = "'.$table->parent_id.'"' );
		}
		$table->setLocation( $table->parent_id, 'last-child' );

		return 0;
	}
	
	// afterImportJoomla_Menu
	public static function afterImportJoomla_MenuItem( $type, &$table, $xml, &$data )
	{
		$table->rebuildPath( $table->id );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // More
	
	// importMore
	public static function importMore( $elemtype, $path, $items, &$data )
	{
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$xml	=	JCckDev::fromXML( $path.$item );
				if ( !$xml || (string)$xml->attributes()->type != $data['elements'][$elemtype] ) {
					return;
				}
				
				$root	=	$xml->{$elemtype};
				$call	=	'beforeImport'.$elemtype;
				$table	=	self::$call( $elemtype, $data );
				foreach ( $table as $k => $v ) {
					if ( isset( $root->{$k} ) ) {
						$table->$k	=	(string)$root->{$k};
					}
				}
				$call	=	'afterImport'.$elemtype;
				self::$call( $elemtype, $table, $xml, $data );
			}
		}
	}
	
	// beforeImportCategory
	public static function beforeImportCategory( $elemtype, &$data )
	{
		return JTable::getInstance( 'Category' );
	}
	
	// afterImportCategory
	public static function afterImportCategory( $elemtype, &$table, $xml, &$data )
	{
		$app	=	( isset( $xml->app ) ) ? (string)$xml->app : '';
		$core	=	( $app ) ? JCckDatabase::loadObject( 'SELECT id, pk FROM #__cck_core WHERE app = "'.$app.'"' ) : 0;

		if ( is_object( $core ) ) {
			$id		=	$core->id;
			$isNew	=	false;
		} else {
			$id		=	JCckPluginLocation::g_onCCK_Storage_LocationPrepareStore();
			$isNew	=	true;
		}
		
		if ( @$core->pk > 0 ) {
			$table->id	=	$core->pk;
		}
		$table->description	=	'::cck::'.$id.'::/cck::<br />::description::::/description::';	//todo
		$table->parent_id	=	( $data['root_category'] > 0 ) ? $data['root_category'] : 1;
		
		$rules	=	new JAccessRules( '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}' );
		$table->setRules( $rules );
		$table->setLocation( $table->parent_id, 'last-child' );
		$table->check();
		$table->store();
		$data['root_category']		=	( $data['root_category'] > 0 ) ? $data['root_category'] : $table->id;
		$data['categories'][$app]	=	$table->id;
		
		if ( $table->rebuildPath( $table->id ) ) {
			$table->rebuild( $table->id, $table->lft, $table->level, $table->path );
		}
		if ( $isNew === true ) {
			$core					=	JCckTable::getInstance( '#__cck_core', 'id' );
			$core->load( $id );
			$core->pk				=	$table->id;
			$core->cck				=	'category';														//todo
			$core->storage_location	=	'joomla_category';
			$core->author_id		=	JCck::getConfig_Param( 'integration_user_default_author', 42 );	//todo
			$core->parent_id		=	$table->parent_id;
			$core->date_time		=	'';																//todo
			$core->app				=	$app;
			$core->storeIt();
		}
	}
	
	// beforeImportTemplate_Style
	public static function beforeImportTemplate_Style( $elemtype, &$data )
	{
		return JCckTable::getInstance( '#__'.$data['elements'][$elemtype], 'id' );
	}
	
	// afterImportTemplate_Style
	public static function afterImportTemplate_Style( $elemtype, &$table, $xml, &$data )
	{
		if ( $table->client_id == '' ) {
			$table->client_id	=	0;
		}
		if ( $table->home == '' ) {
			$table->home	=	0;
		}
		
		// Store
		$pk	=	JCckDatabase::loadResult( 'SELECT id FROM #__'.$elemtype.'s WHERE template = "'.(string)$table->template.'" AND title = "'.(string)$table->title.'"' );
		if ( $pk > 0 ) {
			$table->id	=	$pk;
		}
		$table->store();
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Tables
	
	// importProcessings
	public static function importProcessings( $data )
	{
		$path			=	$data['root'].'/processings';
		$processings	=	JCckDatabaseCache::loadObjectListArray( 'SELECT id, scriptfile, type FROM #__cck_more_processings WHERE published != -44', 'scriptfile', 'type' );

		if ( file_exists( $path ) ) {
			$files	=	JFolder::files( $path, '\.xml$' );
			if ( count( $files ) ) {
				foreach ( $files as $file ) {
					$xml	=	JCckDev::fromXML( $path.'/'.$file );
					if ( !$xml || (string)$xml->attributes()->type != 'processings' ) {
						break;
					}
					$name		=	(string)$xml->processing->name;
					$scriptfile	=	(string)$xml->processing->scriptfile;
					$state		=	(string)$xml->processing->published;
					$type		=	(string)$xml->processing->type;

					if ( $name && $scriptfile && $type != '' ) {
						if ( isset( $processings[$scriptfile] ) ) {
							if ( isset( $processings[$scriptfile][$type] ) ) {
								continue;
							} else {
								$state		=	0;
							}
						}
						$table				=	JCckTable::getInstance( '#__cck_more_processings' );
						$table->name		=	$name;
						$table->title		=	(string)$xml->processing->title;
						
						// Folder			
						$idx	=	(string)$xml->processing->folder;
						if ( isset( $data['folders2'][$idx] ) ) {
							$table->folder	=	$data['folders2'][$idx]->id;
						} elseif ( isset( $data['folders'][$idx] ) ) {
							$table->folder	=	$data['folders'][$idx]->id;
						} else {
							$table->folder	=	7;
						}
						
						$table->type		=	$type;
						$table->description	=	(string)$xml->processing->description;
						$table->options		=	(string)$xml->processing->options;
						$table->ordering	=	(string)$xml->processing->ordering;
						$table->published	=	$state;
						$table->scriptfile	=	$scriptfile;
						
						$table->store();
					}
				}
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // SQL
	
	// importSQL
	public static function importSQL( $src )
	{
		if ( JFolder::exists( $src ) ) {
			$db		=	JFactory::getDbo();
			$files	=	JFolder::files( $src );
			foreach ( $files as $file ) {
				$path	=	$src.'/'.$file;
				if ( JFile::exists( $path ) ) {
					$query	=	file_get_contents( $path );
					$db->setQuery( $query );
					$db->queryBatch();
				}
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Tables
	
	// importTables
	public static function importTables( $data )
	{
		$db		=	JFactory::getDbo();
		$path	=	$data['root'].'/tables';
		
		if ( file_exists( $path ) ) {
			$items	=	JFolder::files( $path, '\.xml$' );
			if ( count( $items ) ) {
				$prefix		=	JFactory::getConfig()->get( 'dbprefix' );
				$tables		=	array_flip( JCckDatabase::loadColumn( 'SHOW TABLES' ) );
				
				foreach ( $items as $item ) {
					$xml	=	JCckDev::fromXML( $path.'/'.$item );
					if ( !$xml || (string)$xml->attributes()->type != 'tables' ) {
						return;
					}
					$name		=	(string)$xml->table->name;
					$table_key	=	(string)$xml->table->primary_key;
					$short		=	str_replace( '#__', $prefix, $name );
					
					if ( isset( $tables[$short] ) ) {
						$table			=	JCckTable::getInstance( $name );
						$table_fields	=	$table->getFields();
						$previous		=	'';
						
						// Fields
						$fields		=	$xml->fields->children();
						if ( count( $fields ) ) {
							foreach ( $fields as $field ) {
								$column		=	(string)$field;
								$type		=	(string)$field->attributes()->type;
								$default	=	(string)$field->attributes()->default;
								if ( !isset( $table_fields[$column] ) ) {
									$query	=	'ALTER TABLE '.$name.' ADD '.JCckDatabase::quoteName( $column ).' '.$type.' NOT NULL';
									$query	.=	( $default != '' ) ? ' DEFAULT "'.$default.'"' : '';
									$query	.=	( $previous != '' ) ? ' AFTER '.JCckDatabase::quoteName( $previous ) : ' FIRST';
									JCckDatabase::execute( $query );
								} else {
									if ( $type != $table_fields[$column]->Type ) {
										$query	=	'ALTER TABLE '.$name.' CHANGE '.JCckDatabase::quoteName( $column ).' '.JCckDatabase::quoteName( $column ).' '.$type.' NOT NULL';
										$query	.=	( $default != '' ) ? ' DEFAULT "'.$default.'"' : '';
										JCckDatabase::execute( $query );
									}
								}
								$previous	=	$column;
							}
						}
						
						// Indexes
						$indexes	=	$xml->indexes->children();
						$indexes2	=	array();
						if ( count( $indexes ) ) {
							foreach ( $indexes as $index ) {
								$idx	=	(string)$index;
								$indexes2[$idx][(string)$index->attributes()->seq_in_type]	=	(string)$index->attributes()->column_name;
							}
						}
						if ( count( $indexes2 ) ) {
							foreach ( $indexes2 as $k=>$v ) {
								if ( $k == 'PRIMARY' ) {
									JCckDatabase::execute( 'ALTER TABLE '.$name.' DROP PRIMARY KEY, ADD PRIMARY KEY ( '.implode( ',', $v ).' )' );
								} else {
									// todo
								}
							}
						}
					} else {
						$sql_query	=	'';
						
						// Fields
						$fields		=	$xml->fields->children();
						if ( count( $fields ) ) {
							foreach ( $fields as $field ) {
								$type		=	(string)$field->attributes()->type;
								$default	=	(string)$field->attributes()->default;
								$sql_query	.=	' '.JCckDatabase::quoteName( (string)$field ).' '.$type.' NOT NULL';
								if ( $default != '' ) {
									$sql_query	.=	' DEFAULT "'.$default.'"';
								}
								$sql_query	.=	',';
							}
						}
						
						// Indexes
						$indexes	=	$xml->indexes->children();
						$indexes2	=	array();
						if ( count( $indexes ) ) {
							foreach ( $indexes as $index ) {
								$idx	=	(string)$index;
								$indexes2[$idx][(string)$index->attributes()->seq_in_type]	=	(string)$index->attributes()->column_name;
							}
						}
						if ( count( $indexes2 ) ) {
							foreach ( $indexes2 as $k=>$v ) {
								$sql_query	.=	( $k == 'PRIMARY' ) ? ' PRIMARY KEY ( '.implode( ',', $v ).' ),' : ' KEY '.$k.' ( '.implode( ',', $v ).' ),';
							}
						}
						
						$sql_query	=	( $sql_query ) ? substr( $sql_query, 0, -1 ) : '';
						JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$name.' (' . $sql_query . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
					}
				}
			}
		}
	}
}
?>