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

$hide	=	$this->item->alt ? 'hide' : '';

if ( $this->item->alt ) {
	$hide		=	'hide';
	$required	=	'';
} else {
	$hide		=	'';
	$required	=	'required';
}

// JS
$js =	'jQuery(document).ready(function($) {
			$("#search_fieldname").isVisibleWhen("search_field","1");
			$("#target_params").isVisibleWhen("target","modal");
			$("#title_custom").isVisibleWhen("title","2,3",false);
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_list', '', $config, array(), array(), $hide ),
				JCckDev::renderForm( 'core_menuitem', '', $config, array( 'required'=>$required ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>0, 'label'=>'Field', 'options'=>'None=-1||Field=optgroup||Inherited=0||Custom=1', 'selectlabel'=>'', 'storage_field'=>'search_field' ), array(), $hide ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field name', 'required'=>'required', 'storage_field'=>'search_fieldname' ), array(), $hide )
			)
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) ),
				JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Target Blank=_blank||Target Self=_self||Target Parent=_parent||Target Top=_top||Advanced=optgroup||Modal Box=modal', 'storage_field'=>'target' ) ),
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Parameters', 'cols'=>80, 'rows'=>1, 'storage_field'=>'target_params' ), array(), 'w100' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>32, 'storage_field'=>'rel' ) ),
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
				JCckDev::renderBlank(),
				JCckDev::renderForm( 'core_tmpl', '', $config ),
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'type'=>'radio', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Prepare=0||Apply=1', 'css'=>'btn-group', 'storage_field'=>'state' ) ),
			),
			'legend'=>JText::_( 'COM_CCK_OPTIONS' )
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'link'
) );
?>