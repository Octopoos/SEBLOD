<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldJform_Calendar extends JCckPluginField
{
	protected static $type		=	'jform_calendar';
	protected static $type2		=	'calendar';
	protected static $friendly	=	1;
	protected static $path;

	protected $userTimeZone		=	null;

	// __construct
	public function __construct( &$subject, $config = array() )
	{
		$this->userTimeZone	=	new DateTimeZone( JFactory::getUser()->getParam( 'timezone', JFactory::getConfig()->get( 'offset' ) ) );

		parent::__construct( $subject, $config );
	}
	
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
		
		// Set
		$value	=	trim( $value );
		if ( (int)$value == 0 ) {
			$field->value	=	'';
			$field->text	=	'';
		} else {
			$field->value	=	$value;
			$date			=	JFactory::getDate( $value, 'UTC' );
			$date->setTimezone( $this->userTimeZone );

			// Transform the date string.
			$format			=	'Y-m-d H:i:s';
			$value			=	$date->format( $format, true, true );
			$value			=	( trim( $value ) == '' ) ? '' : $value;
			$field->text	=	( $value == '' ) ? '' : $date->format( $format, true, true );
		}
		$field->typo_target	=	'text';
	}

	// onCCK_FieldPrepareExport
	public function onCCK_FieldPrepareExport( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		
		if ( (int)$value > 0 ) {
			$field->output	=	$value;
		} else {
			$field->output	=	'';
		}
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
		$options2	=	JCckDev::fromJSON( $field->options2 );
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	$value != '' ? $value : $field->defaultvalue;
		$value		=	trim( $value );
		$value		=	(int)$value == 0 ? '' : $value;
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}

		// Prepare
		$class		=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$readonly	=	( $field->variation == 'disabled' ) ? 'disabled="disabled"' : '';
		$xml		=	'
						<form>
							<field
								type="'.self::$type2.'"
								name="'.$name.'"
								id="'.$id.'"
								label="'.htmlspecialchars( $field->label ).'"
								showtime="'.( isset( $options2['time'] ) && $options2['time'] ? 'true' : 'false' ).'"
								todaybutton="'.( ( isset( $options2['today'] ) && $options2['today'] ) || !isset( $options2['today'] ) ? 'true' : 'false' ).'"
								weeknumbers="'.( isset( $options2['week_numbers'] ) && $options2['week_numbers'] ? 'true' : 'false' ).'"
								translateformat="true"
								filter="user_utc"
								class="'.$class.'"
								'.$readonly.'
							/>
						</form>
					';
		$form	=	JForm::getInstance( $id, $xml );
		$form	=	$form->getInput( $name, '', $value );
		
		if ( JFactory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
			$form	=	str_replace( 'class="field-calendar"', 'class="field-calendar raw"', $form );
			$form	.=	self::_addScript();

			self::_addScripts();
		}

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
		$value	=	trim( $value );

		if ( (int)$value > 0 ) {
			$date			=	JFactory::getDate( $value, $this->userTimeZone );
			$timezone		=	new DateTimeZone( 'UTC' );
			$date->setTimezone( $timezone );
			$value	=	$date->toSql();
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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		$value	=	trim( $value );
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );

		if ( (int)$value > 0 ) {
			$date			=	JFactory::getDate( $value, $this->userTimeZone );
			$timezone		=	new DateTimeZone( 'UTC' );
			$date->setTimezone( $timezone );
			$value	=	$date->toSql();
		}
		
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
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _addScript
	protected static function _addScript()
	{
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}

		$js		=	'
					!(function(window, document){
						"use strict";
							var elements, i;

							elements = document.querySelectorAll(".field-calendar.raw");

							for (i = 0; i < elements.length; i++) {
								JoomlaCalendar.init(elements[i]);
							}
						})(window, document);
					';
		$loaded	=	1;

		return '<script>'.$js.'</script>';
	}

	// _addScripts
	protected static function _addScripts()
	{
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}

		$loaded	=	1;
		$root	=	JUri::root( true );

		echo '<link rel="stylesheet" href="'.$root.'/media/system/css/fields/calendar.css" type="text/css" />';
		echo '<script src="'.$root.'/media/system/js/fields/calendar-locales/en.js" type="text/javascript"></script>';
		echo '<script src="'.$root.'/media/system/js/fields/calendar-locales/date/gregorian/date-helper.js" type="text/javascript"></script>';
		echo '<script src="'.$root.'/media/system/js/fields/calendar.js" type="text/javascript"></script>';
	}

	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>