<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_include.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Include
{
	// addScriptDeclaration
	public static function addScriptDeclaration( $js )
	{
		if ( $js != '' ) {
			JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($) { '.$js.' });' );
		}
	}
	
	// addColorbox (deprecated: use JCck::loadModalBox();)
	public static function addColorbox( $width = '900', $height = '550' )
	{
		$doc	=	JFactory::getDocument();
		
		$doc->addStyleSheet( JROOT_MEDIA_CCK.'/scripts/jquery-colorbox/css/colorbox.css' );
		$doc->addScript( JROOT_MEDIA_CCK.'/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
		
		$js		=	'jQuery(document).ready(function($){ $(".cbox").colorbox({iframe:true, innerWidth:'.$width.', innerHeight:'.$height.'}); });';
		$doc->addScriptDeclaration( $js );
	}
	
	// addValidation
	public static function addValidation( $rules, $options, $id = '', &$config = array() )
	{
		$app	=	JFactory::getApplication();
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
		$js				=	'jQuery(document).ready(function($){ $.validationEngineLanguage.newLang({'.$rules.'}); $("#'.$id.'").validationEngine('.$options.'); });';
		
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<link rel="stylesheet" href="'.JURI::root( true ).'/media/cck/css/cck.validation-3.6.0.css" type="text/css" />';
			echo '<script src="'.JURI::root( true ).'/media/cck/js/cck.validation-3.5.0.min.js" type="text/javascript"></script>';
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			$doc->addStyleSheet( JURI::root( true ).'/media/cck/css/cck.validation-3.6.0.css' );
			$doc->addScript( JURI::root( true ).'/media/cck/js/cck.validation-3.5.0.min.js' );
			$doc->addScriptDeclaration( $js );
		}
	}
}
?>