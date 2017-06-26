<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: search.php sebastienheraud $
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
class CCKModelSearch extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'search';
	
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
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.search.content_type' ) ) {
				$this->setState( 'content_type', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.search.tpl_search' ) ) {
				$this->setState( 'tpl.search', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.search.tpl_filter' ) ) {
				$this->setState( 'tpl.filter', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.search.tpl_list' ) ) {
				$this->setState( 'tpl.list', $tpl );
			}
			if ( $tpl	=	(string)$app->getUserState( CCK_COM.'.add.search.tpl_item' ) ) {
				$this->setState( 'tpl.item', $tpl );
			}
			if ( $skip	=	(string)$app->getUserState( CCK_COM.'.add.search.skip' ) ) {
				$this->setState( 'skip', $skip );
			}
		}
		if ( $client	=	(string)$app->getUserState( CCK_COM.'.edit.search.client' ) ) {
			$this->setState( 'client', $client );
		}
		
		$this->setState( 'search.id', $pk );
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
	public function getTable( $type = 'Search', $prefix = CCK_TABLE, $config = array() )
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$data					=	JRequest::get( 'post' );
		$data['description']	=	JRequest::getVar( 'description', '', '', 'string', JREQUEST_ALLOWRAW );
		$client					=	$data['client'];
		$P						=	'template_'.$client;
		$data[$P]				=	Helper_Workshop::getTemplateStyleInstance( $data[$P], $data['template'], $data['template2'], $data['params'], $data['name'].' ('.$client.')' );
		$data['options']		=	JCckDev::toJSON( @$data['options'] );
		
		if ( ! $data['id'] ) {
			$clients			=	array( 'search', 'filter', 'item' );
			foreach ( $clients as $c ) {
				$P				=	'template_'.$c;
				if ( ! $data[$P] ) {
					$default	=	Helper_Workshop::getDefaultStyle();
					$data[$P]	=	$default->id;
				}
			}
			if ( isset( $data['tpl_list'] ) && $data['tpl_list'] ) {
				$default				=	Helper_Workshop::getDefaultStyle( $data['tpl_list'] );
				$data['template_list']	=	$default->id;
				$data['content']		=	1;
				unset( $data['tpl_list'] );
			}
		} else {
			$doVersion	=	JCck::getConfig_Param( 'version_auto', 2 );
			if ( $doVersion == 1 || ( $doVersion == 2 && Helper_Version::checkLatest( 'search', $data['id'] ) === true ) ) {
				Helper_Version::createVersion( 'search', $data['id'] );

				if ( JCck::getConfig_Param( 'version_remove', 1 ) ) {
					Helper_Version::removeVersion( 'search', $data['id'] );
				}
			}
		}
		if ( $client == 'list' ) {
			$data['content']	=	( $data[$P] > 0 ) ? 1 : 0;
		}
		
		return $data;
	}
	
	// postStore
	public function postStore( $pk )
	{
		$data	=	JRequest::get( 'post' );
		$client	=	$data['client'];
		
		if ( isset( $data['li_end'] ) && $data['li_end'] == '1' ) {
			$this->storeMore( $pk, $data['client'], $data['ff'], $data['ffp'] );
			if ( isset( $data['cck_type'] ) && $data['cck_type'] != '' ) {
				$data['ff2']	=	array( 'cck'=>1 );
				$data['ffp2']	=	array( 'cck'=>array(
													'access'=>'1',
													'conditional'=>'',
													'conditional_options'=>'',
													'label'=>'Type',
													'label2'=>'Type',
													'live'=>'',
													'live_options'=>'',
													'live_value'=>$data['cck_type'],
													'match_collection'=>'',
													'match_mode'=>'exact',
													'match_value'=>'',
													'required'=>'',
													'required_alert'=>'',
													'stage'=>'0',
													'validation'=>'',
													'validation_options'=>'',
													'variation'=>'hidden',
													'variation_override'=>''
												  )
									);
				$this->storeMore( $pk, 'search', $data['ff2'], $data['ffp2'] );
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
			$item->link					=	'index.php?option=com_cck&view=list&search='.$data['name'].'&task=search';
			$item->params				=	'{}';
			$item->type					=	'component';
			
			$item->access				=	$data['access'];
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
	protected function storeMore( $searchId, $client, $fields, $params )
	{
		$db		=	JFactory::getDbo();
		jimport( 'cck.construction.field.generic_more' );
		$table	=	'search_field';
		$method	=	'gm_getConstruction_Values_Search';
		
		JCckDatabase::execute( 'DELETE FROM #__cck_core_'.$table.' WHERE searchid = '.(int)$searchId . ' AND client = "'.$client.'"' );
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
					$css				=	( @$params[$k]['css'] != '' ) ? $params[$k]['css'] : '';
					$position			=	substr( $k, 4 );
					if ( $next != 'position' ) {
						$positions	.=	', ( '.(int)$searchId.', "'.(string)$position.'", "'.$client.'", "'.$legend.'", "'.$variation.'", "'.$variation_options.'", "'.$width.'", "'.$height.'", "'.$css.'" )';
					}
				} else {
					$assigned	.= ', ( '.(int)$searchId.', '.(int)$v.', "'.$client.'", '.$ordering.', '.plgCCK_FieldGeneric_More::$method( $k, $params, $position, $client ).' )';
					$ordering++;
				}
			}
			if ( $assigned ) {
				$assigned	=	substr( $assigned, 1 );
				JCckDatabase::execute( 'INSERT INTO #__cck_core_'.$table.' ( searchid, fieldid, client, ordering, '.plgCCK_FieldGeneric_More::gm_getConstruction_Columns( $table ).' ) VALUES ' . $assigned );
			}
			if ( $positions ) {
				$positions	=	substr( $positions, 1 );
				JCckDatabase::execute( 'DELETE FROM #__cck_core_search_position WHERE searchid = '.(int)$searchId . ' AND client = "'.$client.'"' );
				JCckDatabase::execute( 'INSERT INTO #__cck_core_search_position ( searchid, position, client, legend, variation, variation_options, width, height, css ) VALUES ' . $positions );
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
			$clients		=	array( 'search', 'filter', 'list', 'item' );
			$styles			=	JCckDatabase::loadObjectList( 'SELECT * FROM #__template_styles WHERE id IN ('.(int)$table->template_search.','
																											  .(int)$table->template_filter.','
																											  .(int)$table->template_list.','
																											  .(int)$table->template_item.')', 'id' );
			foreach ( $clients as $client ) {
				$property	=	'template_'.$client;
				$style		=	$styles[$table->$property];
				if ( !( is_object( $style ) && $style->id ) ) {
					if ( $client == 'list' ) {
						$style	=	Helper_Workshop::getDefaultStyle( 'seb_table' );
					} else {
						$style	=	Helper_Workshop::getDefaultStyle();
					}
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
			$query	=	'SELECT a.*, b.storage_table FROM #__cck_core_search_field AS a LEFT JOIN #__cck_core_fields AS b ON b.id = a.fieldid WHERE a.searchid = '.(int)$pk;
			
			$this->_table_no_key_batch( 'query', $query, '#__cck_core_search_field', 'searchid', $table->id, array( 'storage_table' ) );
			
			// Positions			
			$this->_table_no_key_batch( 'where', 'searchid = '.(int)$pk, '#__cck_core_search_position', 'searchid', $table->id );
		}
	}
	
	// version
	public function version( $pks )
	{
		foreach ( $pks as $pk ) {
			Helper_Version::createVersion( 'search', $pk, '', true );

			if ( JCck::getConfig_Param( 'version_remove', 1 ) ) {
				Helper_Version::removeVersion( 'search', $pk );
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
}
?>
