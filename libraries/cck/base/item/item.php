<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: item.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Item
class CCK_Item
{
	// getAssociation
	public static function getAssociation( $id, $strict = false )
	{
		$lang_tag		=	JFactory::getLanguage()->getTag();

		if ( $lang_tag != 'en-GB'/*JComponentHelper::getParams( 'com_languages' )->get( 'site', 'en-GB' )*/ ) {
			$assoc_item		=	JCckDatabase::loadObject( 'SELECT id, pk, storage_location, storage_table FROM #__cck_core WHERE id = '.(int)$id );

			if ( is_object( $assoc_item ) && $assoc_item->pk ) {
				$context		=	'com_cck.free'; /* TODO */

				if ( $assoc_item->storage_location == 'free' ) {
					$context	.=	'.'.$assoc_item->storage_table;
				}

				$associations	=	JLanguageAssociations::getAssociations( 'com_cck', $assoc_item->storage_table, $context, (int)$assoc_item->pk, 'id', '', '' );

				if ( isset( $associations[$lang_tag] ) ) {
					if ( (int)$associations[$lang_tag]->id ) {
						$assoc_id	=	$associations[$lang_tag]->id;

						if ( $assoc_id ) {
							$id	=	(int)JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_table = "'.$assoc_item->storage_table.'" AND pk = '.(int)$assoc_id );
						}
					}
				}
			}
			if ( $strict ) {
				if ( !is_object( $assoc_item ) || $assoc_item->id == $id ) {
					$id	=	0;
				}
			}
		}
		
		return $id;
	}

	// prepare
	public static function prepare( $str, $params = null )
	{
		return JHtml::_( 'content.prepare', $str, $params );
	}

	// render
	public static function render( $id, $params = null, $cache = true )
	{
		if ( !(int)$id ) {
			return '';
		}

		$prefix	=	'';
		$suffix	=	'';
		$user	=	JFactory::getUser();

		if ( $user->id && !$user->guest ) {
			if ( isset( $user->groups[8] ) || isset( $user->groups[22] ) ) {
				$prefix	=	'_';
			} else {
				$cache	=	false;	
			}
		}

		if ( $cache ) {
			/*
			if ( !is_null( $params ) ) {
				$suffix	=	'_'.md5( json_encode( $params ) );
			}
			*/

			$cache		=	JFactory::getCache( $prefix.'cck_item@'.$id.$suffix );
			$cache->setCaching( 1 );

			return $cache->get( array( 'CCK_Item', 'prepare' ), array( '::cck::'.$id.'::/cck::' ) );
		} else {
			return self::prepare( '::cck::'.$id.'::/cck::', $params );
		}
	}
}
?>