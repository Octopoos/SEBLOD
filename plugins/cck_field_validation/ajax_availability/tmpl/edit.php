<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

echo JCckDev::renderForm( 'core_tables', '', $config, array( 'selectlabel'=>'Select', 'required'=>'required' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Column', 'required'=>'required', 'storage_field'=>'column' ) );
echo JCckDev::renderBlank();
echo JCckDev::renderForm( 'core_dev_text', 'id', $config, array( 'label'=>'Key Column', 'required'=>'required', 'storage_field'=>'key' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Fields', 'storage_field'=>'fieldnames' ) );
echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'do' ) );
?>