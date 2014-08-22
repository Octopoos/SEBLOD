<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tabs.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on( '3.1' ) ) {
	
	// JCckDevAccordion
	abstract class JCckDevTabs
	{
		// end
		public static function end()
		{
			return JHtml::_( 'bootstrap.endTab' )
				 . JHtml::_( 'bootstrap.endTabSet' );
		}
		
		// open
		public static function open( $selector, $id, $text )
		{
			return JHtml::_( 'bootstrap.endTab' )
				 . JHtml::_( 'bootstrap.addTab', $selector, $id, $text );
		}
		
		// start
		public static function start( $selector, $id, $text, $params )
		{
	        return JHtml::_( 'bootstrap.startTabSet', $selector, $params )
	        	 . JHtml::_( 'bootstrap.addTab', $selector, $id, $text );
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