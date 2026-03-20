<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Init
JCckDev::initScript( 'link', $this->item );

// JS
$js =	'jQuery(document).ready(function($) {
			$("#form_edition_stage").isVisibleWhen("form_edition","1",false);
			$("#form_fieldname").isVisibleWhen("form","-2");
			$("#title_custom").isVisibleWhen("title","2,3",false);
			$("#redirection_custom,#blank_li").isVisibleWhen("redirection","");
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_ACTION' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|50%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_bool', '', $config, array( 'options'=>'Add=0||Edit=1||Edit As Copy=2', 'storage_field'=>'form_edition' ) ),
								JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'Auto', 'defaultvalue'=>'', 'options'=>'1=1||2=2||3=3||4=4||5=5||Final=0', 'bool8'=>0, 'storage_field'=>'form_edition_stage' ) )
							)
						) )
					)
				),
				JCckDev::renderForm( 'core_menuitem', '', $config, array( 'selectlabel'=>'Inherited' ) ),
				JCckDev::renderForm( 'core_form', '', $config, array( 'selectlabel'=>'Inherited', 'options'=>'Use Value=optgroup||Field=-2||Forms=optgroup', 'bool4'=>1, 'required'=>'' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'form_fieldname' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Redirection', 'selectlabel'=>'Auto', 'options'=>'Inherited=-1', 'storage_field'=>'redirection' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Redirection Custom Variables', 'storage_field'=>'redirection_custom' ) )
			)
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) ),
				JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'storage_field'=>'target' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>24, 'storage_field'=>'rel' ) ),
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_TITLE' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|50%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'None', 'options'=>'Custom Text=2||Translated Text=3', 'storage_field'=>'title' ) ),
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Title', 'size'=>16, 'css'=>'input-medium', 'storage_field'=>'title_custom' ) )
							)
						) )
					)
				),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'type'=>'radio', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Prepare=0||Apply=1', 'css'=>'btn-group', 'storage_field'=>'state' ) ),
				JCckDev::renderForm( 'core_tmpl', '', $config ),
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' )
			),
			'legend'=>JText::_( 'COM_CCK_OPTIONS' )
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Show Value', 'type'=>'radio', 'selectlabel'=>'', 'options'=>'Hide=0||Show=1', 'css'=>'btn-group', 'storage_field'=>'no_access' ) )
			),
			'legend'=>JText::_( 'COM_CCK_CONFIG_NO_ACCESS' )
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'link'
) );
?>