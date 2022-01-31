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

// Init
JCckDev::forceStorage();

// JS
$js =	'jQuery(document).ready(function($) {
			$("#defaultvalue").isVisibleWhen("bool","0",false);
			$("#bool2,#bool3").isVisibleWhen("bool","0");
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
											JCckDev::renderForm( 'core_label', $this->item->label, $config ),
											JCckDev::renderLayoutFile(
												'cck'.JCck::v().'.form.field', array(
													'label'=>JCckDev::getLabel( 'core_pane_behavior', $config ),
													'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
														'grid'=>'|25%',
														'html'=>array(
															JCckDev::getForm( 'core_pane_behavior', $this->item->bool, $config ),
															JCckDev::getForm( 'core_defaultvalue', $this->item->defaultvalue, $config, array( 'size'=>3, 'variation'=>'custom_number', 'attributes'=>'min="0" placeholder="'.JText::_( 'COM_CCK_0' ).'" style="text-align:center;"' ) )
														)
													) )
												)
											),
											JCckDev::renderForm( 'core_dev_text', $this->item->location, $config, array( 'label'=>'GROUP_IDENTIFIER', 'storage_field'=>'location' ) ),											
											JCckDev::renderForm( 'core_dev_bool', $this->item->bool3, $config, array( 'label'=>'ITEM_IDENTIFIER', 'type'=>'radio', 'defaultvalue'=>'0', 'options'=>'Field Name=0||Label=1', 'css'=>'btn-group', 'storage_field'=>'bool3' ) ),
											JCckDev::renderForm( 'core_dev_bool', $this->item->bool2, $config, array( 'label'=>'URL_MODIFIER', 'defaultvalue'=>'0', 'options'=>'None=0||Set Active Pane=1||Set Active Pane and URL Hash=2', 'storage_field'=>'bool2' ) )
								),
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array(),
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>