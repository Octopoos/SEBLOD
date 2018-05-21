<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// CommonHelper
class CommonHelper_Form
{
	// getMediaExtensions
	public static function getMediaExtensions( &$field, $value, $name, $id, $config )
	{
		$field->attributes	=	'style="width:90px;"';
		
		$value	=	( $value != '' ) ? $value : 'common';
		
		if ( $field->options ) {
			$options	=	explode( '||', $field->options );
		} else {
			$options	=	array( 'archive', 'audio', 'document', 'image', 'video' );
		}
		
		$opts  	=	array();
		$opts[]	=	JHtml::_( 'select.option', 'common', JText::_ ( 'COM_CCK_MEDIA_TYPE_COMMON' ), 'value', 'text' );
		$opts[]	=	JHtml::_( 'select.option', 'custom', JText::_( 'COM_CCK_CUSTOM' ) );
		$opts[]	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MEDIA_TYPES' ) );
		
		foreach ( $options AS $o ) {
			$opts[]	=	JHtml::_( 'select.option', $o, JText::_ ( 'COM_CCK_MEDIA_TYPE_'.$o ), 'value', 'text' );
		}

		$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
		$opts[]	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_PRESETS' ) );
		
		for ( $i = 1; $i <= 3; $i++ ) {
			if ( JCck::getConfig_Param( 'media_preset'.$i.'_extensions' ) ) {
				$label 	=	JCck::getConfig_Param( 'media_preset'.$i.'_extensions_label' );
				$label 	=	$label ? $label : JText::_( 'COM_CCK_PRESET'.$i );
				$opts[]	=	JHtml::_( 'select.option', 'preset'.$i, $label );
			}
		}
		
		$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
		
		return JHtml::_( 'select.genericlist', $opts, $name, 'class="inputbox select" '.$field->attributes, 'value', 'text', $value, $id );
	}

	// getPlugins
	public static function getPlugins( &$field, $value, $name, $id, $config )
	{
		$type		=	( $field->location ) ? $field->location : 'field';
		$options	=	array();
		
		if ( trim( $field->selectlabel ) ) {
			$options[]	=	JHtml::_( 'select.option', '', '- '.$field->selectlabel.' -', 'value', 'text' );
		}

		$options	=	array_merge( $options, Helper_Admin::getPluginOptions( $type, 'cck_', false, false, true ) );
		$css		=	( $field->required == 'required' ) ? ' validate[required]' : '';
		
		return JHtml::_( 'select.genericlist', $options, $name, 'class="inputbox select'.$css.'" '.$field->attributes, 'value', 'text', $value, $id );
	}

	// getTables
	public static function getTables( &$field, $value, $name, $id, $config )
	{
		$field->label		=	'Table';
		$field->attributes	=	'style="max-width:200px;"';
		
		$opts		=	array();
		$prefix		=	JFactory::getConfig()->get( 'dbprefix' );
		$tables		=	JCckDatabase::loadColumn( 'SHOW TABLES' );
		
		if ( trim( $field->selectlabel ) ) {
			$opts[]	=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -' );
		}
		
		if ( count( $tables ) ) {
			foreach ( $tables as $table ) {
				$t		=	str_replace( $prefix, '#__', $table );
				$opts[]	=	JHtml::_( 'select.option', $t, $t, 'value', 'text' );
			}
		}
		$class	=	$field->css ? ' '.$field->css : '';
		$attr	=	'class="inputbox select'.$class.'" '.$field->attributes;
		
		return JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
	}
}
?>