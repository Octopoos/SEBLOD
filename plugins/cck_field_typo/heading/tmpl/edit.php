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
JCckDev::initScript( 'typo', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Heading', 'defaultvalue'=>3, 'options'=>'H1=1||H2=2||H3=3||H4=4||H5=5||H6=6', 'selectlabel'=>'', 'storage_field'=>'rank' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) ),
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Enable Anchor', 'defaultvalue'=>0,  'storage_field'=>'anchor' ) )
			)
		),
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom Attributes', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' ),
				JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Auto=0||Typo Label=1', 'storage_field'=>'typo_label' ) )
			),
			'legend'=>JText::_( 'COM_CCK_OPTIONS' )
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>'',
	'type'=>'typo'
) );
?>