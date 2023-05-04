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
?>

<div class="seblod cck-padding-top-0 cck-padding-bottom-0">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_DEFAULT_VALUES' ) ); ?>
    <div class="form-grid">
		<?php echo JCckDev::renderForm( 'core_joomla_article_created_by', '', $config ); ?>
		<div class="control-group">
			<div class="control-label">
				<label><?php echo JText::_( 'COM_CCK_CATEGORY' ); ?></label>
			</div>
			<div class="controls">
		 	<?php
				echo JCckDev::getForm( 'core_joomla_article_catid', '', $config )
				 .	 JCckDev::getForm( 'core_joomla_category_parent_id', '', $config, array( 'selectlabel'=>'No Parent', 'storage_field'=>'parent_id', 'required'=>'required' ), array( 'id'=>'params_category_parent', 'name'=>'params[category_parent]' ) );
				?>
		 	</div>
		</div>
		<?php echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'&nbsp;', 'type'=>'checkbox', 'options'=>'Allow New Categories=1', 'storage_field'=>'params[unknown_categories]' ) ); ?>
		<div class="control-group">
			<div class="control-label">
				<label><?php echo JText::_( 'COM_CCK_TAGS' ); ?></label>
			</div>
			<div class="controls">
		 		<?php
			echo JCckDev::getForm( 'core_joomla_article_tag', '', $config )
		 	.	JCckDev::getForm( 'core_joomla_article_tag', '', $config, array( 'selectlabel'=>'Select a Parent', 'storage_field'=>'params[tags_parent]' ) ); ?>
			</div>
		</div>
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'&nbsp;', 'type'=>'checkbox', 'options'=>'Allow New Tags=1', 'storage_field'=>'params[unknown_tags]' ) );
		echo JCckDev::renderForm( 'core_joomla_article_state', '', $config );
		?>
	</div>
	<div class="clr"></div>
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_SETTINGS' ) ); ?>
	<div class="control-grid">
		<?php
		echo JCckDev::renderForm( 'core_bool', 0, $config, array( 'label'=>'Reordering', 'storage_field'=>'options[reordering]' ) );
		?>
	</div>
	<div class="clr"></div>
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_UPDATE' ) ); ?>
	<div class="control-grid">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Update By Key', 'defaultvalue'=>'', 'selectlabel'=>'None',
								  'options'=>'Custom SL=-1||ID=id||Alias=alias', 'storage_field'=>'options[key]' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'defaultvalue'=>'', 'storage_field'=>'options[key_fieldname]' ) );
        ?>
	</div>
</div>

<script type="text/javascript">
(function ($){
	JCck.Dev.applyConditionalStates = function() {
		$('#options_key_fieldname').isVisibleWhen('options_key','-1');
		$('#params_category_parent').isVisibleWhen('params_unknown_categories','1',false);
		$('#params_tags_parent').isVisibleWhen('params_unknown_tags','1',false);
	}
	$(document).ready(function() {
		JCck.Dev.applyConditionalStates();
	});
})(jQuery);
</script>