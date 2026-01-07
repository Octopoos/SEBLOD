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

@ob_end_clean();
header_remove( 'X-Powered-By' );
set_time_limit( 0 );

$cache_seconds	=	86400;
$mime_types		=	array(
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
$watermark		=	false;

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

if ( isset( $config['watermark'] ) && is_array( $config['watermark'] ) && !empty( $config['watermark']['texts'] ) ) {
	$cmd_normalize			=	'gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -dAutoRotatePages=/All -sOutputFile=- -f -';
	$cmd_watermark			=	'gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress -sOutputFile=- '
							.	'-c "<< /EndPage { exch pop 0 eq { gsave ';
	$cmd_watermark_end		=	'grestore true } { false } ifelse } >> setpagedevice" -f -';
	$ps_instructions		=	'';
	$watermark				=	true;

	foreach ( $config['watermark']['texts'] as $text ) {
		$color		=	isset( $text['color'] ) && $text['color'] ? $text['color'] : '0.3 setgray';
		$font_size	=	isset( $text['font_size'] ) && $text['font_size'] ? $text['font_size'] : '12';
		$imagePath	=	JPATH_SITE.'/media/cck_dev/processings/document/download_tracking/watermark2.png';
		$safeText	=	addslashes( $text['text'] );

		/*
		$command	.=	'grestore gsave 1 0 0 setrgbcolor /Helvetica findfont 12 scalefont setfont '
					.	'50 300 translate 90 rotate 0 0 moveto (Download on xx by xx) show ';

		$command	.=	'grestore gsave 1 0 0 setrgbcolor /Helvetica findfont 12 scalefont setfont '
					.	'30 230 moveto 90 rotate (Download on xx by xx) show ';

		$command	.=	'grestore gsave 0.3 setgray /Helvetica findfont 50 scalefont setfont '
					.	( 25 + 30 ).' '.( 25 + 10 ).' moveto 45 rotate (CONFIDENTIAL) show ';
		*/

		$ps_instructions	.=	'grestore gsave '
							.	'currentpagedevice /PageSize get aload pop /ph exch def /pw exch def '
							.	'30 ph 2 div translate 90 rotate ';

		$ps_instructions	.=	$color.' /Arial findfont '.$font_size.' scalefont setfont '
							.	'(' . $safeText . ') stringwidth pop 2 div neg 0 moveto '
							.	'(' . $safeText . ') show ';
	}

	$command	=	$cmd_normalize.' | '.$cmd_watermark.$ps_instructions.$cmd_watermark_end;
}

header( "Content-Type: $mime_type" );
header( "Content-Disposition: attachment; filename=\"$name\"" );

if ( $watermark ) {
	header( "Expires: 0" );
	header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
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

if ( $mime_type == 'application/pdf' && JCck::getConfig_Param( 'media_canonical', 0 ) ) {
	$uri_link	=	JUri::current().'?task=read&file='.$fieldname.'&id='.$id;

	header( "Link: <$uri_link>; rel=\"canonical\"" );
} else {
	$uri_link	=	JUri::current().'?task=download&file='.$fieldname.'&id='.$id;

	header( "Link: <$uri_link>; rel=\"canonical\"" );
}

if ( isset( $to_be_erased ) && $to_be_erased ) {
	@chmod( $path, 0600 );
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

	if ( $watermark ) {
		$descriptors	=	[
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w'],
			2 => ['pipe', 'w']
		];
		$process		=	proc_open( $command, $descriptors, $pipes );

		if ( !is_resource( $process ) ) {
			die;
		}

		while ( !feof($handle ) ) {
			$chunk	=	fread( $handle, $chunk_size );
			fwrite( $pipes[0], $chunk );
			fflush( $pipes[0] );
		}

		fclose( $handle );
		fclose( $pipes[0] );

		while ( !feof( $pipes[1] ) ) {
			echo @fread( $pipes[1], $chunk_size );
			ob_flush();
			flush();
		}

		fclose( $pipes[1] );
		fclose( $pipes[2] );
		proc_close( $process );
	} else {
		while ( !feof( $handle ) ) {
			echo @fread( $handle, $chunk_size );
			ob_flush();
			flush();
		}

		fclose( $handle );
	}
}

if ( isset( $to_be_erased ) && $to_be_erased ) {
	@unlink( $path );
}

exit;
?>