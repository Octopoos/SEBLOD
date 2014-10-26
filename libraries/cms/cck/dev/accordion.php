<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: accordion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on() ) {
	
	// JCckDevAccordion
	abstract class JCckDevAccordion
	{
		// end
		public static function end()
		{
			return JHtml::_( 'bootstrap.endSlide' )
				 . JHtml::_( 'bootstrap.endAccordion' );
		}
		
		// open
		public static function open( $selector, $id, $text, $class = '' )
		{
			return JHtml::_( 'bootstrap.endSlide' )
				 . JHtml::_( 'bootstrap.addSlide', $selector, $text, $id, $class );
		}
		
		// start
		public static function start( $selector, $id, $text, $params )
		{
			unset( $params['useCookie'] );
			
	        return JHtml::_( 'bootstrap.startAccordion', $selector, $params )
	        	 . JHtml::_( 'bootstrap.addSlide', $selector, $text, $id );
		}
	}
} else {
	
	// JCckDevAccordion (Joomla! 2.5 legacy)
	abstract class JCckDevAccordion
	{
		// end
		public static function end()
		{
			return JHtml::_( 'sliders.end' );
		}
		
		// open
		public static function open( $selector, $id, $text )
		{
			return JHtml::_( 'sliders.panel', $text, $id );
		}
		
		// start
		public static function start( $selector, $id, $text, $params )
		{
			unset( $params['active'] );

			return JHtml::_( 'sliders.start', $selector, $params )
				 . JHtml::_( 'sliders.panel', $text, $id );
		}
	}	
}
?>