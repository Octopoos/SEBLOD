<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_items.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$loaded		=	0;
$location	=	$config['location'];
$params		=	new JRegistry;

if ( count( $items ) && $location ) {
	$pks		=	'';
	$pkbs		=	'';
	$plg		=	JPluginHelper::getPlugin( 'cck_storage_location', $location );
	$plg_params	=	new JRegistry( $plg->params );
	for ( $i = 0; $i < $total; $i++ ) {
		if ( isset( $items[$i]->pk ) ) {
			$pks	.=	(int)$items[$i]->pk.',';
			$pkbs	.=	(int)$items[$i]->pkb.',';
		}
	}
	if ( $pks ) {
		$pks	=	substr( $pks, 0, -1 );
	}
	if ( $pkbs ) {
		$pkbs	=	substr( $pkbs, 0, -1 );
	}
	
	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'onCCK_Storage_LocationPrepareList', array( &$params ) );
	$path	=	JPATH_PLUGINS.'/cck_storage_location/'.$location.'/tmpl/item.php';
	
	foreach ( $items as $item ) {
		include $path;	
	}
} else {
	echo $data;
}
?>