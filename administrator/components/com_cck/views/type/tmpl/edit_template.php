<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_template.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( is_object( $this->style ) ) {
	$this->style->params	=	JCckDev::fromJSON( $this->style->params );
}
?>
<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_RENDERING' ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_template'], $this->style->template, $config );
			echo '<input type="hidden" name="template2" value="'.$this->style->template.'" />';
			$style_title	=	( strlen( $this->style->title ) > 32 ) ? substr( $this->style->title, 0, 32 ).'...' : $this->style->title;
			echo '<li><label>'.JText::_( 'COM_CCK_STYLE' ).'</label><span class="variation_value adminformlist-maxwidth" title="'.$this->style->title.'">'.$style_title.'</span>'
			 .	 '<input class="inputbox" type="hidden" id="template_'.$this->item->client.'" name="template_'.$this->item->client.'" value="'.$this->style->id.'" /></li>';
            ?>
        </ul>
	</div>
	<div class="seblod">
		<div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_ROOT' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( 'core_dev_text', @$this->style->params['rendering_css_class'], $config, array( 'label'=>'Class', 'size'=>'16', 'storage_field'=>'params[rendering_css_class]' ) );
			echo JCckDev::renderForm( 'core_dev_textarea', @$this->style->params['rendering_custom_attributes'], $config, array( 'label'=>'Custom Attributes', 'rows'=>'1', 'cols'=>'88', 'storage_field'=>'params[rendering_custom_attributes]' ), array(), 'w100' );
            ?>
        </ul>
	</div>
	<?php
	if ( is_object( $this->style ) ) {
	    Helper_Workshop::getTemplateParams( $this->style->xml, '//config', 'params', $this->style->params );
	}
	?>
</div>
<div class="clr"></div>