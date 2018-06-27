<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JFactory::getLanguage()->load( 'com_cck_default', JPATH_SITE );

$controller	=	JControllerLegacy::getInstance( 'CCK' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
?>