<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$dir	=	( $alignment ) ? 'pull-'.$alignment : ( ( $doc->direction == 'rtl' ) ? 'pull-right' : '' );
$class	=	( $enabled ) ? 'nav '.$dir : 'nav disabled '.$dir;

require JModuleHelper::getLayoutPath( 'mod_cck_menu', $enabled ? 'default_enabled' : 'default_disabled' );

$menu->renderMenu( 'menu', $class );
?>