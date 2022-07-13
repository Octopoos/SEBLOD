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
$parent		=	JCck::on( '4.0' ) ? '.parent()' : '';

// JS
$js =	'jQuery(document).ready(function($) {
			$("#storage").on("change", function() {
		        if ($(this).val() == "dev")  {
		            $("#bool,#selectlabel").parent()'.$parent.'.show();
		        } else {
		            $("#bool,#selectlabel").parent()'.$parent.'.hide();
		        }
		    });
		    if ($("#storage").val() == "dev")  {
		        $("#bool,#selectlabel").parent()'.$parent.'.show();
		    } else {
		        $("#bool,#selectlabel").parent()'.$parent.'.hide();
		    }
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_description', $this->item->defaultvalue, $config, array( 'label'=>'Default Value', 'storage_field'=>'defaultvalue') ),
									JCckDev::renderForm( 'core_options_editor', @$options2['editor'], $config ),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>JText::_( 'COM_CCK_WIDTH_HEIGHT' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'40%|10%|40%|10%',
												'html'=>array(
													JCckDev::getForm( 'core_options_width', @$options2['width'], $config, array( 'defaultvalue'=>'100%' ) ),
													'<span class="variation_value" style="margin-right: 5px;">x</span>',
													JCckDev::getForm( 'core_options_height', @$options2['height'], $config, array( 'defaultvalue'=>'280', 'attributes'=>'placeholder="280"' ) ),
													'<span class="variation_value">px</span></li>'
												)
											) )
										)
									),
									JCckDev::renderForm( 'core_place', $this->item->bool, $config, array( 'label'=>'DISPLAY_MODE' ) ),
									JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config ),
									JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'Show Buttons', 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1' ) )
								)
							),
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_options_import', @$options2['import'], $config )
								),
								'legend'=>JText::_( 'COM_CCK_PROCESSING' )
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'TEXT' ) )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array( 'field', 'seblod-2-x-wysiwyg-editor-field' ),
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>