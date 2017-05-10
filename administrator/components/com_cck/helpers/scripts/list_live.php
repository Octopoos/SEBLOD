<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list_live.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// getOptions
function getOptions( $string )
{
	$options	=	array();

	if ( $string ) {
		$string	=	explode( '||', $string );
		
		foreach ( $string as $str ) {
			if ( $str != '' ) {
				$s				=	explode( '=', $str );
				$options[$s[0]]	=	$s[1];
			}
		}
	}
	
	return $options;
}

// Init
$app	=	JFactory::getApplication();
$elem	=	$app->input->getString( 'elem', '' );
$name	=	$app->input->getString( 'e_name', '' );
$form	=	'text';
$html	=	'';
if ( ! $elem ) {
	return;
}

$item			=	new stdClass;
$item->client	=	( $elem == 'type' ) ? 'site' : 'search';
$item->id		=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_'.$elem.'s WHERE name = "'.(string)$name.'"' );
if ( ! $item->id ) {
	return;
}

$live			=	$app->input->getString( 'live', '' );
$variat			=	$app->input->getString( 'variat', '' );
$values			=	getOptions( $live );
$variations		=	getOptions( $variat );

// Variations
$search			=	'<span class="icon-key"></span>';
$replace		=	' (*)';
$opts			=	array(
						JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_INHERITED' ).' -' ),
						JHtml::_( 'select.option', 'clear', JText::_( 'COM_CCK_CLEAR' ) ),
						JHtml::_( 'select.option', 'hidden', str_replace( $search, $replace, JText::_( 'COM_CCK_HIDDEN_AND_SECURED' ) ) ),
						JHtml::_( 'select.option', 'hidden_anonymous', str_replace( $search, $replace, JText::_( 'COM_CCK_HIDDEN_ANONYMOUS_AND_SECURED' ) ) ),
						JHtml::_( 'select.option', 'value', str_replace( $search, $replace, JText::_( 'COM_CCK_VALUE_AND_SECURED' ) ) ),
						JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
						JHtml::_( 'select.option', 'form', JText::_( 'COM_CCK_DEFAULT' ) ),
						JHtml::_( 'select.option', 'form_filter', JText::_( 'COM_CCK_FORM_FILTER' ) ),
						JHtml::_( 'select.option', 'disabled', str_replace( $search, $replace, JText::_( 'COM_CCK_FORM_DISABLED_AND_SECURED' ) ) ),
						JHtml::_( 'select.option', '</OPTGROUP>', '' ),
						JHtml::_( 'select.option', '<OPTGROUP>', str_replace( $search, $replace, JText::_( 'COM_CCK_STAR_IS_SECURED' ) ) ),
						JHtml::_( 'select.option', '</OPTGROUP>', '' )
					);

// Process
require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_workshop.php';
$fields		=	Helper_Workshop::getFields( $elem, $item, 'AND (( c.live = "" AND c.live_value = "" ) OR ( c.live = "" AND c.live_value != "" AND c.variation != "hidden" ) OR ( c.live != "" AND c.live != "stage" ))', false, true );

if ( count( $fields ) ) {
	foreach ( $fields as $pos ) {
		foreach ( $pos as $field ) {
			$value		=	( isset( $values[$field->name] ) ) ? htmlspecialchars( $values[$field->name] ) : '';
			$variat		=	( isset( $variations[$field->name] ) ) ? $variations[$field->name] : '';
			$variat		=	( $variat == 'none' ) ? $variat = 'hidden' : $variat;	// TODO: FIX TO REMOVE AFTER GA
			$variation	=	Jhtml::_( 'select.genericlist', $opts, 'variation_'.$field->name, 'class="inputbox variation_values" onchange="CCK_setOptions(\'variation\');"', 'value', 'text', $variat );
			$html		.=	'<label style="padding-left: 10px;">'.$field->title.'</label>'
						.	'<input class="live_values inputbox" type="text" id="live_'.$field->name.'" name="live_'.$field->name.'" value="'.$value.'" onchange="CCK_setOptions(\'live\');" />'
						.	$variation
						.	'<div class="clear"></div>';
		}
	}
}

echo $html;
?>

<script type="text/javascript">
function CCK_setOptions(type) {
	var elem = jQuery("#jform_params_"+type);
	var len = type.length + 1;
	var res = "";
	jQuery("."+type+"_values").each(function (i) {
		v = jQuery(this).val();
		v = encodeURIComponent(v);
		v = v.replace("+", "%2B");
		if (v != "") {
			n = jQuery(this).attr("name");
			n = n.substring( len );
			res += n+"="+v+"||";
		}
	});
	if (res){ res = res.substring( 0, res.length - 2 ); }
	elem.val( res );
}
</script>