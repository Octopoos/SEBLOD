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
class JCckTableRelationship extends JObject
{
	protected $_db;

	protected $_error			=	false;
	protected $_pk				=	0;

	protected $_sql_colums		=	'';
	protected $_sql_rows		=	'';

	protected $_tbl				=	'';
	protected $_tbl_key			=	'';
	protected $_tbl_keys		=	array(); /* TODO? */
	protected $_tbl_rows		=	array();

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct( &$db, $table )
	{
		$this->_db		=	&$db;
		$this->_tbl		=	$table;
		$this->_tbl_key	=	'id';
	}
	
	// getInstance
	public static function getInstance( $table )
	{
		$db			=	JFactory::getDbo();
		$tableClass	=	'JCckTableRelationship';
		
		return new $tableClass( $db, $table );
	}

	// load
	public function load( $identifier, $preload = true )
	{
		$where_clause	=	$this->_setById( $identifier );

		if ( $where_clause && $preload ) {
			$this->_tbl_rows	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$this->_tbl.' WHERE '.$where_clause, 'id2' );

			return true;
		}

		return false;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// delete
	public function delete( $identifier = 0 )
	{
		if ( $identifier ) {
			$where_clause	=	$this->_setById( $identifier );
		} elseif ( !$this->isSuccessful() ) {
			return false;
		} else {
			$where_clause	=	$this->_tbl_key .' = '. (int)$this->_pk;
		}

		if ( !( $this->_pk ) ) {
			return false;
		}

		if ( $where_clause ) {
			$this->_db->setQuery( 'DELETE FROM '.$this->_tbl.' WHERE '.$where_clause );

			if ( $this->_db->execute() ) {
				return true;
			}
		}

		return false;
	}

	// deleteRow
	public function deleteRow( $relation_id )
	{
		if ( !$this->isSuccessful() ) {
			return false;
		}
		if ( !( $this->_pk ) ) {
			return false;
		}

		$where_clause	=	$this->_tbl_key .' = '. (int)$this->_pk
						.	' AND id2 = '.(int)$relation_id;

		$this->_db->setQuery( 'DELETE FROM '.$this->_tbl.' WHERE '.$where_clause );

		if ( $this->_db->execute() ) {
			return true;
		}

		return false;
	}

	// insert
	public function insert( $data )
	{
		/* TODO */
	}

	// insertRow
	public function insertRow( $relation_id, $data = array() )
	{
		/* TODO */
	}

	// setRow
	public function setRow( $relation_id, $data = array() )
	{
		if ( isset( $this->_tbl_rows[$relation_id] ) ) {
			if ( $this->deleteRow( $relation_id ) ) {
				$this->unsetRow( $relation_id );
			}
		}

		// Check
		if ( !isset( $data['id2'] ) ) {
			$data['id2']	=	(string)$relation_id;
		}
		if ( !isset( $data['ordering'] ) ) {
			$data['ordering']	=	(string)count( $this->_tbl_rows );
		}

		$this->_tbl_rows[$relation_id]	=	(object)$data;
	}

	// setRows
	public function setRows( $rows )
	{
		$i	=	0;

		foreach ( $rows as $k=>$row ) {
			$rows[$k]			=	(object)$rows[$k];
			$rows[$k]->ordering	=	$i++;
		}

		$this->_tbl_rows	=	$rows;
	}

	// unsetRow
	public function unsetRow( $relation_id )
	{
		if ( isset( $this->_tbl_rows[$relation_id] ) ) {
			unset( $this->_tbl_rows[$relation_id] );
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// getColumns
	public function getColumns()
	{
		$name			=	$this->_tbl;

		static $cache	=	array();
		
		if ( !isset( $cache[$name] ) ) {
			$columns	=	$this->_db->getTableColumns( $name, true );
			
			if ( empty( $columns ) ) {
				$e	=	new JException( JText::_( 'JLIB_DATABASE_ERROR_COLUMNS_NOT_FOUND' ) );

				$this->setError( $e );

				return false;
			}

			$cache[$name]	=	array_fill_keys( array_keys( $columns ), null );
		}
		
		return $cache[$name];
	}

	// getRows
	public function getRows()
	{
		return $this->_tbl_rows;
	}

	// isSuccessful
	public function isSuccessful()
	{
		return $this->_error ? false : true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Save

	// store
	public function store()
	{
		if ( !$this->_sql_rows ) {
			$columns	=	$this->getColumns();
			$i			=	0;

			foreach ( $this->_tbl_rows as $row ) {
				$sql	=	'';

				foreach ( $columns as $k=>$v ) {
					if ( $i == 0 ) {
						$this->_sql_colums	.=	$this->_db->quoteName( $k ).',';
					}

					$v	=	'';

					if ( $this->_pk ) {
						if ( !( isset( $row->{$this->_tbl_key} ) && $row->{$this->_tbl_key} ) ) {
							$row->{$this->_tbl_key}	=	(string)$this->_pk;
						}
					}
					if ( property_exists( $row, $k ) ) {
						$v	=	$this->_db->escape( $row->$k );
					}
					
					$sql	.=	'"'.$v.'", ';
				}

				if ( $sql != '' ) {
					$sql				=	substr( trim( $sql ), 0, -1 );
					$this->_sql_rows	.=	'(' . $sql . '), ';
				}

				$i++;
			}

			if ( $this->_sql_colums != '' ) {
				$this->_sql_colums	=	'('.substr( trim( $this->_sql_colums ), 0, -1 ).')';
			}
			if ( $this->_sql_rows != '' ) {
				$this->_sql_rows	=	substr( trim( $this->_sql_rows ), 0, -1 );
			}

			if ( !$this->_sql_rows ) {
				return false;
			}
		}

		$this->_db->setQuery( 'INSERT IGNORE INTO '.$this->_tbl.' '.$this->_sql_colums.' VALUES '.$this->_sql_rows );
		
		if ( ! $this->_db->execute() ) {
			return false;
		}

		return true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Misc

	// _getSql
	protected function _getSql( $identifier )
	{

	}

	// _setById
	protected function _setById( $identifier )
	{
		if ( is_array( $identifier ) ) {
			$where	=	array();

			foreach ( $identifier as $k=>$v ) {
				if ( $k == $this->_tbl_key ) {
					$this->_pk	=	$v;
				}

				$where[]	=	$k.' = "'.$v.'"';
			}

			$where_clause	=	implode( ' AND ', $where );
		} else {
			$this->_pk		=	$identifier;
			$where_clause	=	$this->_tbl_key .' = '. (int)$identifier;
		}

		return $where_clause;
	}
	
	// dump
	public function dump()
	{
		dump( $this->_tbl_rows );
	}
}
?>