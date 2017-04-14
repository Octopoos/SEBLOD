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
class plgCCK_Field_RestrictionCck_Workflow extends JCckPluginRestriction
{
	protected static $type	=	'cck_workflow';
	
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
		$action		=	$restriction->get( 'action', '' );
		$author		=	$restriction->get( 'author', '' );
		$location	=	$restriction->get( 'location', '' );
		$type		=	$restriction->get('form', '');
		
		if ( $action ) {
			if ( ( $action == 'add' && !$config['isNew'] )
			  || ( $action == 'edit' && $config['isNew'] ) ) {
				$field->display	=	0;
				return false;
			}
		}

		if ( $author ) {
			$user	=	JFactory::getUser();
			
			if ( ( $author  == '1' && $config['author'] != $user->id )
			  || ( $author  == '-1' && $config['author'] == $user->id ) ) {
				$field->display	=	0;
				return false;
			}
		}
		
		if ( $type ) {
			if ( $type != $config['type'] ) {
				$field->display	=	0;
				return false;
			}
		}

		if ( $location ) {
			if ( !JFactory::getApplication()->{'is'.$location}() ) {
				$field->display	=	0;
				return false;
			}
		}

		return true;
	}
}
?>