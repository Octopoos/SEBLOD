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

use Joomla\String\StringHelper;

// Plugin
class plgCCK_FieldSelect_Dynamic extends JCckPluginField
{
	protected static $type			=	'select_dynamic';
	protected static $convertible	=	1;
	protected static $friendly		=	1;
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Add Database Process
		if ( $data['bool2'] == 0 ) {
			$ext	=	JFactory::getConfig()->get( 'dbprefix' );

			if ( isset( $data['json']['options2']['table'] ) ) {
				$data['json']['options2']['table']	=	str_replace( $ext, '#__', $data['json']['options2']['table'] );
			}
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_AUTO' ) );
			$data['variation']['hidden_auto']	=	JHtml::_( 'select.option', 'hidden_auto', JText::_( 'COM_CCK_HIDDEN_AND_SECURED' ) );
			$data['variation']['202']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$data['variation']['203']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAR_IS_SECURED' ) );
			$data['variation']['204']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );

			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']									=	$config['construction']['variation'][self::$type];
		}
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']['201']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_AUTO' ) );
			$data['variation']['hidden_auto']	=	JHtml::_( 'select.option', 'hidden_auto', JText::_( 'COM_CCK_HIDDEN' ) );
			$data['variation']['202']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$data['variation']['203']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LIST' ) );
			$data['variation']['list']			=	JHtml::_( 'select.option', 'list', JText::_( 'COM_CCK_DEFAULT' ) );
			$data['variation']['list_filter']	=	JHtml::_( 'select.option', 'list_filter', JText::_( 'COM_CCK_FORM_FILTER' ) );
			/*
			$data['variation']['list_filter_ajax']	=	JHtml::_( 'select.option', 'list_filter_ajax', JText::_( 'COM_CCK_FORM_FILTER_AJAX' ) );
			*/
			$data['variation']['204']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$data['variation']['205']			=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAR_IS_SECURED' ) );
			$data['variation']['206']			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			
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
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$divider	=	'';
		$lang_code	=	'';
		$value2		=	'';

		// Prepare
		if ( $field->bool3 ) {
			$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		}

		$optgroups			=	false;
		$options			=	self::_getStaticOption( $field, $field->options, $config, $optgroups );
		$options_1			=	array();

		if ( count( $options ) ) {
			foreach ( $options as $o ) {
				if ( !( $o->value == '<OPTGROUP>' || $o->value == '</OPTGROUP>' ) ) {
					$options_1[]	=	$o->text.'='.$o->value;
				}
			}
		}
		$options_1			=	( count( $options_1 ) ) ? implode( '||', $options_1 ).'||' : '';

		/* tmp */
		$jtext						=	$config['doTranslation'];
		$config['doTranslation']	=	0;
		/* tmp */

		self::_languageDetection( $lang_code, $value2, $options2 );
		$options_2			=	self::_getOptionsList( $options2, $field->bool2, $lang_code, true );
		$field->options		=	$options_1.$options_2;

		// Set
		$field->text		=	parent::g_getOptionText( $value, $field->options, $divider, $config );
		$field->value		=	$value;
		$field->typo_target	=	'text';

		/* tmp */
		$config['doTranslation']	=	$jtext;
		/* tmp */
	}
	
	// onCCK_FieldPrepareExport
	public function onCCK_FieldPrepareExport( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		
		self::onCCK_FieldPrepareContent( $field, $value, $config );
		
		$field->output	=	$field->text;
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
		$name		=	( @$field->bool3 ) ? $name.'[]' : $name;
		$divider	=	'';
		if ( $field->bool3 ) {
			$divider	=	( $field->divider != '' ) ? $field->divider : ',';			
			if ( !is_array( $value ) ) {
				$value		=	explode( $divider, $value );
			}
		} else {
			$field->divider	=	'';
		}
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( parent::g_isStaticVariation( $field, $field->variation, true ) ) {
			if ( is_array( $value ) ) {
				$value		=	implode( $divider, $value );
			}
			$form			=	'';
			$field->text	=	'';
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<input', '', '', $config );
		} else {
			$attr		=	array( 'option.attr'=>'data-cck' );
			$attributes	=	array();
			$auto		=	1;
			$items		=	array();
			$options	=	array();
			$opts		=	array();
			if ( $field->location ) {
				$attribs	=	explode( '||', $field->location );
				$attrib		=	count( $attribs );
			} else {
				$attribs	=	array();
				$attrib		=	0;
			}
			if ( trim( $field->selectlabel ) ) {
				if ( $config['doTranslation'] ) {
					$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
				}
				if ( $attrib ) {
					$attr['attr']	=	'';
					foreach ( $attribs as $k=>$a ) {
						$attr['attr']	.=	' '.$a.'=""';
					}
					$attributes[]	=	$attr['attr'];
					$opts[]			=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', $attr );
				} else {
					$opts[]			=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', 'value', 'text' );
				}
				$options[]			=	$field->selectlabel.'=';

				if ( $field->required ) {
					$auto++;
				}
			}
			$count2			=	JCck::getConfig_Param( 'development_attr', 6 );
			$opt_attr		=	'';
			$opt_attrs		=	array();
			$options2		=	JCckDev::fromJSON( $field->options2 );
			$optgroups		=	false;

			if ( $field->bool4 == 1 || $field->bool4 == 3 ) {
				$results	=	self::_getStaticOption( $field, $field->options, $config, $optgroups );
				$static		=	count( $results );

				if ( $field->bool4 == 3 ) {
					if ( $static > 0 ) {
						$current	=	0;
						$half		=	(int)( $static / 2 );
						$half		=	( $static % 2 ) ? $half+1 : $half;
						
						for ( $current = 0; $current < $half; $current++ ) {
							if ( !( $results[$current]->value == '<OPTGROUP>' || $results[$current]->value == '</OPTGROUP>' ) ) {
								if ( $attrib ) {
									$attributes[]	=	''; // TODO: all attr
								}
								$options[]	=	$results[$current]->text.'='.$results[$current]->value;
							}
							$opts[]		=	$results[$current];
						}	
					}
				} else {
					foreach ( $results as $result ) {
						if ( !( $result->value == '<OPTGROUP>' || $result->value == '</OPTGROUP>' ) ) {
							if ( $attrib ) {
								$attributes[]	=	''; // TODO: all attr
							}
							$options[]	=	$result->text.'='.$result->value;
						}
						$opts[]		=	$result;
					}
				}
			}
			
			if ( $field->bool2 == 0 ) {
				$opt_table			=	isset( $options2['table'] ) ? ' FROM '.$options2['table'] : '';
				$opt_name			=	isset( $options2['name'] ) ? $options2['name'] : '';
				$opt_value			=	isset( $options2['value'] ) ? $options2['value'] : '';

				if ( $count2 ) {
					for ( $i = 1; $i <= $count2; $i++ ) {
						$opt_attrs[]	=	( isset( $options2['attr'.$i] ) && $options2['attr'.$i] != '' ) ? $options2['attr'.$i] : '';
					}
				}
				$opt_where			=	@$options2['where'] != '' ? ' WHERE '.$options2['where']: '';
				$opt_orderby		=	@$options2['orderby'] != '' ? ' ORDER BY '.$options2['orderby'].' '.( ( @$options2['orderby_direction'] != '' ) ? $options2['orderby_direction'] : 'ASC' ) : '';
				$opt_limit			=	@$options2['limit'] > 0 ? ' LIMIT '.$options2['limit'] : '';
				
				// Language Detection
				$count2				=	count( $opt_attrs );
				$lang_code			=	'';
				self::_languageDetection( $lang_code, $value, $options2 );
				$opt_value			=	str_replace( '[lang]', $lang_code, $opt_value );
				$opt_name			=	str_replace( '[lang]', $lang_code, $opt_name );	
				$opt_where			=	str_replace( '[lang]', $lang_code, $opt_where );
				$opt_orderby		=	str_replace( '[lang]', $lang_code, $opt_orderby );
				$opt_group			=	'';

				if ( $count2 ) {
					foreach ( $opt_attrs as $k=>$v ) {
						if ( $v != '' ) {
							$v			=	str_replace( '[lang]', $lang_code, $v ).' AS attr'.( $k + 1 );
							$opt_attr	.=	','.$v;
						}
					}
					if ( $opt_attr == ',' ) {
						$opt_attr	=	'';
					}
				}
				if ( $opt_name && $opt_value && $opt_table ) {
					$query			=	'SELECT '.$opt_name.','.$opt_value.$opt_attr.$opt_table.$opt_where.$opt_orderby.$opt_limit;
					$query			=	JCckDevHelper::replaceLive( $query );
					if ( $config['client'] == '' || $config['client'] == 'dev' ) {
						$tables		=	JCckDatabaseCache::getTableList();
						$prefix		=	JFactory::getDbo()->getPrefix();
						$items		=	( in_array( str_replace( '#__', $prefix, $options2['table'] ), $tables ) ) ? JCckDatabase::loadObjectList( $query ) : array();
					} else {
						$items		=	JCckDatabase::loadObjectList( $query );
					}
				}
			} else {
				if ( @$options2['query'] != '' ) {
					// Language Detection
					$lang_code		=	'';
					self::_languageDetection( $lang_code, $value, $options2 );
					$query			=	str_replace( '[lang]', $lang_code, $options2['query'] );
					$query			=	JCckDevHelper::replaceLive( $query );
					if ( ( strpos( $query, ' value ' ) !== false ) || ( strpos( $query, 'AS value' ) !== false ) || ( strpos( $query, ' value,' ) !== false ) ) {
						$items	=	JCckDatabase::loadObjectList( $query );
					} else {
						$opts2	=	JCckDatabase::loadColumn( $query );
						if ( count( $opts2 ) ) {
							$opts2	=	array_combine( array_values( $opts2 ), $opts2 );
						}
						$opts	=	array_merge( $opts, $opts2 );
					}
				}
				$opt_name	=	'text';
				$opt_value	=	'value';
				$opt_group	=	'optgroup';
			}
			if ( count( $items ) ) {
				if ( $opt_group ) {
					$group	=	'';
					foreach ( $items as $o ) {
						if ( isset( $o->optgroup ) && $o->optgroup != $group ) {
							if ( $group ) {
								$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
							}
							$opts[]	=	JHtml::_( 'select.option', '<OPTGROUP>', $o->optgroup );
							$group	=	$o->optgroup;
						}
						if ( $attrib ) {
							$attr['attr']	=	'';
							foreach ( $attribs as $k=>$a ) {
								$ka				=	'attr'.( $k + 1 );
								if ( isset( $o->{$ka} ) ) {
									$va			=	$o->{$ka};
								} else {
									$ka			=	( isset( $options2['attr'.( $k + 1 )] ) ) ? $options2['attr'.( $k + 1 )] : '';
									$va			=	( $ka != '' && isset( $o->{$ka} ) ) ? $o->{$ka} : '';
								}
								$attr['attr']	.=	' '.$a.'="'.$va.'"';
							}
							$attributes[]	=	$attr['attr'];
							$opts[]			=	JHtml::_( 'select.option', $o->value, $o->text, $attr );
						} else {
							$opts[]			=	JHtml::_( 'select.option', $o->value, $o->text, 'value', 'text' );	
						}
						$options[]			=	$o->text.'='.$o->value;
					}
					if ( $group ) {
						$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
					}
				} else {
					if ( $attrib ) {
						foreach ( $items as $o ) {
							$attr['attr']	=	'';
							foreach ( $attribs as $k=>$a ) {
								$ka				=	'attr'.( $k + 1 );

								if ( isset( $o->{$ka} ) ) {
									$va			=	$o->{$ka};
								} else {
									$ka			=	( isset( $options2['attr'.( $k + 1 )] ) ) ? $options2['attr'.( $k + 1 )] : '';
									$va			=	( $ka != '' && isset( $o->{$ka} ) ) ? $o->{$ka} : '';
								}
								$attr['attr']	.=	' '.$a.'="'.$va.'"';
							}
							$attributes[]	=	$attr['attr'];
							$opts[]			=	JHtml::_( 'select.option', $o->$opt_value, $o->$opt_name, $attr );
							$options[]		=	$o->$opt_name.'='.$o->$opt_value;
						}
					} else {
						foreach ( $items as $o ) {
							$opts[]			=	JHtml::_( 'select.option', $o->$opt_value, $o->$opt_name, 'value', 'text' );
							$options[]		=	$o->$opt_name.'='.$o->$opt_value;
						}
					}
				}
			}
			
			if ( $optgroups !== false ) {
				$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
			}
			if ( $field->bool4 == 2 || $field->bool4 == 3 ) {
				if ( $field->bool4 == 3 ) {
					if ( $static > 1 && isset( $current ) && isset( $half ) && isset( $static ) && isset( $results ) ) {
						for ( ; $current < $static; $current++ ) {
							if ( !( $results[$current]->value == '<OPTGROUP>' || $results[$current]->value == '</OPTGROUP>' ) ) {
								if ( $attrib ) {
									$attributes[]	=	''; // TODO: all attr
								}
								$options[]	=	$results[$current]->text.'='.$results[$current]->value;
							}
							$opts[]		=	$results[$current];
						}
					}
				} else {
					$results	=	self::_getStaticOption( $field, $field->options, $config );

					foreach ( $results as $result ) {
						if ( !( $result->value == '<OPTGROUP>' || $result->value == '</OPTGROUP>' ) ) {
							if ( $attrib ) {
								$attributes[]	=	''; // TODO: all attr
							}
							$options[]	=	$result->text.'='.$result->value;
						}
						$opts[]		=	$result;
					}
				}
			}
			$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
			
			if ( ( is_string( $value ) && $value != '' ) || ( is_array( $value ) && count( $value ) && $value[0] != '' ) ) {
				$class	.=	' has-value';
			}
			$multi	=	( @$field->bool3 ) ? ' multiple="multiple"' : '';
			$size	=	( !@$field->bool3 ) ? '1' : ( ( @$field->rows ) ? $field->rows : count( $opts ) );
			$size	=	( (int)$size > 1 ) ? ' size="'.$size.'"' : '';
			
			$attr	=	'class="'.$class.'"'.$size.$multi . ( $field->attributes ? ' '.$field->attributes : '' );
			$count	=	count( $opts );
			$form	=	'';

			if ( $field->variation == 'hidden_auto' ) {
				if ( $auto == $count && is_object( $opts[$auto - 1] ) ) {
					$count				=	0;
					$field->variation	=	'hidden';
					$value				=	$opts[$auto - 1]->value;

					if ( !$field->live ) {
						JCckDevHelper::secureField( $field, $value );
					}
				} else {
					$field->variation	=	'';
				}
			}
			if ( $count ) {
				if ( $attrib ) {
					$attr	=	array( 'id'=>$id, 'list.attr'=>$attr, 'list.select'=>$value, 'list.translate'=>false,
									   'option.attr'=>'data-cck', 'option.key'=>'value', 'option.text'=>'text' );
					$form	=	JHtml::_( 'select.genericlist', $opts, $name, $attr );
				} else {
					$form	=	JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
				}
			}
			
			/* tmp */
			$jtext						=	$config['doTranslation'];
			$config['doTranslation']	=	0;
			/* tmp */

			// Set
			if ( ! $field->variation ) {
				$field->form			=	$form;
				$field->optionsList		=	( count( $options ) ) ? implode( '||', $options ) : '';
				$field->text			=	parent::g_getOptionText( $value, $field->optionsList, $divider, $config );

				if ( $field->script ) {
					parent::g_addScriptDeclaration( $field->script );
				}
			} else {
				$field->attributesList	=	( count( $attributes ) ) ? implode( '||', $attributes ) : '';
				$field->optionsList		=	( count( $options ) ) ? implode( '||', $options ) : '';
				$field->text			=	parent::g_getOptionText( $value, $field->optionsList, $divider, $config );

				parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
			}

			/* tmp */
			$config['doTranslation']	=	$jtext;
			/* tmp */
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
		
		// Init
		if ( $field->bool3 ) {
			$divider			=	$field->match_value ? $field->match_value : $field->divider;
			$field->match_value	=	$divider;
			if ( is_array( $value ) ) {
				$value	=	implode( $divider, $value );
			}
			
			$field->divider	=	$divider;
		} else {
			$field->match_value	=	$field->match_value ? $field->match_value : ',';
		}
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
		$divider	=	'';
		$value2		=	'';
		
		// Prepare
		if ( $field->bool3 ) {
			// Set Multiple
			$divider	=	( $field->divider != '' ) ? $field->divider : ',';
			if ( $divider ) {
				$nb			=	count( $value );
				if ( is_array( $value ) && $nb > 0 ) {
					$value	=	implode( $divider, $value );
				}
			}
		}

		$optgroups		=	false;
		$options		=	self::_getStaticOption( $field, $field->options, $config, $optgroups );
		$options_1		=	array();

		if ( count( $options ) ) {
			foreach ( $options as $o ) {
				if ( !( $o->value == '<OPTGROUP>' || $o->value == '</OPTGROUP>' ) ) {
					$options_1[]	=	$o->text.'='.$o->value;
				}
			}
		}
		$options_1		=	( count( $options_1 ) ) ? implode( '||', $options_1 ).'||' : '';

		/* tmp */
		$jtext						=	$config['doTranslation'];
		$config['doTranslation']	=	0;
		/* tmp */

		$lang_code		=	'';
		$options2		=	JCckDev::fromJSON( $field->options2 );
		self::_languageDetection( $lang_code, $value2, $options2 );
		$options_2		=	self::_getOptionsList( $options2, $field->bool2, $lang_code );
		$field->options	=	$options_1.$options_2;
		
		// Validate
		$text	=	parent::g_getOptionText( $value, $field->options, $divider, $config );
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		/* tmp */
		$config['doTranslation']	=	$jtext;
		/* tmp */

		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->text	=	$text;
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'text' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _languageDetection
	protected static function _languageDetection( &$lang_code, &$value, $options2 )
	{
		if ( @$options2['geoip'] && function_exists( 'geoip_country_code_by_name' ) ) {
			$lang_code	=	geoip_country_code_by_name( $_SERVER['REMOTE_ADDR'] );
		} else {
			jimport( 'joomla.language.helper' );
			$languages	=	JLanguageHelper::getLanguages( 'lang_code' );
			$lang_tag	=	JFactory::getLanguage()->getTag();
			$lang_code	=	( isset( $languages[$lang_tag] ) ) ? strtoupper( $languages[$lang_tag]->sef ) : '';
		}
		$value			=	str_replace( '[lang]', $lang_code, $value );
		$languages		=	explode( ',', @$options2['language_codes'] );
		if ( ! in_array( $lang_code, $languages ) ) {
			$lang_code	=	@$options2['language_default'] ? $options2['language_default'] : '';
		}
		$lang_code		=	strtolower( $lang_code );
	}
	
	// _getStaticOption
	protected static function _getStaticOption( $field, $options, $config, &$optgroups = false )
	{
		$results	=	array();
		$optgroup	=	0;
		$options	=	explode( '||', $options );
		if ( $field->bool8 ) {
			$field->bool8	=	$config['doTranslation'];
		}
		if ( count( $options ) ) {
			foreach ( $options as $val ) {
				$latest	=	0;
				if ( trim( $val ) != '' ) {
					if ( StringHelper::strpos( $val, '=' ) !== false ) {
						$opt	=	explode( '=', $val );
						if ( $opt[1] == 'optgroup' ) {
							if ( $optgroup == 1 ) {
								$results[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
							}
							if ( $field->bool8 && trim( $opt[0] ) ) {
								$opt[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
							}
							$results[]		=	JHtml::_( 'select.option', '<OPTGROUP>', $opt[0] );
							$optgroup	=	1;
							$latest		=	1;
						} elseif ( $opt[1] == 'endgroup' && $optgroup == 1 ) {
							$results[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
							$optgroup	=	0;
						} else {
							if ( $field->bool8 && trim( $opt[0] ) ) {
								$opt[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $opt[0] ) ) );
							}
							$results[]	=	JHtml::_( 'select.option', $opt[1], $opt[0], 'value', 'text' );
						}
					} else {
						if ( $val == 'endgroup' && $optgroup == 1 ) {
							$results[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
							$optgroup	=	0;
						} else {
							$text	=	$val;
							if ( $field->bool8 && trim( $text ) != '' ) {
								$text	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) );
							}
							$results[]	=	JHtml::_( 'select.option', $val, $text, 'value', 'text' );
						}
					}
				}
			}
			if ( $optgroup == 1 ) {
				if ( $latest == 1 ) {
					$optgroups		=	true;
				} else {
					$results[]		=	JHtml::_( 'select.option', '</OPTGROUP>' );
				}
			}
		}

		return $results;
	}

	// _getOptionsList
	protected static function _getOptionsList( $options2, $free_sql, $lang_code, $static = false )
	{
		$options	=	'';
		
		if ( $free_sql == 0 ) {
			$opt_table		=	isset( $options2['table'] ) ? ' FROM '.$options2['table'] : '';
			$opt_name		=	isset( $options2['name'] ) ? $options2['name'] : '';
			$opt_value		=	isset( $options2['value'] ) ? $options2['value'] : '';
			$opt_where		=	@$options2['where'] != '' ? ' WHERE '.$options2['where']: '';
			$opt_orderby	=	@$options2['orderby'] != '' ? ' ORDER BY '.$options2['orderby'].' '.( ( @$options2['orderby_direction'] != '' ) ? $options2['orderby_direction'] : 'ASC' ) : '';
			
			// Language Detection
			$opt_value		=	str_replace( '[lang]', $lang_code, $opt_value );
			$opt_name		=	str_replace( '[lang]', $lang_code, $opt_name );
			$opt_where		=	str_replace( '[lang]', $lang_code, $opt_where );
			$opt_orderby	=	str_replace( '[lang]', $lang_code, $opt_orderby );
			
			if ( $opt_name && $opt_table ) {
				$query		=	'SELECT '.$opt_name.','.$opt_value.$opt_table.$opt_where.$opt_orderby;
				$query		=	JCckDevHelper::replaceLive( $query );
				$lists		=	( $static ) ? JCckDatabaseCache::loadObjectList( $query ) : JCckDatabase::loadObjectList( $query );
				if ( count( $lists ) ) {
					foreach ( $lists as $list ) {
						$options	.=	$list->$opt_name.'='.$list->$opt_value.'||';
					}
				}
			}
		} else {
			$opt_query	=	isset( $options2['query'] ) ? $options2['query'] : '';
			
			// Language Detection
			$opt_query	=	str_replace( '[lang]', $lang_code, $opt_query );
			$opt_query	=	JCckDevHelper::replaceLive( $opt_query );
			$lists		=	( $static ) ? JCckDatabaseCache::loadObjectList( $opt_query ) : JCckDatabase::loadObjectList( $opt_query );
			if ( count( $lists ) ) {
				foreach ( $lists as $list ) {
					$options	.=	@$list->text.'='.@$list->value.'||';
				}
			}
		}
		
		return $options;
	}

	// _getOptionsListProperty
	protected static function _getOptionsListProperty( $property, $field, $value, $config = array() )
	{
		$divider	=	'';
		$lang_code	=	'';
		$method		=	'get'.ucfirst( $property).'FromOptions';
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$value2		=	'';
		
		/* tmp */
		$jtext						=	$config['doTranslation'];
		$config['doTranslation']	=	0;
		/* tmp */

		// Prepare
		self::_languageDetection( $lang_code, $value2, $options2 );
		
		if ( $field->bool3 ) {
			$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		}
		
		$options_2			=	self::_getOptionsList( $options2, $field->bool2, $lang_code );
		$field->options		=	( $field->options ) ? $field->options.'||'.$options_2 : $options_2;
		$result				=	parent::$method( $field, $value, $config );

		/* tmp */
		$config['doTranslation']	=	$jtext;
		/* tmp */

		return $result;
	}
	
	// getTextFromOptions
	public static function getTextFromOptions( $field, $value, $config = array() )
	{
		return self::_getOptionsListProperty( 'text', $field, $value, $config );
	}
	
	// getValueFromOptions
	public static function getValueFromOptions( $field, $value, $config = array() )
	{
		return self::_getOptionsListProperty( 'value', $field, $value, $config );
	}
	
	// isConvertible
	public static function isConvertible()
	{
		return self::$convertible;
	}
	
	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>