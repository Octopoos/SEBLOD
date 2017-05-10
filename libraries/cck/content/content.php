<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: content.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// CCK_Content
class CCK_Content
{
	// getRegex
	public static function getRegex()
	{
		$res	=	'#::(.*?)::(.*?)::/(.*?)::#s';
		
		return $res;
	}
	
	// getRegex_Field
	public static function getRegex_Field( $fieldname )
	{
		$res	=	'#::'.$fieldname.'::(.*?)::/'.$fieldname.'::#s';
		
		return $res;
	}
	
	// getRegex_Group
	public static function getRegex_Group( $fieldname, $groupname, $gx = '(.*?)' )
	{
		$res	=	'#::'.$fieldname.'\|'.$gx.'\|'.$groupname.'::(.*?)::/'.$fieldname.'\|'.$gx.'\|'.$groupname.'::#s';
		
		return $res;
	}
	
	// getSyntax
	public static function getSyntax( $fieldname, $value )
	{
		$res	=	'::'.$fieldname.'::'.$value.'::/'.$fieldname.'::';
		
		return $res;
	}
	
	// getSyntax_Group
	public static function getSyntax_Group( $fieldname, $groupname, $value, $gx = '(.*?)' )
	{
		$res	=	'#::'.$fieldname.'\|'.$gx.'\|'.$groupname.'::'.$value.'::/'.$fieldname.'\|'.$gx.'\|'.$groupname.'::#s';
		
		return $res;
	}
	
	// getText
	public static function getText( $text )
	{
		$regex	=	CCK_Content::getRegex();
		preg_match_all( $regex, $text, $res );
		
		return $res;
	}
	
	// getValue
	public static function getValue( $text, $fieldname )
	{
		$res	=	'';
		
		$regex	=	CCK_Content::getRegex_Field( $fieldname );
		preg_match( $regex, $text, $matches );
		
		if ( count( $matches ) ) {
			$res	=	$matches[1];
		}
		
		return $res;
	}
	
	// getValues
	public static function getValues( $text, $fieldnames = '' )
	{
		$res	=	array();
		
		//TODO:: if $fieldnames
		
		$regex	=	CCK_Content::getRegex();
		preg_match_all( $regex, $text, $matches );
		
		if ( count( $matches[1] ) ) {
			foreach ( $matches[1] as $key => $val ) {
				$res[$val]	=	$matches[2][$key];
			}
		}
		
		return $res;
	}
	
	// prepare
	public static function prepare( &$row = null, $name = 'description', $params = null )
	{
		if ( $row === null ) {
			return;
		}
		if ( $params === null ) {
			$params	=	new JObject;
		}
		
		JPluginHelper::importPlugin( 'content' );
		
		$row->text	=	'';
		if ( isset( $row->$name ) ) {
			$row->text	=	$row->$name;
		}
		$results	=	JEventDispatcher::getInstance()->trigger( 'onContentPrepare', array ( 'com_content.category', &$row, &$params, 0 ) );
		
		return $row->text;
	}
	
	// setValue
	public static function setValue( $text, $fieldname, $value, $old_value = '' )
	{	
		$res	=	$text;
		$search	=	'';
		
		if ( $old_value ) {
			$search	=	CCK_Content::getSyntax( $fieldname, $old_value );
		} else {
			$regex	=	CCK_Content::getRegex_Field( $fieldname );
			preg_match( $regex, $text, $matches );
			if ( count( $matches ) ) {
				$search	=	$matches[0];
			}
		}
		if ( $search ) {
			$replace	=	CCK_Content::getSyntax( $fieldname, $value );
			if ( strpos( $text, $search ) !== false ) {
				$res	=	str_replace( $search, $replace, $text );
			}
		}
		
		return $res;
	}

	// setValues
	public static function setValues( $text, $fieldnames, $values, $old_values = '' )
	{	
		$res			=	$text;
		$n_fieldnames	=	count( $fieldnames );
		$n_values		=	count( $values );
		$n_old_values	=	count( $old_values );
		
		if ( is_array( $old_values ) ) {
			if ( ( $n_fieldnames == $n_values ) && ( $n_fieldnames == $n_old_values ) ) {
				for ( $i = 0 ; $i < $n_fieldnames; $i++ ) {
					$res	=	CCK_Content::setValue( $res, $fieldnames[$i], $values[$i], $old_values[$i] );
				}
			}
		} else {
			if ( $n_fieldnames == $n_values ) {
				for ( $i = 0; $i < $n_fieldnames; $i++ ) {
					$res	=	CCK_Content::setValue( $res, $fieldnames[$i], $values[$i], $old_values );
				}
			}
		}		
		
		return $res;
	}
}
?>