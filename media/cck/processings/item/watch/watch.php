<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Filesystem\Folder;

if ( !( $config['id'] && !$config['isNew'] ) ) {
	return;
}

$path	=	JPATH_SITE.'/cache/cck_item@'.(int)$config['id'];
$path2	=	JPATH_SITE.'/cache/_cck_item@'.(int)$config['id'];

if ( is_dir( $path ) ) {
	Folder::delete( $path );
}
if ( is_dir( $path2 ) ) {
	Folder::delete( $path2 );
}
?>