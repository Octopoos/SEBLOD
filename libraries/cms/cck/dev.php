<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: dev.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDev
abstract class JCckDev
{
	public static $_urls	=	array();

	// _getField
	protected static function _getField( $caller, $value, &$config = array( 'doValidation' => 2 ), $override = array(), $inherit = array() )
	{
		// Check & and Trigger fallback if needed
		$class		=	'Helper_Form';
		$fallback	=	false;
		$method		=	$caller['function'];

		if ( $caller['component'] == 'com_cck' ) {
			$class	=	'CommonHelper_Form';
			$path	=	JPATH_ADMINISTRATOR.'/components/'.$caller['component'].'/helpers/common/form.php';
		} else {
			$path	=	JPATH_ADMINISTRATOR.'/components/'.$caller['component'].'/helpers/helper_form.php';
		}
		if ( is_file( $path ) ) {
			require_once $path;
		} else {
			$fallback	=	true;
		}
		if ( !$fallback ) {
			if ( !method_exists( $class, $method ) ) {
				$fallback	=	true;
			}
		}
		if ( $fallback ) {
			return JCckDev::getForm( $caller['name'], $value, $config, $override, $inherit );
		}

		// Continue
		if ( !( isset( $override['storage_field'] ) && $override['storage_field'] ) ) {
			$override['storage_field']	=	$caller['name'];
		}

		$field	=	JCckDevField::get( 'core_dev_text', $value, $config, $inherit, $override, 'initialize' );
		if ( ! $field ) {
			return '';
		}

		$name	=	$field->storage_field;
		
		if ( isset( $config['inherit'] ) ) {
			if ( strpos( $name, '[' ) !== false ) {
				$parts				=	explode( '[', $name );
				$inherit['name']	=	$config['inherit'].'['.$parts[0].']['.$parts[1];
			} else {
				$inherit['name']	=	$config['inherit'].'['.$name.']';
			}
		} else {
			if ( ! isset( $inherit['name'] ) ) {
				$inherit['name']	=	$name;
			}
		}
		if ( ! isset( $inherit['id'] ) ) {
			$inherit['id']		=	str_replace( array('[', ']'), array('_', ''), $name );
		}
		
		// --
		JCckPluginField::g_onCCK_FieldPrepareForm( $field, $config );

		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( trim( $field->selectlabel ) ) {
			if ( $config['doTranslation'] ) {
				$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
			}
		}

		// Set
		$field->form	=	$class::$method( $field, $value, $name, $id, $config );
		$field->value	=	$value;

		if ( $field->script ) {
			JCckPluginField::g_addScriptDeclaration( $field->script );
		}
		
		if ( $field->required ) {
			if ( trim( $field->label ) == '' ) {
				$field->required	=	'';
			}
		}

		// --

		return $field;
	}

	// aa
	public static function aa( $data, $note = '' )
	{
		if ( !is_string( $data ) ) {
			$data	=	json_encode( $data );
		}
		if ( $note == '' ) {
			$note	=	JFactory::getDate()->toSql();
		}

		JCckTable::getInstance( '#__aa' )->save( array( 'data'=>$data, 'note'=>$note ) );
	}
	
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
		$lang	=	JFactory::getLanguage();

		if ( !$id ) {
			$id	=	'seblod_form';
		}
		if ( empty( $rules ) ) {
			$rules	=	'';
		}

