<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginField extends JPlugin
{
	protected static $construction	=	'cck_field';
	protected static $convertible	=	0;
	protected static $friendly		=	0;

	// onCCK_FieldPrepareContentDebug
	public function onCCK_FieldPrepareContentDebug( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		self::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareDownload
	public function onCCK_FieldPrepareDownload( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}
		
		$field->filename	=	$value;
	}
	
	// onCCK_FieldPrepareExport
	public function onCCK_FieldPrepareExport( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}

		$field->output	=	$value;
	}

	// onCCK_FieldPrepareResource
	public function onCCK_FieldPrepareResource( &$field, $value = '', &$config = array() )
	{
		if ( static::$type != $field->type ) {
			return;
		}

		$field->data	=	$value;
	}

	// getTextFromOptions
	public static function getTextFromOptions( $field, $value, $config )
	{
		$v	=	$value;

		return self::g_getOptionText( $v, $field->options, $field->divider, $config );
	}

	// getValueFromOptions
	public static function getValueFromOptions( $field, $value, $config = array() )
	{
		$opts	=	explode( '||', $field->options );
		
		if ( $value == '' ) {
			return $value;
		}
		if ( count( $opts ) ) {
			$length	=	strlen( $value );
			foreach ( $opts as $opt ) {
				$o	=	explode( '=', $opt );

				if ( $config['doTranslation'] && trim( $o[0] ) ) {
					$o[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $o[0] ) ) );
				}
				if ( stristr( $o[0], $value ) !== false && strlen( $o[0] ) == $length ) {
					return ( isset( $o[1] ) ) ? $o[1] : $o[0];
					break;
				}
			}
		}

		return $value;
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
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		self::g_onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
	
	// onCCK_FieldConstruct_TypeContent
	public static function onCCK_FieldConstruct_TypeContent( &$field, $style, $data = array(), &$config = array() )
	{
		self::g_onCCK_FieldConstruct_TypeContent( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		self::g_onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
	
	// onCCK_FieldConstruct_SearchOrder
	public static function onCCK_FieldConstruct_SearchOrder( &$field, $style, $data = array(), &$config = array() )
	{
		self::g_onCCK_FieldConstruct_SearchOrder( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
	
	// onCCK_FieldConstruct_SearchContent
	public static function onCCK_FieldConstruct_SearchContent( &$field, $style, $data = array(), &$config = array() )
	{
		self::g_onCCK_FieldConstruct_SearchContent( $field, $style, $data, $config );
		
		krsort( $field->params );
		$field->params	=	implode( '', $field->params );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// g_onCCK_FieldConstruct
	public function g_onCCK_FieldConstruct( &$data )
	{
		$db					=	JFactory::getDbo();
		$data['display']	=	3;
		$data['script']		=	JRequest::getVar( 'script', '', '', 'string', JREQUEST_ALLOWRAW );
		if ( isset( $data['selectlabel'] ) && $data['selectlabel'] == '' ) {
			$data['selectlabel']	=	' ';
		}

		// JSON
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k=>$v ) {
				if ( is_array( $v ) ) {
					if ( isset( $v['options'] ) ) {
						$options	=	array();
						if ( count( $v['options'] ) ) {
							foreach ( $v['options'] as $option ) {
								$options[]	=	$option;
							}
						}
						$v['options']	=	$options;
					}
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}
		
		// STRING
		if ( isset( $data['string'] ) && is_array( $data['string'] ) ) {
			foreach ( $data['string'] as $k=>$v ) {
				if ( is_array( $v ) ) {
					$string	=	'';
					foreach ( $v as $s ) {
						if ( $s != '' ) {
							$string	.=	$s.'||';
						}
					}
					if ( $string ) {
						$string	=	substr( $string, 0, -2 );
					}
					$data[$k]	=	$string;
				}
			}
		}
		
		if ( empty( $data['storage'] ) ) {
			$data['storage']	=	'none';
		}
		if ( $data['storage'] == 'dev' ) {
			$data['published'] 			=	0;
			$data['storage_location']	=	'';
			$data['storage_table']		=	'';
		} else {
			// No Table for None!
			if ( $data['storage'] == 'none' ) {
				$data['storage_location']	=	'';
				$data['storage_table']		=	'';
			}
			
			// Storage Field is required!
			if ( ! @$data['storage_field'] ) {
				if ( $data['storage'] == 'none' && $data['storage_field_prev'] ) {
					$data['storage_field']	=	$data['storage_field_prev'];
				} else {
					$data['storage_field']	=	$data['name'];
					$dev_prefix				=	JCck::getConfig_Param( 'development_prefix', '' );
					if ( $dev_prefix ) {
						$data['storage_field']	=	str_replace( $dev_prefix.'_', '', $data['storage_field'] );
					}	
				}
			}
			
			// Storage Field2 is better for flexibility!
			if ( $data['storage'] != 'standard' && $data['storage_field'] ) {
				if ( ( $cut = strpos( $data['storage_field'], '[' ) ) !== false ) {
					$data['storage_field2']	=	substr( $data['storage_field'], $cut + 1, -1 );
					$data['storage_field']	=	substr( $data['storage_field'], 0, $cut );
				} else {
					$data['storage_field2']	=	'';
				}
			}
			
			// Un-existing Fields must be mapped!
			if ( !isset( $data['alterTable'] ) ) {
				$data['alterTable']			=	true;
			}
			if ( $data['storage_location'] == '' && $data['storage_table'] == '' ) {
				$data['storage']			=	'none';
			}
			if ( $data['alterTable'] ) {
				$data['storage_alter_type']	=	( isset( $data['storage_alter_type'] ) && $data['storage_alter_type'] ) ? $data['storage_alter_type'] : 'VARCHAR(255)';
				$alter						=	isset( $data['storage_alter'] ) && $data['storage_alter'] && in_array( 1, $data['storage_alter'] );
				$pos						=	strpos( $data['storage_table'], 'aka_table' );
				
				if ( !( $pos !== false && $pos == 0 ) ) {
					if ( isset( $data['storage_alter_table'] ) && $data['storage_alter_table'] && $alter ) {
						if ( $data['storage_table'] && $data['storage_field'] ) {
							$columns	=	$db->getTableColumns( $data['storage_table'] );
							if ( !isset( $columns[$data['storage_field']] ) ) {
								if ( $data['storage_alter_table'] == 2 && $data['storage_field_prev'] != '' ) {
									JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $data['storage_table'] ).' CHANGE '.JCckDatabase::quoteName( $data['storage_field_prev'] ).' '.JCckDatabase::quoteName( $data['storage_field'] ).' '.$data['storage_alter_type'].' NOT NULL' );
								} else {
									JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $data['storage_table'] ).' ADD '.JCckDatabase::quoteName( $data['storage_field'] ).' '.$data['storage_alter_type'].' NOT NULL' );
								}							
							} else {
								JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $data['storage_table'] ).' CHANGE '.JCckDatabase::quoteName( $data['storage_field'] ).' '.JCckDatabase::quoteName( $data['storage_field'] ).' '.$data['storage_alter_type'].' NOT NULL' );
							}
						}
					} else {
						if ( $data['storage_table'] && $data['storage_field'] ) {
							if ( ( $data['type'] == 'jform_rules' && $data['storage_field'] == 'rules' ) ||
								 ( $data['storage_table'] == @$data['core_table'] && in_array( $data['storage_field'], $data['core_columns'] ) ) ) {
								unset( $data['core_table'] );
								unset( $data['core_columns'] );
								return;
							}
							$columns	=	$db->getTableColumns( $data['storage_table'] );
							if ( !isset( $columns[$data['storage_field']] ) ) {
								$prefix	=	JFactory::getConfig()->get( 'dbprefix' );
								if ( $data['storage_cck'] != '' ) {
									// #__cck_store_form_
									$table	=	'#__cck_store_form_'.$data['storage_cck'];
									JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table.' ( id int(10) UNSIGNED NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
								} else {
									// #__cck_store_item_
									$table	=	( strpos( $data['storage_table'], 'cck_store_item' ) !== false ) ? $data['storage_table'] : '#__cck_store_item_'.str_replace( '#__', '', $data['storage_table'] );
									JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table.' ( id int(10) UNSIGNED NOT NULL, cck VARCHAR(50) NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
								}
								$columns2	=	$db->getTableColumns( $table );
								if ( !isset( $columns2[$data['storage_field']] ) ) {
									JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $table ).' ADD '.JCckDatabase::quoteName( $data['storage_field'] ).' '.$data['storage_alter_type'].' NOT NULL' );
								}
								$data['storage_table']	=	$table;
							} else {
								if ( $alter ) {
									JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $data['storage_table'] ).' CHANGE '.JCckDatabase::quoteName( $data['storage_field'] ).' '.JCckDatabase::quoteName( $data['storage_field'] ).' '.$data['storage_alter_type'].' NOT NULL' );
								}
							}
						}
					}
				}
			}
		}
		unset( $data['core_table'] );
		unset( $data['core_columns'] );
	}
	
	// g_onCCK_FieldConstruct_TypeForm
	public static function g_onCCK_FieldConstruct_TypeForm( &$field, $style, $data, $config = array() )
	{
		$id					=	$field->id;
		$name				=	$field->name;
		$field->params		=	array();

		// 1
		if ( !$data['label'] ) {
			$column1			=	'';
		} else {
			$column1			=	'<input class="thin blue" type="text" name="ffp['.$name.'][label]" size="22" '
								.	'value="'.( ( @$field->label2 != '' ) ? htmlspecialchars( $field->label2 ) : htmlspecialchars( $field->label ) ).'" />'
								.	'<input class="thin blue" type="hidden" name="ffp['.$name.'][label2]" value="'.$field->label.'" />';
		}
		if ( !$data['variation'] ) {
			$column2			=	'';
		} else {
			$value			=	@$field->variation;
			$text			=	( isset( $data['variation'][$value] ) ) ? $data['variation'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$to				=	( isset( $config['construction']['variation'][$field->type] ) ) ? 'variation-'.$field->type : 'variation';
			$column2		=	'<input type="hidden" id="'.$name.'_variation" name="ffp['.$name.'][variation]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_variation" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_variation_override" name="ffp['.$name.'][variation_override]" '
							.	'value="'.( ( @$field->variation_override != '' ) ? htmlspecialchars( $field->variation_override ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 1, $style, $column1, $column2 );
		
		// 2
		if ( !$data['live'] ) {
			$column1			=	'';
			$column2			=	'';
		} else {
			if ( @$field->live != '' ) {
				$hide0	=	' hide';
				$hide	=	' hide';
				$hide2	=	'';
			} else {
				$hide0	=	'';
				$hide	=	' show';
				$hide2	=	' hide';
			}
			$value				=	@$field->live;
			$to					=	( isset( $config['construction']['live'][$field->type] ) ) ? 'live-'.$field->type : 'live';
			$text				=	( isset( $data['live'][$value] ) ) ? $data['live'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$text2				=	( JCck::callFunc( 'plgCCK_Field'.$field->type, 'isFriendly' ) ) ? $data['_']['icon-friendly'] : '';	// ( static::$friendly ) ? $data['_']['icon-friendly'] : '';
			$column1			=	'<input type="hidden" id="'.$name.'_live" name="ffp['.$name.'][live]" value="'.$value.'" />'
								.	'<span class="text blue sp2se" data-id="'.$name.'_live" data-to="'.$to.'">'.$text.'</span>';
			$column2			=	'<input class="thin blue c_live0'.$hide0.'" type="text" id="'.$name.'_live_value" name="ffp['.$name.'][live_value]" size="22" '
								.	'value="'.( ( @$field->live_value != '' ) ? htmlspecialchars( $field->live_value ) : '' ).'" />'
								.	'<input type="hidden" id="'.$name.'_live_options" name="ffp['.$name.'][live_options]" '
								.	'value="'.( ( @$field->live_options != '' ) ? htmlspecialchars( $field->live_options ) : '' ).'" />'
								.	' <span class="c_live'.$hide.'" name="'.$name.'">'.$text2.'</span>'
								.	' <span class="text blue c_live2'.$hide2.'" name="'.$name.'">'.$data['_']['configure'].'</span>';
		}
		$field->params[]	=	self::g_getParamsHtml( 2, $style, $column1, $column2 );
		
		// 3
		if ( !$data['validation'] ) {
			$column1		=	'';
		} else {
			$required			=	@$field->required ? $data['_']['required'] : $data['_']['optional'];
			if ( @$field->validation ) {
				$required		.=	' + 1';
			}
			$column1			=	'<input type="hidden" id="'.$name.'_required" name="ffp['.$name.'][required]" value="'.@$field->required.'" />'
								.	'<input type="hidden" id="'.$name.'_required_alert" name="ffp['.$name.'][required_alert]" value="'.@$field->required_alert.'" />'
								.	'<input type="hidden" id="'.$name.'_validation" name="ffp['.$name.'][validation]" value="'.@$field->validation.'" />'
								.	'<input type="hidden" id="'.$name.'_validation_options" name="ffp['.$name.'][validation_options]" '
								.	'value="'.( ( @$field->validation_options != '' ) ? htmlspecialchars( $field->validation_options ) : '' ).'" />'
								.	' <span class="text blue c_val" name="'.$name.'">'.$required.'</span>';
		}
		$value				=	@(int)$field->stage;
		$value				=	(string)$value;
		$text				=	( isset( $data['stage'][$value] ) ) ? $data['stage'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$column2			=	'<input type="hidden" id="ffp'.$name.'_stage" name="ffp['.$name.'][stage]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="ffp'.$name.'_stage" data-to="stage">'.$text.'</span>';
		$field->params[]	=	self::g_getParamsHtml( 3, $style, $column1, $column2 );
		
		// 4
		$hide				=	( @$field->restriction != '' ) ? '' : ' hidden';
		$value				=	( @$field->access == '' ) ? 1 : ( ( @$field->access ) ? (int)$field->access : 0 );
		$text				=	( isset( $data['access'][$value] ) ) ? $data['access'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$column1			=	'<input type="hidden" id="ffp'.$name.'_access" name="ffp['.$name.'][access]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="ffp'.$name.'_access" data-to="access">'.$text.'</span>';
		$value				=	@$field->restriction;
		$text				=	( isset( $data['restriction'][$value] ) ) ? $data['restriction'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$to					=	( isset( $config['construction']['restriction'][$field->type] ) ) ? 'restriction-'.$field->type : 'restriction';
		$column2			=	'<input type="hidden" id="'.$name.'_restriction" name="ffp['.$name.'][restriction]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_restriction" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_restriction_options" name="ffp['.$name.'][restriction_options]" '
							.	'value="'.( ( @$field->restriction_options != '' ) ? htmlspecialchars( $field->restriction_options ) : '' ).'" />'
							.	' <span class="c_res'.$hide.'" name="'.$name.'">+</span>';
		$field->params[]	=	self::g_getParamsHtml( 4, $style, $column1, $column2 );
		
		// 5
		if ( !$data['conditional'] ) {
			$column1		=	'';
		} else {
			$column1		=	'<input type="hidden" id="ffp_'.$name.'_conditional" name="ffp['.$name.'][conditional]" value="'.( ( @$field->conditional != '' ) ? $field->conditional : '' ).'" />'
							.	'<span class="text blue c_cond" name="'.$name.'">'.( ( @$field->conditional != '' ) ? '&lt; '.$data['_']['edit'].' /&gt;' : $data['_']['add'] ).'</span>'
							.	'<input type="hidden" id="ffp_'.$name.'_conditional_options" name="ffp['.$name.'][conditional_options]" '
							.	'value="'.( ( @$field->conditional_options != '' ) ? htmlspecialchars( $field->conditional_options ) : '' ).'" />';
		}
		if ( !$data['computation'] ) {
			$column2		=	'';
		} else {
			$column2		=	'<input type="hidden" id="ffp_'.$name.'_computation" name="ffp['.$name.'][computation]" value="'.( ( @$field->computation != '' ) ? $field->computation : '' ).'" />'
							.	'<span class="text blue c_comp" name="'.$name.'">'. ( ( @$field->computation != '' ) ? '&lt; '.$data['_']['edit'].' /&gt;' : $data['_']['add'] ) .'</span>'
							.	'<input type="hidden" id="ffp_'.$name.'_computation_options" name="ffp['.$name.'][computation_options]" '
							.	'value="'.( ( @$field->computation_options != '' ) ? htmlspecialchars( $field->computation_options ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 5, $style, $column1, $column2 );

		// 6
		if ( !$data['markup'] ) {
			$column1		=	'';
		} else {
			$value			=	@$field->markup;
			$to				=	( isset( $config['construction']['markup'][$field->type] ) ) ? 'markup-'.$field->type : 'markup';
			$text			=	( isset( $data['markup'][$value] ) ) ? $data['markup'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$column1		=	'<input type="hidden" id="'.$name.'_markup" name="ffp['.$name.'][markup]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_markup" data-to="'.$to.'">'.$text.'</span>';
		}
		if ( !$data['markup_class'] ) {
			$column2		=	'';
		} else {
			$column2		=	'<input class="thin blue" type="text" name="ffp['.$name.'][markup_class]" size="22" '
							.	'value="'.( ( @$field->markup_class != '' ) ? htmlspecialchars( trim( $field->markup_class ) ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 6, $style, $column1, $column2 );
	}
	
	// g_onCCK_FieldConstruct_TypeContent
	public static function g_onCCK_FieldConstruct_TypeContent( &$field, $style, $data, $config = array() )
	{
		$id					=	$field->id;
		$name				=	$field->name;
		$field->params		=	array();
		
		// 1
		$column1			=	'<input class="thin blue" type="text" name="ffp['.$name.'][label]" size="22" '
							.	'value="'.( ( @$field->label2 != '' ) ? htmlspecialchars( $field->label2 ) : htmlspecialchars( $field->label ) ).'" />'
							.	'<input class="thin blue" type="hidden" name="ffp['.$name.'][label2]" value="'.$field->label.'" />';
		$column2			=	'';
		$field->params[]	=	self::g_getParamsHtml( 1, $style, $column1, $column2 );
		
		// 2
		$hide				=	( @$field->link != '' ) ? '' : ' hidden';
		$value				=	@$field->link;
		$text				=	( isset( $data['link'][$value] ) ) ? $data['link'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$to					=	( isset( $config['construction']['link'][$field->type] ) ) ? 'link-'.$field->type : 'link';
		$column1			=	'<input type="hidden" id="'.$name.'_link" name="ffp['.$name.'][link]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_link" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_link_options" name="ffp['.$name.'][link_options]" '
							.	'value="'.( ( @$field->link_options != '' ) ? htmlspecialchars( $field->link_options ) : '' ).'" />'
							.	' <span class="c_link'.$hide.'" name="'.$name.'">+</span>';
		$hide				=	( @$field->typo != '' ) ? '' : ' hidden';
		$value				=	@$field->typo;
		$text				=	( isset( $data['typo'][$value] ) ) ? $data['typo'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$to					=	( isset( $config['construction']['typo'][$field->type] ) ) ? 'typo-'.$field->type : 'typo';
		$column2			=	'<input type="hidden" id="'.$name.'_typo" name="ffp['.$name.'][typo]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_typo" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_typo_options" name="ffp['.$name.'][typo_options]" '
							.	'value="'.( ( @$field->typo_options != '' ) ? htmlspecialchars( $field->typo_options ) : '' ).'" />'
							.	'<input type="hidden" id="'.$name.'_typo_label" name="ffp['.$name.'][typo_label]" value="'.@$field->typo_label.'" />'
							.	' <span class="c_typo'.$hide.'" name="'.$name.'">+</span>';
		$field->params[]	=	self::g_getParamsHtml( 2, $style, $column1, $column2 );
		
		// 3
		if ( !$data['markup'] ) {
			$column1		=	'';
		} else {
			$value			=	@$field->markup;
			$to				=	( isset( $config['construction']['markup'][$field->type] ) ) ? 'markup-'.$field->type : 'markup';
			$text			=	( isset( $data['markup'][$value] ) ) ? $data['markup'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$column1		=	'<input type="hidden" id="'.$name.'_markup" name="ffp['.$name.'][markup]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_markup" data-to="'.$to.'">'.$text.'</span>';
		}
		if ( !$data['markup_class'] ) {
			$column2		=	'';
		} else {
			$column2		=	'<input class="thin blue" type="text" name="ffp['.$name.'][markup_class]" size="22" '
							.	'value="'.( ( @$field->markup_class != '' ) ? htmlspecialchars( trim( $field->markup_class ) ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 3, $style, $column1, $column2 );
		
		// 4
		$hide				=	( @$field->restriction != '' ) ? '' : ' hidden';
		$value				=	( @$field->access == '' ) ? 1 : ( ( @$field->access ) ? (int)$field->access : 0 );
		$text				=	( isset( $data['access'][$value] ) ) ? $data['access'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$column1			=	'<input type="hidden" id="ffp'.$name.'_access" name="ffp['.$name.'][access]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="ffp'.$name.'_access" data-to="access">'.$text.'</span>';
		$value				=	@$field->restriction;
		$text				=	( isset( $data['restriction'][$value] ) ) ? $data['restriction'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$to					=	( isset( $config['construction']['restriction'][$field->type] ) ) ? 'restriction-'.$field->type : 'restriction';
		$column2			=	'<input type="hidden" id="'.$name.'_restriction" name="ffp['.$name.'][restriction]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_restriction" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_restriction_options" name="ffp['.$name.'][restriction_options]" '
							.	'value="'.( ( @$field->restriction_options != '' ) ? htmlspecialchars( $field->restriction_options ) : '' ).'" />'
							.	' <span class="c_res'.$hide.'" name="'.$name.'">+</span>';
		$field->params[]	=	self::g_getParamsHtml( 4, $style, $column1, $column2 );
	}
	
	// g_onCCK_FieldConstruct_SearchSearch
	public static function g_onCCK_FieldConstruct_SearchSearch( &$field, $style, $data, $config = array() )
	{
		$id					=	$field->id;
		$name				=	$field->name;
		$field->params		=	array();
		
		// 1
		if ( !$data['label'] ) {
			$column1			=	'';
		} else {
			$column1			=	'<input class="thin blue" type="text" name="ffp['.$name.'][label]" size="22" '
								.	'value="'.( ( @$field->label2 != '' ) ? htmlspecialchars( $field->label2 ) : htmlspecialchars( $field->label ) ).'" />'
								.	'<input class="thin blue" type="hidden" name="ffp['.$name.'][label2]" value="'.$field->label.'" />';
		}
		if ( !$data['variation'] ) {
			$column2			=	'';
		} else {
			$value			=	@$field->variation;
			$text			=	( isset( $data['variation'][$value] ) ) ? $data['variation'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$to				=	( isset( $config['construction']['variation'][$field->type] ) ) ? 'variation-'.$field->type : 'variation';
			$column2		=	'<input type="hidden" id="'.$name.'_variation" name="ffp['.$name.'][variation]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_variation" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_variation_override" name="ffp['.$name.'][variation_override]" '
							.	'value="'.( ( @$field->variation_override != '' ) ? htmlspecialchars( $field->variation_override ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 1, $style, $column1, $column2 );
		
		// 2
		if ( !$data['live'] ) {
			$column1			=	'';
			$column2			=	'';
		} else {
			if ( @$field->live != '' ) {
				$hide0	=	' hide';
				$hide	=	' hide';
				$hide2	=	'';
			} else {
				$hide0	=	'';
				$hide	=	' show';
				$hide2	=	' hide';
			}
			$value				=	@$field->live;
			$to					=	( isset( $config['construction']['live'][$field->type] ) ) ? 'live-'.$field->type : 'live';
			$text				=	( isset( $data['live'][$value] ) ) ? $data['live'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$text2				=	( JCck::callFunc( 'plgCCK_Field'.$field->type, 'isFriendly' ) ) ? $data['_']['icon-friendly'] : '';	// ( static::$friendly ) ? $data['_']['icon-friendly'] : '';
			$column1			=	'<input type="hidden" id="'.$name.'_live" name="ffp['.$name.'][live]" value="'.$value.'" />'
								.	'<span class="text blue sp2se" data-id="'.$name.'_live" data-to="'.$to.'">'.$text.'</span>';
			$column2			=	'<input class="thin blue c_live0'.$hide0.'" type="text" id="'.$name.'_live_value" name="ffp['.$name.'][live_value]" size="22" '
								.	'value="'.( ( @$field->live_value != '' ) ? htmlspecialchars( $field->live_value ) : '' ).'" />'
								.	'<input type="hidden" id="'.$name.'_live_options" name="ffp['.$name.'][live_options]" '
								.	'value="'.( ( @$field->live_options != '' ) ? htmlspecialchars( $field->live_options ) : '' ).'" />'
								.	' <span class="c_live'.$hide.'" name="'.$name.'">'.$text2.'</span>'
								.	' <span class="text blue c_live2'.$hide2.'" name="'.$name.'">'.$data['_']['configure'].'</span>';
		}
		$field->params[]	=	self::g_getParamsHtml( 2, $style, $column1, $column2 );
		
		// 3
		if ( !$data['match_mode'] ) {
			$column1			=	'';
		} else {
			$hide				=	( @$field->match_mode != 'none' ) ? '' : ' hidden';
			$value				=	@$field->match_mode;
			$text				=	( isset( $data['match_mode'][$value] ) ) ? $data['match_mode'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$to					=	( isset( $config['construction']['match_mode'][$field->type] ) ) ? 'match_mode-'.$field->type : 'match_mode';
			$column1			=	'<input type="hidden" id="'.$name.'_match_mode" name="ffp['.$name.'][match_mode]" value="'.$value.'" />'
								.	'<span class="text blue sp2se" data-id="'.$name.'_match_mode" data-to="'.$to.'">'.$text.'</span>'
								.	'<input type="hidden" id="'.$name.'_match_value" name="ffp['.$name.'][match_value]" value="'.@$field->match_value.'" />'
								.	'<input type="hidden" id="'.$name.'_match_collection" name="ffp['.$name.'][match_collection]" value="'.@$field->match_collection.'" />'
								.	'<input type="hidden" id="'.$name.'_match_options" name="ffp['.$name.'][match_options]" value="'.htmlspecialchars( @$field->match_options ).'" />'
								.	' <span class="c_mat'.$hide.'" name="'.$name.'">+</span>';
		}
		$value				=	@(int)$field->stage;
		$value				=	(string)$value;
		$text				=	( isset( $data['stage'][$value] ) ) ? $data['stage'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$column2			=	'<input type="hidden" id="ffp'.$name.'_stage" name="ffp['.$name.'][stage]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="ffp'.$name.'_stage" data-to="stage">'.$text.'</span>';
		$field->params[]	=	self::g_getParamsHtml( 3, $style, $column1, $column2 );
		
		// 4
		$hide				=	( @$field->restriction != '' ) ? '' : ' hidden';
		$value				=	( @$field->access == '' ) ? 1 : ( ( @$field->access ) ? (int)$field->access : 0 );
		$text				=	( isset( $data['access'][$value] ) ) ? $data['access'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$column1			=	'<input type="hidden" id="ffp'.$name.'_access" name="ffp['.$name.'][access]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="ffp'.$name.'_access" data-to="access">'.$text.'</span>';
		$value				=	@$field->restriction;
		$text				=	( isset( $data['restriction'][$value] ) ) ? $data['restriction'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
		$to					=	( isset( $config['construction']['restriction'][$field->type] ) ) ? 'restriction-'.$field->type : 'restriction';
		$column2			=	'<input type="hidden" id="'.$name.'_restriction" name="ffp['.$name.'][restriction]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_restriction" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_restriction_options" name="ffp['.$name.'][restriction_options]" '
							.	'value="'.( ( @$field->restriction_options != '' ) ? htmlspecialchars( $field->restriction_options ) : '' ).'" />'
							.	' <span class="c_res'.$hide.'" name="'.$name.'">+</span>';
		$field->params[]	=	self::g_getParamsHtml( 4, $style, $column1, $column2 );
		
		// 5
		if ( !$data['conditional'] ) {
			$column1		=	'';
		} else {
			$column1		=	'<input type="hidden" id="ffp_'.$name.'_conditional" name="ffp['.$name.'][conditional]" value="'.( ( @$field->conditional != '' ) ? $field->conditional : '' ).'" />'
							.	'<span class="text blue c_cond" name="'.$name.'">'.( ( @$field->conditional != '' ) ? '&lt; '.$data['_']['edit'].' /&gt;' : $data['_']['add'] ).'</span>'
							.	'<input type="hidden" id="ffp_'.$name.'_conditional_options" name="ffp['.$name.'][conditional_options]" '
							.	'value="'.( ( @$field->conditional_options != '' ) ? htmlspecialchars( $field->conditional_options ) : '' ).'" />';
		}
		$column2			=	'';
		$field->params[]	=	self::g_getParamsHtml( 5, $style, $column1, $column2 );	
		
		// 6
		if ( !$data['markup'] ) {
			$column1		=	'';
		} else {
			$value			=	@$field->markup;
			$to				=	( isset( $config['construction']['markup'][$field->type] ) ) ? 'markup-'.$field->type : 'markup';
			$text			=	( isset( $data['markup'][$value] ) ) ? $data['markup'][$value]->text : JText::_( 'COM_CCK_UNKNOWN_SETUP' );
			$column1		=	'<input type="hidden" id="'.$name.'_markup" name="ffp['.$name.'][markup]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_markup" data-to="'.$to.'">'.$text.'</span>';
		}
		if ( !$data['markup_class'] ) {
			$column2		=	'';
		} else {
			$column2		=	'<input class="thin blue" type="text" name="ffp['.$name.'][markup_class]" size="22" '
							.	'value="'.( ( @$field->markup_class != '' ) ? htmlspecialchars( trim( $field->markup_class ) ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 6, $style, $column1, $column2 );

		// 7
		if ( !$data['validation'] ) {
			$column1		=	'';
		} else {
			$required		=	@$field->required ? $data['_']['required'] : $data['_']['optional'];
			if ( @$field->validation ) {
				$required	.=	' + 1';
			}
			$column1		=	'<input type="hidden" id="'.$name.'_required" name="ffp['.$name.'][required]" value="'.@$field->required.'" />'
							.	'<input type="hidden" id="'.$name.'_required_alert" name="ffp['.$name.'][required_alert]" value="'.@$field->required_alert.'" />'
							.	'<input type="hidden" id="'.$name.'_validation" name="ffp['.$name.'][validation]" value="'.@$field->validation.'" />'
							.	'<input type="hidden" id="'.$name.'_validation_options" name="ffp['.$name.'][validation_options]" '
							.	'value="'.( ( @$field->validation_options != '' ) ? htmlspecialchars( $field->validation_options ) : '' ).'" />'
							.	' <span class="text blue c_val" name="'.$name.'">'.$required.'</span>';
		}
		$column2			=	'';
		$field->params[]	=	self::g_getParamsHtml( 7, $style, $column1, $column2 );
	}
	
	// g_onCCK_FieldConstruct_SearchOrder
	public static function g_onCCK_FieldConstruct_SearchOrder( &$field, $style, $data, $config = array() )
	{
		$id					=	$field->id;
		$name				=	$field->name;
		$field->params		=	array();
		
		// 1
		$value				=	( @$field->match_mode ) ? $field->match_mode : 'ASC';
		$column1			=	'<input type="hidden" id="'.$name.'_match_mode" name="ffp['.$name.'][match_mode]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_match_mode" data-to="match_mode">'.$data['match_mode'][$value]->text.'</span>';
		$column2			=	'<span class="text blue c_mat2" name="'.$name.'">'.$data['_']['configure'].'</span>'
							.	'<input type="hidden" id="'.$name.'_match_value" name="ffp['.$name.'][match_value]" value="'.@$field->match_value.'" />'
							.	'<input type="hidden" id="'.$name.'_match_collection" name="ffp['.$name.'][match_collection]" value="" />'
							.	'<input type="hidden" id="'.$name.'_match_options" name="ffp['.$name.'][match_options]" value="'.htmlspecialchars( @$field->match_options ).'" />';

		$field->params[]	=	self::g_getParamsHtml( 1, $style, $column1, $column2 );
	}
	
	// g_onCCK_FieldConstruct_SearchContent
	public static function g_onCCK_FieldConstruct_SearchContent( &$field, $style, $data, $config = array() )
	{
		$id					=	$field->id;
		$name				=	$field->name;
		$field->params		=	array();
		
		// 1
		$column1			=	'<input class="thin blue" type="text" name="ffp['.$name.'][label]" size="22" '
							.	'value="'.( ( @$field->label2 != '' ) ? htmlspecialchars( $field->label2 ) : htmlspecialchars( $field->label ) ).'" />'
							.	'<input class="thin blue" type="hidden" name="ffp['.$name.'][label2]" value="'.$field->label.'" />';
		$column2			=	'';
		$field->params[]	=	self::g_getParamsHtml( 1, $style, $column1, $column2 );
		
		// 2
		$hide				=	( @$field->link != '' ) ? '' : ' hidden';
		$value				=	@$field->link;
		$text				=	$data['link'][$value]->text;
		$to					=	( isset( $config['construction']['link'][$field->type] ) ) ? 'link-'.$field->type : 'link';
		$column1			=	'<input type="hidden" id="'.$name.'_link" name="ffp['.$name.'][link]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_link" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_link_options" name="ffp['.$name.'][link_options]" '
							.	'value="'.( ( @$field->link_options != '' ) ? htmlspecialchars( $field->link_options ) : '' ).'" />'
							.	' <span class="c_link'.$hide.'" name="'.$name.'">+</span>';
		$hide				=	( @$field->typo != '' ) ? '' : ' hidden';
		$value				=	@$field->typo;
		$text				=	$data['typo'][$value]->text;
		$to					=	( isset( $config['construction']['typo'][$field->type] ) ) ? 'typo-'.$field->type : 'typo';
		$column2			=	'<input type="hidden" id="'.$name.'_typo" name="ffp['.$name.'][typo]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_typo" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_typo_options" name="ffp['.$name.'][typo_options]" '
							.	'value="'.( ( @$field->typo_options != '' ) ? htmlspecialchars( $field->typo_options ) : '' ).'" />'
							.	'<input type="hidden" id="'.$name.'_typo_label" name="ffp['.$name.'][typo_label]" value="'.@$field->typo_label.'" />'
							.	' <span class="c_typo'.$hide.'" name="'.$name.'">+</span>';
		$field->params[]	=	self::g_getParamsHtml( 2, $style, $column1, $column2 );
		
		// 3
		if ( !$data['markup'] ) {
			$column1		=	'';
		} else {
			$value			=	@$field->markup;
			$to				=	( isset( $config['construction']['markup'][$field->type] ) ) ? 'markup-'.$field->type : 'markup';
			$text			=	$data['markup'][$value]->text;
			$column1		=	'<input type="hidden" id="'.$name.'_markup" name="ffp['.$name.'][markup]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_markup" data-to="'.$to.'">'.$text.'</span>';
		}
		if ( !$data['markup_class'] ) {
			$column2		=	'';
		} else {
			$column2		=	'<input class="thin blue" type="text" name="ffp['.$name.'][markup_class]" size="22" '
							.	'value="'.( ( @$field->markup_class != '' ) ? htmlspecialchars( trim( $field->markup_class ) ) : '' ).'" />';
		}
		$field->params[]	=	self::g_getParamsHtml( 3, $style, $column1, $column2 );
		
		// 4
		$hide				=	( @$field->restriction != '' ) ? '' : ' hidden';
		$value				=	( @$field->access == '' ) ? 1 : ( ( @$field->access ) ? (int)$field->access : 0 );
		$text				=	$data['access'][$value]->text;
		$column1			=	'<input type="hidden" id="ffp'.$name.'_access" name="ffp['.$name.'][access]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="ffp'.$name.'_access" data-to="access">'.$text.'</span>';
		$value				=	@$field->restriction;
		$text				=	$data['restriction'][$value]->text;
		$to					=	( isset( $config['construction']['restriction'][$field->type] ) ) ? 'restriction-'.$field->type : 'restriction';
		$column2			=	'<input type="hidden" id="'.$name.'_restriction" name="ffp['.$name.'][restriction]" value="'.$value.'" />'
							.	'<span class="text blue sp2se" data-id="'.$name.'_restriction" data-to="'.$to.'">'.$text.'</span>'
							.	'<input type="hidden" id="'.$name.'_restriction_options" name="ffp['.$name.'][restriction_options]" '
							.	'value="'.( ( @$field->restriction_options != '' ) ? htmlspecialchars( $field->restriction_options ) : '' ).'" />'
							.	' <span class="c_res'.$hide.'" name="'.$name.'">+</span>';
		$field->params[]	=	self::g_getParamsHtml( 4, $style, $column1, $column2 );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// g_onCCK_FieldPrepareContent
	public static function g_onCCK_FieldPrepareContent( &$field, &$config = array() )
	{
		$field->label		=	( @$field->label2 ) ? $field->label2 : ( ( $field->label ) ? $field->label : $field->title );
		if ( $field->label == 'clear' || $field->label == 'none' ) {
			$field->label	=	'';
		}
		if ( $config['doTranslation'] ) {
			if ( $field->label == '&nbsp;' ) {
				$field->label	=	'Nbsp';
			}
			if ( trim( $field->label ) ) {
				$field->label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->label ) ) );
			}
			if ( trim( $field->description ) ) {
				$desc	=	trim( strip_tags( $field->description ) );
				if ( $desc ) {
					$field->description	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $desc ) );
				}
			}
		}
		
		$field->linked		=	false;
		$field->state		=	1;
		$field->typo_target	=	'value';
		
		// Restriction
		if ( isset( $field->restriction ) && $field->restriction ) {
			$field->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$field->restriction, 'onCCK_Field_RestrictionPrepareContent', array( &$field, &$config ) );
			if ( !$field->authorised ) {
				$field->display	=	0;
				$field->state	=	0;
			}
		}
	}
	
	// g_onCCK_FieldPrepareForm
	public static function g_onCCK_FieldPrepareForm( &$field, &$config = array() )
	{
		$field->label		=	( @$field->label2 ) ? $field->label2 : ( ( $field->label ) ? $field->label : $field->title );
		if ( $field->label == 'clear' || $field->label == 'none' ) {
			$field->label	=	'';
		}
		if ( $config['doTranslation'] ) {
			if ( $field->label == '&nbsp;' ) {
				$field->label	=	'Nbsp';
			}
			if ( trim( $field->label ) ) {
				$field->label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->label ) ) );
			}
			if ( trim( $field->description ) ) {
				$desc	=	trim( strip_tags( $field->description ) );
				if ( $desc ) {
					$field->description	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $desc ) );
				}
			}
		}
		
		$field->link		=	'';
		$field->state		=	1;
		$field->typo_target	=	'value';
		$field->validate	=	array();
		
		// Restriction
		if ( isset( $field->restriction ) && $field->restriction ) {
			$field->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$field->restriction, 'onCCK_Field_RestrictionPrepareForm', array( &$field, &$config ) );
			if ( !$field->authorised ) {
				$field->display	=	0;
				$field->state	=	0;
			}
		}

		// Css
		if ( $field->variation ) {
			$css	=	'';

			switch ( $field->variation ) {
				case 'value':
					$css	=	'is-value';
					break;
				case 'form_filter':
					$css	=	'is-filter';
					break;
				case 'form_filter_ajax':
					$css	=	'is-filter-ajax';
					break;
				default:
					break;
			}
			if ( $css ) {
				$field->css	.=	$field->css ? ' '.$css : $css;
			}
		}

		// Attributes
		if ( isset( $field->attributes ) && $field->attributes != '' ) {
			if ( strpos( $field->attributes, 'J(' ) !== false ) {
				$matches	=	'';
				$search		=	'#J\((.*)\)#U';
				preg_match_all( $search, $field->attributes, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $text ) {
						$field->attributes	=	str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $field->attributes );
					}
				}
			}
		}
	}
		
	// g_onCCK_FieldPrepareForm_Validation
	public static function g_onCCK_FieldPrepareForm_Validation( &$field, $id, &$config = array(), $rules = false )
	{
		if ( $field->validation ) {
			require_once JPATH_PLUGINS.'/cck_field_validation/'.$field->validation.'/'.$field->validation.'.php';
			JCck::callFunc_Array( 'plgCCK_Field_Validation'.$field->validation, 'onCCK_Field_ValidationPrepareForm', array( &$field, $id, &$config ) );
		}
		
		if ( $rules !== false ) {
			$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );
			if ( isset( $rules['maxSize'] ) ) {
				if ( $field->maxlength > 0 ) {
					$field->validate[]	=	'maxSize['.$field->maxlength.']';

					if ( !isset( $config['validation']['maxSize'] ) ) {
						$config['validation']['maxSize']	=	'
																"maxSize":{
																	"regex":"none",
																	"alertText":"'.$prefix.JText::_( 'PLG_CCK_FIELD_VALIDATION_MAXLENGTH_ALERT' ).'",
																	"alertText2":"'.JText::_( 'PLG_CCK_FIELD_VALIDATION_MAXLENGTH_ALERT2' ).'"}
																';
					}
				}
			}
			if ( isset( $rules['minSize'] ) ) {
				if ( $field->minlength > 0 ) {
					$field->validate[]	=	'minSize['.$field->minlength.']';
					if ( !isset( $config['validation']['minSize'] ) ) {
						$config['validation']['minSize']	=	'
																"minSize":{
																	"regex":"none",
																	"alertText":"'.$prefix.JText::_( 'PLG_CCK_FIELD_VALIDATION_MINLENGTH_ALERT' ).'",
																	"alertText2":"'.JText::_( 'PLG_CCK_FIELD_VALIDATION_MINLENGTH_ALERT2' ).'"}
																';
					}
				}
			}
		}
	}
	
	// g_onCCK_FieldPrepareSearch
	public function g_onCCK_FieldPrepareSearch( &$field, &$config = array() )
	{
		$field->label		=	( @$field->label2 ) ? $field->label2 : ( ( $field->label ) ? $field->label : $field->title );
		if ( $field->label == 'clear' || $field->label == 'none' ) {
			$field->label	=	'';
		}
		if ( $config['doTranslation'] ) {
			if ( $field->label == '&nbsp;' ) {
				$field->label	=	'Nbsp';
			}
			if ( trim( $field->label ) ) {
				$field->label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->label ) ) );
			}
			if ( trim( $field->description ) ) {
				$desc	=	trim( strip_tags( $field->description ) );
				if ( $desc ) {
					$field->description	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $desc ) );
				}
			}
		}

		$field->markup		=	'';
		$field->state		=	1;
		
		// Restriction
		if ( isset( $field->restriction ) && $field->restriction ) {
			$field->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$field->restriction, 'onCCK_Field_RestrictionPrepareForm', array( &$field, &$config ) );
			if ( !$field->authorised ) {
				$field->display	=	0;
				$field->state	=	0;
			}
		}
	}
	
	// g_onCCK_FieldPrepareStore
	public function g_onCCK_FieldPrepareStore( &$field, $name, $value, &$config = array() )
	{
		$field->label		=	( @$field->label2 ) ? $field->label2 : ( ( $field->label ) ? $field->label : $field->title );
		if ( $field->label == 'clear' || $field->label == 'none' ) {
			$field->label	=	'';
		}
		if ( $config['doTranslation'] ) {
			if ( $field->label == '&nbsp;' ) {
				$field->label	=	'Nbsp';
			}
			if ( trim( $field->label ) ) {
				$field->label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->label ) ) );
			}
			if ( trim( $field->description ) ) {
				$desc	=	trim( strip_tags( $field->description ) );
				if ( $desc ) {
					$field->description	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $desc ) );
				}
			}
		}
		
		$storage	=	$field->storage;
		
		if ( $storage == 'none' ) {
			if ( ! isset( $config['storages']['none'] ) ) {
				$config['storages']['none']	=	array();
			}			
			if ( is_array( $value ) ) {
				@$config['storages']['none'][$field->storage_field]	=	$value;
			} else {
				@$config['storages']['none'][$field->storage_field]	.=	trim( $value );
			}
		} else {
			if ( ! $field->storage_field2 ) {
				$field->storage_field2	=	$field->name;
			}
			require_once JPATH_PLUGINS.'/cck_storage/'.$storage.'/'.$storage.'.php';
			JCck::callFunc_Array( 'plgCCK_Storage'.$storage, 'onCCK_StoragePrepareStore', array( &$field, $value, &$config ) );
		}

		/*
		$field->state		=	1;
		
		// Restriction
		if ( isset( $field->restriction ) && $field->restriction ) {
			$field->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$field->restriction, 'onCCK_Field_RestrictionPrepareForm', array( &$field, &$config ) );
			if ( !$field->authorised ) {
				$field->display	=	0;
				$field->state	=	0;
			}
		}
		*/
	}
	
	// g_onCCK_FieldPrepareStore_X
	public function g_onCCK_FieldPrepareStore_X( &$field, $name, $value, $store, &$config = array() )
	{
		$storage	=	$field->storage;
		if ( $storage != 'none' ) {
			if ( ! $field->storage_field2 ) {
				$field->storage_field2	=	$field->name;
			}
			require_once JPATH_PLUGINS.'/cck_storage/'.$storage.'/'.$storage.'.php';
			JCck::callFunc_Array( 'plgCCK_Storage'.$storage, 'onCCK_StoragePrepareStore_X', array( &$field, $value, $store, &$config ) );
		}
	}
	
	// g_onCCK_FieldPrepareStore_Validation
	public function g_onCCK_FieldPrepareStore_Validation( &$field, $name, &$value, &$config = array() )
	{
		if ( $config['doValidation'] == 1 || $config['doValidation'] == 3 ) {
			if ( $field->required ) {
				plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareStore( $field, $name, $value, $config );
			}
			
			$validation	=	$field->validation;
			if ( ! $validation ) {
				return;
			}
			require_once JPATH_PLUGINS.'/cck_field_validation/'.$validation.'/'.$validation.'.php';
			JCck::callFunc_Array( 'plgCCK_Field_Validation'.$validation, 'onCCK_Field_ValidationPrepareStore', array( &$field, $name, &$value, &$config ) );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// g_onCCK_FieldRenderContent
	public static function g_onCCK_FieldRenderContent( &$field, $target = 'value' )
	{
		if ( isset( $field->typo ) && $field->typo != '' ) {
			return $field->typo;
		} else {
			if ( isset( $field->link ) && $field->link ) {
				if ( !isset( $field->link_state ) || $field->link_state ) {
					return ( isset( $field->html ) ) ? $field->html : '';
				}
			}
		}

		return $field->$target;
	}
	
	// g_onCCK_FieldRenderForm
	public static function g_onCCK_FieldRenderForm( &$field )
	{
		return $field->form;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// g_addProcess
	public static function g_addProcess( $event, $type, &$config, $params, $priority = 3 )
	{
		if ( $event && $type ) {
			$process						=	new stdClass;
			$process->group					=	self::$construction;
			$process->type					=	$type;
			$process->params				=	$params;
			$process->priority				=	$priority;
			$config['process'][$event][]	=	$process;
		}
	}
	
	// g_addScriptDeclaration
	public static function g_addScriptDeclaration( $script )
	{
		if ( JFactory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
			echo '<script type="text/javascript">jQuery(document).ready(function($){'.$script.'});</script>';
		} else {
			JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($){'.$script.'});' );
		}
	}
	
	//g_doConditionalStates
	public static function g_doConditionalStates( $cck, $fieldname, $value )
	{
	}
	
	// g_get
	public static function g_get( $var = '' )
	{
		//return static::${$var};
	}
	
	// g_getDisplayVariation
	public static function g_getDisplayVariation( &$field, $variation, $value, $text, $form, $id, $name, $html, $hidden = '', $more = '', $config = array() )
	{
		$class	=	'inputbox' . ( $field->css ? ' '.$field->css : '' );
		
		if ( $variation == 'value' ) {
			$attr			=	$field->attributes ? ' '.$field->attributes : '';
			$base			=	( $hidden != '' ) ? trim( $hidden ) : '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ).'" class="'.$class.'"'.$attr.' />';
			$field->form	=	$base . '<span id="_'.$id.'" class="variation_value is-value">'.$text.'</span>';
		} elseif ( $variation == 'disabled' ) {
			$base			=	( $hidden != '' ) ? trim( $hidden ) : '<input type="hidden" id="_'.$id.'" name="'.$name.'" value="'.htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ).'" class="'.$class.'" />';
			$field->form	=	$base;
			if ( $html ) {
				$field->form	.=	str_replace( $html, $html.' disabled="disabled"', $form );
			}
		} elseif ( $variation == 'form_filter' || $variation == 'form_filter_ajax' ) {
			$field->form	=	$form;
			if ( isset( $config['submit'] ) && isset( $config['formId'] ) ) {
				$parent		=	$config['formId'];
				$submit		=	$config['submit'];
			} else {
				$parent		=	'seblod_form';
				$submit		=	'JCck.Core.submit';
			}
			if ( $field->script ) {
				self::g_addScriptDeclaration( $field->script );
			}
			if ( $variation == 'form_filter_ajax' ) {
				$field->form	=	str_replace( 'class="', 'data-cck-ajax="" class="', $field->form );

				self::g_addScriptDeclaration( '$("form#'.$parent.'").on("change", "#'.$id.'.is-filter-ajax", function() { var q = ""; $("form#'.$parent.' [data-cck-ajax=\'\']").each(function(i) { q += "&"+$(this).attr("name")+"="+$(this).myVal(); }); JCck.Core.loadmore("&start=0"+q,0,1); });' );
			} else {
				self::g_addScriptDeclaration( '$("form#'.$parent.'").on("change", "#'.$id.'.is-filter", function() { '.$submit.'(\'search\'); });' );
			}
		} elseif ( $variation == 'list' || $variation == 'list_filter' || $variation == 'list_filter_ajax' ) {
			$attributes		=	( isset( $field->attributesList ) && $field->attributesList != '' ) ? explode( '||', $field->attributesList ) : array();
			$base			=	( $hidden != '' ) ? trim( $hidden ) : '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ).'" class="'.$class.'" />';
			$field->form	=	'';			
			$options		=	( isset( $field->optionsList ) ) ? $field->optionsList : $field->options;
			$options		=	( $options != '' ) ? explode( '||', $options ) : array();
			
			if ( count( $options ) ) {
				static $loaded	=	array();
				if ( !isset($loaded[$id] ) ) {
					if ( isset( $config['submit'] ) && isset( $config['formId'] ) ) {
						$parent		=	$config['formId'];
						$submit		=	$config['submit'];
					} else {
						$parent		=	'seblod_form';	
						$submit		=	'JCck.Core.submit';
					}
					$doc			=	JFactory::getDocument();
					$then			=	'';
					if ( $variation == 'list' || $variation == 'list_filter_ajax' ) {
						if ( $variation == 'list_filter_ajax' ) {
							$base	=	str_replace( 'class="', 'data-cck-ajax="" class="', $base );
							$then	=	' var q = ""; $("form#'.$parent.' [data-cck-ajax=\'\']").each(function(i) { q += "&"+$(this).attr("name")+"="+$(this).myVal(); }); JCck.Core.loadmore("&start=0&"+$("#'.$id.'").attr("name")+"="+q,0,1);';
						}
						$then		.=	' $("#'.$id.'_ > li").removeClass("active"); $(this).parent().addClass("active")';
					} else {
						$then		=	' '.$submit.'("search");';
					}
					$js				=	'$("form#'.$parent.'").on("click", "#'.$id.'_ > li a", function() {var v = $(this).parent().attr("data-value"); $("#'.$id.'").val(v);'.$then.' });';
					$js				=	'(function ($){ $(document).ready(function() { '.$js.' }); })(jQuery);';
					self::g_addScriptDeclaration( $js );
					$loaded[$id]	=	1;
				}
				foreach ( $options as $k=>$opt ) {
					$attribute	=	@$attributes[$k];
					$o			=	explode( '=', $opt );
					$class		=	'';
					if ( @$o[1] == $value ) {
						$class		=	' class="active"';
					} 
					if ( $o[0] != '' ) {
						$field->form	.=	'<li'.$class.' data-value="'.@$o[1].'"'.$attribute.'><a class="list-variation-item" href="javascript:void(0);"><span>'.$o[0].'</span></a></li>';
					}
				}
				if ( $field->form != '' ) {
					$class			=	'list-variation-items';
					$class			=	( $field->css != '' ) ? $class.' '.$field->css : $class;
					$field->form	=	'<ul class="'.$class.'" id="'.$id.'_">'.$field->form.'</ul>';
				}
			}
			$field->form	.=	$base;
		} elseif ( $variation == 'clear' ) {
			$base			=	( $hidden != '' ) ? trim( $hidden ) : '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ).'" class="'.$class.'" />';
			$field->form	=	$base;
			$field->display =	0;
		} else {
			$attr_id		=	( $variation == 'hidden_anonymous' ) ? '' : 'id="'.$id.'" ';
			$attr			=	$field->attributes ? ' '.$field->attributes : '';
			$base			=	( $hidden != '' ) ? trim( $hidden ) : '<input type="hidden" '.$attr_id.'name="'.$name.'" value="'.htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' ).'" class="'.$class.'"'.$attr.' />';
			$field->form	=	$base;
			if ( $field->display ) {
				$field->display =	1;
			}
		}
		
		$field->form	.=	$more;
	}
	
	// g_getOptionText
	public static function g_getOptionText( &$value, $options, $separator = '', $config = array() )
	{
		$opts	=	explode( '||', $options );
		$text	=	'';
		
		if ( $value == '' ) {
			return $text;
		}
		
		if ( $separator ) {
			$values		=	( is_array( $value ) ) ? $value : explode( $separator, $value );
		} elseif ( $separator != '0' ) {
			$values		=	array( 0=>$value );
			$separator	=	'';
		} else {
			$values		=	$value;
		}
		
		$value	=	array();
		if ( count( $opts ) ) {
			foreach ( $values as $i=>$val ) {
				if ( $val != '' ) {
					$exist	=	false;
					foreach ( $opts as $opt ) {
						if ( strpos( '='.$opt.'||', '='.$val.'||' ) !== false ) {
							$texts	=	explode( '=', $opt );
							if ( $config['doTranslation'] && trim( $texts[0] ) != '' ) {
								$texts[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $texts[0] ) ) );
							}
							$exist	=	true;
							$text	.=	$texts[0].$separator;
							break;
						}
					}
					if ( $exist === true ) {
						$value[]	=	$val;
					}
				}
			}
		}
		
		if ( $separator ) {
			$length	=	strlen( $separator );
			$value	=	implode( $separator, $value );
			if ( $text != '' ) {
				$text	=	substr( $text, 0, -$length );
			}
		} elseif ( $separator != '0' ) {
			$value	=	(string)@$value[0];
		}
		
		return $text;
	}
	
	// g_getParamsHtml
	public static function g_getParamsHtml( $num, $style, $column1, $column2 )
	{
		$html	=	'<div class="pane p'.$num.$style[$num].'">';
		if ( $column1 != '' ) {
			$html	.=	'<div class="col1"><div class="colc">'.$column1.'</div></div>';
		}
		if ( $column2 != '' ) {
			$html	.=	'<div class="col2"><div class="colc">'.$column2.'</div></div>';
		}
		$html	.=	'</div>';
		
		return $html;
	}
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
	
	// g_isStaticVariation
	public static function g_isStaticVariation( &$field, $variation, $strict = false )
	{
		if ( $strict !== false ) {
			return ( $variation == 'clear' || $variation == 'hidden' ) ? true : false;
		} else {
			return ( $variation == 'clear' || $variation == 'hidden' || $variation == 'value' ) ? true : false;
		}
	}
}
?>