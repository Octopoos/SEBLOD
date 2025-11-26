<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2021 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
defined( 'CCK_COM' ) or define( 'CCK_COM', 'com_cck' );

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

// Script
class plgSystemCCKInstallerScript
{
	// preflight
	public function preflight( $type, $parent )
	{
		$app		=	Factory::getApplication();

		// WAITING FOR JOOMLA 1.7.x FIX		
		$config		=	Factory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_var_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		$src		=	JPATH_SITE.'/libraries/cck/rendering/variations';
		if ( Folder::exists( $src ) ) {
			Folder::copy( $src, $path );
			$app->cck_core_temp_var	=	$tmp_dir;
		}
		// WAITING FOR JOOMLA 1.7.x FIX

		// Language Constants (SQL)
		if ( is_file( JPATH_ADMINISTRATOR.'/components/com_cck/_VERSION.next.php' ) ) {
			$path	=	JPATH_SITE.'/language';

			if ( Folder::exists( $path ) ) {
				$lang_tags	=	Folder::folders( $path );
				$protected	=	array( 'overrides' );

				foreach ( $lang_tags as $lang_tag ) {
					if ( !in_array( $lang_tag, $protected ) ) {
						if ( strpos( $lang_tag, '-' ) !== false ) {
							File::copy( $path.'/'.$lang_tag.'/com_cck_default.ini', $path.'/'.$lang_tag.'/com_cck_default.sql.ini' );
						}
					}
				}
			}
		}
	}
}
?>