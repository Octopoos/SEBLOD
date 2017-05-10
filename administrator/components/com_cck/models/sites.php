<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: sites.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

// Model
class CCKModelSites extends JModelList
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'title', 'a.title',
				'name', 'a.name',
				'aliases', 'a.aliases',
				'guest_only_group', 'a.guest_only_group',
				'guest_only_viewlevel', 'a.guest_only_viewlevel',
				'groups', 'a.groups',
				'viewlevels', 'a.viewlevels',
				'description', 'a.description',
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
			$group_map	=	JCckDatabase::loadObjectList( 'SELECT a.group_id, COUNT( DISTINCT a.user_id ) AS num FROM #__user_usergroup_map AS a GROUP BY a.group_id', 'group_id' );
			
			foreach ( $items as $item ) {
				$item->name		=	str_replace( '@', '/', $item->name );

				$viewlevels		=	( $item->guest_only_viewlevel ) ? $item->guest_only_viewlevel.','.$item->viewlevels : $item->viewlevels;
				$groups			=	( $item->guest_only_group ) ? $item->guest_only_group.','.$item->groups : $item->groups;

				if ( $viewlevels ) {
					$query			=	'SELECT COUNT(a.id) FROM #__cck_core AS a LEFT JOIN #__content AS b ON b.id = a.pk WHERE a.storage_location = "joomla_article" AND b.access IN ('.$viewlevels.');';				
					$item->articles	=	JCckDatabase::loadResult( $query );
				} else {
					$item->articles	=	0;
				}
				if ( $groups ) {
					$query			=	'SELECT COUNT(DISTINCT a.user_id) FROM #__user_usergroup_map AS a WHERE a.group_id IN ('.$groups.');';
					$item->users	=	JCckDatabase::loadResult( $query );
				} else {
					$item->users	=	0;
				}
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
				'a.aliases as aliases,' .
				'a.guest_only_group as guest_only_group,' .
				'a.guest_only_viewlevel as guest_only_viewlevel,' .
				'a.groups as groups,' .
				'a.viewlevels as viewlevels,' .
				'a.description as description,' .
				'a.published as published,' .
				'a.checked_out as checked_out,' .
				'a.checked_out_time as checked_out_time'
			)
		);

		// From
		$query->from( '`#__cck_core_sites` AS a' );
		
		// Join User
		$query->select( 'u.name AS editor' );
		$query->join( 'LEFT', '#__users AS u ON u.id = a.checked_out' );
		
		// Filter State
		$published	=	$this->getState( 'filter.state' );
		if ( is_numeric( $published ) && $published >= 0 ) {
			$query->where( 'a.published = '.(int)$published );
		}
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
		$query->order( $db->escape( $this->state->get( 'list.ordering', 'title' ).' '.$this->state->get( 'list.direction', 'ASC' ) ) );
		
		return $query;
	}
	
	// getStoreId
	protected function getStoreId( $id = '' )
	{
		$id	.=	':' . $this->getState( 'filter.search' );
		$id	.=	':' . $this->getState( 'filter.location' );
		$id	.=	':' . $this->getState( 'filter.state' );
		$id	.=	':' . $this->getState( 'filter.mode' );

		return parent::getStoreId( $id );
	}

	// getTable
	public function getTable( $type = 'Site', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}

	// populateState
	protected function populateState( $ordering = null, $direction = null )
	{
		$app		=	JFactory::getApplication( 'administrator' );
		$search		=	$app->getUserStateFromRequest( $this->context.'.filter.search', 'filter_search', '' );
		$location	=	$app->getUserStateFromRequest( $this->context.'.filter.location', 'filter_location', 'title' );
			
		$this->setState( 'filter.search', $search );
		$this->setState( 'filter.location', $location );
		
		$state		=	$app->getUserStateFromRequest( $this->context.'.filter.state', 'filter_state', '1', 'string' );
		$this->setState( 'filter.state', $state );

		$mode		=	$app->getUserStateFromRequest( $this->context.'.filter.mode', 'filter_mode', '', 'string' );
		$this->setState( 'filter.mode', $mode );

		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'a.title', 'asc' );
	}
}
?>