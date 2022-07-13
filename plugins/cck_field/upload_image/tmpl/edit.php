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
$options2	=	JCckDev::fromJSON( $this->item->options2 );
$media_ext	=	( $this->isNew ) ? '' : ( ( isset( $options2['media_extensions'] ) ) ? $options2['media_extensions'] : 'custom' );

// JS
$html		=	'';
$js			=	'';

for ( $i = 2; $i < 11; $i++ ) {
	$html	.=	JCckDev::renderForm( 'core_options_thumb_process', @$options2['thumb'.$i.'_process'], $config, array( 'label'=>'Thumb'.$i, 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_process]' ) )
			.	JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_MAXIMUM_SIZE' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'40%|10%|40%|10%',
							'html'=>array(
								JCckDev::getForm( 'core_options_thumb_width', @$options2['thumb'.$i.'_width'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_width]' ) ),
								'<span class="variation_value" style="margin-right: 5px;">x</span>',
								JCckDev::getForm( 'core_options_thumb_height', @$options2['thumb'.$i.'_height'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_height]' ) ),
								'<span class="variation_value">px</span></li>'
							)
						) )
					)
				);
	$js		.=	'$("#json_options2_thumb'.$i.'_width").isVisibleWhen("json_options2_thumb'.$i.'_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic",true,"visibility");'."\n";
}

