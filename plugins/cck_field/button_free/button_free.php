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
class plgCCK_FieldButton_Free extends JCckPluginField
{
	protected static $type		=	'button_free';
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
			$data['variation']	=	$config['construction']['variation'][self::$type];
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
			$data['variation']	=	$config['construction']['variation'][self::$type];
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
		self::$path		=	parent::g_getPath( self::$type.'/' );
		$field->label2	=	trim( @$field->label2 );
		parent::g_onCCK_FieldPrepareContent( $field, $config );

		// Init
		$id				=	$field->name;
		$name			=	$field->name;
		$value			=	$field->label;
		$field->label	=	'';
		
		// Prepare
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$class		=	( strpos( $field->css, 'close' ) !== false ) ? $field->css : ( 'button btn' . ( $field->css ? ' '.$field->css : '' ) );
		$onclick	=	'';
		$form		=	'';
		if ( $field->bool != -1 ) {
			if ( isset( $options2['button_link'] ) && $options2['button_link'] ) {
				$field1		=	(object)array( 'link'=>$options2['button_link'], 'link_options'=>$options2['button_link_options'],
											   'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $value ), 'value'=>'' );
				JCckPluginLink::g_setLink( $field1, $config );
				JCckPluginLink::g_setHtml( $field1, 'text' );
				if ( isset( $field1->link_onclick ) && $field1->link_onclick ) {
					$onclick	=	$field1->link_onclick;
					
					if ( $field1->link && strpos( $field1->link, 'javascript:' ) === false ) {
						$onclick	.=	'else{document.location.href=\''.$field1->link.'\'};';
					}
				} elseif ( $field1->link ) {
					$onclick	=	( isset( $field1->link_target ) && $field1->link_target == '_blank' ) ? 'window.open(\''.$field1->link.'\',\'_blank\')' : 'document.location.href=\''.$field1->link.'\'';
				}
				if ( $onclick ) {
					$onclick	=	' onclick="'.$onclick.'"';
				}
			}
			$class	.=	( isset( $field1->link_class ) && $field1->link_class ) ? ' '.$field1->link_class : '';
			$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' ).$onclick;
			if ( $field->bool ) {
				$label		=	$value;
				
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
				$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
				$form	=	'<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
				$tag	=	'input';
			}
		}
		if ( $field->bool2 == 1 ) {
			$alt	=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			if ( $config['client'] == 'search' ) {
				$onclick	=	'onclick="return;"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_RESET' ).'">'.JText::_( 'COM_CCK_RESET' ).'</a>';				
			} else {
				$onclick	=	'onclick="return;"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_CANCEL' ).'">'.JText::_( 'COM_CCK_CANCEL' ).'</a>';
			}
		} elseif ( $field->bool2 == 2 ) {
			$alt		=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
			JCckPluginLink::g_setLink( $field2, $config );
			JCckPluginLink::g_setHtml( $field2, 'text' );
			if ( $field->bool != '-1' ) {
				$form	.=	$alt;
			}
			$form		.=	$field2->html;
		}
 		if ( $field->bool4 == 2 ) {
			$alt		=	$field->bool5 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			$field2		=	(object)array( 'link'=>$options2['alt2_link'], 'link_options'=>$options2['alt2_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt2_link_text'] ), 'value'=>'' );
			JCckPluginLink::g_setLink( $field2, $config );
			JCckPluginLink::g_setHtml( $field2, 'text' );
			if ( $field->bool != '-1' || $field->bool2 ) {
				$form	.=	$alt;
			}
			$form		.=	$field2->html;
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
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$canDo		=	true;
		$class		=	( strpos( $field->css, 'close' ) !== false ) ? $field->css : ( 'button btn' . ( $field->css ? ' '.$field->css : '' ) );
		$onclick	=	'';
		$form		=	'';
		if ( $field->bool != -1 ) {
			if ( isset( $options2['button_link'] ) && $options2['button_link'] ) {
				$field1		=	(object)array( 'link'=>$options2['button_link'], 'link_options'=>$options2['button_link_options'],
											   'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $value ), 'value'=>'' );
				JCckPluginLink::g_setLink( $field1, $config );
				JCckPluginLink::g_setHtml( $field1, 'text' );
				if ( isset( $field1->link_onclick ) && $field1->link_onclick ) {
					$onclick	=	$field1->link_onclick;
					
					if ( $field1->link && strpos( $field1->link, 'javascript:' ) === false ) {
						$onclick	.=	'else{document.location.href=\''.$field1->link.'\'};';
					}				
				} elseif ( $field1->link ) {
					$onclick	=	( isset( $field1->link_target ) && $field1->link_target == '_blank' ) ? 'window.open(\''.$field1->link.'\',\'_blank\')' : 'document.location.href=\''.$field1->link.'\'';
				} else {
					$canDo		=	false;
				}
				if ( $onclick ) {
					$onclick	=	' onclick="'.$onclick.'"';
				}
			}
			$class	.=	( isset( $field1->link_class ) && $field1->link_class ) ? ' '.$field1->link_class : '';
			$title	=	( isset( $field1->link_title ) && $field1->link_title ) ? ' title="'.$field1->link_title.'"' : '';
			$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' ).$onclick;
			if ( $field->bool ) {
				$label		=	$value;
				
				if ( $field->bool6 == 3 ) {
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>';
					if ( !$title ) {
						$title	=	' title="'.$value.'"';
					}
				} elseif ( $field->bool6 == 2 ) {
					$label		=	$value."\n".'<span class="icon-'.$options2['icon'].'"></span>';
				} elseif ( $field->bool6 == 1 ) {
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>'."\n".$value;
				}
				$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
				$form	=	'<button type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$attr.$title.'>'.$label.'</button>';
				$tag	=	'button';
			} else {
				$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
				$form	=	'<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.$title.' />';
				$tag	=	'input';
			}
		}
		if ( !$canDo ) {
			$form	=	'';
		} else {
			if ( $field->bool2 == 1 ) {
				$alt	=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
				if ( $config['client'] == 'search' ) {
					$onclick	=	'onclick="return;"';
					$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_RESET' ).'">'.JText::_( 'COM_CCK_RESET' ).'</a>';				
				} else {
					$onclick	=	'onclick="return;"';
					$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_CANCEL' ).'">'.JText::_( 'COM_CCK_CANCEL' ).'</a>';
				}
			} elseif ( $field->bool2 == 2 ) {
				$alt		=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
				$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
				JCckPluginLink::g_setLink( $field2, $config );
				JCckPluginLink::g_setHtml( $field2, 'text' );
				if ( $field->bool != '-1' ) {
					$form	.=	$alt;
				}
				$form		.=	$field2->html;
			}
	 		if ( $field->bool4 == 2 ) {
				$alt		=	$field->bool5 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
				$field2		=	(object)array( 'link'=>$options2['alt2_link'], 'link_options'=>$options2['alt2_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt2_link_text'] ), 'value'=>'' );
				JCckPluginLink::g_setLink( $field2, $config );
				JCckPluginLink::g_setHtml( $field2, 'text' );
				if ( $field->bool != '-1' || $field->bool2 ) {
					$form	.=	$alt;
				}
				$form		.=	$field2->html;
			}
		}

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			if ( $field->variation == 'toolbar_button' ) {
				if ( !isset( $options2 ) ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
				}
				$field->form	=	'';
				$icon			=	( isset( $options2['icon'] ) && $options2['icon'] ) ? 'icon-'.$options2['icon'] : '';
				$html			=	'<button class="btn btn-small'.( $field->css ? ' '.$field->css : '' ).'" '.$onclick.'><span class="'.$icon.'"></span> '.$value.'</button>';
				JToolBar::getInstance( 'toolbar' )->appendButton( 'Custom', $html, @$options2['icon'] );
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
}
?>