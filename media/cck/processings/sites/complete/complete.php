<?php
defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

if ( $context != 'com_cck.site' ) {
	return;
}
if ( !$isNew ) {
	return;
}

$app			=	JFactory::getApplication();
$mode			=	JCck::getConfig_Param( 'multisite_integration', '1' );
$type			=	$app->input->getString( 'type', '2,7' ); /* '7' || '2,7' || 2,3,6,7 */
$groups			=	explode( ',', $type );
$groups			=	ArrayHelper::toInteger( $groups );
$guest_only		=	( count( $groups ) > 1 ) ? 1 : 0;
$levels			=	array();
$sitetitle		=	$item->title;
$sitename		=	$item->name;
$sitemail		=	JFactory::getConfig()->get( 'mailfrom' );
$sitemail		=	substr( $sitemail, strpos( $sitemail, '@' ) );

$existing_users	=	array();
$next_level		=	0;
$parent			=	null;
$usergroups		=	array();
$users			=	array();
$users_author	=	array();
$users_bridge	=	array();
$users_force	=	array();
$users_more		=	array();

if ( $item->parent_id ) {
	$parent			=	JCckDatabase::loadObject( 'SELECT guest_only_group, groups FROM #__cck_core_sites WHERE id = '.(int)$item->parent_id );

	if ( is_object( $parent ) ) {
		$parent->groups		=	explode( ',', $parent->groups );
		$parent->groups[]	=	0;

		array_shift( $parent->groups );
	}
}

if ( isset( $item->groups ) && $item->groups != '' ) {
	$item->groups		=	json_decode( $item->groups, true );

	if ( count( $item->groups ) ) {
		$existing_users	=	$item->groups;
	}
	unset( $item->groups );
}
require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/site.php';
JLoader::register( 'JUser', JPATH_PLATFORM.'/joomla/user/user.php' );

// Guest Group
$guest_group	=	( $mode ) ? CCK_TableSiteHelper::addUserGroup( $sitetitle, 1 )
							  : CCK_TableSiteHelper::addUserGroup( 'Public' .' - '. $sitetitle, 1 );
$parent_id		=	$guest_group;
$usergroups[]	=	$guest_group;

if ( $guest_only ) {
	if ( $mode ) {
		$guest_group	=	CCK_TableSiteHelper::addUserGroup( 'Guest Only' .' - '. $sitetitle, $guest_group );
		CCK_TableSiteHelper::updateViewLevel( 5, $guest_group );
	} else {
		$guest_group	=	CCK_TableSiteHelper::addUserGroup( 'Guest Only' .' - '. $sitetitle, 1 );
	}
}

// Guest User
$guest_groups	=	array( 0=>$guest_group );

if ( is_object( $parent ) && $parent->guest_only_group ) {
	$guest_groups[1]	=	$parent->guest_only_group;
}
$item->guest	=	CCK_TableSiteHelper::addUser( '', $sitetitle, $sitemail, $guest_groups );

// Groups
$special		=	0;
$root			=	CCK_TableSiteHelper::getRootAsset();
$rules			=	array();

foreach ( $groups as $i=>$g ) {
	$group		=	JTable::getInstance( 'Usergroup' );
	$group->load( $g );
	
	// Usergroup
	if ( $mode == 1 ) {
		$parent_id		=	CCK_TableSiteHelper::addUserGroup( $group->title .' - '. $sitetitle, $parent_id );
		$usergroups[]	=	$parent_id;
		if ( $special == 0 ) {
			CCK_TableSiteHelper::updateViewLevel( 2, $parent_id );
			$special++;
		}
		if ( ( $g == 6 || $g == 7 ) && $special == 1 ) {
			CCK_TableSiteHelper::updateViewLevel( 3, $parent_id );
			$special++;
			if ( $g == 7 ) {
				CCK_TableSiteHelper::prepareRules( $root, $rules, 6, $parent_id );
			}
		}
		CCK_TableSiteHelper::prepareRules( $root, $rules, $g, $parent_id );
	} else {
		$parent_id		=	$g;
		$usergroups[]	=	CCK_TableSiteHelper::addUserGroup( $group->title .' - '. $sitetitle, $parent_id );
	}

	// User
	$k	=	(string)$g;

	$users_author[$i]	=	0;
	$users_bridge[$i]	=	0;
	$users_force[$i]	=	0;
	$users_more[$i]		=	array();

	if ( isset( $existing_users[$k] ) ) {
		if ( isset( $existing_users[$k]['bridge'] ) ) {
			$users_bridge[$i]	=	(int)$existing_users[$k]['bridge'];

			unset( $existing_users[$k]['bridge'] );
		}
		if ( isset( $existing_users[$k]['force_password'] ) ) {
			$users_force[$i]	=	(int)$existing_users[$k]['force_password'];

			unset( $existing_users[$k]['force_password'] );
		}
		if ( isset( $existing_users[$k]['set_author'] ) ) {
			$users_author[$i]	=	(int)$existing_users[$k]['set_author'];

			unset( $existing_users[$k]['set_author'] );
		}
		if ( isset( $existing_users[$k]['more'] ) ) {
			$users_more[$i]		=	$existing_users[$k]['more'];

			unset( $existing_users[$k]['more'] );
		}
		$users[$i]		=	CCK_TableSiteHelper::addUser( $existing_users[$k] );
	} else {
		$users[$i]		=	CCK_TableSiteHelper::addUser( $group->title, $sitetitle, $sitemail );
	}
	
	// Viewlevel
	if ( $g < 6 ) {
		$levels[$i]		=	CCK_TableSiteHelper::addViewLevel( $sitetitle .' - '. $group->title, array(), $next_level );
	}
}
$item->groups	=	$usergroups;	

