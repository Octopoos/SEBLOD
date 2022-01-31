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

JCckDev::initScript( 'restriction', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_location2', '', $config, array( 'label'=>'Location' ) ),
				JCckDev::renderForm( 'core_action2', '', $config ),
				JCckDev::renderForm( 'core_form', '', $config, array( 'selectlabel'=>'Any Form', 'required'=>'' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Author', 'selectlabel'=>'Any Author', 'options'=>'Current=1||Someone Else=-1', 'required'=>'', 'storage_field'=>'author' ) )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>'',
	'type'=>'restriction'
) );
?>