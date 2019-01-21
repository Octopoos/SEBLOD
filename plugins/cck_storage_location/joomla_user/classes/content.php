<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContentJoomla_User extends JCckContent
{
	// setInstanceBase
	protected function setInstanceBase()
	{
		$this->_instance_base	=	new JUser;

		$fields					=	array_keys( $this->_instance_base->getTable()->getFields() );
		unset( $fields['id'] );

		self::$types[$this->_type]['data_map']				=	array_merge( self::$types[$this->_type]['data_map'], array_fill_keys( $fields, 'base' ) );
		self::$types[$this->_type]['data_map']['groups']	=	'base';

		return true;
	}

	// initialize
	protected function initialize()
	{
		JPluginHelper::importPlugin( 'user' );
	}

	// check
	public function check( $table_instance_name )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}

		if ( $table_instance_name == 'base' ) {
			return true;
		} else {
			return $this->{'_instance_'.$table_instance_name}->check();
		}
	}

	// remove
	protected function remove()
	{
		return $this->_instance_base->delete();
	}

	// saveBase
	protected function saveBase()
	{
		if ( !$this->getId() ) {
			if ( empty( $this->_instance_base->groups ) ) {
				$this->_instance_base->groups	=	array( 2 );
			}
		}

		return $this->_instance_base->save();
	}

	// storeBase
	protected function storeBase()
	{
		return $this->_instance_base->save();
	}

	// triggerDelete
	public function triggerDelete( $event )
	{
		if ( $event == 'beforeDelete' ) {
			return $this->_dispatcher->trigger( self::$objects[$this->_object]['properties']['events'][$event], array( $this->_instance_base->getProperties() ) );
		} elseif ( $event == 'afterDelete' ) {
			return $this->_dispatcher->trigger( self::$objects[$this->_object]['properties']['events'][$event], array( $this->_instance_base->getProperties(), true, $this->_instance_base->getError() ) );
		}

		return true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc

	// _addToGroup
	protected function _addToGroup( $group_id )
	{
		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->can( 'save' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		if ( JCck::getUser()->id == $this->_pk ) {
			return JUserHelper::addUserToGroup( $this->_pk, $group_id );
		} else {
			return JCckDatabase::execute( 'INSERT IGNORE INTO #__user_usergroup_map VALUES ('.(int)$this->_pk.', '.(int)$group_id.')' );
		}
	}

	// _removeFromGroup
	protected function _removeFromGroup( $group_id )
	{
		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->can( 'save' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		if ( JCck::getUser()->id == $this->_pk ) {
			return JUserHelper::removeUserFromGroup( $this->_pk, $group_id );
		} else {
			return JCckDatabase::execute( 'DELETE FROM #__user_usergroup_map WHERE user_id = '.(int)$this->_pk.' AND group_id = '. (int)$group_id );
		}
	}
}
?>