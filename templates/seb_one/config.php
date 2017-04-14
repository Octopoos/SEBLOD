<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

global $user;

$app		=	JFactory::getApplication();
$path_lib	=	JPATH_SITE.'/libraries/cck/rendering/rendering.php';
$user		=	JCck::getUser();

if ( ! file_exists( $path_lib ) ) {
	print( '/libraries/cck/rendering/rendering.php file is missing.' );
	die;
}

require_once $path_lib;
?>