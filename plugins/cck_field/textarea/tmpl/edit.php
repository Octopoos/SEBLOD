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

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_defaultvalue_textarea', $this->item->defaultvalue, $config ),
									JCckDev::renderForm( 'core_columns', $this->item->cols, $config ),
									JCckDev::renderForm( 'core_rows', $this->item->rows, $config, array( 'defaultvalue'=>'3' ) ),
									JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config ),
									JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config ),
									JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'type'=>'radio', 'label'=>'Characters Remaining', 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1', 'css'=>'btn-group' ) )
								)
							),
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'New Lines', 'options'=>'tag_br=0||tag_br_in_p=2||tag_p=1' ) ),
									JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Clear Blank Lines' ) ),
									JCckDev::renderForm( 'core_bool2', $this->item->bool7, $config, array( 'type'=>'radio', 'defaultvalue'=>'1', 'label'=>'Form Filter', 'css'=>'btn-group', 'storage_field'=>'bool7' ) )
								),
								'legend'=>JText::_( 'COM_CCK_PROCESSING' )
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value'=>'TEXT' ) )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array( 'field', 'seblod-2-x-textarea-field' ),
						'html'=>'',
						'item'=>$this->item,
						'script'=>''
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>