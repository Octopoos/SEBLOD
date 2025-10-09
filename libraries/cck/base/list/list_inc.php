<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list_inc.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JHtml::_( 'behavior.core' );

$app			=	JFactory::getApplication();
$data			=	'';
$form			=	'';
$id				=	0;	// $app->input->getInt( 'id', 0 ); Not even sure why it was there.. any regression?
$isCached		=	'';
$itemId			=	( $preconfig['itemId'] == '' ) ? $app->input->getInt( 'Itemid', 0 ) : $preconfig['itemId'];
$items			=	array();
$lang   		=	JFactory::getLanguage();
$path			=	JPATH_SITE.'/templates';
$total			=	0;
$total_items	=	0;
$user 			=	JCck::getUser();
$user->gid		=	25; /* TODO#SEBLOD: ACL */

// Search
$search			=	CCK_List::getSearch( $preconfig['search'], $id );
if ( ! $search ) {
	$config		=	array( 'action'=>$preconfig['action'],
						   'core'=>true,
						   'formId'=>$preconfig['formId'],
						   'Itemid'=>$itemId,
						   'javascript'=>'',
						   'location'=>'',
						   'submit'=>$preconfig['submit'],
						   'validation'=>array(),
						   'validation_options'=>array()
						);
	$app->enqueueMessage( 'Oops! Search Type not found.. ; (', 'error' ); return;
}
$lang->load( 'pkg_app_cck_'.$search->folder_app, JPATH_SITE, null, false, false );

$no_action					=	'';
$options					=	new JRegistry;
$options->loadString( $search->options );
$preconfig['show_form']		=	( $preconfig['show_form'] != '' ) ? (int)$preconfig['show_form'] : (int)$options->get( 'show_form', 1 );
$preconfig['show_list']		=	( isset( $preconfig['show_list'] ) ) ? (int)$preconfig['show_list'] : (int)$options->get( 'show_list', 1 );
$preconfig['auto_redirect']	=	( $preconfig['auto_redirect'] != '' ) ? $preconfig['auto_redirect'] : $options->get( 'auto_redirect', 0 );

$doDebug					=	(int)$options->get( 'debug', JCck::getConfig_Param( 'debug', 0 ) );
$doDebugGroup				=	JCck::getConfig_Param( 'development_group_debug', 0 );
$doDebugGroup				=	$doDebugGroup && isset( $user->groups[$doDebugGroup] ) ? true : false;

if ( $doDebug == 1 || ( $doDebug == 2 && $user->authorise( 'core.admin' ) ) || ( $doDebug == 3 && $doDebugGroup ) ) {
	$doDebug				=	 1;
} elseif ( $doDebug == 11 || ( $doDebug == 12 && $user->authorise( 'core.admin' ) ) ) {
	$doDebug				=	 10;
} elseif ( $doDebug == -1 || ( $doDebug == -2 && $user->authorise( 'core.admin' ) ) ) {
	$doDebug				=	 -1;
} else {
	$doDebug				=	 0;
}

$options->set( 'debug', $doDebug );

// ACL
if ( !in_array( $search->access, $user->getAuthorisedViewLevels() ) ) {
	$config			=	array( 'action'=>$preconfig['action'],
						   'core'=>true,
						   'formId'=>$preconfig['formId'],
						   'Itemid'=>$itemId,
						   'javascript'=>'',
						   'limitend'=>0,
						   'location'=>'',
						   'submit'=>$preconfig['submit'],
						   'type'=>$search->name,
						   'validation'=>array(),
						   'validation_options'=>array()
						);
	$no_message		=	$options->get( 'message_no_access' );
	$no_redirect	=	$options->get( 'redirection_url_no_access', 'index.php?option=com_users&view=login' );
	$no_style		=	$options->get( 'message_style_no_access', 'error' );
	$no_action		=	$options->get( 'action_no_access', 'redirection' );
	CCK_List::redirect( $no_action, $no_redirect, $no_message, $no_style, $config, $doDebug ); return;
}

