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

// JS
$js =   'jQuery(document).ready(function($) {
            $("#storage").on("change", function() {
                if ($(this).val() == "dev")  {
                    $("#bool").parent().show();
                    $("#blank_li").parent().hide();
                } else {
                    $("#bool").parent().hide();
                    $("#blank_li").parent().show();
                }
            });
            if ($("#storage").val() == "dev")  {
                $("#bool").parent().show();
                $("#blank_li").parent().hide();
            } else {
                $("#bool").parent().hide();
                $("#blank_li").parent().show();
            }
        });';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                            JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                            JCckDev::renderForm( 'core_dev_text', @$options2['extension'], $config, array( 'label'=>'Extension', 'storage_field'=>'json[options2][extension]' ) ),
                                            JCckDev::renderForm( 'core_place', $this->item->bool, $config, array( 'label'=>'Display Mode' ) ),
                                            JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' ),
                                            JCckDev::renderForm( 'core_dev_text', @$options2['section'], $config, array( 'label'=>'Section', 'storage_field'=>'json[options2][section]' ) )
                                ),
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'help'=>array(),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>$js
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>