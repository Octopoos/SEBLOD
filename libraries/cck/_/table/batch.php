<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: batch.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

// JCckTableBatch
class JCckTableBatch extends CMSObject
{
	protected $_tbl				=	'';
	protected $_tbl_colums_sql	=	'';
	protected $_tbl_rows		=	array();
	protected $_tbl_rows_sql	=	'';
	protected $_db;
	
	// __construct
	public function __construct( &$db, $table )
	{
		$this->_tbl	=	$table;
		$this->_db	=	&$db;
		
		if ( $fields = $this->getFields() ) {
			foreach ( $fields as $name => $v ) {
				if ( !property_exists( $this, $name ) ) {
					$this->$name	=	null;
				}
			}
		}
	}
	
	// getInstance
	public static function getInstance( $table )
	{
		$db			=	Factory::getDbo();
		$tableClass	=	'JCckTableBatch';
		
		// Instantiate
		$instance	=	new $tableClass( $db, $table );
		
		return $instance;
	}
	
	// getFields
	public function getFields()
	{
		$name			=	$this->_tbl;
		static $cache	=	array();
		
		if ( ! isset( $cache[$name] ) ) {
			$fields	=	$this->_db->getTableColumns( $name, false );
			if ( empty( $fields ) ) {
				$e	=	new \Exception( Text::_( 'JLIB_DATABASE_ERROR_COLUMNS_NOT_FOUND' ) );
				$this->setError( $e );
				return false;
			}
			$cache[$name]	=	$fields;
		}
		
		return $cache[$name];
	}
	
	// getRows
	public function getRows()
	{
		return $this->_tbl_rows;
	}

	// bind
	public function bind( $rows )
	{
		if ( count( $rows ) ) {
			$this->_tbl_rows	=	$rows;
		}
	}
	
	// bindArray
	public function bindArray( $data )
	{
		if ( count( $data ) ) {
			$k	=	key( $data );
			if ( is_string( $k ) ) {
				foreach ( $data[$k] as $v ) {
					$row				=	new stdClass;
					$row->$k			=	$v;
					$this->_tbl_rows[]	=	$row;
				}
			} else {
				foreach ( $data as $key=>$val ) {
					if ( count( $val ) ) {
						$row		=	new stdClass;
						foreach ( $val as $k=>$v ) {
							$row->$k	=	$v;
							
						}
						$this->_tbl_rows[]	=	$row;
					}
				}
			}
		}
	}

	// check
	public function check( $force = array(), $ignore = array(), $init = array() )
	{
		$str	=	'';
		
		if ( count( $this->_tbl_rows ) ) {
			$i	=	0;
			foreach ( $this->_tbl_rows as $row ) {
				$str2	=	'';
				if ( count( $force ) ) {
					foreach ( $force as $k=>$v ) {
						$row->$k	=	$v;
					}
				}
				if ( count( $init ) ) {
					foreach ( $init as $k=>$v ) {
						if ( !isset( $row->$k ) ) {
							$row->$k	=	$v;
						}
					}
				}
				foreach ( $this->getProperties() as $k=>$v ) {
					if ( !in_array($k, $ignore ) ) {
						if ( property_exists( $row, $k ) ) {
							if ( is_array( $row->$k ) ) {
								$row->$k	=	json_encode( $row->$k );
							}

							$str2	.=	'"'.$this->_db->escape( $row->$k ).'", ';

							if ( $i == 0 ) {
								$this->_tbl_colums_sql	.=	$this->_db->quoteName( $k ).',';
							}
						}
					}
				}
				if ( $str2 != '' ) {
					$str2	=	substr( trim( $str2 ), 0, -1 );
					$str	.=	'(' . $str2 . '), ';
				}

				$i++;
			}
			if ( $this->_tbl_colums_sql != '' ) {
				$this->_tbl_colums_sql	=	'('.substr( trim( $this->_tbl_colums_sql ), 0, -1 ).')';
			}
		}
		
		if ( $str != '' ) {
			$str	=	substr( trim( $str ), 0, -1 );
		}
		
		$this->_tbl_rows_sql	=	$str;
	}