$js	=	'jQuery(document).ready(function($) {
			$("#json_options2_legal_extensions").isVisibleWhen("json_options2_media_extensions","custom",false);
			$("#json_options2_image_width").isVisibleWhen("json_options2_image_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic",true,"visibility");
			$("#json_options2_thumb1_width").isVisibleWhen("json_options2_thumb1_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic",true,"visibility");
			'.$js.'
			$("#json_options2_title_image").isVisibleWhen("json_options2_multivalue_mode","1");
			$("#json_options2_desc_image").isVisibleWhen("json_options2_multivalue_mode","1");
			$("#blank_li").isVisibleWhen("json_options2_multivalue_mode","1");
			$("#json_options2_path_label").isVisibleWhen("json_options2_custom_path","1",false);
			$("#json_options2_title_label").isVisibleWhen("json_options2_title_image","1",false);
			$("#json_options2_desc_label").isVisibleWhen("json_options2_desc_image","1",false);
			$("#json_options2_storage_format").isDisabledWhen("json_options2_path_user","1" );
			$("#json_options2_path_user").isDisabledWhen("json_options2_storage_format","1" );
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
											JCckDev::renderForm( 'core_label', $this->item->label, $config ),
											JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
											JCckDev::renderForm( 'core_options_path', @$options2['path'], $config, array( 'required'=>'required' ) ),
											JCckDev::renderForm( 'core_options_format_file', @$options2['storage_format'], $config, array( 'options'=>'Full Path=0' ) ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_PATH_PER_CONTENT' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|50%',
														'html'=>array(
															JCckDev::getForm( 'core_options_path_content', @$options2['path_content'], $config ),
															JCckDev::getForm( 'core_dev_text', @$options2['folder_permissions'], $config, array( 'defaultvalue'=>'0755', 'size'=>4, 'storage_field'=>'json[options2][folder_permissions]' ) )
														)
													) )
												)
											),
											JCckDev::renderForm( 'core_options_path_user', @$options2['path_user'], $config ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_LEGAL_EXTENSIONS' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|70%',
														'html'=>array(
															JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getMediaExtensions', 'name'=>'core_options_media_extensions' ), $media_ext, $config, array( 'defaultvalue'=>'image', 'options'=>'image', 'storage_field'=>'json[options2][media_extensions]' ) ),
															JCckDev::getForm( 'core_options_legal_extensions_image', @$options2['legal_extensions'], $config, array( 'size'=>13, 'required'=>'required' ) )
														)
													) )
												)
											),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_MAXIMUM_SIZE' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|25%',
														'html'=>array(
															JCckDev::getForm( 'core_options_max_size', @$options2['max_size'], $config ),
															JCckDev::getForm( 'core_options_size_unit', @$options2['size_unit'], $config )
														)
													) )
												)
											),
											JCckDev::renderForm( 'core_options_preview_image', @$options2['form_preview'], $config, array( 'label'=>'SHOW_PREVIEW', 'storage_field'=>'json[options2][form_preview]' ) ),
											JCckDev::renderForm( 'core_options_delete_box', @$options2['delete_box'], $config ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_SHOW_CUSTOM_PATH' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|75%',
														'html'=>array(
															JCckDev::getForm( 'core_bool', @$options2['custom_path'], $config, array( 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][custom_path]' ) ),
															JCckDev::getForm( 'core_options_path', @$options2['path_label'], $config, array( 'defaultvalue'=>'Path', 'size'=>18, 'storage_field'=>'json[options2][path_label]' ) )
														)
													) )
												)
											),
											JCckDev::renderForm( 'core_size', $this->item->size, $config ),
											JCckDev::renderForm( 'core_options_multivalue_mode', @$options2['multivalue_mode'], $config, array( 'label'=>'MULTIVALUE_MODE' ) ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_SHOW_TITLE' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|75%',
														'html'=>array(
															JCckDev::getForm( 'core_bool', @$options2['title_image'], $config, array( 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][title_image]' ) ),
															JCckDev::getForm( 'core_options_path', @$options2['title_label'], $config, array( 'defaultvalue'=>'Title', 'size'=>18, 'storage_field'=>'json[options2][title_label]' ) )
														)
													) )
												)
											),
											JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_SHOW_DESCRIPTION_ALT' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|75%',
														'html'=>array(
															JCckDev::getForm( 'core_bool', @$options2['desc_image'], $config, array( 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][desc_image]' ) ),
															JCckDev::getForm( 'core_options_path', @$options2['desc_label'], $config, array( 'defaultvalue'=>'Description alt', 'size'=>18, 'storage_field'=>'json[options2][desc_label]' ) )
														)
													) )
												)
											)
								),
							),
							array(
								'fields'=>array(
											JCckDev::renderForm( 'core_options_force_thumb_creation', @$options2['force_thumb_creation'], $config, array( 'label'=>'FORCE_THUMB_CREATION' ) ),
											JCckDev::renderForm( 'core_options_preview_image', @$options2['content_preview'], $config, array( 'defaultvalue'=>'1', 'label'=>'DISPLAY_AS_DEFAULT',
																	'options'=> 'Image=0||Thumb1=1||Thumb2=2||Thumb3=3||Thumb4=4||Thumb5=5||Thumb6=6||Thumb7=7||Thumb8=8||Thumb9=9||Thumb10=10', 'storage_field'=>'json[options2][content_preview]' ) ),
											JCckDev::renderForm( 'core_options_image_process', @$options2['image_process'], $config, array( 'label'=>'Image', 'defaultvalue'=>'', 'storage_field'=>'json[options2][image_process]' ) ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_WIDTH_HEIGHT' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'40%|10%|40%|10%',
														'html'=>array(
															JCckDev::getForm( 'core_options_image_width', @$options2['image_width'], $config ),
															'<span class="variation_value" style="margin-right: 5px;">x</span>',
															JCckDev::getForm( 'core_options_image_height', @$options2['image_height'], $config ),
															'<span class="variation_value">px</span></li>'
														)
													) )
												)
											),
											JCckDev::renderForm( 'core_options_thumb_process', @$options2['thumb1_process'], $config, array( 'label'=>'Thumb1' ) ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JText::_( 'COM_CCK_WIDTH_HEIGHT' ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'40%|10%|40%|10%',
														'html'=>array(
															JCckDev::getForm( 'core_options_thumb_width', @$options2['thumb1_width'], $config ),
															'<span class="variation_value" style="margin-right: 5px;">x</span>',
															JCckDev::getForm( 'core_options_thumb_height', @$options2['thumb1_height'], $config ),
															'<span class="variation_value">px</span></li>'
														)
													) )
												)
											),
											$html
								),
								'legend'=>JText::_( 'COM_CCK_PROCESSING' )
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array( 'field', 'seblod-2-x-upload-image-field' ),
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>