<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Model
class CCKModelList extends JModelLegacy
{
	var	$_pagination	=	null;
		
	// __construct
	function __construct()
	{
		parent::__construct();
		
		$app	=	JFactory::getApplication();
		$config	=	JFactory::getConfig();
		
		$this->setState( 'limitstart', $app->input->getUInt( 'limitstart', 0 ) );
		$this->setState( 'limit', $app->getUserStateFromRequest( 'com_cck.limit', 'limit', $config->get( 'list_limit' ), 'uint' ) );
	}
	
	// _getPagination
	function _getPagination( $total = 0 )
	{
		if ( empty( $this->_pagination ) )
		{
			jimport( 'joomla.html.pagination' );
			$this->_pagination	=	new JPagination( $total, $this->getState( 'limitstart' ), $this->getState( 'limit' ) );
		}

		return $this->_pagination;
	}
	
	// delete
	function delete( $pks = array() )
	{
		JPluginHelper::importPlugin( 'content' );
		JPluginHelper::importPlugin( 'cck_storage_location' );
		
		$nb		=	0;
		$pks_in	=	implode( ',', $pks );
		
		$items	=	JCckDatabase::loadObjectList( 'SELECT a.id, a.cck, a.pk, a.pkb, a.storage_location, a.storage_table, a.author_id, b.id as type_id FROM #__cck_core as a'
												. ' LEFT JOIN #__cck_core_types AS b ON b.name = a.cck'
												. ' WHERE a.id IN ('.$pks_in.')', 'id' );
		$config	=	array( 'author'=>0, 'type'=>'', 'type_id'=>0 );
		
		foreach ( $pks as $pk ) {
			$location	=	$items[$pk]->storage_location;
			if ( $location ) {
				$config['author']	=	$items[$pk]->author_id;
				$config['type']		=	$items[$pk]->cck;
				$config['type_id']	=	$items[$pk]->type_id;
				if ( JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'onCCK_Storage_LocationDelete', array( $items[$pk]->pk, &$config ) ) ) {
					$nb++;
				}
			}
		}
		
		return $nb;
	}
	
	// saveOrder
	public function saveOrder( $pks = array(), $lft = array() )
	{
		JPluginHelper::importPlugin( 'cck_storage_location' );
		
		if ( !count( $pks ) ) {
			return false;
		}

		$db 	= 	JFactory::getDbo();
		$query	= 	$db->getQuery( true );
		$query->select( 'a.id, a.pk, a.storage_location, b.id AS type_id' )
			  ->from( '#__cck_core AS a' )
			  ->join( 'LEFT', '#__cck_core_types AS b ON b.name = a.cck' )
			  ->where( 'a.id IN (' . implode( ',', $pks ) . ')' );

		$db->setQuery($query);
		$results 	= 	$db->loadAssocList( 'id' );

		if ( !empty( $results ) ) {
			$ids 		= 	array();
			$location 	= 	null;
			$user 		=	JCck::getUser();

			foreach ( $pks as $i=>$pk ) {
				$canEdit	=	$user->authorise( 'core.edit', 'com_cck.form.'.$results[$pk]['type_id'] );
				$canEditOwn	=	$user->authorise( 'core.edit.own', 'com_cck.form.'.$results[$pk]['type_id'] );

				// Check Permissions
				if ( !( $canEdit && $canEditOwn
					|| ( $canEdit && !$canEditOwn && ( $results[$pk]['author_id'] != $user->id ) )
					|| ( $canEditOwn && ( $results[$pk]['author_id'] == $user->id ) ) ) ) {
					unset( $lft[$i] );
					continue;
				}

				$ids[] 		= 	$results[$pk]['pk'];

				if ( null === $location ) {
					$location 	= 	$results[$pk]['storage_location'];
				}

			}
			if ( $location && count( $ids ) ) {
				return JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'onCCK_Storage_LocationSaveOrder', array( $ids, $lft ) );
			}
		}

		return false;
	}
}
?>