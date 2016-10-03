<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_include.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/include.php';

// Helper
class Helper_Include extends CommonHelper_Include
{
	// addDependencies
	public static function addDependencies( $view, $layout, $tmpl = '' )
	{
		$doc		=	JFactory::getDocument();
		$script		=	( $tmpl == 'ajax' ) ? false : true;
		
		Helper_Include::addStyleSheets( true );
		
		// Additional
		switch ( $view ) {
			case 'box':
				JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js', 'jquery.ui.effects.min.js', 'jquery.json.min.js' ) );
				Helper_Include::addSmoothScrool( 500 );
				break;
			case 'folder':
				JCck::loadjQuery( true, true, true );
				break;
			case 'template':
				JCck::loadjQuery( true, true, true );
				break;
			case 'site':
				JCck::loadjQuery( true, true, true );
				break;
			case 'field':
				if ( $script === true ) {
					JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js' ) );
					JCck::loadjQueryUI();
				}
				if ( $tmpl == 'component' ) {
					$doc->addScript( JROOT_MEDIA_CCK.'/js/cck.backend-3.9.0.min.js' );
				}
				break;
			case 'type':
			case 'search':
				if ( $script === true ) {
					JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js', 'jquery.biscuit.min.js' ) );
					JCck::loadjQueryUI();
					$doc->addScript( JROOT_MEDIA_CCK.'/js/cck.backend-3.9.0.min.js' );
					$doc->addStyleSheet( JROOT_CCK.'/administrator/components/com_'.CCK_NAME.'/assets/css/ui-construction.css' );
					$doc->addStyleSheet( JROOT_CCK.'/administrator/components/com_'.CCK_NAME.'/assets/styles/seblod/ui-construction.css' );
				}
				Helper_Include::addColorbox_Live( '930', '550', $script, 'cbox', ', onLoad: function(){ $("#cboxClose").remove();}' );
				Helper_Include::addColorpicker( $script );
				break;
			case 'session':
			case 'version':
				JCck::loadjQuery( true, true, true );
				break;
			// --------
			case 'templates':
			case 'types':
			case 'fields':
			case 'searchs':
			case 'folders':
			case 'sites':
			case 'variations':
			case 'sessions':
			case 'versions':
				require_once JPATH_LIBRARIES.'/cck/joomla/html/cckactionsdropdown.php';

				if ( $view == 'folders' ) {
					JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js' ) );
				} else {
					JCck::loadjQuery();
				}
				JHtml::_( 'bootstrap.tooltip' );
				JHtml::_( 'formbehavior.chosen', 'select:not(.no-chosen)' );
				
				Helper_Include::addSmoothScrool();
				
				if ( $view == 'fields' ) {
					Helper_Include::addColorbox( '500', '300', $script, 'cbox', ', onLoad: function(){ $("#cboxClose").remove();}' );
				} elseif ( $view == 'templates' ) {
					Helper_Include::addColorbox( '850', '585', $script, 'cbox', ', scrolling:false, onLoad: function(){ $("#cboxClose").remove();}' );
				} elseif ( $view == 'types' || $view == 'searchs' ) {
					Helper_Include::addColorbox( '850', '430', true, 'cbox_button', ', scrolling:false' );
				}
				if ( $view == 'searchs' || $view == 'sites' ) {
					$doc->addStyleSheet( JROOT_MEDIA_CCK.'/css/jquery.sly.css' );
					$doc->addScript( JROOT_MEDIA_CCK.'/js/jquery.sly.min.js' );
				}
				if ( $view == 'sessions' ) {
					$doc->addStyleDeclaration( '#system-message-container.j-toggle-main.span10{width: 100%;}' );
				}
				break;
			case 'list':
				JHtml::_( 'formbehavior.chosen', 'select:not(.no-chosen)' );
				break;
			case 'cck':
				$doc->addStyleSheet( JROOT_CCK.'/administrator/components/com_'.CCK_NAME.'/assets/css/cpanel.css' );
				JCck::loadjQuery();
				Helper_Include::addColorbox( '930', '430', true, 'cbox_button' );
				break;
			default:
				break;
		}
	}
	
	// addScriptDeclaration
	public static function addScriptDeclaration( $js )
	{
		if ( $js != '' ) {
			JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($) { '.$js.' });' );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- //
	
	// addColorbox
	public static function addColorbox( $width = '900', $height = '550', $script = true, $class = 'cbox', $options = '' )
	{
		$doc	=	JFactory::getDocument();
		
		if ( $script === true ) {
			$doc->addStyleSheet( JROOT_MEDIA_CCK.'/scripts/jquery-colorbox/css/colorbox.css' );
			$doc->addScript( JROOT_MEDIA_CCK.'/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
		}
		
		$js		=	'jQuery(document).ready(function($){ $(".'.$class.'").colorbox({iframe:true,innerWidth:'.$width.',innerHeight:'.$height.',overlayClose:false,fixed:true'.$options.'}); });';
		$doc->addScriptDeclaration( $js );
	}
	
	// addColorbox_Live
	public static function addColorbox_Live( $width = '900', $height = '550', $script = true, $class = 'cbox', $options = '' )
	{
		$doc	=	JFactory::getDocument();
		
		if ( $script === true ) {
			$doc->addStyleSheet( JROOT_MEDIA_CCK.'/scripts/jquery-colorbox/css/colorbox.css' );
			$doc->addScript( JROOT_MEDIA_CCK.'/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
			
			$js	=	'$(document).on("click",".'.$class.'", function(e) { e.preventDefault();
						$.colorbox({href:$(this).attr(\'href\'),open:true,iframe:true,innerWidth:'.$width.',innerHeight:'.$height.',overlayClose:false,fixed:true'.$options.'});
						return false;
					});
					';
					
			$doc->addScriptDeclaration( '(function ($){'.$js.'})(jQuery);' );
		}		
	}
	
	// addColorpicker
	public static function addColorpicker( $script = true )
	{
		$doc	=	JFactory::getDocument();
		
		if ( $script === true ) {
			$doc->addStyleSheet( JUri::root( true ).'/plugins/cck_field/colorpicker/assets/css/colorpicker_custom.css' );
			$doc->addScript( JUri::root( true ).'/plugins/cck_field/colorpicker/assets/js/colorpicker.js' );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- //
	
	// autoSave
	public static function autoSave( $interval = '2' )
	{
		$doc	=	JFactory::getDocument();
		$delay	=	(float)$interval * 60000;
		
		$js	=	'
				(function ($){
					JCck.Dev = {
						ajaxWork: function(task) {
							if ($("#adminForm").validationEngine("validate",task) === true) {
								$("#task").val(task);
								$.ajax({
									cache: false,
									type: "POST",
									url: "index.php?option=com_cck&task="+task,
									data: $("#adminForm").serialize(),
									success: function(response) {
										var now = new Date();
										if ( ! $("#id").val() ) {
											var id = response.substring( response.indexOf("id")+3, response.length - 12 );
											if ( $("#myid") ) {
												$("#myid").val(id);
											}
											$("#id").val(id);
										}
										$("#ajaxMessage").html("").html("<span>Successfuly saved! "+now.getHours()+":"+now.getMinutes()+":"+now.getSeconds()+"</span>")
										.hide()
										.fadeIn(2000, function() {
											if ( parent.jQuery.colorbox ) {
												parent.jQuery.colorbox.close();
											}
										});
									}
								});
							}
						}
					}
					$(document).ready(function() { setInterval("JCck.Dev.ajaxWork(\'form.apply\')",'.$delay.'); });
				})(jQuery);
				';
		$doc->addScriptDeclaration( $js );
	}
}
?>