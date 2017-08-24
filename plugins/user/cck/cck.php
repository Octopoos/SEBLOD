<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableContent', JPATH_PLATFORM.'/joomla/database/table/content.php' );

// Plugin
class plgUserCCK extends JPlugin
{	
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
		
		return $this->_delete( $pk, 'joomla_user', 'users', 'onUserAfterDelete' );
	}
	
	// onUserAfterSave
	public function onUserAfterSave( $user, $isNew, $user2 )
	{
		// Processing
		JLoader::register( 'JCckToolbox', JPATH_PLATFORM.'/cms/cck/toolbox.php' );
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onUserAfterSave';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

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
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );
						
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}
		}
	}
	
	// onUserLogin
	public function onUserLogin( $user, $options = array() )
	{
		// Processing
		JLoader::register( 'JCckToolbox', JPATH_PLATFORM.'/cms/cck/toolbox.php' );
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onUserLogin';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );
						
						include_once JPATH_SITE.$p->scriptfile;
					}
				}
			}
		}
	}
	
	// _delete
	protected function _delete( $pk, $location, $base, $event = '' )
	{
		$id		=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location = "'.(string)$location.'" AND pk = '.(int)$pk );
		if ( ! $id ) {
			return true;
		}
		$table	=	JCckTable::getInstance( '#__cck_core', 'id', $id );
		$type	=	$table->cck;
		$parent	=	'';
		$pkb	=	(int)$table->pkb;
		
		if ( $table->pk > 0 ) {
			// -- Leave nothing behind
			if ( $type != '' ) {
				require_once JPATH_LIBRARIES.'/cck/base/form/form.php';

				JPluginHelper::importPlugin( 'cck_field' );
				JPluginHelper::importPlugin( 'cck_storage' );
				JPluginHelper::importPlugin( 'cck_storage_location' );

				$config		=	array(
									'pk'=>$table->pk,
									'storages'=>array(),
									'type'=>$table->cck
								);
				$dispatcher	=	JEventDispatcher::getInstance();
				$parent		=	JCckDatabase::loadResult( 'SELECT parent FROM #__cck_core_types WHERE name = "'.$type.'"' );
				$fields		=	CCK_Form::getFields( array( $type, $parent ), 'all', -1, '', true );
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$Pt		=	$field->storage_table;
						$value	=	'';
						
						/* Yes but, .. */

						if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
							$config['storages'][$Pt]	=	'';
							
							$dispatcher->trigger( 'onCCK_Storage_LocationPrepareDelete', array( &$field, &$config['storages'][$Pt], $pk, &$config ) );
						}
						$dispatcher->trigger( 'onCCK_StoragePrepareDelete', array( &$field, &$value, &$config['storages'][$Pt], &$config ) );
						$dispatcher->trigger( 'onCCK_FieldDelete', array( &$field, $value, &$config, array() ) );
					}
				}
			}
			// -- Leave nothing behind

			$table->delete();

			if ( $pkb > 0 ) {
				$table	=	JTable::getInstance( 'Content' );
				$table->delete( $pkb );
			}
		}
		
		// Processing
		JLoader::register( 'JCckToolbox', JPATH_PLATFORM.'/cms/cck/toolbox.php' );
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			if ( $event == 'onUserAfterDelete' ) {
				$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
				if ( isset( $processing[$event] ) ) {
					foreach ( $processing[$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							include_once JPATH_SITE.$p->scriptfile;	/* Variables: $id, $pk, $type */
						}
					}
				}
			}
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

		if ( $parent != '' && in_array( $prefix.'cck_store_form_'.$parent, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_form_'.$parent, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
	}
}
?>