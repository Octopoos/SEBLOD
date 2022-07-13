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

// Init
JCckDev::initScript( 'restriction', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_location2', '', $config, array( 'label'=>'Location' ) ),
				JCckDev::renderForm( 'core_action2', '', $config ),
				JCckDev::renderForm( 'core_content_type', '', $config, array( 'selectlabel'=>'Any Form', 'required'=>'', 'storage_field'=>'form' ) ),
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Author', 'selectlabel'=>'Any Author', 'options'=>'Current=1||Someone Else=-1', 'required'=>'', 'storage_field'=>'author' ) ),
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_VARIABLE_VALUES' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|auto|100%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'storage_field'=>'trigger' ) ),
								JCckDev::getForm( 'core_dev_select', '', $config, array( 'label'=>'', 'selectlabel'=>'', 'defaultvalue'=>'isEqual', 'options'=>'STATE_IS_EQUAL_IN=isEqual||STATE_IS_FILLED=isFilled', 'storage_field'=>'match' ) ),
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'css'=>'input-small', 'storage_field'=>'values' ) )
							)
						) )
					)
				),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'type'=>'radio', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=1', 'css'=>'btn-group', 'storage_field'=>'do' ) )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>'',
	'type'=>'restriction'
) );
?>