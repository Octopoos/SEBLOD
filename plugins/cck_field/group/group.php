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
class plgCCK_FieldGroup extends JCckPluginField
{
	protected static $type		=	'group';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		$data['rows']	=	1;
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		if ( !$field->state ) { /* TODO: Support for "onBeforeRender" restrictions may be implemented later. */
			$field->value	=	'';
			return;
		}
	
		// Prepare
		$name		=	$field->name;
		$dispatcher	=	JEventDispatcher::getInstance();
		$fields		=	self::_getChildren( $field, $config );
		$xn			=	1;
		$content	=	array();
		for ( $xi = 0; $xi < $xn; $xi++ ) {
			foreach ( $fields as $f ) {
				if ( is_object( $f ) ) {
					$f_name				=	$f->name;
					$f_value			=	'';
					$inherit			=	array();
					$content[$f_name]	=	clone $f;
					$table				=	$f->storage_table;
					if ( $table && ! isset( $config['storages'][$table] ) ) {
						$config['storages'][$table]	=	'';
						$dispatcher->trigger( 'onCCK_Storage_LocationPrepareContent', array( &$f, &$config['storages'][$table], $config['pk'], &$config ) );
					}
					$dispatcher->trigger( 'onCCK_StoragePrepareContent_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi ) );
					//					
					$dispatcher->trigger( 'onCCK_FieldPrepareContent', array( &$content[$f_name], $f_value, &$config, $inherit, true ) );
					
					$target	=	( isset( $content[$f_name]->typo_target ) ) ? $content[$f_name]->typo_target : 'value';
					if ( $content[$f_name]->link != '' ) {
						$dispatcher->trigger( 'onCCK_Field_LinkPrepareContent', array( &$content[$f_name], &$config ) );
						if ( $content[$f_name]->link && !@$content[$f_name]->linked ) {
							JCckPluginLink::g_setHtml( $content[$f_name], $target );
						}
					}
					if ( @$content[$f_name]->typo && $content[$f_name]->$target != '' && $config['doTypo'] ) {
						$dispatcher->trigger( 'onCCK_Field_TypoPrepareContent', array( &$content[$f_name], $content[$f_name]->typo_target, &$config ) );
					} else {
						$content[$f_name]->typo	=	'';
					}
					$config['fields'][$f->name]	=	$content[$f_name];
				}
			}
		}
		
		// Set
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

		if ( !$field->state ) { /* TODO: Support for "onBeforeRender" restrictions may be implemented later. */
			$field->form	=	'';
			$field->value	=	'';
			return;
		}
		
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
		$xn			=	( $value ) ? $value : $field->rows;
		$form		=	array();

		for ( $xi = 0; $xi < $xn; $xi++ ) {
			foreach ( $fields as $f ) {
				if ( is_object( $f ) ) {
					$f_name		=	$f->name;
					$f_value	=	'';
					if ( $config['pk'] ) {
						$table	=	$f->storage_table;
						if ( $table && ! isset( $config['storages'][$table] ) ) {
							$config['storages'][$table]	=	'';
							$dispatcher->trigger( 'onCCK_Storage_LocationPrepareForm', array( &$f, &$config['storages'][$table], $config['pk'], &$config ) );
						}
						$dispatcher->trigger( 'onCCK_StoragePrepareForm_Xi', array( &$f, &$f_value, &$config['storages'][$table], $name, $xi ) );
					} elseif ( $f->live ) {
						$dispatcher->trigger( 'onCCK_Field_LivePrepareForm', array( &$f, &$f_value, &$config ) );
					} else {
						$f_value				=	$f->live_value;
					}
					$inherit					=	array();
					$clone						=	clone $f;
					$results					=	$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$clone, $f_value, &$config, $inherit, true ) );
					$form[$f_name]				=	@$results[0];
					@$form[$f_name]->name		=	$f->name;
					$config['fields'][$f->name]	=	$form[$f_name];
				}
			}
		}
		
		// Set
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
		$dispatcher	=	JEventDispatcher::getInstance();
		$data		=	$config['post'];
		$value		=	'';
		
		// Prepare
		$fields 	=	self::_getChildren( $field, $config );
		if ( count( $fields ) ) {
			foreach ( $fields as $f ) {
				$name		=	$f->name;
				$f->state	=	'';
				
				// Restriction
				if ( isset( $f->restriction ) && $f->restriction ) {
					$f->authorised	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$f->restriction, 'onCCK_Field_RestrictionPrepareStore', array( &$f, &$config ) );
					
					if ( !$f->authorised ) {
						continue;
					}
				}

				if ( ( $f->variation == 'hidden' || $f->variation == 'disabled' || $f->variation == 'value' ) && ! $f->live && $f->live_value != '' ) {
					$val	=	$f->live_value;
				} else {
					if ( isset( $data[$name] ) ) {
						$val		=	$data[$name];
					} else {
						$val		=	NULL;
						$f->state	=	'disabled';
					}
				}
				$dispatcher->trigger( 'onCCK_FieldPrepareStore', array( &$f, $val, &$config, array() ) );
				$config['fields'][$name]	=	$f;
			}
		}
		
		$field->value	=	$value;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		if ( $field->typo ) {
			return $field->typo;
		} else {			
			$doc	=	JFactory::getDocument();
			$doc->addStyleSheet( self::$path.'assets/css/'.self::$type.'.css' );
	
			$count	=	count( $field->value );
			$html	=	'';
	
			if ( $count ) {
				$i		=	0;
				$row	=	'';
				$isRow	=	false;
				foreach ( $field->value as $elem ) {
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
							$isRow	=	true;
						}
					}
				}
				if ( $field->markup == 'none' ) {
					$html		=	$row;
				} else {
					if ( $isRow ) {
						$html	.=	'<div id="'.$field->name.'_'.$i.'" class="gxi"><div>' .$row. '</div></div>';
					}
					if ( $html ) {
						$html	=	'<div id="'.$field->name.'" class="gx">' .$html. '</div>';
					}
				}
			}
			
			return $html;
		}
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		$doc	=	JFactory::getDocument();
		$doc->addStyleSheet( self::$path.'assets/css/'.self::$type.'.css' );
		
		$orientation	=	'vertical_gx'; //vertical_gx horizontal_gx
		$width			=	'';

		$count	=	count( $field->form );
		$html	=	'';
		
		if ( $count ) {
			if ( $field->markup != 'none' ) {
				$html	.=	'<div id="cck1_sortable_'.$field->name.'" class="'.$orientation.' '.$width.'">';
			}
			$html	.=	self::_getHtml( $field, $field->form, 0, $count - 1, $config );
			
			if ( $field->markup != 'none' ) {
				$html	.=	'</div>';
			}
		}
		
		return $html;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _getHtml
	protected static function _getHtml( $field, $group, $i, $size_group, &$config )
	{
		$client	=	'cck_'.$config['client'];
		$html	=	'';
		$js		=	'';
		$rId	=	$config['rendering_id'];

		if ( $field->markup != 'none' ) {
			$html	.=	'<div id="'.$rId.'_forms_'.$field->name.'_'.$i.'" class="cck_form cck_form_group cck_form_group_first cck_form_group_last">';
			$html	.=	'<div id="'.$rId.'_form_'.$field->name.'_'.$i.'" class="cck_cgx cck_cgx_form cck_cgx_form_first cck_cgx_form_last">';	
		}
		
		foreach ( $group as $elem ) {
			if ( $elem->display > 1 ) {
				JCck::callFunc( 'plgCCK_Field'.$elem->type, 'onCCK_FieldRenderForm', $elem );

				if ( $elem->markup == 'none' ) {
					if ( $elem->label != '' ) {
						$suffix	=	'';
						if ( $elem->label != '&nbsp;' ) {
							$suffix	=	( $elem->required ) ? '<span class="star"> *</span>' : '';
						}
						$html	.=	'<label for="'.$elem->name.'">'.$elem->label.$suffix.'</label>';
					}
				} else {
					$html	.=	'<div id="'.$rId.'_'.$field->name.'_'.$i.'_'.$elem->name.'" class="cck_forms '.$client.' cck_'.$elem->type.' cck_'.$elem->name.'">';
					
					if ( $elem->label != '' ) {
						$suffix	=	'';
						if ( $elem->label != '&nbsp;' ) {
							$suffix	=	( $elem->required ) ? '<span class="star"> *</span>' : '';
						}
						$html	.=	'<div id="'.$rId.'_'.$field->name.'_'.$i.'_label_'.$elem->name.'" class="cck_label cck_label_'.$elem->type.'"><label for="'.$elem->name.'">'.$elem->label.$suffix.'</label></div>';
					}
					$html	.=	'<div id="'.$rId.'_'.$field->name.'_'.$i.'_form_'.$elem->name.'" class="cck_form cck_form_'.$elem->type.@$elem->markup_class.'">';
				}
			}
			if ( $elem->display ) {
				$html	.=	$elem->form;
			}

			if ( $elem->display > 1 && $elem->markup != 'none' ) {
				$html	.=	'</div>';
				$html	.=	'</div>';
			}
			
			// Computation
			if ( @$elem->computation ) {
				$computation			=	new JRegistry;
				$computation->loadString( $elem->computation_options );
				$computation_options	=	$computation->toObject();
				
				if ( $computation_options->calc == 'custom' ) {
					$computed		=	'';
					$computations	=	explode( ',', $elem->computation );
					if ( count( $computations ) ) {
						foreach ( $computations as $k=>$v ) {
							$computed	.=	chr( 97 + $k ).':$("'.$v.'")'.',';
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
						$config['computation'][$event][]	=	array( '_'=>$elem->computation,
																		   'js'=>'$("#'.$elem->name.'").calc( "'.$computation_options->custom.'", {'.$computed.'}, '
																												 .$targets.', function (s){return s'.$format.';} );' );
					} else {
						$js		.= '(function ($){JCck.Core.recalc_'.$elem->name.' = function() {'
							.'$("#'.$elem->name.'").calc( "'.$computation_options->custom.'", {'.$computed.'}, '.$targets.', function (s){return s'.$format.';} );}'.'})(jQuery);';
						if ( $event != 'none' ) {
							$js	.= '$("'.$elem->computation.'").bind("'.$event.'", JCck.Core.recalc_'.$elem->name.'); JCck.Core.recalc_'.$elem->name.'();';
						}
					}
				} else {
					$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
					$targets	=	@$computation_options->targets ? ', '.json_encode( $computation_options->targets ) : '';
					if ( @$computation_options->recalc ) {
						$config['computation'][$event][]	=	array( '_'=>$elem->computation,
																	   'js'=>'$("'.$elem->computation.'").'.$computation_options->calc.'("'.$event.'", "#'.$elem->name.'"'.$targets.');' );
					} else {
						$js		.=	'$("'.$elem->computation.'").'.$computation_options->calc.'("'.$event.'", "#'.$elem->name.'"'.$targets.');';
						if ( $event != 'none' ) {
							$js	.=	'$("'.$elem->computation.'").bind("'.$event.'", JCck.Core.recalc);';
						}
					}
				}
				$config['doComputation']	=	1;
			}
			
			// Conditional
			if ( @$elem->conditional ) {
				$conditions					=	explode( ',', $elem->conditional );
				$elem->conditional_options	=	str_replace( '#form#', '#'.$elem->name, $elem->conditional_options );
				$js							.=	'$("#'.$rId.'_'.$field->name.'_'.$i.'_'.$elem->name.'").conditionalStates('.$elem->conditional_options.');';
			}
		}
		
		if ( $field->markup != 'none' ) {
			$html	.=	'</div>';
			$html	.=	'</div>';
		}
		
		if ( $js ) {
			if ( JFactory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
				echo '<script type="text/javascript">jQuery(document).ready(function($){'.$js.'});</script>';
			} else {
				JFactory::getDocument()->addScriptDeclaration( 'jQuery(document).ready(function($){'.$js.'});' );
			}
		}
		
		return $html;
	}
	
	// _getChildren
	protected static function _getChildren( $parent, $config = array() )
	{
		$db		=	JFactory::getDbo();
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		
		$client	=	( $config['client'] == 'list' || $config['client'] == 'item' ) ? 'intro' : $config['client'];
		$where	=	' WHERE c.client = "'.$client.'" AND b.name = "'.$parent->extended.'"'
				.	' AND c.access IN ('.$access.')';
		$order	=	' ORDER BY c.ordering ASC';
		
		if ( $client == 'intro' || $client == 'content' ) {
			$cc	=	'';
		} else {
			$cc	=	'c.required, c.required_alert, ';
		}
		$query	= ' SELECT DISTINCT a.*, c.client,'
		        . 	' c.label as label2, c.variation, c.variation_override, '.$cc.'c.validation, c.validation_options, c.live, c.live_options, c.live_value, c.link, c.link_options, c.typo, c.typo_label, c.typo_options, c.markup, c.markup_class, c.stage, c.access, c.restriction, c.restriction_options, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position'
				.	' FROM #__cck_core_fields AS a'
				.	' LEFT JOIN #__cck_core_type_field AS c ON c.fieldid = a.id'
				.	' LEFT JOIN #__cck_core_types AS b ON b.id = c.typeid'
				.	$where
				.	$order
				;
		if ( $config['client'] == 'list' || $config['client'] == 'item' ) {
			$fields	=	JCckDatabaseCache::loadObjectList( $query, 'name' );
		} else {
			$db->setQuery( $query );
			$fields	=	$db->loadObjectList( 'name' ); //#
		}
		
		if ( ! count( $fields ) ) {
			return array();
		}
		
		return $fields;
	}
}
?>