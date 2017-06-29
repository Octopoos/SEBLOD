<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: selection.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$desc	=	'';

// Prepare
if ( $this->item->id == 'content_map' || $this->item->id == 'dev_map' ) {
	if ( $this->item->id == 'dev_map' ) {
		$desc					=	JText::_( 'COM_CCK_SELECT_TO_MAP_EXISTING_COLUMN' ).'<br />'.JText::_( 'COM_CCK_SELECT_TO_MAP_EXISTING_COLUMN_CCK_FIELD' );
		$columns				=	array( ''=>'- '.JText::_( 'COM_CCK_SELECT' ).' -',
										   'bool'=>'bool',
										   'bool2'=>'bool2',
										   'bool3'=>'bool3',
										   'bool4'=>'bool4',
										   'bool5'=>'bool5',
										   'bool6'=>'bool6',
										   'bool7'=>'bool7',
										   'json[options2][...]'=>'options2'
										);
	} else {
		$desc					=	JText::_( 'COM_CCK_SELECT_TO_MAP_EXISTING_COLUMN' );
		$pos					=	strpos( $this->item->title, '__' );
		$prefix					=	JFactory::getConfig()->get( 'dbprefix' );

		if ( $pos !== false && $pos == 0 ) {
			$properties			=	array( 'table'=>str_replace( '#__', $prefix, '#'.$this->item->title ) );
		} else {
			$location			=	$this->item->title;
			$pos				=	strpos( $location, 'aka_' );
			
			if ( $pos !== false && $pos == 0 ) {
				$properties			=	array();
			} elseif ( strpos( $location, '#__' ) !== false || strpos( $location, $prefix ) !== false ) {
				$properties			=	array( 'table'=>str_replace( '#__', $prefix, $location ) );
			} else {
				$properties			=	array( 'table' );
				if ( $location != '' ) {
					require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
					$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );

					if ( isset( $properties['table'] ) ) {
						$properties['table']	=	str_replace( '#__', $prefix, $properties['table'] );
					}
				}
			}
		}

		if ( isset( $properties['table'] ) && $properties['table'] != '' ) {
			$columns			=	array();
			$tables				=	JCckDatabase::getTableList( true );
			
			if ( isset( $tables[$properties['table']] ) ) {
				$target			=	$properties['table'];

				if ( $this->item->name != '' && $this->item->name != 'map' ) {
					$target		=	$this->item->name;
				}
				$columns		=	JCckDatabase::loadColumn( 'SHOW COLUMNS FROM '.$target );

				if ( count( $columns ) ) {
					natsort( $columns );
					$columns		=	array_combine( $columns, $columns );
				}
			}
			$columns			=	array_merge( array( ''=>'- '.JText::_( 'COM_CCK_SELECT' ).' -' ), $columns );
		} else {
			$columns			=	array( ''=>'- '.JText::_( 'COM_CCK_SELECT' ).' -' );
		}
	}
	$field						=	new stdClass;
	$field->type				=	'select_simple';
	$form						=	JHtml::_( 'select.genericlist', $columns, 'map', 'class="inputbox select" style="max-width:175px;"', 'value', 'text', '', 'map' );
} elseif ( $this->item->id == 'object_property' ) {
	$columns					=	array();
	$field						=	new stdClass;
	$field->type				=	'select_simple';

	if ( $this->item->name == 'joomla_user' ) {
		$columns				=	JCckDatabase::getTableColumns( '#__users' );
		$columns				=	array_merge( $columns, JCckDatabase::getTableColumns( '#__cck_store_item_users' ) );

		if ( count( $columns ) ) {
			natsort( $columns );
			$columns			=	array_combine( $columns, $columns );
			unset( $columns['cck'], $columns['otep'], $columns['otpKey'] );
		}
	}
	
	$columns					=	array_merge( array( ''=>'- '.JText::_( 'COM_CCK_SELECT' ).' -' ), $columns );
	$form						=	JHtml::_( 'select.genericlist', $columns, $this->item->name, 'class="inputbox select" style="max-width:175px;"', 'value', 'text', '', $this->item->name );
} else {
	$desc						=	'';
	$field						=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE name = "'.$this->item->name.'"' );
	$field->required_alert		=	'';
	$field->selectlabel			=	'SELECT';
	$field->variation			=	'';
	$field->variation_override	=	'';
	$field->restriction			=	'';
	if ( strpos( $field->type, 'checkbox' ) !== false || strpos( $field->type, 'radio' ) !== false ) {
		$field->bool			=	1;
	}
	if ( $this->item->id == 'stage' ) {
		$form					=	JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'Select',
																							 'options'=>'Stage 1st=1||Stage 2nd=2||Stage 3rd=3||Stage 4th=4||Stage 5th=5',
																							 'storage_field'=>$field->name ) );
	} else {
		$form					=	JCckDevField::getForm( $field, '', $config );
	}
}
$alterMatch					=	0;
$separator					=	'';
$toggle						=	0;

