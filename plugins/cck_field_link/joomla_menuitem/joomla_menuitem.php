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
class plgCCK_Field_LinkJoomla_Menuitem extends JCckPluginLink
{
	protected static $type	=	'joomla_menuitem';
	
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
		$custom			=	$link->get( 'custom', '' );
		$itemId			=	$link->get( 'itemid', '' );
		$redirection	=	$link->get( 'redirection', '' );
		
		// Prepare
		if ( !$itemId ) {
			$itemId			=	$app->input->getInt( 'Itemid', 0 );
		} elseif ( (int)$itemId == -2 ) {
			$itemId	=	JCckDatabaseCache::loadResult( 'SELECT id FROM #__menu WHERE parent_id = '.(int)$app->input->getInt( 'Itemid', 0 ).' ORDER BY lft ASC' );

			if ( !$itemId ) {
				return;
			}
		}
		$link_attr			=	$link->get( 'attributes', '' );
		$link_class			=	$link->get( 'class', '' );
		$link_rel			=	$link->get( 'rel', '' );
		$link_target		=	$link->get( 'target', '' );
		$link_title			=	$link->get( 'title', '' );
		$link_title2		=	$link->get( 'title_custom', '' );
		$tmpl				=	$link->get( 'tmpl', '' );
		$tmpl				=	( $tmpl == '-1' ) ? $app->input->getCmd( 'tmpl', '' ) : $tmpl;
		$tmpl				=	( $tmpl ) ? 'tmpl='.$tmpl : '';
		$vars				=	$tmpl;
		$custom				=	parent::g_getCustomVars( self::$type, $field, $custom, $config );

		if ( $link_target == 'modal' ) {
			if ( strpos( $link_attr, 'data-cck-modal' ) === false ) {
				$modal_json	=	$link->get( 'target_params', '' );

				if ( $modal_json != '' ) {
					$modal_json	=	'=\''.$modal_json.'\'';
				}
				$link_attr	=	trim( $link_attr.' data-cck-modal'.$modal_json );				
			}
		}
		
		// Set
		$field->link		=	Route::_( 'index.php?Itemid='.$itemId );
		
		if ( $field->link ) {
			$link_link	=	$field->link;
			$len		=	strlen( $field->link );

			if ( $link_link[($len - 1)] == '/' ) {
				$field->link	=	substr( $link_link, 0, -1 );
			}

			if ( $vars ) {
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
			}
			if ( $redirection == 'current' ) {
				$uri			=	Uri::getInstance()->toString();
				$return			=	base64_encode( $uri );
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&return='.$return : '?return='.$return;
			}
			if ( $custom ) {
				if ( $custom[0] == '#' ) {
					$field->link	.=	$custom;
				} else {
					$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$custom : '?'.$custom;
				}				
			}
		}

		$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
		$field->link_state		=	$link->get( 'state', 1 );
		$field->link_target		=	$link_target ? ( $link_target == 'modal' ? '' : $link_target ) : ( isset( $field->link_target ) ? $field->link_target : '' );

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
?>