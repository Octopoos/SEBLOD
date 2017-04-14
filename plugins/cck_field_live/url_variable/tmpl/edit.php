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

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_variable_type', '', $config );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Multiple', 'storage_field'=>'multiple' ) );
		echo JCckDev::renderForm( 'core_dev_texts', '', $config, array( 'label'=>'Variables', 'storage_field'=>'variables' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Variable', 'storage_field'=>'variable' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Ignore NULL', 'storage_field'=>'ignore_null' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Return', 'options'=>'First=first||Last=last', 'storage_field'=>'return' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Default Value', 'storage_field'=>'default_value' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Encryption', 'selectlabel'=>'None', 'options'=>'Base64=base64', 'bool8'=>0, 'storage_field'=>'crypt' ) );
		?>
	</ul>
</div>

<?php
JCckDev::initScript( 'live', $this->item );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#sortable_core_dev_texts, #return').isVisibleWhen('multiple','1');
	$('#variable,#default_value,#crypt').isVisibleWhen('multiple','0');
});
</script>