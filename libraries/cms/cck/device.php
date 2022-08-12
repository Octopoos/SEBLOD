<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: device.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Environment\Browser;

JLoader::register( 'Mobile_Detect', JPATH_PLATFORM.'/cck/misc/Mobile_Detect.php' );

// JCckBrowser
class JCckBrowser extends Browser
{
	protected static $instances = array();

	// getInstance
	public static function getInstance( $userAgent = null, $accept = null )
	{
		$signature	=	serialize( array( $userAgent, $accept ) );

		if ( empty( self::$instances[$signature] ) ) {
			self::$instances[$signature]	=	new JCckBrowser( $userAgent, $accept );
		}

		return self::$instances[$signature];
	}

	// setVersions
	public function setVersions( $version )
	{
		if ( strpos( $version[1], '.' ) !== false) {
			$parts	=	explode( '.', $version[1] );

			$this->majorVersion	=	$parts[0];
			$this->minorVersion	=	$parts[1];
		} else {
			$this->majorVersion	=	$version[1];
			$this->minorVersion	=	0;
		}
	}
}

// JCckDevice
class JCckDevice extends Mobile_Detect
{
	protected static $_instance;

	protected $_browser	=	null;

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// getInstance
	public static function getInstance()
	{
		if ( !self::$_instance ) {
			self::$_instance			=	new JCckDevice;
			self::$_instance->_browser	=	JCckBrowser::getInstance();

			$version	=	array();

			if ( self::$_instance->_browser->getBrowser() == 'safari'
			  && preg_match( '|QQBrowserLite\/([0-9.]+)|', self::$_instance->_browser->getAgentString(), $version ) ) {
				self::$_instance->_browser->setBrowser( 'qqbrowserlite' );
				self::$_instance->_browser->setVersions( $version );
			} elseif ( preg_match( '|QQBrowser\/([0-9.]+)|', self::$_instance->_browser->getAgentString(), $version ) ) {
				self::$_instance->_browser->setBrowser( 'qqbrowser' );
				self::$_instance->_browser->setVersions( $version );
			}
		}

		return self::$_instance;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// getBrowserName
	public function getBrowserName()
	{
		return $this->_browser->getBrowser();
	}

	// getBrowserVersion
	public function getBrowserVersion()
	{
		return $this->_browser->getVersion();
	}

	// isMacOs
	public function isMacOs()
	{
		if ( $this->isMobile() ) {
			return false;
		}

		return $this->_browser->getPlatform() == 'mac' ? true : false; 
	}
}
?>