<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_quickicon.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once dirname( __FILE__ ).'/helper.php';
$buttons	=	modCCKQuickIconHelper::getButtons();

JHtml::_( 'stylesheet', 'administrator/components/com_cck/assets/css/font.css', array(), false );

require JModuleHelper::getLayoutPath( 'mod_cck_quickicon', $params->get( 'layout', 'default' ) );
?>