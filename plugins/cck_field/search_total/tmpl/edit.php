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
JCckDev::forceStorage();

$options2       =   JCckDev::fromJSON( $this->item->options2 );
$defaultvalue   =   ( !isset( $options2['jtext'] ) ) ? '1' : '0';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                    JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
                                    JCckDev::renderForm( 'core_dev_text', @$options2['jtext'], $config, array( 'label'=>'Language Constant', 'defaultvalue'=>'COM_CCK_SEARCH_TOTAL', 'storage_field'=>'json[options2][jtext]' ) ),
                                    JCckDev::renderForm( 'core_dev_bool', @$options2['alternative'], $config, array( 'label'=>'Use Alternative', 'defaultvalue'=>$defaultvalue, 'storage_field'=>'json[options2][alternative]' ) )
                                )
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>''
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>