if ( $this->item->title == 'search' ) {
	$isConvertible	=	JCck::callFunc( 'plgCCK_Field'.$field->type, 'isConvertible' );
	if ( $isConvertible == 1 ) {
		$toggle		=	1;
		if ( $field->divider != '' ) {
			$toggle		=	0;
			$separator	=	$field->divider;
		} else {
			$separator	=	',';
		}
		$alterMatch	=	1;
	} else {
		$separator	=	( $field->divider != '' ) ? $field->divider : ',';
	}
} elseif ( $this->item->title == 'conditionnal' || $this->item->title == 'dev' ) {
	$isConvertible	=	JCck::callFunc( 'plgCCK_Field'.$field->type, 'isConvertible' );
	$separator		=	',';
	if ( $isConvertible == 1 ) {
		$toggle		=	1;
		if ( $field->divider != '' ) {
			$toggle		=	0;
		}
	}
}
if ( $alterMatch ) {
	$matchForm	=	JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'', 'options'=>'Keep Match Unchanged=0||Update Match Mode=1', 'storage_field'=>'alter_match', 'attributes'=>'disabled="disabled"' ) );
}

// Set
$doc	=	JFactory::getDocument();
$js		=	'
			(function($) {
				$.fn.isMultiple = function() {
					if (this.is("select") && this.prop("multiple")) {
						return "select";
					} else if (this.is("fieldset") && (this.hasClass("checkbox") || this.hasClass("checkboxes"))){
						return "radio";
					}
					return "";
				}
				$.fn.toggleMultiple = function(offset) {
					if (this.is("select")) {
						if (this.prop("multiple")) {
							this.prepend(\'<option value="">- '.JText::_( 'COM_CCK_SELECT' ).' -</option>\').prop("multiple",false).removeAttr("size");
						} else {
							$("#"+this.attr("id")+" option:eq(0)").remove();
							this.prop("multiple",true).attr("size",15);
						}
						JCck.Dev.resize(offset);
					} else if (this.is("fieldset")) {
						if (this.hasClass("checkbox") || this.hasClass("checkboxes")) {
							var reg = new RegExp(\'type="checkbox"\',"g");
							var src = this.html().replace(reg, \'type="radio"\');
							var val = this.myVal();
							this.html(src).myVal(val);
							this.removeClass("checkbox").addClass("radio");
							this.removeClass("checkboxes").addClass("radios");
						} else {
							var reg = new RegExp(\'type="radio"\',"g");
							var src = this.html().replace(reg, \'type="checkbox"\');
							var val = this.myVal();
							this.html(src).myVal(val);
							this.removeClass("radio").addClass("checkbox");
							this.removeClass("radios").addClass("checkboxes");
						}
					}
					return "";
				}
				JCck.Dev = {
					always:false,
					reset: function() {
						var elem = "#'.$this->item->name.'";
						var elem2 = "#'.$this->item->type.'";
						parent.jQuery(elem2).val("");
						this.close();
					},
					resize: function(offset) {
						var h = $("#layout").height()+44;
						if (offset) {
							h += offset;
						}
						parent.jQuery.colorbox.resize({innerHeight:h});
					},
					submit: function() {
						var client = "'.$this->item->title.'";
						var toggle = "'.$toggle.'";
						var glue = ",";
						var fieldtype = "'.$field->type.'";
						var elem = "#'.$this->item->name.'";
						var elem2 = "#'.$this->item->type.'";
						if (client=="search") {
							var match_value = parent.jQuery(elem+"_match_value").val();
							glue = (match_value!="") ? match_value : "'.$separator.'";
						}
						if (fieldtype != "jform_usergroups") {
							if (client=="search") {
								var v = $(elem).myVal()+"";
								v = v.replace(/,/g, glue);

								if ($("#alter_match_div").length && !$("#alter_match_div").hasClass("hidden-important") && $("#alter_match").val()) {
									var $el = parent.jQuery("#'.$this->item->name.'_match_mode");
									var $el2 = parent.jQuery("span[data-id=\''.$this->item->name.'_match_mode\']");
									var match_mode = $el.val();
									var isMultiple = ($(elem).isMultiple() && v.indexOf(",") !== -1);
									if(isMultiple) {
										if (match_mode=="exact"){
											$el.val("any_exact"); $el2.html(Joomla.JText._("COM_CCK_MATCH_ANY_WORDS_EXACT"));
										} else if (match_mode=="") {
											$el.val("any"); $el2.html(Joomla.JText._("COM_CCK_MATCH_ANY_WORDS"));
										}
									} else {
										if (match_mode=="any_exact"){
											$el.val("exact"); $el2.html(Joomla.JText._("COM_CCK_MATCH_EXACT_PHRASE"));
										} else if (match_mode=="any") {
											$el.val(""); $el2.html(Joomla.JText._("COM_CCK_MATCH_DEFAULT_PHRASE"));
										}
									}
								}
							} else {
								var v = $(elem).myVal();
							}
							parent.jQuery(elem2).val(v);
						} else {
							var v = [];
							$(elem+" input:checked").each(function(i) {
								v[i] = $(this).val();
							});
							parent.jQuery(elem2).val(v.join(","));
						}
						this.close();
						return;
					},
					toggleField: function() {
						var $el = parent.jQuery("#'.$this->item->name.'_match_mode");
						var match_mode = $el.val();
						var match_value = parent.jQuery("#'.$this->item->name.'_match_value").val();
						glue = (match_value!="") ? match_value : "'.$separator.'";
						var v = $("#'.$this->item->name.'").myVal()+"";
						v = v.replace(/,/g, glue);
						/*
						if ($("#'.$this->item->name.'").isMultiple() && $("#alter_match_div").length && $("#alter_match_div").hasClass("hidden-important")) {
							$("#alter_match_div").toggleClass("hidden-important");
						}
						*/
						if ($("#'.$this->item->name.'").isMultiple() && v.indexOf(",") !== -1) {
							if (match_mode=="exact" || match_mode==""){
								$("#alter_match").prop("disabled",false).val("1");
							} else {
								$("#alter_match").prop("disabled",true).val("0");
							}
						} else {
							if (match_mode=="any_exact" || match_mode=="any"){
								$("#alter_match").prop("disabled",false).val("1");
							} else {
								$("#alter_match").prop("disabled",true).val("0");
							}
						}
					}
    			}
				$(document).ready(function(){
					var client = "'.$this->item->title.'";
					var toggle = "'.$toggle.'";
					var glue = ",";
					var fieldtype = "'.$field->type.'";
					var elem = "#'.$this->item->name.'";
					var w = $("#toolbarBox").width()+69;
					var h = $("#layout").height()+54;
					if (w > 300 || h > 200) {
						w = (w > 300) ? w+42 : w;
						parent.jQuery.colorbox.resize({innerWidth:w, innerHeight:h});
					}
					if (client=="search") {
						var match_value = parent.jQuery(elem+"_match_value").val();
						glue = (match_value!="") ? match_value : "'.$separator.'";
						if (toggle=="1") {
							var variation = parent.jQuery(elem+"_variation").val();
							toggle = (variation == "hidden" || variation == "value") ? toggle : "0";
						}
					}
					var elem2 = "#'.$this->item->type.'";
					if (fieldtype != "jform_usergroups") {
						if (toggle=="1") {
							var v = parent.jQuery(elem2).val()+"";
							var reg = new RegExp(glue,"g");
							v = v.replace(reg, ",");
							if (!$(elem).isMultiple() && v.split(",").length > 1) {
								$("#alter_match_div").toggleClass("hidden-important");
								$(elem).toggleMultiple(18);
							}
						} else {
							var v = parent.jQuery(elem2).val();
						}
						if (v != "") {
							$(elem).myVal(v);
						}
					} else {
						$("div.preview ul").attr("id", "'.$this->item->name.'");
						var v2 = parent.jQuery(elem2).val().split(",");
						$(elem+" input").val(v2);
					}
					if (toggle=="1") {
						JCck.Dev.resize(32);
						$("div.toggle-selection").html(\'<a href="#" id="toggle_selection">Toggle</a>\');
						if ($("#'.$this->item->name.'").isMultiple()) {
							JCck.Dev.always = true;
						}
						$("#toggle_selection").on("click", function() {
							if (client=="search" && $("#alter_match_div").length && !JCck.Dev.always) {
								$("#alter_match_div").toggleClass("hidden-important");
							}
							$(elem).toggleMultiple(12);
							if (client=="search" && $("#alter_match_div").length) {
								JCck.Dev.toggleField();
							}
						});
						if (client=="search") {
							$(elem).on("change", function() {
								JCck.Dev.toggleField();
							});
						}
					}
				});
			})(jQuery);
			';
Helper_Include::addDependencies( 'box', 'edit' );
$doc->addStyleSheet( JUri::root( true ).'/media/cck/css/cck.admin.css' );
$doc->addStyleDeclaration( 'div.cck_forms.cck_admin div.cck_form {float:none;}table.DynarchCalendar-topCont{top:0px!important;left:16px!important;} form{margin:0!important;}' );
$doc->addScriptDeclaration( $js );

JText::script( 'COM_CCK_MATCH_ANY_WORDS' );
JText::script( 'COM_CCK_MATCH_ANY_WORDS_EXACT' );
JText::script( 'COM_CCK_MATCH_DEFAULT_PHRASE' );
JText::script( 'COM_CCK_MATCH_EXACT_PHRASE' );
?>

<div class="seblod preview">
	<div align="center" style="text-align:center;">
		<div class="cck_forms cck_admin cck_<?php echo $field->type; ?>">
            <?php if ( $desc ) { ?>
			<div class="cck_desc cck_desc_<?php echo $field->type; ?>">
				<?php echo $desc; ?>
			</div>
            <?php } ?>
			<div class="cck_form cck_form_<?php echo $field->type; ?>">
				<?php echo $form; ?>
			</div>
			<?php if ( $alterMatch ) { ?>
				<div id="alter_match_div" class="hidden-important">
					<?php echo $matchForm; ?>
				</div>
			<?php } ?>
		</div>
	</div>
    <!--<div align="center" style="text-align:center;">
		<?php //echo $form; ?>
	</div>-->
    <?php if ( $toggle ) { ?>
		<div class="toggle-selection"></div>
    <?php } ?>
</div>