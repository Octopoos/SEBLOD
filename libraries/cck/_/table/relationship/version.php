<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: relationship.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckTableRelationship
class JCckTableRelationshipVersion extends JCckTableRelationship
{
	protected $_version_number	=	0;

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// getInstance
	public static function getInstance( $table )
	{
		$db			=	JFactory::getDbo();
		$tableClass	=	'JCckTableRelationshipVersion';
		
		return new $tableClass( $db, $table );
	}

	// load
	public function load( $identifier, $preload_id = 0 )
	{
		$where_clause	=	$this->_setById( $identifier );

		if ( $where_clause == '' ) {
			return false;
		}

		// Get Version Number
		if ( (int)$preload_id > 0 ) {
			$this->_version_number	=	(int)$preload_id;
		} else {
			$this->_version_number	=	(int)JCckDatabase::loadResult( 'SELECT MAX(version) FROM '.$this->_tbl.' WHERE '.$where_clause );
		}

		// Preload Rows
		if ( $preload_id ) {
			if ( $this->_version_number ) {
				$where_clause		.=	' AND version = '.$this->_version_number;

				$this->_tbl_rows	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$this->_tbl.' WHERE '.$where_clause, 'id2' );

				if ( !(int)$preload_id  ) {
					if ( !count( $this->_tbl_rows ) ) {
						$this->_version_number	=	0;

						return false;
					}
				}	
			} else {
				return false;
			}
		}

		return true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// resetRow
	public function resetRow()
	{
		$data	=	array(
						'id2'=>'0',
						'ordering'=>'0',
						'version'=>( $this->getVersionNumber() + 1 ),
						'version_date_time'=>JFactory::getDate()->toSql()
					);

		$this->_tbl_rows['0']	=	(object)$data;
	}

	// setRows
	public function setRows( $rows )
	{
		$version			=	$this->getVersionNumber() + 1;
		$version_date_time	=	JFactory::getDate()->toSql();

		foreach ( $rows as $k=>$row ) {
			$rows[$k]['version']			=	$version;
			$rows[$k]['version_date_time']	=	$version_date_time;

			$rows[$k]	=	(object)$rows[$k];
		}

		$this->_tbl_rows	=	$rows;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// getData
	public function getData()
	{
		$data	=	array();

		foreach ( $this->_tbl_rows as $k=>$row ) {
			$data[$k]	=	array();

			unset( $row->id, $row->version, $row->version_date_time );

			$data[$k]	=	(array)$row;
		}

		return $data;
	}

	// getDataObject
	public function getDataObject()
	{
		$data	=	array();

		foreach ( $this->_tbl_rows as $k=>$row ) {
			$data[$k]	=	array();

			unset( $row->id, $row->version, $row->version_date_time );

			$data[$k]	=	(object)$row;
		}

		return $data;
	}

	// getPks
	public function getPks()
	{
		return array_keys( $this->_tbl_rows );
	}

	// getVersionNumber
	public function getVersionNumber()
	{
		return $this->_version_number;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Has

	// hasDiff
	public function hasDiff( $rows, $ordered = true )
	{
		if ( !$this->_version_number ) {
			return true;
		}

		if ( $ordered ) {
			return true;
		} else {
			$cur	=	$this->getPks();
			$new	=	array_keys( $rows );

   			sort( $cur );
    		sort( $new );

    		return $cur == $new ? false : true;
		}
	}
}
?>