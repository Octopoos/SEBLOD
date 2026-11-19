<?php
/**
* @package		SEBLOD (App Builder & CCK)
* @url			https://www.seblod.com
* @copyright	Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license		GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// One module built from the Bootstrap shipped with this Joomla.
// If those files are not the expected shape, the caller keeps Joomla's own tags.
abstract class JCckBootstrap
{
	// useScript
	public static function useScript( $doc )
	{
		$relative	=	self::relative();

		if ( $relative === '' ) {
			return false;
		}

		$doc->getWebAssetManager()->registerAndUseScript(
			'bootstrap.components',
			$relative,
			array( 'debug'=>false, 'version'=>false ),
			array( 'type'=>'module' ),
			array( 'core' )
		);

		return true;
	}

	// relative
	protected static function relative()
	{
		$source	=	JPATH_ROOT.'/media/vendor/bootstrap/js';

		if ( !is_dir( $source ) ) {
			return '';
		}

		$names	=	array( 'alert', 'button', 'carousel', 'collapse', 'dropdown', 'modal', 'offcanvas', 'popover', 'scrollspy', 'tab', 'toast' );
		$paths	=	array();

		foreach ( array_merge( array( 'dom', 'popper' ), $names ) as $name ) {
			$path	=	self::source( $source, $name );

			if ( $path === '' ) {
				return '';
			}

			$paths[$name]	=	$path;
		}

		$hash	=	hash_init( 'md5' );

		foreach ( $paths as $path ) {
			hash_update_file( $hash, $path );
		}

		$version	=	self::version( $paths['alert'] );
		$dir		=	JPATH_ROOT.'/cache/com_cck';
		$file		=	$dir.'/bootstrap.components.'.$version.'.'.substr( hash_final( $hash ), 0, 12 ).'.js';

		if ( !is_file( $file ) ) {
			$js	=	self::compile( $paths, $names, $version );

			if ( $js === '' ) {
				return '';
			}
			if ( !is_dir( $dir ) && !@mkdir( $dir, 0755, true ) ) {
				return '';
			}
			if ( !is_file( $dir.'/index.html' ) ) {
				@file_put_contents( $dir.'/index.html', '' );
			}

			$tmp	=	$file.'.'.getmypid().'.tmp';

			if ( @file_put_contents( $tmp, $js ) === false ) {
				return '';
			}

			@rename( $tmp, $file );

			if ( !is_file( $file ) ) {
				@unlink( $tmp );

				return '';
			}

			$keep	=	realpath( $file );

			foreach ( glob( $dir.'/bootstrap.components.*.js' ) as $old ) {
				if ( realpath( $old ) !== $keep ) {
					@unlink( $old );
				}
			}
		}

		return 'cache/com_cck/'.basename( $file );
	}

	// compile
	protected static function compile( $paths, $names, $version )
	{
		$js		=	'/* SEBLOD: Bootstrap '.$version.' components from this Joomla. */'."\n";
		$shared	=	array( 'dom'=>'__cckDom', 'popper'=>'__cckPopper' );

		foreach ( $shared as $name => $var ) {
			$part	=	self::parts( $paths[$name], false );

			if ( $part === false || $part['imports'] || !$part['exports'] ) {
				return '';
			}

			$fields	=	array();

			foreach ( $part['exports'] as $public => $local ) {
				$fields[]	=	$public.':'.$local;
			}

			$js		.=	'const '.$var.'=(()=>{'.$part['body'].";\nreturn {".implode( ',', $fields )."};\n})();\n";
		}

		foreach ( $names as $name ) {
			$part	=	self::parts( $paths[$name], true );

			if ( $part === false ) {
				return '';
			}

			$params	=	array();
			$args	=	array();
			$seen	=	array();

			foreach ( $part['imports'] as $import ) {
				if ( isset( $seen[$import['local']] ) ) {
					return '';
				}

				$seen[$import['local']]	=	true;
				$params[]				=	$import['local'];
				$args[]					=	$import['from'].'.'.$import['public'];
			}

			if ( $params ) {
				$js	.=	'(('.implode( ',', $params ).')=>{'.$part['body']."\n})(".implode( ',', $args ).");\n";
			} else {
				$js	.=	'(()=>{'.$part['body']."\n})();\n";
			}
		}

		foreach ( array( 'Alert', 'Button', 'Carousel', 'Collapse', 'Dropdown', 'Modal', 'Offcanvas', 'Popover', 'Scrollspy', 'Tab', 'Toast' ) as $name ) {
			if ( strpos( $js, 'window.bootstrap.'.$name ) === false ) {
				return '';
			}
		}

		return $js;
	}

	// source
	protected static function source( $dir, $name )
	{
		foreach ( array( $name.'.min.js', $name.'.js' ) as $file ) {
			$path	=	$dir.'/'.$file;

			if ( is_file( $path ) ) {
				return $path;
			}
		}

		return '';
	}

	// version
	protected static function version( $path )
	{
		$head	=	file_get_contents( $path, false, null, 0, 500 );

		if ( is_string( $head ) && preg_match( '/\?(\d+\.\d+\.\d+)/', $head, $match ) ) {
			return $match[1];
		}

		$json	=	JPATH_ROOT.'/media/vendor/joomla.asset.json';

		if ( is_file( $json ) ) {
			$data	=	json_decode( (string)file_get_contents( $json ), true );

			if ( is_array( $data ) && !empty( $data['assets'] ) ) {
				foreach ( $data['assets'] as $asset ) {
					if ( !empty( $asset['name'] ) && $asset['name'] === 'bootstrap.alert' && !empty( $asset['version'] ) ) {
						$version	=	preg_replace( '/[^0-9.]/', '', $asset['version'] );

						if ( $version !== '' ) {
							return $version;
						}
					}
				}
			}
		}

		return '0';
	}

	// parts
	protected static function parts( $path, $importsAllowed )
	{
		$source	=	file_get_contents( $path );

		if ( !is_string( $source ) || $source === '' ) {
			return false;
		}
		if ( strncmp( $source, "\xEF\xBB\xBF", 3 ) === 0 ) {
			$source	=	substr( $source, 3 );
		}

		$source		=	self::skipBanner( $source );
		$imports	=	array();

		while ( strpos( $source, 'import' ) === 0 ) {
			if ( !$importsAllowed || !preg_match( '/^import\s*\{([^}]*)\}\s*from\s*([\'"])([^\'"]+)\2\s*;/', $source, $match ) ) {
				return false;
			}

			$from	=	self::vendor( $match[3] );

			if ( $from === '' ) {
				return false;
			}

			foreach ( explode( ',', $match[1] ) as $spec ) {
				$binding	=	self::binding( $spec, false );

				if ( $binding === false ) {
					return false;
				}

				$binding['from']	=	$from;
				$imports[]			=	$binding;
			}

			$source	=	self::skipBanner( substr( $source, strlen( $match[0] ) ) );
		}

		if ( !preg_match( '/export\s*\{([^}]*)\}\s*;?\s*$/', $source, $match ) ) {
			return false;
		}

		$exports	=	array();

		foreach ( explode( ',', $match[1] ) as $spec ) {
			$binding	=	self::binding( $spec, true );

			if ( $binding === false || isset( $exports[$binding['public']] ) ) {
				return false;
			}

			$exports[$binding['public']]	=	$binding['local'];
		}

		$body	=	preg_replace( '/export\s*\{[^}]*\}\s*;?\s*$/', '', $source );

		if ( !is_string( $body ) || strpos( $body, 'import.meta' ) !== false || strpos( $body, 'import(' ) !== false ) {
			return false;
		}

		return array( 'imports'=>$imports, 'exports'=>$exports, 'body'=>$body );
	}

	// binding
	// Import is "{ public as local }". Export is "{ local as public }".
	protected static function binding( $spec, $exportStatement )
	{
		$spec	=	trim( $spec );

		if ( $spec === '' ) {
			return false;
		}
		if ( preg_match( '/^([A-Za-z_$][\w$]*)\s+as\s+([A-Za-z_$][\w$]*)$/', $spec, $match ) ) {
			if ( $exportStatement ) {
				return array( 'public'=>$match[2], 'local'=>$match[1] );
			}

			return array( 'public'=>$match[1], 'local'=>$match[2] );
		}
		if ( preg_match( '/^([A-Za-z_$][\w$]*)$/', $spec, $match ) ) {
			return array( 'public'=>$match[1], 'local'=>$match[1] );
		}

		return false;
	}

	// vendor
	protected static function vendor( $from )
	{
		$file	=	basename( preg_replace( '/\?.*$/', '', str_replace( '\\', '/', $from ) ) );

		if ( $file === 'dom.js' || $file === 'dom.min.js' ) {
			return '__cckDom';
		}
		if ( $file === 'popper.js' || $file === 'popper.min.js' ) {
			return '__cckPopper';
		}

		return '';
	}

	// skipBanner
	protected static function skipBanner( $source )
	{
		while ( true ) {
			$source	=	ltrim( $source );

			if ( strpos( $source, '/*' ) === 0 ) {
				$end	=	strpos( $source, '*/' );

				if ( $end === false ) {
					return $source;
				}

				$source	=	substr( $source, $end + 2 );
				continue;
			}
			if ( strpos( $source, '//' ) === 0 ) {
				$end	=	strpos( $source, "\n" );
				$source	=	( $end === false ) ? '' : substr( $source, $end + 1 );
				continue;
			}

			return $source;
		}
	}
}
