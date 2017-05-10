<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once __DIR__.'/helper.php';

$list				=	modCCKBreadCrumbsHelper::getList( $params );
$count				=	count( $list );
$separator			=	modCCKBreadCrumbsHelper::setSeparator( $params->get( 'separator' ) );
$separator_class	=	$params->get( 'separator_class', 'separator' );
$moduleclass_sfx	=	htmlspecialchars( $params->get( 'moduleclass_sfx' ) );
$class_sfx			=	( $params->get( 'force_moduleclass_sfx', 0 ) == 1 ) ? $moduleclass_sfx : '';
require JModuleHelper::getLayoutPath( 'mod_cck_breadcrumbs', $params->get( 'layout', 'default' ) );
?>