<?php
defined( '_JEXEC' ) or die;

if ( !( $config['id'] && !$config['isNew'] ) ) {
	return;
}

$path	=	JPATH_SITE.'/cache/cck_item@'.(int)$config['id'];
$path2	=	JPATH_SITE.'/cache/_cck_item@'.(int)$config['id'];

if ( is_dir( $path ) ) {
	JFolder::delete( $path );
}
if ( is_dir( $path2 ) ) {
	JFolder::delete( $path2 );
}
?>