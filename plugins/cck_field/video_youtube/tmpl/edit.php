<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
		echo JCckDev::renderForm( 'core_minlength', $this->item->minlength, $config );
        echo JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config );
        echo JCckDev::renderForm( 'core_size', $this->item->size, $config );
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_video_width', @$options2['video_width'], $config )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_options_video_height', @$options2['video_height'], $config )
		 .	 '<span class="variation_value">px</span></li>';
        echo JCckDev::renderForm( 'core_options_video_preview', @$options2['video_preview'], $config );
		echo JCckDev::renderForm( 'core_video_markup', $this->item->bool2, $config );
		
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>
