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
						parent.jQuery("#'.$name.'_match_options").val("{}");
						parent.jQuery("#'.$name.'_match_value").val("");
						this.close();
					},
					submit: function() {
						var data = $("#match_value").val();
						parent.jQuery("#'.$name.'_match_value").val(data);
						var data = {};
						$(".match_options").each(function(i) {
							var id = $(this).attr("id");
							var k = id.substring(14);
							data[k] = $("#"+id).myVal();
						});
						var data2 = [];
						var x = 0;
						if ($("#sortable_core_dev_texts").length && $("#match_options_by_field").val() == "1") {
							$("[name=\"match_options\[by_field_values\]\[\]\"]").each(function(i) {
								v = $(this).val();
								data2[x] = v;
								x++;
							});
						}
						data2 = (data2.length) ? data2.join("||") : "";
						data["by_field_values"] = data2;
						var encoded = $.toJSON(data);
						parent.jQuery("#'.$name.'_match_options").val(encoded);
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var data = parent.jQuery("#'.$name.'_match_value").val();
					$("#match_value").val(data);
					$("#match_mode").val(parent.jQuery("#'.$name.'_match_mode").val());
					var encoded = parent.jQuery("#'.$name.'_match_options").val();
					var data = ( encoded != "" ) ? $.evalJSON(encoded) : "";
					if (data) {
						$.each(data, function(k, v) {
							var elem = "match_options_"+k;
							if($("#"+elem).length) {
								$("#"+elem).myVal(v);
							} else {
								if(elem == "match_options_by_field_values") {
									if ($("#match_options_by_field").val() == "1") {
										var temp = v.split("||");
										var len = temp.length;
										var len2 = $("#sortable_core_dev_texts").children().length;
										for(i = len2; i < len2; i++) {
											$("#sortable_core_dev_texts div:eq(0)").remove();
										}
										for(i = 0; i < len; i++) {
											if ( !$("[name=\"match_options\[by_field_values\]\[\]\"]:eq("+i+")").length ) {
												$("#sortable_core_dev_texts>div:eq("+(i-1)+") .button-add-core_dev_texts").click();
											}
											$("[name=\"match_options\[by_field_values\]\[\]\"]:eq("+i+")").myVal(temp[i]);
										}
									}

								}
							}
						});
					}
					$("#match_options_var_type").isVisibleWhen("match_mode","ASC,DESC");
					$("#match_options_by_field").isVisibleWhen("match_mode","FIELD");
					$("#sortable_core_dev_texts").isVisibleWhen("match_options_by_field","1");
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

$options	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_fields WHERE name = "'.$this->item->name.'"' );
$options	=	explode( '||', $options );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'COM_CCK_SEARCH_OPTIONS' ) ); ?>
	<input type="hidden" id="match_mode" name="match_mode" value="" />
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Modifier', 'defaultvalue'=>'', 'selectlabel'=>'None', 'options'=>'Length=0||Numeric=1', 'storage_field'=>'match_options[var_type]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Ordering', 'selectlabel'=>'', 'options'=>'Following Options=0||Custom=1', 'storage_field'=>'match_options[by_field]', 'css'=>'match_options' ) );
		echo JCckDev::renderForm( 'core_dev_texts', $options, $config, array( 'label'=>'clear', 'selectlabel'=>'', 'options'=>'', 'maxlength'=>88, 'storage_field'=>'match_options[by_field_values]' ) );
		?>
	</ul>
</div>