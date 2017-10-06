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
class plgCCK_FieldJForm_UserGroups extends JCckPluginField
{
	protected static $type		=	'jform_usergroups';
	protected static $type2		=	'usergroups';
	protected static $friendly	=	1;
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Prepare
		$text	=	'';
		if ( is_array( $value ) ) {
			$value	=	implode( ',', $value );
		}
		if ( $value != '' ) {
			$texts	=	JCckDatabase::loadColumn( 'SELECT a.title FROM #__usergroups AS a WHERE id IN ('.$value.') ORDER BY FIELD(id, '.$value.')' );
			$text	=	implode( ',', $texts );
		}

		// Set
		$field->text		=	$text;
		$field->value		=	$value;

		$values				=	explode( ',', $value );
		if ( count( $values ) ) {
			$field->values			=	array();
			foreach ( $values as $k=>$v ) {
				$field->values[$k]	=	(object)array( 'text'=>$texts[$k], 'typo_target'=>'text', 'value'=>$v );
			}
		}
		$field->typo_target	=	'text';
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
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		if ( $config['client'] == 'admin' && ! $config['pk'] && !$value ) {
			$value	=	array( 2 );
		} elseif ( $value && is_string( $value ) ) {
			if ( strpos( $value, '[' ) !== false && $value[0] == '[' ) {
				$value	=	substr( $value, 1, -1 );
			}
			if ( strpos( $value, ',' ) !== false ) {
				$value	=	explode( ',', $value );
			}
		} elseif ( is_null( $value ) ) {
			$value	=	$field->defaultvalue;
		}
		if ( ! is_array( $value ) ) {
			$value	=	array( $value );
		}
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	( $field->css ) ? ' class="'.$field->css.'"' : '';
		$form	=	JHtml::_( 'access.usergroups', $name, $value );		// JForm UserGroups ?!
		$form	=	'<div id="'.$name.'"'.$class.'>'.$form.'</div>';

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$values			=	( is_array( $value ) ) ? implode( ',', $value ) : $value;

			if ( $values != '' ) {
				$field->text	=	JCckDatabase::loadColumn( 'SELECT title FROM #__usergroups WHERE id IN ('.(string)$values.')' );
				$field->text	=	implode( ',', $field->text ); //todo	
			} else {
				$field->text	=	'';
			}
			parent::g_getDisplayVariation( $field, $field->variation, $values, $field->text, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
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
		if ( is_array( $value ) ) {
			$value	=	implode( ',', $value );
		}
		$isMultiple	=	( strpos( $value, ',' ) !== false ) ? 1 : 0;

		if ( $value != '' ) {
			if ( $field->storage_location != '' ) {
				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$field->storage_location.'/'.$field->storage_location.'.php';
				$properties	=	array( 'key', 'table' );
				$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$field->storage_location, 'getStaticProperties', $properties );

				$field->storage_location	=	'free';
				$field->storage_table		=	'#__user_usergroup_map';
				$field->storage_field		=	'group_id';
				$field->storage_field2		=	'';

				$join						=	new stdClass;
				$join->table				=	'#__user_usergroup_map';
				$join->column				=	'user_id';
				$join->column2				=	$properties['key'];
				$join->table2				=	$properties['table'];
				$join->and					=	'';

				$config['joins'][$field->stage][]		=	$join;

				if ( $isMultiple ) {
					$config['query_parts']['group'][]	=	't0.id';
				}
			}
		} else {
			$field->storage					=	'none';
			$field->storage_location		=	'';
			$field->storage_table			=	'';
			$field->storage_field2			=	'';
		}
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );

		$divider			=	$field->match_value ? $field->match_value : ',';
		$field->match_value	=	$divider;
		if ( is_array( $field->value ) ) {
			$field->value	=	implode( $divider, $field->value );
			$value			=	$field->value;
		}

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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'text' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{		
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>