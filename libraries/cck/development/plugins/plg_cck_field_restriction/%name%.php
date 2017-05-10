<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_Restriction%class% extends JCckPluginRestriction
{
	protected static $type	=	'%name%';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_RestrictionPrepareContent
	public static function onCCK_Field_RestrictionPrepareContent( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}

	// onCCK_Field_RestrictionPrepareForm
	public static function onCCK_Field_RestrictionPrepareForm( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// onCCK_Field_RestrictionPrepareStore
	public static function onCCK_Field_RestrictionPrepareStore( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// _authorise
	protected static function _authorise( $restriction, &$field, &$config )
	{
		if ( 1 == 1 ) { // TODO
			return false;
		}
		
		return true;
	}
}
?>