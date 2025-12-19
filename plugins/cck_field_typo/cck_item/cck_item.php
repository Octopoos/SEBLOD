<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_TypoCck_Item extends JCckPluginTypo
{
	protected static $type	=	'cck_item';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{		
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$value	=	parent::g_hasLink( $field, $typo, $field->$target );
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		if ( !(int)$value ) {
			return '';
		}

		jimport( 'cck.base.item.item' );

		require_once JPATH_SITE.'/templates/seb_page_sections/includes/helper.php';

		if ( (int)$typo->get( 'translate', '1' ) ) {
			$value	=	CCK_Item::getAssociation( $value );
		}

		$typo_state	=	$typo->get( 'state', 1 );

		if ( $typo_state ) {
			return CCK_Item::render( $value, null, ( (int)$typo->get( 'caching', '1' ) ? true : false ) );
		} else {
			return '{cck_item:'.$value.'}';
		}
	}
}
?>