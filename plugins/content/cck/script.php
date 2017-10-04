<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
defined( 'CCK_COM' ) or define( 'CCK_COM', 'com_cck' );

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

// Script
class plgContentCCKInstallerScript
{
	// install
	function install( $parent )
	{
		$data	=	"<!DOCTYPE html><title></title>";
		$groups	=	array( 'cck_field', 'cck_field_link', 'cck_field_live', 'cck_field_restriction', 'cck_field_typo', 'cck_field_validation', 'cck_storage', 'cck_storage_location' );
		foreach ( $groups as $group ) {
			JFile::write( JPATH_PLUGINS.'/'.$group.'/'.'index.html', $data );	
		}
	}
	
	// uninstall
	function uninstall( $parent )
	{
		if ( JFile::exists( JPATH_ADMINISTRATOR.'/language/en-GB/en-GB.lib_cck.ini' ) ) {
			JFile::delete( JPATH_ADMINISTRATOR.'/language/en-GB/en-GB.lib_cck.ini' );
		}
		if ( JFile::exists( JPATH_ADMINISTRATOR.'/language/fr-FR/fr-FR.lib_cck.ini' ) ) {
			JFile::delete( JPATH_ADMINISTRATOR.'/language/fr-FR/fr-FR.lib_cck.ini' );
		}
		
		$groups	=	array( 'cck_field', 'cck_field_link', 'cck_field_live', 'cck_field_restriction', 'cck_field_typo', 'cck_field_validation', 'cck_storage', 'cck_storage_location' );
		foreach ( $groups as $group ) {
			if ( JFolder::exists( JPATH_PLUGINS.'/'.$group ) ) {
				JFolder::delete( JPATH_PLUGINS.'/'.$group );
			}
		}
	}
	