if ( $mode == 1 ) {
	CCK_TableSiteHelper::updateRootAsset( $root, $rules );
}

// Guest Viewlevel
$usergroups		=	$item->groups;
if ( $guest_only ) {
	$item->guest_only_group		=	$guest_group;
	$usergroups[]				=	$guest_group;
	$guest_viewlevel			=	CCK_TableSiteHelper::addViewLevel( $sitetitle, $usergroups, $next_level );
	$usergroups					=	$item->groups;
	$item->guest_only_viewlevel	=	CCK_TableSiteHelper::addViewLevel( $sitetitle .' - '. 'Guest Only', array( 0 => $guest_group ), $next_level );
} else {
	$guest_viewlevel			=	CCK_TableSiteHelper::addViewLevel( $sitetitle, $usergroups, $next_level );		
}

// Viewlevels
$viewlevels[]		=	$guest_viewlevel;
foreach ( $levels as $l ) {
	array_shift( $usergroups );
	$levels			=	array( 'title'=>$l->title, 'rules'=>$usergroups );
	$l->bind( $levels );
	if ( is_null( $l->ordering ) ) {
		$l->ordering	=	++$next_level;
	}
	$l->store();
	$viewlevels[]	=	$l->id;
}
$item->viewlevels	=	$viewlevels;

// Users
krsort( $users );
krsort( $users_author );
krsort( $users_bridge );
krsort( $users_force );
krsort( $users_more );

$accounts		=	array();
$usergroups[]	=	0;

$content_type	=	'user';
$integration	=	JCckDatabase::loadObject( 'SELECT options FROM #__cck_core_objects WHERE name = "joomla_user"' );

if ( is_object( $integration ) ) {
	$integration->options	=	new JRegistry( $integration->options );	
	$content_type			=	$integration->options->get( 'default_type', 'user' );
}

$plg		=	JPluginHelper::getPlugin( 'cck_storage_location', 'joomla_user' );
$plg_params	=	new JRegistry( $plg->params );
$plg_params	=	$plg_params->toArray();

if ( isset( $plg_params['bridge_default-access'] ) ) {
	$plg_params['bridge_default-access']	=	$guest_viewlevel;
}

foreach ( $users as $k=>$u ) {
	array_pop( $usergroups );

	$id			=	0;
	$u->groups	=	$usergroups;

	if ( is_object( $parent ) && $parent->groups ) {
		array_pop( $parent->groups );

		$u->groups	=	array_merge( $u->groups, $parent->groups );
	}

	// Force Password
	if ( isset( $users_force[$k] ) && $users_force[$k] ) {
		$u->password	=	$u->password_clear;
	}
	$u->save();

	if ( !$u->id ) {
		continue;
	}

	// Set As Author
	if ( isset( $users_author[$k] ) && $users_author[$k] ) {
		$item->created_user_id	=	$u->id;
	}

	// Store More
	if ( isset( $users_more[$k] ) && count( $users_more[$k] ) ) {
		// Core
		$core					=	JCckTable::getInstance( '#__cck_core', 'id' );
		$core->cck				=	$content_type;
		$core->pk 				=	$u->id;
		$core->storage_location =	'joomla_user';
		$core->storage_table	=	'';
		$core->author_id 		=	$u->id;
		$core->parent_id 		=	0;
		$core->date_time 		=	JFactory::getDate()->toSql();
		$core->check();
		$core->store();
		$id						=	(int)$core->id;

		// More
		$users_more[$k]['cck']	=	$content_type;

		$more					=	JCckTable::getInstance( '#__cck_store_item_users', 'id' );
		$more->load( $u->id, true );
		$more->bind( $users_more[$k] );
		$more->store();
	}

	// Do Bridge
	if ( $id && isset( $users_bridge[$k] ) && $users_bridge[$k] ) {
		$storages 						= 	array();
		$storages['#__users']			=	(array)JCckDatabase::loadObject( 'SELECT * FROM `#__users` WHERE id='.(int)$u->id );

		if ( is_object( $more ) ) {
			$storages['#__cck_store_item_users']	=	(array)$more->getProperties();
		}

		$config		=	array(
							'author'=>$u->id,
							'id'=>$id,
							'parent'=>0,
							'parent_id'=>0,
							'pk'=>$u->id,
							'storages'=>$storages,
							'type'=>'user'
						);
		$location	=	array(
							'_'=>(object)array( 'location'=>'joomla_user', 'state'=>false, 'table'=>'#__users' )
						);
		$pk			=	$u->id;

		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		@JCckPluginLocation::g_doBridge( 'joomla_article', $pk, $location, $config, $plg_params );
	}

	// Send Mails
	if ( $u->authorise( 'core.login.admin' ) ) {
		$accounts[]	=	(object)array( 'username'=>$u->username, 'password'=>$u->password_clear, 'location'=>'admin' );
	} else {
		$accounts[]	=	(object)array( 'username'=>$u->username, 'password'=>$u->password_clear, 'location'=>'site' );
	}
}

if ( (int)JCck::getConfig_Param( 'multisite_mail_to_admin', '1' ) == 1 ) {
	CCK_TableSiteHelper::sendMails( $item, $accounts );
}

// Finalize
if ( is_array( $item->groups ) ) {
	$item->groups		=	implode( ',', $item->groups );
}
if ( is_array( $item->viewlevels ) ) {
	$item->public_viewlevel	=	$item->viewlevels[0];
	$item->viewlevels		=	implode( ',', $item->viewlevels );
}
?>