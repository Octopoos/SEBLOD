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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgCCK_Field_LinkCustom extends JCckPluginLink
{
	protected static $type	=	'custom';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		$app			=	Factory::getApplication();
		$custom			=	$link->get( 'custom', '#' );
		$itemId			=	$link->get( 'itemid', 0 );
		$link_attr		=	$link->get( 'attributes', '' );
		$link_class		=	$link->get( 'class', '' );
		$link_rel		=	$link->get( 'rel', '' );
		$link_target	=	$link->get( 'target', '' );
		$link_title		=	$link->get( 'title', '' );
		$link_title2	=	$link->get( 'title_custom', '' );
		$tmpl			=	$link->get( 'tmpl', '' );
		$tmpl			=	( $tmpl == '-1' ) ? $app->input->getCmd( 'tmpl', '' ) : $tmpl;
		$tmpl			=	( $tmpl ) ? 'tmpl='.$tmpl : '';
		$vars			=	$tmpl;
		
		if ( is_array( $field->value ) ) {
			foreach ( $field->value as $f ) {
				// Prepare
				$c	=	$custom;
				if ( $c != '#' && strpos( $c, '*' ) !== false ) {
					$matches	=	'';
					$search		=	'#\*([a-zA-Z0-9_]*)\*#U';
					preg_match_all( $search, $c, $matches );
					if ( count( $matches[1] ) ) {
						foreach ( $matches[1] as $target ) {
							$c	=	str_replace( '*'.$target.'*', $f->$target, $c );
						}
					}
				}
				
				// Set
				$f->link		=	$c;
				if ( $vars ) {
					$f->link	.=	( strpos( $f->link, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
				}
				$f->link_attributes	=	$link_attr ? $link_attr : ( isset( $f->link_attributes ) ? $f->link_attributes : '' );
				$f->link_class		=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );
				$f->link_rel		=	$link_rel ? $link_rel : ( isset( $f->link_rel ) ? $f->link_rel : '' );
				$f->link_state		=	$link->get( 'state', 1 );
				$f->link_target		=	$link_target ? ( $link_target == '-1' ? '_blank' : $link_target ) : ( isset( $f->link_target ) ? $f->link_target : '' );

				if ( $link_title ) {
					if ( $link_title == '2' ) {
						$f->link_title	=	$link_title2;
					} elseif ( $link_title == '3' ) {
						$f->link_title	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $link_title2 ) ) );
					}
					if ( !isset( $f->link_title ) ) {
						$f->link_title	=	'';
					}
				} else {
					$f->link_title		=	'';
				}
			}
			$field->link		=	'#'; /* TODO#SEBLOD: */
		} else {
			// Prepare
			if ( $custom != '#' && strpos( $custom, '*' ) !== false ) {
				$matches	=	'';
				$search		=	'#\*([a-zA-Z0-9_]*)\*#U';
				preg_match_all( $search, $custom, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $target ) {
						$custom	=	str_replace( '*'.$target.'*', $field->$target, $custom );
					}
				}
			}
			if ( $custom != '#' && strpos( $custom, '$cck->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';
				preg_match_all( $search, $custom, $matches );
				if ( count( $matches[1] ) ) {
					parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches, 'itemId'=>$itemId, 'target_fieldname'=>( $link_target == '-1' ? $link->get( 'target_fieldname', '' ) : '' ) ) );
				}
			} elseif ( $link_target == '-1' ) {
				$matches	=	array();
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches, 'itemId'=>$itemId, 'target_fieldname'=>( $link_target == '-1' ? $link->get( 'target_fieldname', '' ) : '' ) ) );
			}
			if ( $custom != '' && strpos( $custom, '$uri->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$uri\->get([a-zA-Z]*)\( ?\'?([a-zA-Z0-9_]*)\'? ?\)(;)?#';
				preg_match_all( $search, $custom, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$variable	=	$matches[2][$k];
						if ( $v == 'Current' || $v == 'CurrentUrl' || $v == 'EncodedUrl' ) {
							if ( $v == 'CurrentUrl' || $v == 'EncodedUrl' || $variable == 'true' ) { 
                                $request = Uri::getInstance()->toString();
                            } else {
                                $request = Uri::current();
                            }
                            if ( $v == 'EncodedUrl' ) {
                                $request=   urlencode( $request );
                            }
							$custom		=	str_replace( $matches[0][$k], $request, $custom );	
						} elseif ( $v == 'Array' ) {
							$value				=	'';
							$custom_v			=	'';
							static $custom_vars	=	array();
							if ( !isset( $custom_vars[$field->name] ) ) {
								$custom_vars[$field->name]	=	explode( '&', $custom );
							}
							if ( count( $custom_vars[$field->name] ) ) {
								foreach ( $custom_vars[$field->name] as $custom_var ) {
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
								}
							}
							$custom		=	str_replace( '&'.$custom_v.'='.$matches[0][$k], $value, $custom );
						} else {
							$request	=	'get'.$v;
							$custom		=	str_replace( $matches[0][$k], $app->input->$request( $variable, '' ), $custom );
						}
					}
				}
			}
			
			// Set
			$pos	=	strpos( $custom, 'Itemid=' );

			if ( $pos !== false && strpos( $custom, 'Itemid=$' ) === false ) {
				$segment	=	'';
				$pos2		=	strpos( $custom, '/' );

				if ( $pos2 !== false && $pos2 > $pos ) {
					$parts		=	explode( '/', $custom );
					$segment	=	$parts[1];
					$custom		=	$parts[0];
				}
				$custom		=	Route::_( $custom );

				if ( $segment != '' ) {
					$custom	.=	'/'.$segment;
				}
			}
			$field->link	=	$custom;

			if ( $vars ) {
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
			}
			$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
			$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
			$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
			$field->link_state		=	$link->get( 'state', 1 );
			$field->link_target		=	$link_target ? ( $link_target == '-1' ? '_blank' : $link_target ) : ( isset( $field->link_target ) ? $field->link_target : '' );

			if ( $link_title ) {
				if ( $link_title == '2' ) {
					$field->link_title	=	$link_title2;
				} elseif ( $link_title == '3' ) {
					$field->link_title	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $link_title2 ) ) );
				}
				if ( !isset( $field->link_title ) ) {
					$field->link_title	=	'';
				}
			} else {
				$field->link_title		=	'';
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		if ( isset( $process['matches'][1] ) && count( $process['matches'][1] ) ) {
			foreach ( $process['matches'][1] as $k=>$v ) {
				$fieldname		=	$process['matches'][2][$k];
				$target			=	strtolower( $v );
				$value			=	'';
				if ( strpos( $fieldname, ',' ) !== false ) {
					$fieldname	=	explode( ',', $fieldname );
					if ( count( $fieldname ) == 3 ) {
						if ( $fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]] ) {
							$value	=	$fields[$fieldname[0]]->value[$fieldname[1]][$fieldname[2]]->$target;
						}
					} else {
						if ( $fields[$fieldname[0]]->value[$fieldname[1]] ) {
							$value	=	$fields[$fieldname[0]]->value[$fieldname[1]]->$target;
						}
					}
				} else {
					$value	=	$fields[$fieldname]->$target;
				}

				if ( $value && $process['itemId'] ) {
					$value	.=	( strpos( $value, '?' ) !== false ) ? '&Itemid='.$process['itemId'] : '?Itemid='.$process['itemId'];
					$value	=	Route::_( $value );

				}

				$fields[$name]->link        =	str_replace( $process['matches'][0][$k], $value, $fields[$name]->link );

				if ( isset( $fields[$name]->html ) ) {
					$fields[$name]->html		=	str_replace( $process['matches'][0][$k], $value, $fields[$name]->html );
				}

				if ( isset( $fields[$name]->typo ) ) {
					$fields[$name]->typo	=	str_replace( $process['matches'][0][$k], $value, $fields[$name]->typo );
				}
			}
		}

		$pre_link	=	$fields[$name]->link;

		if ( strpos( $fields[$name]->link, 'Itemid=' ) !== false ) {
			$segment	=	'';

			if ( strpos( $fields[$name]->link, '/' ) !== false ) {
				$parts		=	explode( '/', $fields[$name]->link );
				$segment	=	$parts[1];
				$fields[$name]->link		=	$parts[0];
				$fields[$name]->link		=	Route::_( $fields[$name]->link );

				if ( $segment != '' ) {
					$fields[$name]->link	.=	'/'.$segment;

					$fields[$name]->html		=	str_replace( $pre_link, $fields[$name]->link, $fields[$name]->html );

					if ( isset( $fields[$name]->typo ) ) {
						$fields[$name]->typo	=	str_replace( $pre_link, $fields[$name]->link, $fields[$name]->typo );
					}
				}
			}
		}

		if ( $process['target_fieldname'] != '' ) {
			$link_target				=	( isset( $fields[$process['target_fieldname']] ) ) ? $fields[$process['target_fieldname']]->value : '';

			if ( $link_target != '' && $link_target != '_blank' ) {

				if ( $fields[$name]->link_rel ) {
					$rel	=	trim( JCck::getConfig_Param( 'link_rel_blank', 'noopener noreferrer' ) );
					$rel	=	explode( ' ', $rel );

					if ( count( $rel ) ) {
						$link_rel	=	explode( ' ', $fields[$name]->link_rel );
						$rel		=	array_diff( $link_rel, $rel );
						$rel		=	implode( ' ', $rel );
						$link_rel	=	trim( $rel );
						$link_rel	=	$link_rel ? ' rel="'.$link_rel.'"' : '';
					}
				}
				$fields[$name]->html		=	str_replace( 'target="_blank"', 'target="'.$link_target.'"', $fields[$name]->html );

				if ( isset( $link_rel ) ) {
					$fields[$name]->html	=	str_replace( ' rel="'.$fields[$name]->link_rel.'"', $link_rel, $fields[$name]->html );
				}

				if ( isset( $fields[$name]->typo ) ) {
					$fields[$name]->typo	=	str_replace( 'target="_blank"', 'target="'.$link_target.'"', $fields[$name]->typo );

					if ( isset( $link_rel ) ) {
						$fields[$name]->typo	=	str_replace( ' rel="'.$fields[$name]->link_rel.'"', $link_rel, $fields[$name]->typo );
					}
				}
			}
		}
	}
}
?>