// Fields
$fields						=	CCK_List::getFields( $search->name, array( $preconfig['client'], 'order' ), '', true, true );

$count						=	count( $fields['search'] );
$excluded_stages			=	explode( ',', $options->get( 'stages_optional', '' ) );

if ( ! $count ) {
	$config		=	array( 'action'=>$preconfig['action'],
						   'core'=>true,
						   'formId'=>$preconfig['formId'],
						   'Itemid'=>$itemId,
						   'javascript'=>'',
						   'limitend'=>0,
						   'location'=>'',
						   'submit'=>$preconfig['submit'],
						   'type'=>$search->name,
						   'validation'=>array(),
						   'validation_options'=>array()
						);

	if ( !( $preconfig['task'] == 'no' && !$preconfig['show_form'] ) ) {
		$app->enqueueMessage( 'Oops! Fields not found.. ; (', 'error' );
	}

	return;
}

// Init
$limitend		=	( isset( $preconfig['limitend'] ) && $preconfig['limitend'] != '' ) ? (int)$preconfig['limitend'] : (int)$options->get( 'pagination', JCck::getConfig_Param( 'pagination', 48 ) );
$list_context	=	'com_cck.'.$search->name;
$pagination		=	( isset( $pagination ) && $pagination != '' ) ? $pagination : $options->get( 'show_pagination', 0 );

$isAltered		=	false;
$isPersistent	=	(int)$options->get( 'persistent_query', '0' );
$isSearch		=	(int)JUri::getInstance()->hasVar( 'task' ); /* TODO#SEBLOD: add "data-cck-remove-before-search" behavior on empty fields/values + test with persistent search + fix checkboxes on persistent search vs isset($config['post'][...]) */

if ( $isPersistent == 1 || ( $isPersistent == 2 && $user->id && !$user->guest ) ) {
	$isPersistent	=	true;
} else {
	$isPersistent	=	false;
}

// Variations
$variation	=	explode( '||', $variation );
$variations	=	array();

foreach ( $variation as $var ) {
	if ( $var != '' ) {
		$v					=	explode( '=', $var );
		if ( $v[1] == 'none' ) { $v[1] = 'hidden'; } /* TODO#SEBLOD: FIX TO REMOVE AFTER GA */
		$variations[$v[0]]	=	$v[1];
	}
}

$method			=	0;
$searchLength	=	0;
$ordering		=	( @$preconfig['ordering'] != '' ) ? $preconfig['ordering'] : $options->get( 'ordering', '' );
$active			=	array();
$active[0]		=	'cck';
$areas['active']=	$active;

$config			=	array(
						'app'=>null,
						'action'=>$preconfig['action'],
						'client'=>$preconfig['client'],
						'context'=>array(),
						'core'=>true,
						'doAjax'=>false,
						'doPagination'=>true,
						'doPersistent'=>$isPersistent,
						'doQuery'=>true,
						'doRecording'=>false,
						'doSEF'=>$options->get( 'sef', JCck::getConfig_Param( 'sef', '23' ) ),
						'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 1 ),
						'doValidation'=>(int)JCck::getConfig_Param( 'validation', '3' ),
						'fields'=>array(),
						'formFilters'=>array(),
						'formId'=>$preconfig['formId'],
						'formWrapper'=>0,
						'Itemid'=>$itemId,
						'infinite'=>( ( $pagination == 2 || $pagination == 8 ) ? true : false ),
						'limitend'=>0,
						'location'=>'',
						'pagination_vars'=>array(),
						'pk'=>$id,
						'registry'=>JFactory::getSession()->get( 'registry' ),
						'sef_aliases'=>(int)( (int)$search->sef_route_aliases != -1 ? $search->sef_route_aliases : JCck::getConfig_Param( 'sef_aliases', '0' ) ),
						'show_form'=>$preconfig['show_form'],
						'stages'=>array(),
						'submit'=>$preconfig['submit'],
						'type'=>$search->name,
						'type_id'=>$search->id,
						'validate'=>array(),
						'validation'=>array(),
						'validation_options'=>array(),
						'variations'=>$variations
					);

