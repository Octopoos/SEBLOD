<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableContent', JPATH_PLATFORM.'/joomla/database/table/content.php' );

// Plugin
class plgUserCCK extends JPlugin
{
	// onUserBeforeDeleteNote
	public function onUserBeforeDeleteNote( $note )
	{
		$pk	=	$note['id'];
		if ( !$pk ) {
			return true;
		}
		
		return $this->_delete( $pk, 'joomla_user_note', 'user_notes' );
	}
	
	// onUserBeforeDeleteGroup
	public function onUserBeforeDeleteGroup( $group )
	{
		$pk	=	$group['id'];
		if ( !$pk ) {
			return true;
		}
		
		return $this->_delete( $pk, 'joomla_user_group', 'usergroups' );
	}
	
	// onUserAfterDelete
	public function onUserAfterDelete( $user, $success, $msg )
	{
		$pk	=	$user['id'];
		if ( !$pk ) {
			return true;
		}
		
		return $this->_delete( $pk, 'joomla_user', 'users' );
	}
	
	// onUserAfterSave
	public function onUserAfterSave( $user, $isNew, $user2 )
	{
		// Processing
		JLoader::register( 'JCckToolbox', JPATH_PLATFORM.'/cms/cck/toolbox.php' );
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onUserAfterSave';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}
		}
	}
	
	// onUserBeforeSave
	public function onUserBeforeSave( $user, $isNew, $user2 )
	{
		// Processing
		JLoader::register( 'JCckToolbox', JPATH_PLATFORM.'/cms/cck/toolbox.php' );
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onUserBeforeSave';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}
		}
	}
	
	// _delete
	protected function _delete( $pk, $location, $base )
	{
		$id		=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location = "'.(string)$location.'" AND pk = '.(int)$pk );
		if ( ! $id ) {
			return true;
		}
		$table	=	JCckTable::getInstance( '#__cck_core', 'id', $id );
		$type	=	$table->cck;
		$pkb	=	(int)$table->pkb;
		$table->delete();
		
		if ( $pkb > 0 ) {
			$table	=	JTable::getInstance( 'content' );
			$table->delete( $pkb );
		}
		
		$tables	=	JCckDatabase::loadColumn( 'SHOW TABLES' );
		$prefix	= 	JFactory::getConfig()->get( 'dbprefix' );
		
		if ( in_array( $prefix.'cck_store_item_'.$base, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_item_'.$base, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
		
		if ( in_array( $prefix.'cck_store_form_'.$type, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_form_'.$type, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
	}
}
?>