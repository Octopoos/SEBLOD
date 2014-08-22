<?php
/* THIS FIELD IS DEPRECATED AND WILL BE REMOVED IN AN UPCOMING RELEASE */
include_once 'helpers/defines.php';
$filename	=	basename( $file );
$ext		=	substr( $file, strrpos( $file, '.' ) + 1 );
if ( $ext == 'php' || $filename == '.htaccess' ) {
	return;
}
$ext 		=	"zip";
$mime_type	=	( PMA_USR_BROWSER_AGENT == 'IE' || PMA_USR_BROWSER_AGENT == 'OPERA' ) ? 'application/octetstream' : 'application/octet-stream';
header( 'Content-Type: ' . $mime_type );
if ( PMA_USR_BROWSER_AGENT == 'IE' ) {
	header( 'Content-Disposition: inline; filename="' . $filename . '"' );
	header( "Content-Transfer-Encoding: binary" );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Pragma: public' );
	readfile( $file );
} else {
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	header( "Content-Transfer-Encoding: binary" );
	header( 'Expires: 0' );
	header( 'Pragma: no-cache' );
	readfile( $file );
}
?>