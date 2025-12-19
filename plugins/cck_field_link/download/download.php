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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Plugin
class plgCCK_Field_LinkDownload extends JCckPluginLink
{
	protected static $type	=	'download';
	
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
		// Init
		$content			=	$link->get( 'content', '' );
		$content_fieldname	=	$link->get( 'content_fieldname', '' );
		$file_client		=	$link->get( 'file_client', '0' );
		$file_fieldname		=	$link->get( 'file_fieldname', '' );
		$type				=	$link->get( 'type', 'download' );

		$link_attr			=	$link->get( 'attributes', '' );
		$link_class			=	$link->get( 'class', '' );
		$link_rel			=	$link->get( 'rel', '' );
		$link_target		=	$link->get( 'target', '' );
		$link_more			=	'';

		if ( $file_client ) {
			$link_more			=	( $config['client'] == 'intro' /*|| $config['client'] == 'list' || $config['client'] == 'item'*/ ) ? '&client='.$config['client'] : '';
		}
		$link_title			=	$link->get( 'title', '' );
		$link_title2		=	$link->get( 'title_custom', '' );
		$xi					=	0;

		$itemId				=	$link->get( 'itemid', '' );
		$itemId				=	$itemId ? '&Itemid='.$itemId : '';
		
		// Set
		if ( is_array( $field->value ) ) {
			$collection			=	$field->name;
			
			foreach ( $field->value as $f ) {
				$query			=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$config['id'].' AND a.field = "'.(string)$f->name.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$xi;
				$field->hits	=	(int)JCckDatabase::loadResult( $query ); //@
				
				$link_more2		=	$link_more.'&collection='.$collection.'&xi='.$xi;
				$f->link		=	Route::_( 'index.php?option=com_cck&task='.$type.$link_more2.'&file='.$f->name.'&id='.$config['id'].$itemId );
				$f->link_class	=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );

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

				$xi++;
			}
			$field->link		=	'#'; /* TODO#SEBLOD: */
		} else {
			$collection			=	'';
			$field_name			=	( $file_fieldname ) ? $file_fieldname : $field->name;
			$pk					=	$config['id'];

			if ( $content == '2' ) {
				$field->link	=	'';
				$pk				=	0;
				
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'content_fieldname'=>$content_fieldname, 'file_fieldname'=>$field_name, 'link_more'=>$link_more, 'pk'=>$pk, 'type'=>$type ) );
			} else {
				$query			=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$pk.' AND a.field = "'.(string)$field_name.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$xi;
				$field->hits	=	(int)JCckDatabase::loadResult( $query ); //@
				$field->link	=	Route::_( 'index.php?option=com_cck&task='.$type.$link_more.'&file='.$file_fieldname.'&id='.$pk.$itemId );
			}

			$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
			$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
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

	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name		=	$process['name'];
		$fieldname	=	$process['content_fieldname'];
		$pk			=	isset( $fields[$fieldname] ) ? (int)$fields[$fieldname]->value : $process['pk'];

		if ( $pk ) {
			$fields[$name]->link	=	Route::_( 'index.php?option=com_cck&task='.$process['type'].$process['link_more'].'&file='.$process['file_fieldname'].'&id='.$pk.$itemId );
			$target					=	 $fields[$name]->typo_target;

			if ( isset( $fields[$name]->typo_mode ) && $fields[$name]->typo_mode ) {
				$target	=	'typo';
			}

			if ( $fields[$name]->link ) {
				JCckPluginLink::g_setHtml( $fields[$name], $target );
			}
			if ( $fields[$name]->typo ) {
				$html						=	( isset( $fields[$name]->html ) ) ? $fields[$name]->html : '';
				if ( strpos( $fields[$name]->typo, $fields[$name]->$target ) === false ) {
					$fields[$name]->typo	=	$html;
				} else {
					$fields[$name]->typo	=	str_replace( $fields[$name]->$target, $html, $fields[$name]->typo );
				}
			}
		}
	}
}
?>