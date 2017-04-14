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
$field1		=	JCckDev::getEmpty( array( 'name'=>'core_options', 'type'=>'textarea', 'label'=>'PrepareContent',
										  'storage'=>'dev', 'storage_field'=>'json[options2][preparecontent]', 'cols'=>'92', 'rows'=>'5' ) );
JCckDev::get( $field1, htmlspecialchars( @$options2['preparecontent'] ), $config );
$field2		=	JCckDev::getEmpty( array( 'name'=>'core_options2', 'type'=>'textarea', 'label'=>'PrepareForm',
										  'storage'=>'dev', 'storage_field'=>'json[options2][prepareform]', 'cols'=>'92', 'rows'=>'15' ) );
JCckDev::get( $field2, htmlspecialchars( @$options2['prepareform'] ), $config );
$field3		=	JCckDev::getEmpty( array( 'name'=>'core_options2', 'type'=>'textarea', 'label'=>'PrepareStore',
										  'storage'=>'dev', 'storage_field'=>'json[options2][preparestore]', 'cols'=>'92', 'rows'=>'5' ) );
JCckDev::get( $field3, htmlspecialchars( @$options2['preparestore'] ), $config );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		?>
		<li class="w100">
            <label><?php echo JText::_( $field1->label ); ?></label>
            <?php echo $field1->form; ?>
        </li>
		<li class="w100">
            <label><?php echo JText::_( $field2->label ); ?></label>
            <?php echo $field2->form; ?>
        </li>
		<li class="w100">
            <label><?php echo JText::_( $field3->label ); ?></label>
            <?php echo $field3->form; ?>
        </li>
        <?php
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>
