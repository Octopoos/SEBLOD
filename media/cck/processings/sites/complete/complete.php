<?php
defined( '_JEXEC' ) or die;

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
$guest_only		=	( count( $groups ) > 1 ) ? 1 : 0;
$sitetitle		=	$item->title;
$sitename		=	$item->name;
$sitemail		=	JFactory::getConfig()->get( 'mailfrom' );
$sitemail		=	substr( $sitemail, strpos( $sitemail, '@' ) );

$existing_users	=	array();
$next_level		=	0;
$users			=	array();
$usergroups		=	array();

if ( isset( $item->groups ) && $item->groups != '' ) {
	$item->groups		=	json_decode( $item->groups, true );

	if ( count( $item->groups ) ) {
		$existing_users	=	$item->groups;
	}
	unset( $item->groups );
}
require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/site.php';
require_once JPATH_LIBRARIES.'/joomla/user/user.php';

// Guest Group
$guest_group	=	( $mode ) ? CCK_TableSiteHelper::addUserGroup( $sitetitle, 1 )
							  : CCK_TableSiteHelper::addUserGroup( 'Public' .' - '. $sitetitle, 1 );
$parent_id		=	$guest_group;
$usergroups[]	=	$guest_group;

if ( $guest_only ) {
	$guest_group	=	( $mode ) ? CCK_TableSiteHelper::addUserGroup( 'Guest Only' .' - '. $sitetitle, $guest_group )
								  : CCK_TableSiteHelper::addUserGroup( 'Guest Only' .' - '. $sitetitle, 1 );
}

// Guest User
$item->guest	=	CCK_TableSiteHelper::addUser( '', $sitetitle, $sitemail, array( 0=>$guest_group ) );

// Groups
$special		=	0;
$root			=	CCK_TableSiteHelper::getRootAsset();
$rules			=	array();

foreach ( $groups as $i=>$g ) {
	$group		=	JTable::getInstance( 'usergroup' );
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
	if ( isset( $existing_users[$k] ) ) {
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

// Users
krsort( $users );
$accounts		=	array();
$usergroups[]	=	0;

foreach ( $users as $u ) {
	array_pop( $usergroups );
	$u->groups	=	$usergroups;
	$u->save();
	if ( $u->authorise( 'core.login.admin' ) ) {
		$accounts[]	=	(object)array( 'username'=>$u->username, 'password'=>$u->password_clear, 'location'=>'admin' );
	} else {
		$accounts[]	=	(object)array( 'username'=>$u->username, 'password'=>$u->password_clear, 'location'=>'site' );
	}
}

// Guest Viewlevel
$usergroups			=	$item->groups;
if ( $guest_only ) {
	$item->guest_only_group		=	$guest_group;
	$usergroups[]					=	$guest_group;
	$guest_viewlevel				=	CCK_TableSiteHelper::addViewLevel( $sitetitle, $usergroups, $next_level );
	$usergroups						=	$item->groups;
	$item->guest_only_viewlevel	=	CCK_TableSiteHelper::addViewLevel( $sitetitle .' - '. 'Guest Only', array( 0 => $guest_group ), $next_level );
} else {
	$guest_viewlevel	=	CCK_TableSiteHelper::addViewLevel( $sitetitle, $usergroups, $next_level );		
}

// Viewlevels
$viewlevels[]	=	$guest_viewlevel;
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

CCK_TableSiteHelper::sendMails( $item, $accounts );

// Finalize
if ( is_array( $item->groups ) ) {
	$item->groups		=	implode( ',', $item->groups );
}
if ( is_array( $item->viewlevels ) ) {
	$item->viewlevels	=	implode( ',', $item->viewlevels );
}
?>