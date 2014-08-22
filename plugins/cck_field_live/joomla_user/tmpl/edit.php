<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'live', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'User', 'selectlabel'=>'Current', 'options'=>'', 'storage_field'=>'content' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Property', 'required'=>'required', 'storage_field'=>'property' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Excluded Values', 'storage_field'=>'excluded' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Default Value', 'storage_field'=>'default_value' ) );
		?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#excluded,#default_value').isVisibleWhen('property','access,groups');
});
</script>