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

use Joomla\Utilities\ArrayHelper;

// Plugin
class plgCCK_Field_LiveUrl_Variable extends JCckPluginLive
{
	protected static $type	=	'url_variable';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array(), $inherit = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$app		=	JFactory::getApplication();
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );
		
		// Prepare
		$crypt			=	$options->get( 'crypt', '' );
		$default		=	$options->get( 'default_value', '' );
		$ignore_null	=	$options->get( 'ignore_null', 0 );
		$multiple		=	$options->get( 'multiple', 0 );
		$return			=	$options->get( 'return', 'first' );
		if ( $multiple ) {
			$variables	=	$options->get( 'variables', '' );
			$variables	=	explode( '||', $variables );
			if ( count( $variables ) ) {
				foreach ( $variables as $variable ) {
					$request	=	'get'.ucfirst( $options->get( 'type', 'string' ) );
					$variable	=	preg_replace( '/\s+/', '', $variable );
					$result		=	(string)$app->input->$request( $variable, '' );
					
					if ( $ignore_null ) {
						$live	=	( $result ) ? $result : $live;
						if ( $return == 'first' && $live ) {
							break;
						}
					} else {
						$live	=	( $result != '' ) ? $result : $live;
						if ( $return == 'first' && $live != ''  ) {
							break;
						}
					}
				}
			}
		} else {
			$variable		=	$options->get( 'variable', $field->name );
			if ( $variable ) {
				$filter		=	$options->get( 'type', 'string' );
				if ( $filter == 'array' ) {
					$live		=	$app->input->get( $variable, $default, 'array' );
				} elseif ( $filter == 'integers' ) {
					$live		=	(string)$app->input->getString( $variable, $default );
					$live		=	explode( ',', $live );
					$live		=	ArrayHelper::toInteger( $live );
					$live		=	implode( ',', $live );
				} else {
					$request	=	'get'.ucfirst( $filter );
					$live		=	(string)$app->input->$request( $variable, $default );
					if ( $crypt == 'base64' ) {
						$live	=	base64_decode( $live );
					}
				}
			}
		}
		
		// Set
		$value	=	( $ignore_null && !$live ) ? '' : ( is_array( $live ) ? $live : (string)$live );
	}
}
?>