<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: versions.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

// Model
class CCKModelVersions extends JModelList
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'e_title', 'b.e_title',
				'e_name', 'b.e_name',
				'e_version', 'b.e_version',
				'e_more', 'b.e_more',
				'date_time', 'a.date_time',
				'user_id', 'a.user_id',
				'featured', 'a.featured',
				'note', 'a.note',
				'title', 'b.title',
				'name', 'b.name',
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
		$db		=	$this->getDbo();
		$query	=	$db->getQuery( true );	
		
		// Select
		$query->select (
			$this->getState (
				'list.select',
				'a.id as id,' .
				'a.e_title as e_title,' .
				'a.e_name as e_name,' .
				'a.e_version as e_version,' .
				'a.e_more as e_more,' .
				'a.date_time as date_time,' .
				'a.user_id as user_id,' .
				'a.featured as featured,' .
				'a.note as note,' .
				'b.title as title,' .
				'b.name as name,' .
				'a.checked_out as checked_out,' .
				'a.checked_out_time as checked_out_time'
			)
		);

		// From
		$query->from( '`#__cck_core_versions` AS a' );
		
		// Join
		$type	=	$this->getState( 'filter.e_type' );
		$query->join( 'LEFT', '#__cck_core_'.$type.'s AS b ON b.id = a.e_id' );
		
		// Join User
		$query->select( 'c.name AS created_by' );
		$query->join( 'LEFT', '#__users AS c ON c.id = a.user_id' );
		
		// Join User
		$query->select( 'u.name AS editor' );
		$query->join( 'LEFT', '#__users AS u ON u.id = a.checked_out' );

		// Where
		$query->where( 'a.e_type = "'.(string)$type.'"' );
		
		// Force State
		$query->where( 'a.published != -44' );

		// Filter Search
		$location	=	$this->getState( 'filter.location' );
		$search		=	$this->getState( 'filter.search' );
		if ( ! empty( $search ) ) {
			switch ( $location ) {
				case 'e_id':
					$where	=	( strpos( $search, ',' ) !== false ) ? 'a.e_id IN ('.$search.')' : 'a.e_id = '.(int)$search;
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
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'b.title' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		$query->order( $db->escape( 'a.date_time'.' '.'DESC' ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.e_type' );
		
		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Version', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}

	// populateState
	protected function populateState( $ordering = null, $direction = null )
	{
		$app		=	JFactory::getApplication( 'administrator' );
		
		$id			=	$app->input->get( 'e_id', '' );
		if ( $id ) {
			$search		=	$id;
			$location	=	'e_id';
		} else {
			$search		=	$app->getUserStateFromRequest( $this->context.'.filter.search', 'filter_search', '' );
			$location	=	$app->getUserStateFromRequest( $this->context.'.filter.location', 'filter_location', 'title' );
		}
		$this->setState( 'filter.search', $search );
		$this->setState( 'filter.location', $location );
		
		$type	=	$app->getUserStateFromRequest( $this->context.'.filter.e_type', 'filter_e_type', 'type', 'string' );
		$this->setState( 'filter.e_type', $type );
		
		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'b.title', 'asc' );
	}
}
?>