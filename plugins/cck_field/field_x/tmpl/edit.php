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

JCckDev::forceStorage( 'custom' );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_extended', $this->item->extended, $config );
		echo JCckDev::renderForm( 'core_rows', $this->item->rows, $config, array( 'label' => 'DEFAULT', 'defaultvalue' => '1' ) );
		echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label' => 'ADD', 'defaultvalue' => '1' ) );
		echo JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config, array( 'label' => 'MAXIMUM', 'defaultvalue' => '10' ) );
		echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label' => 'DEL', 'defaultvalue' => '1' ) );
		echo JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config, array( 'label' => 'MINIMUM', 'defaultvalue' => '1' ) );
		echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label' => 'DRAG', 'defaultvalue' => '1' ) );
		
		echo JCckDev::renderHelp( 'field', 'seblod-2-x-field-x' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'TEXT' ) );
        ?>
    </ul>
</div>
