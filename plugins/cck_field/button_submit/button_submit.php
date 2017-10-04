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

// Plugin
class plgCCK_FieldButton_Submit extends JCckPluginField
{
	protected static $type		=	'button_submit';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}

		if ( isset( $data['json']['options2']['task'] ) ) {
			$data['json']['options2']['task_id']		=	'';
			$task										=	str_replace( '_ajax', '', $data['json']['options2']['task'] );
			if ( $task == 'export' || $task == 'process' ) {
				$data['json']['options2']['task_id']	=	$data['json']['options2']['task_id_'.$task];
				unset( $data['json']['options2']['task_id_export'] );
				unset( $data['json']['options2']['task_id_process'] );
			}
		}
		parent::g_onCCK_FieldConstruct( $data );
	}

	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['computation']	=	NULL;
		$data['live']			=	NULL;
		$data['validation']		=	NULL;

		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']	=	array(
										'hidden'=>JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
										'value'=>JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
										'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
										''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
										'disabled'=>JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED2' ) ),
										'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
										'102'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TOOLBAR' ) ),
										'toolbar_button'=>JHtml::_( 'select.option', 'toolbar_button', JText::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
										'103'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
									);
			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['live']		=	NULL;
		$data['match_mode']	=	NULL;
		$data['validation']	=	NULL;

		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']	=	array(
										'hidden'=>JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
										'value'=>JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
										'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
										''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
										'disabled'=>JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED2' ) ),
										'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
										'102'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TOOLBAR' ) ),
										'toolbar_button'=>JHtml::_( 'select.option', 'toolbar_button', JText::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
										'103'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
									);
			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );

		// Init
		$id				=	$field->name;
		$form_id		=	$field->name.'_form';
		$name			=	$field->name;
		$value			=	$field->label;
		$field->label	=	'';

		// Prepare
		$pre_task	=	'';
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$task		=	( isset( $options2['task'] ) && $options2['task'] ) ? $options2['task'] : 'save';
		$task_auto	=	( isset( $options2['task_auto'] ) && $options2['task_auto'] == '0' ) ? 0 : 1;
		$task_id	=	( isset( $options2['task_id'] ) && $options2['task_id'] ) ? $options2['task_id'] : 0;
		
		if ( $task_id ) {
			$pre_task	=	htmlspecialchars( 'jQuery("#'.$form_id.'").append(\'<input type="hidden" name="tid" value="'.$task_id.'">\');' );
		}
		$class		=	'button btn' . ( $field->css ? ' '.$field->css : '' );
		
		if ( $task == 'export' || $task == 'process' ) {
			$click	=	'';
		} else {
			echo 'This task is not supported on the Content view.';

			$field->html	=	'';
			$field->value	=	'';

			return;
		}
		
		$attr		=	'class="'.$class.'"'.$click . ( $field->attributes ? ' '.$field->attributes : '' );
		if ( $field->bool ) {
			$label	=	$value;
			
			if ( $field->bool6 == 3 ) {
				$label		=	'<span class="icon-'.$options2['icon'].'"></span>';
				$attr		.=	' title="'.$value.'"';
			} elseif ( $field->bool6 == 2 ) {
				$label		=	$value."\n".'<span class="icon-'.$options2['icon'].'"></span>';
			} elseif ( $field->bool6 == 1 ) {
				$label		=	'<span class="icon-'.$options2['icon'].'"></span>'."\n".$value;
			}
			$type	=	( $field->bool7 == 1 || !$click ) ? 'submit' : 'button';
			$form	=	'<button type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$attr.'>'.$label.'</button>';
			$tag	=	'button';
		} else {
			$form	=	'<input type="submit" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
			$tag	=	'input';
		}

		if ( $form != '' ) {
			$uri	=	JUri::getInstance()->toString();
			$form	=	'<form action="'.JRoute::_( 'index.php?option=com_cck' ).'" autocomplete="off" enctype="multipart/form-data" method="post" id="'.$form_id.'" name="'.$form_id.'">'
					.	$form
					.	'<input type="hidden" name="task" value="'.$task.'" />'
					.	'<input type="hidden" name="cid" value="'.$config['id'].'">'
					.	'<input type="hidden" name="return" value="'.base64_encode( $uri ).'">'
					.	'<input type="hidden" name="tid" value="'.$task_id.'">'
					.	JHtml::_( 'form.token' )
					.	'</form>';
		}

		// Set
		$field->html	=	$form;
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path		=	parent::g_getPath( self::$type.'/' );
		$field->label2	=	trim( @$field->label2 );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value			=	$field->label;
		$field->label	=	'';

		// Prepare
		$pre_task	=	'';
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$task		=	( isset( $options2['task'] ) && $options2['task'] ) ? $options2['task'] : 'save';
		$task_auto	=	( isset( $options2['task_auto'] ) && $options2['task_auto'] == '0' ) ? 0 : 1;
		$task_id	=	( isset( $options2['task_id'] ) && $options2['task_id'] ) ? $options2['task_id'] : 0;

		if ( JFactory::getApplication()->isClient( 'administrator' ) ) {
			$task	=	( $config['client'] == 'admin' ) ? 'form.'.$task : 'list.'.$task;
		}
		if ( $task_id ) {
			$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="tid" value="'.$task_id.'">\');' );
		}
		$class		=	'button btn' . ( $field->css ? ' '.$field->css : '' );
		if ( $task == 'cancel' ) {
			$click	=	' onclick="JCck.Core.submitForm(\''.$task.'\', document.getElementById(\'seblod_form\'));"';
		} elseif ( $task == 'reset' ) {
			$pre_task	=	'jQuery(\'#'.$config['formId'].'\').clearForm();';
			$click		=	isset( $config['submit'] ) ? ' onclick="'.$pre_task.'"' : '';
		} elseif ( $task == 'reset2save' ) {
			$pre_task	=	'jQuery(\'#'.$config['formId'].'\').clearForm();';
			$click		=	isset( $config['submit'] ) ? ' onclick="'.$pre_task.$config['submit'].'(\'save\');return false;"' : '';
		} else {
			if ( $task == 'export_ajax' ) {
				$click		=	'';
				$pre_task	=	'';
				$config['doQuery2']	=	true;
				parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'id'=>$id, 'task'=>$task, 'task_auto'=>$task_auto, 'task_id'=>$task_id, 'fieldnames'=>$field->options ) );
			} elseif ( $task == 'process_ajax' ) {
				$click		=	'';
				$pre_task	=	'';
				$config['doQuery2']	=	true;
				parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'id'=>$id, 'task'=>$task, 'task_auto'=>$task_auto, 'task_id'=>$task_id, 'fieldnames'=>$field->options ) );
			} elseif ( $task == 'export' || $task == 'process' || $task == 'list.export' || $task == 'list.process' ) {
				$uri		=	JUri::getInstance()->toString();
				$pre_task	.=	htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="return" value="'.base64_encode( $uri ).'">\');' );

				$click		=	$pre_task.$config['submit'].'(\''.$task.'\');return false;';
				if ( $field->variation != 'toolbar_button' ) {
					parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'task'=>$task, 'task_auto'=>$task_auto, 'task_id'=>$task_id ) );					
				}
				if ( !$task_auto ) {
					$click	=	'if (document.'.$config['formId'].'.boxchecked.value==0){alert(\''.htmlspecialchars( addslashes( JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ) ) ).'\');}else{'.$click.'}';
				} else {
					$config['doQuery2']	=	true;
				}
				$click		=	isset( $config['submit'] ) ? ' onclick="'.$click.'"' : '';
			} elseif ( $task == 'save2redirect' ) {
				$custom		=	'';
				if ( isset( $options2['custom'] ) && $options2['custom'] ) {
					$custom	=	JCckDevHelper::replaceLive( $options2['custom'] );
					$custom	=	$custom ? '&'.$custom : '';
				}
				if ( $config['client'] == 'search' ) {
					$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].'").attr(\'action\', \''.JRoute::_( 'index.php?Itemid='.$options2['itemid'].$custom ).'\');' );
				} else {
					$pre_task	=	htmlspecialchars( 'jQuery("#'.$config['formId'].' input[name=\'config[url]\']").val(\''.JRoute::_( 'index.php?Itemid='.$options2['itemid'].$custom ).'\');' );
				}
				$click		=	isset( $config['submit'] ) ? ' onclick="'.$pre_task.$config['submit'].'(\''.$task.'\');return false;"' : '';			
			} else {
				$click		=	isset( $config['submit'] ) ? ' onclick="'.$pre_task.$config['submit'].'(\''.$task.'\');return false;"' : '';
			}
		}
		if ( $field->attributes && strpos( $field->attributes, 'onclick="' ) !== false ) {
			$matches	=	array();
			$search		=	'#onclick\=\"([a-zA-Z0-9_\(\)\\\'\;\.]*)"#';
			preg_match( $search, $field->attributes, $matches );
			if ( count( $matches ) && $matches[0] ) {
				if ( $matches[0] == $field->attributes ) {
					$field->attributes	=	substr( trim( $field->attributes ), 0, -1 );
					$click				=	' '.$field->attributes.'"';
					$field->attributes	=	'';
				} else {
					$click				=	' onclick="'.$matches[1].'"';
					$field->attributes	=	trim( str_replace( $matches[0], '', $field->attributes ) );
				}
			}
		}
		$attr		=	'class="'.$class.'"'.$click . ( $field->attributes ? ' '.$field->attributes : '' );
		if ( $field->bool ) {
			$label	=	$value;
			
			if ( $field->bool6 == 3 ) {
				$label		=	'<span class="icon-'.$options2['icon'].'"></span>';
				$attr		.=	' title="'.$value.'"';
			} elseif ( $field->bool6 == 2 ) {
				$label		=	$value."\n".'<span class="icon-'.$options2['icon'].'"></span>';
			} elseif ( $field->bool6 == 1 ) {
				$label		=	'<span class="icon-'.$options2['icon'].'"></span>'."\n".$value;
			}
			$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
			$form	=	'<button type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$attr.'>'.$label.'</button>';
			$tag	=	'button';
		} else {
			$form	=	'<input type="submit" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
			$tag	=	'input';
		}
		if ( $field->bool2 == 1 ) {
			$alt	=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			if ( $config['client'] == 'search' ) {
				$onclick	=	'onclick="jQuery(\'#'.$config['formId'].'\').clearForm();"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_RESET' ).'">'.JText::_( 'COM_CCK_RESET' ).'</a>';				
			} else {
				$onclick	=	'onclick="JCck.Core.submitForm(\'cancel\', document.getElementById(\'seblod_form\'));"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_CANCEL' ).'">'.JText::_( 'COM_CCK_CANCEL' ).'</a>';
			}
		} elseif ( $field->bool2 == 2 ) {
			$alt		=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
			JCckPluginLink::g_setLink( $field2, $config );
			JCckPluginLink::g_setHtml( $field2, 'text' );
			$form		.=	$alt.$field2->html;
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			if ( $field->variation == 'toolbar_button' ) {
				$field->form	=	'';
				$icon			=	( isset( $options2['icon'] ) && $options2['icon'] ) ? 'icon-'.$options2['icon'] : '';
				$onclick		=	$pre_task.'JCck.Core.submit(\''.$task.'\')';
				if ( !$task_auto ) {
					$onclick	=	'if (document.'.$config['formId'].'.boxchecked.value==0){alert(\''.htmlspecialchars( addslashes( JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ) ) ).'\');}else{'.$onclick.'}';
				}
				$html			=	'<button class="btn btn-small'.( $field->css ? ' '.$field->css : '' ).'" onclick="'.$onclick.'"><span class="'.$icon.'"></span> '.$value.'</button>';
				
				parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'button'=>array( 'html'=>$html, 'icon'=>@$options2['icon'] ), 'pre_task'=>$pre_task, 'task'=>$task, 'task_auto'=>$task_auto, 'task_id'=>$task_id ) );
			} else {
				parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<'.$tag, ' ', '', $config );
			}
		}
		$field->value	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_BeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$process['task']	=	str_replace( array( 'form.', 'list.' ), '', $process['task'] );
		
		if ( $process['task'] == 'export_ajax' || $process['task'] == 'process_ajax' ) {
			$name	=	$process['name'];
			$target	=	( isset( $config['ids2'] ) && $config['ids2'] != '' ) ? 'ids2' : 'ids';

			if ( !$fields[$name]->state ) {
				return;
			}
			if ( isset( $config[$target] ) && $config[$target] != '' ) {
				static $loaded	=	0;
				$fieldnames		=	array();
				$step			=	1;
				$vars			=	'';

				if ( $process['fieldnames'] ) {
					$fieldnames	=	explode( '||', $process['fieldnames'] );
				}

				JText::script( 'COM_CCK_COMPLETED' );

				if ( $process['task'] == 'export_ajax' ) {
					$step	=	(int)JComponentHelper::getParams( 'com_cck_exporter' )->get( 'mode_ajax_count', 25 );
					$vars	=	'&search='.$config['type'];
				} elseif ( $process['task'] == 'process_ajax' ) {
					$step	=	(int)JComponentHelper::getParams( 'com_cck_toolbox' )->get( 'mode_ajax_count', 25 );
				}

				if ( !$loaded ) {
					$js 	=	'
								(function ($){
									JCck.Core.SubmitButton = {
										batch:[],
										css:"",
										formId:"'.$config['formId'].'",
										instances: [],
										kvp: "",
										uniq_id:"'.uniqid().'",
										width:0,
										ajaxLoopRequest: function(el) {
											var values = JCck.Core.SubmitButton.batch.splice(0, JCck.Core.SubmitButton.instances[el].step).join("&cid[]=");
											var end = ( JCck.Core.SubmitButton.batch.length > 0 ) ? 0 : 1;
											$.ajax({
												cache: false,
												data: "cid[]="+values+"&tid="+JCck.Core.SubmitButton.instances[el].task_id+"&end="+end+"&uniqid="+JCck.Core.SubmitButton.uniq_id+JCck.Core.SubmitButton.kvp,
												url:  JCck.Core.SubmitButton.instances[el].url,
												complete: function(jqXHR) {
													var w = parseInt($(el+" .bar")[0].style.width);
													$(el+" .bar").css("width",parseInt(w+JCck.Core.SubmitButton.width)+"%");
													if (JCck.Core.SubmitButton.batch.length) {
														JCck.Core.SubmitButton.ajaxLoopRequest(el);
													} else {
														$(el+" .bar").css("width","100%");
														var resp = JSON.parse(jqXHR.responseText);

														if (typeof resp == "object") {
															if (resp.output_path !== undefined) {
																window.setTimeout(function(){
																	$(el+" .bar").css("font-size","inherit").css("padding",JCck.Core.SubmitButton.css).text(Joomla.JText._("COM_CCK_COMPLETED"));
																},500);
																
																document.location.href = resp.output_path;
															} else {
																document.location.reload();
															}
														}
													}
												}
											});
										},
										initProcess: function(el, data) {
											JCck.Core.SubmitButton.instances[el] = data;

											var w = parseFloat($(el)[0].getBoundingClientRect().width);
											var h = $(el).css("height");

											JCck.Core.SubmitButton.css = $(el).css("padding");
											$(el).prop("disabled",true).addClass("btn-progress").css("width", w).css("height", h).css("padding", 0);
											$(el).html(\'<div class="progress"><div class="bar" style="width:0%;"></div></div>\');
											$(el+" > div").css("height", "100%").css("margin", "0").css("padding", "0");

											JCck.Core.SubmitButton.batch = [];

											if (document[JCck.Core.SubmitButton.formId].boxchecked.value!=0) {	
												$(\'input:checkbox[name="cid[]"]:checked\').each(function(i) {
													JCck.Core.SubmitButton.batch[i] = $(this).val();
												});
											} else {
												JCck.Core.SubmitButton.batch = JCck.Core.SubmitButton.instances[el].items;
											}
											JCck.Core.SubmitButton.instances[el].total = JCck.Core.SubmitButton.batch.length;
											JCck.Core.SubmitButton.width = parseInt(JCck.Core.SubmitButton.instances[el].step/JCck.Core.SubmitButton.instances[el].total*100);

											JCck.Core.SubmitButton.kvp = "";
											$.each(JCck.Core.SubmitButton.instances[el].fields, function(k,v) {
  												JCck.Core.SubmitButton.kvp	+=	"&"+v+"="+$("#"+v).myVal();
  											});
										},
										isValid: function()
										{
											if (typeof jQuery.fn.validationEngine === "function") {
												if ($("#"+JCck.Core.SubmitButton.formId).length && $("#"+JCck.Core.SubmitButton.formId).validationEngine("validate") === false) {
													return false;
												}
											}
											return true;
										}
									}
								})(jQuery);
								';
					$loaded	=	1;
					JFactory::getDocument()->addScriptDeclaration( $js );
				}
				$js		=	'';

				if ( !$process['task_auto'] ) {
					$js	=	'if (document[JCck.Core.SubmitButton.formId].boxchecked.value==0){alert(\''.htmlspecialchars( addslashes( JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ) ) ).'\'); return false;}';
				}

				$js 	=	'
							(function ($){
								$(document).ready(function() {
									$("#'.$process['id'].'").on("click", function() {'.$js.'
										if (!JCck.Core.SubmitButton.isValid()) { return false; }
										var data = {
											"fields":'.json_encode( $fieldnames ).',
											"items":['.$config[$target].'],
											"step":'.(int)$step.',
											"task_id":'.(int)$process['task_id'].',
											"total":'.( substr_count( $config[$target], ',' ) + 1 ).',
											"url":"'.JCckDevHelper::getAbsoluteUrl( 'auto', 'task='.str_replace( '_ajax', 'Ajax', $process['task'] ).'&format=raw' ).$vars.'"
										}
										JCck.Core.SubmitButton.initProcess("#"+$(this).attr("id"), data);
										JCck.Core.SubmitButton.ajaxLoopRequest("#"+$(this).attr("id"));
									});
								});
							})(jQuery);
							';
				JFactory::getDocument()->addScriptDeclaration( $js );
			}
		}
		if ( $process['task_auto'] && ( $process['task'] == 'export' || $process['task'] == 'process' ) ) {
			$target	=	( isset( $config['ids2'] ) && $config['ids2'] != '' ) ? 'ids2' : 'ids';
			
			if ( isset( $config[$target] ) && $config[$target] != '' ) {
				$name					=	$process['name'];
				$search					=	'onclick="';
				$replace				=	$search.'if (document.'.$config['formId'].'.boxchecked.value==0){'.htmlspecialchars( 'jQuery("#'.$config['formId'].'").append(\'<input type="hidden" name="ids" value="'.$config[$target].'">\');' ).'}';
				$fields[$name]->form	=	str_replace( $search, $replace, $fields[$name]->form );
			}
		}
		if ( isset( $process['button'] ) && is_array( $process['button'] ) ) {
			if ( isset( $search ) && isset( $replace ) ) {
				$process['button']['html']	=	str_replace( $search, $replace, $process['button']['html'] );
			}
			JToolBar::getInstance( 'toolbar' )->appendButton( 'Custom', $process['button']['html'], $process['button']['icon'] );
		}
	}
}
?>