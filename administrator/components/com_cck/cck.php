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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

$app	=	Factory::getApplication();
$task	=	$app->input->get( 'task' );
$view	=	$app->input->get( 'view' );

if ( !Factory::getUser()->authorise( 'core.manage', 'com_cck' )
  && !( $view == 'form' || $view == 'list' || $task == 'download' || in_array( substr( $task, 0, 5 ), array( 'form.', 'list.' ) ) ) ) {
	return $app->enqueueMessage( Text::_( 'JERROR_ALERTNOAUTHOR' ), 'error' );
}

$lang			=	Factory::getLanguage();
$lang_default	=	$lang->setDefault( 'en-GB' );
$lang->load( 'com_cck' );
$lang->load( 'com_cck_default', JPATH_SITE );
$lang->load( 'com_cck_core' );
$lang->setDefault( $lang_default );

require_once JPATH_COMPONENT.'/helpers/helper_define.php';
require_once JPATH_COMPONENT.'/helpers/helper_display.php';
require_once JPATH_COMPONENT.'/helpers/helper_include.php';

$controller	=	BaseController::getInstance( 'CCK' );
$controller->execute( $app->input->get( 'task' ) );
$controller->redirect();
?>