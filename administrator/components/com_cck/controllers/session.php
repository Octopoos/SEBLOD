<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: version.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerSession extends JControllerForm
{
	protected $text_prefix	=	'COM_CCK';
	
	// getRedirectToListAppend
	protected function getRedirectToItemAppend( $recordId = null, $urlVar = 'id' )
	{
		$app		=	JFactory::getApplication();
		$tmpl		=	$app->input->get( 'tmpl' );
		$layout		=	$app->input->get( 'layout', 'edit', 'string' );
		$extension	=	$app->input->get( 'extension', '' );
		$append		=	'';

		// Setup redirect info.
		if ( $tmpl ) {
			$append	.=	'&tmpl=' . $tmpl;
		}
		if ( $layout ) {
			$append	.=	'&layout=' . $layout;
		}
		if ( $recordId ) {
			$append	.=	'&' . $urlVar . '=' . $recordId;
		}
		if ( $extension ) {
			$append	.=	'&extension=' . $extension;
		}
		
		return $append;
	}

	// getRedirectToListAppend
	protected function getRedirectToListAppend()
	{
		$app		=	JFactory::getApplication();
		$tmpl		=	$app->input->get( 'tmpl', '' );
		$extension	=	$app->input->get( 'extension', '' );
		$append		=	'';

		// Setup redirect info.
		if ( $tmpl ) {
			$append	.=	'&tmpl=' . $tmpl;
		}
		if ( $extension ) {
			$append	.=	'&extension=' . $extension;
		}
		
		return $append;
	}
}
?>