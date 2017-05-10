<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tabs.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on( '3.1' ) ) {
	
	// JCckDevTabs
	abstract class JCckDevTabs
	{
		// end
		public static function end()
		{
			$html	=	JHtml::_( 'bootstrap.endTab' )
				 	.	JHtml::_( 'bootstrap.endTabSet' );

			return $html;
		}
		
		// open
		public static function open( $selector, $id, $text )
		{
			$html	=	JHtml::_( 'bootstrap.endTab' )
				 	.	JHtml::_( 'bootstrap.addTab', $selector, $id, $text );

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
	        $html	=	JHtml::_( 'bootstrap.startTabSet', $selector, $params )
	        	 	.	JHtml::_( 'bootstrap.addTab', $selector, $id, $text );

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
} else {
	
	// JCckDevAccordion (Joomla! 2.5 legacy)
	abstract class JCckDevTabs
	{
		// end
		public static function end()
		{
			return '';
		}
		
		// open
		public static function open( $selector, $id, $text )
		{
			return '';
		}
		
		// start
		public static function start( $selector, $id, $text, $params )
		{
			return '';
		}
	}	
}
?>