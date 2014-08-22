<?php
/**
 * @package     Joomla.Platform
 * @subpackage  User
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport( 'joomla.access.access' );
jimport( 'joomla.registry.registry' );

// CCKUser
class CCKUser extends JUser
{
	protected $_user	=	null;

	// __construct
	public function __construct( $identifier = 0 )
	{
		parent::__construct( $identifier );
		
		$this->_user				=	JFactory::getUser( $identifier );
		$this->_user->cck_multisite	=	1;
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
		
		//echo implode( ',', JFactory::getUser()->getAuthorisedViewLevels() );
	}
}
