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
class plgCCK_Field_LinkCCK_Form extends JCckPluginLink
{
	protected static $type	=	'cck_form';
	
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
		$custom			=	$link->get( 'custom', '' );
		$edit			=	(int)$link->get( 'form_edition', 1 );
		$form			=	$link->get( 'form', '' );

		if ( !$form ) {
			if ( (int)$edit == 1 ) {
				$edit	=	'&id='.$config['pk'];
			} elseif ( $edit == 2 ) {
				$edit	=	'&copyfrom_id='.$config['pk'];
			} else {
				$edit	=	'';
			}
		} else {
			$edit		=	'';
		}
		$form			=	( $form ) ? $form : $config['type'];
		$itemId			=	$link->get( 'itemid', $app->input->getInt( 'Itemid', 0 ) );
		$redirection	=	$link->get( 'redirection', '' );
		$uri			=	JUri::getInstance()->toString();

		if ( strpos( $uri, 'format=raw&infinite=1' ) !== false ) {
			$return		=	$app->input->get( 'return' );
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
		
		if ( !( $form ) ) {
			return;
		}
		
		// Check
		if ( $edit != '' ) {
			static $cache	=	array();
			$stage			=	$link->get( 'form_edition_stage', '' );
			if ( $stage != '' ) {
				$edit		.=	'&stage='.(int)$stage;
			}
			$user 				=	JCck::getUser();
			$canEdit			=	$user->authorise( 'core.edit', 'com_cck.form.'.$config['type_id'] );
			// if ( $user->id && !$user->guest ) {
				$canEditOwn		=	$user->authorise( 'core.edit.own', 'com_cck.form.'.$config['type_id'] );
			// } else {
			//	$canEditOwn		=	false; // todo: guest
			// }
			$canEditOwnContent	=	'';

			// canEditOwnContent
			jimport( 'cck.joomla.access.access' );
			$canEditOwnContent	=	CCKAccess::check( $user->id, 'core.edit.own.content', 'com_cck.form.'.$config['type_id'] );

			if ( $canEditOwnContent ) {
				$parts	=	explode( '@', $canEditOwnContent );
				$field2	=	JCckDatabaseCache::loadObject( 'SELECT storage, storage_table, storage_field FROM #__cck_core_fields WHERE name = "'.$parts[0].'"' );
				$canEditOwnContent		=	false;
				if ( is_object( $field2 ) && $field2->storage == 'standard' ) {
					$pks				=	( isset( $config['pks'] ) ) ? $config['pks'] : $config['pk'];
					$query				=	'SELECT '.$field2->storage_field.' as map, id FROM '.$field2->storage_table.' WHERE id IN ('.$pks.')';
					$index				=	md5( $query );
					if ( !isset( $cache[$index] ) ) {
						$cache[$index.'_pks']	=	JCckDatabase::loadObjectList( $query, 'id' );
						$values					=	array();
						if ( count( $cache[$index.'_pks'] ) ) {
							foreach ( $cache[$index.'_pks'] as $p ) {
								$values[]	=	$p->map;
							}
						}
						if ( count( $values ) ) {
							$values			=	array_diff( $values, array( '' ) );
							$values			=	implode( ',', $values );
						} else {
							$values			=	'0';
						}
						
						$cache[$index]	=	JCckDatabase::loadObjectList( 'SELECT author_id, pk FROM #__cck_core WHERE storage_location = "'.( isset( $parts[1] ) && $parts[1] != '' ? $parts[1] : 'joomla_article' ).'" AND pk IN ( '.$values.' )', 'pk' );
					}
					if ( isset( $cache[$index.'_pks'][$config['pk']] )
						&& isset( $cache[$index][$cache[$index.'_pks'][$config['pk']]->map] )   
						&& $cache[$index][$cache[$index.'_pks'][$config['pk']]->map]->author_id == $user->id ) {
						$canEditOwnContent	=	true;
					}
				}
			} else {
				$canEditOwnContent	=	'';
			}

			// Check Permissions
			if ( !( $canEdit && $canEditOwn
				|| ( $canEdit && !$canEditOwn && ( $config['author'] != $user->id ) )
				|| ( $canEditOwn && ( $config['author'] == $user->id ) )
				|| ( $canEditOwnContent ) ) ) {
				if ( !$link->get( 'no_access', 0 ) ) {
					$field->display	=	0;
				}
				return;
			}
		} elseif ( $form == '-2' ) {
			$form		=	'#'.$link->get( 'form_fieldname', '' ).'#';

			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$link->get( 'form_fieldname', '' ), 'form'=>'-2' ) );
		} elseif ( $form != '' ) {
			$user 		=	JCck::getUser();
			$type_id	=	(int)JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$form.'"' );
			$canCreate	=	( $type_id ) ? $user->authorise( 'core.create', 'com_cck.form.'.$type_id ) : false;

			// Check Permissions
			if ( !$canCreate ) {
				return;
			}
		}
		
		// Prepare
		$link_attr		=	$link->get( 'attributes', '' );
		$link_class		=	$link->get( 'class', '' );
		$link_rel		=	$link->get( 'rel', '' );
		$link_target	=	$link->get( 'target', '' );
		$link_title		=	$link->get( 'title', '' );
		$link_title2	=	$link->get( 'title_custom', '' );
		$tmpl			=	$link->get( 'tmpl', '' );
		$tmpl			=	( $tmpl == '-1' ) ? $app->input->getCmd( 'tmpl', '' ) : $tmpl;
		$tmpl			=	( $tmpl ) ? '&tmpl='.$tmpl : '';
		$vars			=	$tmpl;	// + live
		
		/*
		if ( $config['client'] == 'admin' || $config['client'] == 'site' || $config['client'] == 'search' ) {
			$redirection		=	'-1'; // todo
		}
		*/
		
		// Set
		if ( is_array( $field->value ) ) {
			foreach ( $field->value as $f ) {
				$c				=	$custom;
				$c				=	parent::g_getCustomSelfVars( self::$type, $f, $c, $config );
				$c				=	$c ? '&'.$c : '';
				$f->link		=	'index.php?option=com_cck&view=form&layout=edit&type='.$form.$edit.$vars.'&Itemid='.$itemId.$c;
				if ( $redirection != '-1' ) {
					$f->link	.=	'&return='.$return;
				}
				$f->link			=	JRoute::_( $f->link );
				$f->link_attributes	=	$link_attr ? $link_attr : ( isset( $f->link_attributes ) ? $f->link_attributes : '' );
				$f->link_class		=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );
				$f->link_rel		=	$link_rel ? $link_rel : ( isset( $f->link_rel ) ? $f->link_rel : '' );
				$f->link_state		=	$link->get( 'state', 1 );
				$f->link_target		=	$link_target ? $link_target : ( isset( $f->link_target ) ? $f->link_target : '' );

				if ( $link_title ) {
					if ( $link_title == '2' ) {
						$f->link_title	=	$link_title2;
					} elseif ( $link_title == '3' ) {
						$f->link_title	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $link_title2 ) ) );
					}
					if ( !isset( $f->link_title ) ) {
						$f->link_title	=	'';
					}
				} else {
					$f->link_title		=	'';
				}
			}
			$field->link		=	'#';	//todo
		} else {
			$custom				=	parent::g_getCustomVars( self::$type, $field, $custom, $config );
			if ( $form[0] == '#' ) {
				$field->link	=	'index.php?option=com_cck&view=form&layout=edit&type='.$form.$edit.$vars.'&Itemid='.$itemId;
			} else {
				$field->link	=	JRoute::_( 'index.php?option=com_cck&view=form&layout=edit&type='.$form.$edit.$vars.'&Itemid='.$itemId );
			}
			$separator			=	( strpos( $field->link, '?' ) !== false ) ? '&' : '?';
			if ( $custom ) {
				$field->link	.=	$separator.$custom;
				$separator		=	'&';
			}
			if ( $redirection != '-1' ) {
				$field->link	.=	$separator.'return='.$return;
			}
			$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
			$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
			$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
			$field->link_state		=	$link->get( 'state', 1 );
			$field->link_target		=	$link_target ? $link_target : ( isset( $field->link_target ) ? $field->link_target : '' );

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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		if ( isset( $process['form'] ) && $process['form'] == '-2' ) {
			$fieldname	=	$process['fieldname'];
			$form		=	( isset( $fields[$fieldname] ) ) ? $fields[$fieldname]->value : '';
			$user 		=	JCck::getUser();
				
			$type_id	=	(int)JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$form.'"' );
			$canCreate	=	( $type_id ) ? $user->authorise( 'core.create', 'com_cck.form.'.$type_id ) : false;

			// Check Permissions
			if ( $canCreate ) {
				$fields[$name]->link	=	str_replace( '#'.$fieldname.'#', $form, $fields[$name]->link );
				$fields[$name]->html	=	str_replace( '#'.$fieldname.'#', $form, $fields[$name]->html );
				$fields[$name]->typo	=	str_replace( '#'.$fieldname.'#', $form, $fields[$name]->typo );
			} else {
				$fields[$name]->link	=	'';
				$target					=	 $fields[$name]->typo_target;

				if ( $fields[$name]->typo ) {
					$fields[$name]->typo	=	$fields[$name]->$target; // todo: str_replace link+target par target
				} else {
					$fields[$name]->html	=	$fields[$name]->$target;
				}
			}
		} else {
			$name	=	$process['name'];
			
			if ( count( $process['matches'][1] ) ) {
				self::g_setCustomVars( $process, $fields, $name );
			}
		}
	}
}
?>