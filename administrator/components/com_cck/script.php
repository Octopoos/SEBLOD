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

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

// Script
class com_cckInstallerScript
{
	// install
	function install( $parent )
	{
	}
	
	// uninstall
	function uninstall( $parent )
	{
		// Post Install Log
		self::_postInstallMessage( 'uninstall', $parent );

		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();
		$db->setQuery( 'SELECT extension_id FROM #__extensions WHERE type = "package" AND element = "pkg_cck"' );
		$eid	=	$db->loadResult();
		
		$db->setQuery( 'SELECT extension_id FROM #__extensions WHERE type = "plugin" AND element = "cck" AND folder="system"' );
		$cck	=	$db->loadResult();
		
		// Backup or Drop SQL Tables
		$prefix			=	$db->getPrefix();
		$tables			=	$db->getTableList();
		$tables			=	array_flip( $tables );
		$uninstall_sql	=	(int)JCck::getConfig_Param( 'uninstall_sql', '' );

		if ( count( $tables ) ) {
			$length			=	strlen( $prefix );
			$app->cck_nosql	=	true;
			
			foreach ( $tables as $k=>$v ) {
				$pos		=	strpos( $k, $prefix.'cck_' );

				if ( $pos !== false && $pos == 0 ) {
					$k2		=	$prefix.'_'.substr( $k, $length );

					if ( isset( $tables[$k2] ) ) {
						$db->setQuery( 'DROP TABLE '.$k2 );
						$db->execute();
					}
					if ( $uninstall_sql == 1 ) {
						$db->setQuery( 'DROP TABLE '.$k );
						$db->execute();
					} else {
						$db->setQuery( 'RENAME TABLE '.$k.' TO '.$k2 );
						$db->execute();
					}
				}
			}
		}

		// Uninstall FULL PACKAGE only if package exists && system plugin exists..
		if ( $eid && $cck ) {
			$manifest	=	JPATH_ADMINISTRATOR.'/manifests/packages/pkg_cck.xml';
			if ( JFile::exists( $manifest ) ) {
				$xml	=	JFactory::getXML( $manifest ); // Keep it this way until platform 13.x
			}
			if ( isset( $xml->files ) ) {
				unset( $xml->files->file[3] );
				$xml->asXML( $manifest );
			}
			
			jimport( 'joomla.installer.installer' );
			$installer	=	JInstaller::getInstance();
			$installer->uninstall( 'package', $eid );
		}
	}
	
