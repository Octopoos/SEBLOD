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
$options2   =   JCckDev::fromJSON( $this->item->options2 );

// JS
$js =   'jQuery(document).ready(function($) {
            $("#bool3").isVisibleWhen("json_options2_parent","0");
        });';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                    JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
                                    JCckDev::renderForm( 'core_dev_select', @$options2['mode'], $config, array( 'label'=>'Mode', 'selectlabel'=>'Use Global', 'options'=>'Ajax=ajax||Nested=nested', 'storage_field'=>'json[options2][mode]' ) ),
                                    JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config, array( 'defaultvalue'=>'' ) ),
                                    JCckDev::renderForm( 'core_dev_bool', @$options2['custom'], $config, array( 'label'=>'Allow Submissions', 'options'=>'Yes=1||No=0', 'storage_field'=>'json[options2][custom]' ) ),
                                    JCckDev::renderForm( 'core_dev_bool', @$options2['parent'], $config, array( 'label'=>'Parent', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'json[options2][parent]' ) ),
                                    JCckDev::renderForm( 'core_dev_text', @$options2['language'], $config, array( 'label'=>'Language', 'storage_field'=>'json[options2][language]' ) ),
                                    JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Multiple' ) )
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