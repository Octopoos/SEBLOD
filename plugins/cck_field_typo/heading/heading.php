<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_TypoHeading extends JCckPluginTypo
{
	protected static $type	=	'heading';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{		
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$value	=	parent::g_hasLink( $field, $typo, $field->$target );
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$rank	=	$typo->get( 'rank', 3 );
		$anchor	=	$typo->get( 'anchor', 0 );
		$class	=	$typo->get( 'class', '' );
		$class	=	$class ? ' class="'.$class.'"' : '';
		$custom	=	$typo->get( 'custom', '' );
		$custom	=	$custom ? ' '.$custom : '';
		
		if ( $anchor ) {
			$anchor	=	str_replace( array( '&', '"', '<', '>' ), array( 'a', 'q', 'l', 'g' ), $field->value );
			$anchor	=	trim( preg_replace( array( '/\s+/', '/[^A-Za-z0-9_]/' ), array( '_', '' ), $anchor ) );
			$anchor	=	' id="'.$anchor.'"';
		} else {
			$anchor	=	'';
		}
		
		return '<h'.$rank.$class.$anchor.$custom.'>'.$value.'</h'.$rank.'>';
	}
}
?>