<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
		if ( (float)$app->cck_core_version_old < 3.2 ) {
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
					JFactory::getApplication()->enqueueMessage( 'Patch IDs failed.. while updating to SEBLOD 3.2' );
				}
			}
		}
		// -- End
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$version	=	new JVersion;
		
		if ( version_compare( $version->getShortVersion(), '2.5.0', 'lt' ) ) {
			Jerror::raiseWarning( null, 'This package IS NOT meant to be used on Joomla! 1.7. You should upgrade your site with Joomla 2.5 first, and then install it again !' );
			return false;
		}
		
		$app		=	JFactory::getApplication();
		$lang		=	JFactory::getLanguage();
		
		$app->cck_core				=	true;
		$app->cck_core_version_old	=	self::_getVersion();
		
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
			$rule	=	'{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":[],"core.delete":[],"core.delete.own":{"6":1},"core.edit":[],"core.edit.state":[],"core.edit.own":[],"core.export":{"7":1},"core.process":{"7":1}}';
			$query	=	'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE name = "com_cck"';
			$db->setQuery( $query );
			$db->execute();
		}
		
		// CMS Autoloader
		$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/cms';
		if ( JFolder::exists( $src ) ) {
			JFolder::copy( $src, JPATH_SITE.'/libraries/cms/cck', '', true );
		}
		if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
			jimport( 'cck.base.cck_5_2' );
			$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/src/php5.2/libraries/cms/cck/cck.php';
			if ( JFile::exists( $src ) ) {
				JFile::copy( $src, JPATH_SITE.'/libraries/cms/cck/cck.php' );
			}
		}

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
		$query	=	'SELECT extension_id FROM  #__extensions WHERE type = "component" AND element = "'.$title.'"';

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