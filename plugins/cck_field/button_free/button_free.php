<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

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
		$data['computation']	=	null;
		$data['live']			=	null;
		$data['validation']		=	null;

		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']	=	array(
										'hidden'=>HTMLHelper::_( 'select.option', 'hidden', Text::_( 'COM_CCK_HIDDEN' ) ),
										'value'=>HTMLHelper::_( 'select.option', 'value', Text::_( 'COM_CCK_VALUE' ) ),
										'100'=>HTMLHelper::_( 'select.option', '<OPTGROUP>', Text::_( 'COM_CCK_FORM' ) ),
										''=>HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_DEFAULT' ) ),
										'disabled'=>HTMLHelper::_( 'select.option', 'disabled', Text::_( 'COM_CCK_FORM_DISABLED2' ) ),
										'101'=>HTMLHelper::_( 'select.option', '</OPTGROUP>', '' ),
										'102'=>HTMLHelper::_( 'select.option', '<OPTGROUP>', Text::_( 'COM_CCK_TOOLBAR' ) ),
										'toolbar_button'=>HTMLHelper::_( 'select.option', 'toolbar_button', Text::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
										'103'=>HTMLHelper::_( 'select.option', '</OPTGROUP>', '' )
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
		$data['live']		=	null;
		$data['match_mode']	=	null;
		$data['validation']	=	null;

		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']	=	array(
										'hidden'=>HTMLHelper::_( 'select.option', 'hidden', Text::_( 'COM_CCK_HIDDEN' ) ),
										'value'=>HTMLHelper::_( 'select.option', 'value', Text::_( 'COM_CCK_VALUE' ) ),
										'100'=>HTMLHelper::_( 'select.option', '<OPTGROUP>', Text::_( 'COM_CCK_FORM' ) ),
										''=>HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_DEFAULT' ) ),
										'disabled'=>HTMLHelper::_( 'select.option', 'disabled', Text::_( 'COM_CCK_FORM_DISABLED2' ) ),
										'101'=>HTMLHelper::_( 'select.option', '</OPTGROUP>', '' ),
										'102'=>HTMLHelper::_( 'select.option', '<OPTGROUP>', Text::_( 'COM_CCK_TOOLBAR' ) ),
										'toolbar_button'=>HTMLHelper::_( 'select.option', 'toolbar_button', Text::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
										'103'=>HTMLHelper::_( 'select.option', '</OPTGROUP>', '' )
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
		$field->label2	=	isset( $field->label2 ) ? trim( $field->label2 ) : '';
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
					
					if ( $field1->link && strpos( $field1->link, 'javascript:' ) === false
					  && strpos( $onclick, 'if(' ) !== false && strpos( $onclick, 'else{' ) === false ) {
						$onclick	.=	'else{document.location.href=\''.$field1->link.'\'};';
					}
				} elseif ( $field1->link ) {
					if ( isset( $field1->link_target ) && $field1->link_target == '_blank' ) {
						$onclick	=	'var otherWindow = window.open(); otherWindow.opener = null; otherWindow.location = \''.$field1->link.'\';';					
					} else {
						$onclick	=	'document.location.href=\''.$field1->link.'\'';
					}
				}
				if ( $onclick ) {
					$onclick	=	' onclick="'.$onclick.'"';
				}
			}
			$class	.=	( isset( $field1->link_class ) && $field1->link_class ) ? ' '.$field1->link_class : '';
			$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' ).$onclick;
			if ( $field->bool ) {
				$label		=	$value;
				
				if ( !( isset( $options2['tag'] ) && $options2['tag'] ) ) {
					$options2['tag']	=	'span';
				}
				if ( !JCck::is( '7.0' ) ) {
					$options2['icon']	=	'icon-'.$options2['icon'];
				}
				if ( $field->bool6 == 3 ) {
					$label		=	'<'.$options2['tag'].' class="'.$options2['icon'].'"></'.$options2['tag'].'>';
					$attr		.=	' title="'.$value.'"';
				} elseif ( $field->bool6 == 2 ) {
					$label		=	$value."\n".'<'.$options2['tag'].' class="'.$options2['icon'].'"></'.$options2['tag'].'>';
				} elseif ( $field->bool6 == 1 ) {
					$label		=	'<'.$options2['tag'].' class="'.$options2['icon'].'"></'.$options2['tag'].'>'."\n".$value;
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
			$alt	=	$field->bool3 ? ' '.Text::_( 'COM_CCK_OR' ).' ' : "\n";
			if ( $config['client'] == 'search' ) {
				$onclick	=	'onclick="return;"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.Text::_( 'COM_CCK_RESET' ).'">'.Text::_( 'COM_CCK_RESET' ).'</a>';				
			} else {
				$onclick	=	'onclick="return;"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.Text::_( 'COM_CCK_CANCEL' ).'">'.Text::_( 'COM_CCK_CANCEL' ).'</a>';
			}
		} elseif ( $field->bool2 == 2 ) {
			$alt		=	$field->bool3 ? ' '.Text::_( 'COM_CCK_OR' ).' ' : "\n";
			$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
			JCckPluginLink::g_setLink( $field2, $config );
			JCckPluginLink::g_setHtml( $field2, 'text' );
			if ( $field->bool != '-1' ) {
				$form	.=	$alt;
			}
			$form		.=	$field2->html;
		}
 		if ( $field->bool4 == 2 ) {
			$alt		=	$field->bool5 ? ' '.Text::_( 'COM_CCK_OR' ).' ' : "\n";
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
					if ( isset( $field1->link_target ) && $field1->link_target == '_blank' ) {
						$onclick	=	'var otherWindow = window.open(); otherWindow.opener = null; otherWindow.location = \''.$field1->link.'\';';					
					} else {
						$onclick	=	'document.location.href=\''.$field1->link.'\'';
					}
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
				
				if ( !( isset( $options2['tag'] ) && $options2['tag'] ) ) {
					$options2['tag']	=	'span';
				}
				if ( !JCck::is( '7.0' ) ) {
					$options2['icon']	=	'icon-'.$options2['icon'];
				}
				if ( $field->bool6 == 3 ) {
					$label		=	'<'.$options2['tag'].' class="'.$options2['icon'].'"></'.$options2['tag'].'>';
					if ( !$title ) {
						$title	=	' title="'.$value.'"';
					}
				} elseif ( $field->bool6 == 2 ) {
					$label		=	$value."\n".'<'.$options2['tag'].' class="'.$options2['icon'].'"></'.$options2['tag'].'>';
				} elseif ( $field->bool6 == 1 ) {
					$label		=	'<'.$options2['tag'].' class="'.$options2['icon'].'"></'.$options2['tag'].'>'."\n".$value;
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
				$alt	=	$field->bool3 ? ' '.Text::_( 'COM_CCK_OR' ).' ' : "\n";
				if ( $config['client'] == 'search' ) {
					$onclick	=	'onclick="return;"';
					$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.Text::_( 'COM_CCK_RESET' ).'">'.Text::_( 'COM_CCK_RESET' ).'</a>';				
				} else {
					$onclick	=	'onclick="return;"';
					$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.Text::_( 'COM_CCK_CANCEL' ).'">'.Text::_( 'COM_CCK_CANCEL' ).'</a>';
				}
			} elseif ( $field->bool2 == 2 ) {
				$alt		=	$field->bool3 ? ' '.Text::_( 'COM_CCK_OR' ).' ' : "\n";
				$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
				JCckPluginLink::g_setLink( $field2, $config );
				JCckPluginLink::g_setHtml( $field2, 'text' );
				if ( $field->bool != '-1' ) {
					$form	.=	$alt;
				}
				$form		.=	$field2->html;
			}
	 		if ( $field->bool4 == 2 ) {
				$alt		=	$field->bool5 ? ' '.Text::_( 'COM_CCK_OR' ).' ' : "\n";
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
				if ( isset( $options2['icon'] ) && $options2['icon'] ) {
					$icon	=	$options2['icon'];

					if ( !JCck::is( '7.0' ) ) {
						$icon	=	'icon-'.$icon;
					}
				} else {
					$icon	=	'';
				}
				$icon_tag		=	( isset( $options2['tag'] ) && $options2['tag'] ) ? $options2['tag'] : 'span';
				$html			=	'<button class="btn btn-small'.( $field->css ? ' '.$field->css : '' ).'" '.$onclick.'><'.$icon_tag.' class="'.$icon.'"></'.$icon_tag.'> '.$value.'</button>';
				Toolbar::getInstance( 'toolbar' )->appendButton( 'Custom', $html, $icon );
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