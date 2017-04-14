<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_configuration.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$P			=	'options_'.$this->item->client;
$options	=	JCckDev::fromJSON( $this->item->$P );
?>
<div class="<?php echo $this->css['wrapper']; ?>">
	<?php if ( $this->item->master == 'content' ) { ?>
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONFIG' ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_title'], @$options['title'], $config );
			echo JCckDev::renderForm( $cck['core_typo'], @$options['typo'], $config );
			echo JCckDev::renderForm( $cck['core_sef'], @$options['sef'], $config );
			?>
        </ul>
	</div>
    <?php } else { ?>
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONFIG' ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_message_style'], @$options['message_style'], $config, array( 'options'=>'None=0||Joomla=optgroup||Error=error||Message=message||Notice=notice' ) );
			if ( $this->item->client == 'site' ) {
				echo JCckDev::renderForm( $cck['core_redirection'], @$options['redirection'], $config );
			} else {
				echo JCckDev::renderForm( $cck['core_dev_select'], 'toolbar', $config, array( 'label'=>'REDIRECTION', 'options'=>'ADMIN_TOOLBAR=toolbar', 'attributes'=>'disabled="disabled"' ) );
			}
			echo JCckDev::renderForm( $cck['core_message'], @$options['message'], $config, array( 'label'=>'MESSAGE_THANKS' ) );
			if ( $this->item->client == 'site' ) {
				echo JCckDev::renderForm( $cck['core_menuitem'], @$options['redirection_itemid'], $config, array( 'label'=>'Menu Item List','storage_field'=>'options[redirection_itemid]' ) );
				echo JCckDev::renderForm( $cck['core_redirection_url'], @$options['redirection_url'], $config );
			}
            ?>
        </ul>
	</div>
	<div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_DATA_INTEGRITY' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_dev_text'], @$options['data_integrity_excluded'], $config, array( 'label'=>'Exclude Fields',
																												  'storage_field'=>'options[data_integrity_excluded]' ) );
            ?>
        </ul>
	</div>
    <?php if ( $this->item->client == 'site' ) { ?>
        <div class="seblod">
            <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_GLOBAL_FORM' ); ?></div>
            <ul class="adminformlist adminformlist-2cols">
                <?php
				echo JCckDev::renderForm( $cck['core_show_hide'], @$options['show_form_title'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_FORM_TITLE', 'storage_field'=>'options[show_form_title]' ) );
				echo '<li><label>'.JText::_( 'COM_CCK_CONFIG_TITLE_TAG_CLASS' ).'</label>'
				 .	 JCckDev::getForm( $cck['core_tag_title'], @$options['tag_form_title'], $config, array( 'storage_field'=>'options[tag_form_title]' ) )
				 .	 JCckDev::getForm( $cck['core_class_title'], @$options['class_form_title'], $config, array( 'size'=>16, 'storage_field'=>'options[class_form_title]' ) )
				 .	 '</li>';
				echo JCckDev::renderForm( $cck['core_show_hide2'], @$options['show_form_desc'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_FORM_DESCRIPTION', 'storage_field'=>'options[show_form_desc]' ) );
                ?>
            </ul>
        </div>
    <?php } ?>
	<div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_NO_ACCESS' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( 'core_message_style', @$options['message_style_no_access'], $config, array( 'defaultvalue'=>'error', 'storage_field'=>'options[message_style_no_access]' ) );
			echo JCckDev::renderForm( $cck['core_action_no_access'], @$options['action_no_access'], $config );
			echo JCckDev::renderForm( 'core_message', @$options['message_no_access'], $config, array( 'storage_field'=>'options[message_no_access]' ) );
			echo JCckDev::renderForm( $cck['core_redirection_url_no_access'], @$options['redirection_url_no_access'], $config );
            ?>
        </ul>
	</div>
	<div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_STAGES' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_stages'], @$options['stages'], $config, array( 'label'=>'Count' ) );
			echo JCckDev::renderBlank();
            ?>
        </ul>
	</div>
	<div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_VALIDATION' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_validation_position'], @$options['validation_position'], $config );
			echo JCckDev::renderForm( $cck['core_validation_scroll'], @$options['validation_scroll'], $config );
			echo JCckDev::renderForm( $cck['core_validation_color'], @$options['validation_color'], $config );
			echo JCckDev::renderForm( $cck['core_validation_background_color'], @$options['validation_background_color'], $config );
            ?>
        </ul>
	</div>
    <?php } ?>
</div>
<div class="clr"></div>