<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: match.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$doc	=	JFactory::getDocument();
$name	=	$this->item->name;
$lang   =	JFactory::getLanguage();
$root	=	JUri::root( true );
$lang->load( 'plg_cck_field_field_x', JPATH_ADMINISTRATOR, null, false, true );
$lang->load( 'plg_cck_field_group_x', JPATH_ADMINISTRATOR, null, false, true );
Helper_Include::addDependencies( 'box', 'edit' );
$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
$js		=	'
			(function ($){
				JCck.Dev = {
					reset: function() {
						parent.jQuery("#'.$name.'_match_collection").val("");
						parent.jQuery("#'.$name.'_match_value").val("");
						parent.jQuery("#'.$name.'_match_options").val("");
						this.close();
					},
					submit: function() {
						var data = $("#match_collection").val();
						parent.jQuery("#'.$name.'_match_collection").val(data);
						var data = $("#match_value").val();
						parent.jQuery("#'.$name.'_match_value").val(data);
						var data = {};
						$(".match_options").each(function(i) {
							var id = $(this).attr("id");
							var k = id.substring(14);
							data[k] = $("#"+id).myVal();
						});
						var encoded = $.toJSON(data);
						parent.jQuery("#'.$name.'_match_options").val(encoded);
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var data = parent.jQuery("#'.$name.'_match_collection").val();
					$("#match_collection").val(data);
					var data = parent.jQuery("#'.$name.'_match_value").val();
					$("#match_value").val(data);
					$("#match_mode").val(parent.jQuery("#'.$name.'_match_mode").val());
					var encoded = parent.jQuery("#'.$name.'_match_options").val();
					var data = ( encoded != "" ) ? $.evalJSON(encoded) : "";
					if (data) {
						$.each(data, function(k, v) {
							var elem = "match_options_"+k;
							$("#"+elem).myVal(v);
						});
					}
					$("#match_options_table").isVisibleWhen("match_mode","nested_exact");
					$("#match_options_var_type").isVisibleWhen("match_mode","exact,not_equal,any_exact,not_any_exact");
					$("#match_options_var_mode").isVisibleWhen("match_mode","any_exact");
					$("#match_options_var_count").isVisibleWhen("match_mode","each_exact");
					$("#match_options_var_count_offset").isVisibleWhen("match_options_var_count","1");
					$("#match_value").isVisibleWhen("match_mode","any,any_exact,each,each_exact,not_any_exact");
					$("#match_options_fieldname1,#match_options_fieldname2,#match_options_fieldname3,#match_options_var_unit").isVisibleWhen("match_mode","radius_higher,radius_lower");
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

$options			=	array();
$options[] 			=	JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) );
$options[] 			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'PLG_CCK_FIELD_FIELD_X_LABEL' ) );
$opts				=	JCckDatabase::loadObjectList( 'SELECT a.name AS value, a.title AS text FROM #__cck_core_fields AS a WHERE a.type = "field_x" AND a.published = 1 ORDER BY a.title ASC' );
if ( count( $opts ) ) {
	$options		=	array_merge( $options, $opts );
}
$options[]			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
$options[] 			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'PLG_CCK_FIELD_GROUP_X_LABEL' ) );
$opts				=	JCckDatabase::loadObjectList( 'SELECT a.name AS value, a.title AS text FROM #__cck_core_fields AS a WHERE a.type = "group_x" AND a.published = 1 ORDER BY a.title ASC' );
if ( count( $opts ) ) {
	$options		=	array_merge( $options, $opts );
}
$options[]			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
$form				=	JHtml::_( 'select.genericlist', $options, 'ffp['.$name.'][match_collection]', 'class="inputbox adminformlist-maxwidth"', 'value', 'text', '', 'match_collection' )
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'COM_CCK_SEARCH_OPTIONS' ) ); ?>
    <input type="hidden" id="match_mode" name="match_mode" value="" />
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo '<li><label>'.JText::_( 'PLG_CCK_FIELD_GROUP_COLLECTION' ).'</label>'.$form.'</li>';
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Separator', 'size'=>'8', 'storage_field'=>'match_value' ) );
		echo JCckDev::renderForm( 'core_tables', '', $config, array( 'label'=>'Table', 'selectlabel'=>'Inherited', 'storage_field'=>'match_options[table]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Comparison Rule', 'selectlabel'=>'', 'options'=>'Quoted=1||Unquoted=0', 'storage_field'=>'match_options[var_type]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Comparison Mode', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Simple=0||Multiple=1', 'storage_field'=>'match_options[var_mode]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Comparison Count', 'selectlabel'=>'', 'defaultvalue'=>'', 'options'=>'None=||Equal=0||Minus=1', 'storage_field'=>'match_options[var_count]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Comparison Count Offset', 'size'=>'8', 'storage_field'=>'match_options[var_count_offset]', 'css'=>'match_options' ) );

		echo JCckDev::renderBlank();
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Latitude Field', 'storage_field'=>'match_options[fieldname1]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Longitude Field', 'storage_field'=>'match_options[fieldname2]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Radius Field', 'storage_field'=>'match_options[fieldname3]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Unit', 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'Kilometers=0||Miles=1', 'storage_field'=>'match_options[var_unit]', 'css'=>'match_options' ) );
        ?>
    </ul>
</div>