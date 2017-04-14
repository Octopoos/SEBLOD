<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: conditional.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Prepare
$field			=	JCckDatabase::loadObject( 'SELECT id, title, name FROM #__cck_core_fields WHERE name = "'.$this->item->name.'"' );
$fields			=	array();
if ( $this->item->type && $this->item->id ) {
	$fields		=	JCckDatabase::loadObjectList( 'SELECT DISTINCT a.title as text, a.name as value, a.type FROM #__cck_core_fields AS a'
				.	' LEFT JOIN #__cck_core_'.$this->item->type.'_field AS b ON b.fieldid = a.id'
				.	' WHERE b.'.$this->item->type.'id = '.$this->item->id . ' ORDER BY text' );
}
$options		=	array();
$rules			=	array( 'and'=>JText::_( 'COM_CCK_AND' ), 'or'=>JText::_( 'COM_CCK_OR' ) );
$friendly		=	array();
if ( count( $fields ) ) {
	foreach ( $fields as $f ) {
		$options[]	=	JHtml::_( 'select.option', $f->value, $f->text );
	}
}
$options2		=	array_merge( array( JHtml::_( 'select.option', '', '...' ) ), $options );
$options		=	array_merge( array( JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_SELECT' ).' -' ) ), $options );

$triggerstates_list		=	array( JHtml::_( 'select.option', 'isEqual', JText::_( 'COM_CCK_STATE_IS_EQUAL_IN' ) ),
								   JHtml::_( 'select.option', 'isDifferent', JText::_( 'COM_CCK_STATE_IS_DIFFERENT' ) ),
								   JHtml::_( 'select.option', 'isFilled', JText::_( 'COM_CCK_STATE_IS_FILLED' ) ),
								   JHtml::_( 'select.option', 'isEmpty', JText::_( 'COM_CCK_STATE_IS_EMPTY' ) ),
								   JHtml::_( 'select.option', 'isChanged', JText::_( 'COM_CCK_STATE_IS_CHANGED' ) ),
								   JHtml::_( 'select.option', 'isPressed', JText::_( 'COM_CCK_STATE_IS_PRESSED' ) ),
								   JHtml::_( 'select.option', 'isSubmitted', JText::_( 'COM_CCK_STATE_IS_SUBMITTED' ) ),
								   JHtml::_( 'select.option', 'callFunction', JText::_( 'COM_CCK_STATE_CALL_FUNCTION' ) ) );
$triggerstates_list2	=	array_merge( array( JHtml::_( 'select.option', '', '...' ) ), $triggerstates_list );

$states_list	=	array( JHtml::_( 'select.option', 'isVisible', JText::_( 'COM_CCK_STATE_IS_VISIBLE' ) ),
						   JHtml::_( 'select.option', 'isHidden', JText::_( 'COM_CCK_STATE_IS_HIDDEN' ) ),
					 	   /*JHtml::_( 'select.option', 'isComputed', JText::_( 'COM_CCK_STATE_IS_COMPUTED' ) ),*/
						   JHtml::_( 'select.option', 'isFilled', JText::_( 'COM_CCK_STATE_IS_FILLED' ) ),
						   JHtml::_( 'select.option', 'isFilledBy', JText::_( 'COM_CCK_STATE_IS_FILLED_BY' ) ),
						   JHtml::_( 'select.option', 'isEmpty', JText::_( 'COM_CCK_STATE_IS_EMPTY' ) ),
						   JHtml::_( 'select.option', 'isEnabled', JText::_( 'COM_CCK_STATE_IS_ENABLED' ) ),
						   JHtml::_( 'select.option', 'isDisabled', JText::_( 'COM_CCK_STATE_IS_DISABLED' ) ),
						   JHtml::_( 'select.option', 'hasClass', JText::_( 'COM_CCK_STATE_HAS_CLASS' ) ),
						   JHtml::_( 'select.option', 'hasNotClass', JText::_( 'COM_CCK_STATE_HASNT_CLASS' ) ),
						   JHtml::_( 'select.option', 'triggers', JText::_( 'COM_CCK_STATE_TRIGGERS' ) ) );
$states_list2	=	array_merge( array( JHtml::_( 'select.option', '', '...' ) ), $states_list );
$states_list	=	array_merge( array( JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_SELECT' ).' -' ) ), $states_list );

// Set
$doc	=	JFactory::getDocument();
$root	=	JUri::root( true );
$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
$js		=	'
			(function ($){
				JCck.Dev = {
					clean: function(arr) {
						var i, len=arr.length, out=[], obj={};
						for (i=0;i<len;i++) {obj[arr[i]]=0;}
						for (i in obj) { if(i!=""){ out.push(i); }}
						return out;
					},
					reset: function() {
						var elem = "ffp_'.$this->item->name.'_conditional";
						parent.jQuery("#"+elem).val("");
						var text = "'.JText::_( 'COM_CCK_ADD' ).'";
						parent.jQuery("#"+elem).next("span").html(text);
						var elem = "ffp_'.$this->item->name.'_conditional_options";
						parent.jQuery("#"+elem).val("");
						parent.jQuery("#"+elem).next("span").html("");
						this.close();
					},
					submit: function() {
						var c = 0;
						var n = $(".conditional").length;
						var encoded = "";
						var encode = [];
						var conditionals = [];
						var id = "";
						var k = 0;
						$(".conditional").each(function(i) {
							id = $(this).attr("id");
							var states = [];
							var conditions = [];
							var k = 0;
							$(".conditional_states").each(function(j) {
								var v = $("#"+id+"_states"+j).val();
								if (v && $("#"+id+"_states"+j).is(":visible")) {
									states[k] = {};
									states[k].type = v;
									states[k].selector = $("#"+id+"_states"+j+"_selector").val();
									states[k].revert = ($("#"+id+"_states"+j+"_revert").prop("checked") !== false) ? "1" : "0";
									states[k].value = $.trim($("#"+id+"_states"+j+"_value").val());
									k++;
								}
							});
							var k = 0;
							$(".conditional_conditions").each(function(j) {
								var v = $("#"+id+"_conditions"+j).val();
								var v2 = $("#"+id+"_conditions"+j+"_trigger").val()
								if (v && v2 && $("#"+id+"_conditions"+j).is(":visible")) {
									conditions[k] = {};
									conditions[k].type = v;
									conditions[k].trigger = v2;
									conditions[k].value = $.trim($("#"+id+"_conditions"+j+"_value").val());
									conditionals[c++] = v2;
									k++;
								}
							});
							if (states.length && conditions.length) {
								encode[i] = {};
								encode[i].states = states;
								encode[i].conditions = conditions;
								encode[i].rule = $("#cds"+i+"_rule").val()+"";
							}
						});
						conditionals = JCck.Dev.clean(conditionals);
						conditionals = conditionals.join(",");
						encoded	= $.toJSON(encode);
						if (n == 1) {
							encoded = encoded.substr(1);
							encoded = encoded.substr(0, encoded.length-1);
						}
						var elem = "ffp_'.$this->item->name.'_conditional";
						parent.jQuery("#"+elem).val(conditionals);
						var text = "&lt; '.JText::_( 'COM_CCK_EDIT' ).' /&gt;";
						parent.jQuery("#"+elem).next("span").html(text);
						var elem = "ffp_'.$this->item->name.'_conditional_options";
						parent.jQuery("#"+elem).val(encoded);
						var text = "( "+n+" )";
						parent.jQuery("#"+elem).next("span").html(text);
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var elem = "ffp_'.$this->item->name.'_conditional";
					var conditions = parent.jQuery("#"+elem).val();
					var n = conditions.split(",").length;
					var elem = "ffp_'.$this->item->name.'_conditional_options";
					var encoded = parent.jQuery("#"+elem).val();
					if (n) {
						var data = ( encoded != "" ) ? $.evalJSON(encoded) : "";
						n = ( data.length === undefined ) ? 1 : data.length;
						if (n == 1) {var d = data;	data = []; data[0] = d;}
						if (n==0) {
							$("#"+"cds0_states2").parent().parent().slideUp();
							for(i=8; i>1; i--) { $("#"+"cds0_conditions"+i).parent().slideUp(); }
						} else {
							for(i=0; i<n; i++) {
								if (data[i]) {
									if (data[i]["rule"]) {
										$("#"+"cds"+i+"_rule").val(data[i]["rule"]);
										var n2 = data[i]["states"].length;
										for (c=0; c<n2; c++) {
											$("#"+"cds"+i+"_states"+c).val(data[i]["states"][c].type);
											$("#"+"cds"+i+"_states"+c+"_selector").val(data[i]["states"][c].selector);
											if (data[i]["states"][c].revert == "0") {
												$("#"+"cds"+i+"_states"+c+"_revert").prop("checked", false);
											}
											$("#"+"cds"+i+"_states"+c+"_value").val(data[i]["states"][c].value);
										}
										for (n2++; n2<6; n2++) { $("#"+"cds"+i+"_states"+n2).parent().parent().slideUp("500"); }
										var n2 = data[i]["conditions"].length;
										for (c=0; c<n2; c++) {
											$("#"+"cds"+i+"_conditions"+c).val(data[i]["conditions"][c].type);
											$("#"+"cds"+i+"_conditions"+c+"_trigger").val(data[i]["conditions"][c].trigger);
											$("#"+"cds"+i+"_conditions"+c+"_value").val(data[i]["conditions"][c].value);
										}
										for (n2++; n2<8; n2++) { $("#"+"cds"+i+"_conditions"+n2).parent().slideUp("500"); }
									} else {
										$.each(data[i], function(key, val) {
											var c = 0;
											$.each(val, function(ke, va) {
												$("#"+"cds"+i+"_"+key+c).val(ke.substr(2));
												$.each(va, function(k, v) {
													if (k =="revert") {
														if (v == "0") {
															$("#"+"cds"+i+"_"+key+c+"_"+k).prop("checked", false);
														}
													} else {
														$("#"+"cds"+i+"_"+key+c+"_"+k).val(v);
													}
												});
												c++;
											});
											for (c++; c<6; c++) { $("#"+"cds"+i+"_states"+c).parent().parent().slideUp("500"); }
											for (c = 2; c<8; c++) { $("#"+"cds"+i+"_conditions"+c).parent().slideUp("500"); }
										});
									}
								}
							}
						}
					}
					$("#layout").on("click", ".add", function() {
						var n = $(".conditional:last").attr("id").substr(3);
						var elem = "cds"+ n;
						var num = (parseInt(n)+1);
						var num2 = (parseInt(n)+2);
						var elem2 = "cds"+ num;
						var reg = new RegExp(elem,"g");
						var reg2 = new RegExp("# "+num+".","i");
						var data = \'<div id="\'+elem2+\'" class="seblod conditional">\'+$("#"+elem).clone().html().replace(reg, elem2).replace(reg2, "# "+num2+".")+\'</div>\';
						$("#"+elem).after(data);
						$("#"+elem2+" .del").css("visibility", "visible");
					});
					$("#layout").on("click", ".del", function() {
						$(this).parents().eq(4).remove();
					});
					$("#layout").on("click", ".fill", function() {
						var id = $(this).parents().eq(5).attr("id");
						var idx = $(this).attr("name").replace("condition", "");
						var field = $("#"+id+"_conditions"+idx+"_trigger").val();
						if (field) {
							var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=conditionnal&name="+field+"&type="+id+"_conditions"+idx+"_value";
							$.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}});
						}
					});
					$("#layout").on("change", ".state_kk", function() {
						var cur = $(this).val();
						var id = $(this).parents().eq(6).attr("id");
						var name = $(this).attr("name");
						var val = "";
						if (cur=="isFilled"||cur=="isFilledBy"||cur=="isEmpty"||cur=="isComputed"||cur=="isEnabled"||cur=="isDisabled"||cur=="triggers") { val = " #form#"; }
						$("#"+id+"_"+name+"_selector").val(val);
						if(name == "states1") {
							if(cur) {
								$(this).parent().parent().next().next().slideDown("500");
							} else {
								$(this).parent().parent().next().next().slideUp("500");
							}
						}
					});
					$("#layout").on("change", ".trigger_kk", function() {
						var cur = $(this).val();
						if(cur) {
							$(this).parent().next().next().slideDown("500");
						} else {
							$(this).parent().next().next().slideUp("500");
						}
					});
				});
			})(jQuery);
			';
Helper_Include::addDependencies( 'box', 'edit' );
$doc->addScriptDeclaration( $js );

$add	=	'add icon-plus';
$del	=	'del icon-minus';
$fill	=	'<span class="icon-menu-2"></span>';

for ( $i = 0, $n = (int)$this->item->title; $i < $n; $i++ ) {
	$condition		=	'cds'.$i;
	$legend			=	( $i == 0 ) ? '<div class="legend top left">'.JText::_( 'COM_CCK_CONDITIONAL_STATES' ).'<span class="add icon-plus"></span></div>' : '';
	
	$states0		=	JHtml::_( 'select.genericlist', $states_list, 'states0', 'class="inputbox input-medium blue state_kk"', 'value', 'text', '', $condition.'_states0' );
	$s_value0		=	'<input type="text" id="'.$condition.'_states0_'.'value" name="value" value="" class="inputbox input-mini states0" size="8" />';
	$s_selector0	=	'<input type="text" id="'.$condition.'_states0_'.'selector" name="selector" value="" class="inputbox input-mini states0" size="12" />';
	$s_revert0		=	'<input type="checkbox" id="'.$condition.'_states0_'.'revert" name="revert" value="1" class="inputbox states0" checked="checked" />';
	
	$states1		=	JHtml::_( 'select.genericlist', $states_list2, 'states1', 'class="inputbox input-medium blue state_kk" trigger_kk', 'value', 'text', '', $condition.'_states1' );
	$s_value1		=	'<input type="text" id="'.$condition.'_states1_'.'value" name="value" value="" class="inputbox input-mini states1" size="8" />';
	$s_selector1	=	'<input type="text" id="'.$condition.'_states1_'.'selector" name="selector" value="" class="inputbox input-mini states1" size="12" />';
	$s_revert1		=	'<input type="checkbox" id="'.$condition.'_states1_'.'revert" name="revert" value="1" class="inputbox states1" checked="checked" />';
	
	$states2		=	JHtml::_( 'select.genericlist', $states_list2, 'states2', 'class="inputbox input-medium blue state_kk"', 'value', 'text', '', $condition.'_states2' );
	$s_value2		=	'<input type="text" id="'.$condition.'_states2_'.'value" name="value" value="" class="inputbox input-mini states2" size="8" />';
	$s_selector2	=	'<input type="text" id="'.$condition.'_states2_'.'selector" name="selector" value="" class="inputbox input-mini states2" size="12" />';
	$s_revert2		=	'<input type="checkbox" id="'.$condition.'_states2_'.'revert" name="revert" value="1" class="inputbox states2" checked="checked" />';
	
	$t_rule			=	JHtml::_( 'select.genericlist', $rules, 'rule', 'class="inputbox blue"', 'value', 'text', 'and', $condition.'_rule' );
	
	$t_value0		=	JHtml::_( 'select.genericlist', $triggerstates_list, 'conditions0', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', 
						'isEqual', $condition.'_conditions0' )
					.	'<input type="text" id="'.$condition.'_conditions0_value" name="value" value="" class="inputbox input-mini triggers0" size="8" />'
					.	'&nbsp;<span class="fill" name="condition0">'.$fill.'</span>';
	$t_trigger0		=	JHtml::_( 'select.genericlist', $options, 'trigger', 'class="inputbox input-medium blue triggers0" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions0_trigger' );
	
	$t_value1		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions1', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions1' )
					.	'<input type="text" id="'.$condition.'_conditions1_value" name="value" value="" class="inputbox input-mini triggers1" size="8" />'
					.	'&nbsp;<span class="fill" name="condition1">'.$fill.'</span>';
	$t_trigger1		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers1 trigger_kk" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions1_trigger' );
	
	$t_value2		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions2', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions2' )
					.	'<input type="text" id="'.$condition.'_conditions2_value" name="value" value="" class="inputbox input-mini triggers2" size="8" />'
					.	'&nbsp;<span class="fill" name="condition2">'.$fill.'</span>';
	$t_trigger2		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers2 trigger_kk" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions2_trigger' );
	
	$t_value3		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions3', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions3' )
					.	'<input type="text" id="'.$condition.'_conditions3_value" name="value" value="" class="inputbox input-mini triggers3" size="8" />'
					.	'&nbsp;<span class="fill" name="condition3">'.$fill.'</span>';
	$t_trigger3		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers3 trigger_kk" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions3_trigger' );
						
	$t_value4		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions4', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions4' )
					.	'<input type="text" id="'.$condition.'_conditions4_value" name="value" value="" class="inputbox input-mini triggers4" size="8" />'
					.	'&nbsp;<span class="fill" name="condition4">'.$fill.'</span>';
	$t_trigger4		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers4 trigger_kk" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions4_trigger' );

	$t_value5		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions5', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions5' )
					.	'<input type="text" id="'.$condition.'_conditions5_value" name="value" value="" class="inputbox input-mini triggers5" size="8" />'
					.	'&nbsp;<span class="fill" name="condition5">'.$fill.'</span>';
	$t_trigger5		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers5 trigger_kk" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions5_trigger' );

	$t_value6		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions6', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions6' )
					.	'<input type="text" id="'.$condition.'_conditions6_value" name="value" value="" class="inputbox input-mini triggers6" size="8" />'
					.	'&nbsp;<span class="fill" name="condition6">'.$fill.'</span>';
	$t_trigger6		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers6 trigger_kk" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions6_trigger' );

	$t_value7		=	JHtml::_( 'select.genericlist', $triggerstates_list2, 'conditions7', 'class="inputbox blue triggers0" style="max-width:98px;"', 'value', 'text', '',
						$condition.'_conditions7' )
					.	'<input type="text" id="'.$condition.'_conditions7_value" name="value" value="" class="inputbox input-mini triggers7" size="8" />'
					.	'&nbsp;<span class="fill" name="condition7">'.$fill.'</span>';
	$t_trigger7		=	JHtml::_( 'select.genericlist', $options2, 'trigger', 'class="inputbox input-medium blue triggers7" style="max-width:150px;"', 'value', 'text', '',
						$condition.'_conditions7_trigger' );
	
	$remove		=	( $i > 0 ) ? '<span class="'.$del.'"></span>' : '<span class="'.$del.'" style="visibility: hidden;"></span>';

	echo '<div id="'.$condition.'" class="seblod conditional">'.$legend
	 .	 '<div class="legend top left">'.'# '.($i + 1).'.</div>'
	 .	 '<table class="adminlist cck_radius2">'
	 .	 '<tr class="half">'
	 .	 '<th width="400px" align="center">'.JText::_( 'COM_CCK_STATES' ).'</th>'
	 .	 '<th align="center"></td>'
	 .	 '<th width="380px" align="center">'.JText::_( 'COM_CCK_TRIGGERS' ).'</td>'
	 .	 '</tr>'
	 .	 '<tr class="row0" height="145px">'
	 .	 '<td width="400px" align="center" class="states">'
	 .	 '<div class="begin">'.JText::_( 'COM_CCK_THIS_FIELD' ).'</div>'
	 .	 '<div class="conditional_states">'
	 .	 '<div class="selector">'.$s_selector0.'<span class="star"> &sup1;</span>'.$s_revert0.'<span class="star">&sup2;</span></div><div class="define">'.$states0.'<span>'.$s_value0.'</span></div></div>'
	 .	 '<div class="clr"></div><div class="conditional_states">'
	 .	 '<div class="selector">'.$s_selector1.'<span class="star"> &sup1;</span>'.$s_revert1.'<span class="star">&sup2;</span></div><div class="define">'.$states1.'<span>'.$s_value1.'</span></div></div>'
	 .	 '<div class="clr"></div><div class="conditional_states">'
	 .	 '<div class="selector">'.$s_selector2.'<span class="star"> &sup1;</span>'.$s_revert2.'<span class="star">&sup2;</span></div><div class="define">'.$states2.'<span>'.$s_value2.'</span></div></div>'
	 .	 '</td>'
	 .	 '<td class="ope" align="center">'.JText::_( 'COM_CCK_WHEN' ).$t_rule.'</td>'
	 .	 '<td width="380px" align="center" class="triggers">'
	 .	 '<div class="conditional_conditions">'.$t_trigger0.$t_value0.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger1.$t_value1.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger2.$t_value2.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger3.$t_value3.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger4.$t_value4.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger5.$t_value5.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger6.$t_value6.'</div>'
	 .	 '<div class="clr"></div><div class="conditional_conditions">'.$t_trigger7.$t_value7.'</div>'
	 .	 '</td>'
	 .	 '</tr>'
	 .	 '<tr class="row2 half"><td colspan="4" align="left">'.$remove.'</td></tr>'
	 .	 '</table>'
	 .	 '</div>';
}
?>
<div class="seblod inverted">
	<?php echo JText::_( 'COM_CCK_CONDITIONAL_STATES_DESC' ); ?><br /><br />
	<span class="star">&sup1; </span><span class="star2"><?php echo JText::_( 'COM_CCK_JQUERY_SELECTOR' ); ?></span><?php echo ' '.JText::_( 'COM_CCK_JQUERY_SELECTOR_DESC' ); ?>
    <br /><br />
    <span class="star">&sup2; </span><span class="star2"><?php echo JText::_( 'COM_CCK_STATE_AUTOREVERT' ); ?></span><?php echo ' '.JText::_( 'COM_CCK_STATE_AUTOREVERT_DESC' ); ?>
	<br /><br /><?php echo JText::_( 'COM_CCK_CONDITIONAL_STATES_DESC2' ); ?>
</div>