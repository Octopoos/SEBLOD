<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Plugin
class plgCCK_Field_LiveDateTime extends JCckPluginLive
{
	protected static $type	=	'datetime';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array(), $inherit = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );
		$wrapper	=	$options->get( 'return_jtext', '' );
		
		// Prepare
		$doTimezone	=	(int)$options->get( 'timezone', '0' );
		$format		=	$options->get( 'format', 'Y-m-d H-i-s' );
		$modify		=	$options->get( 'modify', '' );
		$now		=	Factory::getDate()->toSql();

		if ( $format == -1 ) {
			$format	=	$options->get( 'format_custom', 'Y-m-d H-i-s' );
		} elseif ( $format == 'auto' ) {
			$format	=	'Y-m-d H:i:s';
		}
		if ( strpos( $format, 'COM_CCK_' ) !== false || strpos( $format, 'DATE_FORMAT_' ) !== false ) {
			$format	=	Text::_( $format );
		}
		if ( $doTimezone ) {
			$now	=	HTMLHelper::_( 'date', $now, 'Y-m-d H:i:s' );
		}
		if ( $modify != '' ) {
			$live	=	 Factory::getDate( $now )->modify( $modify )->format( $format );
		} else {
			$live	=	 Factory::getDate( $now )->format( $format );
		}
		
		// Set
		$value	=	( $wrapper ) ? Text::sprintf( $wrapper, $live ) : $live;
	}
}
?>