<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: user.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( 'JPATH_PLATFORM' ) or die;

// CCKUser
class CCKUser extends JUser
{
	protected $_user	=	null;

	// __construct
	public function __construct( $identifier = 0, $from_user = null )
	{
		parent::__construct( $identifier );
		
		$this->_user	=	JFactory::getUser( $identifier );

		if ( is_object( $from_user ) ) {
			$params		=	array(
								'from_id'=>(int)$from_user->id,
								'from_id_session'=>(int)JCckDatabase::loadResult( 'SELECT userid FROM #__session WHERE session_id = "'.JFactory::getSession()->getId().'"' ),
								'groups'=>array(),
								'levels'=>array()
							);

			$app_identities				=	JCckDevHelper::getApp( 'identities' );
			$app_identities_group_id	=	$app_identities->params->get( '_impersonate.user_group.Incognito', 0 );
			$app_identities_group		=	$app_identities->params->get( '_impersonate.user_group.Impostor', 0 );
			$app_identities_level		=	$app_identities->params->get( '_impersonate.viewing_access_level', 0 );

			if ( $app_identities_group || $app_identities_level ) {
				if ( !( in_array( '8', $from_user->groups ) || ( $app_identities_group_id && in_array( (string)$app_identities_group_id, $from_user->groups ) ) ) ) {
					if ( $app_identities_group ) {
						$params['groups'][]	=	$app_identities_group;
					}
					if ( $app_identities_level ) {
						$params['levels'][]	=	$app_identities_level;
					}
				}
			}

			JFactory::getSession()->set( 'cck_login_as', json_encode( $params ) );
		}
	}

	// setAuthorisedGroups
	public function setAuthorisedGroups( $groups = array() )
	{
		$this->_user->_authGroups	=	$groups;
	}
	
	// setAuthorisedViewLevels
	public function setAuthorisedViewLevels( $viewlevels = array() )
	{
		$this->_user->_authLevels	=	$viewlevels;
	}

	// makeHimLive
	public function makeHimLive()
	{
		$as	=	JFactory::getSession()->get( 'cck_login_as', '' );

		if ( $as != '' ) {
			$as	=	json_decode( $as, true );

			if ( isset( $as['from_id'] ) ) {
				$this->_user->from_id	=	$as['from_id'];
			}
			if ( isset( $as['from_id_session'] ) ) {
				$this->_user->from_id_session	=	$as['from_id_session'];
			}
			
			if ( isset( $as['groups'] ) ) {
				foreach ( $as['groups'] as $group_id ) {
					$this->_user->_authGroups[]				=	(int)$group_id;
					$this->_user->groups[(int)$group_id]	=	(string)$group_id;
				}
			}
			
			if ( isset( $as['levels'] ) ) {
				foreach ( $as['levels'] as $level_id ) {
					$this->_user->_authLevels[]				=	(int)$level_id;
				}
			}
		}

		JFactory::getSession()->set( 'user', $this->_user );
	}
}
?>