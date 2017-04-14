<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: markup.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// cckMarkup
function cckMarkup( $cck, $html, $field, $options )
{
	$desc	=	'';
	if ( $cck->getStyleParam( 'field_description', 0 ) ) {
		$desc	=	( $field->description != '' ) ? '<div id="'.$cck->id.'_desc_'.$field->name.'" class="cck_desc cck_desc_'.$field->type.'">'.$field->description.'</div>' : '';
	}
	
	$label	=	'';
	if ( $options->get( 'field_label', $cck->getStyleParam( 'field_label', 1 ) ) ) {
		$label	=	$cck->getLabel( $field->name, true, ( $field->required ? '*' : '' ) );
		$label	=	( $label != '' ) ? '<div id="'.$cck->id.'_label_'.$field->name.'" class="cck_label cck_label_'.$field->type.'">'.$label.'</div>' : '';
	}
	
	$html	=	'<div id="'.$cck->id.'_'.$cck->mode_property.'_'.$field->name.'" class="cck_'.$cck->mode_property.' cck_'.$cck->mode_property.'_'.$field->type.$field->markup_class.'">'.$html.'</div>';
	$html	=	'<div id="'.$cck->id.'_'.$field->name.'" class="cck_'.$cck->mode.'s cck_'.$cck->client.' cck_'.$field->type.' cck_'.$field->name.'">'.$label.$html.$desc.'</div>';
	
	return $html;
}
?>