<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on( '4.0' ) ) {
	$attributes		=	'';
	$selectlabel	=	'Any';
	$method			=	'getForm';
} else {
	$attributes		=	'style="max-width:260px;"';
	$selectlabel	=	'None';
	$method			=	'renderForm';
}

echo JCckDev::$method( $cck['core_storage_table'], $table, $config, array( 'selectlabel'=>$selectlabel, 'attributes'=>$attributes ) );
?>