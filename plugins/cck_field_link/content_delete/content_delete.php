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
class plgCCK_Field_LinkContent_Delete extends JCckPluginLink
{
	protected static $type	=	'content_delete';
	
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
		$form			=	$config['type'];
		$id				=	( isset( $config['id'] ) ) ? $config['id'] : 0;
		$itemId			=	$link->get( 'itemid', $app->input->getInt( 'Itemid', 0 ) );
		$redirection	=	$link->get( 'redirection', '' );
		$return			=	'';
		$task			=	( JFactory::getApplication()->isClient( 'administrator' ) ) ? 'list.delete' : 'delete';
		$uri			=	JUri::getInstance()->toString();
		
		// Return
		if ( $redirection == 'url' ) {
			$url		=	$link->get( 'redirection_url', '' );
			if ( strpos( $url, 'Itemid=' ) !== false ) {
				$url	=	JRoute::_( $url );
			} elseif ( $url && ( strpos( $url, 'http' ) === false ) ) {
				$url	=	JUri::base().( ( $url[0] == '/' ) ? substr( $url, 1 ) : $url );
			}
			$return		=	base64_encode( $url );
		} elseif ( $config['client'] == 'content' ) {
			$menu		=	JFactory::getApplication()->getMenu(); /* JMenu::getInstance( 'site' ); */
			$item		=	$menu->getItem( $itemId );

			if ( $item->query['view'] == 'article' ) {
				$return	=	'';
			} else {
				$return	=	JRoute::_( 'index.php?Itemid='.$itemId );
				$return	=	base64_encode( JUri::base().( ( $return[0] == '/' ) ? substr( $return, 1 ) : $return ) );
			}
		} else {
			$return2	=	$link->get( 'redirection_custom', '' );
			if ( $return2 != '' ) {
				if ( $return2[0] == '#' ) {
					$uri	.=	$return2;
				} else {
					$uri	.=	( strpos( $return2, '?' ) !== false ? '&' : '?' ).$return2;
				}
			}
			$return		=	base64_encode( $uri );
		}

		// Check
		$user 			=	JCck::getUser();
		$canDelete		=	$user->authorise( 'core.delete', 'com_cck.form.'.$config['type_id'] );
		$canDeleteOwn	=	$user->authorise( 'core.delete.own', 'com_cck.form.'.$config['type_id'] );
		if ( ( !$canDelete && !$canDeleteOwn ) ||
			 ( !$canDelete && $canDeleteOwn && $config['author'] != $user->id ) ||
			 ( $canDelete && !$canDeleteOwn && $config['author'] == $user->id ) ) {
			if ( !$link->get( 'no_access', 0 ) ) {
				$field->display	=	0;
			}
			return;
		}
		
		// Prepare
		$link_class		=	$link->get( 'class', '' );
		$link_title		=	$link->get( 'title', '' );
		$link_title2	=	$link->get( 'title_custom', '' );
		
		$field->link		=	'index.php?option=com_cck&task='.$task.'&cid='.$id.'&Itemid='.$itemId.( $return ? '&return='.$return : '' );
		$field->link		=	JRoute::_( $field->link );
		$field->link_class	=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		if ( $link->get( 'confirm', 1 ) ) {
			$field->link_onclick	=	'if(!confirm(\''.addslashes( JText::_( 'COM_CCK_CONFIRM_DELETE' ) ).'\')){return false;}';
		}
		if ( $config['client'] == 'search' ) {
			$field->link	=	'';

			if ( isset( $field->link_onclick ) ) {
				$field->link_onclick	.=	'else{'.htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="return" value="'.$return.'">\');' )
										.	'JCck.Core.submitForm(\'delete\', document.getElementById(\''.$config['formId'].'\'));}';
			} else {
				$field->link_onclick	=	'JCck.Core.submitForm(\'delete\', document.getElementById(\''.$config['formId'].'\'));';
			}
			$field->link_onclick	=	'if (document.'.$config['formId'].'.boxchecked.value==0){alert(\''.htmlspecialchars( addslashes( JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ) ) ).'\');}else{'.$field->link_onclick.'}';
		}
		$field->link_state	=	$link->get( 'state', 1 );
		
		if ( $link_title ) {
			if ( $link_title == '2' ) {
				$field->link_title	=	$link_title2;
			} elseif ( $link_title == '3' ) {
				$field->link_title	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $link_title2 ) ) );
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