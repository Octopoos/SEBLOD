<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: user.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( 'JPATH_PLATFORM' ) or die;

// CCKUser
class CCKUser extends JUser
{
	protected $_user	=	null;

	// __construct
	public function __construct( $identifier = 0 )
	{
		parent::__construct( $identifier );
		
		$this->_user	=	JFactory::getUser( $identifier );
	}
	
	// setAuthorisedViewLevels
	public function setAuthorisedViewLevels( $viewlevels = array() )
	{
		$this->_user->_authLevels	=	$viewlevels;
	}
	
	// setAuthorisedGroups
	public function setAuthorisedGroups( $groups = array() )
	{
		$this->_user->_authGroups	=	$groups;
	}
	
	// makeHimLive
	public function makeHimLive()
	{
		JFactory::getSession()->set( 'user', $this->_user );
	}
}
?>