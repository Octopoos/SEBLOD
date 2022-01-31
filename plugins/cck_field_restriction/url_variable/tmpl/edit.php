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

JCckDev::initScript( 'restriction', $this->item );

// JS
$js =	'jQuery(document).ready(function($) {
			$("#values").isVisibleWhen("match","isEqual",false);
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderBlank(),
				JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'type'=>'radio', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=1', 'css'=>'btn-group', 'storage_field'=>'do' ) ),
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_VARIABLE_VALUES' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
							'grid'=>'|auto|25%',
							'html'=>array(
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'storage_field'=>'trigger' ) ),
								JCckDev::getForm( 'core_dev_select', '', $config, array( 'label'=>'', 'selectlabel'=>'', 'defaultvalue'=>'isEqual', 'options'=>'STATE_IS_EQUAL_IN=isEqual||STATE_IS_FILLED=isFilled||STATE_IS_NULL=isNull', 'storage_field'=>'match' ) ),
								JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'css'=>'input-small', 'storage_field'=>'values' ) )
							)
						) )
					)
				)
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'restriction'
) );
?>