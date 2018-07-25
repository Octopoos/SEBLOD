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
class JCckContentFree extends JCckContent
{
	// setInstanceMore
	protected function setInstanceBase()
	{
		$table	=	str_replace( '#__', '', $this->_table );

		if ( !$this->hasTable( $table ) ) {
			return false;
		}

		$this->_instance_base	=	JCckTable::getInstance( '#__'.$table );
		$this->_setDataMap( 'base' );

		return true;
	}

	// setTable
	public function setTable( $table_name )
	{
		if ( !$this->isSuccessful() ) {
			return $this;
		}

		$this->_table	=	$table_name;

		return $this;
	}

	// saveCore
	protected function saveCore()
	{
		if ( !$this->_instance_core->storage_table ) {
			$this->_instance_core->storage_table	=	$this->_table;
		}

		return $this->_instance_core->store();
	}
}
?>