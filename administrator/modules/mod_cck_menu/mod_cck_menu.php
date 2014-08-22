<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: mod_cck_menu.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$roots	=	array( 0=>'',
				   1=>JText::_( 'MOD_CCK_MENU_CONSTRUCTION' ),
				   2=>JText::_( 'MOD_CCK_MENU_CONSTRUCTION' ),
				   3=>JText::_( 'MOD_CCK_MENU_ECOMMERCE' ),
				   4=>JText::_( 'MOD_CCK_MENU_FORMS' ),
				   5=>JText::_( 'MOD_CCK_MENU_LISTS' ),
				   6=>JText::_( 'MOD_CCK_MENU_ADDONS' )
			);

if ( JCck::on() ) {
	
	// Joomla 3.0
	if ( !class_exists( 'modCckMenuHelper' ) ) {
		require __DIR__ . '/helper.php';
	}
	if ( !class_exists( 'JAdminCssMenu' ) ) {
		require JPATH_ADMINISTRATOR . '/modules/mod_menu/menu.php';
	}
	
	$app		=	JFactory::getApplication();
	$doc		=	JFactory::getDocument();
	$lang		=	JFactory::getLanguage();
	$menu		=	new JAdminCSSMenu;
	$user		=	JFactory::getUser();
	
	$alignment	=	$params->get( 'alignment', '' );
	$enabled	=	$app->input->getBool( 'hidemainmenu' ) ? false : true;
	$mode		=	$params->get( 'mode', 2 );
	$options	=	array( 'new'=>$params->get( 'cck_new', 0 ),
						   'ecommerce'=>$params->get( 'cck_ecommerce', 1 ),
						   'inline'=>$params->get( 'cck_inline', 0 ) );
	$root		=	trim( $params->get( 'menutitle' ) );
	if ( !$root ) {
		$root	=	$roots[$mode];
	}

	require JModuleHelper::getLayoutPath( 'mod_cck_menu', $params->get( 'layout', 'default' ) );

} else {

	// Joomla 2.5
	require_once dirname(__FILE__).'/helper.php';
	require_once dirname( __FILE__ ).'/cck_menu.php';

	$app		=	JFactory::getApplication();
	$hide		=	$app->input->getBool( 'hidemainmenu' );
	$document 	=	JFactory::getDocument();
	$root		=	JURI::root( true );
	$document->addStyleSheet( $root.'/administrator/modules/mod_cck_menu/assets/css/cck_menu.css' );

	$moduleid	=	$module->id;
	$mode		=	$params->get( 'mode', 2 );
	$title		=	trim( $params->get( 'menutitle' ) );
	if ( !$title ) {
		$title	=	$roots[$mode];
		if ( $mode == 1 || $mode == 2 ) {
			$title	.=	'&nbsp;<img src="modules/mod_cck_menu/assets/images/icon-12-star.png" border="0" alt=" " width="12" height="12"/>';
		}
	}

	if ( $hide ) {
		modCCKMenuHelper::buildDisabledMenu( $mode, $title, $moduleid );
	} else {
		$document->addScript( $root.'/administrator/modules/mod_cck_menu/assets/js/cck_menu.js' );
		$document->addScript( $root.'/administrator/modules/mod_cck_menu/assets/js/cck_index.js' );

		$items		=	modCckMenuHelper::getItems( $params );
		$options	=	array( 'new'=>$params->get( 'cck_new', '1' ), 'ecommerce'=>$params->get( 'cck_ecommerce', '1' ) );

		modCCKMenuHelper::buildMenu( $mode, $title, $moduleid, $items, $options );
	}

	// TK added - call the init-js with the correct menuname
	if ( !$hide ) { ?>
	<script type="text/javascript" charset="utf-8">
		var jseblodmenuid = '<?php echo $moduleid; ?>';
		cckjs_menu_init('cck_menu_jseblod'+jseblodmenuid);
	</script>
	<?php } ?>

<?php } ?>