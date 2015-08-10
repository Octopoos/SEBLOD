<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldJoomla_Article extends JCckPluginField
{
	protected static $type			=	'joomla_article';
	protected static $convertible	=	1;
	protected static $friendly		=	1;
	protected static $table			=	'#__content';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
		
		$data['divider']	=	'';
		$prefix				=	JFactory::getConfig()->get( 'dbprefix' );
		$table				=	'cck_store_join_'.( ( $data['storage_field2'] != '' ) ? $data['storage_field2'] : $data['storage_field'] );
		JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$prefix.$table.' ( `id` int(11) NOT NULL, `id2` int(11) NOT NULL, PRIMARY KEY (`id`,`id2`) )'
							 . ' ENGINE=InnoDB DEFAULT CHARSET=utf8;' );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Delete

	// onCCK_FieldDelete
	public function onCCK_FieldDelete( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		if ( $value == '' ) {
			return;
		}

		// Process
		$table	=	'#__cck_store_join_'.( $field->storage_field2 ? $field->storage_field2 : $field->storage_field );
		if ( JCckDatabase::execute( 'DELETE a.* FROM '.$table.' AS a WHERE a.id = '.(int)$config['pk'] ) ) {
			return true;
		}

		return false;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		$app	=	JFactory::getApplication();
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		$text	=	'';
		if ( $field->extended && $field->extended != @$config['type'] ) {
			$options2	=	JCckDev::fromJSON( $field->options2 );
			$name		=	$field->storage_field2 ? $field->storage_field2 : $field->storage_field;
			$value		=	array();
			if ( @$options2['child_language'] ) {
				$language	=	( $options2['child_language'] == '-1' ) ? JFactory::getLanguage()->getTag() : $options2['child_language'];
				$language	=	' AND a.language = "'.$language.'"';
			} else {
				$language	=	'';
			}
			$location	=	( @$options2['child_location'] ) ? $options2['child_location'] : 'joomla_article';
			$order		=	( @$options2['child_orderby'] ) ? ' ORDER BY a.'.$options2['child_orderby'].' '.$options2['child_orderby_direction'] : ' ORDER BY a.title ASC';
			$limit		=	( @$options2['child_limit'] ) ? ' LIMIT '.$options2['child_limit'] : '';
			switch ( $field->bool2 ) {
				case 2:
					$properties	=	array( 'table', 'access', 'custom', 'status' );
					$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );
					$and		=	( $properties['status'] ) ? ' AND a.'.$properties['status'].' = 1' : '';
					$and		.=	( $properties['access'] ) ? ' AND a.'.$properties['access'].' IN ('.$access.')' : '';
					$items		=	JCckDatabase::loadObjectList( 'SELECT a.id as pk, a.'.$properties['custom'].' FROM '.$properties['table']
																. ' AS a LEFT JOIN #__cck_store_join_'.$name.' AS b on b.id = a.id'
																. ' WHERE b.id2 = '.(int)$config['pk'].$and.$language.$order.$limit );
					if ( count( $items ) ) {
						foreach ( $items as $item ) {
							$text		.=	JHtml::_( 'content.prepare', $item->$properties['custom'] );
							$value[]	=	$item->pk;
						}
					}
					break;
				case 1:
					$properties	=	array( 'table', 'access', 'status' );
					$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );
					$and		=	( $properties['status'] ) ? ' AND a.'.$properties['status'].' = 1' : '';
					$and		.=	( $properties['access'] ) ? ' AND a.'.$properties['access'].' IN ('.$access.')' : '';
					$items		=	JCckDatabase::loadObjectList( 'SELECT a.id as pk, a.title FROM '.$properties['table']
																. ' AS a LEFT JOIN #__cck_store_join_'.$name.' AS b ON b.id = a.id'
																. ' WHERE b.id2 = '.(int)$config['pk'].$and.$language.$order.$limit );
					if ( count ( $items ) ) {
						foreach ( $items as $item ) {
							$text		.=	', ' . $item->title;
							$value[]	=	$item->pk;
						}
					}
					if ( $text ) {
						$text	=	substr( $text, 2 );
					}
					break;
				default:
					$options2		=	new JRegistry;
					$options2->loadString( $field->options2 );
					$options3_json	=	$options2->get( 'child_link_options' );
					$options3		=	new JRegistry;
					$options3->loadString( $options3_json ); // todo >> href
					$properties		=	array( 'table', 'access', 'status', 'to_route' );
					$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$location, 'getStaticProperties', $properties );
					$and			=	( $properties['status'] ) ? ' AND a.'.$properties['status'].' = 1' : '';
					$and			.=	( $properties['access'] ) ? ' AND a.'.$properties['access'].' IN ('.$access.')' : '';
					$items			=	JCckDatabase::loadObjectList( 'SELECT '.$properties['to_route'].' FROM '.$properties['table']
																	. ' AS a LEFT JOIN #__cck_store_join_'.$name.' AS b ON b.id = a.id'
																	. ' WHERE b.id2 = '.(int)$config['pk'].$and.$language.$order.$limit );
					if ( count( $items ) ) {
						$sef	=	( JFactory::getConfig()->get( 'sef' ) ) ? $options3->get( 'sef', 2 ) : 0;
						JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'setRoutes', array( &$items, $sef, $options3->get( 'itemid', $app->input->getInt( 'Itemid', 0 ) ) ) );
						foreach ( $items as $item ) {
							$text		.=	', ' . '<a href="'.$item->link.'">'.$item->title.'</a>';
							$value[]	=	$item->pk;
						}
						if ( $text ) {
							$text		=	substr( $text, 2 );
						}
					}
					break;
			}
			$field->divider	=	',';
			$value			=	implode( $field->divider, $value );
		} else {
			if ( $value ) {
				switch ( $field->bool ) {
					case 2:
						$text		=	JCckDatabase::loadResult( 'SELECT a.introtext FROM '.self::$table.' AS a WHERE a.id = '.(int)$value.' AND a.state = 1 AND a.access IN ('.$access.')' );
						$text		=	JHtml::_( 'content.prepare', $text );
						break;
					case 1:
						$text		=	JCckDatabase::loadResult( 'SELECT a.title FROM '.self::$table.' AS a WHERE a.id = '.(int)$value.' AND a.state = 1 AND a.access IN ('.$access.')' );
						break;
					default:
						require_once ( JPATH_SITE.'/plugins/cck_storage_location/'.self::$type.'/'.self::$type.'.php' );
						$item			=	JCckDatabase::loadObject( 'SELECT a.id, a.title, a.alias, a.catid, a.language FROM '.self::$table.' AS a WHERE a.id = '.(int)$value.' AND a.state = 1 AND a.access IN ('.$access.')' );
						if ( is_object( $item ) ) {
							$options2		=	new JRegistry;
							$options2->loadString( $field->options2 );
							$options3_json	=	$options2->get( 'parent_link_options' );
							$options3		=	new JRegistry;
							$options3->loadString( $options3_json );
							$field2			=	(object)array( 'link'=>'content', 'link_options'=>$options3_json, 'id'=>$field->name, 'name'=>$field->name, 'text'=>htmlspecialchars( $item->title ), 'value'=>'' );
							JCckPluginLink::g_setLink( $field2, $config );
							$field2->link	=	plgCCK_Storage_LocationJoomla_Article::getRoute( $item, $options3->get( 'sef', 2 ), $options3->get( 'itemid', $app->input->getInt( 'Itemid', 0 ) ) );
							JCckPluginLink::g_setHtml( $field2, 'text' );
							$text			=	$field2->html;
							$field->link	=	$field2->link;
							$field->html	=	$field2->html;
						}
						break;
				}
			}
		}
		
		$field->value		=	$value;
		$field->text		=	$text;
		$field->typo_target	=	'text';
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
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare	
		$opts		=	array();
		if ( trim( $field->selectlabel ) ) {
			if ( $config['doTranslation'] ) {
				$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
			}
			$opts[]	=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', 'value', 'text' );
		}
		$options	=	'"'.str_replace( '||', '","', $field->options ).'"';
		$opts2		=	JCckDatabase::loadObjectList( 'SELECT title AS text, id AS value FROM '.self::$table.' WHERE catid IN('.$options.') AND state = 1 ORDER BY title', 'value' );
		if ( count( $opts2 ) ) {
			$opts	=	array_merge( $opts, $opts2 );
		}
		
		$class	=	'inputbox select'.$validate . ( $field->css ? ' '.$field->css : '' );
		$attr	=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	( count( $opts ) ) ? JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id ) : '';
				
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$field->text	=	isset( $opts2[$value]->text ) ? $opts2[$value]->text : $value;
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<select', '', '', $config );
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
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Set
		$field->match_value	=	$field->match_value ? $field->match_value : ',';
		$field->value		=	$value;
		
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
		
		if ( $field->state != 'disabled' ) {
			parent::g_addProcess( 'afterStore', self::$type, $config, array( 'name'=>( $field->storage_field2 ? $field->storage_field2 : $field->storage_field ), 'value'=>$value ) );
		}
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		if ( $value > 0 ) {
			$field->text	=	JCckDatabase::loadResult( 'SELECT title FROM '.self::$table.' WHERE id ='.(int)$value );
		}
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldAfterStore
	public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
	{
		$table	=	'#__cck_store_join_'.$process['name'];
		$value	=	$process['value'];
		
		static $bool = 1;
		if ( $bool == 1 ) {
			$bool	=	0;
			JCckDatabase::execute( 'DELETE a.* FROM '.$table.' AS a WHERE a.id = '.(int)$config['pk'] );
		}
		if ( $value > 0 ) {
			JCckDatabase::execute( 'INSERT IGNORE INTO '.$table.' VALUES ('.(int)$config['pk'].', '.$value.')' );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
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