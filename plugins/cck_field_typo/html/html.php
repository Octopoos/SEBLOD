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
class plgCCK_Field_TypoHtml extends JCckPluginTypo
{
	protected static $type	=	'html';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		
		// Set
		if ( $typo->get( 'html', '' ) == 'clear' ) {
			$field->display	=	0;
			$field->type	=	'';
		} else {
			if ( $field->typo_label ) {
				$field->label	=	self::_typo( $typo, $field, '', $config );
			}
			$field->typo		=	self::_typo( $typo, $field, '', $config );
		}
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$html	=	$typo->get( 'html', '' );

		if ( !( strpos( $html, '<a href' ) !== false || strpos( $html, '*html*' ) !== false || strpos( $html, '*link*' ) !== false || strpos( $html, 'getLink' ) !== false ) ) {
			$html		=	parent::g_hasLink( $field, $typo, $html );
		}
		if ( $html != '' ) {
			$matches	=	'';
			$search		=	'#\*([a-zA-Z0-9_]*)\*#U';
			preg_match_all( $search, $html, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $target ) {
					if ( isset( $field->$target ) ) {
						if ( is_array( $field->$target ) ) {
							$html	=	str_replace( '*'.$target.'*', ( ( isset( $field->{$target}[0] ) ) ? $field->{$target}[0] : '' ), $html );
						} else {
							$html	=	str_replace( '*'.$target.'*', $field->$target, $html );
						}	
					}
				}
			}
		}
		if ( $html != '' && strpos( $html, '$cck->get' ) !== false ) {
			$matches	=	'';
			$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,\[\]]*)\' ?\)(;)?#';
			preg_match_all( $search, $html, $matches );
			if ( count( $matches[1] ) ) {
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches ) );
			}
		}
		if ( $html != '' && strpos( $html, '$uri->get' ) !== false ) {
			$matches	=	'';
			$search		=	'#\$uri\->get([a-zA-Z]*)\( ?\'?([a-zA-Z0-9_]*)\'? ?\)(;)?#';
			preg_match_all( $search, $html, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $k=>$v ) {
					$variable	=	$matches[2][$k];
					if ( $v == 'Current' ) {
						$request	=	( $variable == 'true' ) ? JUri::getInstance()->toString() : JUri::current();
						$html		=	str_replace( $matches[0][$k], $request, $html );						
					} else {
						$request	=	'get'.$v;
						$html		=	str_replace( $matches[0][$k], $app->input->$request( $variable, '' ), $html );
					}
				}
			}
		}
		if ( $html != '' && strpos( $html, 'J(' ) !== false ) {
			$matches	=	'';
			$search		=	'#J\((.*)\)#U';
			preg_match_all( $search, $html, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $text ) {
					$html	=	str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $html );
				}
			}
		}
		
		return $html;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_TypoBeforeRenderContent
	public static function onCCK_Field_TypoBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		if ( count( $process['matches'][1] ) ) {
			foreach ( $process['matches'][1] as $k=>$v ) {
				$fieldname		=	$process['matches'][2][$k];
				$idx			=	'';
				$search			=	'';
				$target			=	strtolower( $v );
				$value			=	'';
				
				if ( strpos( $fieldname, ',' ) !== false ) {
					$fieldname	=	explode( ',', $fieldname );

					if ( count( $fieldname ) == 3 ) {
						if ( $fieldname[1] == '[x]' ) {
							static $x	= array();

							$idx		=	$config['id'].'_'.$fieldname[0].'_'.$fieldname[2];

							if ( !isset( $x[$idx] ) ) {
								$x[$idx]	=	0;
							}
							$fieldname[1]	=	$x[$idx];
							$search			=	str_replace( array( "'", '$', ',', '>', '(', ')', '[', ']', '-' ), array( "\'", '\$', '\,', '\>', '\(', '\)', '\[', '\]', '-' ), $process['matches'][0][$k] );

							if ( $fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]] ) {
								$value	=	$fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]]->{$target};
							}
							$fields[$fieldname[0]]->value[$fieldname[1]][$name]->typo	=	str_replace( $process['matches'][0][$k], $value, $fields[$fieldname[0]]->value[$fieldname[1]][$name]->typo );
							$x[$idx]++;
						} else {
							if ( $fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]] ) {
								$value	=	$fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]]->{$target};
							}
						}
					} else {
						if ( $fields[$fieldname[0]]->value[$fieldname[1]] ) {
							$value	=	$fields[$fieldname[0]]->value[$fieldname[1]]->{$target};
						}
					}
				} else {
					$pos						=	strpos( $target, 'safe' );
					if ( $pos !== false && $pos == 0 ) {
						$target					=	substr( $target, 4 );
						$value					=	$fields[$fieldname]->{$target};
						$value					=	JCckDev::toSafeID( $value );
					} else {
						$value					=	$fields[$fieldname]->{$target};
					}
				}
				if ( $idx != '' && $search != '' ) {
					$fields[$fieldname[0]]->typo	=	preg_replace( '/'.$search.'/', $value, $fields[$fieldname[0]]->typo, 1 );
				} else {
					$fields[$name]->typo			=	str_replace( $process['matches'][0][$k], $value, $fields[$name]->typo );
				}
			}
		}
	}
}
?>