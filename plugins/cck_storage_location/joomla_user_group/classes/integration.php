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

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_user_group/joomla_user_group.php';

// Class
class plgCCK_Storage_LocationJoomla_User_Group_Integration extends plgCCK_Storage_LocationJoomla_User_Group
{
	// onCCK_Storage_LocationAfterDispatch
	public static function onCCK_Storage_LocationAfterDispatch( &$data, $uri = array() )
	{
		$return	=	'&return_o='.substr( $uri['option'], 4 ).'&return_v='.$uri['view'];
		
		if ( !$uri['layout'] ) {
			if ( $uri['view'] != 'groups' ) {
				return;
			}
			$do	=	$data['options']->get( 'add', 1 );
			$data['options']->set( 'add_alt_link', 'index.php?option=com_users&view=group&layout=edit&cck=1' );
			if ( $do == 1 ) {
				JCckDevIntegration::addModalBox( $data['options']->get( 'add_layout', 'icon' ), $return, $data['options'] );
			} elseif ( $do == 2 ) {
				JCckDevIntegration::addDropdown( 'form', $return, $data['options'] );
			}
		} elseif ( $uri['layout'] == 'edit' && !$uri['id'] ) {
			if ( $uri['view'] != 'group' ) {
				return;
			}
			if ( $data['options']->get( 'add_redirect', 1 ) ) {
				JCckDevIntegration::redirect( $data['options']->get( 'default_type' ), $return.'s' );
			}
		}
	}
	
	// onCCK_Storage_LocationAfterRender
	public static function onCCK_Storage_LocationAfterRender( &$buffer, &$data, $uri = array() )
	{
		if ( $uri['layout'] ) {
			return;
		}
		
		$data['doIntegration']	=	true;
		$data['return_view']	=	'groups';
		$data['search']			=	'#<a href="(.*)index.php\?option=com_users&amp;task=group.edit&amp;id=([0-9]*)"#';
	}
}
?>