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
JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true,
												  'customAttr'=>array( 'direction', 'prepend' ), 'customAttrLabel'=>JText::_( 'COM_CCK_DIRECTION_ASC_DESC' ),
												  'fieldPicker'=>true ) );
JCckDev::forceStorage();

$options	=	JCckDev::fromSTRING( $this->item->options );

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_extended', $this->item->extended, $config, array( 'label'=>'Field Optional', 'required'=>'' ) ),
									JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Fields', 'rows'=>1 ), array( 'after'=>$this->item->init['fieldPicker'] ) ),
									JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Mode', 'type'=>'radio', 'defaultvalue'=>'1', 'options'=>'Numeric=0||Text=1', 'css'=>'btn-group' ) )
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