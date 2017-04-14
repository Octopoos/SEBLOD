<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: preview.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Prepare
$field						=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE name = "'.$this->item->name.'"' );
$field->required_alert		=	'';
$field->selectlabel			=	( trim( $field->selectlabel ) ) ? $field->selectlabel : '';
$field->variation			=	'';
$field->variation_override	=	'';
$field->restriction			=	'';
if ( $field->type == 'checkbox' || $field->type == 'radio' ) {
	$field->bool			=	1;
}

// Set
$doc	=	JFactory::getDocument();
$doc->addStyleSheet( JUri::root( true ).'/media/cck/css/cck.admin.css' );
$doc->addStyleDeclaration( 'div.cck_forms.cck_admin div.cck_form {float:none;}' );
$doc->addScriptDeclaration( 'jQuery(document).ready(function($){ $("#submitBox,#resetBox").hide(); });' );
Helper_Include::addDependencies( 'box', 'edit' );
?>

<div class="seblod preview">
	<div align="center" style="text-align:center;">
		<div class="cck_forms cck_admin cck_<?php echo $field->type; ?>">
			<div class="cck_form cck_form_<?php echo $field->type; ?>">
				<?php echo JCckDevField::getForm( $field, '', $config ); ?>
			</div>
		</div>
	</div>
</div>