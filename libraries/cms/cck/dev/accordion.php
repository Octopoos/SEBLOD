<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: accordion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

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
?>