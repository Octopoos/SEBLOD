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

JCckDev::forceStorage();
$options    =   JCckDev::fromSTRING( $this->item->options );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'MODE', 'options'=>'NameTitle=1||Position=0' ) );
        //echo JCckDev::renderForm( 'core_bool', $this->item->bool7, $config, array( 'label'=>'Show Form', 'options'=>'Hide=0||Show=optgroup||Yes No=1||Modules=2' ) );
		echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config, array( 'label'=>'NAMETITLE_OR_POSITION' ) );
        //echo JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Modules' ) );
		echo JCckDev::renderForm( 'core_module_style', $this->item->style, $config );
        echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'Prepare Content', 'defaultvalue'=>'0' ) );
		
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
	</ul>
</div>
