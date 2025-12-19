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

JCckDev::initScript( 'link', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_task', '', $config, array( 'selectlabel'=>'Select', 'options'=>'Task Export=export||Task Impersonate=impersonate||Task Process=process||Task Toggle=toggle', 'required'=>'required', 'storage_field'=>'task' ) );
		echo JCckDev::renderForm( 'core_task_exporter', '', $config, array( 'storage_field'=>'task_id_export' ) );
		echo JCckDev::renderForm( 'core_task_processing', '', $config, array( 'storage_field'=>'task_id_process' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Property', 'storage_field'=>'task_property_toggle' ) );

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo '<li><label>'.Text::_( 'COM_CCK_TITLE' ).'</label>'
			. JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'None', 'options'=>'Custom Text=2||Translated Text=3', 'storage_field'=>'title' ) )
			. JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Title', 'size'=>16, 'css'=>'input-medium', 'storage_field'=>'title_custom' ) )
			. '</li>';

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONFIG_NO_ACCESS' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Show Value', 'selectlabel'=>'', 'options'=>'Hide=0||Show=1', 'storage_field'=>'no_access' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#task_id_export').isVisibleWhen('task','export');
	$('#task_id_process').isVisibleWhen('task','process');
	$('#title_custom').isVisibleWhen('title','2,3',false);
	$('#task_property_toggle').isVisibleWhen('task','toggle');
});
</script>