<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: language.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$mixin	=	new class() {
	// _getActiveRoute
	protected function _getActiveRoute()
	{
		return function( $lang_code = '' ) {
			$app			=	Factory::getApplication();
			$lang_tag		=	Factory::getLanguage()->getTag();
			$menu_item		=	$app->getMenu()->getActive();
			$route			=	'';
			$route_params	=	JCckDevHelper::getRouteParams( $menu_item->query['search'] );

			// Prepare
			$assoc_id					=	$this->_getMenuAssociation( $menu_item->id, $lang_code );
			$config						=	array(
												'sef_aliases'=>$route_params['sef_aliases']
											);
			$route_params['location']	=	$this->getObject();

			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$route_params['location'].'/'.$route_params['location'].'.php';

			if ( $lang_tag == $lang_code ) {
				$route	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$route_params['location'], 'getRoute', array( $this->getPk(), $route_params['doSEF'], $menu_item->id, $config, $lang_code ) );
			} elseif ( $assoc_id
					&& $this->getProperty( 'language' ) == '*'
					&& $this->getProperty( 'titles.'.$lang_code ) ) {
				$route	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$route_params['location'], 'getRoute', array( $this->getPk(), $route_params['doSEF'], $assoc_id, $config, $lang_code ) );
			}

			return $route;
		};
	}

	// _getMenuAssociation
	protected function _getMenuAssociation()
	{
		return function( $item_id, $lang_code ) {
			\JLoader::register( 'MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php' );

			$associations	=	MenusHelper::getAssociations( $item_id );

			return isset( $associations[$lang_code] ) && $associations[$lang_code] ? $associations[$lang_code] : 0;
		};
	}
}
?>