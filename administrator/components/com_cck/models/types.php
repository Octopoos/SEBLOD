<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: types.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

// Model
class CCKModelTypes extends JModelList
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
				'published', 'a.published',
				'location', 'a.location',
				'storage_location', 'a.storage_location',
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
			$admin		=	JCckDatabase::loadObjectList( 'SELECT a.typeid, COUNT( a.typeid ) AS num FROM #__cck_core_type_field AS a'
														. ' WHERE a.client = "admin" GROUP BY a.typeid', 'typeid' );
			$site		=	JCckDatabase::loadObjectList( 'SELECT a.typeid, COUNT( a.typeid ) AS num FROM #__cck_core_type_field AS a'
														. ' WHERE a.client = "site" GROUP BY a.typeid', 'typeid' );
			$intro		=	JCckDatabase::loadObjectList( 'SELECT a.typeid, COUNT( a.typeid ) AS num FROM #__cck_core_type_field AS a'
														. ' WHERE a.client = "intro" GROUP BY a.typeid', 'typeid' );
			$content	=	JCckDatabase::loadObjectList( 'SELECT a.typeid, COUNT( a.typeid ) AS num FROM #__cck_core_type_field AS a'
														. ' WHERE a.client = "content" GROUP BY a.typeid', 'typeid' );
			$version	=	JCckDatabase::loadObjectList( 'SELECT a.e_id, COUNT( a.e_id ) AS num FROM #__cck_core_versions AS a'
														. ' WHERE a.e_type = "type" GROUP BY a.e_id', 'e_id' );
			$styles		=	JCckDatabase::loadObjectList( 'SELECT a.id, a.template FROM #__template_styles AS a', 'id' );
			
			foreach ( $items as $i ) {
				$i->adminFields		=	@$admin[$i->id]->num ? $admin[$i->id]->num : 0;
				$i->siteFields		=	@$site[$i->id]->num ? $site[$i->id]->num : 0;
				$i->introFields		=	@$intro[$i->id]->num ? $intro[$i->id]->num : 0;
				$i->contentFields	=	@$content[$i->id]->num ? $content[$i->id]->num : 0;
				$i->adminTemplate	=	@$styles[$i->template_admin]->template;
				$i->siteTemplate	=	@$styles[$i->template_site]->template;
				$i->introTemplate	=	@$styles[$i->template_intro]->template;
				$i->contentTemplate	=	@$styles[$i->template_content]->template;
				$i->versions		=	@$version[$i->id]->num ? $version[$i->id]->num : 0;
			}
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
				'a.template_admin as template_admin,' .
				'a.template_site as template_site,' .
				'a.template_intro as template_intro,' .
				'a.template_content as template_content,' .
				'a.published as published,' .
				'a.location as location,' .
				'a.storage_location as storage_location,' .
				'a.checked_out as checked_out,' .
				'a.checked_out_time as checked_out_time'
			)
		);

		// From
		$query->from( '`#__cck_core_types` AS a' );

		// Join Folder
		$query->select( 'c.title AS folder_title, c.color AS folder_color, c.introchar AS folder_introchar, c.colorchar AS folder_colorchar' );
		$query->join( 'LEFT', '#__cck_core_folders AS c ON c.id = a.folder' );
		$query->select( 'd.title AS folder_parent_title, d.id AS folder_parent' );
		$query->join( 'LEFT', '#__cck_core_folders AS d ON d.id = c.parent_id' );
		
		// Join User
		$query->select( 'u.name AS editor' );
		$query->join( 'LEFT', '#__users AS u ON u.id = a.checked_out' );

		// Where
		if ( $app->input->get( 'goto', '' ) == 'form' ) {
			$folderId	=	'';
			$published	=	1;
			$location	=	'';
			$search		=	'';			
		} else {
			$folderId	=	$this->getState( 'filter.folder' );
			$published	=	$this->getState( 'filter.state' );

			if ( ( $folder = $app->input->getInt( 'folder_id', 0 ) ) > 0 ) {
				$location	=	'folder_id';
				$search		=	$folder;
				
				$this->setState( 'filter.location', $location );
				$this->setState( 'filter.search', $search );
			} else {
				$location	=	$this->getState( 'filter.location' );
				$search		=	$this->getState( 'filter.search' );
			}
		}
		
		// Filder Folder
		if ( is_numeric( $folderId ) ) {
			$folders	=	Helper_Folder::getBranch( $folderId, ',' );
			if ( $folders ) {
				$query->where( 'a.folder IN ('.$folders.')' );
			}
		}
		
		// Filter Type
		$type	=	$this->getState( 'filter.type' );
		if ( is_string( $type ) && $type != '' ) {
			$query->where( 'a.storage_location = "'.(string)$type.'"' );
		}
		
		// Filter Client
		$client	=	$this->getState( 'filter.client' );
		if ( $client == 'both' ) {
			$query->where( 'a.location = ""' );
		} elseif ( $client ) {
			if ( strpos( $client, '_both') !== false ) {
				$query->where( '( a.location = "'.(string)str_replace( '_both', '', $client ).'" OR a.location = "" )' );
			} else {
				$query->where( 'a.location = "'.(string)$client.'"' );
			}			
		}

		// Filter State
		if ( is_numeric( $published ) && $published >= 0 ) {
			$query->where( 'a.published = '.(int)$published );
		}
		$query->where( 'a.published != -44' );
		
		// Filter Search
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
				case 'field_name':
					$search	=	$db->quote( '%'.$db->escape( $search, true ).'%' );
					$where	=	'f.name LIKE '.$search;
					$query->join( 'LEFT', '#__cck_core_type_field AS e ON e.typeid = a.id' );
					$query->join( 'LEFT', '#__cck_core_fields AS f ON f.id = e.fieldid' );
					$query->where( $where );
					break;
				case 'template_name':
					$search	=	$db->quote( '%'.$db->escape( $search, true ).'%' );
					$query->join( 'LEFT', '#__template_styles AS e ON e.id = a.template_admin' );
					$query->join( 'LEFT', '#__template_styles AS f ON f.id = a.template_site' );
					$query->join( 'LEFT', '#__template_styles AS g ON g.id = a.template_intro' );
					$query->join( 'LEFT', '#__template_styles AS h ON h.id = a.template_content' );
					$where	=	'( e.template LIKE '.$search.' OR f.template LIKE '.$search.' OR g.template LIKE '.$search.' OR h.template LIKE '.$search. ')';
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
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'title' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.type' );
		$id	.=	':' . $this->getState( 'filter.state' );
		$id	.=	':' . $this->getState( 'filter.folder' );
		$id	.=	':' . $this->getState( 'filter.client' );

		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Type', $prefix = CCK_TABLE, $config = array() )
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
		
		$type		=	$app->getUserStateFromRequest( $this->context.'.filter.type', 'filter_type', '' );
		$this->setState( 'filter.type', $type );
		
		$state		=	$app->getUserStateFromRequest( $this->context.'.filter.state', 'filter_state', '1', 'string' );
		$this->setState( 'filter.state', $state );

		$folderId	=	$app->getUserStateFromRequest( $this->context.'.filter.folder', 'filter_folder', '' );
		$this->setState( 'filter.folder', $folderId );

		$client		=	$app->getUserStateFromRequest( $this->context.'.filter.client', 'filter_client', '', 'string' );
		$this->setState( 'filter.client', $client );

		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'a.title', 'asc' );
	}
}
?>