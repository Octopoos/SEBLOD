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

// Init
JCckDev::initScript( 'typo', $this->item );

$parent		=	'';

if ( JCck::on( '4.0' ) ) {
	$parent	=	'$(".identifier_toggle").isVisibleWhen("type","form,form_disabled,form_hidden",false);'
			.	'$(".identifier_toggle2").isVisibleWhen("type","form,form_disabled,form_hidden,increment",false);';
}

// JS
$js =	'jQuery(document).ready(function($) {
			$("#class").isVisibleWhen("type","activation,block,featured,state");
			$("#class1").isVisibleWhen("type","dropdown,selection");
			$("#class2,#identifier").isVisibleWhen("type","form,form_disabled,form_hidden");
			$("#identifier_name").isVisibleWhen("type","form,form_disabled,form_hidden,increment");
			$("#task").isVisibleWhen("type","sort");
			$("#blank_li2,#task_id_process").isVisibleWhen("task","process_ajax");	
			$("#identifier_suffix").isDisabledWhen("type","increment");
			$("#blank_li").isVisibleWhen("type","increment");
			$("#start").isVisibleWhen("type","increment");
			$("#required").isVisibleWhen("type","form");
			$("#trigger").isVisibleWhen("type","form,selection");
			$("#state_up,#state_down,#state_title").isVisibleWhen("type","state");
			'.$parent.'
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_jgrid_type', '', $config ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'btn btn-micro hasTooltip', 'size'=>24, 'storage_field'=>'class' ) ),
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Start', 'defaultvalue'=>'1', 'options'=>'0=0||1=1', 'storage_field'=>'start' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'', 'size'=>24, 'storage_field'=>'class1' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'input-small', 'size'=>24, 'storage_field'=>'class2' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Task', 'defaultvalue'=>'', 'selectlabel'=>'', 'options'=>'Use Native=||SEBLOD Toolbox Addon=optgroup||Task Process Ajax=process_ajax', 'storage_field'=>'task' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' ),
				JCckDev::renderForm( 'core_task_processing', '', $config, array( 'storage_field'=>'task_id_process' ) ),
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_IDENTIFIER' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|50%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_bool', '', $config, array( 'label'=>'', 'defaultvalue'=>'id', 'options'=>'ID=id||Primary Key=pk', 'storage_field'=>'identifier' ) ),
								JCckDev::getForm( 'core_dev_bool', '', $config, array( 'label'=>'', 'defaultvalue'=>'1', 'storage_field'=>'use_identifier' ) )
							)
						) ),
						'class'=>'identifier_toggle'
					)
				),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_CONTAINER_NAME' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|50%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'storage_field'=>'identifier_suffix' ) ),
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'size'=>13, 'storage_field'=>'identifier_name' ) )
							)
						) ),
						'class'=>'identifier_toggle2'
					)
				),
				JCckDev::renderForm( 'core_required', '', $config, array( 'defaultvalue'=>'0' ) ),
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Trigger Change', 'defaultvalue'=>'0', 'storage_field'=>'trigger' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Status Up Field Name', 'defaultvalue'=>'', 'storage_field'=>'state_up', 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_FIELD_NAME' ).'"' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Status Down Field Name', 'defaultvalue'=>'', 'storage_field'=>'state_down', 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_FIELD_NAME' ).'"' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Status Title Tooltip', 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Self=0', 'storage_field'=>'state_title' ) )
			)
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Auto=0||Always=-2', 'storage_field'=>'typo_label' ) )
			),
			'legend'=>JText::_( 'COM_CCK_OPTIONS' )
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'typo'
) );
?>