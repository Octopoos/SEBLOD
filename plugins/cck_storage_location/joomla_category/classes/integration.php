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

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_category/joomla_category.php';

// Class
class plgCCK_Storage_LocationJoomla_Category_Integration extends plgCCK_Storage_LocationJoomla_Category
{
	// onCCK_Storage_LocationAfterDispatch
	public static function onCCK_Storage_LocationAfterDispatch( &$data, $uri = array() )
	{
		$app		=	JFactory::getApplication();
		$ext		=	$app->input->get( 'extension', '' );
		$exclude	=	$data['options']->get( 'exclude', '' );
		$extensions	=	explode( ',', str_replace( ' ', '', $exclude ) );
		$return		=	'&extension='.$ext.'&return_o='.substr( $uri['option'], 4 );
		
		if ( !in_array( $ext, $extensions ) ) {
			if ( !$uri['layout'] ) {
				$do	=	$data['options']->get( 'add', 1 );
				$data['options']->set( 'add_alt_link', 'index.php?option=com_categories&view=category&layout=edit&extension='.$ext.'&cck=1' );
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
			}
		}
	}
	
	// onCCK_Storage_LocationAfterRender
	public static function onCCK_Storage_LocationAfterRender( &$buffer, &$data, $uri = array() )
	{
		$app		=	JFactory::getApplication();
		$class		=	( JCck::on( '3.5' ) ) ? ' class="hasTooltip"' : '';
		$ext		=	$app->input->get( 'extension', '' );
		$exclude	=	$data['options']->get( 'exclude', '' );
		$extensions	=	explode( ',', str_replace( ' ', '', $exclude ) );
		
		if ( $uri['layout'] || !$ext || in_array( $ext, $extensions ) ) {
			return;
		}

		$data['doIntegration']	=	true;
		$data['replace_end']	=	'&amp;extension='.$ext.'"';
		$data['return_view']	=	'categories';
		$data['search']			=	'#<a'.$class.' href="(.*)index.php\?option=com_categories&amp;task=category.edit&amp;id=([0-9]*)&amp;extension='.$ext.'"#';
	}
}
?>