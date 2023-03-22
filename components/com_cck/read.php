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
header( "Content-Length: " . $file_size );

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

exit();
?>