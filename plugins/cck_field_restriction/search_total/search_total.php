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

// Plugin
class plgCCK_Field_RestrictionSearch_Total extends JCckPluginRestriction
{
	protected static $type	=	'search_total';
	
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
		if ( $config['client'] == 'search' ) {
			parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'restriction'=>$restriction ) );
		} elseif ( $config['client'] == 'list' ) {
			$diff	=	(int)$restriction->get( 'values', 0 );
			$do		=	$restriction->get( 'do', 0 );

			if ( $config['total'] > $diff ) {
				$do	=	( $do ) ? false : true;
			} else {
				$do	=	( $do ) ? true : false;
			}

			if ( $do ) {
				return true;
			} else {
				$field->display	=	0;
				$field->state	=	0;
				return false;
			}
		} else {
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'restriction'=>$restriction ) );
		}

		return true;
	}

	// _authoriseBeforeEvent
	protected static function _authoriseBeforeEvent( $process, &$fields, &$storages, &$config = array() )
	{
		$name			=	$process['name'];
		$restriction	=	$process['restriction'];

		$diff	=	(int)$restriction->get( 'values', 0 );
		$do		=	$restriction->get( 'do', 0 );

		if ( $config['total'] > $diff ) {
			$do	=	( $do ) ? false : true;
		} else {
			$do	=	( $do ) ? true : false;
		}

		if ( $do ) {
			return true;
		} else {
			$fields[$name]->display	=	0;
			$fields[$name]->state	=	0;
			return false;
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events

	// onCCK_Field_RestrictionBeforeRenderContent
	public static function onCCK_Field_RestrictionBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeEvent( $process, $fields, $storages, $config );
	}

	// onCCK_Field_RestrictionBeforeRenderForm
	public static function onCCK_Field_RestrictionBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		return self::_authoriseBeforeEvent( $process, $fields, $storages, $config );
	}
}
?>