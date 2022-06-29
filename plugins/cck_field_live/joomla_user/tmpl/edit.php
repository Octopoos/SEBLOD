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

JCckDev::initScript( 'live', $this->item );
JCck::loadModalBox();

// JS
$js =	'jQuery(document).ready(function($) {
			$("#excluded").isVisibleWhen("property","access,groups");
			$(".value-picker").on("click", function() {
				var field = $(this).attr("name");
				var cur = "none";
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=construct&name=joomla_user&type="+field+"&id=object_property";
				$.colorbox({href:url, iframe:true, innerWidth:600, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, className:"modal-small", onLoad: function(){ $("#cboxClose").remove();}});
			});
		});';

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
	'config'=>$config,
	'form'=>array(
		array(
			'fields'=>array(
				JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'User', 'selectlabel'=>'Current', 'options'=>'Session=session', 'storage_field'=>'content' ) ),
				JCckDev::renderLayoutFile(
					'cck'.JCck::v().'.form.field', array(
						'label'=>JText::_( 'COM_CCK_PROPERTY' ),
						'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.input', array(
							'input'=>JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Property', 'required'=>'required', 'storage_field'=>'property' ) ),
							'button'=>'<button type="button" id="storage_field_pick_property" name="property" class="value-picker btn btn-secondary"><span class="icon-expand"></span></button>'
						) )
					)
				),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Excluded Values', 'storage_field'=>'excluded' ) ),
				JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Default Value', 'storage_field'=>'default_value' ) )
			)
		)
	),
	'html'=>'',
	'item'=>$this->item,
	'script'=>$js,
	'type'=>'live'
) );

JCckDev::initScript( 'live', $this->item );
?>