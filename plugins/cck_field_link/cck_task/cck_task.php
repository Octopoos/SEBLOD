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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgCCK_Field_LinkCck_Task extends JCckPluginLink
{
	protected static $type	=	'cck_task';
	
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
		$link_class		=	$link->get( 'class', '' );
		$link_title		=	$link->get( 'title', '' );
		$link_title2	=	$link->get( 'title_custom', '' );
		$task			=	$link->get( 'task', '' );

		static $i		=	0;
		static $pks		=	array();
		$pk				=	$config['pk'];
		if ( !isset( $pks[$pk] ) ) {
			$pks[$pk]	=	$i;
			$i++;
		}

		if ( !$task ) {
			return;
		} elseif ( !( $task == 'impersonate' || $task == 'toggle' ) ) {
			$task_id	=	$link->get( 'task_id_'.$task, '' );

			if ( !$task_id ) {
				return;
			}
		}

		$uri		=	Uri::getInstance()->toString();
		$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="return" value="'.base64_encode( $uri ).'">\');' )
					.	htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\''.HTMLHelper::_( 'form.token' ).'\');' );
		$user 		=	JCck::getUser();

		// Check
		if ( $task == 'impersonate' ) {
			$task_id	=	$pk;

			if ( !$user->authorise( 'core.admin', 'com_users' ) ) {
				if ( !$link->get( 'no_access', 0 ) ) {
					$field->display	=	0;
				}
				return;
			}
		} elseif ( $task == 'toggle' ) {
			$task_id	=	$config['id'];
		} else {
			if ( !$user->authorise( 'core.'.$task, 'com_cck.form.'.$config['type_id'] ) ) {
				if ( !$link->get( 'no_access', 0 ) ) {
					$field->display	=	0;
				}
				return;
			}
		}

		// $field->onclick	=	'';
		$field->link		=	'javascript: '.$pre_task.'JCck.Core.submitTask(\''.$task.'\','.$task_id.',\'cb'.($i - 1).'\',\''.$config['formId'].'\');';
		$field->link_class	=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		
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