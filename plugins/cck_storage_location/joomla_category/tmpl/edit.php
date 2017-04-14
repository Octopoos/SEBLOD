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

$object	=	'joomla_category';

echo JCckDev::getForm( 'core_form', '', $config, array( 'label'=>'Content Type', 'selectlabel'=>'Generic', 'options'=>'Linked to Content Type=optgroup', 'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published=1 AND storage_location=\"'.$object.'\"","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN"}', 'bool4'=>1, 'required'=>'', 'css'=>'storage-cck-more', 'attributes'=>'disabled="disabled"', 'storage_field'=>'storage_cck' ) );
?>