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
			$("#extended1").isVisibleWhen("bool","0");
			$("#extended2").isVisibleWhen("bool","5");
			$("#location").isVisibleWhen("bool","1,2,-1");
		});';

$extended2	=	'';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Content Type=optgroup||Content Type=0||Multitype=-1||Multilanguage=optgroup||All Languages=1||Current Language=2||Search Type=optgroup||Search Type=5' ) ),
									JCckDev::renderForm( 'core_form', $this->item->extended, $config, array( 'label'=>'CONTENT_TYPE_FORM', 'selectlabel'=>'Select',
										'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published!=-44","value":"name","orderby":"title","orderby_direction":"ASC","limit":""}',
										'required'=>'required', 'storage_field'=>'extended1' ) ),
									JCckDev::renderForm( 'core_form', $this->item->extended, $config, array( 'label'=>'SEARCH_TYPE_LIST', 'selectlabel'=>'Select',
										'options2'=>'{"query":"","table":"#__cck_core_searchs","name":"title","where":"published!=-44 AND location = \'collection\'","value":"name","orderby":"title","orderby_direction":"ASC","limit":""}',
										'required'=>'required', 'storage_field'=>'extended2' ) ),
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