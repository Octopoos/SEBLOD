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

// Plugin
class plgCCK_Field_LiveJoomla_User extends JCckPluginLive
{
	protected static $type	=	'joomla_user';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array(), $inherit = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );
		
		// Prepare
		$default	=	$options->get( 'default_value', '' );
		$excluded	=	$options->get( 'excluded' );
		$property	=	$options->get( 'property' );

		if ( $property ) {
			$user	=	JCck::getUser();
			if ( $user->id > 0 && $user->guest == 1 ) {
				if ( !( $property == 'ip' || $property == 'session_id' ) ) {
					$user	=	new JUser( 0 );
				}
			}
			if ( $property == 'access' ) {
				$viewlevels	=	$user->getAuthorisedViewLevels();
				if ( $excluded != '' ) {
					$excluded	=	explode( ',', $excluded );
					$viewlevels	=	array_diff( $viewlevels, $excluded );
				}
				if ( empty( $viewlevels ) ) {
					$live	=	$default;
				} else {
					$live	=	implode( ',', $viewlevels );	
				}
			} else {
				if ( strpos( $property, '[' ) !== false ) {
					$properties	= 	explode( '[', $property );
					$property 	=	$properties[0];
				}
				if ( isset( $user->$property ) ) {
					$live		=	$user->$property;

					if ( isset( $properties ) ) {
						$values	=	json_decode( $live, true );
						$target	=	substr( $properties[1], 0, -1 );

						if ( isset( $values[$target] ) ) {
							$live	=	$values[$target];
						} else {
							$live	=	'';
						}
					}
					if ( is_array( $live ) ) {
						if ( $excluded != '' ) {
							$excluded	=	explode( ',', $excluded );
							$live		=	array_diff( $live, $excluded );
						}
						if ( empty( $live ) ) {
							$live	=	$default;
						} else {
							$live	=	implode( ',', $live );	
						}
					} elseif ( $live == '' ) {
						$live	=	$default;
					}
				} else {
					$live	=	$default;
				}
			}
		}
		
		// Set
		$value	=	(string)$live;
	}
}
?>