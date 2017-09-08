<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list_inc_list_items.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Prepare
JPluginHelper::importPlugin( 'cck_field_link' );
$p_title	=	0;
$p_typo		=	1;
if ( $p_typo ) {
	JPluginHelper::importPlugin( 'cck_field_typo' );
}

JPluginHelper::importPlugin( 'cck_storage' );
JPluginHelper::importPlugin( 'cck_storage_location' );

require_once JPATH_SITE.'/libraries/cck/rendering/rendering_item.php';
$count		=	count( $items );
$count2		=	count( $fields );
$count3		=	( $client == 'item' && $go_for_both ) ? count( $fields2 ) : 0;
if ( !isset( $doc->list ) ) {
	$doc->list			=	array();
}
$doc->list[$idx]		=	array();

$debug		=	JCck::getConfig_Param( 'debug', 0 );
$ids		=	'';
$optimize	=	(int)JCck::getConfig_Param( 'optimize_memory', 0 );
$properties	=	CCK_List::getPropertyColumns_asString( $optimize );
$pks		=	'';
if ( $list['isCore'] ) {
	for ( $i = 0; $i < $count; $i++ ) {
		$ids	.=	(int)$items[$i]->pid.',';
		$pks	.=	(int)$items[$i]->pk.',';
	}
	$ids		=	substr( $ids, 0, -1 );
	$pks		=	substr( $pks, 0, -1 );
}
$storages		=	array( '_'=>'' );
$suffix			=	'';

