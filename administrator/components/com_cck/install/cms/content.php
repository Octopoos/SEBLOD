<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: content.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContent
{
	protected static $instances	=	array();
	protected $_id				=	'';
	protected $_table 			=	NULL;
	protected $_properties		=	NULL;
	
	// __construct
	public function __construct( $identifier = '', $data = true )
	{
		if ( $identifier ) {
			$this->load( $identifier, $data );
		}
	}
	
	// getInstance
	public static function getInstance( $identifier = '', $data = true )
	{
		if ( !$identifier ) {
			return new JCckContent;
		}

		$key	=	( is_array( $identifier ) ) ? implode( '_', $identifier ) : $identifier;
		if ( !isset( self::$instances[$key] ) ) {
			$instance	=	new JCckContent( $identifier );
			self::$instances[$key]	=	$instance;
		}

		return self::$instances[$key];
	}
	
	// create
	public function create()
	{
		//
	}

	// get
	public function get( $name, $default = '' )
	{
		if ( isset( $this->_properties->$name ) ) {
			return $this->_properties->$name;
		}

		return $default;
	}

	// getTable
	public function getTable()
	{
		return $this->_table;
	}

	// load
	public function load( $identifier, $data = true )
	{
		if ( is_array( $identifier ) ) {
			$core	=	JCckDatabase::loadObject( 'SELECT cck, pk, storage_location as location FROM #__cck_core WHERE storage_location = "'.(string)$identifier[0].'" AND pk = '.(int)$identifier[1] );
		} else {
			$core	=	JCckDatabase::loadObject( 'SELECT cck, pk, storage_location as location FROM #__cck_core WHERE id = '.(int)$identifier );
		}

		if ( is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$core->location.'/'.$core->location.'.php' ) ) {
			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$core->location.'/'.$core->location.'.php';
			$properties		=	array( 'table', 'key' );
			$properties		=	JCck::callFunc( 'plgCCK_Storage_Location'.$core->location, 'getStaticProperties', $properties );
			$this->_table	=	$properties['table'];
		}
		
		if ( !$this->_table ) {
			return;
		}
		
		if ( $data === true ) {
			$this->_properties	=	JCckDatabase::loadObject( 'SELECT a.*, b.* FROM '.$this->_table.' AS a'
															. ' LEFT JOIN #__cck_store_form_'.$core->cck.' AS b ON b.id = a.'.$properties['key']
															. ' WHERE a.'.$properties['key'].' = '.(int)$core->pk );
		} elseif ( is_array( $data ) ) {
			if ( isset( $data[$this->_table] ) ) {
				$select	=	implode( ',', $data[$this->_table] );
				unset( $data[$this->_table] );
			} else {
				$select	=	'*';
			}
			$b	=	'a';
			$i	=	98;
			foreach ( $data as $k=>$v ) {
				$a		=	chr($i);
				$select	.=	', '.$a.'.'.implode( ', '.$a.'.', $v );
				$join	.=	' LEFT JOIN '.$k.' AS '.$a.' ON '.$a.'.id = '.$b.'.'.$properties['key'];
				$b		=	$a;
				$i++;
			}
			$query	=	'SELECT a.'.$select.' FROM '.$this->_table.' AS a'
					.	$join
					.	' WHERE a.'.$properties['key'].' = '.(int)$core->pk;
			$this->_properties	=	JCckDatabase::loadObject( $query );
		}
	}

	// store
	public function store()
	{
		//
	}
}
?>