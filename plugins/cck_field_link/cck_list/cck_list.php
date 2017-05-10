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
class plgCCK_Field_LinkCCK_List extends JCckPluginLink
{
	protected static $type	=	'cck_list';
	
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
		$app			=	JFactory::getApplication();
		$list			=	$link->get( 'list', '' );
		$itemId			=	(int)$link->get( 'itemid', '' );
		$search_field	=	$link->get( 'search_field', 0 );
		$name			=	( $search_field == -1 ) ? '' : ( ( $search_field == 1 ) ? $link->get( 'search_fieldname', $field->name ) : $field->name );
		$custom			=	$link->get( 'custom', '' );
		
		if ( ! $list ) {
			return;
		}
		
		// Prepare
		$link_attr		=	'';
		$link_class		=	$link->get( 'class', '' );
		$link_rel		=	$link->get( 'rel', '' );
		$link_target	=	$link->get( 'target', '' );
		$tmpl			=	$link->get( 'tmpl', '' );
		$tmpl			=	( $tmpl == '-1' ) ? $app->input->getCmd( 'tmpl', '' ) : $tmpl;
		$tmpl			=	( $tmpl ) ? '&tmpl='.$tmpl : '';
		$vars			=	'&task=search'.$tmpl;
		
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
		if ( isset( $field->values ) ) {
			foreach ( $field->values as $f ) {
				$c				=	$custom;
				$c				=	parent::g_getCustomSelfVars( self::$type, $f, $c, $config );
				$c				=	$c ? '&'.$c : '';
				$search_for		=	( $search_field > -1 ) ? '&'.$name.'='.$f->value : '';
				
				$f->link		=	'index.php?option=com_cck&view=list&search='.$list.$vars.'&Itemid='.$itemId.$search_for.$c;
				if ( $itemId > 0 ) {
					$f->link	=	JRoute::_( $f->link );
				}
				$f->link_class	=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );
				$f->link_rel	=	$link_rel ? $link_rel : ( isset( $f->link_rel ) ? $f->link_rel : '' );
				$f->link_state	=	$link->get( 'state', 1 );
				$f->link_target	=	$link_target ? ( $link_target == 'modal' ? '' : $link_target ) : ( isset( $f->link_target ) ? $f->link_target : '' );
			}
			$field->link		=	'#';	//todo
		} elseif ( is_array( $field->value ) ) {
			foreach ( $field->value as $f ) {
				$c				=	$custom;
				$c				=	parent::g_getCustomSelfVars( self::$type, $f, $c, $config );
				$c				=	$c ? '&'.$c : '';
				$search_for		=	( $search_field > -1 ) ? '&'.$name.'='.$f->value : '';
				
				$f->link		=	'index.php?option=com_cck&view=list&search='.$list.$vars.'&Itemid='.$itemId.$search_for.$c;
				if ( $itemId > 0 ) {
					$f->link	=	JRoute::_( $f->link );
				}
				$f->link_class	=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );
				$f->link_rel	=	$link_rel ? $link_rel : ( isset( $f->link_rel ) ? $f->link_rel : '' );
				$f->link_state	=	$link->get( 'state', 1 );
				$f->link_target	=	$link_target ? ( $link_target == 'modal' ? '' : $link_target ) : ( isset( $f->link_target ) ? $f->link_target : '' );
			}
			$field->link		=	'#';	//todo
		} else {
			$custom				=	parent::g_getCustomVars( self::$type, $field, $custom, $config );
			$search_for			=	( $search_field > -1 ) ? '&'.$name.'='.$field->value : '';
			$field->link		=	'index.php?option=com_cck&view=list&search='.$list.$vars.'&Itemid='.$itemId.$search_for;
			if ( $itemId > 0 ) {
				$field->link	=	JRoute::_( $field->link );
			}
			$separator			=	( strpos( $field->link, '?' ) !== false ) ? '&' : '?';
			if ( $custom ) {
				$field->link	.=	$separator.$custom;
			}

			$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
			$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
			$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
			$field->link_state		=	$link->get( 'state', 1 );
			$field->link_target		=	$link_target ? ( $link_target == 'modal' ? '' : $link_target ) : ( isset( $field->link_target ) ? $field->link_target : '' );
		}
	}
}
?>