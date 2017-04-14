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
		echo JCckDev::renderForm( 'core_form', $this->item->extended, $config, array( 'label'=>'CONTENT_TYPE_FORM', 'selectlabel'=>'Select',
							'options2'=>'{"query":"","table":"#__cck_core_types","name":"title","where":"published!=-44","value":"name","orderby":"title","orderby_direction":"ASC","limit":""}',
							'required'=>'required', 'storage_field'=>'extended' ) );
		echo JCckDev::renderForm( 'core_rows', $this->item->rows, $config, array( 'label'=>'DEFAULT', 'defaultvalue'=>'1' ) );
		echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'ADD', 'options'=>'No=0||Yes=1||Above=2||Below=3||Both=4', 'defaultvalue'=>'1' ) );
		echo JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config, array( 'label'=>'MAXIMUM', 'defaultvalue'=>'10' ) );
		echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'DEL', 'defaultvalue'=>'1' ) );
		echo JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config, array( 'label'=>'MINIMUM', 'defaultvalue'=>'1' ) );
		echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'DRAG', 'defaultvalue'=>'1' ) );
		echo JCckDev::renderForm( 'core_orientation', $this->item->bool, $config, array( 'defaultvalue'=>'1', 'options'=>'Horizontal=0||Vertical=1||Table=2' ) );
		
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'TEXT' ) );
        ?>
    </ul>
</div>