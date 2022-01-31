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

require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_admin.php'; /* TODO#SEBLOD: >> core_storage_location */

$items		=	JCckDatabase::loadObjectList( 'SELECT id, title FROM #__cck_core_sites WHERE published = 1' );
$options	=	array();

if ( count( $items ) ) {
	foreach ( $items as $item ) {
		$options[]	=	$item->title.'='.$item->id;
	}
}
$options	=	implode( '||', $options );

// JS
$js =	'jQuery(document).ready(function($) {
			$("#itemid").isDisabledWhen("sef","0");
			$("#itemid_fieldname,#blank_li2").isVisibleWhen("itemid","-2");
			$("#sortable_core_dev_texts").isVisibleWhen("itemid","-3");
			$("#content_fieldname,#blank_li").isVisibleWhen("content","2");
			$("#content_location").isVisibleWhen("content","2,-2");
			$("#blank_li3").isVisibleWhen("content","-2");
			$("#title_custom").isVisibleWhen("title","2,3",false);
			$("#site").isVisibleWhen("path_type","1,2");
			$("#target_params").isVisibleWhen("target","modal");
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_sef', '', $config, array( 'selectlabel'=>'Inherited', 'storage_field'=>'sef' ) ),
				JCckDev::renderForm( 'core_menuitem', '', $config, array( 'selectlabel'=>'Inherited', 'options'=>'Parent=-1||Use Mapping=optgroup||Fields=-3||Use Value=optgroup||Field=-2' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'itemid_fieldname' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'Content', 'selectlabel'=>'Current', 'options'=>'Use Value=optgroup||Field=2||Own=-2', 'storage_field'=>'content' ) ),
				// JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'Content', 'selectlabel'=>'Current', 'options'=>'Next=4||Previous=5||Use Value=optgroup||Field=2', 'storage_field'=>'content' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'content_fieldname' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
				JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getStorageLocation', 'name'=>'core_storage_location' ), '', $config, array( 'label'=>'Content Object', 'storage_field'=>'content_location' ) ),
				JCckDev::renderBlank( '<input type="hidden" id="blank_li3" value="" />' ),
				JCckDev::renderForm( 'core_languages', '', $config, array( 'label'=>'Language', 'selectlabel'=>'Auto', 'storage_field'=>'language' ) ),
				JCckDev::renderForm( 'core_dev_texts', '', $config, array( 'label'=>'Menu Item', 'storage_field'=>'itemid_mapping' ) )
			)
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) ),
				JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Target Blank=_blank||Target Self=_self||Target Parent=_parent||Target Top=_top||Advanced=optgroup||Modal Box=modal', 'storage_field'=>'target' ) ),
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Parameters', 'cols'=>80, 'rows'=>1, 'storage_field'=>'target_params' ), array(), 'w100' ),
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
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Path Paths', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Absolute=1||Relative=0||Resource as Fragment=optgroup||Absolute Resource=2||Only Fragment=3', 'storage_field'=>'path_type' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Site', 'selectlabel'=>'Inherited', 'defaultvalue'=>'', 'options'=>$options, 'bool8'=>false, 'storage_field'=>'site' ) )
			),
			'legend'=>JText::_( 'COM_CCK_OPTIONS' )
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'link'
) );

JCckDev::initScript( 'link', $this->item );
?>
