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
		echo JCckDev::renderBlank();
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_LINK' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_class', @$options2['link_label'], $config, array( 'defaultvalue'=>'Link', 'size'=>'32', 'storage_field'=>'json[options2][link_label]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_defaultvalue', @$options2['def_link'], $config, array( 'label'=>'Default Link', 'storage_field'=>'json[options2][def_link]' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_TEXT' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool2', $this->item->bool2, $config, array( 'defaultvalue'=>'1', 'options'=>'Hide=>0||Show=1' ) )
		 .	 JCckDev::getForm( 'core_options_class', @$options2['text_label'], $config, array( 'defaultvalue'=>'Text', 'size'=>'18', 'storage_field'=>'json[options2][text_label]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_defaultvalue', @$options2['def_text'], $config, array( 'label'=>'DEFAULT_TEXT', 'storage_field'=>'json[options2][def_text]' ) );
		echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'SHOW_TARGET', 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1' ) );
		echo JCckDev::renderForm( 'core_options_target', @$options2['target'], $config, array( 'label'=>'DEFAULT_TARGET' ) );
		echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'SHOW_CLASS', 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1' ) );
		echo JCckDev::renderForm( 'core_options_class', @$options2['class'], $config );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool6, $config, array( 'label'=>'SHOW_REL', 'defaultvalue'=>'0', 'options'=>'Hide=0', 'storage_field'=>'bool6' ) );
		echo JCckDev::renderForm( 'core_options_class', @$options2['rel'], $config, array( 'label'=>'Default Rel', 'defaultvalue'=>'', 'storage_field'=>'json[options2][rel]' ) );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool5, $config, array( 'label'=>'SHOW_PREVIEW', 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1', 'storage_field'=>'bool5' ) );
        echo JCckDev::renderForm( 'core_size', $this->item->size, $config );
        echo JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config );
        echo JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value'=>'VARCHAR(512)' ) );
        ?>
    </ul>
</div>