		$message	=	'';
		$root		=	JUri::root( true );
		$rules		=	str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $rules );

		if ( $lang->hasKey( 'COM_CCK_PLEASE_CHECK_REQUIRED_TABS' ) ) {
			$message	=	JText::_( 'COM_CCK_PLEASE_CHECK_REQUIRED_TABS' );
		}		

		$rules	.=	',"_tabs":{"regex":"","alertText":"'.$message.'"}';

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
			$more		=	'';
			if ( $options->def( 'prettySelect' ) ) {
				$more	.=	',prettySelect:'.( $options->get( 'prettySelect' ) ? 'true' : 'false' );
			}
			if ( $options->def( 'useSuffix' ) ) {
				$more	.=	',useSuffix:"'.$options->get( 'useSuffix' ).'"';
			}
			$options	=	'{'.$scroll.',promptPosition:"'.$position.'"'.$more.'}';
		} else {
			$options	=	'{}';
		}
		$js				=	( $id == '_' ) ? '' : '$("#'.$id.'").validationEngine('.$options.');';
		$js				=	'jQuery(document).ready(function($){ $.validationEngineLanguage.newLang({'.$rules.'});'.$js.' });';
		
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<link rel="stylesheet" href="'.$root.'/media/cck/css/cck.validation-3.18.3.css" type="text/css" />';
			echo '<script src="'.$root.'/media/cck/js/cck.validation-3.18.4.min.js" type="text/javascript"></script>';
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			$doc->addStyleSheet( $root.'/media/cck/css/cck.validation-3.18.3.css' );
			$doc->addScript( $root.'/media/cck/js/cck.validation-3.18.4.min.js' );
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
				JPluginHelper::importPlugin( $type, $plugin );	/* TODO#SEBLOD: improve */
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
			if ( !isset( $options['root'] ) ) {
				$options['base']	=	'core_options';
				$options['picker']	=	'core_options_fields_list';
				$options['root']	=	'#sortable_'.$options['base'];
			} else {
				$options['base']	=	$options['root'];
				$options['picker']	=	$options['base'].'_fields_list';
				$options['root']	=	'#sortable_'.$options['base'];
			}
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
					$selector	=	substr( $options['root'], 1 );
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

				if ( !isset( $options['toggleAttr'] ) ) {
					$options['toggleAttr']	=	true;
				}
				if ( isset( $options['parent'] ) && $options['parent'] ) {
					$attr_class	=	'';
					$attr_name	=	substr( $options['parent'], 0, -1 ).'_params]';
					$attr_size	=	'24';
					$opt_name	=	$options['parent'];
				} else {
					$attr_class	=	' mini';
					$attr_name	=	'json[options2][options]';
					$attr_size	=	'11';
					$opt_name	=	'string[options]';
				}

				if ( isset( $options['customAttr'] ) ) {
					$attribs	=	'';
					
					if ( $options['toggleAttr'] ) {
						$label		=	isset( $options['customAttrLabel'] ) ? $options['customAttrLabel'] : JText::_( 'COM_CCK_CUSTOM_ATTRIBUTES' );
						$html		.=	'<input type="checkbox" id="toggle_attr" name="toggle_attr" value="1" />'
									.	'<label for="toggle_attr" class="toggle_attr inline">'.$label.'</label>';
						$js3		=	'var disp = ($("#toggle_attr").prop("checked") !== false) ? \'style="display: block"\' : "";';
					} else {
						$js3		=	'var disp = "";';
					}

					// Custom Attr
					if ( is_array( $options['customAttr'] ) ) {
						$keys	=	array();
						$n		=	0;
						$nb		=	count( $options['customAttr'] );
						foreach ( $options['customAttr'] as $i=>$customAttr ) {
							$attribs	.=	'<div class="clr"></div><div class="attr">';

							if ( is_array( $customAttr ) ) {
								$attr_id	=	$customAttr['id'];

								if ( isset( $customAttr['label'] ) && $customAttr['label'] ) {
									$attribs	.=	'<span>'.$customAttr['label'].'</span>';
								}
								if ( isset( $customAttr['form'] ) && $customAttr['form'] ) {
									$default_value	=	isset( $customAttr['default'] ) && $customAttr['default'] ? $customAttr['default'] : '';
									$attribs		.=	'<select id="attr__\'+k+\'_'.$attr_id.'" name="'.$attr_name.'[\'+k+\']['.$attr_id.']" class="inputbox adminformlist-minwidth'.$attr_class.'" data-value="\'+(val['.$i.'] !== undefined ? val['.$i.'] : \''.$default_value.'\' )+\'">'.$customAttr['form']['options'].'</select>';
								} else {
									if ( isset( $customAttr['placeholder'] ) && $customAttr['placeholder'] ) {
										$placeholder	=	$customAttr['placeholder'];
									} else {
										$placeholder	=	JText::_( 'COM_CCK_'.$elem->type.'_attr_'.$attr_id );
									}
									if ( isset( $customAttr['size'] ) && $customAttr['size'] ) {
										$size	=	$customAttr['size'];
									} else {
										$size	=	$attr_size;
									}

									$attribs		.=	'<input type="text" id="attr__\'+k+\'" name="'.$attr_name.'[\'+k+\']['.$attr_id.']" value="\'+(val['.$i.'] !== undefined ? val['.$i.'] : \'\' )+\'"'
													.	' class="inputbox'.$attr_class.'" size="'.$size.'" placeholder="'.htmlspecialchars( $placeholder ).'" />';									
								}
							} else {
								$attr_id	=	$customAttr;

								$attribs	.=	'<input type="text" id="attr__\'+k+\'" name="'.$attr_name.'[\'+k+\']['.$attr_id.']" value="\'+(val['.$i.'] !== undefined ? val['.$i.'] : \'\' )+\'"'
											.	' class="inputbox'.$attr_class.'" size="'.$attr_size.'" placeholder="'.htmlspecialchars( JText::_( 'COM_CCK_'.$elem->type.'_attr_'.$attr_id ) ).'" />';
							}

							$attribs		.=	'</div>';

							if ( isset( $customAttr['form'] ) ) {
								$default_value	=	isset( $customAttr['default'] ) && $customAttr['default'] ? $customAttr['default'] : '';
								$attribs_append	=	'<select id="attr__\'+cur+\'" name="'.$attr_name.'[\'+('.( $i == ( $nb - 1 ) ? 'cur++' : 'cur' ).')+\']['.$attr_id.']" class="inputbox adminformlist-minwidth'.$attr_class.'" data-value="'.$default_value.'">'.$customAttr['form']['options'].'</select>';
							} else {
								if ( isset( $customAttr['size'] ) && $customAttr['size'] ) {
									$size	=	$customAttr['size'];
								} else {
									$size	=	$attr_size;
								}
								$attribs_append	=	'<input type="text" id="attr__0" name="'.$attr_name.'[\'+('.( $i == ( $nb - 1 ) ? 'cur++' : 'cur' ).')+\']['.$attr_id.']" value="" class="inputbox'.$attr_class.'" size="'.$size.'" />';
							}
							$keys[]			=	$attr_id;
							$js3			.=	'$("'.$options['root'].'>div:last input:text[name=\''.$opt_name.'[]\']").parent().append(\'<div class="clr"></div><div class="attr"\'+disp+\'>'.$attribs_append.'</div>\');';
						}
						$keys		=	implode( ',', $keys );
					} elseif ( $options['customAttr'] ) {
						$n			=	(int)$options['customAttr'];
						$attribs	=	'<div class="clr"></div><div class="attr">';
						for ( $i = 0; $i < $n; $i++ ) {
							$css		=	( ( $i + 2 ) % 3 == 0 ) ? ' middle' : '';
							$attribs	.=	'<input type="text" id="attr__\'+k+\'_'.($i + 1).'" name="'.$attr_name.'[\'+k+\'][attr][]" value="\'+val['.$i.']+\'" class="inputbox input-mini mini2'.$css.'" size="8" />';
						}
						$attribs	.=	'</div>';
						$location	=	( $elem->location ) ? explode( '||', $elem->location ) : array( 0=>'', 1=>'', 2=>'' );
						$html		.=	'<div class="clr"></div><div class="attr">';
						for ( $i = 0; $i < $n; $i++ ) {
							$css	=	( ( $i + 2 ) % 3 == 0 ) ? ' middle' : '';
							$html	.=	'<input type="text" id="location'.($i + 1).'" name="string[location][]" class="inputbox input-mini mini2'.$css.'" size="8" value="'.( isset( $location[$i] ) ? htmlspecialchars( $location[$i] ) : '' ).'" />';
						}
						$html		.=	'</div>';
						$js3		.=	'var content = \'<div class="clr"></div><div class="attr"\'+disp+\'>';
						for ( $i = 0; $i < $n; $i++ ) {
							if ( $i == 0 ) {
								$js3	.=	'<input type="text" id="attr__0_1" name="'.$attr_name.'[\'+(++cur)+\'][attr][]" value="" class="inputbox input-mini mini2" size="8" />';
							} else {
								$css	=	( ( $i + 2 ) % 3 == 0 ) ? ' middle' : '';
								$js3	.=	'<input type="text" id="attr__0_1" name="'.$attr_name.'[\'+(cur)+\'][attr][]" value="" class="inputbox input-mini mini2'.$css.'" size="8" />';
							}
						}
						$js3		.=	'</div>\';';
						$keys		=	'';
					} else {
						$js3		=	'';
					}

					if ( !isset( $options['options'] ) ) {
						$json	=	JCckDev::fromJSON( $elem->options2 );

						if ( isset( $json['options'] ) ) {
							$options['options']	=	$json['options'];
						} else {
							$options['options']	=	null;
						}
					}
					if ( isset( $options['options'] ) ) {
						$opts	=	json_encode( $options['options'] );
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
									$("div'.$options['root'].' input[name=\''.$opt_name.'[]\']").each(function(k, v) {
										if (len) {
											if (values[k]) {for(i=0; i<len; i++) {if (values[k][keys[i]] !== undefined) {val[i] = values[k][keys[i]];}}}
										} else {
											if (values[k]) {
												for(i=0;i<len2;i++){if (values[k].attr !== undefined && values[k].attr[i] !== undefined) {val[i] = values[k].attr[i];}}
											}
										}
										var $p = $(this).parent();
										$p.append(\''.$attribs.'\');
										$("#"+$p.attr("id")+" select").each(function(k, v) {
											console.log($(this));

											if ($(this).attr("data-value") != "") {
												$(this).myVal($(this).attr("data-value"));
											}
										});
									});											
								} else {
									$("div'.$options['root'].' input[name=\''.$opt_name.'[]\']").each(function(k, v) {
										$(this).parent().append(\''.$attribs.'\');
									});	
								}
								';
					if ( $options['toggleAttr'] ) {
						$js2	.=	'$("div#layer").on("change", "input#toggle_attr", function() { $("div.attr, #location").toggle(); });';
					}
				}

				// Field Picker
				if ( isset( $options['fieldPicker'] ) && $options['fieldPicker'] ) {
					$fields	=	JCckDatabase::loadObjectList( 'SELECT a.title as text, a.name as value FROM #__cck_core_fields AS a'
															. ' WHERE a.published = 1 AND a.storage !="dev" AND a.name != "'.$elem->name.'" ORDER BY text' );
					$fields	=	is_array( $fields ) ? array_merge( array( JHtml::_( 'select.option', '', '- '.JText::_( 'COM_CCK_ADD_A_FIELD' ).' -' ) ), $fields ) : array();
					$elem->init['fieldPicker']	=	JHtml::_( 'select.genericlist', $fields, $options['picker'], 'class="inputbox select" style="max-width:175px;"',
															  'value', 'text', '', $options['picker'] );
					$isNew	=	( !$elem->options ) ? 1 : 0;
					$js2	.=	'/*var cur = 9999;*/ var cur = $("'.$options['root'].'").children().length; var isNew = '.$isNew.';
								$("ul.adminformlist").on("change", "select#'.$options['picker'].'", function() {
									var val = $(this).val();
									if (val) {
										$("'.$options['root'].'>div:last .button-add-'.$options['base'].'").click();
										$("'.$options['root'].'>div:last input:text[name=\''.$opt_name.'[]\']").val(val);
										'.$js3.'
									}
									if (isNew) {
										var attr = "input:text[name=\'json\[options2\]\[options\]\[0\]\[direction\]\']";
										if ($(attr).length) { $(attr).remove(); }
									} isNew = 0;
								';
					if ( !$elem->options ) {
						$js2	.=	'if ($("'.$options['root'].'").children().length == 2 && $("#collection-group-wrap-'.$options['base'].'__0").length) { $("#collection-group-wrap-core_options__0").parent().remove(); }';
					}
					$js2	.=	'});';
					$css	.=	( isset( $options['root'] ) ? $options['root'].' ' : '' ).'.button-add{display:none;}';

					if ( !$elem->options ) {
						$css	.=	'#collection-group-wrap-'.$options['base'].'__0{display:none;}';
					}
					$js3	=	'';
				} else {
					// $js3	=	'(function($){ var cur = 9999; $.fn.JCckFieldxAddAfter = function() {'.$js3.' $(this).next().find(".collection-group-form").append(content);} })(jQuery);';
					if ( $type == 'field' ) {
						$js3	=	'(function($){ /* */ })(jQuery);';
					}
				}
				if ( $html ) {
					$html	=	'<div class="clr"></div><div>'.$html.'</div>';
					$js		=	'if ($("'.$options['root'].'")) { '.$js.' $("'.$options['root'].'").parent().append("'.addslashes( $html ).'"); }';
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
					};
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

	// getFormFromHelper
	public static function getFormFromHelper( $caller, $value, &$config = array( 'doValidation' => 2 ), $override = array(), $inherit = array() )
	{
		$field				=	self::_getField( $caller, $value, $config, $override, $inherit );
		
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

	// renderFormFromHelper
	public static function renderFormFromHelper( $caller, $value, &$config = array( 'doValidation' => 2 ), $override = array(), $inherit = array(), $class = '' )
	{
		$field				=	self::_getField( $caller, $value, $config, $override, $inherit );
		
		$class				=	( $class ) ? ' class="'.$class.'"' : '';
		$config['fields'][]	=	$field->storage_field;
		$html				=	( isset( $field->form ) ) ? $field->form : '';

		if ( isset( $inherit['after'] ) ) {
			$html			.=	$inherit['after'];
		}

		$label				=	'';
		$tag				=	( $field->required ) ? '<span class="star"> *</span>' : '';
		
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
				.	'<a href="'.$link.'" onclick="window.open(this.href, \'targetWindow\', \''.$opts.'\'); return false;" rel="noopener noreferrer">' . JText::_( 'COM_CCK_HOW_TO_SETUP_THIS_'.$type ) . '</a>'
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
			$tag		=	( $tag == '1' || $tag == '2' ) ? ' &sup'.$tag.';' : ' <sup style="font-size:10px;">'.$tag.'</sup>';
			$tag		=	'<span class="star">'.$tag.'</span>';
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
		/* TODO#SEBLOD: object */
		if ( ! $data || ! is_string( $data )  ) {
			return ( $format == 'array' ) ? array() : new stdClass;
		}
		
		return ( $glue != '' ) ? explode( $glue, $data ) : array( $data );
	}
	
	// toSTRING
	public static function toSTRING( $data = '', $glue = '||' )
	{
		/* TODO#SEBLOD: object */
		if ( ! is_array( $data ) ) {
			return '';
		}
		
		return implode( $glue, $data );
	}

	// toSafeID
	public static function toSafeID( $string )
	{
		$string	=	str_replace( array( '&', '"', '<', '>', '-' ), array( 'a', 'q', 'l', 'g', '_' ), $string );
		$str	=	JFactory::getLanguage()->transliterate( $string );
		$length	=	strlen( $str );

		if ( $length ) {
			for ( $i = 0; $i < $length; $i++ ) {
				$n	=	ord( $string[$i] );
				
				if ( $n >= 65 && $n <= 90 )	{
					$str[$i]	=	$string[$i];
				}
			}
		}

		$str	=	trim( preg_replace( array( '/\s+/', '/[^A-Za-z0-9_]/' ), array( '_', '' ), $str ) );
		
		return trim( $str, '_' );
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