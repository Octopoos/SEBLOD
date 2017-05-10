<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_enabled.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Root
$empty	=	true;
if ( $root ) {
	$menu->addChild( new JMenuNode( $root, '#' ), true );
}

// Base
if ( $mode == 1 || $mode == 2 ) {
	$uix	=	JCck::getUIX();
	if ( $uix == 'compact' ) {
		if ( $mode == 2 && JFactory::getUser()->authorise( 'core.manage', 'com_cck' ) ) {
			$empty	=	 false;
			$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_APP_FOLDER_MANAGER' ), 'index.php?option=com_cck&view=folders' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=folders&task=folder.add' ) );
			}
			$menu->getParent();
			$menu->addChild( new JMenuNode( '-&nbsp;'.JText::_( 'MOD_CCK_MENU_FORM_MANAGER' ), 'index.php?option=com_cck&view=types' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=types&task=type.add' ) );
			}
			$menu->getParent();
		}
	} else {
		if ( $mode == 2 && JFactory::getUser()->authorise( 'core.manage', 'com_cck' ) ) {
			$empty	=	 false;
			$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_APP_FOLDER_MANAGER' ), 'index.php?option=com_cck&view=folders', 'cck' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=folders&task=folder.add' ) );
			}
			$menu->getParent();
			$menu->addChild( new JMenuNode( '-&nbsp;'.JText::_( 'MOD_CCK_MENU_CONTENT_TYPE_MANAGER' ), 'index.php?option=com_cck&view=types', 'cck' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=types&task=type.add' ) );
			}
			$menu->getParent();
			$menu->addChild( new JMenuNode( '-&nbsp;'.JText::_( 'MOD_CCK_MENU_FIELD_MANAGER' ), 'index.php?option=com_cck&view=fields', 'cck' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=fields&task=field.add' ) );
			}
			$menu->getParent();
			$menu->addChild( new JMenuNode( '-&nbsp;'.JText::_( 'MOD_CCK_MENU_SEARCH_TYPE_MANAGER' ), 'index.php?option=com_cck&view=searchs', 'cck' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=searchs&task=search.add' ) );
			}
			$menu->getParent();
			$menu->addChild( new JMenuNode( '-&nbsp;'.JText::_( 'MOD_CCK_MENU_TEMPLATE_MANAGER' ), 'index.php?option=com_cck&view=templates', 'cck' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=templates&task=template.add' ) );
			}
			$menu->getParent();
			$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_SITE_MANAGER' ), 'index.php?option=com_cck&view=sites', 'cck' ), true );
			if ( $options['new'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_NEW' ), 'index.php?option=com_cck&view=folders&task=site.add' ) );
			}
			$menu->getParent();
		}
	}
	if ( !$empty ) {
		$menu->addSeparator();
	}
	$empty	=	 false;
	$menu->addChild( new JMenuNode( 'SEBLOD.com', 'https://www.seblod.com/', 'cck', false, '_blank' ), true );
	$menu->addChild( new JMenuNode( 'About', 'https://www.seblod.com/about', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Changelog', 'https://www.seblod.com/changelogs', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Demo', 'https://demo.seblod.com', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'FAQs', 'https://www.seblod.com/faq', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Features', 'https://www.seblod.com/features', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'GitHub', 'https://github.com/Octopoos/SEBLOD', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Terminology', 'https://www.seblod.com/terminology', '', false, '_blank' ) );
	$menu->getParent();
	$menu->addChild( new JMenuNode( '- ' . JText::_( 'MOD_CCK_MENU_SEBLOD_COM_COMMUNITY' ), 'https://www.seblod.com/community', 'cck', false, '_blank' ), true );
	$menu->addChild( new JMenuNode( 'Blog', 'https://www.seblod.com/community/blog', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Events', 'https://www.seblod.com/community/events', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Forums', 'https://www.seblod.com/community/forums', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Showcase', 'https://www.seblod.com/community/showcase', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Social Hub', 'https://www.seblod.com/community/social-hub', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Testimonials', 'https://www.seblod.com/community/testimonials', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Users', 'https://www.seblod.com/community/users', '', false, '_blank' ) );
	$menu->getParent();
	$menu->addChild( new JMenuNode( '- ' . JText::_( 'MOD_CCK_MENU_SEBLOD_COM_RESOURCES' ), 'https://www.seblod.com/resources', 'cck', false, '_blank' ), true );
	$menu->addChild( new JMenuNode( 'Books', 'https://www.seblod.com/resources/books', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Extensions', 'https://www.seblod.com/resources/extensions', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Manuals', 'https://www.seblod.com/resources/manuals', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Marketing', 'https://www.seblod.com/resources/marketing', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Tutorials', 'https://www.seblod.com/resources/tutorials', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Videos', 'https://www.seblod.com/resources/videos', '', false, '_blank' ) );
	$menu->getParent();
	$menu->addChild( new JMenuNode( '- ' . JText::_( 'MOD_CCK_MENU_SEBLOD_COM_PRODUCTS' ), 'https://www.seblod.com/store', 'cck', false, '_blank' ), true );
	$menu->addChild( new JMenuNode( 'Clubs', 'https://www.seblod.com/store/clubs', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Extensions', 'https://www.seblod.com/store/extensions', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( '- Add-ons', 'https://www.seblod.com/store/extensions?seb_item_category=16', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( '- Applications', 'https://www.seblod.com/store/extensions/applications', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( '- Plug-ins', 'https://www.seblod.com/store/extensions?seb_item_category=19,20,21,22,23,24,25,112', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( '- Templates', 'https://www.seblod.com/store/extensions?seb_item_category=27', '', false, '_blank' ) );
	$menu->addChild( new JMenuNode( 'Services', 'https://www.seblod.com/store/services', '', false, '_blank' ) );
	$menu->getParent();
} elseif ( $mode == 3 ) {
	if ( $user->authorise( 'core.manage', 'com_cck_ecommerce' ) ) {
		$empty				=	 false;
		$uix_ecommerce		=	JCckEcommerce::getUIX();
		$product_manager	=	JComponentHelper::getParams( 'com_cck_ecommerce' )->get( 'product_manager_link' );
		$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_CART_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=carts' ) );
		if ( $uix_ecommerce == 'full' ) {
			$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_ORDER_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=orders' ) );
			if ( $options['ecommerce'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_PAYMENT_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=payments' ) );
			}
			if ( $product_manager ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_PRODUCT_MANAGER' ), $product_manager ) );
			}
			if ( $options['ecommerce'] ) {
				$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_SHIPPING_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=shippings' ) );
			}
			$menu->addChild( new JMenuNode( JText::_( 'MOD_CCK_MENU_STORE_MANAGER' ), 'index.php?option=com_cck_ecommerce&view=stores' ) );
		}
	}
} elseif ( $mode == 4 ) {
	$items	=	JCckDatabase::loadObjectList( 'SELECT a.id, a.name, a.title as text FROM #__cck_core_types AS a'
											. ' WHERE a.published = 1 AND a.location != "none" AND a.location != "site" AND a.storage_location != "none" ORDER BY text' );
	$link	=	'index.php?option=com_cck&view=form&type=';
	if ( count( $items ) ) {
		foreach ( $items as $item ) {
			if ( $user->authorise( 'core.create', 'com_cck.form.'.$item->id ) ) {
				$empty	=	 false;
				$menu->addChild( new JMenuNode( JText::_( $item->text ), $link.$item->name ) );
			}
		}
	}
} elseif ( $mode == 5 ) {
	$groups	=	implode( ',', $user->getAuthorisedViewLevels() );
	$items	=	JCckDatabase::loadObjectList( 'SELECT a.id, a.name, a.title as text FROM #__cck_core_searchs AS a'
											. ' WHERE a.published = 1 AND a.location != "none" AND a.location != "site" AND a.access IN ('.$groups.') ORDER BY text' );
	$link	=	'index.php?option=com_cck&view=list&search=';
	if ( count( $items ) ) {
		foreach ( $items as $item ) {
			$empty	=	 false;
			$menu->addChild( new JMenuNode( JText::_( $item->text ), $link.$item->name ) );
		}
	}
	if ( $options['inline'] ) {
		$root	=	false;
	}
} elseif ( $mode == 6 ) {
	$items	=	JCckDatabase::loadObjectList( 'SELECT a.element, b.title as text FROM #__extensions AS a'
											. ' LEFT JOIN #__menu AS b on b.component_id = a.extension_id'
											. ' WHERE a.type = "component" AND a.element LIKE "com_cck\_%" ORDER BY title' );
	$link	=	'index.php?option=';
	if ( count( $items ) ) {
		foreach ($items as $item ) {
			if ( $user->authorise( 'core.manage', $item->element ) ) {
				$empty	=	 false;
				$menu->addChild( new JMenuNode( $item->text, $link.$item->element ) );
			}
		}
	}
}

// Custom
$items	=	modCckMenuHelper::getItems( $params );
if ( count( $items ) ) {
	if ( !$empty ) {
		$menu->addSeparator();
	}
	$empty	=	false;
	foreach ( $items as $key=>$item ) {
		$link	=	explode( '||', $item );
		$target	=	'';
		$text	=	( strpos( $link[0], 'icon-' ) !== false ) ? '<span class="'.$link[0].'"></span>' : JText::_( $link[0] );
		if ( $link[1] == 'root' ) {
			$link[1]	=	JUri::root();
			$target		=	'_blank';
		}
		$menu->addChild( new JMenuNode( $text, $link[1], '', false, $target ) );
	}
}
if ( !$empty && $root ) {
	$menu->getParent();
}
?>