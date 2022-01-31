<?php
defined( '_JEXEC' ) or die;

echo JCckDev::renderForm(
	'core_form',
	'',
	$displayData['config'],
	array( 'label'=>'Content Type2', 'selectlabel'=>'None', 'options'=>'Linked to Content Type=optgroup', 'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published=1 AND storage_location=\"'.$displayData['name'].'\"","value":"name","orderby":"title","orderby_direction":"ASC","limit":"","language_detection":"joomla","language_codes":"EN,GB,US,FR","language_default":"EN"}', 'bool4'=>1, 'required'=>'', 'css'=>'storage-cck-more', 'attributes'=>'disabled="disabled"', 'storage_field'=>'storage_cck' ),
	array( 'after'=>'<span class="switch notice">'.JText::_( 'COM_CCK_FIELD_WILL_NOT_BE_LINKED' ).JText::_( 'COM_CCK_FIELD_WILL_BE_LINKED' ).'</span>' ),
	'w100'
);
?>