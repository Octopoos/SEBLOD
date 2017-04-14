<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
abstract class modCCKQuickIconHelper
{
	protected static $buttons	=	array();
	
	// button
	public static function button( $button )
	{
		if ( !empty( $button['access'] ) ) {
			if ( is_bool( $button['access'] ) && $button['access'] == false ) {
				return '';
			}
			
			// Take each pair of permission, context values.
			for ( $i = 0, $n = count( $button['access'] ); $i < $n; $i += 2 ) {
				if ( !JFactory::getUser()->authorise( $button['access'][$i], $button['access'][$i+1] ) ) {
					return '';
				}
			}
			
			$button['target']	=	( strpos( $button['link'], 'http://' ) !== false ) ? '_blank' : '_self';
		}
		
		ob_start();
		require JModuleHelper::getLayoutPath( 'mod_cck_quickicon', 'default_button' );
		$html	=	ob_get_clean();
		
		return $html;
	}

	// getButtons
	public static function &getButtons()
	{
		$uix	=	JCck::getUIX();
		
		if ( empty( self::$buttons ) ) {
			if ( $uix == 'compact' ) {
				self::$buttons	=	array(
										array(
											'link'	=>	JRoute::_( 'index.php?option=com_cck&view=types' ),
											'icon'	=>	'icon-48-types.png',
											'image'	=>	'cck-form',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_FORM_MANAGER' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_FORM_MANAGER' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_CONSTRUCTION'
										),
										array(
											'link'	=>	JRoute::_( 'index.php?option=com_cck&view=folders' ),
											'icon'	=>	'icon-48-folders.png',
											'image'	=>	'cck-application',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_APP_FOLDER_MANAGER' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_APP_FOLDER_MANAGER' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_CONSTRUCTION'
										),
										array(
											'link'	=>	JRoute::_( 'https://www.seblod.com/products' ),
											'target'=>	'_blank',
											'icon'	=>	'icon-48-seblod.png',
											'image'	=>	'cck-products',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_SEBLOD_MARKET' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_SEBLOD_MARKET_EXTEND' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_SEBLOD_MARKET'
										)
									);
			} else {
				self::$buttons	=	array(
										array(
											'link'	=>	JRoute::_( 'index.php?option=com_cck&view=types' ),
											'icon'	=>	'icon-48-types.png',
											'image'	=>	'cck-form',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_CONTENT_TYPE_MANAGER' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_CONTENT_TYPE_MANAGER' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_CONSTRUCTION'
											),
										array(
											'link'	=>	JRoute::_( 'index.php?option=com_cck&view=fields' ),
											'icon'	=>	'icon-48-fields.png',
											'image'	=>	'cck-plugin',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_FIELD_MANAGER' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_FIELD_MANAGER' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_CONSTRUCTION'
										),
										array(
											'link'	=>	JRoute::_( 'index.php?option=com_cck&view=searchs' ),
											'icon'	=>	'icon-48-searchs.png',
											'image'	=>	'cck-search',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_SEARCH_TYPE_MANAGER' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_SEARCH_TYPE_MANAGER' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_CONSTRUCTION'
										),
										array(
											'link'	=>	JRoute::_( 'index.php?option=com_cck&view=templates' ),
											'icon'	=>	'icon-48-templates.png',
											'image'	=>	'cck-template',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_TEMPLATE_MANAGER' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_TEMPLATE_MANAGER' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_CONSTRUCTION'
										)
									);
				self::$buttons[]	=	array(
											'link'	=>	JRoute::_( 'https://www.seblod.com/products' ),
											'target'=>	'_blank',
											'icon'	=>	'icon-48-seblod.png',
											'image'	=>	'cck-products',
											'label' =>	JText::_( 'MOD_CCK_QUICKICON_SEBLOD_MARKET' ),
											'text' 	=>	str_replace( '<br />', ' ', JText::_( 'MOD_CCK_QUICKICON_SEBLOD_MARKET_EXTEND' ) ),
											'access'=>	array( 'core.manage', 'com_cck' ),
											'group'	=>	'MOD_CCK_QUICKICON_SEBLOD_MARKET'
										);
			}
		}
		
		return self::$buttons;
	}

	// groupButtons
	public static function groupButtons( $buttons )
	{
		$groupedButtons	=	array();

		foreach ( $buttons as $button ) {
			$groupedButtons[$button['group']][]	=	$button;
		}

		return $groupedButtons;
	}
}
?>