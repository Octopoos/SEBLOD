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

$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );

		echo JCckDev::renderForm( 'core_options_math', @$options2['math'], $config );
        echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config );
		echo JCckDev::renderForm( 'core_options_start', @$options2['start'], $config );
		echo JCckDev::renderForm( 'core_options_first', @$options2['first'], $config );
		echo JCckDev::renderForm( 'core_options_step', @$options2['step'], $config );
		echo JCckDev::renderForm( 'core_options_last', @$options2['last'], $config );
		echo JCckDev::renderForm( 'core_options_end', @$options2['end'], $config );
		echo JCckDev::renderForm( 'core_dev_select', @$options2['force_digits'], $config, array( 'label'=>'Force Digits', 'defaultvalue'=>'0', 'selectlabel'=>'',
																								 'options'=>'No=0||2 Digits=2||3 Digits=3||4 Digits=4||5 Digits=5', 'storage_field'=>'json[options2][force_digits]' ) );
		
		echo JCckDev::renderHelp( 'field', 'seblod-2-x-select-numeric-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'INT(11)' ) );
        ?>
	</ul>
</div>