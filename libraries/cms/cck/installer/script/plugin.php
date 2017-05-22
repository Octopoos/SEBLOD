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

jimport( 'cck.base.install.install' );

// Script
class JCckInstallerScriptPlugin
{
	protected $cck;
	protected $core;
	
	// install
	function install( $parent )
	{
		if ( isset( $this->cck ) && is_object( $this->cck ) ) {
			self::_setIntegration();
		}
		if ( $this->core === true ) {
			return;
		}

		$db		=	JFactory::getDbo();
		$where	=	'WHERE type = "'.$this->cck->type.'" AND element = "'.$this->cck->element.'"';
		if ( $this->cck->group ) {
			$where	.=	' AND folder = "'.$this->cck->group.'"';
		}

		// Publish
		$query	=	'UPDATE #__extensions SET enabled = 1 '.$where;
		$db->setQuery( $query );
		$db->query();

		// Post Install Log
		self::postInstallMessage( 'install' );
	}
	
	// uninstall
	function uninstall( $parent )
	{	
		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();
		$where	=	'WHERE type = "'.$this->cck->type.'" AND element = "'.$this->cck->element.'"';
		if ( $this->cck->group ) {
			$where	.=	' AND folder = "'.$this->cck->group.'"';
		}

		// Integration
		if ( $this->cck->group == 'cck_storage_location' && !isset( $app->cck_nosql ) ) {
			$db->setQuery( 'DELETE FROM #__cck_core_objects WHERE name = "'.$this->cck->element.'"' );
			$db->query();
		}

		if ( $this->core === true ) {
			return;
		}

		// Post Install Log
		self::postInstallMessage( 'uninstall' );
	}
	
	// update
	function update( $parent )
	{
		if ( $this->core === true ) {
			return;
		}
		
		// Post Install Log
		self::postInstallMessage( 'update' );

		// Integration
		self::_setIntegration();
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$app		=	JFactory::getApplication();
		$this->core	=	( isset( $app->cck_core ) ) ? $app->cck_core : false;
		
		if ( $this->core === true ) {
			if ( (string)$parent->getParent()->getManifest()->attributes()->type == 'plugin'
			  && (string)$parent->getParent()->getManifest()->attributes()->group == 'cck_storage_location' ) {
				
				$core_objects	=	array(
										'plg_cck_storage_location_free'=>'',
										'plg_cck_storage_location_joomla_article'=>'',
										'plg_cck_storage_location_joomla_category'=>'',
										'plg_cck_storage_location_joomla_user'=>'',
										'plg_cck_storage_location_joomla_user_group'=>''
									);
				$name	=	(string)$parent->getParent()->getManifest()->name;

				if ( isset( $core_objects[$name] ) ) {
					return;
				}
			} else {
				return;
			}
		}
		$this->cck	=	CCK_Install::init( $parent );
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		if ( $this->core === true ) {
			return;
		}
		CCK_Install::import( $parent, 'install', $this->cck );
	}

	// postInstallMessage
	function postInstallMessage( $event, $pk = 0 )
	{
		if ( !version_compare( JVERSION, '3.2', 'ge' ) ) {
			return;
		}
		if ( !$pk ) {
			$db		=	JFactory::getDbo();
			$query	=	'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_cck"';

			$db->setQuery( $query );
			$pk		=	$db->loadResult();
			if ( !$pk ) {
				return false;
			}
		}

		$lang		=	JFactory::getLanguage();
		$title		=	(string)$this->cck->xml->name;
		$lang->load( $title );
		$lang->load( 'lib_cck', JPATH_SITE, 'en-GB', true );
		$title		=	JText::_( $title );
		if ( isset( $this->cck->xml->version ) ) {
			$title	=	str_replace( ' for SEBLOD', '', $title ).' '.(string)$this->cck->xml->version;
		}
		$user		=	JFactory::getUser();
		$user_name	=	'<a href="index.php?option=com_cck&view=form&return_o=users&return_v=users&type=user&id='.$user->id.'" target="_blank">'.$user->name.'</a>';
		$version	=	'3.2.0';
		jimport( 'joomla.filesystem.file' );
		if ( JFile::exists( JPATH_ADMINISTRATOR.'/components/com_cck/_VERSION.php' ) ) {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/_VERSION.php';
			if ( class_exists( 'JCckVersion' ) ) {
				$version	=	new JCckVersion;
				$version	=	$version->getShortVersion();
			} else {
				$version	=	file_get_contents( JPATH_ADMINISTRATOR.'/components/com_cck/_VERSION.php' );
			}
		}
		
		require_once JPATH_SITE.'/libraries/cms/cck/cck.php';			
		require_once JPATH_SITE.'/libraries/cms/cck/database.php';
		require_once JPATH_SITE.'/libraries/cms/cck/table.php';
		$table						=	JCckTable::getInstance( '#__postinstall_messages' );
		$table->extension_id		=	$pk;
		$table->title_key			=	$title;
		$table->description_key		=	JText::sprintf( 'LIB_CCK_POSTINSTALL_'.strtoupper( $event ).'_DESCRIPTION', $user_name, JFactory::getDate()->format( JText::_( 'DATE_FORMAT_LC2' ) ) );
		$table->language_extension	=	'lib_cck';
		$table->type				=	'message';
		$table->version_introduced	=	$version;
		$table->store();

		return true;
	}

	// _setIntegration
	function _setIntegration()
	{
		if ( $this->cck->group == 'cck_storage_location' ) {
			if ( isset( $this->cck->xml->cck_integration ) ) {
				JFactory::getLanguage()->load( 'plg_cck_storage_location_'.$this->cck->element, JPATH_ADMINISTRATOR, 'en-GB', true );

				$db				=	JFactory::getDbo();				
				$integration	=	array( 'component', 'context', 'options', 'vars', 'view' );
				$title			=	JText::_( 'PLG_CCK_STORAGE_LOCATION_'.$this->cck->element.'_LABEL2' );

				foreach ( $integration as $i=>$elem ) {
					if ( isset( $this->cck->xml->cck_integration->$elem ) ) {
						$integration[$elem]	=	(string)$this->cck->xml->cck_integration->$elem;
						unset( $integration[$i] );
					}
				}
				if ( $id = JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_objects WHERE name = "'.$this->cck->element.'"' ) ) {
					$query		=	'UPDATE #__cck_core_objects SET `component` = "'.$integration['component'].'", `context` = "'.$integration['context'].'", `vars` = "'.$integration['vars'].'", `view` = "'.$integration['view'].'" WHERE id = '.(int)$id;
					$db->setQuery( $query );
					$db->query();
				} else {
					$query		=	'INSERT IGNORE INTO #__cck_core_objects (`title`,`name`,`component`,`context`,`options`,`vars`,`view`)'
								.	' VALUES ("'.$title.'", "'.$this->cck->element.'", "'.$integration['component'].'", "'.$integration['context'].'", "'.$db->escape( $integration['options'] ).'", "'.$integration['vars'].'", "'.$integration['view'].'")';
					$db->setQuery( $query );
					$db->query();
				}
			}
		}
	}
}
?>