if ( $debug == -1 ) {
	$suffix		=	'Debug';
	foreach ( $fields as $field ) {
		$field->storage	=	'lipsum';
	}
}
if ( $count ) {
	for ( $i = 0; $i < $count; $i++ ) {
		if ( isset( $items[$i]->pk ) ) {
			$PK						=	$items[$i]->pk;
		} else {
			$PK						=	$i;
			$items[$i]->author		=	0;
			$items[$i]->cck			=	'';
			$items[$i]->loc			=	$list['location'];
			$items[$i]->parent		=	'';
			$items[$i]->pid			=	0;
			$items[$i]->pk			=	$i;
			$items[$i]->pkb			=	0;
			$items[$i]->type_id		=	0;
			$items[$i]->type_alias	=	'';
		}
		$item	=	new CCK_Item( $templateStyle->name, $search->name, $items[$i]->pk );
		
		// --
		if ( $count2 ) {
			$config		=	array(
								'author'=>$items[$i]->author,
								'client'=>'item',
								'doSEF'=>$list['doSEF'],
								'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
								'doTypo'=>$p_typo,
								'error'=>0,
								'fields'=>array(),
								'formId'=>$list['formId'],
								'formWrapper'=>false,
								'id'=>$items[$i]->pid,
								'ids'=>$ids,
								'Itemid'=>$list['itemId'],
								'links'=>array(),
								'location'=>$items[$i]->loc,
								'parent_id'=>$items[$i]->parent,
								'pk'=>$items[$i]->pk,
								'pkb'=>$items[$i]->pkb,
								'pks'=>$pks,
								'storages'=>array(),
								'type'=>$items[$i]->cck,
								'type_id'=>(int)$items[$i]->type_id,
								'type_alias'=>( $items[$i]->type_alias ? $items[$i]->type_alias : $items[$i]->cck )
							);
			$fieldsI	=	array();
			
			foreach ( $fields as $field ) {
				$field				=	clone $field;
				$field->typo_target	=	'value';
				$fieldName			=	$field->name;
				$value				=	'';
				$name				=	( ! empty( $field->storage_field2 ) ) ? $field->storage_field2 : $fieldName; //-
				if ( $fieldName ) {
					$Pt				=	( $field->storage_table != '' ) ? $field->storage_table : '_';
					if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
						if ( ! isset( $storages[$Pt] ) ) {
							$storages[$Pt]					=	'';
							if ( !$list['isCore'] || $Pt == '_' ) {
								$config['storages'][$Pt]	=	$items[$i];
							} else {
								$dispatcher->trigger( 'onCCK_Storage_LocationPrepareItems', array( &$field, &$storages, $config['pks'], &$config, true ) );
								$config['storages'][$Pt]				=	isset( $storages[$Pt][$config['pk']] ) ? $storages[$Pt][$config['pk']] : null;
								if ( $storages['_'] && !isset( $config['storages'][$storages['_']] ) ) {
									$config['storages'][$storages['_']]	=	$storages[$storages['_']][$config['pk']];
								}
							}
						} else {
							if ( !$list['isCore'] || $Pt == '_' ) {
								$config['storages'][$Pt]	=	$items[$i];						
							} else {
								$dispatcher->trigger( 'onCCK_Storage_LocationPrepareItems', array( &$field, &$storages, $config['pks'], &$config, false ) );
								$config['storages'][$Pt]				=	isset( $storages[$Pt][$config['pk']] ) ? $storages[$Pt][$config['pk']] : null;
								if ( $storages['_'] && !isset( $config['storages'][$storages['_']] ) ) {
									$config['storages'][$storages['_']]	=	$storages[$storages['_']][$config['pk']];
								}
							}
						}
					}
					
					$dispatcher->trigger( 'onCCK_StoragePrepareContent', array( &$field, &$value, &$config['storages'][$Pt] ) );
					if ( is_string( $value ) ) {
						$value		=	trim( $value );
					}
					$hasLink	=	( $field->link != '' ) ? 1 : 0;
					$dispatcher->trigger( 'onCCK_FieldPrepareContent'.$suffix, array( &$field, $value, &$config ) );
					$target		=	$field->typo_target;
					if ( $hasLink ) {
						$dispatcher->trigger( 'onCCK_Field_LinkPrepareContent', array( &$field, &$config ) );
						if ( $field->link ) {
							JCckPluginLink::g_setHtml( $field, $target );
						}
					}
					if ( @$field->typo && ( $field->$target !== '' || $field->typo_label == -2 ) && $p_typo ) {
						$dispatcher->trigger( 'onCCK_Field_TypoPrepareContent', array( &$field, $field->typo_target, &$config ) );
					} else {
						$field->typo	=	'';
					}
					
					$fieldsI[$fieldName]			=	$field;

					if ( $i == 0 ) {
						$pos						=	$field->position;
						$positions[$pos][]			=	$field->name;
					}

					// Was it the last one?
					if ( $config['error'] ) {
						break;
					}
				}
			}
			
			// Merge
			if ( count( $config['fields'] ) ) {
				foreach ( $config['fields'] as $k=>$v ) {
					if ( $v->restriction != 'unset' ) {
						$fieldsI[$k]	=	$v;
					}
				}
				$config['fields']	=	NULL;
				unset( $config['fields'] );
			}
			
			// Todo: ->legend2 may be deprecated (from here) in a near future... in order to move at template level.
			if ( $i == 0 ) {
				foreach ( $positions as $p_key=>$p_fields ) {
					$legend2	=	'';

					foreach ( $p_fields as $p_field ) {
						if ( $fieldsI[$p_field]->display ) {
							$legend2	=	( $legend2 != '' && $fieldsI[$p_field]->label ) ? $legend2 .' / '. $fieldsI[$p_field]->label : $fieldsI[$p_field]->label;
						}
					}

					if ( isset( $positions_p[$p_key] ) ) {
						$positions_p[$p_key]->legend2	=	$legend2;
					}
				}
			}

			// BeforeRender
			if ( isset( $config['process']['beforeRenderContent'] ) && count( $config['process']['beforeRenderContent'] ) ) {
				JCckDevHelper::sortObjectsByProperty( $config['process']['beforeRenderContent'], 'priority' );
				
				foreach ( $config['process']['beforeRenderContent'] as $process ) {
					if ( $process->type ) {
						JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'BeforeRenderContent', array( $process->params, &$fieldsI, &$config['storages'], &$config ) );
					}
				}
			}

			// Optimize
			if ( $optimize == 11 ) {
				foreach ( $fieldsI as $k=>$v ) {
					if ( isset( $v->state ) && !$v->state ) {
						unset( $fieldsI[$k] );
					}
				}
			} elseif ( $optimize ) {
				foreach ( $fieldsI as $k=>$v ) {
					if ( $optimize ) {
						foreach ( $properties as $property ) {
							unset( $v->$property );
						}
					}
				}	
			}
			
			$item->$target_f	=	$fieldsI;
		}
		
		if ( $count3 ) {
			$config		=	array(
								'author'=>$items[$i]->author,
								'client'=>'item',
								'doSEF'=>$list['doSEF'],
								'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
								'doTypo'=>$p_typo,
								'error'=>0,
								'fields'=>array(),
								'formId'=>$list['formId'],
								'formWrapper'=>false,
								'id'=>$items[$i]->pid,
								'ids'=>$ids,
								'Itemid'=>$list['itemId'],
								'links'=>array(),
								'location'=>$items[$i]->loc,
								'parent_id'=>$items[$i]->parent,
								'pk'=>$items[$i]->pk,
								'pkb'=>$items[$i]->pkb,
								'pks'=>$pks,
								'storages'=>array(),
								'type'=>$items[$i]->cck,
								'type_id'=>(int)$items[$i]->type_id,
								'type_alias'=>( $items[$i]->type_alias ? $items[$i]->type_alias : $items[$i]->cck )
							);
			$fieldsI	=	array();
			
			foreach ( $fields2 as $field ) {
				$field				=	clone $field;
				$field->typo_target	=	'value';
				$fieldName			=	$field->name;
				$value				=	'';
				$name				=	( ! empty( $field->storage_field2 ) ) ? $field->storage_field2 : $fieldName; //-
				if ( $fieldName ) {
					$Pt				=	( $field->storage_table != '' ) ? $field->storage_table : '_';
					if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
						if ( ! isset( $storages[$Pt] ) ) {
							$storages[$Pt]					=	'';
							if ( !$list['isCore'] || $Pt == '_' ) {
								$config['storages'][$Pt]	=	$items[$i];
							} else {
								$dispatcher->trigger( 'onCCK_Storage_LocationPrepareItems', array( &$field, &$storages, $config['pks'], &$config, true ) );
								$config['storages'][$Pt]				=	isset( $storages[$Pt][$config['pk']] ) ? $storages[$Pt][$config['pk']] : null;
								if ( $storages['_'] && !isset( $config['storages'][$storages['_']] ) ) {
									$config['storages'][$storages['_']]	=	$storages[$storages['_']][$config['pk']];
								}
							}
						} else {
							if ( !$list['isCore'] || $Pt == '_' ) {
								$config['storages'][$Pt]	=	$items[$i];
							} else {
								$dispatcher->trigger( 'onCCK_Storage_LocationPrepareItems', array( &$field, &$storages, $config['pks'], &$config, false ) );
								$config['storages'][$Pt]				=	isset( $storages[$Pt][$config['pk']] ) ? $storages[$Pt][$config['pk']] : null;
								if ( $storages['_'] && !isset( $config['storages'][$storages['_']] ) ) {
									$config['storages'][$storages['_']]	=	$storages[$storages['_']][$config['pk']];
								}
							}
						}
					}
					
					$dispatcher->trigger( 'onCCK_StoragePrepareContent', array( &$field, &$value, &$config['storages'][$Pt] ) );
					if ( is_string( $value ) ) {
						$value		=	trim( $value );
					}
					$hasLink	=	( $field->link != '' ) ? 1 : 0;
					$dispatcher->trigger( 'onCCK_FieldPrepareContent', array( &$field, $value, &$config ) );
					$target		=	$field->typo_target;
					if ( $hasLink ) {
						$dispatcher->trigger( 'onCCK_Field_LinkPrepareContent', array( &$field, &$config ) );
						if ( $field->link ) {
							JCckPluginLink::g_setHtml( $field, $target );
						}
					}
					if ( @$field->typo && ( $field->$target !== '' || $field->typo_label == -2 ) && $p_typo ) {
						$dispatcher->trigger( 'onCCK_Field_TypoPrepareContent', array( &$field, $field->typo_target, &$config ) );
					} else {
						$field->typo	=	'';
					}

					// Optimize Memory
					if ( $optimize ) {
						foreach ( $properties as $property ) {
							unset( $field->$property );
						}
					}
					$fieldsI[$fieldName]			=	$field;

					if ( $i == 0 ) {
						$pos						=	$field->position;
						$positions2[$pos][]			=	$field->name;
					}

					// Was it the last one?
					if ( $config['error'] ) {
						break;
					}
				}
			}
			
			// Merge
			if ( count( $config['fields'] ) ) {
				foreach ( $config['fields'] as $k=>$v ) {
					if ( $v->restriction != 'unset' ) {
						$fieldsI[$k]	=	$v;
					}
				}
				$config['fields']	=	NULL;
				unset( $config['fields'] );
			}
			
			// Todo: ->legend2 may be deprecated (from here) in a near future... in order to move at template level.
			if ( $i == 0 ) {
				foreach ( $positions2 as $p_key=>$p_fields ) {
					$legend2	=	'';

					foreach ( $p_fields as $p_field ) {
						if ( $fieldsI[$p_field]->display ) {
							$legend2	=	( $legend2 != '' && $fieldsI[$p_field]->label ) ? $legend2 .' / '. $fieldsI[$p_field]->label : $fieldsI[$p_field]->label;
						}
					}

					if ( isset( $positions_p[$p_key] ) ) {
						$positions_p[$p_key]->legend2	=	$legend2;
					}
				}
			}

			// BeforeRender
			if ( isset( $config['process']['beforeRenderContent'] ) && count( $config['process']['beforeRenderContent'] ) ) {
				JCckDevHelper::sortObjectsByProperty( $config['process']['beforeRenderContent'], 'priority' );

				foreach ( $config['process']['beforeRenderContent'] as $process ) {
					if ( $process->type ) {
						JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'BeforeRenderContent', array( $process->params, &$fieldsI, &$config['storages'], &$config ) );
					}
				}
			}
			$item->fields_list		=	$fieldsI;
		}
		
		// --
		$item->params				=	$templateStyle->params;
		$item->positions			=	$positions;
		$item->positions_m			=	$positions_p;
		// --
		
		$item->initialize();
		$doc->list[$idx][$PK]			=	$item;
		$doc->list[$idx][$PK]->pid		=	$items[$i]->pid;
		$doc->list[$idx][$PK]->pk		=	(string)$items[$i]->pk;
		$doc->list[$idx][$PK]->pkb		=	$items[$i]->pkb;
		$doc->list[$idx][$PK]->cck		=	$items[$i]->cck;
		$doc->list[$idx][$PK]->location	=	$items[$i]->loc;
		$doc->list[$idx][$PK]->author	=	$items[$i]->author;
	}
} else {
	foreach ( $fields as $field ) {
		$pos				=	$field->position;
		$positions[$pos][]	=	$field->name;
	}
}
?>