<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableField extends JTable
{
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_fields', 'id', $db );
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		if ( empty( $this->name ) ) {
			return false;
		}
		
		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			if ( strpos( $this->storage_table, '#__cck_store_item_' ) !== false || strpos( $this->storage_table, '#__cck_store_form_' ) !== false ) {
				if ( !$this->storage_field2 ) {
					$db			=	JFactory::getDbo();
					$table		=	(string)$this->storage_table;
					$column		=	( $this->storage_field ) ? $this->storage_field : $this->name;
					$columns	=	$db->getTableColumns( $table );
					if ( isset( $columns[$column] ) ) {
						if ( JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core_fields WHERE storage_table = "'.(string)$table.'" AND storage_field = "'.(string)$column.'"' ) == 1 ) {
							JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $table ).' DROP COLUMN '.JCckDatabase::quoteName( (string)$column ) );
						}
					}
				}
			}

			JCckDatabase::execute( 'DELETE IGNORE a.*'
							.	' FROM #__cck_core AS a'
							.	' WHERE a.storage_location="cck_field" AND a.pk="'.(int)$this->id.'"' );
		}
		
		return parent::delete();
	}
}
?>