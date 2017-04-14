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

JCckDev::initScript( 'typo', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_TYPO_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Heading', 'defaultvalue'=>3, 'options'=>'H1=1||H2=2||H3=3||H4=4||H5=5||H6=6', 'selectlabel'=>'', 'storage_field'=>'rank' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Enable Anchor', 'defaultvalue'=>0,  'storage_field'=>'anchor' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom Attributes', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Auto=0||Typo Label=1', 'storage_field'=>'typo_label' ) );
		echo JCckDev::renderBlank();
        ?>
    </ul>
</div>