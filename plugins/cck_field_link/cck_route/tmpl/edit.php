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
?>
<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Auto=-1||Collection=1||||Collection=11||Fields=0', 'storage_field'=>'mode' ) );
		echo JCckDev::renderForm( 'core_form', '', $config, array( 'label'=>'CONTENT_TYPE_FORM', 'selectlabel'=>'Select',
								  'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published!=-44 AND location=\"collection\"","value":"name","orderby":"title","orderby_direction":"ASC","limit":""}',
								  'required'=>'required', 'storage_field'=>'routes_fieldname' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'size'=>24, 'storage_field'=>'routes_fieldname_ct' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_dev_texts', '', $config, array( 'label'=>'Fields', 'storage_field'=>'routes' ) );

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Target Blank=_blank||Target Self=_self||Target Parent=_parent||Target Top=_top||Advanced=optgroup||Modal Box=modal', 'storage_field'=>'target' ) );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Parameters', 'cols'=>92, 'rows'=>1, 'storage_field'=>'target_params' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>24, 'storage_field'=>'rel' ) );
		echo JCckDev::renderBlank();
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Path Paths', 'selectlabel'=>'Inherited', 'defaultvalue'=>'', 'options'=>'Absolute=1', 'storage_field'=>'path_type' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Apply=1||Prepare=0', 'storage_field'=>'state' ) );
        ?>
    </ul>
</div>

<?php
JCckDev::initScript( 'link', $this->item );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#sortable_core_dev_texts,#blank_li').isVisibleWhen('mode','0');
	$('#routes_fieldname').isVisibleWhen('mode','1');
	$('#routes_fieldname_ct').isVisibleWhen('mode','11');
	$('#target_params').isVisibleWhen('target','modal');
});
</script>