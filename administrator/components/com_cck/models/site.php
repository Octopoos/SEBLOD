<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: site.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
		
		if ( ! $data['id'] ) {
			$data	=	$this->preStore( $data );
		}
		
		// todo: call generic->store = JSON
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}
		if ( isset( $data['aliases'] ) && is_array( $data['aliases'] ) ) {
			$data['aliases']	=	implode( '||', $data['aliases'] );
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
		
		require_once JPATH_LIBRARIES.'/joomla/user/user.php';
		
		// Guest Group
		$guest_group	=	( $mode ) ? $this->_addGroup( $sitetitle, 1 ) : $this->_addGroup( 'Public' .' - '. $sitetitle, 1 );
		$parent_id		=	$guest_group;
		$usergroups[]	=	$guest_group;
		if ( $guest_only ) {
			//$guest_group	=	( $mode ) ? $this->_addGroup( 'Guest Only', $guest_group ) : $this->_addGroup( 'Guest Only' .' - '. $sitetitle, 1 );	// WAITING FOR JOOMLA 1.7.x FIX
			$guest_group	=	( $mode ) ? $this->_addGroup( 'Guest Only' .' - '. $sitetitle, $guest_group ) : $this->_addGroup( 'Guest Only' .' - '. $sitetitle, 1 );
		}
		
		// Guest User
		$data['guest']	=	$this->_addUser( '', $sitetitle, $sitemail, array( 0 => $guest_group ) );
		
		// Groups
		$special		=	0;
		$root			=	$this->_getRootAsset();
		$rules			=	array();
		foreach ( $groups as $k => $g ) {
			$group		=	JTable::getInstance( 'usergroup' );
			$group->load( $g );
			
			if ( $mode == 1 ) {
				//$parent_id	=	$this->_addGroup( $group->title, $parent_id );
				$parent_id		=	$this->_addGroup( $group->title .' - '. $sitetitle, $parent_id );	// WAITING FOR JOOMLA 1.7.x FIX
				$usergroups[]	=	$parent_id;
				if ( $special == 0 ) {
					$this->_updateViewlevel( 2, $parent_id );					
					$special++;
				}
				if ( ( $g == 6 || $g == 7 ) && $special == 1 ) {
					$this->_updateViewlevel( 3, $parent_id );
					$special++;
					if ( $g == 7 ) {
						$this->_prepareRules( $root, $rules, 6, $parent_id );
					}
				}
				$this->_prepareRules( $root, $rules, $g, $parent_id );
			} else {
				$parent_id		=	$g;
				$usergroups[]	=	$this->_addGroup( $group->title .' - '. $sitetitle, $parent_id );
			}
			$users[$k]			=	$this->_addUser( $group->title, $sitetitle, $sitemail );
			if ( $g < 6 ) {
				$levels[$k]		=	$this->_addViewlevel( $sitetitle .' - '. $group->title, array(), $next_level );
			}
		}
		$data['groups']	=	$usergroups;		
		if ( $mode == 1 ) {
			$this->_updateRootAsset( $root, $rules );
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
			$guest_viewlevel				=	$this->_addViewlevel( $sitetitle, $usergroups, $next_level );
			$usergroups						=	$data['groups'];
			$data['guest_only_viewlevel']	=	$this->_addViewlevel( $sitetitle .' - '. 'Guest Only', array( 0 => $guest_group ), $next_level );
		} else {
			$guest_viewlevel	=	$this->_addViewlevel( $sitetitle, $usergroups, $next_level );		
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
		
		$this->_sendSiteMail( $data, $accounts );
		
		return $data;
	}

	// postStore
	public function postStore( $pk )
	{
	}
	
	// _addGroup
	protected function _addGroup( $title, $parent_id )
	{
		$data['parent_id']	=	$parent_id;
		$data['title']		=	$title;
		
		$table				=	JTable::getInstance( 'usergroup' );
		$table->bind( $data );
		$table->store();
		
		return $table->id;
	}
	
	// _addUser
	protected function _addUser( $grouptitle, $sitetitle, $sitemail, $groups = array() )
	{
		$sitetitle2 =	JFactory::getLanguage()->transliterate( $sitetitle );
		$sitetitle2	=	trim( strtolower( $sitetitle2 ) );
		$sitetitle2	=	preg_replace( '/(\s|[^a-z0-9_])+/', '_', $sitetitle2 );
		$sitetitle2	=	trim( $sitetitle2, '_' );
		
		if ( !$grouptitle ) {
			$data['name']		=	$sitetitle;
			$data['username']	=	$sitetitle2;
			$data['password']	=	$sitetitle2;
		} else {
			$data['name']		=	$sitetitle .' - '. $grouptitle;
			$data['username']	=	strtolower( $grouptitle .'.'. $sitetitle2 );
			$data['password']	=	strtolower( $grouptitle );
		}
		$data['password2']	=	$data['password'];
		$data['email']		=	$data['username'] . $sitemail;
		
		$table				=	new JUser;
		$table->bind( $data );
		
		if ( count( $groups ) ) {
			$table->groups	=	$groups;
			$table->save();
			
			return $table->id;
		}
		
		return $table;
	}
	
	// _addViewlevel
	protected function _addViewlevel( $title, $groups = array(), &$next = 0 )
	{
		$table			=	JTable::getInstance( 'viewlevel' );
		$table->title	=	$title;
		
		if ( count( $groups ) ) {
			$data['title']		=	$title;
			$data['rules']		=	$groups;
			$table->bind( $data );
			if ( $next > 0 ) {
				$table->ordering	=	++$next;
			} else {
				$next				=	$table->getNextOrder();
				$table->ordering	=	$next;
			}
			$table->store();
			
			return $table->id;
		}
		
		return $table;
	}
	
	// _updateViewlevel
	protected function _updateViewlevel( $id, $group_id )
	{
		$table	=	JTable::getInstance( 'viewlevel' );
		$table->load( $id );
		$table->rules	=	str_replace( ']', ','.$group_id.']', $table->rules );
		$table->store();
	}
	
	// _sendSiteMail
	protected function _sendSiteMail( $data, $accounts )
	{
		$config		=	JFactory::getConfig();
		
		$email		=	$config->get( 'mailfrom' );
		$fromname	=	$config->get( 'fromname' );
		$sitename	=	$config->get( 'sitename' );
		
		$url		=	'http://'.$data['name'];
		
		$acc_admin	=	'';
		$acc_site	=	'';
		krsort( $accounts );
		if ( count( $accounts ) ) {
			foreach ( $accounts as $a ) {
				if ( $a->location == 'admin' ) {
					$acc_admin	.=	"\n".JText::_( 'COM_CCK_USERNAME' ).": ".$a->username."\n".JText::_( 'COM_CCK_PASSWORD' ).": ".$a->password."\n";
				} else {
					$acc_site	.=	"\n".JText::_( 'COM_CCK_USERNAME' ).": ".$a->username."\n".JText::_( 'COM_CCK_PASSWORD' ).": ".$a->password."\n";
				}
			}
		}
		
		$subject	=	JText::sprintf( 'COM_CCK_SITE_CREATION_SUBJECT', $sitename );
		$body		=	JText::sprintf( 'COM_CCK_SITE_CREATION_BODY', $sitename, $url, $acc_site, $url.'/administrator/', $acc_admin );
		
		JFactory::getMailer()->sendMail( $email, $fromname, $email, $subject, $body );
	}
	
	// _prepareRules
	protected function _prepareRules( $root, &$rules, $from, $to )
	{
		$actions	=	array( 'core.login.site', 'core.login.admin', 'core.login.offline', 'core.admin', 'core.manage', 'core.create', 'core.delete', 'core.edit', 'core.edit.state', 'core.edit.own' );
		foreach ( $actions as $action ) {
			if ( is_object( $root->rules2_data[$action] ) ) {
				$rule	=	$root->rules2_data[$action]->getData();
				if ( isset( $rule[$from] ) ) {
					$rules[$action][$to]	=	$rule[$from];
				}
			}
		}
	}
	
	// _getRootAsset
	protected function _getRootAsset()
	{
		$table	=	JTable::getInstance( 'asset' );
		$table->load( 1 );
		
		$rules				=	new JAccessRules( $table->rules );
		$table->rules2		=	$rules;
		$table->rules2_data	=	$table->rules2->getData();
		
		return $table;
	}

	// _updateRootAsset
	protected function _updateRootAsset( $table, $rules )
	{
		$table->rules2->merge( $rules );
		
		$table->rules	=	$table->rules2->__toString();
		unset( $table->rules2 );
		$table->store();
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