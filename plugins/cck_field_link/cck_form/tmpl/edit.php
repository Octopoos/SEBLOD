<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'link', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_form', '', $config, array( 'selectlabel'=>'Inherited', 'options'=>'Use Value=optgroup||Field=-2||Forms=optgroup', 'bool4'=>1, 'required'=>'' ) );
		echo JCckDev::renderForm( 'core_menuitem', '', $config, array( 'selectlabel'=>'Inherited' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_EDITION' ).'</label>'
			. JCckDev::getForm( 'core_dev_bool', '', $config, array( 'label'=>'Edition', 'options'=>'No=0||Yes=1||Yes as copy=2', 'storage_field'=>'form_edition' ) )
			. JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'Auto', 'defaultvalue'=>'', 'options'=>'1=1||2=2||3=3||4=4||5=5||Final=0', 'bool8'=>0, 'storage_field'=>'form_edition_stage' ) )
			. '</li>';
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'form_fieldname' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Redirection', 'selectlabel'=>'Auto', 'options'=>'Inherited=-1', 'storage_field'=>'redirection' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Redirection Custom Variables', 'storage_field'=>'redirection_custom' ) );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'storage_field'=>'target' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>24, 'storage_field'=>'rel' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_TITLE' ).'</label>'
			. JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'None', 'options'=>'Custom Text=2||Translated Text=3', 'storage_field'=>'title' ) )
			. JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Title', 'size'=>16, 'css'=>'input-medium', 'storage_field'=>'title_custom' ) )
			. '</li>';
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Apply=1||Prepare=0', 'storage_field'=>'state' ) );
		echo JCckDev::renderForm( 'core_tmpl', '', $config );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONFIG_NO_ACCESS' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Show Value', 'selectlabel'=>'', 'options'=>'Hide=0||Show=1', 'storage_field'=>'no_access' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#form_edition,#blank_li').isVisibleWhen('form','');
	$('#form_fieldname').isVisibleWhen('form','-2');
	$('#title_custom').isVisibleWhen('title','2,3',false);
	$('#redirection_custom,#blank_li').isVisibleWhen('redirection','');
});
</script>