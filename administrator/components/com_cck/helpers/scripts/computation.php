<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: computation.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

Helper_Include::addDependencies( 'box', 'edit' );

$config	=	JCckDev::init( array( 'select_simple', 'text', 'textarea' ), true, array() );
$doc	=	JFactory::getDocument();
$root	=	JUri::root( true );
$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
$event	=	str_replace( array( "\r", "\n", "\t" ), '', JCckDev::getForm( 'core_computation_event', '', $config, array( 'attributes'=>'style="width:60px; padding:5px 2px;  text-transform:lowercase;"' ) ) );
$event	=	str_replace( 'events_', 'events__0', $event );
$event	=	''; // next time..
$js		=	'
			(function ($){
				JCck.Dev = {
					reset: function() {
						var elem = "ffp_'.$this->item->name.'_computation";
						parent.jQuery("#"+elem).val("");
						var text = "'.JText::_( 'COM_CCK_ADD' ).'";
						parent.jQuery("#"+elem).next("span").html(text);
						var elem = "ffp_'.$this->item->name.'_computation_options";
						parent.jQuery("#"+elem).val("");
						parent.jQuery("#"+elem).next("span").html("");
						this.close();
					},
					submit: function() {
						var k = 0;
						var v = "";
						var encoded = "";
						var computation = {};
						var data = [];
						var pattern = new RegExp("\.");
						computation.calc = $("#math").val();
						computation.custom = "";
						computation.recalc = $("#recalc").val();
						computation.event = $("#event").val();
						computation.fields = [];
						computation.targets = [];
						computation.events = [];
						if (computation.calc=="custom") {
							computation.custom = $("#custom").val();
						}
						computation.format = $("#format").val();
						if (computation.format=="toFixed") {
							computation.precision = $("#precision").val();
						}
						$("input:text[name=\'fields[]\']").each(function(i) {
							v = $(this).val();
							if (v) {
								computation.fields[k] = v;
								computation.targets[k] = $("input:text[name=\'targets[]\']:eq("+i+")").val();
								computation.events[k] = $("select[name=\'events[]\']:eq("+i+")").val();
								if (computation.targets[k]=== undefined ){
									computation.targets[k] = "";
								}
								if (computation.events[k]=== undefined ){
									computation.events[k] = "";
								}
								if (/^[a-z0-9_\|]+$/.test(v)) {
									data[k] = "#"+v;
								} else {
									data[k] = v;
								}
								k++;
							}
						});
						var data2 = computation.fields.join(",");
						encoded	= $.toJSON(computation);
						var elem = "ffp_'.$this->item->name.'_computation";
						parent.jQuery("#"+elem).val(data);
						var text = "&lt; '.JText::_( 'COM_CCK_EDIT' ).' /&gt;";
						parent.jQuery("#"+elem).next("span").html(text);
						var elem = "ffp_'.$this->item->name.'_computation_options";
						parent.jQuery("#"+elem).val(encoded);
						this.close();
						return;
					}
    			}
				$(document).ready(function() {
					var elem = "ffp_'.$this->item->name.'_computation";
					var computation = parent.jQuery("#"+elem).val();
					var n = computation.split(",").length;
					var elem = "ffp_'.$this->item->name.'_computation_options";
					var encoded = parent.jQuery("#"+elem).val();
					var data = ( encoded != "" ) ? $.evalJSON(encoded) : "";
					var vt = "";
					var hasTarget = 0;
					var isNew = 0;
					if (data.calc) {
						$("#math").val(data.calc);
						if (data.calc=="custom") {
							$("#custom").val(data.custom);
						}
					}
					if (data.fields) {
						$("input:text[name=\'fields[]\']").each(function(k, v) {
							$(this).val(data.fields[k]);
							if (data.targets) {
								if (data.targets[k]) {
									vt = data.targets[k];
									hasTarget = 1;
								} else {
									vt = "";
								}
							}
							var ev = \''.$event.'\';
							if (ev.length>0) {
								ev = ev.replace("__0", "__"+k);
							}
							$(this).parent().append(\'<div class="attr">\'+ev+\'<input type="text" id="targets__\'+k+\'" name="targets[]" value="\'+vt+\'" class="inputbox mini" style="position:relative; top:3px;" size="10" /><span class="star"> &sup1;</span></div>\');
							if (data.events) {
								if (data.events[k]) {
									$("#events__"+k).val(data.events[k]);
								}
							}
						});
					} else {
						isNew = 1;
					}
					if (hasTarget == 1) {
						$("#toggle_attr").prop("checked", true);
						$("div.attr, div.seblod.inverted").toggle();
					}
					if (data.format) {
						$("#format").val(data.format);
						if (data.format="toFixed") {
							$("#precision").val(data.precision);
						}
					}
					if (data.recalc) {
						$("#recalc").val(data.recalc);
					}
					if (!data.event) {
						data.event = "keyup";
					}
					if (data.event !== undefined) {
						$("#event").val(data.event);
					}
					$("ul.adminformlist").on("change", "select#fields_list", function() {
						var val = $(this).val();
						if (val) {
							$("#sortable_core_options>div:last .button-add-core_options").click();
							var disp = ($("#toggle_attr").prop("checked") !== false) ? \'style="display: block"\' : "";
							$("#sortable_core_options>div:last input:text[name=\'fields[]\']").val(val).parent().append(\'<div class="attr"\'+disp+\'>'.$event.'<input type="text" id="targets__0" name="targets[]" value="" class="inputbox mini" style="position:relative; top:3px;" size="10" /><span class="star"> &sup1;</span></div>\');
						}
						if (isNew == 1) {$("#collection-group-wrap-core_options__0").remove(); isNew = 0;}
					});
					$("div#layout").on("change", "input#toggle_attr", function() {
						$("div.attr, div.seblod.inverted").toggle();
					});
					/**/
					$("#custom, #presets").isVisibleWhen("math","custom",false);
					$("#presets").on("change", function() {
						$("#custom").val($(this).val());
					});
					$("#precision").isVisibleWhen("format","toFixed",false);
					$("div#layout").on("change", "#math", function() {
						if ($(this).val() == "format") {
							$("#recalc").val("0");
							$("#event").val("keyup");
							$("#sortable_core_options>div:last .button-add-core_options").click();
							var disp = ($("#toggle_attr").prop("checked") !== false) ? \'style="display: block"\' : "";
							$("#sortable_core_options>div:last input:text[name=\'fields[]\']").val("'.$this->item->name.'").parent().append(\'<div class="attr"\'+disp+\'><input type="text" id="targets__0" name="targets[]" value="" class="inputbox mini" style="position:relative; top:3px;" size="10" /><span class="star"> &sup1;</span></div>\');
						}
					});
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

$field		=	JCckDatabase::loadObject( 'SELECT id, title, name FROM #__cck_core_fields WHERE name = "'.$this->item->name.'"' );
if ( (int)$this->item->id > 0 ) {
	$fields	=	JCckDatabase::loadObjectList( 'SELECT DISTINCT a.title as text, a.name as value FROM #__cck_core_fields AS a'
			.	' LEFT JOIN #__cck_core_'.$this->item->type.'_field AS b ON b.fieldid = a.id'
			.	' WHERE b.'.$this->item->type.'id = '.$this->item->id.' AND a.name != "'.$this->item->name.'" ORDER BY text' );
	$fields	=	( is_array( $fields ) && count( $fields ) ) ? array_merge( array( JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_ADD_A_FIELD' ).' -' ) ), $fields ) : array();
} else {
	$fields	=	array();
}
$css		=	'div.collection-group-form{margin-right:0px;} ul.adminformlist-2cols li {width:31em!important; padding:0px 37px 0px 0px!important;} #custom{font-size:11px;}';
 
if ( !$this->item->title ) {
	$css	.=	'#collection-group-wrap-core_options__0{display:none;}';
}
$doc->addStyleDeclaration( $css );
$selectors	=	explode( ',', $this->item->title );
?>

<?php
echo '<div class="seblod conditional"><div class="legend top left">'.JText::_( 'COM_CCK_COMPUTATION_RULES' ).'</div>'
 .	 '<ul class="adminformlist adminformlist-2cols">'
 .	 '<li class="w100"><label>'.JText::_( 'COM_CCK_COMPUTATION' ).'</label>'
 .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'sum', 'selectlabel'=>'Select', 'required'=>'required', 'storage_field'=>'math',
					   'options'=>'Custom=custom||Numeric=optgroup||Average=avg||Count=count||Format=format||Max=max||Min=min||Product=product||Sum=sum||String=optgroup||Concatenate=concatenate' ) )
 .	 JCckDev::getForm( 'core_computation_presets', '', $config, array( 'selectlabel'=>'Free' ) )
 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'size'=>64, 'storage_field'=>'custom' ) )
 .	 '</li>'
 .	 '<li><label>'.JText::_( 'COM_CCK_FIELDS' ).'</label>'
 .	 JCckDev::getForm( 'core_options', $selectors, $config, array( 'label'=>'Fields', 'rows'=>1, 'storage_field'=>'fields' ) )
 .	 JHtml::_( 'select.genericlist', $fields, 'fields_list', 'class="inputbox select" style="max-width:175px;"', 'value', 'text', '', 'fields_list' )
 .	 '<input type="checkbox" id="toggle_attr" name="toggle_attr" value="1" /><label for="toggle_attr" class="toggle_attr">'.JText::_( 'COM_CCK_CUSTOM_ATTRIBUTE_AND_EVENT' ).'</label>'
 .	 '</li>'
 .	 '<li><label>'.JText::_( 'COM_CCK_FORMAT_PRECISION' ).'</label>'
 .	 JCckDev::getForm( 'core_computation_format', '', $config )
 .	 JCckDev::getForm( 'core_computation_precision', '', $config )
 .	 '</li>'
 .	 JCckDev::renderForm( 'core_computation_recalc', '', $config )
 .	 JCckDev::renderForm( 'core_computation_event', '', $config, array( 'defaultvalue'=>'change', 'options'=>'None=none||Event=optgroup||Event Change=change||Event Keyup=keyup', 'storage_field'=>'event' ) )
 .	 '</ul></div>';
?>
<div class="seblod inverted" style="display:none;">
	<span class="star">&sup1; </span><span class="star2"><?php echo JText::_( 'COM_CCK_COMPUTATION_CUSTOM_ATTR' ); ?></span><?php echo ' '.JText::_( 'COM_CCK_COMPUTATION_CUSTOM_ATTR_DESC' ); ?>
</div>