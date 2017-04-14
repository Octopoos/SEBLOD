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

$count		=	JCck::getConfig_Param( 'development_attr', 6 );
$location	=	JCckDev::fromSTRING( $this->item->location );
$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_query', $this->item->bool2, $config );
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config );
		// 1
		echo JCckDev::renderForm( 'core_options_query', @$options2['query'], $config, array(), array(), 'w100' );
		// 2
		echo JCckDev::renderForm( 'core_options_table', @$options2['table'], $config );
		echo JCckDev::renderForm( 'core_options_name', @$options2['name'], $config );
		echo JCckDev::renderForm( 'core_options_where', @$options2['where'], $config );
		echo JCckDev::renderForm( 'core_options_value', @$options2['value'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_ORDER_BY' ).'</label>'
		.	 JCckDev::getForm( 'core_options_orderby', @$options2['orderby'], $config )
		.	 JCckDev::getForm( 'core_options_orderby_direction', @$options2['orderby_direction'], $config )
		.	 '</li>';
		echo '<li><label>'.JText::_( 'COM_CCK_OPTIONS_ATTRIBUTES' ).'</label><a href="javascript:void(0);" id="custom_attr_toggle"><span class="variation_value">'.JText::_( 'COM_CCK_TOGGLE' ).'</span></a></li>';
		echo JCckDev::renderForm( 'core_options_limit', @$options2['limit'], $config );
		
		// Multiple
		echo JCckDev::renderForm( 'core_bool3', $this->item->bool3, $config, array( 'label'=>'Multiple' ) );
		echo JCckDev::renderForm( 'core_rows', $this->item->rows, $config );
		echo JCckDev::renderForm( 'core_separator', $this->item->divider, $config );

		// Language
		echo JCckDev::renderForm( 'core_options_language_detection', @$options2['language_detection'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_LANGUAGE_CODES_DEFAULT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_language_codes', @$options2['language_codes'], $config, array( 'size' => 21 ) )
		 .	 JCckDev::getForm( 'core_options_language_default', @$options2['language_default'], $config, array( 'size' => 5 ) )
		 .	 '</li>';

		// Static
		echo JCckDev::renderForm( 'core_bool4', $this->item->bool4, $config, array( 'label'=>'Static Options', 'options'=>'No=0||Yes=optgroup||Above=1||Below=2||Both=3' ) );
		echo JCckDev::renderBlank();
		echo JCckDev::renderForm( 'core_options', $options, $config );
		
		if ( $count ) {
			for ( $i = 0; $i < $count; $i++ ) {
				echo '<li class="w100 custom_attr_mapping"><label>'.JText::_( 'COM_CCK_OPTIONS_ATTR_COLUMN' ).'</label>';
				echo JCckDev::getForm( 'core_dev_text', @$location[$i], $config, array( 'size'=>7, 'storage_field'=>'string[location][]' ) );
				$j	=	$i;
				$j++;
				echo JCckDev::getForm( 'core_dev_text', @$options2['attr'.$j], $config, array( 'size'=>7, 'storage_field'=>'json[options2][attr'.$j.']' ) );
				echo '</li>';
			}
		}

		echo JCckDev::renderHelp( 'field', 'seblod-2-x-select-dynamic-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
	</ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_query').isVisibleWhen('bool2','1');
	$('#json_options2_table, #json_options2_name, #json_options2_value, #json_options2_where, #json_options2_orderby, #json_options2_limit, #blank_li').isVisibleWhen('bool2','0');
	$('#rows, #divider').isVisibleWhen('bool3','1');
	$('#sortable_core_options').isVisibleWhen('bool4','1,2,3');
	$('.custom_attr_mapping').hide();
	$("#adminForm").on("click", "#custom_attr_toggle", function() {
		$('.custom_attr_mapping').toggle();
	});
});
</script>