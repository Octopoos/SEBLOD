<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: dev.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDev
abstract class JCckDev
{
	public static $_urls	=	array();
	
	// addField
	public static function addField( $name, &$config = array( 'doValidation' => 2 ) )
	{
		$config['fields'][]	=	$name;
	}

	// addScript
	public static function addScript( $url, $type = "text/javascript", $defer = false, $async = false )
	{
		$app	=	JFactory::getApplication();

		if ( !isset( $app->cck_document ) ) {
			$app->cck_document	=	array();
		}
		
		// Make sure to have only one inclusion of special external scripts
		if ( strpos( $url, 'http' ) !== false ) {
			$url	=	self::getMergedScript( $url );
			
			if ( $url == '' ) {
				return;
			}
		}
		$app->cck_document['scripts'][$url]['mime']			=	$type;
		$app->cck_document['scripts'][$url]['defer']		=	$defer;
		$app->cck_document['scripts'][$url]['async']		=	$async;
	}

	// addStyleSheet
	public static function addStyleSheet( $url, $type = 'text/css', $media = null, $attribs = array() )
	{
		$app	=	JFactory::getApplication();

		if ( !isset( $app->cck_document ) ) {
			$app->cck_document	=	array();
		}
		$app->cck_document['styleSheets'][$url]['mime']		=	$type;
		if ( is_string( $media ) ) {
			$app->cck_document['styleSheets'][$url]['media']	=	$media;
		}
		if ( count( $attribs) ) {
			$app->cck_document['styleSheets'][$url]['attribs']	=	$attribs;
		}
	}

	// addValidation
	public static function addValidation( $rules, $options, $id = '', &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		
		if ( !$id ) {
			$id	=	'seblod_form';
		}
		if ( empty( $rules ) ) {
			$rules	=	'';
		}
		$root	=	JUri::root( true );
		$rules	=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $rules );
		
