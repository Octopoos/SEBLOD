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
class plgCCK_FieldGroup_X extends JCckPluginField
{
	protected static $type		=	'group_x';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		parent::g_onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
		
	// onCCK_FieldConstruct_TypeContent
	public static function onCCK_FieldConstruct_TypeContent( &$field, $style, $data = array(), &$config = array() )
	{
		parent::g_onCCK_FieldConstruct_TypeContent( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
	
	// onCCK_FieldDelete
	public function onCCK_FieldDelete( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		// Prepare
		$name		=	$field->name;
		$dispatcher	=	JEventDispatcher::getInstance();
		$fields		=	self::_getChildren( $field, $config, false );

		// TODO: call storage plugin.
		$xn			=	( $field->storage == 'xml' ) ? ( is_object( $value ) ? count( $value->children() ) : count( $value ) ) : $value;
		$content	=	array();
		for ( $xi = 0; $xi < $xn; $xi++ ) {
			foreach ( $fields as $f ) {
				if ( is_object( $f ) ) {
					$f_name					=	$f->name;
					$f_value				=	'';
					$inherit				=	array( 'parent' => $field->name, 'xi' => $xi );
					$content[$xi][$f_name]	=	clone $f;
					//
					if ( $field->storage == 'custom' ) {
						$f->storage				=	$field->storage;
						$f->storage_table		=	$field->storage_table;
						$f->storage_field		=	$field->storage_field;
					}
					$table					=	$f->storage_table;
					if ( $table && ! isset( $config['storages'][$table] ) ) {
						$config['storages'][$table]	=	'';
						$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'] ) );
					}
					$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi, $field ) );

					$dispatcher->trigger( 'onCCK_FieldDelete', array( &$content[$xi][$f_name], $f_value, &$config ) );
				}
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Prepare
		$name		=	$field->name;
		$dispatcher	=	JEventDispatcher::getInstance();
		$fields		=	self::_getChildren( $field, $config );
		// TODO: call storage plugin.
		$xn			=	( $field->storage == 'xml' ) ? ( is_object( $value ) ? count( $value->children() ) : count( $value ) ) : $value;
		$content	=	array();
		for ( $xi = 0; $xi < $xn; $xi++ ) {
			foreach ( $fields as $f ) {
				if ( is_object( $f ) ) {
					$f_name					=	$f->name;
					$f_value				=	'';
					$inherit				=	array( 'parent' => $field->name, 'xi' => $xi );
					$content[$xi][$f_name]	=	clone $f;
					//
					if ( $field->storage == 'custom' ) {
						$f->storage				=	$field->storage;
						$f->storage_table		=	$field->storage_table;
						$f->storage_field		=	$field->storage_field;
					}
					$table					=	$f->storage_table;
					if ( $table && ! isset( $config['storages'][$table] ) ) {
						$config['storages'][$table]	=	'';
						$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'] ) );
					}
					$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi, $field ) );
					//	
					$f_value2	=	(string)$f_value;
					$dispatcher->trigger( 'onCCK_FieldPrepareContent', array( &$content[$xi][$f_name], $f_value2, &$config, $inherit, true ) );
					$target	=	$content[$xi][$f_name]->typo_target;
					if ( $content[$xi][$f_name]->link != '' ) {
						$dispatcher->trigger( 'onCCK_Field_LinkPrepareContent', array( &$content[$xi][$f_name], &$config ) );
						if ( $content[$xi][$f_name]->link && !@$content[$xi][$f_name]->linked ) {
							JCckPluginLink::g_setHtml( $content[$xi][$f_name], $target );
						}
					}
					if ( @$content[$xi][$f_name]->typo && $content[$xi][$f_name]->$target != '' && $config['doTypo'] ) {
						$dispatcher->trigger( 'onCCK_Field_TypoPrepareContent', array( &$content[$xi][$f_name], $content[$xi][$f_name]->typo_target, &$config ) );
					} else {
						$content[$xi][$f_name]->typo	=	'';
					}
				}
			}
		}
		$field->value	=	$content;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		
		// Prepare
		$dispatcher	=	JEventDispatcher::getInstance();
		$fields		=	self::_getChildren( $field, $config );
		if ( $value ) {
			// TODO: call storage plugin.
			$xn		=	( $field->storage == 'xml' ) ? ( is_object( $value ) ? count( $value->children() ) : count( $value ) ) : $value;
		} else {
			$xn		=	$field->rows;
		}
		$form		=	array();
		for ( $xi = 0; $xi < $xn; $xi++ ) {
			foreach ( $fields as $f ) {
				if ( is_object( $f ) ) {
					$f_name		=	$f->name;
					$f_value	=	'';
					if ( $config['pk'] ) {
						if ( $field->storage == 'custom' ) {
							$f->storage			=	$field->storage;
							$f->storage_table	=	$field->storage_table;
							$f->storage_field	=	$field->storage_field;	
						}
						$table					=	$f->storage_table;
						if ( $table && ! isset( $config['storages'][$table] ) ) {
							$config['storages'][$table]	=	'';
							$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'] ) );
						}
						$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi, $field ) );
					} elseif ( (int)$value > 0 ) {
						$f->storage			=	$field->storage;
						$f->storage_table	=	$field->storage_table;
						$f->storage_field	=	$field->storage_field;
						$table				=	$f->storage_table;
						static $already		=	0;
						if ( !$already ) {
							JPluginHelper::importPlugin( 'cck_storage' );
							$already	=	1;
						}
						$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi, $field ) );
					}
					$inherit					=	array( 'id' => $name.'_'.$xi.'_'.$f_name, 'name' => $name.'['.$xi.']['.$f_name.']', 'xk'=>$xi );
					$clone						=	clone $f;
					$results					=	$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$clone, $f_value, &$config, $inherit, true ) );
					$form[$xi][$f_name]			=	$results[0];
					@$form[$xi][$f_name]->name	=	$f->name;
				}
			}
		}
		if ( $field->bool2 ) {
			foreach ( $fields as $f ) {		// Empty
				$f_name						=	$f->name;
				$inherit					=	array( 'id' => $name.'_'.'0'.'_'.$f_name, 'name' => $name.'['.'0'.']['.$f_name.']', 'xk' => '0', 'empty' => true );
				$clone						=	clone $f;
				$results					=	$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$clone, '', &$config, $inherit, true ) );
				$form[$xi][$f_name]			=	$results[0];
				@$form[$xi][$f_name]->name	=	$f->name;
			}
		}

		// Set
		if ( $field->script ) {
			parent::g_addScriptDeclaration( $field->script );
		}
		$field->form	=	$form;
		$field->value	=	'';
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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		$dispatcher	=	JEventDispatcher::getInstance();
		
		// Prepare
		$store	=	'';
		$text	=	'';
		$xi		=	0;
		if ( count( $value ) ) {
			$store	=	'<br />';
			foreach ( $value as $key=>$val ) {
				$store	.=	'<br />::cck_'.$name.'::'.$field->extended.'::/cck_'.$name.'::';
				$text	.=	'- '.$field->label.' '.$xi.': <ul style="line-height:10px;">';
				$fields	=	self::_getChildren( $field, $config );
				if ( count( $fields ) ) {
					foreach ( $fields as $f ) {
						$f->storage			=	$field->storage;
						$f->storage_table	=	$field->storage_table;
						$f->storage_field	=	$field->storage_field;
						$f->state			=	'';	//todo;
						$f_label			=	$f->label;
						$f_name				=	$f->name;
						$f_value			=	@$val[$f_name];
						$inherit			=	array( 'xk' => $key, 'xi' => $xi, 'parent' => $name, 'array_x' => 1, 'post' => $val );
						$results			=	$dispatcher->trigger( 'onCCK_FieldPrepareStore', array( &$f, $f_value, &$config, $inherit, true ) );
						$v					=	@$results[0];
						$store				.=	'<br />::'.$f_name.'|'.$xi.'|'.$name.'::'.$v.'::/'.$f_name.'|'.$xi.'|'.$name.'::';
						$text				.=	'<li style="line-height:10px;">'.$f_label.' : '.$v.'</li>';
						// todo: add childs (secondary) storages.. not primary!
					}
				}
				$store	.=	'<br />::cckend_'.$name.'::::/cckend_'.$name.'::';
				$text	.=	'</ul>';
				$xi++;
			}
			$store	.=	'<br />';
		}
		$field->values	=	$value;
		$value			=	$xi;
		$field->value	=	$value;
		$field->text	=	$text;

		parent::g_onCCK_FieldPrepareStore_X( $field, $name, $value, $store, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		if ( $field->typo ) {
			return $field->typo;
		} else {			
			$doc	=	JFactory::getDocument();
			$doc->addStyleSheet( self::$path.'assets/css/style2.css' );
	
			$count	=	count( $field->value );
			$html	=	'';
			
			if ( $count ) {
				$i		=	0;
				foreach ( $field->value as $group ) {
					$row	=	'';
					$isRow	=	false;
					foreach ( $group as $elem ) {
						if ( $elem->display ) {
							$value	=	JCck::callFunc( 'plgCCK_Field'.$elem->type, 'onCCK_FieldRenderContent', $elem );
							if ( $value != '' ) {
								if ( $elem->markup == 'none' ) {
									$row	.=	$elem->label.$value;
								} else {
									$row	.=	'<div id="'.$field->name.'_'.$i.'_'.$elem->name.'" class="cck_'.$elem->type.'">';
									if ( $elem->label != '' ) {
										$row	.=	'<label class="cck_label_'.$elem->type.'">'.$elem->label.'</label>';
									}
									$row	.=	$value
											.	'</div>';
								}
							}
							$isRow	=	true;
						}
					}
					if ( $isRow ) {
						if ( $field->markup == 'none' ) {
							$html	.=	$row;
						} else {
							$html	.=	'<div id="'.$field->name.'_'.$i.'" class="gxi"><div>' .$row. '</div></div>';
						}
					}
					$i++;
				}
				if ( $html && $field->markup != 'none' ) {
					$html	=	'<div id="'.$field->name.'" class="gx">' .$html. '</div>';
				}
			}
			
			return $html;
		}
	}

	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();

		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<link rel="stylesheet" href="'.self::$path.'assets/css/style2.css'.'" type="text/css" />';
		} else {
			$doc->addStyleSheet( self::$path.'assets/css/style2.css' );
		}

		$count			=	$field->bool2 ? count( $field->form ) - 1 : count( $field->form );
		$buttons 		= 	$field->bool2 || $field->bool3 || $field->bool4;
		$button_top		=	'';
		$button_bottom 	=	'';
		$html			=	'';
		$empty			=	'';
		$css 			=	( $field->css ) ? ' '.$field->css : '';
		$rId			=	$config['rendering_id'];
		
		if ( $field->bool2 > 1 ) {
			$external_button 	=	'<button class="button btn btn-success external cck_button_add_'.$field->name.'" type="button"><span class="icon-file-plus"></span>'."\n".JText::_( 'COM_CCK_ADD_NEW' ).'</button>';
			$external_button 	=	'<div class="btn-toolbar">'.$external_button.'</div>';
			if ( $field->bool2 == 2 || $field->bool2 == 4 ) {
				$button_top		=	$external_button;
			}
			if ( $field->bool2 == 3 || $field->bool2 == 4 ) {
				$button_bottom	=	$external_button;
			}
		}

		if ( $count ) {
			if ( $field->bool == 2 ) {
				$head		=	'';
				$foot		=	'';
				for ( $i = 0; $i < $count; $i++ ) {
					if ( $i == 0 ) {
						$head	.=	'<tr class="head">';
						foreach ( $field->form[$i] as $elem ) {
							if ( $elem->display > 1 ) {
								$class	=	( @$elem->markup_class ) ? ' class="'.trim( @$elem->markup_class ).'"' : '';
								$head	.=	'<th'.$class.'>'.$elem->label.'</th>';
							}
						}
						$head 	.=	( $buttons ) ? '<th></th>' : '';
						$head	.=	'</tr>';
					}
					$html	.=	self::_getHtmlTable( $field, $field->form[$i], $i, $count, $buttons, $config );
				}
				$head		=	'<thead>'.$head.'</thead>';
				$html		=	'<tbody id="'.$rId.'_sortable_'.$field->name.'" >'.$html.'</tbody>';
				$html		=	'<table border="0" cellpadding="0" cellspacing="0" class="table'.$css.'">'.$head.$html.$foot.'</table>';

				if ( $field->bool2 ) {
					$empty		=	self::_getHtmlTable( $field, @$field->form[$i], 0, 0, $buttons, $config );
					$html 		=	$button_top.$html.$button_bottom;
				}

			} else {
				$orientation=	( $field->bool == 1 ) ? 'vertical_gx' : 'horizontal_gx';
				$width		=	'';			
				$html		.=	'<div id="'.$rId.'_sortable_'.$field->name.'" class="'.$orientation.$css.' '.$width.'">';
				for ( $i = 0; $i < $count; $i++ ) {
					$html	.=	self::_getHtml( $field, $field->form[$i], $i, $count, $buttons, $config );
				}
				$html		.=	'</div>';
				if ( $field->bool2 ) {
					$empty		=	self::_getHtml( $field, @$field->form[$i], 0, 0, $buttons, $config );
					$html 		=	$button_top.$html.$button_bottom;
				}
			}
		}
		
		if ( $field->bool2 || $field->bool3 || $field->bool4 ) {
			self::_addScripts();
			self::_addScript( $field->name, array( 'min'=>$field->minlength, 'max'=>$field->maxlength, 'default'=>$field->rows,
												   'del'=>$field->bool3, 'add'=>$field->bool2, 'drag'=>$field->bool4, 'empty_html'=>$empty, 'rId'=>$rId ) );
		}
				
		return $html;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _addScripts
	protected static function _addScripts()
	{
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}
		
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		$loaded	=	1;
		
		JCck::loadjQuery();

		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<script src="'.JUri::root( true ).'/media/cck/js/jquery.ui.min.js" type="text/javascript"></script>';
			echo '<script src="'.self::$path.'assets/js/script3.js'.'" type="text/javascript"></script>';
		} else {
			JCck::loadjQueryUI();
			$doc->addScript( self::$path.'assets/js/script3.js' );
		}
	}
	
	// _addScript
	protected static function _addScript( $id, $params = array() )
	{
		$app		=	JFactory::getApplication();
		$doc		=	JFactory::getDocument();
		$search		=	array( '.', '<', '>', '"', '%', ';' );
		$replace	=	array( '\.', '\<', '\>', '\"', '\%', '\;' );
		$rId		=	$params['rId'];
		
		$params['empty_html']	=	preg_replace( "/(\r\n|\n|\r)/", " ", $params['empty_html'] );
		$params['empty_html']	=	str_replace( $search, $replace, $params['empty_html'] );
		
		$js		=	'jQuery(document).ready(function($) {';
		if ( $params['del'] ) {
			$js	.=	'JCck.GroupX.remove("'.$id.'",'.$params['min'].',"'.$rId.'");';
		}
		if ( $params['add'] ) {
			$js	.=	'JCck.GroupX.add("'.$id.'",'.$params['max'].',"'.$params['empty_html'].'","'.$rId.'");';
		}
		if ($params['drag']) {
			$js	.=	'$("#'.$rId.'_sortable_'.$id.'").sortable({'
						.	'axis	: "y",'
						.	'handle	: ".cck_button_drag_'.$id.'",'
						.	'scroll	: true,'
						.	'forcePlaceholderSize: true,'
						.	'start		: function(event, ui) {
								ui.item.css({"top":"0","left":"0"}); /* ~Fix */
								$(this).css({"overflow":"visible"});
							},
							stop		: function(event, ui) {
								ui.item.css({"top":"0","left":"0"}); /* ~Fix */
								$(this).css({"overflow":"auto"});
							}'
						.'});';
		}
		$js	.=	'});';
		
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			$doc ->addScriptDeclaration( $js );
		}
	}

	// _getHtmlTable
	protected static function _getHtmlTable( $field, $group, $i, $size_group, $buttons, &$config )
	{
		$client				=	'cck_'.$config['client'];
		$html_div_buttons	=	'';
		$js					=	'';
		$js2				=	'';
		$js_format			=	( $i == 0 && $size_group == 0 ) ? 'raw' : 'html';
		$rId				=	$config['rendering_id'];
		$class				=	'cck_cgx cck_cgx_form';
		$class2				=	( $i % 2 ) ? ' even' : ' odd';

		if ( $buttons ) {
			if ( $field->bool3 ) {
				$html_div_buttons	.=	'<div class="cck_button cck_button_del_'.$field->name.' cck_button_del cck_button_first"><span class="icon-minus"></div>';
			}
			if ( $field->bool2 ) {
				$html_div_buttons	.=	'<div class="cck_button cck_button_add_'.$field->name.' cck_button_add"><span class="icon-plus"></span></div>';
			}
			if ( $field->bool4 ) {
				$html_div_buttons	.=	'<div class="cck_button cck_button_drag_'.$field->name.' cck_button_drag cck_button_last"><span class="icon-circle"></div>';
			}
		}
		if ( $size_group == 1 ) {
			$html		=	'<tr id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x cck_form_group_x_first cck_form_group_x_last'.$class2.'">';
			$buttons	=	( $buttons ) ? '<td id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button cck_cgx_button_first cck_cgx_button_last">'.$html_div_buttons.'</td>' : '';
			$class		.=	' cck_cgx_form_first cck_cgx_form_last';
		} elseif ( $size_group == 0 ) {
			$html		=	'<tr id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x'.$class2.'">';
			$buttons	=	( $buttons ) ? '<td id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button">'.$html_div_buttons.'</td>' : '';
		} elseif ( $i == 0 ) {
			$html		=	'<tr id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x cck_form_group_x_first'.$class2.'">';
			$buttons	=	( $buttons ) ? '<td id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button cck_cgx_button_first">'.$html_div_buttons.'</td>' : '';
			$class		.=	' cck_cgx_form_first';
		} elseif ( $i == $size_group -1 ) {
			$html		=	'<tr id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x cck_form_group_x_last'.$class2.'">';
			$buttons	=	( $buttons ) ? '<td id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button cck_cgx_button_last">'.$html_div_buttons.'</td>' : '';
			$class		.=	' cck_cgx_form_last';
		} else {
			$html		=	'<tr id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x'.$class2.'">';
			$buttons	=	( $buttons ) ? '<td id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button">'.$html_div_buttons.'</td>' : '';
		}

		if ( count( $group ) ) {
			foreach ( $group as $elem ) {
				if ( @$elem->markup_class ) {
					$class2	=	$class.@$elem->markup_class;
				}
				$td			=	'<td id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="'.$class2.'">';

				if ( $elem->display > 1 ) {
					$html	.=	$td;
				}
				if ( $elem->markup == 'none' ) {
					$html	.=	$elem->form;
				} else {
					$html	.=	'<div class="cck_forms">'.$elem->form.'</div>';
				}
				if ( $elem->display > 1 ) {
					$html	.=	'</td>';			
				}
				if ( @$elem->computation ) {
					$computation			=	new JRegistry;
					$computation->loadString( $elem->computation_options );
					$computation_options	=	$computation->toObject();
					if ( $computation_options->calc == 'custom' ) {
						$computed	=	'';
						if ( count( $computation_options->fields ) ) {
							foreach ( $computation_options->fields as $k=>$v ) {
								$computed	.=	chr( 97 + $k ).':$("#'.$field->name.'_'.$i.'_'.$v.'")'.',';
							}
							$computed		=	substr( $computed, 0, -1 );
						}
						$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
						$targets	=	@$computation_options->targets ? json_encode( $computation_options->targets ) : '[]';
						$format		=	'';
						if ( $computation_options->format == 'toFixed' ) {
							$format	=	'.'.$computation_options->format.'('.$computation_options->precision.')';
						} elseif ( $computation_options->format ) {
							$format	=	'.'.$computation_options->format.'()';
						}
						if ( @$computation_options->recalc ) {
							$config['computation'][$event][]	=	array( '_'=>str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation ),
																		   'js'=>'$("#'.$field->name.'_'.$i.'_'.$elem->name.'").calc( "'.$computation_options->custom.'", {'.$computed.'}, '.$targets.', function (s){return s'.$format.';} );' );
						} else {
							$js2	.=	'JCck.Core.recalc_'.$field->name.'_'.$i.'_'.$elem->name.' = function() {'.'$("#'.$field->name.'_'.$i.'_'.$elem->name.'").calc( "'
														  .$computation_options->custom.'", {'.$computed.'}, '.$targets.', function (s){return s'.$format.';} );'.'}';
							if ( $event != 'none' ) {
								$js		.=	'$("'.str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation ).'").bind("'.$event.'", JCck.Core.recalc_'.$field->name.'_'.$i.'_'.$elem->name.'); JCck.Core.recalc_'.$field->name.'_'.$i.'_'.$elem->name.'();';
							}
							JFactory::getDocument()->addScriptDeclaration( '(function ($){'.$js2.'})(jQuery);' );
						}
					} else {
						$computed	=	str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation );
						$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
						$targets	=	@$computation_options->targets ? ', '.json_encode( $computation_options->targets ) : '';
						if ( @$computation_options->recalc ) {
							$config['computation'][$event][]	=	array( '_'=>$computed,
																		   'js'=>'$("'.$computed.'").'.$computation_options->calc.'("'.$event.'", "#'.$field->name.'_'.$i.'_'.$elem->name.'"'.$targets.');' );
						} else {
							$js		.=	'$("'.$computed.'").'.$computation_options->calc.'("'.$event.'", "#'.$field->name.'_'.$i.'_'.$elem->name.'"'.$targets.');';
						}
					}
					$config['doComputation']	=	1;
				}
			}
			$html	.=	$buttons;
		}
		$html	.=	'</tr>';

		if ( $js ) {
			if ( $js_format == 'raw' ) {
				$html	.=	'<script type="text/javascript">(function ($){'.$js.'})(jQuery);</script>';
			} else {
				JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($){'.$js.'});' );
			}
		}

		return $html;
	}

	// _getHtml
	protected static function _getHtml( $field, $group, $i, $size_group, $buttons, &$config )
	{
		$client				=	'cck_'.$config['client'];
		$html_div_buttons	=	'';
		$js					=	'';
		$js2				=	'';
		$js_format			=	( $i == 0 && $size_group == 0 ) ? 'raw' : 'html';
		$rId				=	$config['rendering_id'];
		
		if ( $buttons ) {
			if ( $field->bool3 ) {
				$html_div_buttons	.=	'<div class="cck_button cck_button_del_'.$field->name.' cck_button_del cck_button_first"><span class="icon-minus"></span></div>';
			}
			if ( $field->bool2 ) {
				$html_div_buttons	.=	'<div class="cck_button cck_button_add_'.$field->name.' cck_button_add"><span class="icon-plus"></span></div>';
			}
			if ( $field->bool4 ) {
				$html_div_buttons	.=	'<div class="cck_button cck_button_drag_'.$field->name.' cck_button_drag cck_button_last"><span class="icon-circle"></span></div>';
			}
		}
		
		if ( $size_group == 1 ) {
			$html	=	'<div id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x cck_form_group_x_first cck_form_group_x_last">';
			$html	.=	( $buttons ) ? '<aside id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button cck_cgx_button_first cck_cgx_button_last">'.$html_div_buttons.'</aside>' : '';
			$html	.=	'<div id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_form cck_cgx_form_first cck_cgx_form_last">';			
		} elseif ( $size_group == 0 ) {
			$html	=	'<div id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x">';
			$html	.=	( $buttons ) ? '<aside id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button">'.$html_div_buttons.'</aside>' : '';
			$html	.=	'<div id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_form">';
		} elseif ( $i == 0 ) {
			$html	=	'<div id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x cck_form_group_x_first">';
			$html	.=	( $buttons ) ? '<aside id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button cck_cgx_button_first">'.$html_div_buttons.'</aside>' : '';
			$html	.=	'<div id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_form cck_cgx_form_first">';
		} elseif ( $i == $size_group -1 ) {
			$html	=	'<div id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x cck_form_group_x_last">';
			$html	.=	( $buttons ) ? '<aside id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button cck_cgx_button_last">'.$html_div_buttons.'</aside>' : '';
			$html	.=	'<div id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_form cck_cgx_form_last">';
		} else {
			$html	=	'<div id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group_x">';
			$html	.=	( $buttons ) ? '<aside id="'.$rId.'_button_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_button">'.$html_div_buttons.'</aside>' : '';
			$html	.=	'<div id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_form">';
		}
		
		if ( count( $group ) ) {
			foreach ( $group as $elem ) {
				if ( $elem->display > 1 ) {
					if ( $elem->markup == 'none' ) {
						if ( $elem->label != '' ) {
							$html	.=	'<label for="'.$elem->name.'">'.$elem->label.'</label>';
						}
					} else {
						$html	.=	'<div id="'.$rId.'_'.$field->name.'_'.$i.'_'.$elem->name.'" class="cck_forms '.$client.' cck_'.$elem->type.' cck_'.$elem->name.'">';
						$html	.=	'<div id="'.$rId.'_'.$field->name.'_'.$i.'_label_'.$elem->name.'" class="cck_label cck_label_'.$elem->type.'"><label for="'.$elem->name.'">'.$elem->label.'</label></div>';
						$html	.=	'<div id="'.$rId.'_'.$field->name.'_'.$i.'_form_'.$elem->name.'" class="cck_form cck_form_'.$elem->type.@$elem->markup_class.'">';
					}
				}
				$html		.=	$elem->form;
				if ( $elem->display > 1 && $elem->markup != 'none' ) {
					$html	.=	'</div>';
					$html	.=	'</div>';
				}
				
				// Computation & Conditional
				if ( @$elem->computation ) {			
					$computation			=	new JRegistry;
					$computation->loadString( $elem->computation_options );
					$computation_options	=	$computation->toObject();
					if ( $computation_options->calc == 'custom' ) {
						$computed	=	'';
						if ( count( $computation_options->fields ) ) {
							foreach ( $computation_options->fields as $k=>$v ) {
								$computed	.=	chr( 97 + $k ).':$("#'.$field->name.'_'.$i.'_'.$v.'")'.',';
							}
							$computed		=	substr( $computed, 0, -1 );
						}
						$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
						$targets	=	@$computation_options->targets ? json_encode( $computation_options->targets ) : '[]';
						$format		=	'';
						if ( $computation_options->format == 'toFixed' ) {
							$format	=	'.'.$computation_options->format.'('.$computation_options->precision.')';
						} elseif ( $computation_options->format ) {
							$format	=	'.'.$computation_options->format.'()';
						}
						if ( @$computation_options->recalc && $js_format != 'raw' ) {
							$config['computation'][$event][]	=	array( '_'=>str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation ),
																		   'js'=>'$("#'.$field->name.'_'.$i.'_'.$elem->name.'").calc( "'.$computation_options->custom.'", {'.$computed.'}, '
																																 .$targets.', function (s){return s'.$format.';} );' );
						} else {
							$js2	.=	'JCck.Core.recalc_'.$field->name.'_'.$i.'_'.$elem->name.' = function() {'.'$("#'.$field->name.'_'.$i.'_'.$elem->name.'").calc( "'
														  .$computation_options->custom.'", {'.$computed.'}, '.$targets.', function (s){return s'.$format.';} );'.'}';
							if ( $event != 'none' ) {
								$js		.=	'$("'.str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation ).'").bind("'.$event.'", JCck.Core.recalc_'.$field->name.'_'.$i.'_'.$elem->name.'); JCck.Core.recalc_'.$field->name.'_'.$i.'_'.$elem->name.'();';
							}
							if ( $js_format == 'raw' ) {
								if ( $event != 'none' ) {
									$js	.=	$js2.'$("'.str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation ).'").bind("'.$event.'", JCck.Core.recalc);';
								}
							} else {
								JFactory::getDocument()->addScriptDeclaration( '(function ($){'.$js2.'})(jQuery);' );
							}
						}
					} else {
						$computed	=	str_replace( '#', '#'.$field->name.'_'.$i.'_', $elem->computation );
						$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
						$targets	=	@$computation_options->targets ? ', '.json_encode( $computation_options->targets ) : '';
						if ( @$computation_options->recalc && $js_format != 'raw' ) {
							$config['computation'][$event][]	=	array( '_'=>$computed,
																		   'js'=>'$("'.$computed.'").'.$computation_options->calc.'("'.$event.'", "#'.$field->name.'_'.$i.'_'.$elem->name.'"'.$targets.');' );
						} else {
							$js		.=	'$("'.$computed.'").'.$computation_options->calc.'("'.$event.'", "#'.$field->name.'_'.$i.'_'.$elem->name.'"'.$targets.');';
							if ( $js_format == 'raw' ) {
								if ( $event != 'none' ) {
									$js	.=	'$("'.$computed.'").bind("'.$event.'", JCck.Core.recalc);';
								}
							}
						}
					}
					$config['doComputation']	=	1;
				}
				if ( @$elem->conditional ) {
					$conditions					=	explode( ',', $elem->conditional );
					$elem->conditional_options	=	str_replace( '#form#', '#'.$field->name.'_'.$i.'_'.$elem->name, $elem->conditional_options );
					if ( count( $conditions ) > 1 ) {
						$c_opts	=	$elem->conditional_options;
						foreach ( $conditions as $c ) {
							$c_opts	=	str_replace( $c, $field->name.'_'.$i.'_'.$c, $c_opts );
						}
					} else {
						$c_opts		=	str_replace( $conditions[0], $field->name.'_'.$i.'_'.$conditions[0], $elem->conditional_options );
					}
					$js	.=	'$("#'.$rId.'_'.$field->name.'_'.$i.'_'.$elem->name.'").conditionalStates('.$c_opts.');';
				}
			}
		}
		
		$html	.=	'</div>';
		$html	.=	'</div>';
		
		if ( $js ) {
			if ( $js_format == 'raw' ) {
				$html	.=	'<script type="text/javascript">(function ($){'.$js.'})(jQuery);</script>';
			} else {
				JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($){'.$js.'});' );
			}
		}
		
		return $html;
	}
	
	// _getChildren
	protected static function _getChildren( $parent, $config = array(), $isClient = true )
	{
		$db		=	JFactory::getDbo();
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		
		if ( !$isClient ) {
			$client_w	=	'';
		} else {
			$client		=	( $config['client'] == 'list' || $config['client'] == 'item' ) ? 'intro' : $config['client'];
			$client_w	=	'c.client = "'.$client.'" AND ';
		}
		
		$where	=	' WHERE '.$client_w.'b.name = "'.$parent->extended.'"'
				.	' AND a.type != "form_action"'
				.	' AND c.access IN ('.$access.')';
		$order	=	' ORDER BY c.ordering ASC';
		
		$query	= ' SELECT DISTINCT a.*, c.client,'
		        . 	' c.label as label2, c.variation, c.variation_override, c.required, c.required_alert, c.validation, c.validation_options, c.live, c.live_options, c.live_value, c.link, c.link_options, c.typo, c.typo_label, c.typo_options, c.markup, c.markup_class, c.stage, c.access, c.restriction, c.restriction_options, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position'
				.	' FROM #__cck_core_fields AS a'
				.	' LEFT JOIN #__cck_core_type_field AS c ON c.fieldid = a.id'
				.	' LEFT JOIN #__cck_core_types AS b ON b.id = c.typeid'
				.	$where
				.	$order
				;
		$db->setQuery( $query );
		$fields	=	$db->loadObjectList( 'name' ); //#
		
		if ( ! count( $fields ) ) {
			return array();
		}
		
		return $fields;
	}
}
?>