<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_TypoDate extends JCckPluginTypo
{
	protected static $type	=	'date';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{
		if ( self::$type != $field->typo ) {
			return;
		}
		self::$path	=	JURI::root().'plugins/cck_field_typo/'.self::$type.'/';
		
		// Prepare
		if ( $field->value && $field->value != '' && $field->value != '0000-00-00 00:00:00' ) {
			$typo			=	parent::g_getTypo( $field->typo_options );
			$field->typo	=	self::_typo( $typo, $field, '', $config );
		} else {
			$field->typo	=	'';
		}
		
		$field->typo		=	parent::g_hasLink( $field, $typo, $field->typo );
	}
		
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$format	=	$typo->get( 'format', 'Y-m-d' );
		$value	=	trim( $field->value );
		
		if ( $format == -2 ) {
			$typo		=	self::_getTimeAgo( $value );
		} else {
			$options2	=	JCckDev::fromJSON( $field->options2 );
			if ( $format == -1 ) {
				$format	=	trim( $typo->get( 'format_custom', @$options2['format'] ) );
			}
			$value		=	self::_getValueWithFormatStorage( $value, @$options2['storage_format'] );
			$date_eng	=	$format ? date(  $format, $value ) : $value;
			$typo	=	self::_getDateByLang( $format, $value, $date_eng );
		}
	
		return $typo;
	}

	// _getTimeAgo
	protected static function _getTimeAgo( $value )
	{
		if ( @!mktime( $value ) ) {
			return;
		}
		
		// Init
		$text_years		=	strtolower( JText::_( 'COM_CCK_YEARS' ) );
		$text_year		=	strtolower( JText::_( 'COM_CCK_YEAR' ) );
		$text_months	=	strtolower( JText::_( 'COM_CCK_MONTHS' ) );
		$text_month		=	strtolower( JText::_( 'COM_CCK_MONTH' ) );
		$text_days		=	strtolower( JText::_( 'COM_CCK_DAYS' ) );
		$text_today		=	strtolower( JText::_( 'COM_CCK_TODAY' ) );
		$text_yesterday	=	strtolower( JText::_( 'COM_CCK_YESTERDAY' ) );
		
		// Prepare
		$date1			=	new DateTime( $value );
		$date1->setTime( 00, 00, 00 );
		$date2			=	new DateTime();
		$date2->setTime( 00, 00, 00 );

		$interval		=	$date1->diff( $date2 );
		$years			=	$interval->format( '%y' );
		$months			=	$interval->format( '%m' );
		$days			=	$interval->format( '%d' );

		if ( $years != 0 ) {
			$interval	=	( $years > 1 ) ? $years.' '.$text_years : $years.' '.$text_year;
			$interval	=	JText::sprintf( 'COM_CCK_AGO_SENTENCE', $interval );
		} elseif ( $months != 0 ) {
			$interval	=	( $months > 1 ) ? $months.' '.$text_months : $months.' '.$text_month;
			$interval	=	JText::sprintf( 'COM_CCK_AGO_SENTENCE', $interval );
		} else {
			$interval	=	( $days > 1 ) ? JText::sprintf( 'COM_CCK_AGO_SENTENCE', $days.' '.$text_days ) : ( ( $days > 0 ) ? $text_yesterday : $text_today );
		}
		
		//$interval	=	( $link != '' ) ? '<a href="'.$link.'">'.$interval.'</a>' : $interval;
		//return '<span class="interval">'.$interval.'</span>';
		return $interval;
	}

	// _getValueWithFormatStorage
	protected static function _getValueWithFormatStorage( $value, $format_storage = '0' )
	{
		$format_storage	=	( isset( $format_storage ) ) ? $format_storage : '0';
		
		return ( trim ( $value ) == '' ) ? '' : (( $format_storage == '0' ) ? strtotime ( $value ) : $value );
	}
	
	// _getDateByLang
	protected static function _getDateByLang( $format, $date_n, $date_eng )
	{
		$month_short	=	date( 'M', $date_n );
		$month			=	date( 'F', $date_n );		
		$t_month_short	=	JText::_( strtoupper( $month ).'_SHORT' );
		$t_month		=	JText::_( strtoupper( $month ) );
		$day_short		=	date( 'D', $date_n );
		$t_day_short	=	JText::_( strtoupper( $day_short ) );
		$day			=	date( 'l', $date_n );
		$t_day			=	JText::_( strtoupper( $day ) );
		
		$before	=	array( $month, $day );
		$after	=	array( $t_month, $t_day );
		$date	=	str_replace( $before, $after, $date_eng );
		$before	=	array( $month_short, $day_short );
		$after	=	array( $t_month_short, $t_day_short );
		$date	=	str_replace( $before, $after, $date );

		return $date;
	}
}
?>