if ( $preconfig['task'] == 'no' ) {
	$config['task']	=	'no';
} elseif ( $preconfig['task'] == 'search' || $preconfig['task'] == 'search2' ) {
	$config['post']	=	( $method ) ? $app->input->post->getArray() : $app->input->get->getArray();
}

$config['app']	=	new JCckApp;
$config['app']->loadDefault();

jimport( 'cck.rendering.document.document' );
JPluginHelper::importPlugin( 'cck_field' );
JPluginHelper::importPlugin( 'cck_field_live' );
JPluginHelper::importPlugin( 'cck_field_restriction' );

// -------- -------- -------- -------- -------- -------- -------- -------- // Show Form

if ( $preconfig['show_form'] ) {
	JHtml::_( 'behavior.core' );

	// Template
	$P				=	'template_'.$preconfig['client'];
	$templateStyle	=	CCK_List::getTemplateStyle( $search->$P, array( 'rendering_css_core'=>$search->stylesheets ) );
	if ( ! $templateStyle ) {
		$app->enqueueMessage( 'Oops! Template not found.. ; (', 'error' ); return;
	}

	$doc			=	CCK_Document::getInstance( 'html' );

	// Positions
	$positions		=	array();
	$positions_more	=	CCK_List::getPositions( $search->id, $preconfig['client'] );
	
	// Template Override
	$tpl['home']	=	$app->getTemplate();
	$path			=	JPATH_SITE.'/templates/'.$templateStyle->name;

	if ( $preconfig['show_form'] > -1 ) {
		$path_root		=	JPATH_SITE.'/templates';
		$tmpl			=	$templateStyle->name;
		$rparams		=	array( 'template'=>$tmpl, 'file'=>'index.php', 'directory'=>$path_root );	
	}
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare Context

$context	=	$app->input->getString( 'context' );
$isRaw		=	false;

if ( $context != '' ) {
	$context	=	json_decode( $context, true );
	$excluded	=	array(
						'cid'=>'',
						'copyfrom_id'=>'',
						/* 'id'=>'', OK, we can keep it */
						'limit'=>'',
						'option'=>'',
						'pk'=>'',
						/* 'referrer'=>'', we should keep this one as well */
						'return'=>'',
						'search'=>'',
						'skip'=>'',
						'stage'=>'',
						'task'=>'',
						'tid'=>'',
						'tmpl'=>'', /* Let's keep it when format!=raw for now */
						'type'=>''
					);

	foreach ( $context as $k=>$v ) {
		if ( isset( $excluded[$k] ) ) {
			if ( $k == 'tmpl' ) {
				if ( $v == 'raw' ) {
					$isRaw	=	true;
				}
				if ( $app->input->get( 'format' ) == 'raw' ) {
					continue;
				}
			} else {
				continue;
			}
		}
		$app->input->set( $k, $v );
	}
}

// Lives
if ( !isset( $lives ) ) {
	$lives		=	array();

	if ( isset( $live ) && $live && !$isRaw ) {
		$live		=	explode( '||', $live );
		
		foreach ( $live as $liv ) {
			if ( $liv != '' ) {
				$l				=	explode( '=', $liv );
				$lives[$l[0]]	=	$l[1];
			}
		}
	} elseif ( isset( $context['referrer'] ) && $context['referrer'] ) {
		if ( $config['registry']->exists( $list_context.'.lives.'.$context['referrer'] ) ) {
			$lives	=	$app->getUserState( $list_context.'.lives.'.$context['referrer'], array(), array() );
		}
	}
} elseif ( count( $lives ) && $config['infinite'] ) {
	if ( isset( $preconfig['caller'] ) && $preconfig['caller'] ) {
		$app->setUserState( $list_context.'.lives.'.$preconfig['caller'], $lives );
	}
}

$config['lives']	=	$lives;

// We need to override form stuff based on the tmpl found within the context
if ( $isRaw ) {
	if ( strpos( $config['formId'], '_raw' ) === false ) {
		$config['formId']	.=	'_raw';
		$config['submit']	.=	'_raw';
	}
}

if ( $config['infinite'] && $app->input->get( 'view' ) == 'list' && !isset( $menu )  ) {
	$menu				=	$app->getMenu()->getItem( $app->input->getInt( 'Itemid' ) );

	if ( is_object( $menu ) ) {
		$menu_params			=	$menu->getParams();

		if ( is_object( isset( $menu_params ) ) ) {
			$preconfig['limit']		=	$menu_params->get( 'limit' );
			$preconfig['search2']	=	$menu_params->get( 'search2' );
		}
	}
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare Search

// Validation
if ( (int)JCck::getConfig_Param( 'validation', '3' ) > 1 ) {
	$lang->load( 'plg_cck_field_validation_required', JPATH_ADMINISTRATOR, null, false, true );
	require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
}
$preconfig['client']	=	'list';
$error					=	'';
$current				=	array( 'stage'=>0, 'stages'=>array(), 'order_by'=>@$order_by );

CCK_List::prepareSearch( $fields['search'], $config, 'positions', $positions );

// Merge
if ( count( $config['fields'] ) ) {
	foreach ( $config['fields'] as $k=>$v ) {
		if ( isset( $v->restriction ) &&  $v->restriction != 'unset' ) {
			$fields['search'][$k]	=	$v;
		}
	}
	$config['fields']	=	null;
	unset( $config['fields'] );
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Do Search

$config['doSelect']		=	$search->content ? false : true;

if ( (int)$app->input->getInt( 'infinite', '0' ) ) {
	if ( $app->input->get( 'end', '' ) != '' ) {
		$limitend	=	(int)$app->input->getInt( 'end', '' );
	}
}

if ( $config['limitend'] > 0 ) {
	$limitend		=	$config['limitend'];
}

if ( $limitstart != -1 ) {
	$start_var		=	( $app->isClient( 'administrator' ) || !JFactory::getConfig()->get( 'sef' ) ) ? 'limitstart' : 'start';
	$start			=	(int)$app->input->getInt( $start_var );

	if ( $limitstart > 0 && !$start ) {
		$isAltered	=	true;
	}
	if ( isset( $this ) && isset( $this->state ) && is_object( $this->state ) ) {
		if ( $limitend != -1 ) {
			$this->state->set( 'limit', (int)$limitend );
		}

		$limitend	=	(int)$this->state->get( 'limit' );

		$app->cck_pagination	=	$limitend;
	}
}
if ( isset( $preconfig['limit'] ) && $preconfig['limit'] ) {
	$options->set( 'limit', $preconfig['limit'] );
}

$config['limitend']		=	$limitend;
$config['limitstart']	=	$limitstart;

if ( $doDebug ) {
	jimport( 'joomla.error.profiler' );
	
	$profiler	=	JProfiler::getInstance();
	echo $profiler->mark( 'beforeSearch'.$isCached ).'<br />';
}

if ( $search->storage_location ) {
	$config['type_object']	=	$search->storage_location;
}
if ( $preconfig['task'] == 'search' ) {
	if ( isset( $config['process']['beforeSearch'] ) && count( $config['process']['beforeSearch'] ) ) {
		foreach ( $config['process']['beforeSearch'] as $process ) {
			if ( $process->type ) {
				JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'BeforeSearch', array( $process->params, &$fields, &$config['storages'], &$config ) );
			}
		}
	}
	if ( $preconfig['show_form'] ) {
		$doc->fields	=	$fields['search'];
	}
	if ( isset( $config['error'] ) && $config['error'] ) {
		$error	=	1;
	}
	
	if ( isset( $preconfig['items'] ) ) {
		$config['doQuery']	=	false;
		$items				=	$preconfig['items'];
	} else {
		$countStages		=	count( $config['stages'] );
		$items				=	array();

		if ( $countStages ) {		
			for( $stage =  1; $stage <= $countStages; $stage++ ) {
				if ( ! $error ) {
					// Search
					$current['stage']	=	$stage;
					$items				=	CCK_List::getList( $ordering, $areas, $fields['search'], $fields['order'], $config, $current, $options, $user );
					if ( ! $items && $config['stages'][$stage] == 0 && in_array( $stage, $excluded_stages ) === false ) {
						$error			=	1;
						break;
					}
					$current['stages'][$stage]	=	implode( ',', $items );
				}
			}
		}
		if ( ! $error ) {
			$current['stage']	=	0;
			$items				=	CCK_List::getList( $ordering, $areas, $fields['search'], $fields['order'], $config, $current, $options, $user );
		}
	}
	if ( is_array( $items ) ) {
		$total	=	count( $items );
	} else {
		$items	=	array();
	}

	// IDs & PKs
	if ( isset( $config['process']['beforeRenderForm'] ) && count( $config['process']['beforeRenderForm'] ) ) {
		$ids	=	'';
		$pks	=	'';
		if ( $config['doQuery'] ) {
			for ( $i = 0; $i < $total; $i++ ) {
				$ids	.=	(int)$items[$i]->pid.',';
				$pks	.=	(int)$items[$i]->pk.',';
			}
			$ids		=	substr( $ids, 0, -1 );
			$pks		=	substr( $pks, 0, -1 );
		}
		$config['ids']	=	$ids;
		$config['pks']	=	$pks;
	}

	// Total
	if ( isset( $config['total'] ) && $config['total'] > 0 ) {
		if ( $isAltered ) {
			$config['total']	=	( $config['total'] > $limitstart ) ? $config['total'] - $limitstart : 0;
		}

		$limitstart			=	-1;
		$limitend			=	0;	
		$total				=	$config['total'];
	} else {
		$config['total']	=	$total;
	}
	$total_items			=	$total;

	// Pagination
	if ( $config['doPagination'] ) {
		if ( $limitstart != -1 && $limitend > 0 && !( $preconfig['limit2'] > 0 ) ) {
			$items	=	array_splice( $items, $limitstart, $limitend );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Do List

	if ( $preconfig['show_list'] ) {
		$target					=	'search';
		if ( isset( $preconfig['search2'] ) && $preconfig['search2'] != '' ) {
			$target				=	'search2';
			$search2			=	CCK_List::getSearch( $preconfig['search2'], $id );
			$options2			=	new JRegistry;
			$options2->loadString( $search2->options );

			if ( $options2->get( 'sef' ) != '' ) {
				$config['doSEF']	=	$options2->get( 'sef' );
			}
			$search->content		=	$search2->content;
		}
		if ( isset( $config['language'] ) && $config['language'] ) {
			${$target}->lang_tag	=	$config['language'];
		}
		if ( (int)$options->get( 'mode_no_result', '0' ) && (int)$total > 1 ) {
			$total 	=	0;
		}

		if ( $total ) {
			if ( isset( $preconfig['idx'] ) ) {
				$config['idx']	=	$preconfig['idx'];
				if ( !isset( $app->cck_idx ) ) {
					$app->cck_idx	=	array( 0=>false );
				}
				$app->cck_idx[]	=	$preconfig['idx'];
			}
			
			// Limit2 + Random
			if ( $preconfig['limit2'] > 0 ) {
				$total		=	( $preconfig['limit2'] > $total ) ? $total : $preconfig['limit2'];
				if ( $preconfig['ordering2'] == 'random' || $preconfig['ordering2'] == 'random_shuffle' ) {
					// Random
					$rand_keys	=	array_rand( $items, $total );
					if ( ! is_array( $rand_keys ) ) { 
						$rand_keys	=	array( $rand_keys );
					}	
					$rand_list	=	array();
					foreach ( $rand_keys as $key ) { 
						array_push( $rand_list, $items[$key] );
					}
					$items	=	array();
					$items	=	array_merge( $items, $rand_list );
				} else {
					// Cut
					$items	=	array_splice( $items, 0, $total );
				}
			} else {
				$total	=	count( $items ); /* TODO#SEBLOD: change above?? */
			}
			// Suffle
			if ( $preconfig['ordering2'] == 'shuffle' || $preconfig['ordering2'] == 'random_shuffle' ) {
				shuffle( $items );
			}
			
			// Redirect
			if ( $total == 1 ) {
				if ( $preconfig['auto_redirect'] == 1 ) {
					// Content
					$return			=	'';
					if ( @$preconfig['auto_redirect_vars'] != '' ) {
						$return		=	$app->input->getString( $preconfig['auto_redirect_vars'], '' );

						if ( $return != '' ) {
							$return		=	$preconfig['auto_redirect_vars'].'='.$return;
						}
					}
					$sef			=	( JFactory::getConfig()->get( 'sef' ) ) ? $config['doSEF'] : 0;
					$redirect_url	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$items[0]->loc, 'getRoute', array( $items[0]->pk, $sef, $config['Itemid'] ) );

					if ( $return != '' ) {
						$return			=	( strpos( $redirect_url, '?' ) !== false ) ? '&'.$return : '?'.$return;
						$redirect_url	.=	$return;
					}
					$app->redirect( $redirect_url );
					return;
				} elseif ( $preconfig['auto_redirect'] == 2 ) {
					// Form
					$return			=	'';
					if ( @$preconfig['auto_redirect_vars'] != '' ) {
						$variables	=	explode( ',', $preconfig['auto_redirect_vars'] );
						
						foreach ( $variables as $variable ) {
							if ( $variable != '' ) {
								$result	=	$app->input->getString( $variable, '' );

								if ( $result != '' ) {
									$return		.=	'&'.$variable.'='.$result;
								}
							}
						}
					}
					if ( strpos( $return, '&return=' ) === false ) {
						$return		.=	'&return='.base64_encode( $_SERVER["HTTP_REFERER"] );
					}
					
					$redirect_url	=	JRoute::_( 'index.php?option=com_cck&view=form&layout=edit&type='.$items[0]->cck.'&id='.$items[0]->pk.'&Itemid='.$config['Itemid'].$return, false );
					$app->redirect( $redirect_url );
					return;
				}
			}

			// Render
			$doCache2		=	isset( $options2 ) && is_object( $options2 ) ? $options2->get( 'cache2', $options->get( 'cache2' ) ) : $options->get( 'cache2' );
			$siteId			=	JCck::getSite()->id;

			if ( $doCache2 ) {
				$group		=	( $doCache2 == '2' ) ? 'com_cck_'.$config['type'].'_list' : 'com_cck';
				$cache		=	JFactory::getCache( $group );
				$cache->setCaching( 1 );
				$data		=	$cache->get( array( 'CCK_List', 'render' ), array( $items, ${$target}, $path, $preconfig['client'], $config['Itemid'], $options, $config ) );
				$isCached	=	' [Cache=ON]';
			} else {
				if ( ${$target}->content > 0 ) {
					$data	=	CCK_List::render( $items, ${$target}, $path, $preconfig['client'], $config['Itemid'], $siteId, $options, $config );
				}
				$isCached	=	' [Cache=OFF]';
			}
			if ( is_array( $data ) ) {
				if ( count( $data['config'] ) ) {
					if ( isset( $data['config']['formValidation'] ) ) {
						$formValidation	=	$data['config']['formValidation'];

						unset( $data['config']['formValidation'] );
					}
					foreach ( $data['config'] as $k=>$v ) {
						$config[$k]	=	$v;
					}
				}
				$data	=	$data['buffer'];
			}
		} else {
			$no_action	=	$options->get( 'action', '' );

			// --
			if ( isset( $options2 ) && is_object( $options2 ) && $options2->get( 'action', '' ) == '0' && $options2->get( 'action', '' ) != $no_action ) {
				$no_action	=	$options2->get( 'action', '' );
			}
			// --

			$no_message	=	$options->get( 'message', '' );
			$no_style	=	$options->get( 'message_style', 'message' );
			
			if ( ! $no_message ) {
				$no_message	=	JText::_( 'COM_CCK_NO_RESULT' );
			} else {
				if ( JCck::getConfig_Param( 'language_jtext', 1 ) ) {
					$no_message	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $no_message ) ) );
				}
			}
			if ( $no_style ) {
				if ( $no_style == '-1' ) {
					$data	=	$no_message;
				} else {
					$app->enqueueMessage( $no_message, $no_style );
				}
			}
		}
	}
} else {
	$no_action	=	$options->get( 'action_no_search', '' );
	$no_message	=	$options->get( 'message_no_search', '' );
	$no_style	=	$options->get( 'message_style_no_search', '0' );

	if ( ! $no_message ) {
		$no_message	=	JText::_( 'COM_CCK_NO_SEARCH' );
	} else {
		if ( JCck::getConfig_Param( 'language_jtext', 1 ) ) {
			$no_message	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $no_message ) ) );
		}
	}
	if ( $no_style ) {
		if ( $no_style == '-1' ) {
			$data	=	$no_message;
		} else {
			$app->enqueueMessage( $no_message, $no_style );
		}
	}
}
if ( $no_action ) {
	$target					=	'search';
	if ( isset( $preconfig['search2'] ) && $preconfig['search2'] != '' ) {
		$target				=	'search2';
		$search2			=	CCK_List::getSearch( $preconfig['search2'], $id );
		$options2			=	new JRegistry;
		$options2->loadString( $search2->options );

		if ( $options2->get( 'sef' ) != '' ) {
			$config['doSEF']	=	$options2->get( 'sef' );
		}
		$search->content		=	$search2->content;
	}
	if ( $no_action == 'auto_redirect' ) {
		/* TODO#SEBLOD: is this even possible? */
		if ( isset( $fields['search']['cck'] ) && !$fields['search']['cck']->live && $fields['search']['cck']->live_value ) {
			$return			=	base64_encode( $_SERVER["HTTP_REFERER"] );
			$redirect_url	=	JRoute::_( 'index.php?option=com_cck&view=form&layout=edit&type='.$fields['search']['cck']->live_value.'&Itemid='.$config['Itemid'].'&return='.$return );
			$app->redirect( $redirect_url );
		}
		return;
	} elseif ( $no_action == 'file' ) {
		$templateStyle2	=	CCK_List::getTemplateStyle( ${$target}->template_list, array( 'rendering_css_core'=>${$target}->stylesheets ) );
		$file1			=	JPATH_SITE.'/templates/'.$templateStyle2->name.'/includes/'.${$target}->name.'/no_result.php';
		$file2			=	JPATH_SITE.'/templates/'.$templateStyle2->name.'/includes/no_result.php';
		
		if ( file_exists( $file1 ) ) {
			$file	=	$file1;
		} elseif ( file_exists( $file2 ) ) {
			$file	=	$file2;
		} else {
			$file	=	'';
		}
		if ( $file && is_file( $file ) ) {
			ob_start();
			include $file;
			$data	=	ob_get_clean();
		}
	} else {
		$siteId		=	JCck::getSite()->id;
		$data		=	CCK_List::render( $items, ${$target}, $path, $preconfig['client'], $config['Itemid'], $siteId, $options, $config );

		if ( count( $data['config'] ) ) {
			if ( isset( $data['config']['formValidation'] ) ) {
				$formValidation	=	$data['config']['formValidation'];

				unset( $data['config']['formValidation'] );
			}
			foreach ( $data['config'] as $k=>$v ) {
				$config[$k]	=	$v;
			}
		}
		$data	=	$data['buffer'];
	}
}
if ( $doDebug > 0 && ( $preconfig['task'] == 'search' || $no_action ) ) {
	echo $profiler->mark( 'afterRender'.$isCached ).'<br /><br />';
}

