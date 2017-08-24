<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: site.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableAsset', JPATH_PLATFORM.'/joomla/database/table/asset.php' );
JLoader::register( 'JTableUsergroup', JPATH_PLATFORM.'/joomla/database/table/usergroup.php' );
JLoader::register( 'JTableViewlevel', JPATH_PLATFORM.'/joomla/database/table/viewlevel.php' );

// Model
class CCKModelSite extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'site';
	
	// canDelete
	protected function canDelete( $record )
	{
		$user	=	JFactory::getUser();
		
		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.delete', CCK_COM.'.folder.'.(int)$record->folder );
		} else {
			// Component Permissions
			return parent::canDelete( $record );
		}
	}

	// canEditState
	protected function canEditState( $record )
	{
		$user	=	JFactory::getUser();

		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.edit.state', CCK_COM.'.folder.'.(int)$record->folder );
		} else {
			// Component Permissions
			return parent::canEditState( $record );
		}
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		if ( ( $type = $app->getUserState( CCK_COM.'.edit.site.type' ) ) != '' ) {
			$this->setState( 'type', $type );
		}
		
		$this->setState( 'site.id', $pk );
	}
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		$form	=	$this->loadForm( CCK_COM.'.'.$this->vName, $this->vName, array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		
		return $form;
	}
	
	// getItem
	public function getItem( $pk = null )
	{
		if ( $item = parent::getItem( $pk ) ) {
			//
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Site', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	JFactory::getApplication()->getUserState( CCK_COM.'.edit.'.$this->vName.'.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$data					=	JRequest::get( 'post' );
		$data['description']	=	JRequest::getVar( 'description', '', '', 'string', JREQUEST_ALLOWRAW );
		
		if ( $data['context'] != '' ) {
			$data['name']	.=	'@'.$data['context'];
		}

		if ( ! $data['id'] ) {
			// $data	=	$this->preStore( $data );
		}
		
		if ( isset( $data['aliases'] ) && is_array( $data['aliases'] ) && count( $data['aliases'] ) ) {
			$data['aliases']	=	implode( '||', $data['aliases'] );
		}
		if ( isset( $data['exclusions'] ) && is_array( $data['exclusions'] ) && count( $data['exclusions'] ) ) {
			$data['json']['configuration']['exclusions']	=	implode( '||', $data['exclusions'] );
			
			unset( $data['exclusions'] );
		}
		
		// todo: call generic->store = JSON
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}
		
		// todo: call plugins->prepareStore()
		$data['groups']		=	$this->_implodeValues( $data['groups'], $data['guest_only_group'] );
		$data['viewlevels']	=	$this->_implodeValues( $data['viewlevels'], $data['guest_only_viewlevel'] );
		
		return $data;
	}

	// preStore
	public function preStore( $data )
	{
		$mode		=	JCck::getConfig_Param( 'multisite_integration', '1' );
		
		$groups		=	explode( ',', $data['type'] );
		$guest_only	=	( count( $groups ) > 1 ) ? 1 : 0;
		$sitetitle	=	$data['title'];
		$sitename	=	$data['name'];
		$sitemail	=	JFactory::getConfig()->get( 'mailfrom' );
		$sitemail	=	substr( $sitemail, strpos( $sitemail, '@' ) );
		
		$users		=	array();
		$usergroups	=	array();
		
		$next_level	=	0;
		
		JLoader::register( 'JUser', JPATH_PLATFORM.'/joomla/user/user.php' );
		
		// Guest Group
		$guest_group	=	( $mode ) ? CCK_TableSiteHelper::addUserGroup( $sitetitle, 1 ) : CCK_TableSiteHelper::addUserGroup( 'Public' .' - '. $sitetitle, 1 );
		$parent_id		=	$guest_group;
		$usergroups[]	=	$guest_group;
		if ( $guest_only ) {
			$guest_group	=	( $mode ) ? CCK_TableSiteHelper::addUserGroup( 'Guest Only' .' - '. $sitetitle, $guest_group ) : CCK_TableSiteHelper::addUserGroup( 'Guest Only' .' - '. $sitetitle, 1 );
		}
		
		// Guest User
		$data['guest']	=	CCK_TableSiteHelper::addUser( '', $sitetitle, $sitemail, array( 0 => $guest_group ) );
		
		// Groups
		$special		=	0;
		$root			=	CCK_TableSiteHelper::getRootAsset();
		$rules			=	array();
		foreach ( $groups as $k => $g ) {
			$group		=	JTable::getInstance( 'Usergroup' );
			$group->load( $g );
			
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
			$users[$k]			=	CCK_TableSiteHelper::addUser( $group->title, $sitetitle, $sitemail );
			if ( $g < 6 ) {
				$levels[$k]		=	CCK_TableSiteHelper::addViewLevel( $sitetitle .' - '. $group->title, array(), $next_level );
			}
		}
		$data['groups']	=	$usergroups;		
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
		$usergroups			=	$data['groups'];
		if ( $guest_only ) {
			$data['guest_only_group']		=	$guest_group;
			$usergroups[]					=	$guest_group;
			$guest_viewlevel				=	CCK_TableSiteHelper::addViewLevel( $sitetitle, $usergroups, $next_level );
			$usergroups						=	$data['groups'];
			$data['guest_only_viewlevel']	=	CCK_TableSiteHelper::addViewLevel( $sitetitle .' - '. 'Guest Only', array( 0 => $guest_group ), $next_level );
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
		$data['viewlevels']	=	$viewlevels;
		
		CCK_TableSiteHelper::sendMails( $data, $accounts );
		
		return $data;
	}

	// postStore
	public function postStore( $pk )
	{
	}
	
	// _implodeValues
	protected function _implodeValues( $values, $excluded = '' )
	{
		if ( !$excluded ) {
			return implode( ',', $values );
		}

		$str	=	'';
		foreach ( $values as $i=>$v ) {
			if ( $v != $excluded ) {
				$str	.=	','.$v;
			}
		}

		return substr( $str, 1 );
	}
}
?>