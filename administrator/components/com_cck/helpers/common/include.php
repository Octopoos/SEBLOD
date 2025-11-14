<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: include.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

// CommonHelper
class CommonHelper_Include
{	
	// addDependencies
	public static function addDependencies( $view, $layout, $tmpl = '' )
	{		
		$doc	=	Factory::getDocument();
		$script	=	( $tmpl == 'ajax' ) ? false : true;
		
		if ( $script !== false ) {
			HTMLHelper::_( 'bootstrap.framework' );
			HTMLHelper::_( 'bootstrap.tooltip' );
			
			JCck::loadjQuery( true, true, true );
		}
		Helper_Include::addStyleSheets( true );
	}
	
	// addStyleDeclaration
	public static function addStyleDeclaration( $css, $minify = false )
	{
		$doc	=	Factory::getDocument();
		
		if ( $minify === true ) {
			$css	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
		}
		
		$doc->addStyleDeclaration( $css );
	}
	
	// addStyleSheets
	public static function addStyleSheets( $component, $paths = array() )
	{
		$doc	=	Factory::getDocument();
		
		$doc->addStyleDeclaration( 'div.pagetitle {display: block!important;}' );
		if ( $component ) {
			$doc->addStyleDeclaration( 'div.seblod {margin: 0 10px 10px 10px!important;}' );
			$doc->addStyleSheet( Uri::root( true ).'/administrator/components/'.CCK_COM.'/assets/css/admin.css?v4.0' );
			HTMLHelper::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/css/font.css' );
		}
		HTMLHelper::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/css/icons.css' );
		$doc->addStyleSheet( Uri::root( true ).'/administrator/components/'.CCK_COM.'/assets/css/ui.css?v5' );

		if ( JCck::on( '4.0' ) ) {
			$doc->addStyleSheet( Uri::root( true ).'/administrator/components/'.CCK_COM.'/assets/styles/cck_4x/ui4.css?v2' );
		}
		
		if ( count( $paths ) ) {
			foreach ( $paths as $path ) {
				HTMLHelper::_( 'stylesheet', $path );
			}
		}
	}
}
?>