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

$options	=	JCckDev::fromJSON( $this->item->options );
?>
<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONFIG' ). '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_ALL' ).')</span>'; ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            echo '<li><label>'.JText::_( 'COM_CCK_LIMIT' ).' / '.JText::_( 'COM_CCK_COUNT' ).'</label>'
             .   JCckDev::getForm( $cck['core_limit'], @$options['limit'], $config )
             .   JCckDev::getForm( 'core_dev_select', @$options['count'], $config, array( 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'Auto=0||Estimate=1', 'storage_field'=>'options[count]' ) )
             .   '</li>';
            echo '<li><label>'.JText::_( 'COM_CCK_CACHE_SEARCH' ).'</label>'
             .   JCckDev::getForm( $cck['core_cache'], @$options['cache'], $config )
             .   JCckDev::getForm( 'core_dev_select', @$options['cache_per_user'], $config, array( 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'For Anyone=0||Per User=1', 'storage_field'=>'options[cache_per_user]' )  )
             .   '</li>';
            echo '<li><label>'.JText::_( 'COM_CCK_OPTIONAL_STAGES' ).'</label>'
             .   JCckDev::getForm( 'core_dev_text', @$options['stages_optional'], $config, array( 'label'=>'', 'size'=>8, 'storage_field'=>'options[stages_optional]' ) )
             .   '</li>';
			echo JCckDev::renderForm( $cck['core_cache2'], @$options['cache2'], $config, array( 'label'=>'CACHE_RENDER' ) );
			echo JCckDev::renderForm( $cck['core_pagination'], @$options['pagination'], $config );
			echo JCckDev::renderForm( $cck['core_debug'], @$options['debug'], $config );
			echo JCckDev::renderForm( $cck['core_sef'], @$options['sef'], $config );
			echo JCckDev::renderForm( 'core_dev_select', @$options['persistent_query'], $config, array( 'label'=>'Persistent Search', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=optgroup||Yes for Everyone=1||Registered=2', 'storage_field'=>'options[persistent_query]' ) );
            echo JCckDev::renderForm( 'core_dev_text', $this->item->sef_route, $config, array( 'label'=>'SEF Helper', 'storage_field'=>'sef_route' ) );
            echo JCckDev::renderForm( $cck['core_prepare_content'], @$options['prepare_content'], $config );
            ?>
        </ul>
	</div>
	<div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_GLOBAL_LIST' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
			<?php
            echo JCckDev::renderForm( $cck['core_auto_redirection'], @$options['auto_redirect'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_AUTO_REDIRECTION', 'storage_field'=>'options[auto_redirect]' ) );
            echo JCckDev::renderForm( $cck['core_ordering'], @$options['ordering'], $config, array( 'label'=>'CONFIG_ORDERING', 'selectlabel'=>'', 'storage_field'=>'options[ordering]' ) );
            echo JCckDev::renderForm( 'core_show_hide', @$options['show_list_title'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_LIST_TITLE', 'storage_field'=>'options[show_list_title]' ) );
            echo '<li><label>'.JText::_( 'COM_CCK_CONFIG_TITLE_TAG_CLASS' ).'</label>'
             .	 JCckDev::getForm( $cck['core_tag_title'], @$options['tag_list_title'], $config, array( 'storage_field'=>'options[tag_list_title]' ) )
             .	 JCckDev::getForm( $cck['core_class_title'], @$options['class_list_title'], $config, array( 'size'=>16, 'storage_field'=>'options[class_list_title]' ) )
             .	 '</li>';
            echo JCckDev::renderForm( $cck['core_show_hide2'], @$options['show_list_desc'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_LIST_DESCRIPTION', 'storage_field'=>'options[show_list_desc]' ) );
            echo JCckDev::renderBlank();
            echo JCckDev::renderForm( 'core_show_hide', @$options['show_list'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_SEARCH_LIST', 'storage_field'=>'options[show_list]' ) );
            echo JCckDev::renderForm( 'core_show_hide2', @$options['show_form'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_SEARCH_FORM', 'options'=>'Hide=0||Prepare=-1||Show=optgroup||Above=1||Below=2', 'storage_field'=>'options[show_form]' ) );
            echo JCckDev::renderForm( 'core_show_hide', @$options['show_items_number'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_SHOW_ITEMS_NUMBER', 'storage_field'=>'options[show_items_number]' ) );
            echo '<li><label>'.JText::_( 'COM_CCK_CONFIG_ITEMS_NUMBER_LABEL_CLASS' ).'</label>'
             .	 JCckDev::getForm( $cck['core_label_total'], @$options['label_items_number'], $config, array( 'storage_field'=>'options[label_items_number]' ) )
             .	 JCckDev::getForm( $cck['core_class_total'], @$options['class_items_number'], $config, array( 'size'=>16, 'storage_field'=>'options[class_items_number]' ) )
             .	 '</li>';
            echo JCckDev::renderForm( 'core_show_hide', @$options['show_pages_number'], $config, array( 'defaultvalue'=>'0', 'label'=>'CONFIG_SHOW_PAGES_NUMBER', 'storage_field'=>'options[show_pages_number]' ) );
            echo JCckDev::renderForm( $cck['core_show_pagination'], @$options['show_pagination'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_SHOW_PAGINATION', 'storage_field'=>'options[show_pagination]' ) );
            echo JCckDev::renderForm( $cck['core_class_pagination'], @$options['class_pagination'], $config, array( 'label'=>'CONFIG_PAGINATION_CLASS', 'size'=>16, 'storage_field'=>'options[class_pagination]' ) );
            echo JCckDev::renderForm( 'core_dev_text', @$options['label_pagination'], $config, array( 'label'=>'Config Pagination Label', 'size'=>32, 'storage_field'=>'options[label_pagination]' ) );
            echo JCckDev::renderForm( 'core_dev_text', @$options['callback_pagination'], $config, array( 'label'=>'Config Pagination Callback', 'storage_field'=>'options[callback_pagination]' ) );
            ?>
        </ul>
	</div>
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
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_NO_RESULT' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
			echo JCckDev::renderForm( $cck['core_message_style'], @$options['message_style'], $config );
			echo JCckDev::renderForm( $cck['core_action'], @$options['action'], $config );
			echo JCckDev::renderForm( $cck['core_message'], @$options['message'], $config );
            echo JCckDev::renderForm( 'core_show_hide', @$options['show_list_desc_no_result'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_LIST_DESCRIPTION', 'storage_field'=>'options[show_list_desc_no_result]' ) );
            ?>
        </ul>
	</div>
    <div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_CONFIG_NO_SEARCH' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            echo JCckDev::renderForm( 'core_message_style', @$options['message_style_no_search'], $config, array( 'defaultvalue'=>'0', 'storage_field'=>'options[message_style_no_search]' ) );
            echo JCckDev::renderForm( 'core_action', @$options['action_no_search'], $config, array( 'storage_field'=>'options[action_no_search]' ) );
            echo JCckDev::renderForm( 'core_message', @$options['message_no_search'], $config, array( 'storage_field'=>'options[message_no_search]' ) );
            echo JCckDev::renderBlank();            
            ?>
        </ul>
    </div>
    <div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_RESOURCE_AS_FRAGMENT_LEGEND' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            echo JCckDev::renderForm( 'core_bool', @$options['load_resource'], $config, array( 'defaultvalue'=>'0', 'label'=>'Enable Loading', 'storage_field'=>'options[load_resource]' ) );
            echo JCckDev::renderBlank( '<input type="hidden" id="blank_li7" value="" />' );
            echo JCckDev::renderForm( 'core_dev_select', @$options['tmpl_resource'], $config, array( 'defaultvalue'=>'', 'label'=>'Tmpl', 'selectlabel'=>'None', 'options'=>'Component=component||Raw=raw', 'storage_field'=>'options[tmpl_resource]' ) );
            echo JCckDev::renderForm( 'core_dev_textarea', @$options['json_resource'], $config, array( 'label'=>'Parameters', 'cols'=>80, 'rows'=>1, 'storage_field'=>'options[json_resource]' ), array(), 'w100' );
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
</div>
<div class="clr"></div>