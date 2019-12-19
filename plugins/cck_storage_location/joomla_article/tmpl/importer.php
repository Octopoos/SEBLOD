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
    <ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_joomla_article_created_by', '', $config );
		echo '<li><label>'.JText::_( 'COM_CCK_CATEGORY' ).'</label>'
		 .	 JCckDev::getForm( 'core_joomla_article_catid', '', $config )
		 .	 JCckDev::getForm( 'core_joomla_category_parent_id', '', $config, array( 'selectlabel'=>'No Parent', 'storage_field'=>'parent_id', 'required'=>'required' ), array( 'id'=>'params_category_parent', 'name'=>'params[category_parent]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'&nbsp;', 'type'=>'checkbox', 'options'=>'Allow New Categories=1', 'storage_field'=>'params[unknown_categories]' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_TAGS' ).'</label>'
		 .	 JCckDev::getForm( 'core_joomla_article_tag', '', $config )
		 .	 JCckDev::getForm( 'core_joomla_article_tag', '', $config, array( 'selectlabel'=>'Select a Parent', 'storage_field'=>'params[tags_parent]', 'required'=>'required' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'&nbsp;', 'type'=>'checkbox', 'options'=>'Allow New Tags=1', 'storage_field'=>'params[unknown_tags]' ) );
		echo JCckDev::renderForm( 'core_joomla_article_state', '', $config );
		?>
	</ul><div class="clr"></div>
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_SETTINGS' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_bool', 0, $config, array( 'label'=>'Reordering', 'storage_field'=>'options[reordering]' ) );
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
		$('#params_category_parent').isVisibleWhen('params_unknown_categories','1',false);
		$('#params_tags_parent').isVisibleWhen('params_unknown_tags','1',false);
	}
	$(document).ready(function() {
		JCck.Dev.applyConditionalStates();
	});
})(jQuery);
</script>