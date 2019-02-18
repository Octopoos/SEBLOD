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
	return;
}
if ( !is_file( $path ) ) {
	return;
}

$file_size	=	filesize( $path );
$mime_types	=	array(
					// Archives
					'zip' => 'application/octet-stream',
					'tgz' => 'application/x-compressed',
					'rar' => 'application/x-rar-compressed',
					'gz' => 'application/x-gzip',

					// Documents
					'pdf' => 'application/pdf',
					'doc' => 'application/msword',
					'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'xls' => 'application/vnd.ms-excel',
					'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'ppt' => 'application/vnd.ms-powerpoint',
					'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
					'pps' => 'application/vnd.ms-powerpoint',
					'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
					'odt' => 'application/vnd.oasis.opendocument.text',
					'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
					'odp' => 'application/vnd.oasis.opendocument.presentation',
					'txt' => 'text/plain',
					'csv' => 'text/csv',

					// Executables
					'exe' => 'application/octet-stream',

					// Images
					'gif' => 'image/gif',
					'png' => 'image/png',
					'jpg' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'tif' => 'image/tiff',
					'tiff' => 'image/tiff',
					'bmp' => 'image/bmp',

					// Audio
					'mp3' => 'audio/mpeg',
					'wav' => 'audio/x-wav',

					// Video
					'mpeg' => 'video/mpeg',
					'mpg' => 'video/mpeg',
					'mpe' => 'video/mpeg',
					'mov' => 'video/quicktime',
					'avi' => 'video/x-msvideo',
					'mp4' => 'video/mp4',
					'flv' => 'video/x-flv'
				);

if ( !isset( $mime_types[$ext] ) ) {
	$mime_type	=	'';

	if ( function_exists( 'mime_content_type' ) ) {
		$mime_type	=	mime_content_type( $path );
	} elseif ( function_exists( 'finfo_file' ) ) {
		$file_info	=	finfo_open( FILEINFO_MIME );
		$mime_type	=	finfo_file( $file_info, $path );
		
		finfo_close( $file_info );
	}

	if ( !$mime_type ) {
		$mime_type	=	"application/force-download";
	}
} else {
	$mime_type	=	$mime_types[$ext];
}

header( "Pragma: public" );
header( "Expires: 0" );
header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( "Cache-Control: public" );
header( "Content-Type: $mime_type" );
header( "Content-Disposition: attachment; filename=\"$name\"" );
header( "Content-Length: " . $file_size );

if ( isset( $to_be_erased ) && $to_be_erased ) {
	@chmod( $path, 0600 );
}

$chunk_size	=	1024 * 1024;
$handle		=	fopen( $path, 'rb' );

if ( $handle === false ) {
	return;
}
while ( !feof( $handle ) ) {
	echo @fread( $handle, $chunk_size );
	ob_flush();
	flush();
}

fclose( $handle );

if ( isset( $to_be_erased ) && $to_be_erased ) {
	@unlink( $path );
}

exit();
?>