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
class plgCCK_FieldSearch_Ordering extends JCckPluginField
{
	protected static $type		=	'search_ordering';
	protected static $friendly	=	1;
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
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['match_mode'][self::$type] ) ) {
			$data['match_mode']	=	array(
										'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_DISABLED' ) ),
										''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_ENABLED' ) )
									);

			$config['construction']['match_mode'][self::$type]	=	$data['match_mode'];
		} else {
			$data['match_mode']									=	$config['construction']['match_mode'][self::$type];
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
		
		// Set
		$field->value	=	$value;
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
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value, ENT_QUOTES );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( isset( $inherit['opts'] ) ) {
			$opts				=	$inherit['opts'];
		} else {
			$field->children	=	self::_getChildren( $field, $config );
			if ( count( $field->children ) ) {
				foreach ( $field->children as $k=>$child ) {
					$text		=	$child->label;
					if ( $config['doTranslation'] && trim( $text ) ) {
						$text	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
					}
					if ( $field->bool ) {
						$val	=	(string)$k;
					} else {
						$val	=	(int)$k;
					}
					$opts[]		=	JHtml::_( 'select.option', $val, $text, 'value', 'text' );
				}
			}
		}
		$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
		if ( $value != '' ) {
			$class	.=	' has-value';
		}
		$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'';
		if ( count( $opts ) ) {
			$form	=	JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
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
		$field->children	=	self::_getChildren( $field, $config );
		$opts				=	array();
		
		if ( $field->extended ) {
			if ( count( $field->children ) ) {
				foreach ( $field->children as $k=>$child ) {
					$text				=	$child->label;
					if ( $config['doTranslation'] && trim( $text ) ) {
						$text			=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
					}
					$opts[]				=	$text.'='.(string)$k;
				}
				$opts	=	implode( '||', $opts );
			}
			$extended		=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name = "'.$field->extended.'"' );
			if ( is_object( $extended ) ) {
				$form					=	JCckDevField::getForm( $extended, $value, $config, array( 'id'=>$field->id, 'name'=>$field->name,
																									  'options'=>$opts, 'variation'=>$field->variation ) );
				$field->form			=	$form;
				$field->value			=	$value;
				$field->markup			=	'';
				$field->markup_class	.=	' cck_form_'.$extended->type;
				if ( $field->variation == 'clear' ) {
					$field->display		=	0;
				} elseif ( $field->variation == 'hidden' ) {
					$field->display		=	1;
				}
			} else {
				$field->form			=	'';
				$field->form			=	$value;
			}
		} else {
			if ( count( $field->children ) ) {
				foreach ( $field->children as $k=>$child ) {
					$text				=	$child->label;
					if ( $config['doTranslation'] && trim( $text ) ) {
						$text			=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
					}
					if ( $field->bool ) {
						$val	=	(string)$k;
					} else {
						$val	=	(int)$k;
					}
					$opts[]				=	JHtml::_( 'select.option', $val, $text, 'value', 'text' );
				}
			}
			$inherit['opts']	=	$opts;
			// todo: type2
			self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		}
		
		// Set
		$field->type				=	self::$type;
		$field->storage				=	'';
		$field->storage_location	=	'';
		$field->storage_table		=	'';
		$field->storage_field		=	'';
		
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
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _getChildren
	protected static function _getChildren( $parent, $config = array() )
	{
		$fieldnames	=	array();
		$options2	=	json_decode( $parent->options2, true );

		if ( strpos( $parent->options, '=' ) !== false ) {
			$fields		=	explode( '||', $parent->options );
			$names		=	'';
			$names2		=	'';
			if ( count( $fields ) ) {
				$i		=	0;

				foreach ( $fields as $f ) {
					$prepend	=	( isset( $options2['options'][$i]['prepend'] ) && $options2['options'][$i]['prepend'] ) ? $options2['options'][$i]['prepend'] : '';

					if ( $prepend ) {
						if ( strpos( $prepend, ' ' ) !== false ) {
							$prepend	=	explode( ' ', $prepend );
							$prepend	=	$prepend[0];
						}
						if ( $prepend ) {
							$names2	.=	'"'.$prepend.'",';
						}
					}
					if ( strpos( $f, '=' ) !== false ) {
						$v	=	explode( '=', $f );
						$fieldnames[]	=	(object)array( 'key'=>$v[1], 'val'=>$v[0] );
						$names	.=	'"'.$v[1].'",';
						$names2	.=	'"'.$v[1].'",';
					} else {
						$fieldnames[]	=	(object)array( 'key'=>$f, 'val'=>$f );
						$names	.=	'"'.$f.'",';
						$names2	.=	'"'.$f.'",';
					}
					$i++;
				}
				$names	=	substr( $names, 0, -1 );
				$names2	=	substr( $names2, 0, -1 );
			}
		} else {
			$names	=	'"'.str_replace( '||', '","', $parent->options ).'"';
			$names2	=	'"'.str_replace( '||', '","', $parent->options ).'"';
		}
		
		$query		= 	'SELECT a.name, a.label, a.type, a.storage_table, a.storage_field, a.storage_field2'
					.	' FROM #__cck_core_fields AS a'
					.	' WHERE a.name IN ('.$names2.') ORDER BY FIELD(name, '.$names2.')'
					;
		$fields2	=	JCckDatabase::loadObjectList( $query, 'name' );
		$fields		=	array();
		$names		=	explode( ',', str_replace( '"', '', $names ) );
		$override	=	true;

		if ( ! count( $fieldnames ) ) {
			$override			=	false;
			foreach ( $names as $k=>$v ) {
				$fieldnames[]	=	(object)array( 'key'=>$v, 'val'=>$v );
			}
		}
		if ( ! count( $fields2 ) ) {
			return array();
		}
		if ( count( $fieldnames ) ) {
			$i	=	0;
			foreach ( $names as $name ) {
				if ( is_object( $fields2[$name] ) ) {
					$dir		=	( isset( $options2['options'][$i]['direction'] ) && $options2['options'][$i]['direction'] ) ? $options2['options'][$i]['direction'] : 'ASC';
					$prepend	=	( isset( $options2['options'][$i]['prepend'] ) && $options2['options'][$i]['prepend'] ) ? $options2['options'][$i]['prepend'] : '';
					
					if ( $parent->bool ) {
						$idx	=	$fields2[$name]->name.':'.strtolower( $dir );
					} else {
						$idx	=	$i;
					}
					$fields[$idx]				=	clone $fields2[$name];
					$fields[$idx]->match_mode	=	strtoupper( $dir );
					if ( $override ) {
						foreach ( $fieldnames as $k=>$f ) {
							if ( $name == $f->key ) {
								$fields[$idx]->label	=	$f->val;
								unset($fieldnames[$k]);
								break;
							}
						}
					}
					if ( $prepend ) {
						if ( strpos( $prepend, ' ' ) !== false ) {
							$prepend	=	explode( ' ', $prepend );
						} else {
							$prepend	=	array( 0=>$prepend, 1=>'ASC' );
						}
						if ( isset( $fields2[$prepend[0]] ) ) {
							$fields[$idx]->prepend				=	$fields2[$prepend[0]];
							$fields[$idx]->prepend->match_mode	=	strtoupper( $prepend[1] );
						}
					}
					$i++;
				}
			}
		}
		
		return $fields;
	}
	
	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>