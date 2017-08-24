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

// Table
class CCK_TableSite extends JTable
{	
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_sites', 'id', $db );
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		$this->name	=	str_replace( 'http://', '', $this->name );
		$this->name	=	( $this->name[strlen( $this->name )-1] == '/' ) ? substr( $this->name, 0, -1 ) : $this->name;
		if ( empty( $this->name ) ) {
			return false;
		}
		
		if ( $this->id ) {
			//
		} else {
			if ( !(int)$this->created_date ) {
				$this->created_date		=	JFactory::getDate()->toSql();
			}
			if ( empty( $this->created_user_id ) ) {
				$this->created_user_id	=	JFactory::getUser()->id;
			}
		}

		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			JCckDatabase::execute( 'DELETE IGNORE a.*'
								.  ' FROM #__cck_core AS a'
								.  ' WHERE a.storage_location="cck_site" AND a.pk="'.(int)$this->id.'"' );
		}

		return parent::delete();
	}

	// store
	public function store( $updateNulls = false )
	{
		$result	=	parent::store( $updateNulls );

		if ( $result !== false ) {
			if ( !(int)$this->access || (int)$this->access == 1 ) {
				$viewlevels	=	$this->viewlevels;

				if ( is_string( $viewlevels ) ) {
					$viewlevels	=	explode( ',', $viewlevels );
				}
				if ( $viewlevels[0] ) {
					$this->access	=	$viewlevels[0];
					$this->store();
				}
			}
		}

		return $result;
	}
}

// CCK_TableSiteHelper
abstract class CCK_TableSiteHelper
{
	// addUser
	public static function addUser( $grouptitle, $sitetitle = '', $sitemail = '', $groups = array() )
	{
		if ( is_array( $grouptitle ) ) {
			$data		=	$grouptitle;
		} else {
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
		}
		
		$table				=	new JUser;
		$table->bind( $data );
		
		if ( count( $groups ) ) {
			$table->groups	=	$groups;
			$table->save();
			
			return $table->id;
		}
		
		return $table;
	}

	// addUserGroup
	public static function addUserGroup( $title, $parent_id )
	{
		$data['parent_id']	=	$parent_id;
		$data['title']		=	$title;
		
		$table				=	JTable::getInstance( 'Usergroup' );
		$table->bind( $data );
		$table->store();
		
		return $table->id;
	}

	// addViewLevel
	public static function addViewLevel( $title, $groups = array(), &$next = 0 )
	{
		$table			=	JTable::getInstance( 'Viewlevel' );
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
	
	// getRootAsset
	public static function getRootAsset()
	{
		$table	=	JTable::getInstance( 'Asset' );
		$table->load( 1 );
		
		$rules				=	new JAccessRules( $table->rules );
		$table->rules2		=	$rules;
		$table->rules2_data	=	$table->rules2->getData();
		
		return $table;
	}

	// prepareRules
	public static function prepareRules( $root, &$rules, $from, $to )
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

	// sendMails
	public static function sendMails( $site, $accounts )
	{
		$config		=	JFactory::getConfig();
		
		$email		=	$config->get( 'mailfrom' );
		$fromname	=	$config->get( 'fromname' );
		$sitename	=	$config->get( 'sitename' );
		$url		=	'http://'.$site->name;
		
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

	// updateRootAsset
	public static function updateRootAsset( $table, $rules )
	{
		$table->rules2->merge( $rules );
		
		$table->rules	=	$table->rules2->__toString();
		unset( $table->rules2 );
		$table->store();

		return $table;
	}

	// updateViewLevel
	public static function updateViewLevel( $id, $group_id )
	{
		$table			=	JTable::getInstance( 'Viewlevel' );
		$table->load( $id );
		$table->rules	=	str_replace( ']', ','.$group_id.']', $table->rules );
		$table->store();

		return $table;
	}
}
?>