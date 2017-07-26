<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'typo', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_TYPO_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_jgrid_type', '', $config );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'btn btn-micro hasTooltip', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Start', 'defaultvalue'=>'1', 'options'=>'0=0||1=1', 'storage_field'=>'start' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'', 'size'=>24, 'storage_field'=>'class1' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'defaultvalue'=>'input-small', 'size'=>24, 'storage_field'=>'class2' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_IDENTIFIER' ).'</label>'
			. JCckDev::getForm( 'core_dev_bool', '', $config, array( 'label'=>'', 'defaultvalue'=>'id', 'options'=>'ID=id||Primary Key=pk', 'storage_field'=>'identifier' ) )
			. JCckDev::getForm( 'core_dev_bool', '', $config, array( 'label'=>'', 'defaultvalue'=>'1', 'storage_field'=>'use_identifier' ) )
			. '</li>';
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo '<li><label>'.JText::_( 'COM_CCK_CONTAINER_NAME' ).'</label>'
			. JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'css'=>'input-xsmall', 'storage_field'=>'identifier_suffix' ) )
			. JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'size'=>13, 'storage_field'=>'identifier_name' ) )
			. '</li>';
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Trigger Change', 'defaultvalue'=>'0', 'storage_field'=>'trigger' ) );

		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Status Up Field Name', 'defaultvalue'=>'', 'storage_field'=>'state_up', 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_FIELD_NAME' ).'"' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Status Title Tooltip', 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Self=0', 'storage_field'=>'state_title' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Status Down Field Name', 'defaultvalue'=>'', 'storage_field'=>'state_down', 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_FIELD_NAME' ).'"' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Auto=0||Always=-2', 'storage_field'=>'typo_label' ) );
		echo JCckDev::renderBlank();
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#class').isVisibleWhen('type','activation,block,featured,state');
	$('#class1').isVisibleWhen('type','dropdown');
	$('#class2,#identifier').isVisibleWhen('type','form,form_disabled,form_hidden');
	$('#identifier_name').isVisibleWhen('type','form,form_disabled,form_hidden,increment');
	$('#identifier_suffix').isDisabledWhen('type','increment');
	$('#blank_li').isVisibleWhen('type','increment');
	$('#start').isVisibleWhen('type','increment');
	$('#trigger').isVisibleWhen('type','form,selection');
	$('#state_up,#state_down,#state_title').isVisibleWhen('type','state');
});
</script>