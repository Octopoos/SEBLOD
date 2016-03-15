<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: table.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckTable
class JCckTable extends JTable
{
	// __construct
	function __construct( $table, $key, &$db )
	{
		parent::__construct( $table, $key, $db );
	}
	
	// getInstance
	public static function getInstance( $table, $key = 'id', $pk = 0, $force = false )
	{
		$db			=	JFactory::getDbo();
		$tableClass	=	'JCckTable';
		
		// Instantiate
		$instance	=	new $tableClass( $table, $key, $db );
		if ( $pk > 0 ) {
			$instance->load( $pk, $force );
		}
		
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
				$e	=	new JException( JText::_( 'JLIB_DATABASE_ERROR_COLUMNS_NOT_FOUND' ) );
				$this->setError( $e );
				return false;
			}
			$cache[$name]	=	$fields;
		}
		
		return $cache[$name];
	}
	
	// load
	public function load( $pk = null, $force = false )
	{
		$return	=	parent::load( $pk );
		$k		=	$this->_tbl_key;
		
		if ( ! $return ) {
			if ( $force === true ) {
				JCckDatabase::execute( 'INSERT INTO '.$this->_tbl.' ('.$k.') VALUES ('.(int)$pk.')' );
				$return	=	parent::load( $pk );
			}
		}
		
		return $return;
	}
	
	// storeIt
	public function storeIt()
	{
		$this->check();
		$this->store();
	}
}
?>