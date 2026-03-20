<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: webservice.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginWebservice extends JPlugin
{
	protected static $construction	=	'cck_webservice';
	
	protected $autoloadLanguage		=	true;

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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// g_onCCK_FieldConstruct
	public function g_onCCK_WebserviceConstruct( &$data )
	{
		// JSON
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}
		// STRING
		if ( isset( $data['string'] ) && is_array( $data['string'] ) ) {
			foreach ( $data['string'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$string	=	'';
					foreach ( $v as $s ) {
						if ( $s ) {
							$string	.=	$s.'||';
						}
					}
					if ( $string ) {
						$string	=	substr( $string, 0, -2 );
					}
					$data[$k]	=	$string;
				}
			}
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