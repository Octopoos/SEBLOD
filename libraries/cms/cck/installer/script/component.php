<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'cck.base.install.install' );

// Script
class JCckInstallerScriptComponent
{
	protected $cck;
	protected $core;
	
	// install
	public function install( $parent )
	{
		// Post Install Log
		self::postInstallMessage( 'install' );
	}
	
	// uninstall
	public function uninstall( $parent )
	{
		// Post Install Log
		self::postInstallMessage( 'uninstall' );
	}
	
	// update
	public function update( $parent )
	{
		// Post Install Log
		self::postInstallMessage( 'update' );
	}
	
	// preflight
	public function preflight( $type, $parent )
	{
		$this->cck	=	CCK_Install::init( $parent );
	}
	
	// postflight
	public function postflight( $type, $parent )
	{
	}

	// postInstallMessage
	protected function postInstallMessage( $event, $pk = 0 )
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
		
		if ( !( is_object( $this->cck ) && isset( $this->cck->element ) && $this->cck->element != '' ) ) {
			return;
		}

		$lang		=	JFactory::getLanguage();
		$title		=	(string)$this->cck->element;
		$lang->load( $title );
		$lang->load( 'lib_cck', JPATH_SITE, 'en-GB', true );
		$title		=	JText::_( $title.'_addon' );
		if ( isset( $this->cck->xml->version ) ) {
			$title	=	str_replace( ' for SEBLOD', '', $title ).' '.(string)$this->cck->xml->version;
		}
		$user		=	JFactory::getUser();
		$user_name	=	'<a href="index.php?option=com_cck&view=form&return_o=users&return_v=users&type=user&id='.$user->id.'" target="_blank" rel="noopener noreferrer">'.$user->name.'</a>';
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
}
?>