<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folders.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

// Model
class CCKModelFolders extends JModelList
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'title', 'a.title',
				'name', 'a.name',
				'color', 'a.color',
				'introchar', 'a.introchar',
				'colorchar', 'a.colorchar',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'featured', 'a.featured',
				'home', 'a.home',
				'published', 'a.published',
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
			$templates		=	JCckDatabase::loadObjectList( 'SELECT a.folder, COUNT( a.folder ) AS num FROM #__cck_core_templates AS a WHERE a.published != -44 GROUP BY a.folder', 'folder' );
			$types			=	JCckDatabase::loadObjectList( 'SELECT a.folder, COUNT( a.folder ) AS num FROM #__cck_core_types AS a WHERE a.published != -44 GROUP BY a.folder', 'folder' );
			$fields			=	JCckDatabase::loadObjectList( 'SELECT a.folder, COUNT( a.folder ) AS num FROM #__cck_core_fields AS a WHERE a.published != -44 GROUP BY a.folder', 'folder' );
			$searchs		=	JCckDatabase::loadObjectList( 'SELECT a.folder, COUNT( a.folder ) AS num FROM #__cck_core_searchs AS a WHERE a.published != -44 GROUP BY a.folder', 'folder' );
			$processings	=	JCckDatabase::loadObjectList( 'SELECT a.folder, COUNT( a.folder ) AS num FROM #__cck_more_processings AS a WHERE a.published != -44 GROUP BY a.folder', 'folder' );
			
			foreach ( $items as $item ) {
				$item->templates_nb		=	@$templates[$item->id]->num ? $templates[$item->id]->num : 0;
				$item->types_nb			=	@$types[$item->id]->num ? $types[$item->id]->num : 0;
				$item->fields_nb		=	@$fields[$item->id]->num ? $fields[$item->id]->num : 0;
				$item->searchs_nb		=	@$searchs[$item->id]->num ? $searchs[$item->id]->num : 0;
				$item->processings_nb	=	@$processings[$item->id]->num ? $processings[$item->id]->num : 0;
			}
		}
		
		return $items;
	}
	
	// getListQuery
	protected function getListQuery()
	{
		$db		=	$this->getDbo();
		$query	=	$db->getQuery( true );
		
		// Select
		$query->select (
			$this->getState (
				'list.select',
				'a.id as id,' .
				'a.title as title,' .
				'a.name as name,' .
				'a.color as color,' .
				'a.introchar as introchar,' .
				'a.colorchar as colorchar,' .
				'a.depth as depth,' .
				'a.lft as lft,' .
				'a.rgt as rgt,' .
				'a.featured as featured,' .
				'a.home as home,' .
				'a.published as published,' .
				'a.checked_out as checked_out,' .
				'a.checked_out_time as checked_out_time'
			)
		);

		// From
		$query->from( '`#__cck_core_folders` AS a' );

		// Join Parent
		$query->join( 'CROSS', '#__cck_core_folders AS parent' );
		
		// Join User
		$query->select( 'u.name AS editor' );
		$query->join( 'LEFT', '#__users AS u ON u.id = a.checked_out' );

		// Where
		$query->where( 'a.lft BETWEEN parent.lft AND parent.rgt' );

		// Filder Folder
		$folderId	=	$this->getState( 'filter.folder' );
		if ( is_numeric( $folderId ) ) {
			$folders	=	Helper_Folder::getBranch( $folderId, ',' );
			if ( $folders ) {
				$query->where( 'a.id IN ('.$folders.')' );
			}
		}
		
		// Filter State
		$published	=	$this->getState( 'filter.state' );
		if ( is_numeric( $published ) && $published >= 0 ) {
			$query->where( 'a.published = '.(int)$published );
		}
		$query->where( 'a.published != -44' );
		
		// Filter Depth
		$depth	=	$this->getState( 'filter.depth' );
		if ( is_numeric( $depth ) && $depth > 0 ) {
			$query->where( 'a.depth <= '.(int)$depth );
		}
		
		// Filter Search
		$location	=	$this->getState( 'filter.location' );
		$search		=	$this->getState( 'filter.search' );
		if ( ! empty( $search ) ) {
			switch ( $location ) {
				case 'id':
					$where	=	( strpos( $search, ',' ) !== false ) ? 'a.id IN ('.$search.')' : 'a.id = '.(int)$search;
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
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'lft' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.folder' );
		$id	.=	':' . $this->getState( 'filter.state' );
		$id	.=	':' . $this->getState( 'filter.depth' );

		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Folder', $prefix = CCK_TABLE, $config = array() )
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
			$location	=	'id';
		} else {
			$search		=	$app->getUserStateFromRequest( $this->context.'.filter.search', 'filter_search', '' );
			$location	=	$app->getUserStateFromRequest( $this->context.'.filter.location', 'filter_location', 'title' );
		}
		$this->setState( 'filter.search', $search );
		$this->setState( 'filter.location', $location );
			
		$folderId	=	$app->getUserStateFromRequest( $this->context.'.filter.folder', 'filter_folder', '' );
		$this->setState( 'filter.folder', $folderId );
		
		$state		=	$app->getUserStateFromRequest( $this->context.'.filter.state', 'filter_state', '', 'string' );
		$this->setState( 'filter.state', $state );
		
		$depthId	=	$app->getUserStateFromRequest( $this->context.'.filter.depth', 'filter_depth', '' );
		$this->setState( 'filter.depth', $depthId );

		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'a.lft', 'asc' );
	}
}
?>