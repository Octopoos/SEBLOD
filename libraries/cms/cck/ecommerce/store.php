<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: promotion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceStore
abstract class JCckEcommerceStore
{
	// apply
	public static function countItems( $store_id, $object = 'joomla_article' )
	{
		static $cache	=	array();
		$count	=	0;
		$user	=	JFactory::getUser();
		
		if ( !isset( $cache[$store_id] ) ) {
			$cache[$store_id]	=	JCckDatabase::loadResult( 'SELECT COUNT(a.id) FROM #__cck_core AS a'
															. ' LEFT JOIN #__content AS b ON (b.id = a.pk AND a.storage_location = "joomla_article")'
															. ' WHERE a.store_id = '.$store_id.' AND b.state = 1' );
		}

		return $cache[$store_id];
	}
}
?>