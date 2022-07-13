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
			$("#title_custom").isVisibleWhen("title","2,3",false);
			$("#redirection_url,#blank_li").isVisibleWhen("redirection","url");
			$("#redirection_custom,#blank_li2").isVisibleWhen("redirection","");
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'defaultvalue'=>1, 'label'=>'Confirm', 'storage_field'=>'confirm' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Redirection', 'selectlabel'=>'Auto', 'options'=>'Url=url', 'storage_field'=>'redirection' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Url', 'storage_field'=>'redirection_url' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Redirection Custom Variables', 'storage_field'=>'redirection_custom' ) )
			)
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) ),
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
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'type'=>'radio', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Prepare=0||Apply=1', 'css'=>'btn-group', 'storage_field'=>'state' ) )
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