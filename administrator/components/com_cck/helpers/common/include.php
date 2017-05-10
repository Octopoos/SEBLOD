<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: include.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// CommonHelper
class CommonHelper_Include
{	
	// addDependencies
	public static function addDependencies( $view, $layout, $tmpl = '' )
	{		
		$doc	=	JFactory::getDocument();
		$script	=	( $tmpl == 'ajax' ) ? false : true;
		
		if ( $script !== false ) {
			JHtml::_( 'bootstrap.framework' );
			JHtml::_( 'bootstrap.tooltip' );
			
			JCck::loadjQuery( true, true, true );
		}
		Helper_Include::addStyleSheets( true );
	}
	
	// addStyleDeclaration
	public static function addStyleDeclaration( $css, $minify = false )
	{
		$doc	=	JFactory::getDocument();
		
		if ( $minify === true ) {
			$css	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
		}
		
		$doc->addStyleDeclaration( $css );
	}
	
	// addStyleSheets
	public static function addStyleSheets( $component, $paths = array() )
	{
		$doc	=	JFactory::getDocument();
		
		$doc->addStyleDeclaration( 'div.pagetitle {display: block!important;}' );
		if ( $component ) {
			$doc->addStyleDeclaration( 'div.seblod {margin: 0px 10px 10px 10px!important;}' );
			JHtml::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/css/admin.css', array(), false );
			JHtml::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/css/font.css', array(), false );
		}
		JHtml::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/css/icons.css', array(), false );
		JHtml::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/css/ui.css', array(), false );
		
		JHtml::_( 'stylesheet', 'administrator/components/'.CCK_COM.'/assets/styles/seblod/ui.css', array(), false );
		
		if ( count( $paths ) ) {
			foreach ( $paths as $path ) {
				JHtml::_( 'stylesheet', $path, array(), false );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- //
	
	// addSmoothScrool
	public static function addSmoothScrool( $time = 1000 )
	{
		$doc	=	JFactory::getDocument();
		
		$js		=	'jQuery(document).ready(function($){ $(".scroll").click(function(event){ event.preventDefault(); $("html,body").animate({scrollTop:$(this.hash).offset().top}, '.$time.'); }); });';
		$doc->addScriptDeclaration( $js );
	}
	
	// addTooltip
	public static function addTooltip( $elem = '', $pos_my = 'top left', $pos_at = 'bottom right', $classes = '', $script = true, $tmpl = '' )
	{
	}
	
	// addValidation (deprecated)
	public static function addValidation( $rules, $options, $id = '', &$config = array() )
	{
		JCckDev::addValidation( $rules, $options, $id, $config );
	}
}
?>