<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContentJoomla_User extends JCckContent
{
	// setInstanceBase
	protected function setInstanceBase()
	{
		$this->_instance_base	=	JUser::getInstance();

		$fields					=	 array_keys( $this->_instance_base->getTable()->getFields() );
		unset( $fields['id'], $fields['cck'] );
		$this->_data_map		=	array_merge( $this->_data_map, array_fill_keys( $fields, 'base' ) );

		return true;
	}

	// initialize
	protected function initialize()
	{
		JPluginHelper::importPlugin( 'user', 'cck' );
	}

	// check
	public function check( $instance_name )
	{
		if ( $instance_name == 'base' ) {
			return true;
		} else {
			return $this->{'_instance_'.$instance_name}->check();
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
		return $this->_instance_base->save();
	}

	// store
	public function store( $instance_name )
	{
		if ( !$this->can( 'save' ) ) {
			return false;
		}

		if ( $instance_name == 'base' ) {
			return $this->_instance_base->save();
		} else {
			return $this->{'_instance_'.$instance_name}->store();
		}
	}

	// triggerDelete
	public function triggerDelete( $event )
	{
		if ( $event == 'beforeDelete' ) {
			return $this->_dispatcher->trigger( $this->_columns['events'][$event], array( $this->_instance_base->getProperties() ) );
		} elseif ( $event == 'afterDelete' ) {
			return $this->_dispatcher->trigger( $this->_columns['events'][$event], array( $this->_instance_base->getProperties(), true, $this->_instance_base->getError() ) );
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
			return false;
		}

		if ( JCck::getUser()->id == $this->_pk ) {
			return JUserHelper::removeFromGroup( $this->_pk, $group_id );
		} else {
			return JCckDatabase::execute( 'DELETE FROM #__user_usergroup_map WHERE user_id = '.(int)$this->_pk.' AND group_id = '. (int)$group_id );
		}
	}
}
?>