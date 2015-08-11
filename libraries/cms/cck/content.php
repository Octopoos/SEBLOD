<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: content.php oliviernolbert $
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
	protected static $instances		=	array();
	protected $_id					=	'';
	protected $_pk					=	'';
	protected $_type				=	'';
	protected $_table 				=	NULL;
	protected $_properties			=	NULL;
	protected $_object				=	NULL;
	protected $_columns				=	array();
	
	protected $_instance_base		=	'';
	protected $_instance_core		=	'';
	protected $_instance_more		=	'';
	
	// __construct
	public function __construct( $identifier = '', $data = true )
	{
		$this->_instance_base	=	JCckTable::getInstance( '#__cck_core', 'id' );
		
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
	public function create( $cck, $data_content, $data_more = null )
	{
		if ( $this->_id ) {
			return;
		}
		
		$this->_type	=	$cck;
		
		if ( empty( $this->_object ) ) {
			$this->_object		=	JCckDatabaseCache::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );
			$this->_columns		=	$this->_getProperties();
		}
		
		$this->_instance_core	=	JTable::getInstance( $this->_columns['table_object'][0], $this->_columns['table_object'][1] );
		
		$author_id 		=	0; // TODO: Get default author id
		$parent_id		=	0; // TODO: Get default parent_id
		
		// Set the author_id
		if ( isset( $this->_columns['author'] ) && $this->_columns['author'] && isset( $data_content[$this->_columns['author']] ) ) {
			$author_id	=	$data_content[$this->_columns['author']];
		} else {
			$user_id	=	JFactory::getUser()->get( 'id' );
			
			if ( $user_id ) {
				$author_id	=	$user_id;
			}
		}
		
		// Set the parent_id
		if ( isset( $this->_columns['parent'] ) && $this->_columns['parent'] && isset( $data_content[$this->_columns['parent']] ) ) {
			$parent_id	=	$data_content[$this->_columns['parent']];
		}
		
		// -------- -------- --------
		if ( !( $this->save( 'core', $data_content ) ) ) {
			return false;
		}
		
		if ( !( $this->save( 'base', array(
							'cck'=>$this->_type,
							'pk'=>$this->_pk,
							'storage_location'=>$this->_object,
							'author_id'=>$author_id,
							'parent_id'=>$parent_id,
							'date_time'=>JFactory::getDate()->toSql()
						 ) ) ) ) {
			return false;
		}
	
		if ( is_array( $data_more ) && count( $data_more ) ) {
			$this->_instance_more	=	JCckTable::getInstance( '#__cck_store_form_'.$this->_type );
			$this->_instance_more->load( $this->_pk, true );
			unset( $data_more['id'] );
			
			if ( !( $this->save( 'more', $data_more ) ) ) {
				return false;
			}
		}
		
		//TODO : Load instance info
		return $this->_pk;
	}

	// delete
	public function delete()
	{
		if ( $this->_object == '' ) {
			return false;
		}
		if ( !( $this->_id && $this->_pk ) ) {
			return false;
		}

		$config	=	array(
						'author'=>$this->_instance_base->author_id,
						'type'=>$this->_instance_base->cck,
						'type_id'=>0
					);
		$nb		=	0;

		if ( $config['type'] ) {
			$config['type_id']	=	JCckDatabaseCache::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$config['type'].'"' );
		}
		if ( !$config['type_id'] ) {
			return false;
		}

		JPluginHelper::importPlugin( 'content' );

		if ( JCck::callFunc_Array( 'plgCCK_Storage_Location'.$this->_object, 'onCCK_Storage_LocationDelete', array( $this->_pk, &$config ) ) ) {
			$nb++;
		}
		
		return ( $nb ) ? true : false;
	}

	// get
	public function get( $name, $default = '' )
	{
		if ( isset( $this->_properties->$name ) ) {
			return $this->_properties->$name;
		}

		return $default;
	}
	
	// getId
	public function getId()
	{
		return $this->_id;
	}
	
	// getTable
	public function getTable()
	{
		return $this->_table;
	}

	// load
	public function load( $identifier, $data = true )
	{
		$this->_type	=	'';
		$this->_pk		=	'';
		$this->_id		=	'';
			
		if ( is_array( $identifier ) ) {
			$this->_object			=	$identifier[0];
			$this->_columns			=	$this->_getProperties();
			$this->_instance_core	=	JTable::getInstance( $this->_columns['table_object'][0], $this->_columns['table_object'][1] );
			
			if( !isset( $identifier[1] ) ) {
				return;
			}
			
			$base					=	JCckDatabase::loadObject( 'SELECT id, cck, pk, storage_location FROM #__cck_core WHERE storage_location = "'.(string)$identifier[0].'" AND pk = '.(int)$identifier[1] );
		} else {
			$base					=	JCckDatabase::loadObject( 'SELECT id, cck, pk, storage_location FROM #__cck_core WHERE id = '.(int)$identifier );
			$this->_object			=	$base->storage_location;
			$this->_columns			=	$this->_getProperties();
			$this->_instance_core	=	JTable::getInstance( $this->_columns['table_object'][0], $this->_columns['table_object'][1] );
		}
		if ( !( @$base->id && @$base->pk ) ) {
			return false;
		}
		
		$this->_type				=	$base->cck;
		$this->_pk					=	$base->pk;
		$this->_id					=	$base->id;
		$this->_instance_base->load( $this->_id );
		$this->_instance_core->load( $this->_pk );
		$this->_instance_more		=	JCckTable::getInstance( '#__cck_store_form_'.$this->_type );
		$this->_instance_more->load( $this->_pk );
		
		if ( !$this->_columns['table'] ) {
			return;
		}
		
		$this->_table	=	$this->_columns['table'];
		
		if ( $data === true ) {
			$this->_properties	=	JCckDatabase::loadObject( 'SELECT a.*, b.* FROM '.$this->_table.' AS a'
															. ' LEFT JOIN #__cck_store_form_'.$this->_type.' AS b ON b.id = a.'.$this->_columns['key']
															. ' WHERE a.'.$this->_columns['key'].' = '.(int)$this->_pk );
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
				$join	.=	' LEFT JOIN '.$k.' AS '.$a.' ON '.$a.'.id = '.$b.'.'.$this->_columns['key'];
				$b		=	$a;
				$i++;
			}
			$query	=	'SELECT a.'.$select.' FROM '.$this->_table.' AS a'
					.	$join
					.	' WHERE a.'.$this->_columns['key'].' = '.(int)$this->_pk;
			$this->_properties	=	JCckDatabase::loadObject( $query );
		}
	}

	// bind
	public function bind( $instance_name, $data )
	{
		return $this->{'_instance_'.$instance_name}->bind( $data );
	}
	
	// store
	public function store( $instance_name )
	{
		return $this->{'_instance_'.$instance_name}->store();
	}
	
	// save
	public function save( $instance_name, $data )
	{
		$status	=	$this->{'_instance_'.$instance_name}->bind( $data );
		$status	=	$this->{'_instance_'.$instance_name}->check();
		$status	=	$this->{'_instance_'.$instance_name}->store();
		
		if ( $status ) {
			switch( $instance_name ) {
				case 'base':
					$this->_id	=	$this->{'_instance_'.$instance_name}->id;
					if ( property_exists( $this->_instance_core, $this->_columns['custom'] ) ) {
						$this->_instance_core->{$this->_columns['custom']}	=	'::cck::'.$this->_id.'::/cck::';
					}
					$this->store( 'core' );
					break;
				case 'core':
					$this->_pk	=	$this->{'_instance_'.$instance_name}->id;
					break;
				case 'more':
					break;
			}
		}
		
		return $status;
	}
	
	// _getProperties
	protected function _getProperties()
	{	
		$values		=	array( 'author', 'custom', 'key', 'parent', 'table', 'table_object' );
		$properties	=	array();
		
		if ( is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$this->_object.'/'.$this->_object.'.php' ) ) {
			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$this->_object.'/'.$this->_object.'.php';
			$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$this->_object, 'getStaticProperties', $values );
		}
		
		return $properties;
	}
}
?>