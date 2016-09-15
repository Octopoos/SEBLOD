<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: include.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
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
	
	// addValidation
	public static function addValidation( $rules, $options, $id = '', &$config = array() )
	{
		$doc	=	JFactory::getDocument();
		
		if ( !$id ) {
			$id	=	'seblod_form';
		}
		if ( empty( $rules ) ) {
			$rules	=	'';
		}
		$rules	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $rules );
		
		if ( is_object( $options ) ) {
			$bgcolor	=	$options->get( 'validation_background_color', JCck::getConfig_Param( 'validation_background_color', '' ) );
			$color		=	$options->get( 'validation_color', JCck::getConfig_Param( 'validation_color', '' ) );
			$position	=	$options->get( 'validation_position', JCck::getConfig_Param( 'validation_position', 'topRight' ) );
			$scroll		=	( $options->get( 'validation_scroll', JCck::getConfig_Param( 'validation_scroll', 1 ) ) ) ? 'scroll:true' : 'scroll:false';
			if ( $color != '' ) {
				if ( $position == 'inline' ) {
					$doc->addStyleDeclaration( '#'.$id.' .formError .formErrorContent{color: '.$color.'}' );
				} else {
					$doc->addStyleDeclaration( '.formError .formErrorContent{color: '.$color.'}' );
				}
			}
			if ( $position != 'inline' && $bgcolor != '' ) {
				$css	=	'.formError .formErrorContent{background: '.$bgcolor.'}';
				if ( $position == 'topLeft' || $position == 'topRight' ) {
					$css	.=	'.formError .formErrorArrow{border-color: '.$bgcolor.' transparent transparent transparent;}';
				} else {
					$css	.=	'.formError .formErrorArrow.formErrorArrowBottom{border-color: transparent transparent '.$bgcolor.' transparent;}';
				}
				$doc->addStyleDeclaration( $css );
			}
			$options	=	'{'.$scroll.',promptPosition:"'.$position.'"}';
		} else {
			$options	=	'{}';
		}
		
		$doc->addStyleSheet( JUri::root( true ).'/media/cck/css/cck.validation-3.9.0.css' );
		$doc->addScript( JUri::root( true ).'/media/cck/js/cck.validation-3.9.0.min.js' );
		
		$js	=	'jQuery(document).ready(function($){ $.validationEngineLanguage.newLang({'.$rules.'}); $("#'.$id.'").validationEngine('.$options.'); });';
		$doc->addScriptDeclaration( $js );
	}
}
?>