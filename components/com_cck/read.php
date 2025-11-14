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

$file_size	=	filesize( $path );
$mime_types	=	array(
					// PDF Documents
					'pdf' => 'application/pdf',
				);

if ( !isset( $mime_types[$ext] ) ) {
	die;
} else {
	$mime_type	=	$mime_types[$ext];
}

header( "Expires: 0" );
header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( "Cache-Control: no-store" );
header( "Content-Type: $mime_type" );
header( "Content-Disposition: inline; filename=\"$name\"" );
header( "Content-Length: " . $file_size );

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

if ( isset( $config['app'] ) ) {
	$buffer	=	file_get_contents( $path );

	echo $config['app']->decrypt( $buffer );
} else {
	$chunk_size	=	1024 * 1024;
	$handle		=	fopen( $path, 'rb' );

	if ( $handle === false ) {
		die;
	}
	while ( !feof( $handle ) ) {
		echo @fread( $handle, $chunk_size );
		ob_flush();
		flush();
	}

	fclose( $handle );
}

exit();
?>