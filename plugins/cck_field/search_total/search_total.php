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
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

// Plugin
class plgCCK_FieldSearch_Total extends JCckPluginField
{
	protected static $type		=	'search_total';
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
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['live']		=	null;
		$data['match_mode']	=	null;
		$data['validation']	=	null;
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
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
		$form		=	'';
		$value		=	'';
		
		// Prepare
		$options2	=	new Registry( $field->options2 );
		parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'id'=>$id, 'name'=>$name, 'jtext'=>$options2->get( 'jtext', '' ), 'alternative'=>(int)$options2->get( 'alternative', '0' ) ) );

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
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
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$alt	=	$process['alternative'];
		$id		=	$process['id'];
		$name	=	$process['name'];
		$jtext	=	$process['jtext'];
		
		if ( ( isset( $config['total'] ) && $config['total'] > 0 ) || $fields[$name]->defaultvalue != '' ) {
			if ( $config['total'] < $config['limitend'] ) {
				$step				=	$config['total'];
			} else {
				$step				=	( ( $diff = $config['total'] - $config['limitstart'] ) < $config['limitend'] ) ? $diff : $config['limitend'];
			}
			$start					=	$config['limitstart'] + 1;
			$end					=	$config['limitstart'] + $step;
			$end					=	( $config['total'] < $end ) ? $config['total'] : $end;

			if ( $jtext != '' ) {
				if ( $alt ) {
					if ( $config['total'] == 1 && Factory::getLanguage()->hasKey( $jtext.'_1' ) ) {
						$jtext	.=	'_1';
					} elseif ( $config['total'] == 0 && Factory::getLanguage()->hasKey( $jtext.'_0' ) ) {
						$jtext	.=	'_0';
					}
				}
				$fields[$name]->form	=	Text::sprintf( $jtext, $config['total'], $step, $start, $end );
			} else {
				$fields[$name]->form	=	$config['total'];
			}
			$fields[$name]->value	=	$config['total'];
		}
	}
}
?>