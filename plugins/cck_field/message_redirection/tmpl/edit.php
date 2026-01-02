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

use Joomla\CMS\Language\Text;

// Init
JCckDev::forceStorage();
$options2	=	JCckDev::fromJSON( $this->item->options2 );

// JS
$js =	'jQuery(document).ready(function($) {
			$("#json_options2_status").isVisibleWhen("json_options2_timeout","0",false);
			$("#json_options2_timeout_ms").isVisibleWhen("json_options2_timeout","1",false);
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_menuitem', @$options2['itemid'], $config, array( 'label'=>'Redirection', 'options'=>'Inherited=-1||Previous Segment=-2', 'selectlabel'=>'None', 'storage_field'=>'json[options2][itemid]' ) ),

									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>Text::_( 'COM_CCK_TIMEOUT_MS' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'|50%',
												'html'=>array(
													JCckDev::getForm( 'core_dev_bool', @$options2['timeout'], $config, array( 'defaultvalue'=>'0', 'storage_field'=>'json[options2][timeout]' ) ),
													JCckDev::getForm( 'core_dev_text', @$options2['timeout_ms'], $config, array( 'size'=>12, 'storage_field'=>'json[options2][timeout_ms]' ) )
												)
											) )
										)
									),
									JCckDev::renderForm( 'core_dev_select', @$options2['message_style'], $config, array( 'label'=>'Message', 'defaultvalue'=>'', 'selectlabel'=>'None', 'options'=>'Error=error||Message=message||Notice=notice||Warning=warning', 'storage_field'=>'json[options2][message_style]' ) ),
									JCckDev::renderForm( 'core_message', @$options2['message'], $config, array( 'label'=>'Message', 'defaultvalue'=>'', 'storage_field'=>'json[options2][message]' ) ),
									JCckDev::renderForm( 'core_dev_select', @$options2['status_code'], $config, array( 'label'=>'Status Code', 'defaultvalue'=>'303', 'selectlabel'=>'', 'options'=>'301||303', 'bool8'=>0, 'storage_field'=>'json[options2][status_code]' ) )
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