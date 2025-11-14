<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

// Helper
class modCCKBreadCrumbsHelper
{
	// getList
	public static function getList( &$params )
	{
		$app			=	Factory::getApplication();
		$pathway_mode	=	$params->get( 'pathway', 0 );
		$list			=	array();

		if ( $pathway_mode == 2 ) {
			$Itemid		=	$app->input->getInt( 'Itemid', 0 );
			$pathway	=	Table::getInstance( 'Menu' );
			$items		=	$pathway->getPath( $Itemid );
			$count 		=	count( $items );
			
			for ( $i = 1, $j = 0; $i < $count; $i++ ) {
				$list[$j]			=	new stdClass;
				$list[$j]->name		=	stripslashes( htmlspecialchars( $items[$i]->title, ENT_COMPAT, 'UTF-8' ) );
				if ( $items[$i]->type == 'alias' ) {
					$item_id		=	0;
					if ( isset( $items[$i]->params ) ) {
						$registry	=	new Registry;
						$registry->loadString( $items[$i]->params );
						$item_id	=	$registry->get( 'aliasoptions' );
					}
				} elseif ( $items[$i]->type == 'separator' || $items[$i]->type == 'heading' ) {
					$item_id		=	0;	
				} else {
					$item_id		=	$items[$i]->id;
				}
				if ( $item_id ) {
					$link			=	Route::_( 'index.php?Itemid='.$item_id );

					if ( strpos( $link, '?Itemid=' ) !== false ) {
						unset( $list[$j] );
					} else {
						$list[$j]->id			=	$item_id;
						$list[$j]->link			=	$link;
						$list[$j]->link_nosef	=	$items[$i]->link;
						$j++;
					}
				} else {
					$list[$j]->id			=	0;
					$list[$j]->link			=	'';
					$list[$j]->link_nosef	=	'';
					$j++;
				}
			}
			$count		=	count( $list );
			$pathway	=	$app->getPathway();
			$items		=	$pathway->getPathWay();
			$count2 	=	count( $items );
			if ( !$count2 ) {
				if ( isset( $list[0] ) ) {
					unset( $list[0] );
				}
			} elseif ( $count2 > $count ) {
				$count2--;
				if ( isset( $items[$count2]->name ) ) {
					$list[$j]			=	new stdClass;
					$list[$j]->link		=	'';
					$list[$j]->name		=	stripslashes( htmlspecialchars( $items[$count2]->name, ENT_COMPAT, 'UTF-8' ) );
				}
			}
		} elseif ( $pathway_mode == 1 ) {
			$base		=	Factory::getConfig()->get( 'sef_rewrite' ) ? Uri::base( true ).'/' : Uri::base( true ).'/index.php/';
			$pathway	=	$app->getPathway();
			$items		=	$pathway->getPathWay();
			$count 		=	count( $items );
			$j			=	substr_count( $base, '/' );
			
			for ( $i = 0; $i < $count; $i++ ) {
				$items[$i]->name		=	stripslashes( htmlspecialchars( $items[$i]->name, ENT_COMPAT, 'UTF-8' ) );
				$items[$i]->link_nosef	=	$items[$i]->link;
				$items[$i]->link		=	Route::_( $items[$i]->link );

				if ( ( ( strpos( $items[$i]->link, $base.'component/' ) === false ) && substr_count( $items[$i]->link, '/' ) == $j )
					 || !$items[$i]->link ) {
					$list[]	=	$items[$i];
					$j++;
				}
			}
		} else {
			$pathway	=	$app->getPathway();
			$items		=	$pathway->getPathWay();
			$count 		=	count( $items );

			for ( $i = 0; $i < $count; $i++ ) {
				$list[$i]				=	new stdClass;
				$list[$i]->name			=	stripslashes( htmlspecialchars( $items[$i]->name, ENT_COMPAT, 'UTF-8' ) );
				$list[$i]->link			=	Route::_( $items[$i]->link );
				$list[$i]->link_nosef	=	$items[$i]->link;

				$parts					=	explode( 'Itemid=', $items[$i]->link );
				$list[$i]->id			=	( isset( $parts[1] ) ) ? $parts[1] : 0;
			}
		}

		return self::_getItems( $params, $list );
	}
	
	// getItems
	protected static function _getItems( &$params, $items )
	{
		$app	=	Factory::getApplication();
		
		if ( $params->get( 'showHome', 1 ) ) {
			$item				=	new stdClass;
			$item->id			=	$app->getMenu()->getDefault()->id;
			$item->link			=	Route::_( 'index.php?Itemid='.$app->getMenu()->getDefault()->id );
			$item->link_nosef	=	'index.php?Itemid='.$app->getMenu()->getDefault()->id;
			$item->name			=	htmlspecialchars( $params->get( 'homeText', Text::_( 'MOD_CCK_BREADCRUMBS_HOME' ) ) );
			
			array_unshift( $items, $item );
		}
		
		return $items;
	}

	// setSeparator
	public static function setSeparator( $custom = null )
	{
		$lang	=	Factory::getLanguage();
		
		if ( $custom == null ) {
			if ( $lang->isRTL() ) {
				$_separator	=	HTMLHelper::_( 'image', 'system/arrow_rtl.png', null, null, true );
			} else {
				$_separator	=	HTMLHelper::_( 'image', 'system/arrow.png', null, null, true );
			}
		} else {
			$_separator	=	htmlspecialchars( $custom );
		}
		
		return $_separator;
	}
}
?>