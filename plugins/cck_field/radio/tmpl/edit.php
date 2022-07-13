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
JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true, 'doTranslation'=>1, 'customAttr'=>JCck::getConfig_Param( 'development_attr', 6 ) ) );

$options    =   JCckDev::fromSTRING( $this->item->options );

// JS
$js =   'jQuery(document).ready(function($) {
            $("#bool2").isVisibleWhen("bool","1",false);
        });';

// Set
$displayData    =   array(
                        'config'=>$config,
                        'form'=>array(
                            array(
                                'fields'=>array(
                                    JCckDev::renderForm( 'core_label', $this->item->label, $config ),
                                    JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
                                    JCckDev::renderForm( 'core_sorting', $this->item->sorting, $config ),

                                    JCckDev::renderLayoutFile(
                                        'cck'.JCck::v().'.form.field', array(
                                            'label'=>JText::_( 'COM_CCK_ORIENTATION' ),
                                            'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
                                                'grid'=>'|50%',
                                                'html'=>array(
                                                    JCckDev::getForm( 'core_orientation', $this->item->bool, $config ),
                                                    JCckDev::getForm( 'core_orientation_vertical', $this->item->bool2, $config )
                                                )
                                            ) )
                                        )
                                    ),
                                    JCckDev::renderForm( 'core_options', $options, $config )
                                ),
                            ),
                            array(
                                'fields'=>array(
                                    JCckDev::getForm( 'core_storage', $this->item->storage, $config )
                                ),
                                'mode'=>'storage'
                            )
                        ),
                        'help'=>array( 'field', 'seblod-2-x-radio-field' ),
                        'html'=>'',
                        'item'=>$this->item,
                        'script'=>$js
                    );

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>