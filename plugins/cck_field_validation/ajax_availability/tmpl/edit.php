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

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.common.edit_fieldset', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getTables', 'name'=>'core_tables' ), '', $config, array( 'selectlabel'=>'Select', 'required'=>'required', 'storage_field'=>'table' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Column', 'required'=>'required', 'storage_field'=>'column' ) ),
				JCckDev::renderBlank(),
				JCckDev::renderForm( 'core_dev_text', 'id', $config, array( 'label'=>'Key Column', 'required'=>'required', 'storage_field'=>'key' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Fields', 'storage_field'=>'fieldnames' ) ),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'do' ) )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>'',
	'type'=>'validation'
) );
?>