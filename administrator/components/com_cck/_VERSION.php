<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: _VERSION.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckVersion
final class JCckVersion
{
	public $RELEASE = '3.7';
	
	public $DEV_LEVEL = '1';

	public $DEV_STATUS = '';
	
	// getDevStatus
	public function getDevStatus()
	{
		return $this->DEV_STATUS;
	}

	// getShortVersion
	public function getShortVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL.( $this->DEV_STATUS ? ' '.$this->DEV_STATUS : '' );
	}
}
?>