	// update
	function update( $parent )
	{		
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		// WAITING FOR JOOMLA 1.7.x FIX
		$app		=	JFactory::getApplication();
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	$app->cck_core_temp_var;
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		$dest		=	JPATH_SITE.'/libraries/cck/rendering/variations';
		$protected	=	array( 'empty' );
		if ( $tmp_dir && JFolder::exists( $path ) ) {
			$vars		=	JFolder::folders( $path );
			foreach ( $vars as $var ) {
				if ( ! in_array( $var, $protected ) ) {
					JFolder::move( $path.'/'.$var, $dest.'/'.$var );
				}
			}
			JFolder::delete( $path );
		}
		// WAITING FOR JOOMLA 1.7.x FIX
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();
		
		// Force { CCK } Plugins + { CCK } Library to be published
		$db->setQuery( 'UPDATE #__extensions SET enabled = 1 WHERE element = "cck"' );
		$db->execute();
		
		// Rename Menu Item
		$db->setQuery( 'UPDATE #__menu SET title = "com_cck", alias = "SEBLOD", path="SEBLOD" WHERE link = "index.php?option=com_cck"' );
		$db->execute();

		// Re-build menu
		$query	=	'SELECT id, level, lft, path FROM #__menu WHERE link = "index.php?option=com_cck"';
		$db->setQuery( $query );
		$seblod	=	$db->loadObject();
		if ( $seblod->id > 0 ) {		
			$query	=	'SELECT extension_id as id, element FROM #__extensions WHERE type = "component" AND element LIKE "com_cck_%" ORDER BY name';
			$db->setQuery( $query );
			$addons	=	$db->loadObjectList();
			if ( count( $addons ) ) {			
				JLoader::register( 'JTableMenu', JPATH_PLATFORM.'/joomla/database/table/menu.php' );
				$titles	=	array(
								'com_cck_builder'=>'Builder',
								'com_cck_developer'=>'Developer',
								'com_cck_ecommerce'=>'eCommerce',
								'com_cck_exporter'=>'Exporter',
								'com_cck_importer'=>'Importer',
								'com_cck_manager'=>'Manager',
								'com_cck_multilingual'=>'Multilingual',
								'com_cck_packager'=>'Packager',
								'com_cck_toolbox'=>'Toolbox',
								'com_cck_updater'=>'Updater',
								'com_cck_webservices'=>'WebServices'
							);
				foreach ( $addons as $addon ) {
					$addon->title	=	$titles[$addon->element];
					self::_addAddon( $addon, $seblod, $type );
				}
			}
		}	
		
		// Reorder Plugins
		$i		=	2;
		$ids	=	'';
		$query	=	'SELECT extension_id FROM #__extensions WHERE type = "plugin" AND folder = "content" AND element != "cck" ORDER BY ordering';
		$db->setQuery( $query );
		$plgs	=	$db->loadObjectList();
		$sql	=	'UPDATE #__extensions SET ordering = CASE extension_id';
		foreach ( $plgs as $p ) {
			$ids	.=	$p->extension_id.',';
			$sql	.=	' WHEN '.$p->extension_id.' THEN '.$i;
			$i++;
		}
		$ids	=	substr( $ids, 0, -1 );
		$sql	.=	' END WHERE extension_id IN ('.$ids.')';
		$db->setQuery( $sql );
		$db->execute();			
		$db->setQuery( 'UPDATE #__extensions SET ordering = 1 WHERE type = "plugin" AND folder = "content" AND element = "cck"' );
		$db->execute();
		
		if ( $type == 'install' ) {
			// Manage Modules
			$modules	=	array(	0=>array( 'name'=>'mod_cck_menu', 'update'=>'title = "Admin Menu - SEBLOD", access = 3, published = 1, position = "menu", ordering = 2' ),
									1=>array( 'name'=>'mod_cck_quickadd', 'update'=>'title = "Quick Add - SEBLOD", access = 3, published = 1, position = "status", ordering = 0' ),
									2=>array( 'name'=>'mod_cck_quickicon', 'update'=>'title = "Quick Icons - SEBLOD", access = 3, published = 1, position = "icon", ordering = 2' ),
									3=>array( 'name'=>'mod_cck_breadcrumbs', 'update'=>'title = "Breadcrumbs - SEBLOD"' ),
									4=>array( 'name'=>'mod_cck_form', 'update'=>'title = "Form - SEBLOD"' ),
									5=>array( 'name'=>'mod_cck_list', 'update'=>'title = "List - SEBLOD"' ),
									6=>array( 'name'=>'mod_cck_search', 'update'=>'title = "Search - SEBLOD"' ) );
			foreach ( $modules as $module ) {
				$query	=	'UPDATE #__modules SET '.$module['update'].' WHERE module = "'.$module['name'].'"';
				$db->setQuery( $query );
				$db->execute();
				$query	=	'SELECT id FROM #__modules WHERE module="'.$module['name'].'"';
				$db->setQuery( $query );
				$mid	=	$db->loadResult();
				
				try {
					$query	=	'INSERT INTO #__modules_menu (moduleid, menuid) VALUES ('.$mid.', 0)';
					$db->setQuery( $query );
					$db->execute();
				} catch ( Exception $e ) {
					// Do nothing
				}
			}
				
			// Publish Plugins
			$query	=	'UPDATE #__extensions SET enabled = 1 WHERE folder LIKE "cck_%"';
			$db->setQuery( $query );
			$db->execute();

			// Set Template Styles
			$query	=	'SELECT id FROM #__template_styles WHERE template="seb_one" ORDER BY id';
			$db->setQuery( $query );
			$style	=	$db->loadResult();
			$query	=	'SELECT id FROM #__template_styles WHERE template="seb_blog" ORDER BY id';
			$db->setQuery( $query );
			$style2	=	$db->loadResult();
			$query	=	'SELECT id FROM #__template_styles WHERE template="seb_table" ORDER BY id';
			$db->setQuery( $query );
			$style3	=	$db->loadResult();
			
			// - Content Types
			$query	=	'UPDATE #__cck_core_types SET template_admin = '.$style.', template_site = '.$style.', template_content = '.$style.', template_intro = '.$style;
			$db->setQuery( $query );
			$db->execute();
			
			// - Search Types (Blog)
			$query	=	'UPDATE #__cck_core_searchs SET template_search = '.$style.', template_filter = '.$style.', template_list = '.$style2.', template_item = '.$style.' WHERE id IN (1,5,8)';
			$db->setQuery( $query );
			$db->execute();

			// - Search Types (Table)
			$query	=	'UPDATE #__cck_core_searchs SET template_search = '.$style.', template_filter = '.$style.' WHERE id IN (11,15,18)';
			$db->setQuery( $query );
			$db->execute();
			
			$searchs	=	array(
								'11'=>array(
										'list'=>array( 'seb_table', 0, '0', 'seb_table - article_manager (list)', '{"rendering_css_class":"","rendering_item_attributes":"sortable-group-id=\\"$cck->getValue(\'art_catid\')\\"","cck_client_item":"0","class_table":"table table-striped","table_header":"0","class_table_tr_even":"","table_layout":"responsive","class_table_tr_odd":"","table_columns":"0","position_margin":"10"}' )
									  ),
								'15'=>array(
										'list'=>array( 'seb_table', 0, '0', 'seb_table - category_manager (list)', '{"rendering_css_class":"","rendering_item_attributes":"sortable-group-id=\\"$cck->getValue(\'cat_parent_id\')\\"","cck_client_item":"0","class_table":"table table-striped","table_header":"0","class_table_tr_even":"","table_layout":"responsive","class_table_tr_odd":"","table_columns":"0","position_margin":"10"}' )
									  ),
								'18'=>array(
										'list'=>array( 'seb_table', 0, '0', 'seb_table - user_manager (list)', '{"rendering_css_class":"","rendering_item_attributes":"","cck_client_item":"0","class_table":"table table-striped","table_header":"0","class_table_tr_even":"","table_layout":"responsive","class_table_tr_odd":"","table_columns":"0","position_margin":"10"}' )
									  )
							);

			if ( count( $searchs ) ) {
				foreach ( $searchs as $k=>$v ) {
					$s	=	0;

					if ( is_array( $v ) ) {
						if ( is_array( $v['list'] ) ) {
							$query	=	'INSERT INTO #__template_styles (template, client_id, home, title, params) VALUES ("'.$v['list'][0].'",'.$v['list'][1].',"'.$v['list'][2].'","'.$v['list'][3].'","'.$db->escape( $v['list'][4] ).'")';
							$db->setQuery( $query );
							if ( $db->execute() ) {
								$query	=	'SELECT MAX(id) FROM #__template_styles';
								$db->setQuery( $query );
								$s		=	$db->loadResult();
							}
						} elseif ( $v['list'] ) {
							$s	=	$v['list'];
						}

						if ( $s ) {
							$query	=	'UPDATE #__cck_core_searchs SET template_list = '.$s.' WHERE id = '.(int)$k;
							$db->setQuery( $query );
							$db->execute();
						}
					}
				}
			}

			// Add Categories
			JPluginHelper::importPlugin( 'content' );
			JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );
			
			$categories	=	array(	0=>array( 'title'=>'Users', 'published'=>'1', 'access'=>'2', 'language'=>'*', 'parent_id'=>1, 'plg_name'=>'joomla_user' ),
									1=>array( 'title'=>'User Groups', 'published'=>'1', 'access'=>'2', 'language'=>'*', 'parent_id'=>1, 'plg_name'=>'joomla_user_group' ) );
			$dispatcher	=	JEventDispatcher::getInstance();
			
			foreach ( $categories as $category ) {
				$table	=	JTable::getInstance( 'Category' );
				$table->access	=	2;
				$table->setLocation( 1, 'last-child' );	
				$table->bind( $category );
				$rules	=	new JAccessRules( '{"core.create":{"1":0},"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}' );
				$table->setRules( $rules );
				$table->check();
				$table->extension	=	'com_content';
				$table->path		.=	$table->alias;
				$table->language	=	'*';
				$table->store();
				
				$dispatcher->trigger( 'onContentBeforeSave', array( '', &$table, true ) );
				$table->store();
				$dispatcher->trigger( 'onContentAfterSave', array( '', &$table, true ) );
				
				$query			=	'SELECT extension_id as id, params FROM #__extensions WHERE type="plugin" AND folder="cck_storage_location" AND element="'.$category['plg_name'].'"';
				$db->setQuery( $query );
				$plugin			=	$db->loadObject();
				$params			=	str_replace( '"bridge_default-catid":"2"', '"bridge_default-catid":"'.$table->id.'"', $plugin->params );
				$query			=	'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE extension_id = '.(int)$plugin->id;
				$db->setQuery( $query );
				$db->execute();
			}
			
			// Init Default Author
			$res	=	JCckDatabase::loadResult( 'SELECT id FROM #__users ORDER BY id ASC' );
			$params =	JComponentHelper::getParams( 'com_cck' );
			$params->set( 'integration_user_default_author', (int)$res );
			$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE name = "com_cck"' );
			$db->execute();
			
			// Init Default Config
			$params->set( 'site_variation', 'seb_css3b' );
			$params->set( 'site_variation_form', 'seb_css3b' );
			$params->set( 'optimize_memory', '1' );
			
			// Init ACL
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_admin.php';
			$pks	=	JCckDatabase::loadColumn( 'SELECT id FROM #__cck_core_folders ORDER BY lft' );
			if ( count( $pks ) ) {
				$rules	=	'{"core.create":[],"core.delete":[],"core.delete.own":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
				Helper_Admin::initACL( array( 'table'=>'folder', 'name'=>'folder', 'rules'=>$rules ), $pks );
			}
			$pks	=	JCckDatabase::loadColumn( 'SELECT id FROM #__cck_core_types ORDER BY id' );
			if ( count( $pks ) ) {
				$rules	=	'{"core.create":[],"core.create.max.parent":{"8":0},"core.create.max.parent.author":{"8":0},"core.create.max.author":{"8":0},'
						.	'"core.delete":[],"core.delete.own":[],"core.edit":[],"core.edit.own":[]}';
				$rules2	=	array( 8=>'{"core.create":{"1":1,"2":0},"core.create.max.parent":{"8":0},"core.create.max.parent.author":{"8":0},"core.create.max.author":{"8":0},'
									. '"core.delete":[],"core.delete.own":[],"core.edit":{"4":0},"core.edit.own":{"2":1}}' );
				Helper_Admin::initACL( array( 'table'=>'type', 'name'=>'form', 'rules'=>$rules ), $pks, $rules2 );
			}

			// Set Initial Version
			$params->set( 'initial_version', $app->cck_core_version );
			$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE name = "com_cck"' );
			$db->execute();

			// Set Utf8mb4 flag
			self::_setUtf8mb4( $params );
		} else {
			$new		=	$app->cck_core_version;
			$old		=	$app->cck_core_version_old;
			$root		=	JPATH_ADMINISTRATOR.'/components/com_cck';
			require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_folder.php';

			// ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** //
			$versions	=	array(	0=>'2.0.0', 1=>'2.0.0.RC2', 2=>'2.0.0.RC2-1', 3=>'2.0.0.RC2-2', 4=>'2.0.0.RC2-3', 5=>'2.0.0.RC3', 6=>'2.0.0.RC4',
									7=>'2.0.0.GA', 8=>'2.0.5', 9=>'2.0.6', 10=>'2.0.7', 11=>'2.1.0', 12=>'2.1.5', 13=>'2.2.0', 14=>'2.2.5',
									15=>'2.3.0', 16=>'2.3.1', 17=>'2.3.5', 18=>'2.3.6', 19=>'2.3.7', 20=>'2.3.8', 21=>'2.3.9', 22=>'2.3.9.2',
									23=>'2.4.5', 24=>'2.4.6', 25=>'2.4.7', 26=>'2.4.8', 27=>'2.4.8.5', 28=>'2.4.9',
									29=>'2.4.9.1', 30=>'2.4.9.2', 31=>'2.4.9.5', 32=>'2.4.9.6', 33=>'2.4.9.7', 34=>'2.4.9.8', 35=>'2.5.0', 36=>'2.5.1', 37=>'2.5.2',
									38=>'2.6.0', 39=>'2.7.0', 40=>'2.8.0', 41=>'2.9.0', 42=>'3.0.0', 43=>'3.0.1', 44=>'3.0.2', 45=>'3.0.3', 46=>'3.0.4', 47=>'3.0.5',
									48=>'3.1.0', 49=>'3.1.1', 50=>'3.1.2', 51=>'3.1.3', 52=>'3.1.4', 53=>'3.1.5',
									54=>'3.2.0', 55=>'3.2.1', 56=>'3.2.2', 57=>'3.3.0', 58=>'3.3.1', 59=>'3.3.2', 60=>'3.3.3', 61=>'3.3.4', 62=>'3.3.5', 63=>'3.3.6', 64=>'3.3.7', 65=>'3.3.8',
									66=>'3.4.0', 67=>'3.4.1', 68=>'3.4.2', 69=>'3.4.3', 70=>'3.5.0', 71=>'3.5.1',
									72=>'3.6.0', 73=>'3.6.1', 74=>'3.6.2', 75=>'3.6.3', 76=>'3.6.4', 77=>'3.6.5',
									78=>'3.7.0', 79=>'3.7.1', 80=>'3.7.2', 81=>'3.7.3', 82=>'3.7.4', 83=>'3.7.5', 84=>'3.7.6', 85=>'3.7.7',
									86=>'3.8.0', 87=>'3.8.1', 88=>'3.8.2', 89=>'3.8.3', 90=>'3.8.4', 91=>'3.8.5',
									92=>'3.9.0', 93=>'3.9.1', 94=>'3.9.2', 95=>'3.10.0', 96=>'3.10.1', 97=>'3.10.2', 98=>'3.10.3', 99=>'3.10.4', 100=>'3.10.5', 101=>'3.10.6', 102=>'3.10.7', 103=>'3.10.8', 104=>'3.10.9',
									105=>'3.11.0', 106=>'3.11.1', 107=>'3.11.2', 108=>'3.11.3', 109=>'3.11.4',
									110=>'3.12.0', 111=>'3.12.1', 112=>'3.12.2', 113=>'3.12.3',
									114=>'3.13.0', 115=>'3.13.1', 116=>'3.14.0', 117=>'3.14.1',
									118=>'3.15.0', 119=>'3.15.1', 120=>'3.15.2'
							);
			// ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** ******** //
			
			$i			=	array_search( $old, $versions );
			$i2			=	$i;
			$n			=	array_search( $new, $versions );
			if ( $i < 7 ) {		// ONLY < 2.0 GA
				$prefix	=	JFactory::getConfig()->get( 'dbprefix' );
				$tables	=	JCckDatabase::loadColumn( 'SHOW TABLES' );
				if ( count( $tables ) ) {
					foreach ( $tables as $table ) {
						if ( strpos( $table, $prefix.'cck_item_' ) !== false ) {
							$replace	=	str_replace( $prefix.'cck_item_', $prefix.'cck_store_item_', $table );
							if ( $replace ) {
								JCckDatabase::doQuery( 'ALTER TABLE '.$table.' RENAME '.$replace );
							}
						} elseif ( strpos( $table, $prefix.'cck_type_' ) !== false ) {
							$replace	=	str_replace( $prefix.'cck_type_', $prefix.'cck_store_form_', $table );
							if ( $replace ) {
								JCckDatabase::doQuery( 'ALTER TABLE '.$table.' RENAME '.$replace );
							}
						}
					}
				}
				
				$fields	=	JCckDatabase::loadObjectList( 'SELECT id, storage_table FROM #__cck_core_fields WHERE storage_table LIKE "#__cck_item_%"' );
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$replace	=	str_replace( '#__cck_item_', '#__cck_store_item_', $field->storage_table );
						JCckDatabase::doQuery( 'UPDATE #__cck_core_fields SET storage_table = "'.$replace.'" WHERE id = '.(int)$field->id );
					}
				}
				$fields	=	JCckDatabase::loadObjectList( 'SELECT id, storage_table FROM #__cck_core_fields WHERE storage_table LIKE "#__cck_type_%"' );
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$replace	=	str_replace( '#__cck_type_', '#__cck_store_form_', $field->storage_table );
						JCckDatabase::doQuery( 'UPDATE #__cck_core_fields SET storage_table = "'.$replace.'" WHERE id = '.(int)$field->id );
					}
				}
				$fields	=	JCckDatabase::loadObjectList( 'SELECT id, options2 FROM #__cck_core_fields WHERE type = "select_dynamic"' );
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$options2		=	$field->options2;
						if ( strpos( $options2, '#__cck_item_' ) !== false ) {
							$options2	=	str_replace( '#__cck_item_', '#__cck_store_item_', $options2 );
						}
						if ( strpos( $options2, '#__cck_type_' ) !== false ) {
							$options2	=	str_replace( '#__cck_type_', '#__cck_store_form_', $options2 );
						}
						if ( $options2 != $field->options2 ) {
							JCckDatabase::doQuery( 'UPDATE #__cck_core_fields SET options2 = "'.$db->escape( $options2 ).'" WHERE id = '.(int)$field->id );
						}
					}
				}
			}
			
			if ( $i < 23 ) {	// ONLY < 2.4.5
				JCckDatabase::doQuery( 'ALTER TABLE #__cck_core_folders ADD path VARCHAR( 1024 ) NOT NULL AFTER parent_id;' );
				require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_folder.php';
				$folders	=	JCckDatabase::loadColumn( 'SELECT id FROM #__cck_core_folders WHERE lft ORDER BY lft' );
				foreach ( $folders as $f ) {
					$path	=	Helper_Folder::getPath( $f, '/' );
					JCckDatabase::doQuery( 'UPDATE #__cck_core_folders SET path = "'.$path.'" WHERE id = '.(int)$f );
				}
				if ( JCckDatabase::doQuery( 'INSERT IGNORE #__cck_core_folders (id) VALUES (29)' ) ) {
					require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/tables/folder.php';
					$folder			=	JTable::getInstance( 'Folder', 'CCK_Table' );
					$folder->load( 29 );
					$folder_data	=	array( 'parent_id'=>13, 'path'=>'joomla/user/profile', 'title'=>'Profile', 'name'=>'profile', 'color'=>'#0090d1',
											   'introchar'=>'U.', 'colorchar'=>'#ffffff', 'elements'=>'field', 'featured'=>0, 'published'=>1 );
					$rules	=	new JAccessRules( '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}' );
					$folder->setRules( $rules );
					$folder->bind( $folder_data );
					$folder->store();
				}
			}
			
			for ( $i = $i + 1; $i <= $n; $i++ ) {
				$file		=	$root.'/install/upgrades/'.strtolower( $versions[$i] ).'.sql';
				if ( JFile::exists( $file ) ) {
					$buffer		=	file_get_contents( $file );
					$queries	=	JInstallerHelper::splitSql( $buffer );
					foreach ( $queries as $query ) {
						$query	=	trim( $query );
						if ( $query != '' && $query{0} != '#' ) {
							$db->setQuery( $query );
							$db->execute();
						}
					}
				}
			}
			
			if ( $i2 < 23 ) {	// ONLY < 2.4.5
				$bool	=	true;
				$live	=	JCckDatabase::loadObjectList( 'SELECT typeid, fieldid, client, live, live_value FROM #__cck_core_type_field WHERE live IN ("url_var_int","url_var_string","user_profile")' );
				if ( count( $live ) ) {
					foreach ( $live as $l ) {
						if ( $l->live == 'user_profile' ) {
							$live_type		=	'joomla_user';
							$live_options	=	'{"content":"","property":"'.$l->live_value.'"}';
						} elseif ( $l->live == 'url_var_int' ) {
							$live_type		=	'url_variable';
							$live_options	=	'{"variable":"'.$l->live_value.'","type":"int"}';
						} elseif ( $l->live == 'url_var_string' ) {
							$live_type		=	'url_variable';
							$live_options	=	'{"variable":"'.$l->live_value.'","type":"string"}';
						}
						if ( !JCckDatabase::doQuery( 'UPDATE #__cck_core_type_field SET live = "'.$live_type.'", live_options = "'.$db->escape( $live_options ).'" WHERE typeid = '.$l->typeid.' AND fieldid = '.$l->fieldid.' AND client = "'.$l->client.'"' ) ) {
							$bool	=	false;
						}
					}
				}
				$live	=	JCckDatabase::loadObjectList( 'SELECT searchid, fieldid, client, live, live_value FROM #__cck_core_search_field WHERE live IN ("url_var_int","url_var_string","user_profile")' );
				if ( count( $live ) ) {
					foreach ( $live as $l ) {
						if ( $l->live == 'user_profile' ) {
							$live_type		=	'joomla_user';
							$live_options	=	'{"content":"","property":"'.$l->live_value.'"}';
						} elseif ( $l->live == 'url_var_int' ) {
							$live_type		=	'url_variable';
							$live_options	=	'{"variable":"'.$l->live_value.'","type":"int"}';
						} elseif ( $l->live == 'url_var_string' ) {
							$live_type		=	'url_variable';
							$live_options	=	'{"variable":"'.$l->live_value.'","type":"string"}';
						}
						if ( !JCckDatabase::doQuery( 'UPDATE #__cck_core_search_field SET live = "'.$live_type.'", live_options = "'.$db->escape( $live_options ).'" WHERE searchid = '.$l->searchid.' AND fieldid = '.$l->fieldid.' AND client = "'.$l->client.'"' ) ) {
							$bool	=	false;
						}
					}
				}
				if ( $bool ) {
					JCckDatabase::doQuery( 'UPDATE #__extensions SET enabled = 0 WHERE element IN ("url_var_int","url_var_string","user_profile") AND folder = "cck_field_live"' );
				}
			}
			if ( $i2 < 25 ) {
				$table	=	JTable::getInstance( 'Asset' );
				$table->loadByName( 'com_cck' );
				if ( $table->rules ) {
					$rules	=	(array)json_decode( $table->rules );
					$rules['core.delete.own']	=	array( 6=>"1" );
					$table->rules	=	json_encode( $rules );
					$table->store();
				}
			}
			if ( $i2 < 33 ) {
				$folders	=	array( 10, 11, 12, 13, 14 );
				foreach ( $folders as $folder ) {
					Helper_Folder::rebuildBranch( $folder );
				}
			}
			
			if ( $i2 < 35 ) {
				$objects	=	array(
									'joomla_article'=>'article',
									'joomla_category'=>'category',
									'joomla_user'=>'user',
									'joomla_user_group'=>'user_group',
								);
				foreach ( $objects as $k=>$v ) {
					$params	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_objects WHERE name = "'.$k.'"' );
					$params	=	json_decode( $params );
					$params->default_type	=	JCck::getConfig_Param( 'integration_'.$v, '' );
					$params->add_redirect	=	( $params->default_type != '' ) ? '1' : '0';
					$params->edit			=	JCck::getConfig_Param( 'integration_'.$v.'_edit', '0' );
					if ( $k == 'joomla_category' ) {
						$params->exclude	=	JCck::getConfig_Param( 'integration_'.$v.'_exclude', '' );
					}
					JCckDatabase::doQuery( 'UPDATE #__cck_core_objects SET options = "'.$db->escape( json_encode( $params ) ).'" WHERE name = "'.$k.'"' );
				}
			}
			
			if ( $i2 < 45 ) {
				$table		=	'#__cck_store_item_users';
				$columns	=	$db->getTableColumns( $table );
				if ( isset( $columns['password2'] ) ) {
					JCckDatabase::doQuery( 'ALTER TABLE '.JCckDatabase::quoteName( $table ).' DROP '.JCckDatabase::quoteName( 'password2' ) );
				}
			}
			
			if ( $i2 < 66 ) {
				$path	=	JPATH_ADMINISTRATOR.'/components/com_cck/download.php';
				if ( JFile::exists( $path ) ) {
					JFile::delete( $path );
				}
			}
			
			if ( $i2 < 70 ) {
				$plg_image	=	JPluginHelper::getPlugin( 'cck_field', 'upload_image' );
				$plg_params	=	new JRegistry( $plg_image->params );

				$com_cck	=	JComponentHelper::getComponent( 'com_cck' );
				$com_cck->params->set( 'media_quality_jpeg', $plg_params->get( 'quality_jpeg', '90' ) );
				$com_cck->params->set( 'media_quality_png', $plg_params->get( 'quality_png', '3' ) );
				
				JCckDatabase::doQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $com_cck->params->toString() ).'" WHERE type = "component" AND element = "com_cck"' );
			}
			
			if ( $i2 < 105 ) {
				$config		=	JFactory::getConfig();
				$tmp_path	=	$config->get( 'tmp_path' );

				if ( is_file( JPATH_SITE.'/components/com_cck/models/box.php' ) ) {
					JFile::delete( JPATH_SITE.'/components/com_cck/models/box.php', $tmp_path.'/box.php' );
				}
				if ( is_dir( JPATH_SITE.'/components/com_cck/views/box' ) ) {
					JFolder::delete( JPATH_SITE.'/components/com_cck/views/box', $tmp_path.'/box' );
				}
			}

			// Convert Tables To Utf8mb4
			self::_convertTablesToUtf8mb4();

			// Rebuild Folder Tree
			Helper_Folder::rebuildTree( 2, 1 );
		}
		
		// Force Auto Increments
		self::_forceAutoIncrements();
	}
	
	// _addAddon
	protected function _addAddon( $addon, $parent, $type )
	{
		$db		=	JFactory::getDbo();
		$name	=	str_replace( 'com_cck_', '', $addon->element );

		// -- Dirty workaround cleanup
		if ( $type == 'update' && version_compare( JFactory::getApplication()->cck_core_version_old, '3.11.4', '<' ) && $name != '' ) {
			$db->setQuery( 'DELETE FROM #__menu WHERE link = "index.php?option=com_cck_'.$name.'" AND parent_id IN (0,1)' );
			$db->execute();
		}
		
		$table	=	JTable::getInstance( 'Menu' );
		$data	=	array( 'menutype'=>'main', 'title'=>$addon->element.'_title', 'alias'=>$addon->title, 'path'=>'SEBLOD/'.$addon->title,
						   'link'=>'index.php?option=com_cck_'.$name, 'type'=>'component', 'published'=>1, 'parent_id'=>$parent->id,
						   'level'=>2, 'component_id'=>$addon->id, 'access'=>1, 'img'=>'class:component', 'client_id'=>1 );
		
		$table->setLocation( $data['parent_id'], 'last-child' );
		$table->bind( $data );
		$table->check();
		$table->alias	=	$addon->title;
		$table->path	=	'SEBLOD/'.$addon->title;
		$table->store();
		$table->rebuildPath( $table->id );
		$db->setQuery( 'UPDATE #__menu SET alias = "'.$addon->title.'", path = "SEBLOD/'.$addon->title.'" WHERE id = '.(int)$table->id. ' AND client_id = 1' );
		$db->execute();
	}

	// _convertTablesToUtf8mb4
	protected function _convertTablesToUtf8mb4()
	{
		$app		=	JFactory::getApplication();
		$db			=	JFactory::getDbo();
		$name		=	$db->getName();
		$params		=	JComponentHelper::getParams( 'com_cck' );
		$status		=	(int)$params->get( 'utf8_conversion', '' );
		$utf8mb4	=	false;

		if ( stristr( $name, 'mysql' ) === false ) {
			return;
		}

		if ( !JCck::on( '3.5 ' ) ) {
		    return;
		}

		if ( $status > 0 ) {
		    return;
		}

		if ( JCck::on( '3.5' ) ) {
			$utf8mb4	=	$db->hasUTF8mb4Support();
		}

		$i			=	0;
		$prefix		=	JFactory::getConfig()->get( 'dbprefix' );
		$tables		=	$db->getTableList();

		if ( count( $tables ) ) {
		    foreach ( $tables as $name ) {
				$continue	=	false;
				$pos		=	strpos( $name, $prefix.'cck_' );

		        if ( !( $pos !== false && $pos == 0 ) ) {
					continue;
				}
				$columns	=	$db->getTableColumns( $name, false );

				if ( count( $columns ) ) {
					foreach ( $columns as $column ) {
					    if ( isset( $column->Collation ) && $column->Collation ) {
							$collations	=	explode( '_', $column->Collation );
							$charset	=	@$collations[0];

							// Convert only if utf8
							if ( $charset ) {
								$charset = strtolower( $charset );

								if ( $charset !== 'utf8' && $charset !== 'utf8mb4' ) {
									$continue	=	true;
									break;
								}
							}

							// Convert only if indexes allow it
							if ( isset( $column->Key ) && $column->Key ) {
								$type		=	'';

							    if ( isset( $column->Type ) && $column->Type ) {
									$type	=	$column->Type;
								}
							    if ( $type != '' ) {
									$types	=	explode( '(', $type );
									$type	=	@$types[1];
									
									if ( $type ) {
										$len	=	strlen( $type );

										if ( $type[$len - 1] == ')' ) {
											$type	=	substr( $type, 0, -1 );
										}
										if ( $type ) {
											if ( (int)$type > 191 ) {
												$continue	=	true;
												break;    
											}
										}
									}
								}
							}
						}
					}
				}
		        if ( $continue ) {
					$app->enqueueMessage( '<strong>'.$name.'</strong> not converted to utf8mb4. Please check this table manually.', 'error' );
					continue;
				}
				$query	=	'ALTER TABLE `'.$name.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
				$query2	=	'ALTER TABLE `'.$name.'` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';

		        if ( !$utf8mb4 ) {
					$query	=	$db->convertUtf8mb4QueryToUtf8( $query );
					$query2	=	$db->convertUtf8mb4QueryToUtf8( $query2 );
				}
				$db->setQuery( $query );
				$db->execute();
				$db->setQuery( $query2 );
				$db->execute();

				$i++;
			}
			$message	=	( $utf8mb4 ) ? 'utf8mb4_unicode_ci' : 'utf8_unicode_ci (as utf8mb4_unicode_ci is not supported)';
			$message	=	'<strong>'.$i.' tables</strong> converted to '.$message.'.';
			
			$app->enqueueMessage( $message );

			$params->set( 'utf8_conversion', ( $utf8mb4 ? '2' : '1' ) );
			$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE name = "com_cck"' );
			$db->execute();
		}
	}

	// _forceAutoIncrements
	protected function _forceAutoIncrements()
	{
		$tables =	array(
						'#__cck_core_fields'=>5000,
						'#__cck_core_folders'=>500,
						'#__cck_core_searchs'=>500,
						'#__cck_core_sites'=>500,
						'#__cck_core_templates'=>500,
						'#__cck_core_types'=>500,
						'#__cck_core_versions'=>500,
						'#__cck_more_jobs'=>500,
						'#__cck_more_processings'=>500,
						'#__cck_more_sessions'=>500
					);

		if ( count( $tables ) ) {
			foreach ( $tables as $name=>$auto_inc ) {
				$max	=	(int)JCckDatabase::loadResult( 'SELECT MAX(id) FROM '.$name );

				if ( $max < $auto_inc ) {
					// Add temp entry
					$table	=	JCckTable::getInstance( $name );

					if ( $table->load( $auto_inc, true ) ) {
						if ( property_exists( $table, 'published' ) ) {
							$table->published   =   -44;
							$table->store();
						}
					}
				} elseif ( $max > $auto_inc ) {
					// Remove temp entry (id = $auto_inc && published = -44 && title == '')
					$table	=	JCckTable::getInstance( $name );
					$table->load( $auto_inc );

					if ( is_object( $table ) && $table->id > 0 ) {
						if ( isset( $table->published ) && $table->published == -44 ) {
							if ( ( isset( $table->title ) && $table->title == '' )
							  || ( isset( $table->e_title ) && $table->e_title == '' ) ) {
								$table->delete( $auto_inc );
							}
						}
					}
				}
			}
		}
	}

	// _setUtf8mb4
	protected function _setUtf8mb4( $params )
	{
		$db			=	JFactory::getDbo();
		$name		=	$db->getName();
		$status		=	(int)$params->get( 'utf8_conversion', '' );
		$utf8mb4	=	false;

		if ( stristr( $name, 'mysql' ) === false ) {
			return;
		}

		if ( !JCck::on( '3.5 ' ) ) {
		    return;
		}

		if ( $status > 0 ) {
		    return;
		}

		if ( JCck::on( '3.5' ) ) {
			$utf8mb4	=	$db->hasUTF8mb4Support();
		}

		$params->set( 'utf8_conversion', ( $utf8mb4 ? '2' : '1' ) );
		$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params ).'" WHERE name = "com_cck"' );
		$db->execute();
	}
}
?>