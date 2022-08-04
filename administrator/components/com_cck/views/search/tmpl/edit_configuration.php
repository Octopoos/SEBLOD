<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_configuration.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$options	=	JCckDev::fromJSON( $this->item->options );
?>
<div class="<?php echo $this->css['wrapper']; ?>">
	<?php
	echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.common.edit_fieldset', array(
		'form'=>array(
			array(
				'fields'=>array(
					JCckDev::renderLayoutFile(
						'cck'.JCck::v().'.form.field', array(
							'label'=>JText::_( 'COM_CCK_LIMIT' ).' / '.JText::_( 'COM_CCK_COUNT' ),
							'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
								'grid'=>'30%|',
								'html'=>array(
									JCckDev::getForm( $cck['core_limit'], @$options['limit'], $config ),
									JCckDev::getForm( 'core_dev_select', @$options['count'], $config, array( 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'Auto=0||Estimate=1', 'storage_field'=>'options[count]' ) )
								)
							) )
						)
					),
					JCckDev::renderLayoutFile(
						'cck'.JCck::v().'.form.field', array(
							'label'=>JText::_( 'COM_CCK_CACHE_SEARCH' ),
							'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
								'grid'=>'30%|',
								'html'=>array(
									JCckDev::getForm( $cck['core_cache'], @$options['cache'], $config ),
									JCckDev::getForm( 'core_dev_select', @$options['cache_per_user'], $config, array( 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'For Anyone=0||Per User=1', 'storage_field'=>'options[cache_per_user]' )  )
								)
							) )
						)
					),
					JCckDev::renderLayoutFile(
						'cck'.JCck::v().'.form.field', array(
							'label'=>JText::_( 'COM_CCK_OPTIONAL_STAGES' ),
							'html'=>JCckDev::getForm( 'core_dev_text', @$options['stages_optional'], $config, array( 'label'=>'', 'size'=>8, 'storage_field'=>'options[stages_optional]' ) )
						)
					),
					JCckDev::renderForm( $cck['core_cache2'], @$options['cache2'], $config, array( 'label'=>'CACHE_RENDER' ) ),
					JCckDev::renderForm( $cck['core_pagination'], @$options['pagination'], $config ),
					JCckDev::renderForm( $cck['core_debug'], @$options['debug'], $config ),
					JCckDev::renderForm( 'core_dev_select', @$options['persistent_query'], $config, array( 'label'=>'Persistent Search', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=optgroup||Yes for Everyone=1||Registered=2', 'storage_field'=>'options[persistent_query]' ) ),
					JCckDev::renderForm( $cck['core_prepare_content'], @$options['prepare_content'], $config )
				),
				'legend'=>JText::_( 'COM_CCK_CONFIG' ). '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_ALL' ).')</span>'
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( $cck['core_sef'], @$options['sef'], $config ),
					JCckDev::renderForm( 'core_dev_text', $this->item->sef_route, $config, array( 'label'=>'SEF Helper', 'storage_field'=>'sef_route' ) ),
					JCckDev::renderForm( $cck['core_sef_canonical'], @$options['sef_canonical'], $config ),
					JCckDev::renderForm( 'core_dev_select', $this->item->sef_route_aliases, $config, array( 'label'=>'SEF Multi Aliases', 'selectlabel'=>'', 'defaultvalue'=>'-1', 'options'=>'Use Global SL=-1||No=0||Yes=optgroup||All languages=2||All languages but default=1', 'storage_field'=>'sef_route_aliases' ) )
				),
				'legend'=>JText::_( 'COM_CCK_CONFIG' ).JText::_( 'COM_CCK_PAIR_KEY_VALUE_SEPARATOR' ).JText::_( 'COM_CCK_SEO' ). '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_ALL' ).')</span>'
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( $cck['core_auto_redirection'], @$options['auto_redirect'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_AUTO_REDIRECTION', 'storage_field'=>'options[auto_redirect]' ) ),
					JCckDev::renderForm( $cck['core_ordering'], @$options['ordering'], $config, array( 'label'=>'CONFIG_ORDERING', 'selectlabel'=>'', 'storage_field'=>'options[ordering]' ) ),
					JCckDev::renderForm( 'core_show_hide', @$options['show_list_title'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_LIST_TITLE', 'storage_field'=>'options[show_list_title]' ) ),
					JCckDev::renderLayoutFile(
						'cck'.JCck::v().'.form.field', array(
							'label'=>JText::_( 'COM_CCK_CONFIG_TITLE_TAG_CLASS' ),
							'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
								'grid'=>'30%|',
								'html'=>array(
									JCckDev::getForm( $cck['core_tag_title'], @$options['tag_list_title'], $config, array( 'storage_field'=>'options[tag_list_title]' ) ),
									JCckDev::getForm( $cck['core_class_title'], @$options['class_list_title'], $config, array( 'size'=>16, 'storage_field'=>'options[class_list_title]' ) )
								)
							) )
						)
					),
					JCckDev::renderForm( $cck['core_show_hide2'], @$options['show_list_desc'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_LIST_DESCRIPTION', 'storage_field'=>'options[show_list_desc]' ) ),
					JCckDev::renderBlank(),
					JCckDev::renderForm( 'core_show_hide', @$options['show_list'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_SEARCH_LIST', 'storage_field'=>'options[show_list]' ) ),
					JCckDev::renderForm( 'core_show_hide2', @$options['show_form'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_SEARCH_FORM', 'options'=>'Hide=0||Prepare=-1||Show=optgroup||Above=1||Below=2', 'storage_field'=>'options[show_form]' ) ),
					JCckDev::renderForm( 'core_show_hide', @$options['show_items_number'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_SHOW_ITEMS_NUMBER', 'storage_field'=>'options[show_items_number]' ) ),
					JCckDev::renderLayoutFile(
						'cck'.JCck::v().'.form.field', array(
							'label'=>JText::_( 'COM_CCK_CONFIG_ITEMS_NUMBER_LABEL_CLASS' ),
							'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
								'grid'=>'30%|',
								'html'=>array(
									JCckDev::getForm( $cck['core_label_total'], @$options['label_items_number'], $config, array( 'storage_field'=>'options[label_items_number]' ) ),
									JCckDev::getForm( $cck['core_class_total'], @$options['class_items_number'], $config, array( 'size'=>16, 'storage_field'=>'options[class_items_number]' ) )
								)
							) )
						)
					),
					JCckDev::renderForm( 'core_show_hide', @$options['show_pages_number'], $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'label'=>'CONFIG_SHOW_PAGES_NUMBER', 'storage_field'=>'options[show_pages_number]' ) ),
					JCckDev::renderForm( $cck['core_show_pagination'], @$options['show_pagination'], $config, array( 'defaultvalue'=>0, 'label'=>'CONFIG_SHOW_PAGINATION', 'storage_field'=>'options[show_pagination]' ) ),
					JCckDev::renderForm( $cck['core_class_pagination'], @$options['class_pagination'], $config, array( 'label'=>'CONFIG_PAGINATION_CLASS', 'size'=>16, 'storage_field'=>'options[class_pagination]' ) ),
					JCckDev::renderForm( 'core_dev_text', @$options['label_pagination'], $config, array( 'label'=>'Config Pagination Label', 'size'=>32, 'storage_field'=>'options[label_pagination]' ) ),
					JCckDev::renderForm( 'core_dev_text', @$options['callback_pagination'], $config, array( 'label'=>'Config Pagination Callback', 'storage_field'=>'options[callback_pagination]' ) )
				),
				'legend'=>'&rArr; ' . JText::_( 'COM_CCK_CONFIG_GLOBAL_LIST' )
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( 'core_message_style', @$options['message_style_no_access'], $config, array( 'defaultvalue'=>'error', 'storage_field'=>'options[message_style_no_access]' ) ),
					JCckDev::renderForm( $cck['core_action_no_access'], @$options['action_no_access'], $config ),
					JCckDev::renderForm( 'core_message', @$options['message_no_access'], $config, array( 'storage_field'=>'options[message_no_access]' ) ),
					JCckDev::renderForm( $cck['core_redirection_url_no_access'], @$options['redirection_url_no_access'], $config )
				),
				'legend'=>'&rArr; ' . JText::_( 'COM_CCK_CONFIG_NO_ACCESS' )
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( $cck['core_message_style'], @$options['message_style'], $config, array( 'defaultvalue'=>'0' ) ),
					JCckDev::renderForm( $cck['core_action'], @$options['action'], $config, array( 'defaultvalue'=>'file' ) ),
					JCckDev::renderForm( $cck['core_message'], @$options['message'], $config ),
					JCckDev::renderForm( 'core_show_hide', @$options['show_list_desc_no_result'], $config, array( 'defaultvalue'=>1, 'label'=>'CONFIG_SHOW_LIST_DESCRIPTION', 'storage_field'=>'options[show_list_desc_no_result]' ) ),
					JCckDev::renderForm( 'core_dev_bool', @$options['mode_no_result'], $config, array( 'label'=>'Trigger', 'defaultvalue'=>'0', 'options'=>'Config No Result=0||Config No Unique Result=1', 'storage_field'=>'options[mode_no_result]' ) )
				),
				'legend'=>'&rArr; ' . JText::_( 'COM_CCK_CONFIG_NO_RESULT' )
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( 'core_message_style', @$options['message_style_no_search'], $config, array( 'defaultvalue'=>'0', 'storage_field'=>'options[message_style_no_search]' ) ),
					JCckDev::renderForm( 'core_action', @$options['action_no_search'], $config, array( 'storage_field'=>'options[action_no_search]' ) ),
					JCckDev::renderForm( 'core_message', @$options['message_no_search'], $config, array( 'storage_field'=>'options[message_no_search]' ) ),
					JCckDev::renderBlank()
				),
				'legend'=>'&rArr; ' . JText::_( 'COM_CCK_CONFIG_NO_SEARCH' )
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( 'core_bool', @$options['load_resource'], $config, array( 'defaultvalue'=>'0', 'label'=>'Enable Loading', 'storage_field'=>'options[load_resource]' ) ),
					JCckDev::renderBlank( '<input type="hidden" id="blank_li7" value="" />' ),
					JCckDev::renderForm( 'core_dev_select', @$options['tmpl_resource'], $config, array( 'defaultvalue'=>'', 'label'=>'Tmpl', 'selectlabel'=>'None', 'options'=>'Component=component||Raw=raw', 'storage_field'=>'options[tmpl_resource]' ) ),
					JCckDev::renderForm( 'core_dev_textarea', @$options['json_resource'], $config, array( 'label'=>'Parameters', 'cols'=>80, 'rows'=>1, 'storage_field'=>'options[json_resource]' ), array(), 'w100' ),
					JCckDev::renderForm( 'core_dev_text', @$options['autoid_resource'], $config, array( 'defaultvalue'=>'', 'label'=>'Auto ItemId', 'storage_field'=>'options[autoid_resource]' ) )
				),
				'legend'=>'&rArr; ' . JText::_( 'COM_CCK_RESOURCE_AS_FRAGMENT_LEGEND' )
			),
			array(
				'fields'=>array(
					JCckDev::renderForm( $cck['core_validation_position'], @$options['validation_position'], $config ),
					JCckDev::renderForm( $cck['core_validation_scroll'], @$options['validation_scroll'], $config ),
					JCckDev::renderForm( $cck['core_validation_color'], @$options['validation_color'], $config ),
					JCckDev::renderForm( $cck['core_validation_background_color'], @$options['validation_background_color'], $config )
				),
				'legend'=>'&rArr; ' . JText::_( 'COM_CCK_CONFIG_VALIDATION' )
			)
		)
	) );
	?>
</div>
<div class="clr"></div>