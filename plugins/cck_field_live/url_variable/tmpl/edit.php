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

// JS
$js =	'jQuery(document).ready(function($) {
			$("#sortable_core_dev_texts, #return").isVisibleWhen("multiple","1");
			$("#variable,#default_value,#crypt").isVisibleWhen("multiple","0");
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_variable_type', '', $config ),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Multiple', 'storage_field'=>'multiple' ) ),
				JCckDev::renderForm( 'core_dev_texts', '', $config, array( 'label'=>'Variables', 'storage_field'=>'variables' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Variable', 'storage_field'=>'variable' ) ),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Ignore NULL', 'storage_field'=>'ignore_null' ) ),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Return', 'options'=>'First=first||Last=last', 'storage_field'=>'return' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Default Value', 'storage_field'=>'default_value' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Encryption', 'selectlabel'=>'None', 'options'=>'Base64=base64', 'bool8'=>0, 'storage_field'=>'crypt' ) )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'live'
) );

JCckDev::initScript( 'live', $this->item );
?>
