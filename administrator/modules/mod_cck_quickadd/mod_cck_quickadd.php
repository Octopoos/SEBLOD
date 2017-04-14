<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_quickadd.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$modal_layout	=	$params->get( 'modal_box_layout', 'icon' );

require JModuleHelper::getLayoutPath( 'mod_cck_quickadd', $params->get( 'layout', 'default' ) );
?>