	// update
	function update( $parent )
	{
		// Post Install Log
		self::_postInstallMessage( 'update', $parent );

		// WAITING FOR JOOMLA 1.7.x FIX
		$app		=	JFactory::getApplication();
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_var_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		$src		=	JPATH_SITE.'/libraries/cck/rendering/variations';
		if ( JFolder::exists( $src ) ) {
			JFolder::copy( $src, $path );
			$app->cck_core_temp_var	=	$tmp_dir;
		}
		// WAITING FOR JOOMLA 1.7.x FIX

		// -- Patch for websites started with SEBLOD 2.x 
		if ( version_compare( $app->cck_core_version_old, '3.2', '<' ) ) {
			$db		=	JFactory::getDbo();
			$db->setQuery( 'SELECT id FROM #__cck_core_fields WHERE id >= 500 AND id < 5000' );
			$fields	=	$db->loadObjectList();

			if ( count( $fields ) ) {
				$errors	=	0;
				foreach ( $fields as $f ) {
					$id		=	(int)$f->id;
					$id2	=	(int)'100'.$id;

					$query	=	'UPDATE #__cck_core_fields SET id = '.$id2.' WHERE id = '.(int)$id;
					$db->setQuery( $query );

					if ( $db->execute() !== false ) {
						$query	=	'UPDATE #__cck_core_type_field SET fieldid = '.$id2.' WHERE fieldid = '.$id;
						$db->setQuery( $query );
						$db->execute();

						$query	=	'UPDATE #__cck_core_search_field SET fieldid = '.$id2.' WHERE fieldid = '.$id;
						$db->setQuery( $query );
						$db->execute();
					} else {
						$errors++;
					}
				}
				if ( $errors ) {
					JFactory::getApplication()->enqueueMessage( 'Patch IDs failed.. while updating to SEBLOD 3.2.0' );
				}
			}
		}
		// -- End

		// -- Patch for websites started between SEBLOD 3.6.0+ and 3.8.0-
		if ( version_compare( $app->cck_core_version_old, '3.6', '>=' )
		  && version_compare( $app->cck_core_version_old, '3.8', '<' ) ) {
			$db			=	JFactory::getDbo();
			$db->setQuery( 'SELECT id, name FROM #__cck_core_fields WHERE id >= 533 AND id < 5000' );
			$fields		=	$db->loadObjectList();
			$ignore		=	array(
								'533'=>array( 'name'=>'core_session_extension', 'sql'=>"(533, 'Core Session Extension', 'core_session_extension', 3, 'select_simple', '', 0, 'Extension', 'Select', 3, 'required', '', '', 'Exporter=com_cck_exporter||Importer=com_cck_importer', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, 'max-width-150', '', 'dev', '', '', '', 'extension', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'534'=>array( 'name'=>'core_session_location_filter', 'sql'=>"(534, 'Core Session Location Filter', 'core_session_location_filter', 3, 'select_simple', '', 0, 'Location', ' ', 3, '', '', '', 'Title=title||Name=name||IDS=id', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'dev', '', '', '', 'filter_location', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'535'=>array( 'name'=>'tab2_details', 'sql'=>"(535, 'Tab2 Details (Start)', 'tab2_details', 3, 'tabs', '', 1, 'Details', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 0, 'cck_tabs2', '', '', '', 0, 1, 0, 0, 0, 0, 1, '', '', 'none', '', '', '', 'tab2_details', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'536'=>array( 'name'=>'tab2_publishing', 'sql'=>"(536, 'Tab2 Publishing (Panel)', 'tab2_publishing', 3, 'tabs', '', 1, 'Publishing', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 1, 'cck_tabs2', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', 'seb_session', '', '', 'tab2_publishing', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'537'=>array( 'name'=>'tab2_end', 'sql'=>"(537, 'Tab2 (End)', 'tab2_end', 3, 'tabs', '', 1, 'clear', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 2, 'cck_tabs2', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', 'seb_session', '', '', 'tab2_end', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'538'=>array( 'name'=>'tab2_metadata', 'sql'=>"(538, 'Tab2 Metadata (Panel)', 'tab2_metadata', 3, 'tabs', '', 1, 'Metadata', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 1, 'cck_tabs2', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', 'seb_session', '', '', 'tab2_metadata', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'539'=>array( 'name'=>'tab2_options', 'sql'=>"(539, 'Tab2 Options (Panel)', 'tab2_options', 3, 'tabs', '', 1, 'Options', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 1, 'cck_tabs2', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', 'seb_session', '', '', 'tab2_options', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'540'=>array( 'name'=>'tab2_media', 'sql'=>"(540, 'Tab2 Media (Panel)', 'tab2_media', 3, 'tabs', '', 1, 'Media', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 1, 'cck_tabs2', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', 'seb_session', '', '', 'tab2_media', '', '', '', 0, '0000-00-00 00:00:00')" ),
								'541'=>array( 'name'=>'tab2_permissions', 'sql'=>"(541, 'Tab2 Permissions (Panel)', 'tab2_permissions', 3, 'tabs', '', 1, 'Permissions', '', 3, '', '', '', '', '', 0, 255, 32, 0, 0, 0, 0, '', 1, 'cck_tabs2', '', '', '', 0, 0, 0, 0, 0, 0, 1, '', '', 'none', 'seb_session', '', '', 'tab2_permissions', '', '', '', 0, '0000-00-00 00:00:00')" )
							);
			$redo		=	array();
			if ( count( $fields ) ) {
				$errors	=	0;
				
				foreach ( $fields as $f ) {
					$id		=	(int)$f->id;
					$id2	=	(int)'110'.$id;

					if ( isset( $ignore[$f->id] ) && $ignore[$f->id]['name'] == $f->name ) {
						continue;
					}
					$query	=	'UPDATE #__cck_core_fields SET id = '.$id2.' WHERE id = '.(int)$id;
					$db->setQuery( $query );

					if ( $db->execute() !== false ) {
						$query	=	'UPDATE #__cck_core_type_field SET fieldid = '.$id2.' WHERE fieldid = '.$id;
						$db->setQuery( $query );
						$db->execute();

						$query	=	'UPDATE #__cck_core_search_field SET fieldid = '.$id2.' WHERE fieldid = '.$id;
						$db->setQuery( $query );
						$db->execute();

						$redo[(string)$f->id]	=	true;
					} else {
						$errors++;
					}
				}
				if ( ( $nb = count( $redo ) ) ) {
					if ( version_compare( $app->cck_core_version_old, '3.6.5', '>' ) ) {
						foreach ( $redo as $f_id=>$f_val ) {
							if ( $f_id == 533 || $f_id == 534 ) {
								$do	=	true;
							} elseif ( ( $f_id >= 535 && $f_id <= 541 ) && version_compare( $app->cck_core_version_old, '3.7.2', '>=' ) ) {
								$do	=	true;
							} else {
								$do	=	false;
							}
							if ( !$do ) {
								continue;
							}
							if ( isset( $ignore[$f_id] ) && $ignore[$f_id]['sql'] != '' ) {
								$query	=	'INSERT IGNORE INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_cck`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES '.$ignore[$f_id]['sql'];
								$db->setQuery( $query );
								$db->execute();
							}
						}
					}
					JFactory::getApplication()->enqueueMessage( '<strong>'.$nb.' field(s)</strong> patched (>= 533 and < 5000).' );
				}
				if ( $errors ) {
					JFactory::getApplication()->enqueueMessage( 'Patch IDs failed.. while updating to SEBLOD 3.8.0', 'error' );
				}
			}
		}
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$app		=	JFactory::getApplication();
		$lang		=	JFactory::getLanguage();
		
		$app->cck_core				=	true;
		$app->cck_core_version_old	=	self::_getVersion();

		// -- Dirty workaround (for websites with corrupted Menu Tree) cleaned on postflight
		if ( $type == 'update' && version_compare( $app->cck_core_version_old, '3.11.4', '<' ) ) {
			$db			=	JFactory::getDbo();
			$query		=	'SELECT b.id, b.lft, b.rgt'
						.	' FROM #__menu AS a'
						.	' LEFT JOIN #__menu AS b ON b.parent_id = a.id'
						.	' WHERE a.link = "index.php?option=com_cck" AND a.client_id = 1';
			$db->setQuery( $query );
			$items		=	$db->loadObjectList();

			if ( count( $items ) ) {
				foreach ( $items as $item ) {
					$db->setQuery( 'UPDATE #__menu SET parent_id = 1 AND level = 1 AND lft = 0 AND rgt = 0 WHERE id = '.(int)$item->id. ' AND client_id = 1' );
					$db->execute();
				}
			}

			$db->setQuery( 'UPDATE #__menu SET rgt = lft WHERE link = "index.php?option=com_cck" AND client_id = 1' );
			$db->execute();
		}

		set_time_limit( 0 );
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();
		
		$app->cck_core_version		=	self::_getVersion();
		
		if ( $type == 'update' ) {
			$params	=	JComponentHelper::getParams( 'com_cck' );
			$uix	=	$params->get( 'uix', '' );
			if ( $uix == 'nano' ) {
				$params->set( 'uix', '' );
				$db	=	JFactory::getDbo();
				$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params->toString() ).'" WHERE element = "com_cck"' );
				$db->execute();
			}
		} elseif ( 'install' ) {
			$rule	=	'{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":[],"core.delete":[],"core.delete.own":{"6":1},"core.edit":[],"core.edit.state":[],"core.edit.own":[],"core.addto.cart":{"7":1},"core.admin.form":{"7":1},"core.export":{"7":1},"core.process":{"7":1}}';
			$query	=	'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE name = "com_cck"';
			$db->setQuery( $query );
			$db->execute();
		}
		
		/* Todo: loop */
		$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/cli/cck_job.php';
		if ( JFile::exists( $src ) ) {
			JFile::delete( JPATH_SITE.'/cli/cck_job.php' );
			JFile::copy( $src, JPATH_SITE.'/cli/cck_job.php' );
		}

		$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/tmpl/raw.php';
		$dest	=	JPATH_ADMINISTRATOR.'/templates/'.$app->getTemplate().'/raw.php';
		if ( !JFile::exists( $dest ) ) {
			JFile::copy( $src, $dest );
		}
		$query	=	$db->getQuery( true );
		$query->select( $db->quoteName( array( 'template' ) ) )
			  ->from( $db->quoteName( '#__template_styles' ) )
			  ->where( $db->quoteName( 'client_id' ) . ' = 0' )
			  ->where( $db->quoteName( 'home' ) . ' = 1' );
		$db->setQuery( $query );
		
		if ( $site_template = $db->loadResult() ) {
			$dest	=	JPATH_SITE.'/templates/'.$site_template.'/raw.php';
			if ( !JFile::exists( $dest ) ) {
				JFile::copy( $src, $dest );
			}
		}

		$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/cms';
		if ( JFolder::exists( $src ) ) {
			JFolder::copy( $src, JPATH_SITE.'/libraries/cms/cck', '', true );
		}
		/* Todo: loop */
		
		if ( $type == 'install' ) {
			// Post Install Log
			self::_postInstallMessage( 'install', $parent );
		}
	}
	
	// _getVersion
	function _getVersion( $default = '2.0.0' )
	{
		$db		=	JFactory::getDbo();
		
		$db->setQuery( 'SELECT manifest_cache FROM #__extensions WHERE element = "com_cck" AND type = "component"' );
		
		$res		=	$db->loadResult();
		$registry	=	new JRegistry;
		$registry->loadString( $res );
		
		return $registry->get( 'version', $default );
	}

	// _postInstallMessage
	function _postInstallMessage( $event, $parent )
	{
		if ( !version_compare( JVERSION, '3.2', 'ge' ) ) {
			return;
		}
		$db		=	JFactory::getDbo();
		$title	=	'com_cck';
		$query	=	'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "'.$title.'"';

		$db->setQuery( $query );
		$pk		=	$db->loadResult();
		if ( !$pk ) {
			return false;
		}
		
		JFactory::getLanguage()->load( $title );
		$version	=	(string)$parent->getParent()->getManifest()->version;
		if ( $event == 'install' ) {
			$text	=	JText::_( 'LIB_CCK_POSTINSTALL_WELCOME_DESCRIPTION' );
		} else {
			$user		=	JFactory::getUser();
			$user_id	=	$user->id;
			$user_type	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location = "joomla_user" AND pk = '.$user_id );
			if ( $user_type ) {
				$user_link	=	'index.php?option=com_cck&view=form&return_o=users&return_v=users&type='.$user_type.'&id='.$user_id;
			} else {
				$user_link	=	'index.php?option=com_users&task=user.edit&id='.$user_id;
			}
			$user_name	=	'<a href="'.$user_link.'" target="_blank">'.$user->name.'</a>';
			$text		=	JText::sprintf( 'LIB_CCK_POSTINSTALL_'.strtoupper( $event ).'_DESCRIPTION', $user_name, JFactory::getDate()->format( JText::_( 'DATE_FORMAT_LC2' ) ) );
		}
		$title		=	'SEBLOD '.$version;
		
		require_once JPATH_SITE.'/libraries/cms/cck/cck.php';			
		require_once JPATH_SITE.'/libraries/cms/cck/database.php';
		require_once JPATH_SITE.'/libraries/cms/cck/table.php';
		$table						=	JCckTable::getInstance( '#__postinstall_messages' );
		$table->extension_id		=	$pk;
		$table->title_key			=	$title;
		$table->description_key		=	$text;
		$table->language_extension	=	'lib_cck';
		$table->type				=	'message';
		$table->version_introduced	=	$version;
		$table->store();

		return true;
	}
}
?>