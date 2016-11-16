<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
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
				static $loaded_users	=	0;
				static $user			=	NULL;
				if ( !$loaded_users ) {
					require_once JPATH_ADMINISTRATOR.'/components/com_users/helpers/html/users.php';
					$loaded_users		=	1;
					$user				=	JFactory::getUser();
				}
				if ( $type == 'activation' ) {
					$activated	=	empty( $value ) ? 0 : 1;
					$title		=	( $activated == 0 ) ? 'COM_CCK_ACTIVATED' : 'COM_CCK_UNACTIVATED';
					$value		=	JHtml::_('jgrid.state', JHtmlUsers::activateStates(), $activated, $pks[$pk], 'users.', false /*(boolean)$activated*/ );
					$value		=	str_replace( array( 'title=""', 'title="COM_USERS_ACTIVATED"' ), 'title="'.JText::_( $title ).'"', $value );
						
					if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
						$class	.=	' disabled';
						$value	=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
					}
					/*
					$value		=	str_replace( 'return listItemTask(', 'return JCck.Core.doTask(', $value );
					$value		=	str_replace( '\'users.activate\'', '\'update.activate\', document.getElementById(\''.$formId.'\')', $value );
					*/
				} else {
					$value		=	$field->value;
					$self		=	$user->id == $pk;
					$title		=	( $value == 1 ) ? 'COM_CCK_DISABLED' : 'COM_CCK_ENABLED';
					$value		=	JHtml::_( 'jgrid.state', JHtmlUsers::blockStates(), $value, $pks[$pk], 'users.', false /*!$self*/ );
					$value		=	str_replace( 'title=""', 'title="'.JText::_( $title ).'"', $value );

					if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
						$class	.=	' disabled';
						$value	=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
					}
					/*
					$value		=	str_replace( 'return listItemTask(', 'return JCck.listItemTask(', $value );
					$value	=	str_replace( '\'users.block\'', '\'update.block\', document.getElementById(\''.$formId.'\')', $value );
					$value	=	str_replace( '\'users.unblock\'', '\'update.unblock\', document.getElementById(\''.$formId.'\')', $value );
					*/
				}
				break;
			case 'dropdown':
				$class	=	$typo->get( 'class1', '' );
				$class	=	$class ? ' '.$class : '';
				$value	=	'<button data-toggle="dropdown" class="dropdown-toggle btn'.$class.'"><span class="caret"></span></button>'
						.	'<ul class="dropdown-menu">'
						.	'<li>'
						.	( ( isset( $field->html ) && $field->html ) ? $field->html : $value )
						.	'</li>'
						.	'</ul>';
				break;
			case 'featured':
				static $loadedF = 0;
				if ( !$loadedF ) {
					JHtml::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_content/helpers/html' );
					$loadedF	=	1;
				}
				$value		=	JHtml::_( 'contentadministrator.featured', $field->value, $pks[$pk], false /*$canChange*/ );

				if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					$class	.=	' disabled';
					$value	=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
				}
				break;
			case 'form':
			case 'form_disabled':
			case 'form_hidden':
				$class			=	$typo->get( 'class2', '' );
				if ( !isset( $config['doValidation'] ) ) {
					$config['doValidation']	=	0;
				}
				$dispatcher			=	JDispatcher::getInstance();
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
					$field->attributes	.=	'onchange="if(!document.getElementById(\'cb'.($i - 1).'\').checked){document.getElementById(\'cb'.($i - 1).'\').checked=true; Joomla.isChecked(document.getElementById(\'cb'.($i - 1).'\').checked, document.getElementById(\''.( @$config['formId'] ? $config['formId'] : 'seblod_form' ).'\'));}"';
				}				
				$field->css			=	trim( $field->css.' '.$class );
				$field->label2		=	( $field->label != '' ) ? $field->label : 'clear';
				
				if ( $type == 'form_disabled' ) {
					$field->variation	=	'disabled';
				} elseif ( $type == 'form_hidden' ) {
					$field->variation	=	'hidden';
				}
				$dispatcher->trigger( 'onCCK_FieldPrepareForm', array( &$field, $field->value, &$config, $inherit ) );
				$field->form		=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldRenderForm', array( $field, &$config ) );
				$value				=	$field->form;
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
				if ( $typo->get( 'trigger' ) ) {
					$value	=	str_replace( 'this.checked);"', 'this.checked, document.getElementById(\''.$formId.'\')); jQuery(\'#boxchecked\').trigger(\'change\');"', $value );
				} else {					
					$value	=	str_replace( 'this.checked', 'this.checked, document.getElementById(\''.$formId.'\')', $value );
				}
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
						.	'<i class="icon-menu"></i>'
						.	'<input type="text" style="display:none" name="order[]" size="5" value="'.$order.'" />'
						.	'</span>';
				break;
			case 'state':
				$value		=	JHtml::_( 'jgrid.published', $field->value, $pks[$pk], '', false /*$canChange*/, 'cb', '' /*$item->publish_up*/, '' /*$item->publish_down*/ );
				
				if ( !( $class == '' || $class == 'btn btn-micro hasTooltip' ) ) {
					$class	.=	' disabled';
					$value	=	preg_replace( '#class="[a-zA-Z0-9\-\ ]*" #U', 'class="'.$class.'"', $value );
				}
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
}
?>