// Dynamic Filter
if ( $config['doRecording'] ) {
	if ( !$isSearch ) {
		$app->setUserState( $list_context.'.'.$config['Itemid'].'.recording.ids', isset( $config['ids2'] ) ? $config['ids2'] : $config['ids'] );
		$app->setUserState( $list_context.'.'.$config['Itemid'].'.recording.pks', isset( $config['pks'] ) ? $config['pks2'] : $config['pks'] );
	} else {
		$config['ids_all']	=	$app->getUserState( $list_context.'.'.$config['Itemid'].'.recording.ids', '' );
		$config['pks_all']	=	$app->getUserState( $list_context.'.'.$config['Itemid'].'.recording.pks', '' );
	}
}

if ( $preconfig['show_form'] > 0 ) {
	// BeforeRender
	if ( isset( $config['process']['beforeRenderForm'] ) && count( $config['process']['beforeRenderForm'] ) ) {
		foreach ( $config['process']['beforeRenderForm'] as $process ) {
			if ( $process->type ) {
				JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'beforeRenderForm', array( $process->params, &$fields['search'], &$config['storages'], &$config ) );
			}
		}
	}
	
	$doc->fields	=	&$fields['search'];
	$infos			=	array( 'context'=>'', 'params'=>$templateStyle->params, 'path'=>$path, 'root'=>JUri::root( true ), 'template'=>$templateStyle->name, 'theme'=>$tpl['home'] );
	$doc->finalize( 'form', $search->name, $config['client'], $positions, $positions_more, $infos );
	$form			=	$doc->render( false, $rparams );
} elseif ( $preconfig['show_form'] ) {
	// BeforeRender
	if ( isset( $config['process']['beforeRenderForm'] ) && count( $config['process']['beforeRenderForm'] ) ) {
		foreach ( $config['process']['beforeRenderForm'] as $process ) {
			if ( $process->type ) {
				JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'beforeRenderForm', array( $process->params, &$fields['search'], &$config['storages'], &$config ) );
			}
		}
	}

	if ( $preconfig['task'] != 'search' ) {
		$doc->fields	=	&$fields['search'];
	}
}

// Ajax Wrapper
if ( $config['infinite'] && $app->input->get( 'wrapper' ) ) {
	$data_form			=	array();

	foreach ( $config['formFilters'] as $dynamic_filter ) {
		$data_form[$dynamic_filter]	=	'';

		if ( isset( $fields['search'][$dynamic_filter] ) ) {
			$data_form[$dynamic_filter]	=	$fields['search'][$dynamic_filter]->form;
		}
	}

	$data		=	json_encode( array(
									'count'=>(int)$total,
									'html'=>$data,
									'html_form'=>$data_form,
									'total'=>(int)$config['total']
							 	), JSON_HEX_QUOT | JSON_HEX_TAG );
}

// Unset
unset( $config['post'], $config['registry'], $config['stages'] );

// Validation
$config['validation']			=	( is_array( $config['validation'] ) && count( $config['validation'] ) ) ? implode( ',', $config['validation'] ) : '';
$config['validation_options']	=&	$options;

// WIP Stuff
$hasAjax	=	$config['doAjax'];
?>
