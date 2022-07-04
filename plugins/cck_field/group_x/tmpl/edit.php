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

JCckDev::forceStorage( 'custom' );

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_form', $this->item->extended, $config, array( 'label'=>'CONTENT_TYPE_FORM', 'selectlabel'=>'Select', 'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published!=-44","value":"name","orderby":"title","orderby_direction":"ASC","limit":""}', 'required'=>'required', 'storage_field'=>'extended' ) ),
									JCckDev::renderForm( 'core_rows', $this->item->rows, $config, array( 'label'=>'DEFAULT', 'defaultvalue'=>'1' ) ),
									JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'ADD', 'options'=>'No=0||Yes=1||Above=2||Below=3||Both=4', 'defaultvalue'=>'1' ) ),
									JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config, array( 'label'=>'MAXIMUM', 'defaultvalue'=>'10' ) ),
									JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'DEL', 'defaultvalue'=>'1' ) ),
									JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config, array( 'label'=>'MINIMUM', 'defaultvalue'=>'1' ) ),
									JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'DRAG', 'defaultvalue'=>'1' ) ),
									JCckDev::renderForm( 'core_orientation', $this->item->bool, $config, array( 'defaultvalue'=>'1', 'options'=>'Horizontal=0||Vertical=1||Table=2' ) )
								)
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value'=>'TEXT' ) )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array( 'field', 'seblod-2-x-field-x' ),
						'html'=>'',
						'item'=>$this->item,
						'script'=>'',
						'type'=>'field'
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>