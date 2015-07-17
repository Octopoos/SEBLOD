<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// TODO: cache all tables, filter, and push.
echo JCckDev::getForm( 'core_form', '', $config, array( 'label'=>'Content Type', 'selectlabel'=>'Generic', 'options'=>'Linked to Content Type=optgroup', 'bool4'=>1, 'required'=>'', 'css'=>'storage-cck-more', 'storage_field'=>'storage_cck' ) );
?>