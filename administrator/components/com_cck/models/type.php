<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: type.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_COMPONENT.'/helpers/helper_version.php';
require_once JPATH_COMPONENT.'/helpers/helper_workshop.php';

// Model
class CCKModelType extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'type';
	
	// canDelete
	protected function canDelete( $record )
	{
		$user	=	JFactory::getUser();
		
		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.delete', CCK_COM.'.folder.'.(int)$record->folder );
		} else {
			// Component Permissions
			return parent::canDelete( $record );
		}
	}

	// canEditState
	protected function canEditState( $record )
	{
		$user	=	JFactory::getUser();

		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.edit.state', CCK_COM.'.folder.'.(int)$record->folder );
		} else {
			// Component Permissions
			return parent::canEditState( $record );
		}
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication( 'administrator' );
		
		if ( ! ( $pk = $app->input->getInt( 'id', 0 ) ) ) {
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.type.skeleton_id' ) ) {
				$this->setState( 'skeleton_id', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.type.tpl_admin' ) ) {
				$this->setState( 'tpl.admin', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.type.tpl_site' ) ) {
				$this->setState( 'tpl.site', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.type.tpl_content' ) ) {
				$this->setState( 'tpl.content', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.type.tpl_intro' ) ) {
				$this->setState( 'tpl.intro', $tpl );
			}
		} else {
			if ( $client	=	(string)$app->getUserState( CCK_COM.'.edit.type.client' ) ) {
				$this->setState( 'client', $client );
			}
		}
		
		$this->setState( 'type.id', $pk );
	}
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		$form	=	$this->loadForm( CCK_COM.'.'.$this->vName, $this->vName, array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		
		return $form;
	}
	
	// getItem
	public function getItem( $pk = null )
	{
		if ( $item = parent::getItem( $pk ) ) {
			//
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Type', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	JFactory::getApplication()->getUserState( CCK_COM.'.edit.'.$this->vName.'.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// prepareTable2
	protected function prepareTable2( &$table, &$data )
	{
		if ( !$data['jform']['id'] && !$data['jform']['rules'] ) {
			$data['jform']['rules']	=	array( 'core.create'=>array(),
											   'core.create.max.parent'=>array( '8'=>"0" ),
											   'core.create.max.parent.author'=>array( '8'=>"0" ),
											   'core.create.max.author'=>array( '8'=>"0" ),
											   'core.delete'=>array(),
											   'core.delete.own'=>array(),
											   'core.edit'=>array(),
											   'core.edit.own'=>array(),
											   'core.edit.own.content'=>array(),
											   'core.export'=>array(),
											   'core.process'=>array()
										);
		}
		if ( $data['jform']['rules'] ) {
			if ( !is_array( $data['jform']['rules'] ) ) {
				$data['jform']['rules']	=	json_decode( $data['jform']['rules'] );
			}
			jimport( 'cck.joomla.access.access' );
			$rules	=	new CCKRules( JCckDevHelper::getRules( $data['jform']['rules'] ), 'com_cck', 'form' );
			$table->setRules( $rules );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$data					=	JRequest::get( 'post' );
		$data['description']	=	JRequest::getVar( 'description', '', '', 'string', JREQUEST_ALLOWRAW );
		$client					=	$data['client'];
		$P						=	'template_'.$client;
		$data[$P]				=	Helper_Workshop::getTemplateStyleInstance( $data[$P], $data['template'], $data['template2'], $data['params'], $data['name'].' ('.$client.')' );
		$P						=	'options_'.$client;
		$data[$P]				=	JCckDev::toJSON( @$data['options'] );
		
		if ( ! $data['id'] ) {
			$clients			=	array( 'admin', 'site', 'content', 'intro' );
			foreach ( $clients as $client ) {
				$P	=	'template_'.$client;
				if ( ! $data[$P] ) {
					$default	=	Helper_Workshop::getDefaultStyle();
					$data[$P]	=	$default->id;
				}
			}
		} else {
			$doVersion	=	JCck::getConfig_Param( 'version_auto', 2 );
			if ( $doVersion == 1 || ( $doVersion == 2 && Helper_Version::checkLatest( 'type', $data['id'] ) === true ) ) {
				Helper_Version::createVersion( 'type', $data['id'] );

				if ( JCck::getConfig_Param( 'version_remove', 1 ) ) {
					Helper_Version::removeVersion( 'type', $data['id'] );
				}
			}
		}
		
		return $data;
	}
	
	// postStore
	public function postStore( $pk )
	{
		$data	=	JRequest::get( 'post' );
		$client	=	$data['client'];
		
		if ( $data['fromclient'] ) {
			if ( $client == 'site' ) {
				$from 	=	'admin';
			} elseif ( $client == 'admin' ) {
				$from	=	'site';
			} elseif ( $client == 'content' ) {
				$from	=	'intro';
			} elseif ( $client == 'intro' ) {
				$from	=	'content';
			}
			$table	=	JCckTableBatch::getInstance( '#__cck_core_type_field' );
			$table->load( 'typeid = '.$pk.' AND client = "'.$from.'"' );
			$table->check( array( 'client'=>$client ) );
			$table->store();
			
			$table	=	JCckTableBatch::getInstance( '#__cck_core_type_position' );
			$table->load( 'typeid = '.$pk.' AND client = "'.$from.'"' );
			$table->check( array( 'client'=>$client ) );
			$table->store();
		} else {
			if ( isset( $data['li_end'] ) && $data['li_end'] == '1' ) {
				$this->storeMore( $pk, $data['client'], $data['ff'], $data['ffp'] );
			}
		}
		
		if ( isset( $data['quick_menuitem'] ) && $data['quick_menuitem'] ) {
			if ( is_file( JPATH_SITE.'/libraries/joomla/database/table/menu.php' ) ) {
				require_once JPATH_SITE.'/libraries/joomla/database/table/menu.php';
			}
			$quick_item					=	explode( '.', $data['quick_menuitem'] );
			$item						=	JTable::getInstance( 'Menu' );
			$item->id					=	0;
			$item->title				=	$data['title'];
			$item->menutype				=	$quick_item[0];
			$item->parent_id			=	$quick_item[1];
			$item->published			=	1;
			
			$item->component_id			=	JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_cck"' );
			$item->link					=	'index.php?option=com_cck&view=form&layout=edit&type='.$data['name'];
			$item->params				=	'{}';
			$item->type					=	'component';
			
			$item->client_id			=	0;
			$item->home					=	0;
			$item->language				=	'*';
			$item->template_style_id	=	0;
			
			$item->setLocation( $quick_item[1], 'last-child' );
			$item->check();
			$item->store();
		}
	}
	
	// storeMore
	protected function storeMore( $typeId, $client, $fields, $params )
	{
		$db		=	JFactory::getDbo();
		jimport( 'cck.construction.field.generic_more' );
		$table	=	'type_field';
		$method	=	'gm_getConstruction_Values_Type';
		
		JCckDatabase::execute( 'DELETE FROM #__cck_core_'.$table.' WHERE typeid = '.(int)$typeId . ' AND client = "'.$client.'"' );
		if ( count( $fields ) ) {
			$assigned	=	'';
			$ordering	=	1;
			$position	=	'mainbody';
			$positions	=	'';
			foreach ( $fields as $k => $v ) {
				$next	=	next( $fields );
				if ( $v == 'position' ) {
					$legend				=	( @$params[$k]['legend'] != '' ) ? $db->escape( $params[$k]['legend'] ) : '';
					$variation			=	( @$params[$k]['variation'] != '' ) ? $params[$k]['variation'] : '';
					$variation_options	=	( @$params[$k]['variation_options'] != '' ) ? $db->escape( $params[$k]['variation_options'] ) : '';
					$width				=	( @$params[$k]['width'] != '' ) ? $params[$k]['width'] : '';
					$height				=	( @$params[$k]['height'] != '' ) ? $params[$k]['height'] : '';
					$position			=	substr( $k, 4 );
					if ( $next != 'position' ) {
						$positions	.=	', ( '.(int)$typeId.', "'.(string)$position.'", "'.$client.'", "'.$legend.'", "'.$variation.'", "'.$variation_options.'", "'.$width.'", "'.$height.'" )';
					}
				} else {
					$assigned	.= ', ( '.(int)$typeId.', '.(int)$v.', "'.$client.'", '.$ordering.', '.plgCCK_FieldGeneric_More::$method( $k, $params, $position, $client ).' )';
					$ordering++;
				}
			}
			if ( $assigned ) {
				$assigned	=	substr( $assigned, 1 );
				JCckDatabase::execute( 'INSERT INTO #__cck_core_'.$table.' ( typeid, fieldid, client, ordering, '.plgCCK_FieldGeneric_More::gm_getConstruction_Columns( $table ).' ) VALUES ' . $assigned );
			}
			if ( $positions ) {
				$positions	=	substr( $positions, 1 );
				JCckDatabase::execute( 'DELETE FROM #__cck_core_type_position WHERE typeid = '.(int)$typeId . ' AND client = "'.$client.'"' );
				JCckDatabase::execute( 'INSERT INTO #__cck_core_type_position ( typeid, position, client, legend, variation, variation_options, width, height ) VALUES ' . $positions );
			}
		}
	}
	
	// duplicate
	public function duplicate( $pk )
	{
		$app	=	JFactory::getApplication();
		$db		=	$this->getDbo();
		$title	=	$app->input->getString( 'duplicate_title', '' );
		$user	=	JFactory::getUser();

		if ( ! $user->authorise( 'core.create', COM_CCK ) ) {
			throw new Exception( JText::_( 'JERROR_CORE_CREATE_NOT_PERMITTED' ) );
		}

		$table	=	$this->getTable();
		$table->load( $pk, true );
		
		if ( $table->id ) {
			$table->id	=	0;

			if ( $title ) {
				$table->title	=	$title;
			} else {
				$table->title	.=	' (2)';
			}
			$table->name	=	'';
			
			if ( !$table->check() ) {
				throw new Exception( $table->getError() );
			}
			
			$prefix	=	JCck::getConfig_Param( 'development_prefix', '' );
			if ( $prefix ) {
				$table->name	=	$prefix.'_'.$table->name;
			}
			
			// Template Styles
			$clients		=	array( 'admin', 'site', 'content', 'intro' );
			$styles			=	JCckDatabase::loadObjectList( 'SELECT * FROM #__template_styles WHERE id IN ('.(int)$table->template_admin.','
																											  .(int)$table->template_site.','
																											  .(int)$table->template_content.','
																											  .(int)$table->template_intro.')', 'id' );
			foreach ( $clients as $client ) {
				$property	=	'template_'.$client;
				$style		=	$styles[$table->$property];
				if ( !( is_object( $style ) && $style->id ) ) {
					$style	=	Helper_Workshop::getDefaultStyle();
				}
				$table->$property	=	Helper_Workshop::getTemplateStyleInstance( $style->id, $style->template, $style->template, $style->params, $table->name.' ('.$client.')', true );
			}
			
			if ( !$table->store() ) {
				throw new Exception( $table->getError() );
			}
			
			if ( ! $table->id ) {
				return 0;
			}
			
			// Fields
			$query	=	'SELECT a.*, b.storage_table FROM #__cck_core_type_field AS a LEFT JOIN #__cck_core_fields AS b ON b.id = a.fieldid WHERE a.typeid = '.(int)$pk;
			
			$this->_table_no_key_batch( 'query', $query, '#__cck_core_type_field', 'typeid', $table->id, array( 'storage_table' ), '_check_storage' );
			
			// Positions			
			$this->_table_no_key_batch( 'where', 'typeid = '.(int)$pk, '#__cck_core_type_position', 'typeid', $table->id );		
		}
	}
	
	// version
	public function version( $pks )
	{
		foreach ( $pks as $pk ) {
			Helper_Version::createVersion( 'type', $pk, '', true );

			if ( JCck::getConfig_Param( 'version_remove', 1 ) ) {
				Helper_Version::removeVersion( 'type', $pk );
			}
		}
		
		return true;
	}
	
	// _table_no_key_batch
	protected function _table_no_key_batch( $sql_type, $sql, $table, $key, $val, $excluded = array(), $callback = '' )
	{
		$db	=	JFactory::getDbo();
		
		if ( $sql_type == 'where' ) {
			$elems	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.$sql );
		} else {
			$elems	=	JCckDatabase::loadObjectList( $sql );
		}
		$str	=	'';
		
		foreach ( $elems as $elem ) {
			$add	=	1;
			if ( $callback != '' ) {
				$add	=	$this->$callback( $elem );
			}
			if ( $add == 1 ) {
				$str2	=	'';
				foreach ( $elem as $k => $v ) {
					if ( in_array( $k, $excluded ) !== true ) {
						if ( $k == $key ) {
							$v	=	$val;
						}
						$str2	.=	'"'.$db->escape( $v ).'", ';
					}
				}
				if ( $str2 != '' ) {
					$str2	=	substr( trim( $str2 ), 0, -1 );
					$str	.=	'(' . $str2 . '), ';
				}
			}
		}
		if ( $str != '' ) {
			$str	=	substr( trim( $str ), 0, -1 );
		}
		
		JCckDatabase::execute( 'INSERT INTO '.$table.' VALUES '.$str );
	}
	
	// _check_storage
	protected function _check_storage( $elem )
	{
		if ( strpos( $elem->storage_table, '#__cck_store_form_' ) !== false ) {
			return 0;
		}
		
		return 1;
	}
}
?>
