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
        echo JCckDev::renderForm( 'core_dev_text', @$options2['extension'], $config, array( 'label'=>'Extension', 'storage_field'=>'json[options2][extension]' ) );
        echo JCckDev::renderForm( 'core_place', $this->item->bool, $config, array( 'label'=>'Display Mode' ) );
        echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
        echo JCckDev::renderForm( 'core_dev_text', @$options2['section'], $config, array( 'label'=>'Section', 'storage_field'=>'json[options2][section]' ) );
				
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#storage').on('change', function() {
        if ($(this).val() == "dev")  {
            $('#bool').parent().show();
            $('#blank_li').parent().hide();
        } else {
            $('#bool').parent().hide();
            $('#blank_li').parent().show();
        }
    });
    if ($('#storage').val() == "dev")  {
        $('#bool').parent().show();
        $('#blank_li').parent().hide();
    } else {
        $('#bool').parent().hide();
        $('#blank_li').parent().show();
    }
});
</script>