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
		self::$path	=	JUri::root().'plugins/cck_field_typo/'.self::$type.'/';
		
		// Prepare
		if ( $field->value && $field->value != '' && $field->value != '0000-00-00 00:00:00' ) {
			$typo			=	parent::g_getTypo( $field->typo_options );
			$field->typo	=	self::_typo( $typo, $field, '', $config );
		} else {
			$field->typo	=	'';
		}
		
		$field->typo		=	parent::g_hasLink( $field, $typo, $field->typo );
		$field->typo_mode	=	1;
	}
		
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$format		=	$typo->get( 'format', 'Y-m-d' );
		$language	=	$typo->get( 'language', '' );
		$timezone	=	(int)$typo->get( 'timezone', '1' );
		$value		=	trim( $field->value );
		
		if ( $language ) {
			$tag	=	JFactory::getLanguage()->getTag();

			if ( $tag != $language ) {
				JCckDevHelper::setLanguage( $language );
			}
		}
		if ( $format == -2 ) {
			$typo		=	self::_getTimeAgo( $value, $typo->get( 'unit', '' ), $typo->get( 'alt_format', '' ), $typo->get( 'format2', 'Y-m-d' ) );
		} else {
			$options2	=	JCckDev::fromJSON( $field->options2 );
			
			if ( $format == -1 ) {
				$format	=	trim( $typo->get( 'format_custom', @$options2['format'] ) );
			}
			if ( strpos( $format, 'COM_CCK_' ) !== false || strpos( $format, 'DATE_FORMAT_' ) !== false ) {
				$format	=	JText::_( $format );
			}
			if ( $timezone ) {
				if ( !( isset( $options2['storage_format'] ) && $options2['storage_format'] ) ) {
					$value	=	JHtml::_( 'date', $value, 'Y-m-d H:i:s' );
				}
			}
			$value		=	self::_getValueWithFormatStorage( $value, @$options2['storage_format'] );
			$date_eng	=	$format ? date(  $format, $value ) : $value;
			$typo		=	self::_getDateByLang( $format, $value, $date_eng );
		}
		if ( $language ) {
			JCckDevHelper::setLanguage( $tag );
		}
	
		return $typo;
	}

	// _getTimeAgo
	protected static function _getTimeAgo( $value, $unit, $limit, $alt_format )
	{
		if ( @!mktime( $value ) ) {
			return;
		}
		
		// Init
		$date1			=	new DateTime( $value, new DateTimeZone('UTC') );
		$date1->setTime( 00, 00, 00 );
		$now			=	new DateTime( 'now', new DateTimeZone('UTC') );
		$now->setTime( 00, 00, 00 );

		$interval		=	$date1->diff( $now );
		$years			=	$interval->format( '%y' );
		$months			=	$interval->format( '%m' );
		$days			=	$interval->format( '%d' );
        $state			=	( $date1 < $now ) ? 'COM_CCK_AGO_SENTENCE' : 'COM_CCK_TIMELEFT_SENTENCE';

		if ( $limit && $days >= $limit ) {
			$value		=	self::_getValueWithFormatStorage( $value );
			$date_eng	=	$alt_format ? date(  $alt_format, $value ) : $value;
			$interval	=	self::_getDateByLang( $alt_format, $value, $date_eng );
		} else {			
			// Prepare
			if ( $years != 0 ) {
				$text_years		=	strtolower( JText::_( 'COM_CCK_YEARS' ) );
				$text_year		=	strtolower( JText::_( 'COM_CCK_YEAR' ) );

				$interval		=	( $years > 1 ) ? $years.' '.$text_years : $years.' '.$text_year;
				$interval		=	JText::sprintf( $state, $interval );
			} elseif ( $months != 0 ) {
				$text_months	=	strtolower( JText::_( 'COM_CCK_MONTHS' ) );
				$text_month		=	strtolower( JText::_( 'COM_CCK_MONTH' ) );

				$interval		=	( $months > 1 ) ? $months.' '.$text_months : $months.' '.$text_month;
				$interval		=	JText::sprintf( $state, $interval );
			} else {
				if ( $days > 1 ) {
					$interval	=	JText::sprintf( $state, $days.' '.strtolower( JText::_( 'COM_CCK_DAYS' ) ) );
				} elseif ( $days > 0 ) {
					$interval	=	JText::_( 'COM_CCK_YESTERDAY' );
				} else {
					if ( !$unit ) {
						$interval		=	JText::_( 'COM_CCK_TODAY' );
					} else {
						$date1			=	new DateTime( $value, new DateTimeZone('UTC') );
						$now			=	new DateTime( 'now', new DateTimeZone('UTC') );
						$interval		=	$date1->diff( $now );
						$hours			=	$interval->format( '%h' );
						$minutes		=	$interval->format( '%i' );
						if ( $unit == 2 ) {
							$minutes		+=	( $hours * 60 );
							$text_minutes	=	strtolower( JText::_( 'COM_CCK_MINUTES' ) );
							$text_minute	=	strtolower( JText::_( 'COM_CCK_MINUTE' ) );

							if ( $minutes == 0 ) {
								$interval	=	JText::_( 'COM_CCK_JUST_NOW' );
							} elseif ( $minutes < 60 ) {
								$interval	=	( $minutes > 1 ) ? $minutes.' '.$text_minutes : $minutes.' '.$text_minute;
								$interval	=	JText::sprintf( $state, $interval );
							} else {
								$unit		=	1;
							}
						}
						if ( $unit == 1 ) {
							$text_hours		=	strtolower( JText::_( 'COM_CCK_HOURS' ) );
							$text_hour		=	strtolower( JText::_( 'COM_CCK_HOUR' ) );

							if ( $hours == 0 ) {
								$interval	=	JText::_( 'COM_CCK_JUST_NOW' );
							} else {
								$interval	=	( $hours > 1 ) ? $hours.' '.$text_hours : $hours.' '.$text_hour;
								$interval	=	JText::sprintf( $state, $interval );
							}
						}
					}
				}
			}
		}
		
		return $interval;
	}

	// _getValueWithFormatStorage
	protected static function _getValueWithFormatStorage( $value, $format_storage = '0' )
	{
		$format_storage	=	( isset( $format_storage ) ) ? $format_storage : '0';
		
		return ( trim ( $value ) == '' ) ? '' : ( ( $format_storage == '0' ) ? strtotime ( $value ) : $value );
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
