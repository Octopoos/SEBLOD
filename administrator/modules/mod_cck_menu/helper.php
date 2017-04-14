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
class modCckMenuHelper
{
	// getItems
	public static function getItems( &$params )
	{
		$items			=	array();

		// Components
		for ( $i = 1; $i <= 5; $i++ ) {
			$cur	=	str_pad( $i, 2, '0' , STR_PAD_LEFT );
			if ( $params->get( 'enable'.$cur ) ) {
				$items['comp'.$cur]	=	$params->get( 'component'.$cur );
			}
		}

		// Custom
		for ( $i = 1; $i <= 10; $i++ ) {
			$cur	=	str_pad( $i, 2, '0' , STR_PAD_LEFT );
			if ( $params->get( 'free'.$cur ) ) {
				$items['free0'.$cur]	=	$params->get( 'free'.$cur.'_title' ).'||'.$params->get( 'free'.$cur.'_url' ).'||'.$params->get( 'free'.$cur.'_icon' );
			}
		}

		return $items;
	}

	// buildMenu
	public static function buildMenu( $mode, $menutitle, $moduleid, $com, $more = array() )
	{
		$app	=	JFactory::getApplication();
		$empty	=	false;
		$label	=	$menutitle;
		$menu	=	new JAdminCSSCCKMenu();
		$uix	=	JCck::getUIX();

		if ( $mode == 1 || $mode == 2 ) {
			if ( $uix == 'compact' ) {
				$menu->addChild( new JCCKMenuNode( $label, 'index.php?option=com_cck' ), true );
				if ( $mode == 2 && JFactory::getUser()->authorise( 'core.manage', 'com_cck' ) ) {
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_FORM_MANAGER' ), 'index.php?option=com_cck&view=types', 'class:type-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=types&task=type.add', 'class:newarticle' ) );
					}
					$menu->getParent();
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_APP_FOLDER_MANAGER' ), 'index.php?option=com_cck&view=folders', 'class:folder-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=folders&task=folder.add', 'class:newarticle' ) );
					}
					$menu->getParent();
				}
			} else {
				$menu->addChild( new JCCKMenuNode( $label, 'index.php?option=com_cck' ), true );
				if ( $mode == 2 && JFactory::getUser()->authorise( 'core.manage', 'com_cck' ) ) {
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_CONTENT_TYPE_MANAGER' ), 'index.php?option=com_cck&view=types', 'class:type-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=types&task=type.add', 'class:newarticle' ) );
					}
					$menu->getParent();
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_FIELD_MANAGER' ), 'index.php?option=com_cck&view=fields', 'class:field-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=fields&task=field.add', 'class:newarticle' ) );
					}
					$menu->getParent();
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_SEARCH_TYPE_MANAGER' ), 'index.php?option=com_cck&view=searchs', 'class:search-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=searchs&task=search.add', 'class:newarticle' ) );
					}
					$menu->getParent();
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_TEMPLATE_MANAGER' ), 'index.php?option=com_cck&view=templates', 'class:template-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=templates&task=template.add', 'class:newarticle' ) );
					}
					$menu->getParent();
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_APP_FOLDER_MANAGER' ), 'index.php?option=com_cck&view=folders', 'class:folder-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=folders&task=folder.add', 'class:newarticle' ) );
					}
					$menu->getParent();
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_SITE_MANAGER' ), 'index.php?option=com_cck&view=sites', 'class:site-manager' ), true );
					if ( $more['new'] == 1 ) {
						$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=folders&task=site.add', 'class:newarticle' ) );
					}
					$menu->getParent();
				}
			}
			$menu->addSeparator();
			$menu->addChild( new JCCKMenuNode( 'SEBLOD.com', 'https://www.seblod.com/', 'class:cck', false, '_blank' ), true );
			$menu->addChild( new JCCKMenuNode( 'Community', 'https://www.seblod.com/community', 'class:cck', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Blog', 'https://www.seblod.com/community/blog', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Events', 'https://www.seblod.com/community/events', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Forums', 'https://www.seblod.com/community/forums', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Showcase', 'https://www.seblod.com/community/showcase', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Social Hub', 'https://www.seblod.com/community/social-hub', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Testimonials', 'https://www.seblod.com/community/testimonials', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Users', 'https://www.seblod.com/community/users', '', false, '_blank' ) );
			$menu->addSeparator();
			$menu->addChild( new JCCKMenuNode( 'Store', 'https://www.seblod.com/store', 'class:cck', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Clubs', 'https://www.seblod.com/store/clubs', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Extensions', 'https://www.seblod.com/store/extensions', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Services', 'https://www.seblod.com/store/services', '', false, '_blank' ) );
			$menu->addSeparator();
			$menu->addChild( new JCCKMenuNode( 'Resources', 'https://www.seblod.com/resources', 'class:cck', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Books', 'https://www.seblod.com/resources/books', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Extensions', 'https://www.seblod.com/resources/extensions', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Manuals', 'https://www.seblod.com/resources/manuals', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Marketing', 'https://www.seblod.com/resources/marketing', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Tutorials', 'https://www.seblod.com/resources/tutorials', '', false, '_blank' ) );
			$menu->addChild( new JCCKMenuNode( '- Videos', 'https://www.seblod.com/resources/videos', '', false, '_blank' ) );
			$menu->addSeparator();
			$menu->getParent();
		} elseif ( $mode == 3 ) {
			$uix_ecommerce		=	JCckEcommerce::getUIX();
			$product_manager	=	JComponentHelper::getParams( 'com_cck_ecommerce' )->get( 'product_manager_link' );
			$menu->addChild( new JCCKMenuNode( $label, 'index.php?option=com_cck_ecommerce' ), true );
			$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_CART_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=carts', 'class:cart-manager' ), true );
			$menu->getParent();
			if ( $uix_ecommerce == 'full' ) {
				$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_ORDER_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=orders', 'class:order-manager' ), true );
				$menu->getParent();
				if ( $more['ecommerce'] ) {
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_PAYMENT_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=payments', 'class:payment-manager' ), true );
					$menu->getParent();
				}
				if ( $product_manager ) {
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_PRODUCT_MANAGER' ), $product_manager, 'class:product-manager' ), true );
					$menu->getParent();
				}
				if ( $more['ecommerce'] ) {
					$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_SHIPPING_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=shippings', 'class:shipping-manager' ), true );
					$menu->getParent();
				}
				$menu->addChild( new JCCKMenuNode( JText::_( 'MOD_CCK_MENU_STORE_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=stores', 'class:store-manager' ), true );
				$menu->getParent();
			}
		} elseif ( $mode == 4 ) {
			$items	=	JCckDatabase::loadObjectList( 'SELECT title as text, name as value, id FROM #__cck_core_types'
													. ' WHERE published = 1 AND location != "none" AND location != "site" AND storage_location != "none" ORDER BY text' );
			$menu->addChild( new JCCKMenuNode( $label ), true );
			if ( count( $items ) ) {
				$link	=	'index.php?option=com_cck&view=form&type=';
				$user	=	JFactory::getUser();
				foreach ( $items as $item ) {
					if ( $user->authorise( 'core.create', 'com_cck.form.'.$item->id ) ) {
						$menu->addChild( new JCCKMenuNode( JText::_( $item->text ), $link.$item->value, 'newarticle' ) );
					}
				}
			}
		} elseif ( $mode == 5 )	{
			$user	=	JFactory::getUser();
			$groups	= implode( ',', $user->getAuthorisedViewLevels() );
			$items	=	JCckDatabase::loadObjectList( 'SELECT title as text, name as value, id FROM #__cck_core_searchs'
													. ' WHERE published = 1 AND location !="none" AND location != "site" AND access IN ('.$groups.') ORDER BY text' );
			$menu->addChild( new JCCKMenuNode( $label ), true );
			if ( count( $items ) ) {
				$link	=	'index.php?option=com_cck&view=list&search=';
				$user	=	JFactory::getUser();
				foreach ( $items as $item ) {
					$menu->addChild( new JCCKMenuNode( JText::_( $item->text ), $link.$item->value, 'component' ) );
				}
			}
		} elseif ( $mode == 6 )	{
			$addons	=	JCckDatabase::loadObjectList( 'SELECT a.element, b.title FROM #__extensions AS a'
													. ' LEFT JOIN #__menu AS b on b.component_id = a.extension_id'
													. ' WHERE a.type = "component" AND a.element LIKE "com_cck\_%" ORDER BY title' );
			$menu->addChild( new JCCKMenuNode( $label ), true );
			if ( count( $addons ) )  {
				foreach ( $addons as $addon ) {
					$menu->addChild( new JCCKMenuNode( $addon->title, 'index.php?option='.$addon->element, 'component' ) );
				}
			}
		} else {
			$empty	=	true;
			if ( strpos( $label, 'icon-16-' ) !== false ) {
				$class	=	str_replace( 'icon-16-', '', $label ).'||root-icon-position';
				$label	=	' ';
			} else {
				$class	=	'';
			}
			$menu->addChild( new JCCKMenuNode( $label, '#', $class ), true );
		}
		if ( count( $com ) ) {
			if ( !$empty ) {
				$menu->addSeparator();
			}
			foreach ( $com AS $key => $item ) {
				$link			=	null;
				$link			=	explode( '||', $item );
				if ( strpos( $key, 'free' ) === false ) {
					$link[1]	=	$link[1];
					$link[2]	=	( $link[2] ) ? $link[2] : 'component';
				} else {
					$link[2]	=	( $link[2] ) ? str_replace( array( 'icon-16-', '.png' ), '', $link[2] ) : 'component';
				}
				$menu->addChild( new JCCKMenuNode( JText::_( $link[0] ), $link[1], $link[2] ) );
			}
		}
		$menu->getParent();

		// TK added menutitle to menuname
		$menu->renderMenu( 'cck_menu_jseblod'.$moduleid, '' );
	}

	// buildDisabledMenu
	public static function buildDisabledMenu( $mode, $menutitle, $moduleid )
	{
		$menu	=	new JAdminCSSCCKMenu();
		if ( strpos( $menutitle, 'icon-16-' ) !== false ) {
			$class		=	str_replace( 'icon-16-', '', $menutitle ).'||root-icon-position';
			$menutitle	=	' ';
		} else {
			$class	=	'';
		}
		$menu->addChild( new JCCKMenuNode( $menutitle, NULL, 'disabled' ) );

		// TK added menutitle and cck_menu class
		$menu->renderMenu( 'cck_menu_jseblod'.$moduleid, 'cck_menu disabled' );
	}
}
?>
