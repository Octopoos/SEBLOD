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

// JS
$js =	'jQuery(document).ready(function($) {
			$("#format_custom").isVisibleWhen("format","-1");
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_select', 'Y-m-d', $config, array( 'label'=>'Format', 'defaultvalue'=>'', 'selectlabel'=>'Auto Alias',
									  'options'=>'AUTO_SQL_DATE=auto||Free=-1||Use JText=optgroup||DATE_FORMAT_LC=DATE_FORMAT_LC||DATE_FORMAT_LC1=DATE_FORMAT_LC1||DATE_FORMAT_LC2=DATE_FORMAT_LC2||DATE_FORMAT_LC3=DATE_FORMAT_LC3||DATE_FORMAT_LC4=DATE_FORMAT_LC4||DATE_FORMAT_JS1=DATE_FORMAT_JS1||DATE_FORMAT_TZ=DATE_FORMAT_TZ',
									  'storage_field'=>'format' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Free Format', 'storage_field'=>'format_custom' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Modifier', 'storage_field'=>'modify' ) ),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Apply Time Zone', 'defaultvalue'=>'0', 'storage_field'=>'timezone' ) ),
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Return JText', 'cols'=>50, 'rows'=>1, 'storage_field'=>'return_jtext' ), array(), 'w100' )
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