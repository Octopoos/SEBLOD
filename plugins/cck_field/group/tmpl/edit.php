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
JCckDev::forceStorage();

// JS
$js =	'jQuery(document).ready(function($) {
			$("#extended").isVisibleWhen("bool","0");
			$("#location").isVisibleWhen("bool","1,2");
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Standard=0||Multilanguage=optgroup||All=1||Current=2' ) ),
									JCckDev::renderForm( 'core_form', $this->item->extended, $config, array( 'label'=>'CONTENT_TYPE_FORM', 'selectlabel'=>'Select',
										'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published!=-44","value":"name","orderby":"title","orderby_direction":"ASC","limit":""}',
										'required'=>'required', 'storage_field'=>'extended' ) ),
									JCckDev::renderForm( 'core_extended', $this->item->location, $config, array( 'label'=>'CONTENT_TYPE_FORM', 'storage_field'=>'location' ) )
								)
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config )
								),
								'mode'=>'storage'
							)
						),
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>