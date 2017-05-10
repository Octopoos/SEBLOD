<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: link.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginLink extends JPlugin
{
	protected static $construction	=	'cck_field_link';
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		if ( count( $process['matches'][1] ) ) {
			self::g_setCustomVars( $process, $fields, $name );
		}
	}
	
	// onCCK_Field_LinkBeforeRenderForm
	public static function onCCK_Field_LinkBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		if ( count( $process['matches'][1] ) ) {
			self::g_setCustomVars( $process, $fields, $name );
		}
	}
	
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
	
	// g_getCustomSelfVars
	public static function g_getCustomSelfVars( $type, $field, $custom, &$config = array() )
	{
		if ( $custom != '' && strpos( $custom, '*' ) !== false ) {
			$matches	=	'';
			$search		=	'#\*([a-zA-Z0-9_]*)\*#U';
			preg_match_all( $search, $custom, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $target ) {
					$custom	=	str_replace( '*'.$target.'*', $field->$target, $custom );
				}
			}
		}
		
		return $custom;
	}
	
	// g_getCustomVars
	public static function g_getCustomVars( $type, $field, $custom, &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$event	=	( $config['client'] == 'admin' || $config['client'] == 'site' || $config['client'] == 'search' ) ? 'beforeRenderForm' : 'beforeRenderContent';
		if ( $custom != '' && strpos( $custom, '*' ) !== false ) {
			$matches	=	'';
			$search		=	'#\*([a-zA-Z0-9_]*)\*#U';
			preg_match_all( $search, $custom, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $target ) {
					$custom	=	str_replace( '*'.$target.'*', $field->$target, $custom );
				}
			}
		}
		if ( $custom != '' && strpos( $custom, '$cck->get' ) !== false ) {
			$matches	=	'';
			$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
			preg_match_all( $search, $custom, $matches );
			if ( count( $matches[1] ) ) {
				self::g_addProcess( $event, $type, $config, array( 'name'=>$field->name, 'fieldname'=>'', 'location'=>'', 'matches'=>$matches ) );
			}
		}
		if ( $custom != '' && strpos( $custom, '$uri->get' ) !== false ) {
			$matches	=	'';
			$search		=	'#([a-zA-Z0-9_]*)=\$uri\->get([a-zA-Z]*)\( ?\'?([a-zA-Z0-9_]*)\'? ?\)(;)?#';
			preg_match_all( $search, $custom, $matches );
			
			if ( count( $matches[2] ) ) {
				foreach ( $matches[2] as $k=>$v ) {
					$variable	=	$matches[3][$k];
					
					if ( $v == 'Current' ) {
						$request	=	( $variable == 'true' ) ? JUri::getInstance()->toString() : JUri::current();
						$custom		=	str_replace( $matches[0][$k], $matches[1][$k].'='.$request, $custom );						
					} elseif ( $v == 'Array' ) {
						$name				=	$field->name;
						$value				=	'';
						$custom_v			=	'';
						static $custom_vars	=	array();
						
						if ( !isset( $custom_vars[$name] ) ) {
							$custom_vars[$name]	=	explode( '&', $custom );
						}
						if ( count( $custom_vars[$name] ) ) {
							foreach ( $custom_vars[$name] as $custom_var ) {
								if ( strpos( $custom_var, $matches[0][$k] ) !== false ) {
									$custom_v	=	substr( $custom_var, 0, strpos( $custom_var, '=' ) );
								}
							}
						}
						if ( $custom_v != '' ) {
							$values		=	$app->input->get( $variable, '', 'array' );
							if ( is_array( $values ) && count( $values ) ) {
								foreach ( $values as $val ) {
									$value	.=	'&'.$custom_v.'[]='.$val;
								}
								$value	=	substr( $value, 1 );
							}
						}
						$pos		=	strpos( $custom, '&'.$matches[0][$k] );
						
						if ( $value == '' ) {
							$pre	=	( $pos !== false ) ? '&' : '';
						} else {
							$pre	=	( $pos !== false && $pos == 0 ) ? '&' : '';
						}
						$custom		=	str_replace( $pre.$matches[0][$k], $value, $custom );
					} else {
						$pos		=	strpos( $custom, '&'.$matches[0][$k] );
						$request	=	'get'.$v;
						$result		=	urlencode( $app->input->$request( $variable, '' ) );
						
						if ( $result == '' ) {
							$pre	=	( $pos !== false ) ? '&' : '';
							$custom	=	str_replace( $pre.$matches[0][$k], '', $custom );
						} else {
							$pre	=	( $pos !== false && $pos == 0 ) ? '&' : '';
							$custom	=	str_replace( $pre.$matches[0][$k], $matches[1][$k].'='.$result, $custom );
						}
					}
				}
			}
		}
		
		return $custom;
	}
	
	// g_setCustomVars
	public static function g_setCustomVars( $process, &$fields, $name )
	{
		foreach ( $process['matches'][1] as $k=>$v ) {
			$fieldname					=	$process['matches'][2][$k];
			$target						=	strtolower( $v );
			$pos						=	strpos( $target, 'safe' );
			if ( $pos !== false && $pos == 0 ) {
				$target					=	substr( $target, 4 );
				$replace				=	$fields[$fieldname]->{$target};
				$replace				=	JCckDev::toSafeID( $replace );
			} else {
				$replace				=	urlencode( $fields[$fieldname]->{$target} );
			}
			$fields[$name]->link        =	str_replace( $process['matches'][0][$k], $replace, $fields[$name]->link );
			if ( isset( $fields[$name]->form ) ) {
				$fields[$name]->form	=	str_replace( $process['matches'][0][$k], $replace, $fields[$name]->form );
			}
			if ( isset( $fields[$name]->html ) ) {
				$fields[$name]->html	=	str_replace( $process['matches'][0][$k], $replace, $fields[$name]->html );
			}
			if ( isset( $fields[$name]->typo ) ) {
				$fields[$name]->typo	=	str_replace( $process['matches'][0][$k], $replace, $fields[$name]->typo );
			}
		}
	}
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
	
	// g_getLink
	public static function g_getLink( $params, $format = '' )
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
	
	// g_setLink
	public static function g_setLink( &$field, &$config = array() )
	{
		if ( !$field->link ) {
			return;
		}
		
		require_once JPATH_PLUGINS.'/cck_field_link/'.$field->link.'/'.$field->link.'.php';
		JCck::callFunc_Array( 'plgCCK_Field_Link'.$field->link, 'onCCK_Field_LinkPrepareContent', array( &$field, &$config ) );
	}
	
	// g_setHtml
	public static function g_setHtml( &$field, $target = '' )
	{
		if ( is_array( $field->value ) ) {
			foreach ( $field->value as $f ) {
				if ( is_object( $f ) ) {
					if ( isset( $f->link ) ) {
						$target			=	$f->typo_target;

						$link_onclick	=	( isset( $f->link_onclick ) && $f->link_onclick != '' ) ? 'onclick="'.$f->link_onclick.'" ' : '';
						$link_attr		=	( isset( $f->link_attributes ) && $f->link_attributes != '' ) ? $f->link_attributes : '';
						$link_class		=	( isset( $f->link_class ) && $f->link_class != '' ) ? 'class="'.$f->link_class.'" ' : '';
						$link_rel		=	( isset( $f->link_rel ) && $f->link_rel != '' ) ? 'rel="'.$f->link_rel.'" ' : '';
						$link_target	=	( isset( $f->link_target ) && $f->link_target != '' ) ? 'target="'.$f->link_target.'" ' : '';
						$link_title		=	( isset( $f->link_title ) && $f->link_title != '' ) ? 'title="'.$f->link_title.'" ' : '';
						$attr			=	trim( $link_onclick.$link_class.$link_rel.$link_target.$link_title.$link_attr );
						$attr			=	( $attr != '' ) ? ' '.$attr : '';
						
						$f->html		=	( $f->$target != '' ) ? '<a href="'.$f->link.'"'.$attr.'>'.$f->$target.'</a>' : '';
					}
				}
			}
		} elseif ( isset( $field->values ) && count( $field->values ) ) {
			$html	=	'';
			foreach ( $field->values as $f ) {
				if ( is_object( $f ) ) {
					if ( isset( $f->link ) ) {
						$target			=	$f->typo_target;

						$link_onclick	=	( isset( $f->link_onclick ) && $f->link_onclick != '' ) ? 'onclick="'.$f->link_onclick.'" ' : '';
						$link_attr		=	( isset( $f->link_attributes ) && $f->link_attributes != '' ) ? $f->link_attributes : '';
						$link_class		=	( isset( $f->link_class ) && $f->link_class != '' ) ? 'class="'.$f->link_class.'" ' : '';
						$link_rel		=	( isset( $f->link_rel ) && $f->link_rel != '' ) ? 'rel="'.$f->link_rel.'" ' : '';
						$link_target	=	( isset( $f->link_target ) && $f->link_target != '' ) ? 'target="'.$f->link_target.'" ' : '';
						$link_title	=	( isset( $f->link_title ) && $f->link_title != '' ) ? 'title="'.$f->link_title.'" ' : '';
						$attr			=	trim( $link_onclick.$link_class.$link_rel.$link_target.$link_title.$link_attr );
						$attr			=	( $attr != '' ) ? ' '.$attr : '';
						
						$f->html		=	( $f->$target != '' ) ? '<a href="'.$f->link.'"'.$attr.'>'.$f->$target.'</a>' : '';
						$html			.=	$f->html.', ';
					}
				}
			}
			$field->html	=	$html ? substr( $html, 0, -2 ) : '';
		} else {
			$applyLink		=	( isset( $field->link_state ) ) ? $field->link_state : 1;
			
			if ( $applyLink ) {
				$link_attr		=	( isset( $field->link_attributes ) && $field->link_attributes != '' ) ? $field->link_attributes : '';
				$link_class		=	( isset( $field->link_class ) && $field->link_class != '' ) ? 'class="'.$field->link_class.'" ' : '';
				$link_onclick	=	( isset( $field->link_onclick ) && $field->link_onclick != '' ) ? 'onclick="'.$field->link_onclick.'" ' : '';
				$link_rel		=	( isset( $field->link_rel ) && $field->link_rel != '' ) ? 'rel="'.$field->link_rel.'" ' : '';
				$link_target	=	( isset( $field->link_target ) && $field->link_target != '' ) ? 'target="'.$field->link_target.'" ' : '';
				$link_title		=	( isset( $field->link_title ) && $field->link_title != '' ) ? 'title="'.$field->link_title.'" ' : '';
				$attr			=	trim( $link_onclick.$link_class.$link_rel.$link_target.$link_title.$link_attr );
				$attr			=	( $attr != '' ) ? ' '.$attr : '';

				$field->html	=	( $field->$target != '' ) ? '<a href="'.$field->link.'"'.$attr.'>'.$field->$target.'</a>' : '';
			} else {
				$field->html	=	( $field->$target != '' ) ? $field->$target : '';
			}
		}
	}
}
?>