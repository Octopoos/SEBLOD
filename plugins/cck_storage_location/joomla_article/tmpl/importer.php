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
?>

<div class="seblod cck-padding-top-0 cck-padding-bottom-0">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_DEFAULT_VALUES' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_joomla_article_created_by', '', $config );
		echo JCckDev::renderForm( 'core_joomla_article_catid', '', $config );
		echo JCckDev::renderForm( 'core_joomla_article_state', '', $config );
		?>
	</ul><div class="clr"></div>
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_SETTINGS' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_bool', 1, $config, array( 'label'=>'Reordering', 'storage_field'=>'options[reordering]' ) );
		?>
	</ul><div class="clr"></div>
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_UPDATE' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Update By Key', 'defaultvalue'=>'', 'selectlabel'=>'None',
								  'options'=>'Custom SL=-1||ID=id||Alias=alias', 'storage_field'=>'options[key]' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'defaultvalue'=>'', 'storage_field'=>'options[key_fieldname]' ) );
        ?>
	</ul>
</div>

<script type="text/javascript">
(function ($){
	JCck.Dev.applyConditionalStates = function() {
		$('#options_key_fieldname').isVisibleWhen('options_key','-1');
	}
	$(document).ready(function() {
		JCck.Dev.applyConditionalStates();
	});
})(jQuery);
</script>