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

use Joomla\CMS\Uri\Uri;

if ( !( isset( $name ) && $name != '' && isset( $path ) && $path != '' ) ) {
	die;
}
if ( !is_file( $path ) ) {
	die;
}

@ob_end_clean();
header_remove( 'X-Powered-By' );
set_time_limit( 0 );

$cache_seconds	=	86400;
$mime_types		=	array(
						// PDF Documents
						'pdf' => 'application/pdf',
					);
$watermark		=	false;

if ( !isset( $mime_types[$ext] ) ) {
	die;
} else {
	$mime_type	=	$mime_types[$ext];
}

if ( isset( $config['watermark'] ) && is_array( $config['watermark'] ) && !empty( $config['watermark']['texts'] ) ) {
	$watermark	=	$config['watermark'];
}

header( "Content-Type: $mime_type" );
header( "Content-Disposition: inline; filename=\"$name\"" );

if ( $watermark || !$id || ( strpos( $path, '/private/' ) !== false ) ) {
	header( "Expires: 0" );
	header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
	
	if ( !$watermark && !isset( $config['app'] ) ) {
		header( "Content-Length: ".filesize( $path ) );
	}
} else {
	header( "Expires: ".gmdate( "D, d M Y H:i:s", time() + $cache_seconds)." GMT" );
	header( "Cache-Control: public, max-age=" . $cache_seconds . ", must-revalidate" );

	if ( !isset( $config['app'] ) ) {
		header( "Content-Length: ".filesize( $path ) );
	}
}
if ( isset( $x_robots ) && $x_robots ) {
	switch ( $x_robots ) {
		case 'index, nofollow':
			header( "X-Robots-Tag: nofollow" );
			break;
		case 'noindex, follow':
			header( "X-Robots-Tag: noindex" );
			break;
		case 'noindex, nofollow':
			header( "X-Robots-Tag: noindex, nofollow" );
			break;
		default:
			break;
	}
}

if ( !JCck::getConfig_Param( 'media_canonical', 0 ) ) {
	$uri_link	=	Uri::current().'?task=download&file='.$fieldname.'&id='.$id;

	header( "Link: <$uri_link>; rel=\"canonical\"" );
} else {
	$uri_link	=	Uri::current().'?task=read&file='.$fieldname.'&id='.$id;

	header( "Link: <$uri_link>; rel=\"canonical\"" );
}

JCckDevHelper::stream( $path, $watermark, $config );

exit;
?>