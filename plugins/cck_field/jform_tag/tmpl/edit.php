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

$options2   =   JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_dev_select', @$options2['mode'], $config, array( 'label'=>'Mode', 'selectlabel'=>'Use Global', 'options'=>'Ajax=ajax||Nested=nested', 'storage_field'=>'json[options2][mode]' ) );
        echo JCckDev::renderForm( 'core_dev_bool', @$options2['parent'], $config, array( 'label'=>'Parent', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'json[options2][parent]' ) );
        echo JCckDev::renderForm( 'core_dev_bool', @$options2['custom'], $config, array( 'label'=>'Allow Submissions', 'options'=>'Yes=1||No=0', 'storage_field'=>'json[options2][custom]' ) );
        echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Multiple' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#bool3').isVisibleWhen('json_options2_parent','0');
});
</script>