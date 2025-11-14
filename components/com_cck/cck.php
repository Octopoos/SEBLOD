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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

$lang	=	Factory::getLanguage();
$lang_default	=	$lang->setDefault( 'en-GB' );
$lang->load( 'com_cck' );
$lang->load( 'com_cck_default', JPATH_SITE );
$lang->setDefault( $lang_default );

$controller	=	BaseController::getInstance( 'CCK' );
$controller->execute( Factory::getApplication()->input->get( 'task' ) );
$controller->redirect();
?>