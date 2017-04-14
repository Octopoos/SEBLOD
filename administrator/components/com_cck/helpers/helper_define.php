<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_define.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( is_file( JPATH_COMPONENT.'/_VERSION.php' ) ) {
	require_once JPATH_COMPONENT.'/_VERSION.php';
	$version	=	new JCckVersion;
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Core

define( 'CCK_VERSION', 			( isset( $version ) && is_object( $version ) ) ? $version->getFullVersion() : '3.x' );
define( 'CCK_NAME',				'cck' );
define( 'CCK_TITLE',			'CCK' );
define( 'CCK_COM',				'com_'.CCK_NAME );
define( 'CCK_MODEL',			CCK_TITLE.'Model' );
define( 'CCK_TABLE',			CCK_NAME.'_Table' );
define( 'CCK_WEBSITE',			'https://www.seblod.com' );

define( '_C0_NAME',				'folders' );
define( '_C1_NAME',				'templates' );
define( '_C2_NAME',				'types' );
define( '_C3_NAME',				'fields' );
define( '_C4_NAME',				'searchs' );
define( '_C5_NAME',				'sites' );
define( '_C6_NAME',				'versions' );
define( '_C7_NAME',				'variations' );
define( '_C8_NAME',				'sessions' );

define( '_C1_TEXT',				'COM_CCK_TEMPLATE' );
define( '_C3_TEXT',				'COM_CCK_FIELD' );
define( '_C5_TEXT',				'COM_CCK_SITE' );
define( '_C6_TEXT',				'COM_CCK_VERSION' );
define( '_C7_TEXT',				'COM_CCK_VARIATION' );
define( '_C8_TEXT',				'COM_CCK_SESSION' );

define( 'CCK_LINK',				'index.php?option=com_'.CCK_NAME );
define( '_C0_LINK',				CCK_LINK.'&view='._C0_NAME );
define( '_C1_LINK',				CCK_LINK.'&view='._C1_NAME );
define( '_C2_LINK',				CCK_LINK.'&view='._C2_NAME );
define( '_C3_LINK',				CCK_LINK.'&view='._C3_NAME );
define( '_C4_LINK',				CCK_LINK.'&view='._C4_NAME );
define( '_C5_LINK',				CCK_LINK.'&view='._C5_NAME );
define( '_C6_LINK',				CCK_LINK.'&view='._C6_NAME );
define( '_C7_LINK',				CCK_LINK.'&view='._C7_NAME );
define( '_C8_LINK',				CCK_LINK.'&view='._C8_NAME );

define( '_NBSP', 				str_repeat( '&nbsp;', 3 ) );
define( '_NBSP2', 				str_repeat( '&nbsp;', 5 ) );
?>