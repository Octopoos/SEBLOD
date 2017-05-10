<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: typo.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginTypo extends JPlugin
{
	protected static $construction	=	'cck_field_typo';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_addProcess
	public static function g_addProcess( $event, $type, &$config, $params, $priority = 3 )
	{
		if ( $event && $type ) {
			$process						=	new stdClass;
			$process->group					=	self::$construction;
			$process->type					=	$type;
			$process->params				=	$params;
			$process->priority				=	$priority;
			$config['process'][$event][]	=	$process;
		}
	}
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
	
	// g_hasLink
	public static function g_hasLink( &$field, $typo, $value, &$config = array() )
	{
		if ( isset( $field->link ) && $field->link != '' ) {
			$applyLink		=	( isset( $field->link_state ) ) ? $field->link_state : 1;

			if ( $applyLink && strpos( $value, '<a href' ) === false ) {
				$link_attr		=	( isset( $field->link_attributes ) && $field->link_attributes != '' ) ? $field->link_attributes : '';
				$link_class		=	( isset( $field->link_class ) && $field->link_class != '' ) ? 'class="'.$field->link_class.'" ' : '';
				$link_onclick	=	( isset( $field->link_onclick ) && $field->link_onclick != '' ) ? 'onclick="'.$field->link_onclick.'" ' : '';
				$link_rel		=	( isset( $field->link_rel ) && $field->link_rel != '' ) ? 'rel="'.$field->link_rel.'" ' : '';
				$link_target	=	( isset( $field->link_target ) && $field->link_target != '' ) ? 'target="'.$field->link_target.'" ' : '';
				$attr			=	trim( $link_onclick.$link_class.$link_rel.$link_target.$link_attr );
				$attr			=	( $attr != '' ) ? ' '.$attr : '';

				return '<a href="'.$field->link.'"'.$attr.'>'.$value.'</a>';
			}
		}
		
		return $value;
	}
	
	// g_getTypo
	public static function g_getTypo( $params, $format = '' )
	{
		if ( $format != '' )  {
			return JCckDev::fromJSON( $params, $format );
		} else {
			$reg	=	new JRegistry;
		
			if ( $params ) {			
				$reg->loadString( $params );
			}
			
			return $reg;
		}
	}
}
?>