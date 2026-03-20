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
				JCckDev::renderForm( 'core_validation_field', '', $config )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>'',
	'type'=>'validation'
) );
?>