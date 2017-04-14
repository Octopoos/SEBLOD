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
class plgCCK_FieldDiv extends JCckPluginField
{
	protected static $type		=	'div';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
		$data['display']	=	1;
	}

	// onCCK_FieldConstruct_SearchContent
	public static function onCCK_FieldConstruct_SearchContent( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']		=	NULL;

		parent::onCCK_FieldConstruct_SearchContent( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['label']		=	NULL;
		$data['live']		=	NULL;
		$data['match_mode']	=	NULL;
		$data['markup']		=	NULL;
		$data['validation']	=	NULL;
		$data['variation']	=	NULL;

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_TypeContent
	public static function onCCK_FieldConstruct_TypeContent( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']		=	NULL;

		parent::onCCK_FieldConstruct_TypeContent( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['computation']	=	NULL;
		$data['label']			=	NULL;
		$data['live']			=	NULL;
		$data['markup']			=	NULL;
		$data['validation']		=	NULL;
		$data['variation']		=	NULL;
		
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Init
		$html	=	'';
		
		// Prepare
		if ( $field->bool == -1 ) {
			$class	=	( $field->markup_class ) ? ' class="'.trim( $field->markup_class ).'"' : '';
			$attr	=	$class.( ( $field->attributes ) ? ' '.$field->attributes : '' );
			$html	=	'<div'.$attr.'></div>';
		} elseif ( $field->bool == 2 ) {
			$html	=	'</div>';
		} elseif ( $field->bool == 1 ) {
			$class	=	( $field->markup_class ) ? ' class="'.trim( $field->markup_class ).'"' : '';
			$attr	=	$class.( ( $field->attributes ) ? ' '.$field->attributes : '' );
			$html	=	'</div><div'.$attr.'>';
		} else {
			$class	=	( $field->markup_class ) ? ' class="'.trim( $field->markup_class ).'"' : '';
			$attr	=	$class.( ( $field->attributes ) ? ' '.$field->attributes : '' );
			$html	=	'<div'.$attr.'>';
		}
		
		// Set
		$field->html	=	$html;
		$field->value	=	'';
		$field->label	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
		} else {
			$id		=	$field->name;
		}
		
		// Prepare
		if ( $field->bool == -1 ) {
			$class	=	( $field->markup_class ) ? ' class="'.trim( $field->markup_class ).'"' : '';
			$attr	=	$class.( ( $field->attributes ) ? ' '.$field->attributes : '' );
			$form	=	'<div'.$attr.'></div>';
		} elseif ( $field->bool == 2 ) {
			$form	=	'</div>';
		} elseif ( $field->bool == 1 ) {
			$class	=	( $field->markup_class ) ? ' class="'.trim( $field->markup_class ).'"' : '';
			$attr	=	$class.( ( $field->attributes ) ? ' '.$field->attributes : '' );
			$form	=	'</div><div'.$attr.'>';
		} else {
			$class	=	( $field->markup_class ) ? ' class="'.trim( $field->markup_class ).'"' : '';
			$attr	=	$class.( ( $field->attributes ) ? ' '.$field->attributes : '' );
			$form	=	'<div'.$attr.'>';
		}

		// Set
		$field->form	=	$form;
		$field->value	=	'';
		$field->label	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( &$field, &$config = array() )
	{
		$field->markup	=	'none';
		
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( &$field, &$config = array() )
	{
		$field->markup	=	'none';
		
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>