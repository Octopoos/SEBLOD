<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContentJoomla_Menu_Item extends JCckContent
{	
	// preSave
	protected function preSave( $table_instance_name, &$data )
	{
		if ( $table_instance_name == 'base' ) {
			if ( !isset( $data['parent_id'] ) ) {
				$data['parent_id']	=	( $this->getPk() ) ? $this->_instance_base->parent_id : 1;
			}
			if ( !$this->getPk() || ( $data['parent_id'] != $this->_instance_base->parent_id ) ) {
				$this->_instance_base->setLocation( $data['parent_id'], 'last-child' );
			}
		}
	}

	// postSave
	protected function postSave( $table_instance_name, $data )
	{
		if ( $table_instance_name == 'base' ) {
			$this->_instance_base->rebuildPath( $this->getPk() );
		}
	}
}
?>