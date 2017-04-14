<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: object.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckVersionObject
class JCckVersionObject
{
	// getDevStatus
	public function getDevStatus()
	{
		return $this->DEV_STATUS;
	}

	// getFullVersion
	public function getFullVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL.( $this->DEV_STATUS ? ' '.$this->DEV_STATUS : '' );
	}

	// getShortVersion
	public function getShortVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL;
	}
}
?>