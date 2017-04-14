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
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue_textarea', $this->item->defaultvalue, $config );
        echo JCckDev::renderForm( 'core_columns', $this->item->cols, $config );
        echo JCckDev::renderForm( 'core_rows', $this->item->rows, $config, array( 'defaultvalue'=>'3' ) );
		echo JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config );
        echo JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config );
        echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'Characters Remaining', 'defaultvalue'=>'0', 'options'=>'Show=1||Hide=0' ) );
		
        echo JCckDev::renderHelp( 'field', 'seblod-2-x-textarea-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_PROCESSING' ), JText::_( 'COM_CCK_PROCESSING_DESC_TEXTAREA' ), 2 );
        echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'New Lines', 'options'=>'tag_br=0||tag_br_in_p=2||tag_p=1' ) );
        echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Clear Blank Lines' ) );

        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value'=>'TEXT' ) );
        ?>
    </ul>
</div>