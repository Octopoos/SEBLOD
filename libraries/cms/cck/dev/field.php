<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDevField
abstract class JCckDevField
{
	// -------- -------- -------- -------- -------- -------- -------- -------- // Get
	
	// get
	public static function get( $field, $value, &$config = array( 'doTranslation'=>1, 'doValidation'=>2 ), $inherit = array(), $override = array() )
	{
		if ( ! is_object( $field ) ) {
			$field	=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name = "'.$field.'"' ); //#
			if ( ! $field ) {
				return;
			}
		}
		$field->required_alert		=	'';
		$field->validation_options	=	'';
		$field->variation			=	'';
		$field->variation_override	=	'';
		$field->access				=	'';
		$field->restriction			=	'';
		$field->restriction_options	=	'';
		$field->computation			=	'';
		$field->computation_options	=	'';
		$field->conditional			=	'';
		$field->conditional_options	=	'';
		$field->markup				=	'';
		$field->markup_class		=	'';
		if ( count( $override ) ) {
			foreach ( $override as $k => $v ) {
				$field->$k	=	$v;
			}
		}
		if ( ! ( $field && ( @$field->storage == 'dev' && @$field->storage_field ) || $field->type == 'button_submit' ) ) {
			return '';
		}
		$name	=	$field->storage_field;
		if ( isset( $config['inherit'] ) ) {
			if ( strpos( $name, '[' ) !== false ) {
				$parts				=	explode( '[', $name );
				$inherit['name']	=	$config['inherit'].'['.$parts[0].']['.$parts[1];
			} else {
				$inherit['name']	=	$config['inherit'].'['.$name.']';
			}
		} else {
			if ( ! isset( $inherit['name'] ) ) {
				$inherit['name']	=	$name;
			}
		}
		if ( ! isset( $inherit['id'] ) ) {
			$inherit['id']		=	str_replace( array('[', ']'), array('_', ''), $name );
		}
		
		JEventDispatcher::getInstance()->trigger( 'onCCK_FieldPrepareForm', array( &$field, $value, &$config, $inherit ) );
		
		if ( $field->required ) {
			if ( trim( $field->label ) == '' ) {
				$field->required	=	'';
			}
		}

		$field->form	=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldRenderForm', array( $field, &$config ) );
		
		return $field;
	}
	
	// getAttribute
	public static function getAttribute( $fieldname, $attribute )
	{
		if ( ! $fieldname || ! $attribute ) {
			return false;
		}
		$res	=	JCckDatabase::loadResult( 'SELECT s.'.$attribute.' FROM #__cck_core_fields AS s'
									   .' WHERE s.name="'.$fieldname.'"' );
		
		return $res;
	}
	
	// getForm
	public static function getForm( $field, $value, &$config = array( 'doTranslation'=>1, 'doValidation'=>1, 'client'=>'site', 'pk'=>0 ), $override = array() )
	{
		if ( ! is_object( $field ) ) {
			$field	=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name = "'.$field.'"' ); //#
			if ( ! $field ) {
				return;
			}
		}
		$field->required_alert		=	'';
		$field->validation_options	=	'';
		$field->variation			=	'';
		$field->variation_override	=	'';
		$field->access				=	'';
		$field->restriction			=	'';
		$field->restriction_options	=	'';
		$field->computation			=	'';
		$field->computation_options	=	'';
		$field->conditional			=	'';
		$field->conditional_options	=	'';
		$field->markup				=	'';
		$field->markup_class		=	'';
		
		$inherit	=	array();

		if ( count( $override ) ) {
			foreach ( $override as $k=>$v ) {
				if ( $k == 'id' ) {
					$inherit['id']	=	$v;
				}
				$field->$k	=	$v;
			}
		}
		
		JEventDispatcher::getInstance()->trigger( 'onCCK_FieldPrepareForm', array( &$field, $value, &$config, $inherit ) );
		
		return JCck::callFunc( 'plgCCK_Field'.$field->type, 'onCCK_FieldRenderForm', $field );
	}
	
	// getObject
	public static function getObject( $fieldname, $attribute = '' )
	{
		if ( ! $fieldname ) {
			return false;
		}
		if ( $attribute ) {
			if ( is_array( $attribute ) ) {
				$req	=	'';
				foreach ( $attribute as $attrib ) {
					if ( $attrib ) {
						$req	.=	'a.'.$attrib.',';
					}
				}
				if ( $req ) {
					$req	=	substr( $req, 0, -1 );
				}
			} else {
				$req	=	'a.'.$attribute;
			}
			$join	=	'';
		} else {
			$req	=	'a.*';
		}
		$res	=	JCckDatabase::loadObject( 'SELECT '.$req.' FROM #__cck_core_fields AS a WHERE a.name="'.$fieldname.'"' ); //#
		
		return $res;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render

	// renderContent
	public static function renderContent( $field, $value = '', &$config = array( 'doTranslation'=>1, 'doValidation'=>2 ) )
	{	
		if ( ! is_object( $field ) ) {
			$field	=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name = "'.$field.'"' ); //#
			if ( ! $field ) {
				return;
			}
		}
		
		JEventDispatcher::getInstance()->trigger( 'onCCK_FieldPrepareContent', array( &$field, $value, &$config ) );
		
		return JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldRenderContent', array( $field, &$config ) );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// cleanExtended
	public static function cleanExtended( $fieldname )
	{
		if ( ( $cut = strpos( $fieldname, '[' ) ) !== false ) {
			$res	=	substr( $fieldname, $cut + 1, -1 );
		} elseif ( ( $cut = strpos( $fieldname, '(' ) ) !== false ) {
			$res	=	substr( $fieldname, $cut + 1, -1 );
		} else {
			$res	=	$fieldname;
		}
	
		return $res;
	}
	
	// split
	public static function split( $field, $fieldname, $needle = '[' )
	{
		if ( ( $cut = strpos( $fieldname, $needle ) ) !== false ) {
			$field->$fieldname			=	substr( $fieldname, 0, $cut );
			$field->{$fieldname.'2'}	=	substr( $fieldname, $cut + 1, -1 );			
		} else {
			$field->{$fieldname.'2'}	=	$field->$fieldname;
		}
	}

	// updateValue
	public static function updateValue( $context, $name, $value, &$fields = array(), &$config = array() )
	{
		$event	=	'';
		$pk		=	0;

		if ( is_array( $context ) ) {
			$event	=	$context[0];
			$pk		=	$context[1];
		} elseif ( is_numeric( $context ) ) {
			$pk		=	$context;
		} else {
			$event	=	$context;
		}

		if ( isset( $fields[$name] ) ) {
			$fields[$name]->value	=	$value;

			$query	=	'SELECT storage, storage_table, storage_field'
					.	' FROM #__cck_core_fields'
					.	' WHERE name = "'.$name.'"'
					;
			$field	=	JCckDatabase::loadObject( $query );

			if ( is_object( $field ) && $field->storage == 'standard' && $field->storage_table && $field->storage_field ) {
				if ( isset( $config['storages'] ) ) {
					$config['storages'][$field->storage_table][$field->storage_field]	=	$value;
				}

				if ( $event == 'afterStore' && $pk ) {
					JCckDatabase::execute( 'UPDATE '.$field->storage_table.' SET '.$field->storage_field.'= "'.$value.'" WHERE id = '.(int)$pk );
				}
			}
		}
	}
}
?>