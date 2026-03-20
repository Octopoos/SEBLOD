<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: shipping.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginShipping extends JPlugin
{
	protected static $construction	=	'cck_ecommerce_shipping';
	
	// __construct
	public function __construct( &$subject, $config = array() )
	{
		parent::__construct( $subject, $config );

		// Fix Language
		if ( JFactory::getApplication()->isClient( 'administrator' ) ) {
			$lang			=	JFactory::getLanguage();
			$lang_default	=	$lang->setDefault( 'en-GB' );

			$lang->load( 'plg_'.$this->_type.'_'.$this->_name, JPATH_ADMINISTRATOR );
			$lang->setDefault( $lang_default );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
}
?>