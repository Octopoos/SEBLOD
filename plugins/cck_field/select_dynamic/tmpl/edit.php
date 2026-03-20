<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Init
$html_attr	=	'';
$location	=	JCckDev::fromSTRING( $this->item->location );
$options2	=	JCckDev::fromJSON( $this->item->options2 );

if ( $count = JCck::getConfig_Param( 'development_attr', 6 ) ) {
	for ( $i = 0; $i < $count; $i++ ) {
		$html1	=	JCckDev::getForm( 'core_dev_text', @$location[$i], $config, array( 'size'=>7, 'storage_field'=>'string[location][]' ) );
		$j		=	$i;
		$j++;
		$html2	=	JCckDev::getForm( 'core_dev_text', @$options2['attr'.$j], $config, array( 'size'=>7, 'storage_field'=>'json[options2][attr'.$j.']' ) );

		$html_attr	.=	JCckDev::renderLayoutFile(
						'cck'.JCck::v().'.form.field', array(
							'label'=>JText::_( 'COM_CCK_OPTIONS_ATTR_COLUMN' ),
							'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
										'grid'=>'|50%',
										'html'=>array(
												$html1,
												$html2
										)
									) ),
							'class'=>'custom_attr_mapping'
						)
					);
	}
}

// JS
$js =	'jQuery(document).ready(function($) {
			$("#json_options2_query").isVisibleWhen("bool2","1");
			$("#json_options2_table, #json_options2_name, #json_options2_value, #json_options2_where, #json_options2_orderby, #json_options2_limit, #blank_li").isVisibleWhen("bool2","0");
			$("#rows, #divider").isVisibleWhen("bool3","1");
			$("#sortable_core_options").isVisibleWhen("bool4","1,2,3");
			$(".custom_attr_mapping").hide();
			$("#adminForm").on("click", "#custom_attr_toggle", function() {
				$(".custom_attr_mapping").toggle();
			});
		});';

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config ),
									JCckDev::renderForm( 'core_query', $this->item->bool2, $config ),
									JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config ),
									JCckDev::renderForm( 'core_options_query', @$options2['query'], $config, array(), array(), 'w100' ),
									JCckDev::renderForm( 'core_options_table', @$options2['table'], $config ),
									JCckDev::renderForm( 'core_options_name', @$options2['name'], $config ),
									JCckDev::renderForm( 'core_options_where', @$options2['where'], $config ),
									JCckDev::renderForm( 'core_options_value', @$options2['value'], $config ),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>JText::_( 'COM_CCK_ORDER_BY' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'|50%',
												'html'=>array(
													JCckDev::getForm( 'core_options_orderby', @$options2['orderby'], $config ),
													JCckDev::getForm( 'core_options_orderby_direction', @$options2['orderby_direction'], $config )
												)
											) )
										)
									),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>JText::_( 'COM_CCK_OPTIONS_ATTRIBUTES' ),
											'html'=>'<a href="javascript:void(0);" id="custom_attr_toggle"><span class="variation_value">'.JText::_( 'COM_CCK_TOGGLE' ).'</span></a>'
										)
									),
									JCckDev::renderForm( 'core_options_limit', @$options2['limit'], $config ),
									JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Multiple' ) ),
									JCckDev::renderForm( 'core_rows', $this->item->rows, $config ),
									JCckDev::renderForm( 'core_separator', $this->item->divider, $config ),
									JCckDev::renderForm( 'core_options_language_detection', @$options2['language_detection'], $config ),
									JCckDev::renderLayoutFile(
										'cck'.JCck::v().'.form.field', array(
											'label'=>JText::_( 'COM_CCK_LANGUAGE_CODES_DEFAULT' ),
											'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
												'grid'=>'|25%',
												'html'=>array(
		 											JCckDev::getForm( 'core_options_language_codes', @$options2['language_codes'], $config, array( 'size' => 21 ) ),
		 											JCckDev::getForm( 'core_options_language_default', @$options2['language_default'], $config, array( 'size' => 5 ) )
												)
											) )
										)
									),
									JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'Static Options', 'options'=>'No=0||Yes=optgroup||Above=1||Below=2||Both=3' ) ),
									JCckDev::renderBlank(),
									JCckDev::renderForm( 'core_options', JCckDev::fromSTRING( $this->item->options ), $config ),
									$html_attr
								)
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array( 'field', 'seblod-2-x-select-dynamic-field' ),
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>