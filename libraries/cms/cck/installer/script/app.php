<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'cck.base.install.install' );

// Script
class JCckInstallerScriptApp
{
	protected $cck;
	protected $core;
	
	// install
	function install( $parent )
	{
	}
	
	// uninstall
	function uninstall( $parent )
	{
	}
	
	// update
	function update( $parent )
	{
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$app		=	JFactory::getApplication();
		$this->core	=	( isset( $app->cck_core ) ) ? $app->cck_core : false;
		if ( $this->core === true ) {
			return;
		}
		$this->cck			=	CCK_Install::init( $parent );
		$this->cck->isApp	=	true;
		if ( is_file( JPATH_ADMINISTRATOR.'/manifests/packages/'.$this->cck->xml->name.'.xml' ) ) {
			$this->cck->isUpgrade	=	true;
		} else {
			$this->cck->isUpgrade	=	false;
		}
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		if ( $this->core === true ) {
			return;
		}
		CCK_Install::import( $parent, 'elements', $this->cck );
	}
}
?>