		if ( is_object( $options ) ) {
			$bgcolor	=	$options->get( 'validation_background_color', JCck::getConfig_Param( 'validation_background_color', '' ) );
			$color		=	$options->get( 'validation_color', JCck::getConfig_Param( 'validation_color', '' ) );
			$position	=	$options->get( 'validation_position', JCck::getConfig_Param( 'validation_position', 'topRight' ) );
			$scroll		=	( $options->get( 'validation_scroll', JCck::getConfig_Param( 'validation_scroll', 1 ) ) ) ? 'scroll:true' : 'scroll:false';
			if ( $color != '' ) {
				if ( $position == 'inline' && $id != '_' ) {
					$doc->addStyleDeclaration( '#'.$id.' .formError .formErrorContent{color: '.$color.'}' );
				} else {
					$doc->addStyleDeclaration( '.formError .formErrorContent{color: '.$color.'}' );
				}
			}
			if ( $position != 'inline' && $bgcolor != '' ) {
				$css	=	'.formError .formErrorContent{background: '.$bgcolor.'}';
				if ( $position == 'topLeft' || $position == 'topRight' ) {
					$css	.=	'.formError .formErrorArrow{border-color: '.$bgcolor.' transparent transparent transparent;}';
				} else {
					$css	.=	'.formError .formErrorArrow.formErrorArrowBottom{border-color: transparent transparent '.$bgcolor.' transparent;}';
				}
				$doc->addStyleDeclaration( $css );
			}
			$options	=	'{'.$scroll.',promptPosition:"'.$position.'"}';
		} else {
			$options	=	'{}';
		}
		$js				=	( $id == '_' ) ? '' : '$("#'.$id.'").validationEngine('.$options.');';
		$js				=	'jQuery(document).ready(function($){ $.validationEngineLanguage.newLang({'.$rules.'});'.$js.' });';
		
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<link rel="stylesheet" href="'.$root.'/media/cck/css/cck.validation-3.9.0.css" type="text/css" />';
			echo '<script src="'.$root.'/media/cck/js/cck.validation-3.11.1.min.js" type="text/javascript"></script>';
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			$doc->addStyleSheet( $root.'/media/cck/css/cck.validation-3.9.0.css' );
			$doc->addScript( $root.'/media/cck/js/cck.validation-3.11.1.min.js' );
			$doc->addScriptDeclaration( $js );
		}
	}

	// forceStorage
	public static function forceStorage( $value = 'none', $allowed = '' )
	{
		$doc	=	JFactory::getDocument();
		$js		=	'';
		
		if ( $value == 'none' ) {
			if ( $allowed == '' ) {
				$allowed = false;
			}
		} else {
			if ( $allowed == '' ) {
				$allowed = true;
			}
		}

		if ( !$allowed ) {
			$js	=	'$("#storage").val( "'.$value.'" ).prop("disabled", true); $("#force_storage").val( "1" );';
		} else {
			$js	=	'if ( !$("#myid").val() ) { $("#storage").val( "'.$value.'" ); $("#force_storage").val( "1" ); }';
		}
		
		echo '<script type="text/javascript">jQuery(document).ready(function($){'.$js.'});</script>';
	}
	
	public static function getMergedScript( $url )
	{
		$app	=	JFactory::getApplication();
		$base	=	'';
		$index	=	'';
		$pos	=	strpos( $url, '?' );

		$base	=	substr( $url, 0, $pos );
		$index	=	str_replace( array( 'http://', 'https://' ), '', $base );

		if ( isset( self::$_urls[$index] ) ) {
			$cur		=	self::$_urls[$index];
			$cur_vars	=	JCckDevHelper::getUrlVars( $cur )->toArray();
			$new_vars	=	JCckDevHelper::getUrlVars( $url )->toArray();
			$vars		=	array();

			if ( count( $cur_vars ) ) {
				foreach ( $cur_vars as $k=>$v ) {
					if ( isset( $new_vars[$k] ) ) {
						$values	=	array();
						
						if ( $v != '' ) {
							$values[]	=	$v;
						}
						$v2		=	$new_vars[$k];

						if ( $v2 != '' && !in_array( $v2, $values ) ) {
							$values[]	=	$v2;
						}
						$vars[]	=	$k.'='.implode( ',', $values );
						unset( $new_vars[$k] );
					} else {
						$vars[]	=	$k.'='.$v;
					}
				}
			}

			if ( count( $new_vars ) ) {
				foreach ( $new_vars as $k=>$v ) {
					$vars[]	=	$k.'='.$v;
				}
			}
			if ( count( $vars ) ) {
				$url	=	$base.'?'.implode( '&', $vars );
			} else {
				$url	=	$base;
			}

			unset( $app->cck_document['scripts'][$cur] );
		}

		self::$_urls[$index]	=	$url;
		
		return $url;
	}

	// importPlugin
	public static function importPlugin( $type, $plugins )
	{
		if ( count( $plugins ) > 0 ) {
			foreach ( $plugins as $plugin ) {
				JPluginHelper::importPlugin( $type, $plugin );	// todo: improve
			}
		} else {
			JPluginHelper::importPlugin( $type );
		}
	}
	
	// init
	public static function init( $plugins = array(), $core = true, $more = array() )
	{
		self::importPlugin( 'cck_field', $plugins );
		
		$config	=	array( 'asset'=>'',
						   'asset_id'=>0,
						   'client'=>'',
						   'doTranslation'=>1,
						   'doValidation'=>0,
						   'fields'=>array(),
						   'item'=>'',
						   'validation'=>array()
						);
		
		if ( $core === true ) {
			JFactory::getLanguage()->load( 'plg_cck_field_validation_required', JPATH_ADMINISTRATOR, null, false, true );

			$config['doValidation']	=	2;
			require_once JPATH_PLUGINS.'/cck_field_validation/required/required.php';
		}
		$config['id']				=	0;
		$config['pk']				=	0;
		
		if ( count( $more ) ) {
			foreach ( $more as $k => $v ) {
				$config[$k]	=	$v;
			}
		}
		
		return $config;
	}
	
	// initScript
	public static function initScript( $type, &$elem, $options = array() )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		$css	=	'';
		$js		=	'';
		$js2	=	'';
		$js3	=	'';

		if ( $type == 'field' ) {
			if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' ) {
				unset( $options['doTranslation'] );
				unset( $options['hasOptions'] );
			}
			if ( isset( $options['doTranslation'] ) ) {
				if ( is_array( $options['doTranslation'] ) ) {
					$flag		=	'&nbsp;';
					$function	=	'after';
					$selector	=	$options['doTranslation']['id'];
					if ( is_null( $elem->bool8 ) ) {
						$elem->bool8	=	$options['doTranslation']['value'];
					}
				} else {
					$flag		=	'';
					$function	=	'before';
					$selector	=	'sortable_core_options';
					if ( is_null( $elem->bool8 ) ) {
						$elem->bool8	=	$options['doTranslation'];
					}
				}
				if ( $elem->bool8 == 1 ) {
					$c0 	=	'';
					$c1 	=	'checked="checked"';
					$class	=	'publish icon-flag';
				} else {
					$c0		=	'checked="checked"';
					$c1 	=	'';
					$class	=	'unpublish icon-flag';
				}
				$flag	.=	'<a href="javascript: void(0);" id="bool8" class="btn btn-micro jgrid"><span class="hasTooltip state '.$class.'" title="'.JText::_( 'COM_CCK_TRANSLATE_OPTIONS' ).'"></span></a>'
						.	'<input type="radio" id="bool80" name="bool8" value="0" '.$c0.' style="display:none;" />'
						.	'<input type="radio" id="bool81" name="bool8" value="1" '.$c1. ' style="display:none;" />';
				$js2	.=	'$("#'.$selector.'").'.$function.'("'.addslashes( $flag ).'");'
						.	'$("a#bool8 .hasTooltip").tooltip({});'
						.	'$("#bool8").click(function(){ if ( $("#bool80").prop("checked") == true ) {'
						.	'$("#bool8 span").removeClass("unpublish").addClass("publish"); $("#bool81").prop("checked", true); $("#bool80").prop("checked", false); } else {'
						.	'$("#bool8 span").removeClass("publish").addClass("unpublish"); $("#bool81").prop("checked", false); $("#bool80").prop("checked", true); } });';
			}
			if ( isset( $options['hasOptions'] ) && $options['hasOptions'] === true ) {
				$html		=	'';
				if ( isset( $options['customAttr'] ) ) {
					$label		=	isset( $options['customAttrLabel'] ) ? $options['customAttrLabel'] : JText::_( 'COM_CCK_CUSTOM_ATTRIBUTES' );
					$html		.=	'<input type="checkbox" id="toggle_attr" name="toggle_attr" value="1" />'
								.	'<label for="toggle_attr" class="toggle_attr inline">'.$label.'</label>';
					$attribs	=	'';
					
					if ( is_array( $options['customAttr'] ) ) {
						$keys	=	array();
						$js3	=	'var disp = ($("#toggle_attr").prop("checked") !== false) ? \'style="display: block"\' : "";';
						$n		=	0;
						$nb		=	count( $options['customAttr'] );
						foreach ( $options['customAttr'] as $i=>$customAttr ) {
							$attribs	.=	'<div class="clr"></div><div class="attr">'
										.	'<input type="text" id="attr__\'+k+\'" name="json[options2][options][\'+k+\']['.$customAttr.']" value="\'+(val['.$i.'] !== undefined ? val['.$i.'] : \'\' )+\'"'
										.	' class="inputbox mini" size="10" placeholder="'.htmlspecialchars( JText::_( 'COM_CCK_'.$elem->type.'_attr_'.$customAttr ) ).'" />'
										.	'</div>';
							$keys[]		=	$customAttr;
							$js3		.=	'$("#sortable_core_options>div:last input:text[name=\'string[options][]\']").parent().append(\'<div class="clr"></div><div class="attr"\'+disp+\'><input type="text" id="attr__0" name="json[options2][options][\'+('.( $i == ( $nb - 1 ) ? 'cur++' : 'cur' ).')+\']['.$customAttr.']" value="" class="inputbox mini" size="10" /></div>\');';
						}
						$keys		=	implode( ',', $keys );
					} elseif ( $options['customAttr'] ) {
						$js3		=	'var disp = ($("#toggle_attr").prop("checked") !== false) ? \'style="display: block"\' : "";';
						$n			=	(int)$options['customAttr'];
						$attribs	=	'<div class="clr"></div><div class="attr">';
						for ( $i = 0; $i < $n; $i++ ) {
							$css		=	( ( $i + 2 ) % 3 == 0 ) ? ' middle' : '';
							$attribs	.=	'<input type="text" id="attr__\'+k+\'_'.($i + 1).'" name="json[options2][options][\'+k+\'][attr][]" value="\'+val['.$i.']+\'" class="inputbox input-mini mini2'.$css.'" size="8" />';
						}
						$attribs	.=	'</div>';
						$location	=	( $elem->location ) ? explode( '||', $elem->location ) : array( 0=>'', 1=>'', 2=>'' );
						$html		.=	'<div class="clr"></div><div class="attr">';
						for ( $i = 0; $i < $n; $i++ ) {
							$css	=	( ( $i + 2 ) % 3 == 0 ) ? ' middle' : '';
							$html	.=	'<input type="text" id="location'.($i + 1).'" name="string[location][]" class="inputbox input-mini mini2'.$css.'" size="8" value="'.htmlspecialchars( @$location[$i] ).'" />';
						}
						$html		.=	'</div>';
						$js3		.=	'var content = \'<div class="clr"></div><div class="attr"\'+disp+\'>';
						for ( $i = 0; $i < $n; $i++ ) {
							if ( $i == 0 ) {
								$js3	.=	'<input type="text" id="attr__0_1" name="json[options2][options][\'+(++cur)+\'][attr][]" value="" class="inputbox input-mini mini2" size="8" />';
							} else {
								$css	=	( ( $i + 2 ) % 3 == 0 ) ? ' middle' : '';
								$js3	.=	'<input type="text" id="attr__0_1" name="json[options2][options][\'+(cur)+\'][attr][]" value="" class="inputbox input-mini mini2'.$css.'" size="8" />';
							}
						}
						$js3		.=	'</div>\';';
						$keys		=	'';
					}
					if ( !isset( $options['options'] ) ) {
						$options['options']	=	JCckDev::fromJSON( $elem->options2 );
					}
					if ( isset( $options['options']['options'] ) ) {
						$opts	=	json_encode( $options['options']['options'] );
					} else {
						$opts	=	'{}';
					}
					$js		=	'
								var keys = "'.$keys.'";
								var len = 0; var len2 = "'.$n.'";
								if (keys!="") {keys = keys.split(","); len = keys.length;}
								var val = []; for(i=0;i<len2;i++){val[i] = "";}
								var values = $.parseJSON("'.addslashes( $opts ).'");
								if (values.length>0) {
									$("div#sortable_core_options input[name=\'string[options][]\']").each(function(k, v) {
										if (len) {
											if (values[k]) {for(i=0; i<len; i++) {if (values[k][keys[i]] !== undefined) {val[i] = values[k][keys[i]];}}}
										} else {
											if (values[k]) {
												for(i=0;i<len2;i++){if (values[k].attr !== undefined && values[k].attr[i] !== undefined) {val[i] = values[k].attr[i];}}
											}
										}
										$(this).parent().append(\''.$attribs.'\');
									});											
								} else {
									$("div#sortable_core_options input[name=\'string[options][]\']").each(function(k, v) {
										$(this).parent().append(\''.$attribs.'\');
									});	
								}
								';
					$js2	.=	'$("div#layer").on("change", "input#toggle_attr", function() { $("div.attr, #location").toggle(); });';
				}
				if ( isset( $options['fieldPicker'] ) ) {
					$fields	=	JCckDatabase::loadObjectList( 'SELECT a.title as text, a.name as value FROM #__cck_core_fields AS a'
															. ' WHERE a.published = 1 AND a.storage !="dev" AND a.name != "'.$elem->name.'" ORDER BY text' );
					$fields	=	is_array( $fields ) ? array_merge( array( JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_ADD_A_FIELD' ).' -' ) ), $fields ) : array();
					$elem->init['fieldPicker']	=	JHtml::_( 'select.genericlist', $fields, 'fields_list', 'class="inputbox select" style="max-width:175px;"',
															  'value', 'text', '', 'fields_list' );
					$isNew	=	( !$elem->options ) ? 1 : 0;
					$target	=	( is_string( $options['fieldPicker'] ) ) ? $options['fieldPicker'] : 'string[options]';
					$js2	.=	'var cur = 9999; var isNew = '.$isNew.';
								$("ul.adminformlist").on("change", "select#fields_list", function() {
									var val = $(this).val();
									if (val) {
										$("#sortable_core_options>div:last .button-add-core_options").click();
										$("#sortable_core_options>div:last input:text[name=\''.$target.'[]\']").val(val);
										'.$js3.'
									}
									if (isNew) {
										var attr = "input:text[name=\'json\[options2\]\[options\]\[0\]\[direction\]\']";
										if ($(attr).length) { $(attr).remove(); }
									} isNew = 0;
								';
					if ( !$elem->options ) {
						$js2	.=	'if ($("#sortable_core_options").children().length == 2 && $("#collection-group-wrap-core_options__0").length) { $("#collection-group-wrap-core_options__0").parent().remove(); }';
					}
					$js2	.=	'});';
					if ( !$elem->options ) {
						// $js2	.=	'$("#sortable_core_options>div:last .button-add-core_options").click();';
					}
					$css	.=	'.button-add{display:none;}';
					if ( !$elem->options ) {
						$css	.=	'#collection-group-wrap-core_options__0{display:none;}';
					}
					$js3	=	'';
				} else {
					$js3	=	'(function($){ var cur = 9999; $.fn.JCckFieldxAddAfter = function() {'.$js3.' $(this).next().find(".collection-group-form").append(content);} })(jQuery);';
				}
				if ( $html ) {
					$html	=	'<div class="clr"></div><div>'.$html.'</div>';
					$js		=	'if ($("#sortable_core_options")) { '.$js.' $("#sortable_core_options").parent().append("'.addslashes( $html ).'"); }';
				}
			}
			if ( $css ) {
				echo '<style type="text/css">'.$css.'</style>';
			}
			if ( $js || $js2 ) {
				echo '<script type="text/javascript">'.'jQuery(document).ready(function($){'.$js.$js2.'});'.$js3.'</script>';
			}
			
			return;
		}
		if ( $type == 'processing' ) {
			$offset	=	0;
			$path	=	$elem->scriptfile;
			$pos	=	strpos( $path, '.' );

			if ( $path[0] == '/' ) {
				$offset	=	1;
			}
			$path	=	substr( $path, $offset, $pos );
			$path	=	str_replace( '/', '_', $path );

			JFactory::getLanguage()->load( 'files_pro_cck_'.$path.'.sys', JPATH_SITE, null, false, true );
		} else {
			if ( $elem->name ) {
				JFactory::getLanguage()->load( 'plg_cck_field_'.$type.'_'.$elem->name, JPATH_ADMINISTRATOR, null, false, true );
			}
		}
		
		if ( $type == 'validation' ) {
			return;
		}
		$js2		=	'';
		$js3		=	'';
		if ( $type == 'typo' ) {
			$js2	=	'if($("#typo_label").length) { if (parent.jQuery("#"+eid+"_typo_label").val()!="") { $("#typo_label").val(parent.jQuery("#"+eid+"_typo_label").val()); }}';
			$js3	=	'if($("#typo_label").length) { parent.jQuery("#"+eid+"_typo_label").val($("#typo_label").val()); } excluded[0] = "typo_label"';
		}
		if ( !isset( $options['js']['load'] ) ) {
			$options['js']['load']		=	'var eid = "'.$elem->id.'";
											var elem = "'.$elem->id.'_'.$type.'_options";
											if(!parent.jQuery("#"+elem).length) { elem = "'.$elem->id.'"; }
											var encoded = parent.jQuery("#"+elem).val();
											var data = ( encoded !== undefined && encoded != "" ) ? $.evalJSON(encoded) : "";
											if (data) {
												var j = 0;
												$.each(data, function(k, v) {
													if(!$("#"+k).length) {
														if (typeof v === "object") {
															var p = "'.$type.'";
															var $clone = $("#'.$type.'_id").parent().clone().addClass("new").appendTo(".target");
															$("li.new > *").attr("id",p+j).myVal(k).parent().removeClass("new");
															var $clone = $("#'.$type.'_options_id").parent().clone().addClass("new").appendTo(".target");
															$("li.new > *").attr("id",p+j+"_options").myVal($.toJSON(v)).parent().removeClass("new");
														} else {
															var temp = v.split("||");
															var len = temp.length;
															for(i = 0; i < len; i++) {
																if ( i+1 < len ) { $("#sortable_core_dev_texts>div:last .button-add-core_dev_texts").click(); }
																$("[name=\""+k+"\[\]\"]:eq("+i+")").myVal(temp[i]);
															}
														}
													} else {
														$("#"+k).myVal( v );
													}
												});
											}
											'.$js2;
		}
		if ( !isset( $options['js']['reset'] ) ) {
			$options['js']['reset']		=	'var elem = "'.$elem->id.'_'.$type.'_options";
											if(!parent.jQuery("#"+elem).length) { elem = "'.$elem->id.'"; }
											parent.jQuery("#"+elem).val("");
											this.close();';
		}
		if ( !isset( $options['js']['submit'] ) ) {
			$options['js']['submit']	=	'if ( $("#adminForm").validationEngine("validate") === true ) {
												var eid = "'.$elem->id.'";
												var elem = "'.$elem->id.'_'.$type.'_options";
												if(!parent.jQuery("#"+elem).length) { elem = "'.$elem->id.'"; }
												var data = {};
												var excluded = [];
												'.$js3.'
												if (typeof cck_dev != "undefined") {
													$.each(cck_dev, function(k, v) {
														if(jQuery.inArray(v, excluded) == -1) {
															if(!$("#"+v).length) {
																var temp = [];
																$("[name=\""+v+"\[\]\"]").each(function(i) {
																	temp[i] = $(this).val();
																});
																data[v] = temp.join("||");
															} else {
																data[v] = $("#"+v).myVal();
															}
														}
													});
												} else {
													$(".'.$type.'s").each(function(i) {
														var v = $(this).myVal();
														if (v != "") {
															var enc = $(".'.$type.'s_options:eq("+i+")").myVal();
															if (enc == "") {
																enc = "{}";
															}
															var d = $.evalJSON(enc);
															data[v] = d;
														}
													});
												}
												var encoded = $.toJSON(data);
												parent.jQuery("#"+elem).val(encoded);
												this.close();
												return;
											}';
		}
		$js	=	'
				(function ($){
					JCck.Dev = {
						reset: function() {'.$options['js']['reset'].'},
						submit: function() {'.$options['js']['submit'].'}
					}
					$(document).ready(function(){'.$options['js']['load'].'});
				})(jQuery); 
			';
		
		$doc->addScriptDeclaration( $js );
	}
	
	// preload
	public static function preload( $fieldnames )
	{
		$preload	=	array();
		$fields_in	=	implode( '","', $fieldnames );
		$fields		=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name IN ("'.$fields_in.'")', 'name' ); //#
		
		foreach ( $fieldnames as $f ) {
			$preload[$f]	=	( isset( $fields[$f] ) ) ? $fields[$f] : $f;
		}
		
		return $preload;
	}
	
	// validate
	public static function validate( $config, $id = 'adminForm' )
	{
		$config['validation']			=	count( $config['validation'] ) ? implode( ',', $config['validation'] ) : '"null":{}';
		$config['validation_options']	=	new JRegistry( array( 'validation_background_color'=>'#242424', 'validation_color'=>'#ffffff', 'validation_position'=>'topRight', 'validation_scroll'=>0 ) );
		
		self::addValidation( $config['validation'], $config['validation_options'], $id );
		
		if ( isset( $config['fields'] ) && count( $config['fields'] ) ) {
			JFactory::getDocument()->addScriptDeclaration( 'var cck_dev = '.json_encode( $config['fields'] ).';' );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Fields & Markup
	
	// get
	public static function get( $field, $value, &$config = array( 'doValidation' => 2 ), $override = array(), $inherit = array() )
	{
		return JCckDevField::get( $field, $value, $config, $inherit, $override );
	}
	
	// getEmpty
	public static function getEmpty( $properties )
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		$field	=	JTable::getInstance( 'Field', 'CCK_Table' );
		
		if ( is_array( $properties ) ) {
			foreach ( $properties as $k => $v ) {
				$field->$k	=	$v;
			}
		}
		
		return $field;
	}
	
	// getForm
	public static function getForm( $field, $value, &$config = array( 'doValidation' => 2 ), $override = array(), $inherit = array() )
	{
		$field	=	JCckDevField::get( $field, $value, $config, $inherit, $override );
		if ( ! $field ) {
			return '';
		}
		
		$config['fields'][]	=	$field->storage_field;
		$html				=	( isset( $field->form ) ) ? $field->form : '';
		if ( isset( $inherit['after'] ) ) {
			$html			.=	$inherit['after'];
		}
		
		return $html;
	}
	
	// renderForm
	public static function renderForm( $field, $value, &$config = array( 'doValidation' => 2 ), $override = array(), $inherit = array(), $class = '' )
	{	
		$field	=	JCckDevField::get( $field, $value, $config, $inherit, $override );
		if ( ! $field ) {
			return '';
		}
		
		$config['fields'][]	=	$field->storage_field;
		$tag				=	( $field->required ) ? '<span class="star"> *</span>' : '';
		$class				=	( $class ) ? ' class="'.$class.'"' : '';
		$html				=	( isset( $field->form ) ) ? $field->form : '';
		if ( isset( $inherit['after'] ) ) {
			$html			.=	$inherit['after'];
		}
		$label				=	'';
		if ( $field->label ) {
			$label			=	'<label>'.$field->label.$tag.'</label>';
		}
		$html				=	'<li'.$class.'>'.$label.$html.'</li>';
		
		return $html;
	}
	
	// renderBlank
	public static function renderBlank( $html = '', $label = '' )
	{
		$app	=	JFactory::getApplication();

		if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' ) {
			return;
		}

		return '<li><label>'.$label.'</label>'.$html.'</li>';
	}
	
	// renderHelp
	public static function renderHelp( $type, $url = '' )
	{
		if ( !$url ) {
			return;
		}
		
		$app	=	JFactory::getApplication();
		$raw	=	false;

		if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' ) {
			return;
		}

		switch ( $type ) {
			case 'addon':
				$raw	=	true;
				break;
			case 'link':
			case 'live':
			case 'typo':
				$type	=	'plugin';
				break;
			case 'validation':
				$type	=	'plugin';
				$raw	=	true;
				break;
			default:
				break;
		}
		
		$app->cck_markup_closed	=	true;
		
		$link	=	'https://www.seblod.com/resources/manuals/archives/'.$url.'?tmpl=component';
		$opts	=	'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=685,height=600';
		$help	=	'<div class="clr"></div><div class="how-to-setup">'
				.	'<a href="'.$link.'" onclick="window.open(this.href, \'targetWindow\', \''.$opts.'\'); return false;">' . JText::_( 'COM_CCK_HOW_TO_SETUP_THIS_'.$type ) . '</a>'
				.	'</div>';
		
		return ( $raw !== false ) ? $help : '</ul>'.$help.'</div>';
	}
	
	// renderLegend
	public static function renderLegend( $legend, $tooltip = '', $tag = '1' )
	{
		$app	=	JFactory::getApplication();
		
		if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' ) {
			return;
		}

		if ( $tooltip != '' ) {
			$tag		=	'<span class="star"> &sup'.$tag.';</span>';
			$tooltip	=	' class="hasTooltip qtip_cck" title="'.$tooltip.'"';
		} else {
			$tag		=	'';
			$tooltip	=	'';
		}
		
		return '<div class="legend top left"><span'.$tooltip.'>'.$legend.$tag.'</span></div>';
	}
	
	// renderSpacer
	public static function renderSpacer( $legend, $tooltip = '', $tag = '2', $options = array( 'class_sfx'=>'-2cols' ) )
	{
		$app	=	JFactory::getApplication();
		
		if ( $app->input->get( 'option' ) == 'com_cck' && $app->input->get( 'view' ) == 'form' ) {
			return;
		}

		if ( isset( $app->cck_markup_closed ) && $app->cck_markup_closed === true ) {
			$close					=	'';
			$app->cck_markup_closed	=	false;
		} else {
			$close	=	'</ul></div>';
		}

		$note	=	'';

		if ( $legend == JText::_( 'COM_CCK_STORAGE' ) ) {
			$note	=	'<span class="storage-desc content-type">'.JText::_( 'COM_CCK_STORAGE_CONTENT_TYPE_FIELD_DESC' ).'</span>'
					.	'<span class="storage-desc search-type">'.JText::_( 'COM_CCK_STORAGE_SEARCH_TYPE_FIELD_DESC' ).'</span>';
		}
		if ( $tooltip ) {
			$legend	=	'<span class="hasTooltip qtip_cck" title="'.$tooltip.'">'.$legend.'<span class="star"> &sup'.$tag.';</span></span>';
		}
		
		return $close.'<div class="seblod"><div class="legend top left">'.$legend.$note.'</div><ul class="adminformlist adminformlist'.$options['class_sfx'].'">';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Variables Manipulation
	
	// fromJSON
	public static function fromJSON( $data = '', $format = 'array' )
	{
		if ( ! $data || ! is_string( $data )  ) {
			return ( $format == 'array' ) ? array() : new stdClass;
		}
		
		$method		=	'to'.ucfirst( $format );
		$registry	=	new JRegistry;
		$registry->loadString( $data, 'JSON' );
		
		return $registry->$method();
	}
	
	// toJSON
	public static function toJSON( $data = '' )
	{
		$registry	=	new JRegistry;
		$registry->loadArray( $data );

		return $registry->toString();
	}
	
	// fromSTRING
	public static function fromSTRING( $data = '', $glue = '||', $format = 'array' )
	{
		// todo: object
		if ( ! $data || ! is_string( $data )  ) {
			return ( $format == 'array' ) ? array() : new stdClass;
		}
		
		return ( $glue != '' ) ? explode( $glue, $data ) : array( $data );
	}
	
	// toSTRING
	public static function toSTRING( $data = '', $glue = '||' )
	{
		// todo: object
		if ( ! is_array( $data ) ) {
			return '';
		}
		
		return implode( $glue, $data );
	}

	// toSafeID
	public static function toSafeID( $string )
	{
		$str	=	str_replace( array( '&', '"', '<', '>' ), array( 'a', 'q', 'l', 'g' ), $string );
		$str	=	trim( preg_replace( array( '/\s+/', '/[^A-Za-z0-9_]/' ), array( '_', '' ), $str ) );
		
		return $str;
	}
	
	// toSafeSTRING
	public static function toSafeSTRING( $string, $char = '_', $case = 0 )
	{
		$len	=	strlen( $char );
		if ( $len > 1 ) {
			$chars	=	'';
			for ( $i = 0; $i < $len; $i++ ) {
				$chars	.=	'\\'.$char[$i];
			}
			$char	=	$chars[1];
			$str	=	str_replace( $char, ' ', $string );
			if ( $case != 2 ) {
				$str	=	JFactory::getLanguage()->transliterate( $str );	
			}
			$str	=	preg_replace( array( '/\s+/', '/[^A-Za-z0-9'.$chars.']/' ), array( $char, '' ), $str );
		} else {
			$str	=	str_replace( $char, ' ', $string );
			if ( $case != 2 ) {
				$str	=	JFactory::getLanguage()->transliterate( $str );
			}
			$str	=	preg_replace( array( '/\s+/', '/[^A-Za-z0-9'.$char.']/' ), array( $char, '' ), $str );
		}
		if ( $case == 1 ) {
			$str	=	strtoupper( $str );
		} elseif ( $case == 0 ) {
			$str	=	strtolower( $str );
		}
		$str		=	trim( $str );

		return $str;
	}
	
	// fromXML
	public static function fromXML( $data = '', $isFile = true )
	{
		libxml_use_internal_errors( true );
		
		if ( $isFile ) {
			$xml	=	simplexml_load_file( $data, 'JCckDevXml' );
		} else {
			$xml	=	simplexml_load_string( $data, 'JCckDevXml' );
		}
		
		if ( empty( $xml ) ) {
			JError::raiseWarning( 100, JText::_( 'JLIB_UTIL_ERROR_XML_LOAD' ) );
			
			if ( $isFile ) {
				JError::raiseWarning( 100, $data );
			}
			foreach ( libxml_get_errors() as $error ) {
				JError::raiseWarning( 100, 'XML: ' . $error->message );
			}
		}
		
		return $xml;
	}
}
?>