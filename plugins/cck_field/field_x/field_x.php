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
class plgCCK_FieldField_X extends JCckPluginField
{
	protected static $type		=	'field_x';
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		$name		=	$field->name;
		
		$dispatcher	=	JEventDispatcher::getInstance();
		$f			=	self::_getChild( $field, $config );
		$xn			=	$value;
		$content	=	array();
		if ( $xn > 0 && is_object( $f ) ) {
			for ( $xi = 0; $xi < $xn; $xi++ ) {
				$f_value			=	'';
				$inherit			=	array( 'parent' => $field->name, 'xi' => $xi );
				$content[$xi]		=	clone $f;
				//
				$table				=	$f->storage_table;
				if ( $table && ! isset( $config['storages'][$table] ) ) {
					$config['storages'][$table]	=	'';
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'] ) );
				}
				$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi ) );
				//
				$dispatcher->trigger( 'onCCK_FieldPrepareContent', array( &$content[$xi], $f_value, &$config, $inherit, true ) );
			}
			if ( $content[0] ) {
				$field->display	=	$content[0]->display;
			}
		}
		$field->value	=	$content;
	}
	
	// onCCK_FieldDelete
	public function onCCK_FieldDelete( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		if ( $value == '' ) {
			return;
		}

		$name		=	$field->name;
		$dispatcher	=	JEventDispatcher::getInstance();
		$f			=	self::_getChild( $field, $config );
		$xn			=	$value;
		$content	=	array();
		if ( $xn > 0 && is_object( $f ) ) {
			for ( $xi = 0; $xi < $xn; $xi++ ) {
				$f_value			=	'';
				$inherit			=	array( 'parent' => $field->name, 'xi' => $xi );
				$content[$xi]		=	clone $f;
				//
				$table				=	$f->storage_table;
				if ( $table && ! isset( $config['storages'][$table] ) ) {
					$config['storages'][$table]	=	'';
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'] ) );
				}
				$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi ) );
				//
				$dispatcher->trigger( 'onCCK_FieldDelete', array( &$content[$xi], $f_value, &$config ) );
			}
		}
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
		//if ( $config['pk'] ) {
			$dispatcher	=	JEventDispatcher::getInstance();
		//}
		
		// Prepare
		$f		=	self::_getChild( $field, $config );
		$xn		=	( $value ) ? ( ( is_array( $value ) ? count( $value ) : $value ) ) : $field->rows;
		$xn		=	max( $field->minlength, $xn );
		$form	=	array();
		if ( $xn > 0 && is_object( $f ) ) {
			for ( $xi = 0; $xi < $xn; $xi++ ) {
				$f_value			=	'';
				if ( $config['pk'] ) {
					$table			=	$f->storage_table;
					if ( $table && ! isset( $config['storages'][$table] ) ) {
						$config['storages'][$table]	=	'';
						$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'] ) );
					}
					$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi ) );
				} else {
					$f_value		=	@$value[$xi];
				}
				$inherit			=	array( 'id' => $name.'__'.$xi, 'name' => $name.'[]', 'xk' => $xi );
				$clone				=	clone $f;
				$results			=	$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$clone, $f_value, &$config, $inherit, true ) );
				$form[$xi]			=	$results[0];
				$form[$xi]->name	=	$f->name;
			}
			if ( $form[0] ) {
				$field->display	=	$form[0]->display;
			}
			// Empty			
			$inherit			=	array( 'id' => $name.'__'.'0', 'name' => $name.'[]', 'xk' => '0', 'empty' => true );
			$clone				=	clone $f;
			$results			=	$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$clone, '', &$config, $inherit, true ) );
			$form[$xi]			=	$results[0];
			$form[$xi]->name	=	$f->name;
		}
		
		// Set
		$field->form	=	$form;
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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		$value		=	( isset( $config['post'][$name.'_hidden'] ) ) ? $config['post'][$name.'_hidden'] : $value;
		$dispatcher	=	JEventDispatcher::getInstance();
		
		// Prepare
		$store	=	'';
		$xk		=	0;
		$xi		=	0;
		if ( count( $value ) ) {
			$store		=	'<br />';	//begin?
			$f			=	self::_getChild( $field, $config );
			$f_name		=	$f->name;
			$f->state	=	'';	//todo;
			foreach ( $value as $val ) {
				//if ( $val != '' ) {
					$inherit	=	array( 'name' => $name, 'xk' => $xk, 'xi' => $xi, 'parent' => $name );					
					$results	=	$dispatcher->trigger( 'onCCK_FieldPrepareStore', array( &$f, $val, &$config, $inherit, true ) );
					$v			=	$results[0];
					
					if ( $v != '' ) {
						$store	.=	'<br />::'.$f_name.'|'.$xi.'|'.$name.'::'.$v.'::/'.$f_name.'|'.$xi.'|'.$name.'::';
						$xi++;
					}
					// todo: add childs (secondary) storages.. not primary!
				//}
				$xk++;
			}
			$store	.=	'<br />';	//end?
		}
		$field->values	=	$value;
		$value			=	$xi;
		$field->value	=	$value;

		parent::g_onCCK_FieldPrepareStore_X( $field, $name, $value, $store, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		if ( $field->typo ) {
			return $field->typo;
		}
		
		$html	=	'';
		if ( count( $field->value ) ) {
			$html	.=	'<ul class="cck-fl">';
			foreach ( $field->value as $elem ) {
				$html	.=	'<li>' . JCck::callFunc( 'plgCCK_Field'.$elem->type, 'onCCK_FieldRenderContent', $elem ) . '</li>';
			}
			$html	.=	'</ul>';
		}
		
		return $html;
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		$count	=	count( $field->form );
		$html	=	'';
		
		if ( $count ) {
			$html	.=	'<div id="sortable_'.$field->name.'" class="adminformlist">';
			for ( $i = 0; $i < $count - 1; $i++ ) {
				$html	.=	self::_getHtml( $field, $field->form[$i], $i );
			}
			$html	.=	'</div>';
			$empty	=	self::_getHtml( $field, $field->form[$i], 0 );
		}
		self::_addScripts( $field->name, array( 'min'=>$field->minlength, 'max'=>$field->maxlength, 'default'=>$field->rows,
												'del'=>$field->bool3, 'add'=>$field->bool2, 'drag'=>$field->bool4, 'empty_html'=>$empty ), $config );
		
		return $html;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _addScriptDeclaration
	protected static function _addScripts( $id, $params = array(), &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		
		$search					=	array( '.' , '<', '>', '"', '%', ';' );
		$replace				=	array( '\.', '\<', '\>', '\"', '\%', '\;' );
		$params['empty_html']	=	preg_replace( "/(\r\n|\n|\r)/", " ", $params['empty_html'] );
		$params['empty_html']	=	str_replace( $search, $replace, $params['empty_html'] );
		
		$css_s	=	self::$path.'assets/css/style2.css';
		$js		=	'jQuery(document).ready(function($) {';
		if ( $params['drag'] ) {
			$js	.=	'$("#sortable_'.$id.'").sortable({'
				.		'axis	: "y",'
				.		'handle	: ".button-drag",'
				.		'scroll: true,'
				.		'forcePlaceholderSize: true,'
				.		'start	: function(event, ui) {
							ui.item.css({"top":"0","left":"0"}); /* ~Fix */
							$(this).css({"overflow":"visible"});
						},
						stop	: function(event, ui) {
							ui.item.css({"top":"0","left":"0"}); /* ~Fix */
							$(this).css({"overflow":"auto"});
						}'
				.	'});'
				;
		}
		if ( $params['del'] ) {
			$js	.=		'var elem;'
				.		'var options = {};'
				.		'var name = "'.$id.'";'
				.		'var min_element = '.$params['min'].';'
				.		'time	=	500;'
				.		'$("#sortable_'.$id.'").on( "click", ".button-del-"+name, function() {'
				.			'elem	=	$(this).parent().parent().parent().parent();'
				.			'var n	=	elem.parent().children().length;'
				.			'if (n > min_element) {'
				.				'if(jQuery.fn.JCckFieldxDelBefore){elem.JCckFieldxDelBefore();}'
				.				'$(this).parent().parent().toggle(); $(this).parent().parent().parent().slideUp( "normal", callback(elem,time) );'
				.			'}'
				.		'});'
				.	'function callback(elem,time) {'
				.		'setTimeout(function() {'
				.			'elem.remove();'
				.		'}, time );'
				.	'}';
		}
		if ( $params['add'] ) {
			$js	.=		'var reg;'
				.		'var id_length;'
				.		'var id;'
				.		'var content;'
				.		'var reg = new RegExp();'
				.		'var elem = $("#button_del__"+name+"__0").parent().parent().parent().parent().parent();'
				.		'var tmp = ( elem.children().length );'
				.		'var length;'
				.		'var options = {color:"#ffffdd"};'
				.		'var max_element = '.$params['max'].';'
				.		'var new_elem = "'.$params['empty_html'].'";'
				.		'content = new_elem;'
				.		'$("#sortable_'.$id.'").on( "click", ".button-add-"+name, function() {'
				.			'elem = $(this).parent().parent().parent().parent();'
				.			'length = ( elem.parent().children().length );'
				.			'if (length < max_element) {'
				.				'content = new_elem;'
				.				'reg = RegExp(name+"__"+"0","g");'
				.				'content = content.replace(reg,name+"__"+tmp);'
				.				'reg = RegExp(name+"[\[]"+"0","g");'
				.				'content = content.replace(reg,name+"["+tmp);'
				.				'elem.after(content); if(jQuery.fn.JCckFieldxAddAfter){elem.JCckFieldxAddAfter();}'
				.				'$("#button_add__"+name+"__"+tmp).parent().parent().parent().parent().show( "highlight", options, 1000 );'
				.				'tmp = tmp + 1;'
				.			'}'
				.		'});';
		}
		$js		.=	'});';
		
		if ( isset( $config['tmpl'] ) && $config['tmpl'] == 'ajax' ) {
			echo '<link rel="stylesheet" href="'.$css_s.'" type="text/css" />';
			echo '<script type="text/javascript">'.$js.'</script>';
		} elseif ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<link rel="stylesheet" href="'.$css_s.'" type="text/css" />';
			echo '<script src="'.JUri::root( true ).'/media/cck/js/jquery.ui.min.js" type="text/javascript"></script>';
			echo '<script type="text/javascript">'.$js.'</script>';
		} else {
			JCck::loadjQuery();
			JCck::loadjQueryUI();
			$doc->addStyleSheet( $css_s );
			$doc->addScriptDeclaration( $js );
		}
	}

	// _getHtml
	protected static function _getHtml( $field, $elem, $i )
	{
		$html	=	'<div>';
		$html	.=	'<div id="collection-group-wrap-'.$field->name.'__'.$i.'" class="collection-group-wrap">';
		$html	.=	'<div id="collection-group-form-'.$field->name.'__'.$i.'" class="collection-group-form">';
		$html	.=	$elem->form;
		$html	.=	'</div>';
		$html	.=	'<div id="collection-group-button-'.$field->name.'__'.$i.'" class="collection-group-button">';
		if ( $field->bool3 ) {
			$html	.=	'<div class="button-del">'
					.		'<span id="button_del'.'__'.$field->name.'__'.$i.'" class="button-del-'.$field->name.' icon-minus"></span>'
					.	'</div> ';
		}
		if ( $field->bool2 ) {
			$html	.=	'<div class="button-add">'
					.		'<span id="button_add'.'__'.$field->name.'__'.$i.'" class="button-add-'.$field->name.' icon-plus"></span>'
					.	'</div> ';
		}
		if ( $field->bool4 ) {
			$html	.=	'<div class="button-drag">'
					.		'<span id="button_drag'.'" class="icon-circle"></span>'
					.	'</div> ';
		}
		$html	.=	'</div>';
		$html	.=	'</div>';
		$html	.=	'</div>';
		
		return $html;
	}
	
	// _getChild
	protected static function _getChild( $parent, $config = array() )
	{
		$field	=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name="'.$parent->extended.'"' ); //#
		if ( is_object( $field ) ) {
			$field->access				=	@$parent->access;
			$field->variation			=	@$parent->variation;
			$field->variation_override	=	@$parent->variation_override;
			$field->required			=	@$parent->required;
			$field->required_alert		=	@$parent->required_alert;
			$field->validation			=	@$parent->validation;
			$field->validation_options	=	@$parent->validation_options;
			$field->restriction			=	@$parent->restriction;
			$field->restriction_options	=	@$parent->restriction_options;
			$field->computation			=	@$parent->computation;
			$field->computation_options	=	@$parent->computation_options;
			$field->conditional			=	@$parent->conditional;
			$field->conditional_options	=	@$parent->conditional_options;
			$field->link				=	'';
			$field->link_options		=	'';
			$field->typo				=	'';
			$field->typo_label			=	'';
			$field->typo_options		=	'';
			$field->storage				=	$parent->storage;
			$field->storage_table		=	$parent->storage_table;
			$field->storage_field		=	$parent->storage_field;
		}
		
		return $field;
	}
}
?>