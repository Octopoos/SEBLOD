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
class plgCCK_Field_TypoJoomla_Jgrid extends JCckPluginTypo
{
	protected static $type		=	'joomla_jgrid';
	protected static $increment	=	array();
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$value	=	parent::g_hasLink( $field, $typo, $field->$target );

		// Set
		$field->typo	=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$class	=	$typo->get( 'class', '' );
		$start	=	$typo->get( 'start', '1' );
		$type	=	$typo->get( 'type', '' );
		
		if ( !$type ) {
			return $value;
		}

		static $i		=	0;
		static $formId	=	NULL;
		static $pks		=	array();
		$pk				=	$config['pk'];
		if ( !isset( $pks[$pk] ) ) {
			$pks[$pk]	=	$i;
			$i++;
		}

		switch ( $type ) {
			case 'activation':
			case 'block':
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'type'=>$type, 'value'=>$value, 'class'=>$class, 'pk'=>$pk, 'pk_i'=>$pks[$pk] ) );

				$config['formWrapper']	=	true;
				break;
			case 'dropdown':
				static $dropdown		=	array();
				static $dropdown_css	=	false;

				if ( !isset( $dropdown[$pk] ) ) {
					$class	=	$typo->get( 'class1', '' );
					$class	=	$class ? ' '.$class : '';
					$value	=	'<button data-toggle="dropdown" class="dropdown-toggle btn'.$class.'"><span class="caret"></span></button>'
							.	'<ul class="dropdown-menu flex-column-reverse"></ul>';

					$dropdown[$pk]		=	array( 'parent'=>$field->name, 'html'=>'' );

					if ( !$dropdown_css ) {
						$dropdown_css	=	true;

						JFactory::getDocument()->addStyleDeclaration( '.btn-group.open > .dropdown-menu{display: -webkit-box; display: -ms-flexbox; display: flex; -webkit-box-orient:vertical; -webkit-box-direction:reverse; -ms-flex-direction:column-reverse; flex-direction:column-reverse;}' );
					}
				}
				$dropdown[$pk]['html']	=	'<li>'.( ( isset( $field->html ) && $field->html ) ? $field->html : $value ).'</li>';

				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$dropdown[$pk]['parent'], 'target'=>$field->name, 'type'=>$type, 'html'=>$dropdown[$pk]['html'] ) );
				break;
			case 'featured':
				static $loaded_featured	=	0;
				if ( !$loaded_featured ) {
					JHtml::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_content/helpers/html' );
					$loaded_featured	=	1;
				}
				$value		=	$field->value;

				if ( is_numeric( $value ) ) {
					$value	=	( (int)$value > 0 ) ? 1 : 0;
				} elseif ( is_array( $value ) ) {
					$value	=	( count( $value ) > 0 ) ? 1 : 0;
				} else {
					$value	=	( $value == '' ) ? 0 : 1;
				}

				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'type'=>$type, 'value'=>$value, 'class'=>$class, 'pk'=>$pks[$pk] ) );

				$config['formWrapper']	=	true;
				break;
			case 'form':
			case 'form_disabled':
			case 'form_hidden':
				$class			=	$typo->get( 'class2', '' );
				if ( !isset( $config['doValidation'] ) ) {
					$config['doValidation']	=	0;
				}
				$hasIdentifier		=	$typo->get( 'use_identifier', '1' );
				$identifier			=	( $typo->get( 'identifier', 'id' ) == 'pk' ) ? $config['pk'] : $config['id'];
				$identifier_name	=	$typo->get( 'identifier_name', '' );
				$identifier_name	=	( $identifier_name != '' ) ? $identifier_name : $field->name;
				$identifier_suffix	=	$typo->get( 'identifier_suffix', '' );
				$inherit			=	array( 'id'=>$identifier.'_', 'name'=>'' );
				
				if ( $identifier_suffix ) {
					if ( $hasIdentifier ) {
						$inherit['name']	=	$identifier.'['.$identifier_suffix.']'.'['.$identifier_name.']';
					} else {
						$inherit['name']	=	$identifier_suffix.'['.$identifier_name.'][]';
					}
					$inherit['id']			.=	$identifier_suffix.'_';
				} else {
					if ( $hasIdentifier ) {
						$inherit['name']	=	$identifier.'['.$identifier_name.']';
					} else {
						$inherit['name']	=	$identifier_name.'[]';
					}
				}
				$inherit['id']		.=	$identifier_name;
				
				if ( $typo->get( 'trigger' ) ) {
					$field->attributes	.=	' onchange="if(!document.getElementById(\'cb'.($i - 1).'\').checked){document.getElementById(\'cb'.($i - 1).'\').checked=true; Joomla.isChecked(document.getElementById(\'cb'.($i - 1).'\').checked, document.getElementById(\''.( @$config['formId'] ? $config['formId'] : 'seblod_form' ).'\'));}"';
				}
				$field->attributes	.=	' data-cck-remove-before-search=""';
				$field->css			=	trim( $field->css.' '.$class );
				$field->label2		=	( $field->label != '' ) ? $field->label : 'clear';
				
				if ( $type == 'form_disabled' ) {
					$field->variation	=	'disabled';
				} elseif ( $type == 'form_hidden' ) {
					$field->variation	=	'hidden';
				}
				JEventDispatcher::getInstance()->trigger( 'onCCK_FieldPrepareForm', array( &$field, $field->value, &$config, $inherit ) );
				$field->form		=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldRenderForm', array( $field, &$config ) );
				$value				=	$field->form;

				$config['formWrapper']	=	true;
				break;
			case 'increment':
				$identifier_name	=	$typo->get( 'identifier_name', '' );

				if ( $identifier_name != '' ) {
					if ( !isset( self::$increment[$identifier_name] ) ) {
						self::$increment[$identifier_name]	=	array( 'i'=>0, 'pks'=>array() );
					}
					if ( !isset( self::$increment[$identifier_name]['pks'][$config['pk']] ) ) {
						self::$increment[$identifier_name]['pks'][$config['pk']]	=	self::$increment[$identifier_name]['i'];
						self::$increment[$identifier_name]['i']++;
					}
				}
				$value		=	( !$start ) ? $i - 1 : $i;
				break;
			case 'selection':
				if ( !$formId ) {
					$formId	=	( @$config['formId'] != '' ) ? $config['formId'] : 'seblod_form';
				}
				$value		=	JHtml::_( 'grid.id', $pks[$pk], $value );
				$value		=	str_replace( ' />', ' data-cck-remove-before-search="" />', $value );
				if ( $typo->get( 'trigger' ) ) {
					$value	=	str_replace( 'this.checked);"', 'this.checked, document.getElementById(\''.$formId.'\')); jQuery(\'#boxchecked\').trigger(\'change\');"', $value );
				} else {					
					$value	=	str_replace( 'this.checked', 'this.checked, document.getElementById(\''.$formId.'\')', $value );
				}

				$config['formWrapper']	=	true;
				break;
			case 'selection_label':
				$value		=	'<label for="cb'.$pks[$pk].'">'.$value.'</label>';
				break;
			case 'sort':
				$parentId		=	$config['parent_id'];
				static $orders	=	array();

				if ( !isset( $orders[$parentId] ) ) {
					$orders[$parentId]	=	0;
				}
				$orders[$parentId]++;
				$order			=	$orders[$parentId];

				static $loaded 	= 	false;
				$listDir		=	'asc';

				if ( !$loaded ) {
					if ( ( isset( $field->state ) && $field->state ) || !isset( $field->state ) ) {
						$app			=	JFactory::getApplication();
						$formId			=	( @$config['formId'] != '' ) ? $config['formId'] : 'seblod_form';
						$tableWrapper	=	$formId . ' table.table';
						$saveOrderUrl	=	JRoute::_( 'index.php?option=com_cck&task=saveOrderAjax&tmpl=component', false );
						JHtml::_( 'sortablelist.sortable', $tableWrapper, $formId, $listDir, $saveOrderUrl, false, true );
						$loaded			= 	true;
					}
				}

				$value 	= 	'<span class="sortable-handler">'
						.	'<span class="icon-menu"></span>'
						.	'<input type="text" style="display:none" name="order[]" size="5" value="'.$order.'" data-cck-remove-before-search="" />'
						.	'</span>';

				$config['formWrapper']	=	true;
				break;
			case 'state':
				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'type'=>$type, 'value'=>$field->value, 'class'=>$class, 'pk'=>$pks[$pk], 'title'=>$typo->get( 'state_title', '' ), 'fieldname_up'=>$typo->get( 'state_up', '' ), 'fieldname_down'=>$typo->get( 'state_down', '' ) ) );

				$config['formWrapper']	=	true;
				break;
			default:
				break;
		}
		
		return $value;
	}

	// getStaticValue
	public static function getStaticValue( $identifier, $pk = 0 )
	{
		if ( $pk ) {
			return ( isset( self::$increment[$identifier] ) ) ? self::$increment[$identifier]['pks'][$pk] : 0;
		} else {
			return ( isset( self::$increment[$identifier] ) ) ? self::$increment[$identifier]['i'] : 0;
		}
	}

	// onCCK_Field_TypoBeforeRenderContent
	public static function onCCK_Field_TypoBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		$type	=	$process['type'];

		if ( !$name ) {
			return;
		}

		if ( $type == 'state' ) {
			$class				=	$process['class'];
			$field_name_up		=	$process['fieldname_up'];
			$field_name_down	=	$process['fieldname_down'];

			if ( $field_name_up || $field_name_down ) {
				$state_up		=	( $field_name_up != '' && isset( $fields[$field_name_up] ) ) ? $fields[$field_name_up]->value : '';
				$state_up		=	( $state_up == '' ) ? '0000-00-00 00:00:00' : $state_up;
				$state_down		=	( $field_name_down != '' && isset( $fields[$field_name_down] ) ) ? $fields[$field_name_down]->value : '';
				$state_down		=	( $state_down == '' ) ? '0000-00-00 00:00:00' : $state_down;

				$value			=	JHtml::_( 'jgrid.published', $process['value'], $process['pk'], '', false /*$canChange*/, 'cb', $state_up, $state_down );
			} else {
				$value			=	JHtml::_( 'jgrid.published', $process['value'], $process['pk'], '', false /*$canChange*/, 'cb', '', '' );
			}
			if ( $fields[$name]->link ) {
				$hasLink		=	true;
				$value			=	str_replace( '<a ', '<a href="'.$fields[$name]->link.'"', $value );
			} else {
				$hasLink		=	false;
			}
			if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
				if ( !$hasLink ) {
					$class	.=	' disabled';
				}
				if ( $hasLink && isset( $fields[$name]->link_title ) && $fields[$name]->link_title ) {
					$value	=	preg_replace( '#title=".*"#U', 'title="'.$fields[$name]->link_title.'"', $value );
				} elseif ( $process['title'] === '0' ) {
					$output	=	JCckField::getInstance( $name );
					$output->loadValue( $process['value'] );
					$value	=	preg_replace( '#title=".*"#U', 'title="'.$output->getText().'"', $value );
				}
				$value		=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
			}

			$fields[$name]->typo	=	$value;
		} elseif ( $type == 'featured' ) {
			if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_content/helpers/html/contentadministrator.php' ) ) {
				$fields[$name]->typo	=	$fields[$name]->text ? $fields[$name]->text : $process['value'];
			} else {
				$class		=	$process['class'];
				$value		=	JHtml::_( 'contentadministrator.featured', $process['value'], $process['pk'], false /*$canChange*/ );

				if ( $fields[$name]->link ) {
					$hasLink		=	true;
					$value			=	str_replace( '<a ', '<a href="'.$fields[$name]->link.'"', $value );
				} else {
					$hasLink		=	false;
				}
				if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					if ( !$hasLink ) {
						$class	.=	' disabled';
					}
					if ( $hasLink && isset( $fields[$name]->link_title ) && $fields[$name]->link_title ) {
						$value	=	preg_replace( '#title=".*"#U', 'title="'.$fields[$name]->link_title.'"', $value );
					}
					$value		=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" title#U', 'class="'.$class.'" title', $value );
				}
				$fields[$name]->typo	=	$value;
			}
		} elseif ( $type == 'block' || $type == 'activation' ) {
			static $loaded_users	=	0;
			static $user			=	NULL;
			if ( !$loaded_users ) {
				require_once JPATH_ADMINISTRATOR.'/components/com_users/helpers/html/users.php';
				$loaded_users		=	1;
				$user				=	JFactory::getUser();
			}
			$class		=	$process['class'];

			if ( $type == 'activation' ) {
				$activated	=	empty( $fields[$name]->value ) ? 0 : 1;
				$title		=	( $activated == 0 ) ? 'COM_CCK_ACTIVATED' : 'COM_CCK_UNACTIVATED';
				$value		=	JHtml::_('jgrid.state', JHtmlUsers::activateStates(), $activated, $process['pk_i'], 'users.', false /*(boolean)$activated*/ );

				if ( $fields[$name]->link && $activated ) {
					$hasLink		=	true;
					$value			=	str_replace( '<a ', '<a href="'.$fields[$name]->link.'"', $value );
				} else {
					$hasLink		=	false;
				}
				
				// if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					// $class	.=	' disabled';
					// $value	=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
				// }
				if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					if ( !$hasLink ) {
						$class	.=	' disabled';
					} else {
						if ( $activated ) {
							$class	=	str_replace( ' disabled', '', $class );
						}
					}
					if ( $hasLink && isset( $fields[$name]->link_title ) && $fields[$name]->link_title ) {
						$value	=	preg_replace( '#title=".*"#U', 'title="'.$fields[$name]->link_title.'"', $value );
					} else {
						$value	=	str_replace( array( 'title=""', 'title="COM_USERS_ACTIVATED"' ), 'title="'.JText::_( $title ).'"', $value );
					}
					$value		=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
				}
				/*
				$value		=	str_replace( 'return listItemTask(', 'return JCck.Core.doTask(', $value );
				$value		=	str_replace( '\'users.activate\'', '\'update.activate\', document.getElementById(\''.$formId.'\')', $value );
				*/
				$fields[$name]->typo	=	$value;
			} else {
				$value		=	$fields[$name]->value;
				$self		=	$user->id == $process['pk'];
				$title		=	( $value == 1 ) ? 'COM_CCK_BLOCKED' : 'COM_CCK_ENABLED';
				$value		=	JHtml::_( 'jgrid.state', JHtmlUsers::blockStates(), $value, $process['pk_i'], 'users.', false /*!$self*/ );

				if ( $fields[$name]->link ) {
					$hasLink		=	true;
					$value			=	str_replace( '<a ', '<a href="'.$fields[$name]->link.'"', $value );
				} else {
					$hasLink		=	false;
				}
				// if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					// $class	.=	' disabled';
					// $value	=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
				// }
				if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					if ( !$hasLink ) {
						$class	.=	' disabled';
					} else {
						$class	=	str_replace( ' disabled', '', $class );
					}
					if ( $hasLink && isset( $fields[$name]->link_title ) && $fields[$name]->link_title ) {
						$value	=	preg_replace( '#title=".*"#U', 'title="'.$fields[$name]->link_title.'"', $value );
					} else {
						$value	=	str_replace( 'title=""', 'title="'.JText::_( $title ).'"', $value );
					}
					$value		=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
				}
				/*
				$value		=	str_replace( 'return listItemTask(', 'return JCck.listItemTask(', $value );
				$value	=	str_replace( '\'users.block\'', '\'update.block\', document.getElementById(\''.$formId.'\')', $value );
				$value	=	str_replace( '\'users.unblock\'', '\'update.unblock\', document.getElementById(\''.$formId.'\')', $value );
				*/
				$fields[$name]->typo	=	$value;
			}
		} elseif ( $type == 'dropdown' ) {
			$target	=	$process['target'];

			if ( $fields[$target]->display ) {
				$fields[$name]->typo	=	str_replace( '<ul class="dropdown-menu flex-column-reverse">', '<ul class="dropdown-menu flex-column-reverse">'.$process['html'], $fields[$name]->typo );
			}
		}
	}
}
?>
