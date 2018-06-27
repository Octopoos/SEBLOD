<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: processing.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDevProcessing
abstract class JCckDevProcessing
{
	// isFirstItem
	public static function isFirstItem( $config )
	{
		if ( (int)$config['i'] == 0 ) {
			if ( isset( $config['uniqid'] ) && $config['uniqid'] ) {
				if ( (int)JFactory::getApplication()->input->getInt( 'start' ) == 1 ) {
					return true;
				}
			} else {
				return true;
			}
		}

		return false;
	}

	// isLastItem
	public static function isLastItem( $config )
	{
		if ( (int)$config['i'] == ( (int)$config['count'] - 1 ) ) {
			if ( isset( $config['uniqid'] ) && $config['uniqid'] ) {
				if ( (int)JFactory::getApplication()->input->getInt( 'end' ) == 1 ) {
					return true;
				}
			} else {
				return true;
			}
		}

		return false;
	}
}
?>