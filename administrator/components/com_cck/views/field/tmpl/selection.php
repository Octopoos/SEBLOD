<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: selection.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

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
		$location				=	$this->item->title;
		$prefix					=	JFactory::getConfig()->get( 'dbprefix' );
		if ( strpos( $location, '#__' ) !== false || strpos( $location, $prefix ) !== false ) {
			$properties				=	array( 'table'=>str_replace( $prefix, '#__', $location ) );
		} else {
			$properties			=	array( 'table' );
			if ( $location != '' ) {
				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
				$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );
			}
		}
		if ( isset( $properties['table'] ) && $properties['table'] != '' ) {
			$columns			=	JCckDatabase::loadColumn( 'SHOW COLUMNS FROM '.$properties['table'] );
			if ( count( $columns ) ) {
				natsort( $columns );
				$columns		=	array_combine( $columns, $columns );
			}
			$columns			=	array_merge( array( ''=>'- '.JText::_( 'COM_CCK_SELECT' ).' -' ), $columns );
		} else {
			$columns			=	array( ''=>'- '.JText::_( 'COM_CCK_SELECT' ).' -' );
		}
	}
	$field						=	new stdClass;
	$field->type				=	'select_simple';
	$form						=	JHtml::_( 'select.genericlist', $columns, 'map', 'class="inputbox select" style="max-width:175px;"', 'value', 'text', '', 'map' );
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

$toggle						=	0;
$separator					=	'';
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
				$.fn.toggleMultiple = function() {
					if (this.is("select")) {
						if (this.prop("multiple")) {
							this.prepend(\'<option value="">- '.JText::_( 'COM_CCK_SELECT' ).' -</option>\').prop("multiple",false).attr("size",1);
						} else {
							$("#"+this.attr("id")+" option:eq(0)").remove();
							this.prop("multiple",true).attr("size",15);
						}
						JCck.Dev.resize();
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
					reset: function() {
						var elem = "#'.$this->item->name.'";
						var elem2 = "#'.$this->item->type.'";
						parent.jQuery(elem2).val("");
						this.close();
					},
					resize: function() {
						var h = $("#layout").height()+61;
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
					}
    			}
				$(document).ready(function(){
					var client = "'.$this->item->title.'";
					var toggle = "'.$toggle.'";
					var glue = ",";
					var fieldtype = "'.$field->type.'";
					var elem = "#'.$this->item->name.'";
					var w = $("#toolbarBox").width()+69;
					var h = $("#layout").height()+80;
					if (w > 300 || h > 200) {
						w = (w > 300) ? w+20 : w;
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
								$(elem).toggleMultiple();
							}
						} else {
							var v = parent.jQuery(elem2).val();
						}
						$(elem).myVal(v);
					} else {
						$("div.preview ul").attr("id", "'.$this->item->name.'");
						$(elem+" input").val(parent.jQuery(elem2).val().split(","));
					}
					if (toggle=="1") {
						$("div.toggle-selection").html(\'<a href="#" id="toggle_selection">Toggle</a>\');
						$("#toggle_selection").live("click", function() {
							$(elem).toggleMultiple();
						});
					}
				});
			})(jQuery);
			';
Helper_Include::addDependencies( 'box', 'edit' );
$doc->addStyleSheet( JURI::root( true ).'/media/cck/css/cck.admin.css' );
$doc->addStyleDeclaration( 'div.cck_forms.cck_admin div.cck_form {float:none;}table.DynarchCalendar-topCont{top:0px!important;left:16px!important;}' );
$doc->addScriptDeclaration( $js );
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
		</div>
	</div>
    <!--<div align="center" style="text-align:center;">
		<?php //echo $form; ?>
	</div>-->
    <?php if ( $toggle ) { ?>
		<div class="toggle-selection"></div>
    <?php } ?>
</div>