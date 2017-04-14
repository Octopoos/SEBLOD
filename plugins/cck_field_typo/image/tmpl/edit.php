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

JCckDev::initScript( 'typo', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_TYPO_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Display as Default', 'defaultvalue'=>'thumb1',
																   'options'=>'Image=value||Thumb1=thumb1||Thumb2=thumb2||Thumb3=thumb3||Thumb4=thumb4||Thumb5=thumb5||Thumb6=thumb6||Thumb7=thumb7||Thumb8=thumb8||Thumb9=thumb9||Thumb10=thumb10', 'storage_field'=>'thumb' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'Auto', 'options'=>'Fixed=1', 'storage_field'=>'thumb_custom' ) )
		 .	 '<div id="thumb_width_custom">'
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'size'=>3, 'required'=>'required', 'storage_field'=>'thumb_width', 'attributes'=>'style="text-align:center"' ) )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'size'=>3, 'required'=>'required', 'storage_field'=>'thumb_height', 'attributes'=>'style="text-align:center"' ) )
		 .	 '<span class="variation_value">px</span>'
		 .	 '</div></li>';
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Display as Default 2x', 'defaultvalue'=>'', 'selectlabel'=>'None',
																   'options'=>'Image=value||Thumb1=thumb1||Thumb2=thumb2||Thumb3=thumb3||Thumb4=thumb4||Thumb5=thumb5||Thumb6=thumb6||Thumb7=thumb7||Thumb8=thumb8||Thumb9=thumb9||Thumb10=thumb10', 'storage_field'=>'thumb_2x' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Display as Default 3x', 'defaultvalue'=>'', 'selectlabel'=>'None',
																   'options'=>'Image=value||Thumb1=thumb1||Thumb2=thumb2||Thumb3=thumb3||Thumb4=thumb4||Thumb5=thumb5||Thumb6=thumb6||Thumb7=thumb7||Thumb8=thumb8||Thumb9=thumb9||Thumb10=thumb10', 'storage_field'=>'thumb_3x' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Modal Box', 'defaultvalue'=>'none',
																   'options'=>'Image=value||Thumb1=thumb1||Thumb2=thumb2||Thumb3=thumb3||Thumb4=thumb4||Thumb5=thumb5||Thumb6=thumb6||Thumb7=thumb7||Thumb8=thumb8||Thumb9=thumb9||Thumb10=thumb10||None=none', 
																   'storage_field'=>'image' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'Auto', 'options'=>'Fixed=1||Inner=2||Max=3', 'storage_field'=>'image_custom' ) )
		 .	 '<div id="image_width_custom">'
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'size'=>3, 'required'=>'required', 'storage_field'=>'image_width', 'attributes'=>'style="text-align:center"' ) )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'size'=>3, 'required'=>'required', 'storage_field'=>'image_height', 'attributes'=>'style="text-align:center"' ) )
		 .	 '<span class="variation_value">px</span>'
		 .	 '</div></li>';
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Image Alt Field',  'defaultvalue'=>'', 'storage_field'=>'image_alt_fieldname' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Image Title', 'defaultvalue'=>'1', 'selectlabel'=>'', 'options'=>'None=0||Auto=1', 'storage_field'=>'image_title' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Path Paths', 'selectlabel'=>'', 'defaultvalue'=>0, 'options'=>'Absolute=1||Relative=0',
																		 'storage_field'=>'path_type' ) );
        
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#thumb_width_custom').isVisibleWhen('thumb_custom','1',false);
	$('#image_width_custom').isVisibleWhen('image_custom','1,2,3',false);
});
</script>