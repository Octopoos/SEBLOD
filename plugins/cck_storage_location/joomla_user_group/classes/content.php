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
class JCckContentJoomla_User_Group extends JCckContent
{
	// initialize
	protected function initialize()
	{
		JPluginHelper::importPlugin( 'user', 'cck' );
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
}
?>