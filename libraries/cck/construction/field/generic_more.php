<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: generic_more.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldGeneric_More
{
	protected static $type_field				=	'label, variation, variation_override, required, required_alert, validation, validation_options, link, link_options, live, live_options, live_value, markup, markup_class, typo, typo_label, typo_options, stage, access, restriction, restriction_options, computation, computation_options, conditional, conditional_options, position';
	protected static $type_field_get			=	'c.label as label2, c.variation, c.variation_override, c.required, c.required_alert, c.validation, c.validation_options, c.link, c.link_options, c.live, c.live_options, c.live_value, c.markup, c.markup_class, c.typo, c.typo_label, c.typo_options, c.stage, c.access, c.restriction, c.restriction_options, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position';
	protected static $search_field				=	'label, variation, variation_override, required, required_alert, validation, validation_options, link, link_options, live, live_options, live_value, markup, markup_class, match_collection, match_mode, match_options, match_value, typo, typo_label, typo_options, stage, access, restriction, restriction_options, computation, computation_options, conditional, conditional_options, position';
	protected static $search_field_get			=	'c.label as label2, c.variation, c.variation_override, c.required, c.required_alert, c.validation, c.validation_options, c.link, c.link_options, c.live, c.live_options, c.live_value, c.markup, c.markup_class, c.match_collection, c.match_mode, c.match_options, c.match_value, c.typo, c.typo_label, c.typo_options, c.stage, c.access, c.restriction, c.restriction_options, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// gm_getConstruction_Columns
	public static function gm_getConstruction_Columns( $table, $suffix = '' )
	{
		return self::${$table.$suffix};
	}
	
	// gm_getConstruction_Values_Type
	public static function gm_getConstruction_Values_Type( $k, $params, $position, $client )
	{
		$db						=	JFactory::getDbo();
		
		$label					=	( @$params[$k]['label'] != '' && ( $params[$k]['label'] != $params[$k]['label2'] ) ) ? $db->escape( $params[$k]['label'] ) : '';
		$variation				=	( @$params[$k]['variation'] != '' ) ? $params[$k]['variation'] : '';
		$variation_override		=	( @$params[$k]['variation_override'] != '' ) ? $db->escape( $params[$k]['variation_override'] ) : '';
		$required				=	( @$params[$k]['required'] != '' ) ? $params[$k]['required'] : '';
		$required_alert			=	( @$params[$k]['required_alert'] != '' ) ? str_replace( '"', '', $params[$k]['required_alert'] ) : '';
		$validation				=	( @$params[$k]['validation'] != '' ) ? $params[$k]['validation'] : '';
		$validation_options		=	( @$params[$k]['validation_options'] != '' ) ? $db->escape( $params[$k]['validation_options'] ) : '';
		$link					=	( @$params[$k]['link'] != '' ) ? $params[$k]['link'] : '';
		$link_options			=	( @$params[$k]['link_options'] != '' ) ? $db->escape( $params[$k]['link_options'] ) : '';
		$live					=	( @$params[$k]['live'] != '' ) ? $params[$k]['live'] : '';
		$live_options			=	( @$params[$k]['live_options'] != '' ) ? $db->escape( $params[$k]['live_options'] ) : '';
		$live_value				=	( @$params[$k]['live_value'] != '' ) ? $db->escape( $params[$k]['live_value'] ) : '';
		$markup					=	( @$params[$k]['markup'] != '' ) ? $params[$k]['markup'] : '';
		$markup_class			=	( @$params[$k]['markup_class'] != '' ) ? ' '.trim( $db->escape( $params[$k]['markup_class'] ) ) : '';
		$typo					=	( @$params[$k]['typo'] != '' ) ? $params[$k]['typo'] : '';
		$typo_label				=	( @$params[$k]['typo_label'] ) ? $params[$k]['typo_label'] : 0;
		$typo_options			=	( @$params[$k]['typo_options'] != '' ) ? $db->escape( $params[$k]['typo_options'] ) : '';
		$stage					=	( @$params[$k]['stage'] ) ? $params[$k]['stage'] : 0;
		$access					=	( @$params[$k]['access'] != '' ) ? $params[$k]['access'] : 1;
		$restriction			=	( @$params[$k]['restriction'] != '' ) ? $params[$k]['restriction'] : '';
		$restriction_options	=	( @$params[$k]['restriction_options'] != '' ) ? $db->escape( $params[$k]['restriction_options'] ) : '';
		$computation			=	( @$params[$k]['computation'] != '' ) ? $params[$k]['computation'] : '';
		$computation_options	=	( @$params[$k]['computation_options'] != '' && $computation != '' ) ? $db->escape( $params[$k]['computation_options'] ) : '';
		$conditional			=	( @$params[$k]['conditional'] != '' ) ? $params[$k]['conditional'] : '';
		$conditional_options	=	( @$params[$k]['conditional_options'] != '' && $conditional != '' ) ? $db->escape( $params[$k]['conditional_options'] ) : '';
		
		$values					=	'"'.$label.'", "'.$variation.'", "'.$variation_override.'", "'.$required.'", "'.$required_alert.'", "'.$validation.'", "'.$validation_options.'", '
								.	'"'.$link.'", "'.$link_options.'", "'.$live.'", "'.$live_options.'", "'.$live_value.'", "'.$markup.'", "'.$markup_class.'", "'.$typo.'", "'.$typo_label.'", "'.$typo_options.'", '.$stage.', '
								.	'"'.$access.'", "'.$restriction.'", "'.$restriction_options.'", "'.$computation.'", "'.$computation_options.'", "'.$conditional.'", "'.$conditional_options.'", "'.$position.'"';
		
		return $values;
	}
	
	// gm_getConstruction_Values_Search
	public static function gm_getConstruction_Values_Search( $k, $params, $position )
	{
		$db						=	JFactory::getDbo();
		
		$label					=	( @$params[$k]['label'] != '' && ( $params[$k]['label'] != $params[$k]['label2'] ) ) ? $db->escape( $params[$k]['label'] ) : '';
		$variation				=	( @$params[$k]['variation'] != '' ) ? $params[$k]['variation'] : '';
		$variation_override		=	( @$params[$k]['variation_override'] != '' ) ? $db->escape( $params[$k]['variation_override'] ) : '';
		$required				=	( @$params[$k]['required'] != '' ) ? $params[$k]['required'] : '';
		$required_alert			=	( @$params[$k]['required_alert'] != '' ) ? str_replace( '"', '', $params[$k]['required_alert'] ) : '';
		$validation				=	( @$params[$k]['validation'] != '' ) ? $params[$k]['validation'] : '';
		$validation_options		=	( @$params[$k]['validation_options'] != '' ) ? $db->escape( $params[$k]['validation_options'] ) : '';
		$link					=	( @$params[$k]['link'] != '' ) ? $params[$k]['link'] : '';
		$link_options			=	( @$params[$k]['link_options'] != '' ) ? $db->escape( $params[$k]['link_options'] ) : '';
		$live					=	( @$params[$k]['live'] != '' ) ? $params[$k]['live'] : '';
		$live_options			=	( @$params[$k]['live_options'] != '' ) ? $db->escape( $params[$k]['live_options'] ) : '';
		$live_value				=	( @$params[$k]['live_value'] != '' ) ? $db->escape( $params[$k]['live_value'] ) : '';
		$markup					=	( @$params[$k]['markup'] != '' ) ? $params[$k]['markup'] : '';
		$markup_class			=	( @$params[$k]['markup_class'] != '' ) ? ' '.trim( $db->escape( $params[$k]['markup_class'] ) ) : '';
		$match_collection		=	( @$params[$k]['match_collection'] != '' ) ? $params[$k]['match_collection'] : '';
		$match_mode				=	( @$params[$k]['match_mode'] != '' ) ? $params[$k]['match_mode'] : '';
		$match_options			=	( @$params[$k]['match_options'] != '' ) ? $db->escape( $params[$k]['match_options'] ) : '';
		$match_value			=	( @$params[$k]['match_value'] != '' ) ? str_replace( '"', '', $params[$k]['match_value'] ) : '';
		$typo					=	( @$params[$k]['typo'] != '' ) ? $params[$k]['typo'] : '';
		$typo_label				=	( @$params[$k]['typo_label'] ) ? $params[$k]['typo_label'] : 0;
		$typo_options			=	( @$params[$k]['typo_options'] != '' ) ? $db->escape( $params[$k]['typo_options'] ) : '';
		$stage					=	( @$params[$k]['stage'] ) ? $params[$k]['stage'] : 0;
		$access					=	( @$params[$k]['access'] != '' ) ? $params[$k]['access'] : 1;
		$restriction			=	( @$params[$k]['restriction'] != '' ) ? $params[$k]['restriction'] : '';
		$restriction_options	=	( @$params[$k]['restriction_options'] != '' ) ? $db->escape( $params[$k]['restriction_options'] ) : '';
		$computation			=	( @$params[$k]['computation'] != '' ) ? $params[$k]['computation'] : '';
		$computation_options	=	( @$params[$k]['computation_options'] != '' && $computation != '' ) ? $db->escape( $params[$k]['computation_options'] ) : '';
		$conditional			=	( @$params[$k]['conditional'] != '' ) ? $params[$k]['conditional'] : '';
		$conditional_options	=	( @$params[$k]['conditional_options'] != '' && $conditional != '' ) ? $db->escape( $params[$k]['conditional_options'] ) : '';
		
		$values					=	'"'.$label.'", "'.$variation.'", "'.$variation_override.'", "'.$required.'", "'.$required_alert.'", "'.$validation.'", "'.$validation_options.'", '
								.	'"'.$link.'", "'.$link_options.'", "'.$live.'", "'.$live_options.'", "'.$live_value.'", "'.$markup.'", "'.$markup_class.'", "'.$match_collection.'", "'.$match_mode.'", "'.$match_options.'", ' 	
								.	'"'.$match_value.'", "'.$typo.'", "'.$typo_label.'", "'.$typo_options.'", '.$stage.', "'.$access.'", "'.$restriction.'", "'.$restriction_options.'", "'.$computation.'", '
								.	'"'.$computation_options.'", "'.$conditional.'", "'.$conditional_options.'", "'.$position.'"';
		
		return $values;
	}
}
?>