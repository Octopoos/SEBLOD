<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: variations.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

// Model
class CCKModelVariations extends JModelList
{
	// __construct
	public function __construct( $config = array() )
	{
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields']	=	array(
				'id', 'a.id',
				'title', 'a.title',
				'folder', 'a.folder',
				'template', 'a.template',
				'type', 'a.type'
			);
		}
		
		parent::__construct( $config );
	}
	
	// getItems
	public function getItems()
	{
		jimport( 'joomla.filesystem.folder' );
		$folders	=	JFolder::folders( JPATH_LIBRARIES.'/cck/rendering/variations' );
		$i			=	0;
		$items		=	array();
		$variations	=	array(
							'empty'=>'',
							'joomla'=>'',
							'seb_css3'=>'',
							'seb_css3b'=>''
						);

		// Filter Search
		$location	=	$this->getState( 'filter.location' );
		$search		=	$this->getState( 'filter.search' );

		// Library
		if ( count( $folders ) && $location != 'template_name' ) {
			foreach ( $folders as $k=>$v ) {
				if ( $search && strpos( $v, $search ) === false ) {
					continue;
				}
				$items[$i]				=	new stdClass;
				$items[$i]->folder		=	'/libraries/cck/rendering/variations/';
				$items[$i]->template	=	'';
				$items[$i]->title		=	$v;
				$items[$i]->type		=	( isset( $variations[$v] ) ) ? 0 : 1;
				$items[$i++]->id		=	$i;
			}
		}
		
		// Templates
		if ( $search && $location == 'template_name' ) {
			$templates	=	array( 0=>$search );
			$search		=	'';
		} else {
			$templates	=	JCckDatabase::loadColumn( 'SELECT name FROM #__cck_core_templates' );	
		}
		if ( count( $templates ) ) {
			foreach ( $templates as $k=>$template ) {
				$path	=	'/templates/'.$template.'/variations';
				if ( JFolder::exists( JPATH_SITE.$path ) ) {
					$folders	=	JFolder::folders( JPATH_SITE.$path );
					if ( count( $folders ) ) {
						foreach ( $folders as $k=>$v ) {
							if ( $search && strpos( $v, $search ) === false ) {
								continue;
							}
							$items[$i]				=	new stdClass;
							$items[$i]->folder		=	$path;
							$items[$i]->template	=	$template;
							$items[$i]->title		=	$v;
							$items[$i]->type		=	1;
							$items[$i++]->id		=	$i;
						}
					}
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

	// getTotal
	public function getTotal()
	{
		return 0;
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
		
		//$type	=	$app->input->get( 'e_type', '' );
		$type	=	$app->getUserStateFromRequest( $this->context.'.filter.e_type', 'filter_e_type', 'type', 'string' );
		$this->setState( 'filter.e_type', $type );
		
		$params		=	JComponentHelper::getParams( CCK_COM );
		$this->setState( 'params', $params );
		
		parent::populateState( 'b.title', 'asc' );
	}
}
?>