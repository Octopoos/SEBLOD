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

$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_description', $this->item->defaultvalue, $config, array( 'label'=>'Default Value', 'storage_field'=>'defaultvalue') );
		
		echo JCckDev::renderForm( 'core_options_editor', @$options2['editor'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_width', @$options2['width'], $config, array( 'defaultvalue'=>'100%' ) )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_options_height', @$options2['height'], $config, array( 'defaultvalue'=>'280', 'attributes'=>'placeholder="280"' ) )
		 .	 '<span class="variation_value">px</span></li>';
		echo JCckDev::renderForm( 'core_place', $this->item->bool, $config, array( 'label'=>'DISPLAY_MODE' ) );
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config );
		echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'Show Buttons', 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1' ) );

		echo JCckDev::renderHelp( 'field', 'seblod-2-x-wysiwyg-editor-field' );
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_PROCESSING' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC_PROCESSING' ), 2 );
		echo JCckDev::renderForm( 'core_options_import', @$options2['import'], $config );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ), 3 );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'TEXT' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#storage').on('change', function() {
        if ($(this).val() == "dev")  {
            $('#bool,#selectlabel').parent().show();
        } else {
            $('#bool,#selectlabel').parent().hide();
        }
    });
    if ($('#storage').val() == "dev")  {
        $('#bool,#selectlabel').parent().show();
    } else {
        $('#bool,#selectlabel').parent().hide();
    }
});
</script>