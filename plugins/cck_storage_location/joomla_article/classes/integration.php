<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
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
		
		$class					=	( JCck::on( '3.4' ) ) ? ' class="hasTooltip"' : '';
		$data['doIntegration']	=	false;
		$data['multilanguage']	=	$data['options']->get( 'multilanguage', 0 );
		
		if ( $data['multilanguage'] ) {
			$data['search']		=	'#<a'.$class.' href="(.*)index.php\?option=com_content&amp;task=article.edit'.$tag.'&amp;id=([0-9]*)" (.*)>#U';
		} else {
			$data['search']		=	'#<a'.$class.' href="(.*)index.php\?option=com_content&amp;task=article.edit'.$tag.'&amp;id=([0-9]*)"#';
		}
		$data['search_alt']		=	'#<a href = "javascript://" onclick="listItemTask\(\'cb([0-9]*)\', \'articles.archive\'\)">(.*)</a>#sU';
		
		if ( JCckDevHelper::hasLanguageAssociations() && $data['multilanguage'] ) {
			$query		=	'SELECT a.pk, a.cck, b.key, c.language FROM #__cck_core AS a'
						.	' LEFT JOIN #__associations AS b ON ( b.id = a.pk AND context = "com_content.item" )'
						.	' LEFT JOIN #__content AS c ON c.id = a.pk'
						.	' WHERE storage_location="joomla_article"';
			$list_assoc	=	JCckDatabase::loadObjectListArray( 'SELECT a.id, a.key, b.language FROM #__associations AS a LEFT JOIN #__content AS b ON ( b.id = a.id AND a.context = "com_content.item" )', 'key', 'language' );
		} else {
			$query		=	'SELECT pk, cck FROM #__cck_core WHERE storage_location="joomla_article"';
			$list_assoc	=	array();
		}
		$list	=	JCckDatabase::loadObjectList( $query, 'pk' );
		$buffer	=	JCckDevIntegration::rewriteBuffer( $buffer, $data, $list, $list_assoc );
	}
}
?>