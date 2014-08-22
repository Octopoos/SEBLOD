<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: router.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.categories' );

// CckBuildRoute
function CckBuildRoute( &$query )
{
	$app		=	JFactory::getApplication();
	$menu		=	$app->getMenu();
	$segments	=	array();

	// Prevent..
	if ( isset( $query['view'] ) ) {
		$view	=	$query['view'];
	} else {
		return $segments;
	}
	
	// SEBLOD => Form
	if ( $view == 'form' ) {
		$segments[]	=	'form';
		if ( isset( $query['type'] ) ) {
			$segments[]	=	$query['type'];
			unset( $query['type'] );
		}
		
		unset( $query['view'] );
		unset( $query['layout'] );

		return $segments;
	}
	
	// SEBLOD => Content Objects
	if ( empty( $query['Itemid'] ) ) {
		$menuItem	=	$menu->getActive();
	} else {
		$menuItem	=	$menu->getItem( $query['Itemid'] );
	}
	$legacy			=	0;
	if ( $legacy || !isset( $menuItem->query['search'] ) ) {
		if ( isset( $query['catid'] ) ) {
			$segments[]	=	$query['catid'];
			unset( $query['catid'] );
		}
		if ( isset( $query['id'] ) ) {
			$segments[]	=	$query['id'];
			unset( $query['id'] );
		}
	} else {
		$params		=	JCckDevHelper::getRouteParams( $menuItem->query['search'] );
		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$params['location'].'/'.$params['location'].'.php';
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$params['location'], 'buildRoute', array( &$query, &$segments, $params ) );
	}
	
	unset( $query['view'] );
	
	return $segments;
}

// CckParseRoute
function CckParseRoute( $segments )
{
	$app		=	JFactory::getApplication();
	$count		=	count( $segments );
	$menu		=	$app->getMenu();
	$menuItem	=	$menu->getActive();
	$vars		=	array();

	if ( $segments[0] == 'form' ) {
		$menu->setActive( $app->input->getInt( 'Itemid', 0 ) );
		$vars['option']	=	'com_cck';
		$vars['view']	=	'form';
		$vars['layout']	=	'edit';
		$vars['type']	=	$segments[1];
	} else {
		$legacy	=	0;
		if ( !( $menuItem->query['option'] == 'com_cck' && $menuItem->query['view'] = 'list' ) ) {
			$legacy	=	0;
		}
		if ( !$legacy ) {
			$params	=	JCckDevHelper::getRouteParams( $menuItem->query['search'] );
			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$params['location'].'/'.$params['location'].'.php';
			JCck::callFunc_Array( 'plgCCK_Storage_Location'.$params['location'], 'parseRoute', array( &$vars, $segments, $count, $params ) );
		} else {
			if ( $count == 2 ) {
				$vars['option']		=	'com_content';
				$vars['view']		=	'article';
				$vars['catid']		=	$segments[0];
				$vars['id']			=	$segments[1];
			} elseif ( $count == 1 ) {
				$vars['option']		=	'com_content';
				
				@list( $id, $alias )=	explode( ':', $segments[0], 2 );
				$category			=	JCategories::getInstance('Content')->get( $id );
				if ( $category && $category->id == $id && $category->alias == $alias ) {
					$vars['view']	=	'categories';
				} else {
					$vars['view']	=	'article';
				}
				
				$vars['id']		=	$segments[0];
			}
		}
	}
	
	return $vars;
}
?>