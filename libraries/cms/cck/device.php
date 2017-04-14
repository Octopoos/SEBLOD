<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: device.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'Mobile_Detect', JPATH_PLATFORM.'/cck/misc/Mobile_Detect.php' );

// JCckDevice
class JCckDevice extends Mobile_Detect
{
}
?>