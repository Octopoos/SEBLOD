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

// JCckDevice
class JCckDevice extends Mobile_Detect
{
	protected $_platform	=	null;

	// isMacOs
	public function isMacOs()
	{
		if ( $this->isMobile() ) {
			return false;
		} elseif ( is_null( $this->_platform ) ) {
			$this->_platform	=	Browser::getInstance();
		}

		return $this->_platform->getPlatform() == 'mac' ? true : false; 
	}
}
?>