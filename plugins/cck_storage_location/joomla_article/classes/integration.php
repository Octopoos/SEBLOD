<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';

// Class
class plgCCK_Storage_LocationJoomla_Article_Integration extends plgCCK_Storage_LocationJoomla_Article
{
	// onCCK_Storage_LocationAfterDispatch
	public static function onCCK_Storage_LocationAfterDispatch( &$data, $uri = array() )
	{
		$return	=	'&return_o='.substr( $uri['option'], 4 );
		
		if ( !$uri['layout'] ) {
			$do	=	$data['options']->get( 'add', 1 );
			$data['options']->set( 'add_alt_link', 'index.php?option=com_content&view=article&layout=edit&cck=1' );
			if ( $do == 1 ) {
				JCckDevIntegration::addModalBox( $data['options']->get( 'add_layout', 'icon' ), $return, $data['options'] );
			} elseif ( $do == 2 ) {
				JCckDevIntegration::addDropdown( 'form', $return, $data['options'] );
			}
			JCckDevIntegration::addWarning( 'copy' );
		} elseif ( $uri['layout'] == 'edit' && !$uri['id'] ) {
			if ( $data['options']->get( 'add_redirect', 1 ) ) {
				JCckDevIntegration::redirect( $data['options']->get( 'default_type' ), $return );
			}
		} elseif ( $uri['layout'] == 'edit' && $uri['id'] ) {
			// $isCck	=	JCckDatabase::loadResult( 'SELECT cck FROM #__cck_core WHERE storage_location="'.self::$type.'" AND pk='.(int)$uri['id'] );
			// if ( $data['options']->get( 'edit_redirect', 0 ) || $isCck ) {
				// redirect
			// }
		}
	}
	
	// onCCK_Storage_LocationAfterRender
	public static function onCCK_Storage_LocationAfterRender( &$buffer, &$data, $uri = array() )
	{
		$app	=	JFactory::getApplication();

		if ( $uri['layout'] ) {
			return;
		}
		
		if ( $uri['view'] == 'featured' ) {
			$data['return_view']	=	'featured';
			$tag					=	'&amp;return=featured';
		} else {
			$data['return_view']	=	'';
			$tag					=	'';
		}
		
		$class					=	' class="hasTooltip"';
		$data['doIntegration']	=	false;
		$data['multilanguage']	=	$data['options']->get( 'multilanguage', 0 );
		
		if ( $data['multilanguage'] ) {
			$data['search']		=	'#<a'.$class.' href="(.*)index.php\?option=com_content&amp;task=article.edit'.$tag.'&amp;id=([0-9]*)" (.*)>#U';
		} else {
			$data['search']		=	'#<a'.$class.' href="(.*)index.php\?option=com_content&amp;task=article.edit'.$tag.'&amp;id=([0-9]*)"#';
		}
		$data['search_alt']		=	'#<a href = "javascript://" onclick="listItemTask\(\'cb([0-9]*)\', \'articles.archive\'\)">(.*)</a>#sU';
		
		$db = JFactory::getDbo();
		$pks	=	array();

		if($app->isAdmin()){
			$option	=	$app->input->get('option','');
			if($option == 'com_content'){
				$model		=	new ContentModelArticles();
				$items = $model->getItems();
				foreach($items as $i){
					$pks[] = $i->id;
				}

/*
				$addLimit	=	true;

				$limit		=	$model->getState('list.limit');
				$start		=	$model->getStart();

				// Add the list ordering clause.
		                $orderCol = $model->getState('list.ordering', 'a.id');
		                $orderDirn = $model->getState('list.direction', 'desc');
		
	        	        if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
	        	        {
					$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
	                	}

	                	// SQL server change
	                	if ($orderCol == 'language')
	                	{
	                	        $orderCol = 'l.title';
	                	}

	                	if ($orderCol == 'access_level')
	                	{
	                	        $orderCol = 'ag.title';
	                	}

				$order = $db->escape($orderCol . ' ' . $orderDirn);
*/
			}
		}


		if ( JCckDevHelper::hasLanguageAssociations() && $data['multilanguage'] ) {
			$query		=	'SELECT a.pk, a.cck, b.key, c.language FROM #__cck_core AS a'
						.	' LEFT JOIN #__associations AS b ON ( b.id = a.pk AND context = "com_content.item" )'
						.	' LEFT JOIN #__content AS c ON c.id = a.pk'
						.	' WHERE storage_location="joomla_article"';
			$query		.=	count($pks) ? ' AND a.pk IN( '.implode(',',$pks).')' : '';
			$query_assoc	=	'SELECT a.id, a.key, b.language FROM #__associations AS a LEFT JOIN #__content AS b ON ( b.id = a.id AND a.context = "com_content.item" )';
			$query_assoc	.=	count($pks) ? ' WHERE a.id IN ( '.implode(',',$pks).')':'';
/*
			$query_assoc	.=	$order ? ' ORDER BY '.$order : '';
			$query_assoc	.=	$addLimit ? ' LIMIT '.$start.','.$limit : '';
*/
			$list_assoc	=	JCckDatabase::loadObjectListArray( $query_assoc, 'key', 'language' );
		} else {
			$query		=	'SELECT pk, cck FROM #__cck_core WHERE storage_location="joomla_article"';
			$query		.=	count($pks) ? ' AND pk IN( '.implode(',',$pks).')' : '';
			$list_assoc	=	array();
		}
/*
		$query		.=	$addLimit ? ' ORDER BY '.$order : '';
		$query		.=	$addLimit ? ' LIMIT '.$start.','.$limit : '';
*/
		$list	=	count($pks) ? JCckDatabase::loadObjectList( $query, 'pk' ) : array();
		$buffer	=	JCckDevIntegration::rewriteBuffer( $buffer, $data, $list, $list_assoc );
	}
}
?>
