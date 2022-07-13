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

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                    JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
                                    JCckDev::renderForm( 'core_dev_text', @$options2['extension'], $config, array( 'label'=>'Extension', 'storage_field'=>'json[options2][extension]' ) ),
                                    JCckDev::renderForm( 'core_dev_text', @$options2['view'], $config, array( 'label'=>'View', 'storage_field'=>'json[options2][view]' ) )
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
                        'script'=>''
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>