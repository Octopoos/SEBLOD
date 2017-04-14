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

JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true, 'doTranslation'=>1, 'customAttr'=>JCck::getConfig_Param( 'development_attr', 6 ) ) );
$options	=	JCckDev::fromSTRING( $this->item->options );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
        echo JCckDev::renderForm( 'core_sorting', $this->item->sorting, $config );
		echo JCckDev::renderForm( 'core_rows', $this->item->rows, $config );
		echo JCckDev::renderForm( 'core_options', $options, $config );
        echo JCckDev::renderForm( 'core_separator', $this->item->divider, $config );
		
		echo JCckDev::renderHelp( 'field', 'seblod-2-x-select-multiple-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
	</ul>
</div>
