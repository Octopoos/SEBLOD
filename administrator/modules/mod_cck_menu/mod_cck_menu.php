<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_menu.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$roots	=	array( 0=>'',
				   1=>JText::_( 'MOD_CCK_MENU_CONSTRUCTION' ),
				   2=>JText::_( 'MOD_CCK_MENU_CONSTRUCTION' ),
				   3=>JText::_( 'MOD_CCK_MENU_ECOMMERCE' ),
				   4=>JText::_( 'MOD_CCK_MENU_FORMS' ),
				   5=>JText::_( 'MOD_CCK_MENU_LISTS' ),
				   6=>JText::_( 'MOD_CCK_MENU_ADDONS' )
			);
if ( !class_exists( 'modCckMenuHelper' ) ) {
	require __DIR__ . '/helper.php';
}

if ( JCck::on( '3.8' ) ) {
	require_once JPATH_ADMINISTRATOR . '/modules/mod_cck_menu/cck_menu_legacy.php';

	$menu		=	new JAdminCssCckLegacyMenu;
} else {
	if ( !class_exists( 'JAdminCssMenu' ) ) {
		require JPATH_ADMINISTRATOR . '/modules/mod_menu/menu.php';
	}
	
	$menu		=	new JAdminCSSMenu;
}

$app		=	JFactory::getApplication();
$doc		=	JFactory::getDocument();
$lang		=	JFactory::getLanguage();
$user		=	JFactory::getUser();

$alignment	=	$params->get( 'alignment', '' );
$enabled	=	$app->input->getBool( 'hidemainmenu' ) ? false : true;
$mode		=	$params->get( 'mode', 2 );
$options	=	array( 'new'=>$params->get( 'cck_new', 0 ),
					   'ecommerce'=>$params->get( 'cck_ecommerce', 1 ),
					   'inline'=>$params->get( 'cck_inline', 0 ) );
$root		=	trim( $params->get( 'menutitle' ) );
if ( !$root ) {
	$root	=	$roots[$mode];
}
require JModuleHelper::getLayoutPath( 'mod_cck_menu', $params->get( 'layout', 'default' ) );