<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: fields.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

// Model
class CCKModelFields extends JModelList
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'title', 'a.title',
				'name', 'a.name',
				'folder', 'a.folder', 'folder_title',
				'type', 'a.type',
				'storage', 'a.storage',
				'storage_table', 'a.storage_table',
				'storage_field', 'a.storage_field',
				'storage_field2', 'a.storage_field2',
				'published', 'a.published',
				'ordering', 'a.ordering',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
			);
		}

		parent::__construct( $config );
	}
	
	// getItems
	public function getItems()
	{
		if ( $items = parent::getItems() ) {
		}
		
		return $items;
	}
	
	// getListQuery
	protected function getListQuery()
	{
		$app	=	JFactory::getApplication();
		$db		=	$this->getDbo();
		$query	=	$db->getQuery( true );	
		
		// Select
		$query->select (
			$this->getState (
				'list.select',
				'a.id as id,' .
				'a.title as title,' .
				'a.name as name,' .
				'a.folder as folder,' .
				'a.type as type,' .
				'a.storage as storage,' .
				'a.storage_table as storage_table,' .
				'a.storage_field as storage_field,' .
				'a.storage_field2 as storage_field2,' .
				'a.published as published,' .
				'a.ordering as ordering,' .
				'a.checked_out as checked_out,' .
				'a.checked_out_time as checked_out_time'
			)
		);

		// From
		$query->from( '`#__cck_core_fields` AS a' );
		
		// Join Folder
		$query->select( 'c.title AS folder_title, c.color AS folder_color, c.introchar AS folder_introchar, c.colorchar AS folder_colorchar' );
		$query->join( 'LEFT', '#__cck_core_folders AS c ON c.id = a.folder' );		
		$query->select( 'd.title AS folder_parent_title, d.id AS folder_parent' );
		$query->join( 'LEFT', '#__cck_core_folders AS d ON d.id = c.parent_id' );

		// Join User
		$query->select( 'u.name AS editor' );
		$query->join( 'LEFT', '#__users AS u ON u.id = a.checked_out' );
		
		// Filder Folder
		$folderId	=	$this->getState( 'filter.folder' );
		if ( is_numeric( $folderId ) ) {
			$folders	=	Helper_Folder::getBranch( $folderId, ',' );
			if ( $folders ) {
				$query->where( 'a.folder IN ('.$folders.')' );
			}
		}
		
		// Filter State
		$published	=	$this->getState( 'filter.state', 1 );
		if ( is_numeric( $published ) && $published >= 0 ) {
			$query->where( 'a.published = '.(int)$published );
		}
		$query->where( 'a.published != -44' );
		
		// Filter Type
		$type	=	trim( $this->getState( 'filter.type' ) );
		if ( is_string( $type ) && $type != '' ) {
			$query->where( 'a.type = "'.(string)$type.'"' );
		}
		
		// Filter Search
		if ( ( $folder = $app->input->getInt( 'folder_id', 0 ) ) > 0 ) {
			$location	=	'folder_id';
			$search		=	$folder;
				
			$this->setState( 'filter.location', $location );
			$this->setState( 'filter.search', $search );
		} else {
			$location	=	$this->getState( 'filter.location' );
			$search		=	$this->getState( 'filter.search' );
		}
		if ( ! empty( $search ) ) {
			switch ( $location ) {
				case 'id':
					$where	=	( strpos( $search, ',' ) !== false ) ? 'a.id IN ('.$search.')' : 'a.id = '.(int)$search;
					$query->where( $where );
					break;
				case 'folder_id':
					$where	=	( strpos( $search, ',' ) !== false ) ? 'a.folder IN ('.$search.')' : 'a.folder = '.(int)$search;
					$query->where( $where );
					break;
				default:
					$search	=	$db->quote( '%'.$db->escape( $search, true ).'%' );
					$query->where( 'a.'.$location.' LIKE '.$search );
					break;
			}
		}

		// Group By
		$query->group( 'a.id' );	
		
		// Order By
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'a.title' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.folder' );
		$id	.=	':' . $this->getState( 'filter.state' );
		$id	.=	':' . $this->getState( 'filter.type' );
		
		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Field', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}

	// populateState
	protected function populateState( $ordering = null, $direction = null )
	{
		$app	=	JFactory::getApplication( 'administrator' );

		$folder	=	$app->input->getInt( 'folder_id', 0 );
		if ( $folder > 0 ) {
			$search		=	$folder;
			$location	=	'folder_id';
		} else {
			$search		=	$app->getUserStateFromRequest( $this->context.'.filter.search', 'filter_search', '' );
			$location	=	$app->getUserStateFromRequest( $this->context.'.filter.location', 'filter_location', 'title' );
		}
		$this->setState( 'filter.search', $search );
		$this->setState( 'filter.location', $location );
		
		$folderId	=	$app->getUserStateFromRequest( $this->context.'.filter.folder', 'filter_folder', '' );
		$this->setState( 'filter.folder', $folderId );

		$state		=	$app->getUserStateFromRequest( $this->context.'.filter.state', 'filter_state', '1', 'string' );
		$this->setState( 'filter.state', $state );

		$type		=	$app->getUserStateFromRequest( $this->context.'.filter.type', 'filter_type', '', 'string' );
		$this->setState( 'filter.type', $type);

		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'a.title', 'asc' );
	}
}
?>