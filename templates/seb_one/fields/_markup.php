<?php
/**
* @version			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// The markup around each field (label+value/form) can be Overridden.
// Remove the underscore [_] from the Filename. (filename = markup.php)
// Edit the function name:
//	- fields/markup.php 			=>	cckMarkup_[template]
//	- fields/[contenttype]/markup.php	=>	cckMarkup_[template]_[contenttype]
//	- fields/[searchtype]/markup.php	=>	cckMarkup_[template]_[searchtype]
// Write your Custom Markup code. (see default markup below)

// cckMarkup
function cckMarkup_seb_one( $cck, $html, $field, $options )
{
	// Computation
	if ( isset( $field->computation ) && $field->computation ) {
		$cck->setComputationRules( $field );
	}
	// Conditional
	if ( isset( $field->conditional ) && $field->conditional ) {
		$cck->setConditionalStates( $field );
	}

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
