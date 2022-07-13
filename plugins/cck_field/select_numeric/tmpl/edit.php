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

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
									JCckDev::renderForm( 'core_options_math', @$options2['math'], $config ),
									JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config ),
									JCckDev::renderForm( 'core_options_start', @$options2['start'], $config ),
									JCckDev::renderForm( 'core_options_first', @$options2['first'], $config ),
									JCckDev::renderForm( 'core_options_step', @$options2['step'], $config ),
									JCckDev::renderForm( 'core_options_last', @$options2['last'], $config ),
									JCckDev::renderForm( 'core_options_end', @$options2['end'], $config ),
									JCckDev::renderForm( 'core_dev_select', @$options2['force_digits'], $config, array( 'label'=>'Force Digits', 'defaultvalue'=>'0', 'selectlabel'=>'',
																						 								'options'=>'No=0||2 Digits=2||3 Digits=3||4 Digits=4||5 Digits=5', 'storage_field'=>'json[options2][force_digits]' ) ),
									JCckDev::renderBlank(),
									JCckDev::renderForm( 'core_dev_select', @$options2['force_decimals'], $config, array( 'label'=>'Force Decimals', 'defaultvalue'=>'0', 'selectlabel'=>'',
																						 								 'options'=>'No=0||2 Decimals=2', 'storage_field'=>'json[options2][force_decimals]' ) )
                                )
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'INT(11)' ) )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'help'=>array( 'field', 'seblod-2-x-select-numeric-field' ),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>'',
                        'type'=>'field'
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>