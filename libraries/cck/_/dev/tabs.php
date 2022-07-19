<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tabs.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

// JCckDevTabs
abstract class JCckDevTabs
{
	// end
	public static function end()
	{
		if ( JCck::on( '4.0' ) && JFactory::getApplication()->isClient( 'administrator' ) ) {
			$html	=	HTMLHelper::_( 'uitab.endTab' )
			 		.	HTMLHelper::_( 'uitab.endTabSet' );
		} else {
			$html	=	JHtml::_( 'bootstrap.endTab' )
				 	.	JHtml::_( 'bootstrap.endTabSet' );
		}

		return $html;
	}
	
	// open
	public static function open( $selector, $id, $text )
	{
		if ( JCck::on( '4.0' ) && JFactory::getApplication()->isClient( 'administrator' ) ) {
			$html	=	HTMLHelper::_( 'uitab.endTab' )
				 	.	HTMLHelper::_( 'uitab.addTab', $selector, $id, $text );
		} else {
			$html	=	JHtml::_( 'bootstrap.endTab' )
				 	.	JHtml::_( 'bootstrap.addTab', $selector, $id, $text );
		}

		if ( JFactory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
        	static $tabScriptLayout	=	null;
        	
        	$active				=	'';
			$tabScriptLayout	=	is_null( $tabScriptLayout ) ? new JLayoutFile( 'libraries.cms.html.bootstrap.addtabscript' ) : $tabScriptLayout;
			$js					=	$tabScriptLayout->render( array(
																'selector'=>$selector,
																'id'=>$id,
																'active'=>$active,
																'title' => $text
															) );
        	$html				.=	 '<script type="text/javascript">'.$js.'</script>';
        }

		return $html;
	}
	
	// start
	public static function start( $selector, $id, $text, $params )
	{
		if ( JCck::on( '4.0' ) && JFactory::getApplication()->isClient( 'administrator' ) ) {
			$params['breakpoint']	=	768;
			$params['recall']		=	true;

			$html	=	HTMLHelper::_( 'uitab.startTabSet', $selector, $params )
	        	 	.	HTMLHelper::_( 'uitab.addTab', $selector, $id, $text );
		} else {
			$html	=	JHtml::_( 'bootstrap.startTabSet', $selector, $params )
	        	 	.	JHtml::_( 'bootstrap.addTab', $selector, $id, $text );
		}

        if ( JFactory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
			$tabScriptLayout	=	new JLayoutFile( 'libraries.cms.html.bootstrap.addtabscript' );
			$js					=	$tabScriptLayout->render( array(
																'selector'=>$selector,
																'id'=>$id,
																'active'=>'active',
																'title' => $text
															) );

        	$js					.=	JLayoutHelper::render( 'libraries.cms.html.bootstrap.starttabsetscript', array( 'selector' => $selector ) );;
        	$html				.=	 '<script type="text/javascript">'.$js.'</script>';
        }

        return $html;
	}
}
?>