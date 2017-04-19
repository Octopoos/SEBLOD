<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldTabs extends JCckPluginField
{
	protected static $type		=	'tabs';
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

	// onCCK_FieldConstruct_SearchContent
	public static function onCCK_FieldConstruct_SearchContent( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']			=	NULL;
		$data['markup_class']	=	NULL;

		parent::onCCK_FieldConstruct_SearchContent( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['match_mode']		=	NULL;
		$data['markup']			=	NULL;
		$data['markup_class']	=	NULL;
		$data['validation']		=	NULL;
		$data['variation']		=	NULL;
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// onCCK_FieldConstruct_TypeContent
	public static function onCCK_FieldConstruct_TypeContent( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']			=	NULL;
		$data['markup_class']	=	NULL;

		parent::onCCK_FieldConstruct_TypeContent( $field, $style, $data, $config );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['markup']			=	NULL;
		$data['markup_class']	=	NULL;
		$data['validation']		=	NULL;
		$data['variation']		=	NULL;

		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
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
		$id			=	$field->name;
		$value		=	(int)$field->defaultvalue;
		$value		=	( $value ) ? $value - 1 : 0;
		$group_id	=	( $field->location != '' ) ? $field->location : 'cck_tabs1';

		// Prepare
		$html		=	'';
		if ( $field->state ) {
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'group_id'=>$group_id, 'id'=>$id, 'identifier'=>$field->bool3, 'label'=>$field->label, 'url_actions'=>$field->bool2, 'value'=>$value ), 5 );
		}

		// Set
		$field->html	=	$html;
		$field->value	=	$field->label;
		$field->label	=	'';
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
		} else {
			$id		=	$field->name;
		}
		$value		=	( $value != '' ) ? (int)$value : (int)$field->defaultvalue;
		$value		=	( $value ) ? $value - 1 : 0;
		$group_id	=	( $field->location != '' ) ? $field->location : 'cck_tabs1';
		
		// Prepare
		$form		=	'';
		if ( $field->state ) {
			parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'group_id'=>$group_id, 'id'=>$id, 'identifier'=>$field->bool3, 'label'=>$field->label, 'url_actions'=>$field->bool2, 'value'=>$value ), 5 );
		}

		// Set
		$field->form	=	$form;	// todo: '<div class="tabbable tabs-left">'
		$field->value	=	$field->label;
		$field->label	=	'';
		
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
	public static function onCCK_FieldRenderContent( &$field, &$config = array() )
	{
		$field->markup	=	'none';

		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( &$field, &$config = array() )
	{
		$field->markup	=	'none';

		return parent::g_onCCK_FieldRenderForm( $field );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderContent
	public static function onCCK_FieldBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}
		
		self::_prepare( 'html', $process, $fields );
	}

	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}
		
		self::_prepare( 'form', $process, $fields );
	}

	// _prepare
	protected static function _prepare( $target, $process, &$fields )
	{
		$id				=	$process['id'];
		$label			=	$process['label'];
		$name			=	$process['name'];
		$group_id		=	$process['group_id'];
		$value			=	$process['value'];

		static $groups	=	array();
		if ( !isset( $groups[$group_id] ) ) {
			$groups[$group_id]	=	array( 'active'=>$value, 'current'=>0, 'identifier'=>$process['identifier'], 'url_actions'=>$process['url_actions'] );
		}
		if ( $groups[$group_id]['identifier'] ) {
			$id			=	JCckDev::toSafeID( $label );
		}
		
		if ( $fields[$name]->bool == 2 ) {
			$html	=	JCckDevTabs::end();
		} elseif ( $fields[$name]->bool == 1 ) {
			$html	=	JCckDevTabs::open( $group_id, $id, $label );
			if ( $target == 'form' ) {
				$html	=	str_replace( 'class="tab-pane', 'class="tab-pane cck-tab-pane', $html );
			}
			$js		=	'';
			if ( $groups[$group_id]['current'] == $groups[$group_id]['active'] ) {
				$js	=	'$("#'.$group_id.'Tabs > li,#'.$group_id.'Content > div").removeClass("active"); $("#'.$group_id.'Tabs > li:eq('.(int)$groups[$group_id]['active'].'),#'.$id.'").addClass("active");';
			}
			if ( $groups[$group_id]['url_actions'] ) {
				$js	=	'var cur = window.location.hash; if(cur!="" && $(cur).length) { $("a[href=\'"+cur+"\']").tab("show"); }';
				if ( $groups[$group_id]['url_actions'] == 2 ) {
					$js	.=	' $("a[data-toggle=\'tab\']").on("shown", function(e) {window.location.hash = e.target.hash;})';
				}
			}
			if ( $js ) {
				$js	=	'(function($){ $(document).ready(function() { '.$js.' }); })(jQuery);';
				JFactory::getDocument()->addScriptDeclaration( $js );
			}
		} else {
			$html	=	JCckDevTabs::start( $group_id, $id, $label, array( 'active'=>$id ) );
			if ( $target == 'form' ) {
				$html	=	str_replace( 'class="nav nav-tabs"', 'class="nav nav-tabs cck-tabs"', $html );
				$html	=	str_replace( 'class="tab-pane', 'class="tab-pane cck-tab-pane', $html );
			}
		}
		$groups[$group_id]['current']++;

		$fields[$name]->$target	=	$html;
	}
}
?>