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

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

// Plugin
class JCckPluginShipping extends CMSPlugin
{
	protected static $construction	=	'cck_ecommerce_shipping';
	
	// __construct
	public function __construct( &$subject, $config = array() )
	{
		parent::__construct( $subject, $config );

		// Fix Language
		if ( Factory::getApplication()->isClient( 'administrator' ) ) {
			$lang			=	Factory::getLanguage();
			$lang_default	=	$lang->setDefault( 'en-GB' );

			$lang->load( 'plg_'.$this->_type.'_'.$this->_name, JPATH_ADMINISTRATOR );
			$lang->setDefault( $lang_default );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return Uri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
}
?>