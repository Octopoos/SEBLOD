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
class plgCCK_Field_RestrictionUrl_Variable extends JCckPluginRestriction
{
	protected static $type	=	'url_variable';
	
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
		$do					=	$restriction->get( 'do', 0 );
		$state				=	0;

		// --
		$condition_field	=	$restriction->get( 'trigger' );
		$condition_match	=	$restriction->get( 'match' );
		$condition_values	=	$restriction->get( 'values' );

		$variable			=	JFactory::getApplication()->input->get( $condition_field, null, null );

		if ( $config['client'] == 'site' /* || $config['client'] == 'admin' */ ) {
			if ( !isset( $config['context'][$condition_field] ) ) {
				$config['context'][$condition_field]	=	(string)$variable;
			}
		}

		if ( $condition_match == 'isFilled' ) {
			if ( is_array( $variable ) ) {
				foreach ( $variable as $v ) {
					if ( $v != '' ) {
						$state	=	1;
						break;
					}
				}
			} elseif ( $variable != '' ) {
				$state		=	1;
			}
		} elseif ( $condition_match == 'isEqual' ) {
			if ( isset( $variable ) ) {
				$condition_values	=	explode( ',', $condition_values );
				if ( is_array( $variable ) ) {
					if ( count( array_intersect( $condition_values, $variable ) ) ) {
						$state		=	1;
					}
				} else {
					foreach ( $condition_values as $v ) {
						if ( $variable == $v ) {
							$state		=	1;
							break;
						}  	
					}
				}	
			}
		}
		// --

		if ( $state ) {
			$do		=	( $do ) ? false : true;
		} else {
			$do		=	( $do ) ? true : false;
		}

		if ( $do ) {
			return true;
		} else {
			$field->display	=	0;
			$field->state	=	0;
			return false;
		}
	}
}
?>