	// delete
	public function delete( $where_clause )
	{
		$where	=	'';
		if ( is_array( $where_clause ) ) {
			if ( count( $where_clause ) ) {
				foreach ( $where_clause as $k=>$v ) {
					$where	.=	' AND '.$k . ' = "'.$v.'"';
				}
				$where	=	substr( $where, 5 );
			}
		} else {
			$where	=	$where_clause;
		}
		
		if ( !$where ) {
			return false;
		}
		
		$query	=	'DELETE FROM '.$this->_tbl.' WHERE '.$where;
		$this->_db->setQuery( $query );
		if ( ! $this->_db->execute() ) {
			return false;
		}
	}

	// count
	public function count( $where_clause = '' )
	{
		if ( $where_clause != '' ) {
			return (int)JCckDatabase::loadResult( 'SELECT COUNT(*) FROM '.$this->_tbl.' WHERE '.$where_clause );
		} else {
			return is_array( $this->_tbl_rows ) ? count( $this->_tbl_rows )	: 0;
		}
	}
	
	// dump
	public function dump()
	{
		dump( $this->_tbl_rows );
	}

	// load
	public function load( $where_clause, $key = null )
	{
		$str	=	'';
		$where	=	'';
		if ( is_array( $where_clause ) ) {
			if ( count( $where_clause ) ) {
				foreach ( $where_clause as $k=>$v ) {
					$where	.=	' AND '.$k . ' = "'.$v.'"';
				}
				$where		=	substr( $where, 5 );
			}
		} else {
			$where			=	$where_clause;
		}
		
		$query				=	'SELECT * FROM '.$this->_tbl.' WHERE '.$where;
		$this->_tbl_rows	=	JCckDatabase::loadObjectList( $query, $key );
	}
	
	// mergeArray
	public function mergeArray( $data )
	{
		if ( count( $this->_tbl_rows ) ) {
			foreach ( $this->_tbl_rows as $key=>$val ) {
				if ( isset( $data[$key] ) ) {
					if ( count( $data[$key] ) ) {
						foreach ( $data[$key] as $k=>$v ) {
							if ( isset( $val->$k ) ) {
								$val->$k	=	$v;
							}
						}
					}
					unset( $data[$key] );
				} else {
					unset( $this->_tbl_rows[$key] );
				}
			}
		}
		if ( count( $data ) ) {
			foreach ( $data as $key=>$val ) {
				$obj	=	JCckTable::getInstance( $this->_tbl );
				if ( count( $val ) ) {
					foreach ( $val as $k=>$v ) {
						if ( property_exists( $obj, $k ) ) {
							$obj->$k	=	$v;
						}
					}
				}
				$this->_tbl_rows[$key]	=	$obj;
			}
		}
	}

	// save
	public function save( $rows = array(), $force = array(), $ignore = array(), $init = array() )
	{
		$this->bind( $rows );
		$this->check( $force, $ignore, $init );
		$this->store();
	}
	
	// sort
	public function sort( $keys )
	{
		if ( count( $keys ) ) {
			$rows	=	array();
			foreach ( $keys as $key ) {
				if ( isset( $this->_tbl_rows[$key] ) ) {
					$rows[]	=	$this->_tbl_rows[$key];
				}
			}
			$this->bind( $rows );
		}
	}

	// store
	public function store()
	{
		if ( !$this->_tbl_rows_sql ) {
			return false;
		}
		
		$query	=	'INSERT IGNORE INTO '.$this->_tbl.$this->_tbl_colums_sql.' VALUES '.$this->_tbl_rows_sql;
		
		$this->_db->setQuery( $query );
		
		if ( ! $this->_db->execute() ) {
			return false;
		}
	}
}
?>