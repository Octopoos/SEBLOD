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
class plgCCK_Field_ValidationAjax_Availability extends JCckPluginValidation
{
	protected static $type	=	'ajax_availability';
	protected static $regex	=	'';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$and		=	'';
		$name		=	'availability_'.$fieldId;
		$validation	=	parent::g_getValidation( $field->validation_options );
		
		$extraData	=	'avTable='.str_replace( '#__', '', $validation->table ).'&avColumn='.$validation->column;
		$extraData2	=	'';

		$alert		=	self::_alert( $validation, 'alert', $config );
		$alert2		=	self::_alert( $validation, 'alert2', $config );
		$alert3		=	self::_alert( $validation, 'alert3', $config );
		$prefix		=	JCck::getConfig_Param( 'validation_prefix', '* ' );
		
		if ( isset( $validation->fieldnames ) && $validation->fieldnames ) {
			$extraData	.=	'&avWhere='.str_replace( '||', ',', $validation->fieldnames );
			$extraData2	=	'"extraDataDynamic": "#'.str_replace( '||', ',#', $validation->fieldnames ).'",';
			$and		=	self::_where( $validation->table, $validation->fieldnames, @$config['storages'][$validation->table], 'object' );
		}
		if ( isset( $field->value ) && $field->value != '' ) {
			$pk			=	(int)JCckDatabase::loadResult( 'SELECT '.$validation->key.' FROM '.$validation->table.' WHERE '.$validation->column.'="'.JCckDatabase::escape( $field->value ).'"'.$and );
			$extraData	.=	'&avKey='.$validation->key.'&avPk='.$pk.'&avPv='.htmlspecialchars( str_replace( array( '<', '>', "'" ), array( '%26lt;', '%26gt;', '%27' ), $field->value ) );
		}
		$extraData	.=	'&avInvert='.(int)$validation->do;
		
		$rule		=	'
					"'.$name.'":{
						"url": "'.JCckDevHelper::getAbsoluteUrl( 'auto', 'task=ajax&format=raw&file=/plugins/cck_field_validation/ajax_availability/assets/ajax/script.php' ).'",
						"extraData": "'.$extraData.'",
						'.$extraData2.'
						"alertText": "'.$prefix.$alert.'",
						"alertTextOk": "'.$prefix.$alert2.'",
						"alertTextLoad": "'.$prefix.$alert3.'"}
						';
		
		$config['validation'][$name]	=	$rule;
		$field->validate[]				=	'ajax['.$name.']';
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		$error		=	false;
		$validation	=	parent::g_getValidation( $field->validation_options );
		
		if ( $value != '' ) {
			if ( isset( $validation->fieldnames ) && $validation->fieldnames ) {
				parent::g_addProcess( 'beforeStore', self::$type, $config, array( 'name'=>$name, 'value'=>$value, 'validation'=>$validation ) );
			} else {
				$do	=	true;
				// Check if table = object (todo: will be improved later..)
				if ( $validation->table == '#__users' ) {
					$type	=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$config['type'].'"' );
					
					if ( $type != 'joomla_user' ) {
						$do	=	false;
					}
				}
				if ( $do !== false ) {
					$error	=	self::_check( $validation, $value, $config );
				}
			}
		}
		
		if ( $error ) {
			self::_setError( $name, $config );
		}
	}
	
	// _alert
	protected static function _alert( $validation, $target, $config )
	{
		if ( isset( $validation->$target ) && $validation->$target != '' ) {
			$alert	=	$validation->$target;
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
		} else {
			static $already	=	0;
			if ( !$already ) {
				JFactory::getLanguage()->load( 'plg_cck_field_validation_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
				$already	=	1;
			}
			$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_'.$target );
		}
		
		return $alert;
	}

	// _check
	protected static function _check( $validation, $value, $config, $and = '' )
	{
		if ( $config['pk'] > 0 ) {
			$count	=	(int)JCckDatabase::loadResult( 'SELECT '.$validation->key.' FROM '.$validation->table
					.	' WHERE '.$validation->column.' = "'.JCckDatabase::escape( $value ).'"'.$and );
			if ( $count > 0 && $count != $config['pk'] ) {
				$error	=	true;
			}
		} else {
			$count	=	(int)JCckDatabase::loadResult( 'SELECT COUNT('.$validation->column.') FROM '.$validation->table
					.	' WHERE '.$validation->column.' = "'.JCckDatabase::escape( $value ).'"'.$and );
			if ( $count > 0 ) {
				$error	=	true;
			}
		}

		return $error;
	}

	// _setError
	protected static function _setError( $name, &$config )
	{
		$app	=	JFactory::getApplication();
		$lang	=	JFactory::getLanguage();
		$lang->load( 'plg_cck_field_validation_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
		
		$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT' ) .' - '. $name ;
		
		$app->enqueueMessage( $alert, 'error' );
		$config['validate']	=	'error';
	}

	// _where
	protected static function _where( $table, $fieldnames, $values, $method = 'array' )
	{
		$and		=	'';
		$fields		=	JCckDatabase::loadObjectList( 'SELECT name, storage, storage_table, storage_field FROM #__cck_core_fields WHERE name IN ("'.str_replace( '||', '","', $fieldnames ).'")', 'name' );
		$s_fields	=	array();
		$where		=	explode( '||', $fieldnames );
		if ( $method == 'object' ) {
			foreach ( $where as $w ) {
				if ( isset( $fields[$w] ) && $fields[$w]->storage == 'standard' && $fields[$w]->storage_table == $table ) {
					$s_field	=	$fields[$w]->storage_field;
					$v			=	isset( $values->$s_field ) ? $values->$s_field : '';
					if ( $v != '' && !isset( $s_fields[$s_field] ) ) {
						$s_fields[$s_field]	=	'';
						$and				.=	' AND '.$s_field.'="'.JCckDatabase::escape( $v ).'"';
					}
				}
			}
		} else {
			foreach ( $where as $w ) {
				if ( isset( $fields[$w] ) && $fields[$w]->storage == 'standard' && $fields[$w]->storage_table == $table ) {
					$v		=	$values[$w]->value;
					if ( $v != '' && !isset( $s_fields[$s_field] ) ) {
						$s_fields[$s_field]	=	'';
						$and				.=	' AND '.$values[$w]->storage_field.'="'.JCckDatabase::escape( $v ).'"';
					}
				}
			}
		}

		return $and;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events

	// onCCK_Field_ValidationBeforeStore
	public static function onCCK_Field_ValidationBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
		$validation	=	$process['validation'];
		$and		=	self::_where( $validation->table, $validation->fieldnames, $fields );
		$error		=	self::_check( $process['validation'], $process['value'], $config, $and );

		if ( $error ) {
			self::_setError( $process['name'], $config );
		}
	}
}
?>