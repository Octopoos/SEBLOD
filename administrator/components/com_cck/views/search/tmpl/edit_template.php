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
	<?php if ( $this->item->client != 'order' ) { ?>
        <div class="seblod">
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_RENDERING' ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
            <ul class="adminformlist adminformlist-2cols">
            	<?php
                if ( $this->item->client == 'list' ) {
					echo JCckDev::renderForm( $cck['core_template'], $this->item->template, $config, array( 'selectlabel'=>( ( isset( $this->item->template ) && $this->item->template ) ? 'Disable List Template' : 'Enable List Template' ),
						'options2'=>'{"query":"SELECT DISTINCT a.template AS value, CONCAT(b.title,\" - \",b.name) AS text FROM #__template_styles AS a LEFT JOIN #__cck_core_templates AS b ON b.name = a.template WHERE b.id AND b.published !=-44 AND b.mode=2 ORDER BY b.title"}' ) );
				} else {
					echo JCckDev::renderForm( $cck['core_template'], $this->item->template, $config );
				}
				if ( $this->item->template ) {
					echo '<input type="hidden" name="template2" value="'.$this->item->template.'" />';
					$style_title	=	( strlen( $this->style->title ) > 32 ) ? substr( $this->style->title, 0, 32 ).'...' : $this->style->title;
					echo '<li><label>'.JText::_( 'COM_CCK_STYLE' ).'</label><span class="variation_value adminformlist-maxwidth" title="'.$this->style->title.'">'.$style_title.'</span>'
					 .	 '<input class="inputbox" type="hidden" id="template_'.$this->item->client.'" name="template_'.$this->item->client.'" value="'.$this->style->id.'" /></li>';
				}
				?>
            </ul>
        </div>
		<div class="seblod">
			<div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_GLOBAL' ); ?></div>
	        <ul class="adminformlist adminformlist-2cols">
	            <?php
				echo JCckDev::renderForm( 'core_dev_textarea', @$this->style->params['rendering_item_attributes'], $config, array( 'label'=>'Item Custom Attributes', 'rows'=>'1', 'cols'=>'88', 'storage_field'=>'params[rendering_item_attributes]' ), array(), 'w100' );
				echo JCckDev::renderForm( 'core_dev_text', @$this->style->params['rendering_css_class'], $config, array( 'label'=>'Root Class', 'size'=>'16', 'storage_field'=>'params[rendering_css_class]' ) );
				echo JCckDev::renderBlank();
	            ?>
	        </ul>
        </div>
        <?php
		if ( is_object( $this->style ) ) {
	        Helper_Workshop::getTemplateParams( $this->style->xml, '//config', 'params', $this->style->params );
		}
		?>
    <?php } ?>
</div>
<div class="clr"></div>