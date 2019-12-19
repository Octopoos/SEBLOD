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

$options2   =   JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
        echo '<li><label>'.JText::_( 'Show Time' ).'</label>'
         .   JCckDev::getForm( 'core_options_time', @$options2['time'], $config, array( 'label'=>'Show Time', 'defaultvalue'=>'24', 'options'=>'Hide=0||Show=optgroup||12AM=12||24H=24' ) )
         .   JCckDev::getForm( 'core_dev_select', @$options2['modify'], $config, array( 'defaultvalue'=>'', 'selectlabel'=>'__', 'options'=>'Modify Plus One Day Alt=+23 hours +59 minutes +59 seconds||Modify Plus One Day=+1 day', 'storage_field'=>'json[options2][modify]' ) )
         .   '</li>';
        echo JCckDev::renderForm( 'core_dev_select', @$options2['format'], $config, array( 'label'=>'Format', 'selectlabel'=>'', 'defaultvalue'=>'translate', 'options'=>'International=international||Use JText=translate', 'storage_field'=>'json[options2][format]' ) );
        echo JCckDev::renderForm( 'core_options_today', @$options2['today'], $config, array( 'label'=>'Show Today', 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1' ) );
        echo JCckDev::renderForm( 'core_dev_select', @$options2['format_filter'], $config, array( 'label'=>'Storage Format', 'selectlabel'=>'Select', 'defaultvalue'=>'', 'options'=>'Raw=raw||UTC=optgroup||Server Time Zone=server_utc||User Time Zone=user_utc', 'required'=>'required', 'storage_field'=>'json[options2][format_filter]' ) );
        echo JCckDev::renderForm( 'core_options_week_numbers', @$options2['week_numbers'], $config );

        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'DATETIME' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#json_options2_modify').isVisibleWhen('json_options2_time','0',false);
});
</script>