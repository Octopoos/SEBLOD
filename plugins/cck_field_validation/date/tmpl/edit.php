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

// JS
$js =	'jQuery(document).ready(function($) {
			$("#alert").parent().find("label").append("<span class=\"star\"> </star>");
			if ($("#separator").val() != "") {
				$("#alert").addClass("validate[required]").parent().find("span.star").html(" *");
			}
			$("#separator").isVisibleWhen("region","en,fr,us");
			$("#range,#range_fieldname,#range_alert").isVisibleWhen("region","international");
			$("div#layout").on("change", "#separator", function() {
				if ($(this).val() != "") {
					$("#alert").addClass("validate[required]").parent().find("span.star").html(" *");
				} else {
					$("#alert").removeClass("validate[required]").parent().find("span.star").html("");
				}
			});
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.common.edit_fieldset', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Format', 'selectlabel'=>'Use JText', 'options'=>'International=international||EN=en||FR=fr||US=us', 'bool8'=>false, 'storage_field'=>'region' ) ),
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Time', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=optgroup||Standard=1||Short=-1', 'selectlabel'=>'', 'storage_field'=>'time' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Separator', 'selectlabel'=>'Any', 'options'=>'-=-||.=.||/=/', 'bool8'=>false, 'storage_field'=>'separator' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Restriction', 'selectlabel'=>'None', 'options'=>'State is Future=isFuture', 'storage_field'=>'range' ) ),
	            JCckDev::renderLayoutFile(
	                'cck'.JCck::v().'.form.field', array(
	                    'label'=>JText::_( 'COM_CCK_ALERT' ).' (2)',
	                    'html'=>JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Alert', 'storage_field'=>'range_alert' ) )
	                )
	            ),
	            JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'range_fieldname' ) )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'validation'
) );
?>