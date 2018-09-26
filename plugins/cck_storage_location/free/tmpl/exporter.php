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
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_FIELDS' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Core Table', 'selectlabel'=>'', 'options'=>'Raw Output=optgroup||All Fields=0||Raw Prepared Output=optgroup||No Fields or from List=-1', 'storage_field'=>'columns[core]' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'defaultvalue'=>'', 'label'=>'Custom Table', 'required'=>'required', 'storage_field'=>'options[table]' ) );
		?>
	</ul>
</div>