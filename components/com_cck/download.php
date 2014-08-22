<?php
if ( !( isset( $name ) && $name != '' && isset( $path ) && $path != '' ) ) {
  return;
}
$allowed_ext  = array(
  // archives
  'zip' => 'application/zip',
  'tgz' => 'application/x-compressed',
  'rar' => 'application/x-rar-compressed',
  'gz' => 'application/x-gzip',

  // documents
  'pdf' => 'application/pdf',
  'doc' => 'application/msword',
  'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  'xls' => 'application/vnd.ms-excel',
  'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  'ppt' => 'application/vnd.ms-powerpoint',
  'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
  'pps' => 'application/vnd.ms-powerpoint',
  'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
  'txt' => 'text/plain',
  'csv' => 'text/csv',

  // executables
  'exe' => 'application/octet-stream',

  // images
  'gif' => 'image/gif',
  'png' => 'image/png',
  'jpg' => 'image/jpeg',
  'jpeg' => 'image/jpeg',
  'tif' => 'image/tiff',
  'tiff' => 'image/tiff',
  'bmp' => 'image/bmp',

  // audio
  'mp3' => 'audio/mpeg',
  'wav' => 'audio/x-wav',

  // video
  'mpeg' => 'video/mpeg',
  'mpg' => 'video/mpeg',
  'mpe' => 'video/mpeg',
  'mov' => 'video/quicktime',
  'avi' => 'video/x-msvideo',
  'mp4' => 'video/mp4',
  'flv' => 'video/x-flv'
);
if ( @$allowed_ext[$ext] == '' ) {
	$mtype	=	'';
	if ( function_exists( 'mime_content_type' ) ) {
		$mtype	=	mime_content_type( $path );
	} elseif ( function_exists( 'finfo_file' ) ) {
		$finfo	=	finfo_open( FILEINFO_MIME );
		$mtype	=	finfo_file( $finfo, $path );
		finfo_close( $finfo );
  }
	if ( $mtype == '' ) {
    	$mtype	=	"application/force-download";
	}
} else {
	$mtype	=	$allowed_ext[$ext];
}
header( "Pragma: public" );
header( "Expires: 0" );
header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( "Cache-Control: public" );
header( "Content-Description: File Transfer" );
header( "Content-Type: $mtype" );
header( "Content-Disposition: attachment; filename=\"$name\"" );
/*
header( "Content-Length: " . filesize($path) );
*/
if ( file_exists( $path ) ) {
  ob_clean();
	flush();
	readfile( $path );
	exit();
}
?>