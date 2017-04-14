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
class CCKModelSessions extends JModelList
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'title', 'a.title',
				'type', 'a.type',
				'extension', 'a.extension',
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
				'a.title as title,' .
				'a.type as type,' .
				'a.extension as extension'
			)
		);

		// From
		$query->from( '`#__cck_more_sessions` AS a' );
		
		$type		=	$this->getState( 'filter.type' );
		$extension	=	$this->getState( 'filter.extension' );
		
		// Where
		$query->where( 'a.extension = "'.(string)$extension.'"' );
		
		// Force State
		$query->where( 'a.published != -44' );

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
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'a.title' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.extension' );
		$id	.=	':' . $this->getState( 'filter.type' );
		
		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Session', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}

	// populateState
	protected function populateState( $ordering = null, $direction = null )
	{
		$app		=	JFactory::getApplication( 'administrator' );
		
		$id			=	$app->input->get( 'id', '' );
		if ( $id ) {
			$search		=	$id;
			$location	=	'id';
		} else {
			$search		=	$app->getUserStateFromRequest( $this->context.'.filter.search', 'filter_search', '' );
			$location	=	$app->getUserStateFromRequest( $this->context.'.filter.location', 'filter_location', 'title' );
		}
		$this->setState( 'filter.search', $search );
		$this->setState( 'filter.location', $location );
		
		$extension	=	$app->getUserStateFromRequest( $this->context.'.filter.extension', 'extension', 'extension', 'string' );
		$this->setState( 'filter.extension', $extension );
		
		$type	=	$app->getUserStateFromRequest( $this->context.'.filter.type', 'filter_type', 'type', 'string' );
		$this->setState( 'filter.type', $type );
		
		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'a.title', 'asc' );
	}
}
?>