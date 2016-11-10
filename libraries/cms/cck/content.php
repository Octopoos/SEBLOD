<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: content.php oliviernolbert $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
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
	protected $_instance_more2		=	'';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// __construct
	public function __construct( $identifier = '', $data = true )
	{
		$this->_instance_core	=	JCckTable::getInstance( '#__cck_core', 'id' );
		
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
			$classname	=	'JCckContent';
			$object		=	'';

			if ( is_array( $identifier ) ) {
				if ( isset( $identifier[0] ) && $identifier[0] != '' ) {
					$object	=	$identifier[0];
				}
			} else {
				$object	=	'';
			}
			if ( $object && is_file( JPATH_SITE.'/plugins/cck_storage_location/'.$identifier[0].'/classes/content.php' ) ) {
				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/classes/content.php';
				
				$classname	=	'JCckContent'.$object;
			}

			self::$instances[$key]	=	new $classname( $identifier );
		}

		return self::$instances[$key];
	}
	
	// getInstanceBase
	protected function getInstanceBase()
	{
		return JTable::getInstance( $this->_columns['table_object'][0], $this->_columns['table_object'][1] );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Init

	// create
	public function create( $cck, $data_content, $data_more = null, $data_more2 = null )
	{
		if ( $this->_id ) {
			return;
		}
		
		$this->_type	=	$cck;
		
		if ( empty( $this->_object ) || empty( $this->_table ) ) {
			$this->_object		=	JCckDatabaseCache::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$this->_type.'"' );
			$this->_columns		=	$this->_getProperties();
			$this->_table		=	$this->_columns['table'];
			
			if ( !$this->_object ) {
				return;
			}
		}
		
		$this->_instance_base	=	$this->getInstanceBase();

		$author_id 		=	0; // TODO: Get default author id
		$parent_id		=	0; // TODO: Get default parent_id
		
		// Base
		if ( !( $this->save( 'base', $data_content ) ) ) {
			return false;
		}
		
		// Set the author_id
		if ( isset( $this->_columns['author'] ) && $this->_columns['author'] ) {
			if ( $this->_columns['author'] == $this->_columns['key'] ) {
				$author_id	=	$this->_instance_base->get( $this->_columns['key'], 0 );
			} elseif ( isset( $data_content[$this->_columns['author']] ) ) {
				$author_id	=	$data_content[$this->_columns['author']];
			}
		}
		if ( !$author_id ) {
			$user_id		=	JFactory::getUser()->id;
			
			if ( $user_id ) {
				$author_id	=	$user_id;
			}
		}
		
		// Set the parent_id
		if ( isset( $this->_columns['parent'] ) && $this->_columns['parent'] && isset( $data_content[$this->_columns['parent']] ) ) {
			$parent_id	=	$data_content[$this->_columns['parent']];
		}

		// Core
		if ( !( $this->save( 'core',
							 array(
								'cck'=>$this->_type,
								'pk'=>$this->_pk,
								'storage_location'=>$this->_object,
								'author_id'=>$author_id,
								'parent_id'=>$parent_id,
								'date_time'=>JFactory::getDate()->toSql()
						   ) ) ) ) {
			return false;
		}
		
		// More
		if ( is_array( $data_more ) && count( $data_more ) ) {
			$this->_instance_more	=	JCckTable::getInstance( '#__cck_store_form_'.$this->_type );
			$this->_instance_more->load( $this->_pk, true );
			unset( $data_more['id'] );
			
			if ( !( $this->save( 'more', $data_more ) ) ) {
				return false;
			}
		}
		if ( is_array( $data_more2 ) && count( $data_more2 ) ) {
			$this->_instance_more2	=	JCckTable::getInstance( '#__cck_store_item_'.str_replace( '#__', '', $this->_table ) );
			$this->_instance_more2->load( $this->_pk, true );

			if ( !isset( $data_more2['cck'] ) ) {
				$data_more2['cck']	=	$this->_type;
			}
			unset( $data_more2['id'] );
			
			if ( !( $this->save( 'more2', $data_more2 ) ) ) {
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
						'author'=>$this->_instance_core->author_id,
						'type'=>$this->_instance_core->cck,
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

	// dump
	public function dump()
	{
		if ( $this->_instance_base ) {
			dump( $this->_instance_base );
		}
		if ( $this->_instance_core ) {
			dump( $this->_instance_core );
		}
		if ( $this->_instance_more ) {
			dump( $this->_instance_more );
		}
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

	// getPk
	public function getPk()
	{
		return $this->_pk;
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
			$this->_instance_base	=	$this->getInstanceBase();
			
			if( !isset( $identifier[1] ) ) {
				return;
			}
			
			$core					=	JCckDatabase::loadObject( 'SELECT id, cck, pk, storage_location FROM #__cck_core WHERE storage_location = "'.(string)$identifier[0].'" AND pk = '.(int)$identifier[1] );
		} else {
			$core					=	JCckDatabase::loadObject( 'SELECT id, cck, pk, storage_location FROM #__cck_core WHERE id = '.(int)$identifier );
			$this->_object			=	$core->storage_location;
			$this->_columns			=	$this->_getProperties();
			$this->_instance_base	=	$this->getInstanceBase();
		}
		if ( !( @$core->id && @$core->pk ) ) {
			return false;
		}
		
		$this->_type				=	$core->cck;
		$this->_pk					=	$core->pk;
		$this->_id					=	$core->id;
		$this->_instance_core->load( $this->_id );
		$this->_instance_base->load( $this->_pk );
		
		if ( !$this->_columns['table'] ) {
			return;
		}
		
		$this->_table		=	$this->_columns['table'];
		$suffixMore2		=	str_replace( '#__', '', $this->_table );
		
		$tables				=	JCckDatabaseCache::getTableList( true );
		$hasMore			=	isset( $tables[JFactory::getConfig()->get( 'dbprefix' ).'cck_store_form_'.$this->_type] );
		$hasMore2			=	isset( $tables[JFactory::getConfig()->get( 'dbprefix' ).'cck_store_item_'.$suffixMore2] );
		
		if ( $hasMore ) {
			$this->_instance_more	=	JCckTable::getInstance( '#__cck_store_form_'.$this->_type );
			$this->_instance_more->load( $this->_pk );
		}
		if ( $hasMore2 ) {
			$this->_instance_more2	=	JCckTable::getInstance( '#__cck_store_item_'.$suffixMore2 );
			$this->_instance_more2->load( $this->_pk );
		}

		if ( $data === true ) {
			$select				=	'';
			$join				=	'';
			if ( $hasMore ) {
				$select			.=	', b.*';
				$join			.=	' LEFT JOIN #__cck_store_form_'.$this->_type.' AS b ON b.id = a.'.$this->_columns['key'];
			}
			if ( $hasMore2 ) {
				$select			.=	', c.*';
				$join			.=	' LEFT JOIN #__cck_store_item_'.$suffixMore2.' AS c ON c.id = a.'.$this->_columns['key'];
			}
			$query				=	'SELECT a.*'.$select
								.	' FROM '.$this->_table.' AS a'
								.	$join
								.	' WHERE a.'.$this->_columns['key'].' = '.(int)$this->_pk;
			$this->_properties	=	JCckDatabase::loadObject( $query );
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

	// set
	public function set( $instance_name, $property, $value )
	{
		$this->{'_instance_'.$instance_name}->$property	=	$value;
	}

	// setType
	public function setType( $cck, $reload = true )
	{
		$this->_instance_core->cck	=	$cck;
		$this->_type				=	$cck;

		if ( $reload ) {
			$this->_instance_more	=	JCckTable::getInstance( '#__cck_store_form_'.$this->_type );
			$this->_instance_more->load( $this->_pk );
		}
	}

	// updateType
	public function updateType( $cck )
	{
		$this->_instance_core->cck	=	$cck;
		$this->_type				=	$cck;

		$this->_instance_core->store();
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Store

	// bind
	public function bind( $instance_name, $data )
	{
		return $this->{'_instance_'.$instance_name}->bind( $data );
	}

	// check
	public function check( $instance_name )
	{
		return $this->{'_instance_'.$instance_name}->check();
	}

	// postSave
	public function postSave( $instance_name, $data )
	{
		switch( $instance_name ) {
			case 'base':
				$this->_pk	=	$this->{'_instance_'.$instance_name}->{$this->_columns['key']};
				
				if ( $this->_instance_core->id ) {
					$data_core	=	array();

					if ( $this->_columns['author'] == $this->_columns['key'] ) {
						$data_core['author_id']	=	$this->{'_instance_'.$instance_name}->get( $this->_columns['key'], 0 );
					} elseif ( isset( $data[$this->_columns['author']] ) ) {
						$data_core['author_id']	=	$data[$this->_columns['author']];
					}
					if ( isset( $data[$this->_columns['parent']] ) ) {
						$data_core['parent_id']	=	$data[$this->_columns['parent']];
					}
					if ( count( $data_core ) ) {
						$this->save( 'core', $data_core );
					}
				}
				break;
			case 'core':
				$this->_id	=	$this->{'_instance_'.$instance_name}->id;
				
				if ( property_exists( $this->_instance_base, $this->_columns['custom'] ) ) {
					$this->_instance_base->{$this->_columns['custom']}	=	'::cck::'.$this->_id.'::/cck::';
				}
				$this->store( 'base' );
				break;
			case 'more':
			case 'more2':
			default:
				break;
		}
	}
	
	// preSave
	public function preSave( $instance_name, $data )
	{
	}
	
	// save
	public function save( $instance_name, $data = array() )
	{
		$this->preSave( $instance_name, $data );

		$status		=	$this->bind( $instance_name, $data );
		$status		=	$this->check( $instance_name );

		if ( get_class( $this ) == 'JCckContent' ) {
			$status	=	$this->_saveLegacy( $instance_name, $data );
		} else {
			$method	=	'save'.ucfirst( $instance_name );
			$status	=	$this->$method();
		}

		if ( $status ) {
			$this->postSave( $instance_name, $data );
		}
		
		return $status;
	}

	// saveBase
	public function saveBase()
	{
		return $this->store( 'base' );
	}

	// saveCore
	public function saveCore()
	{
		return $this->store( 'core' );
	}

	// saveMore
	public function saveMore()
	{
		return $this->store( 'more' );
	}

	// saveMore2
	public function saveMore2()
	{
		return $this->store( 'more2' );
	}

	// store
	public function store( $instance_name )
	{
		return $this->{'_instance_'.$instance_name}->store();
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Others
	
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

	// _saveLegacy (deprecated)
	protected function _saveLegacy( $instance_name, $data )
	{
		/* TODO: this is no good, and will need to move, but later! */
		if ( $instance_name == 'base' ) {
			if ( property_exists( $this->{'_instance_'.$instance_name}, 'language' ) && $this->{'_instance_'.$instance_name}->language == '' ) {
				$this->{'_instance_'.$instance_name}->language	=	'*';
			}
			$status			=	$this->store( $instance_name );

			if ( !$this->_pk && !$status && ( $this->_object == 'joomla_article' || $this->_object == 'joomla_category' ) ) {
				$i			=	2;
				$alias		=	$this->{'_instance_'.$instance_name}->alias.'-'.$i;
				$property	=	$this->_columns['parent'];
				$test		=	JTable::getInstance( 'content' );
				
				while ( $test->load( array( 'alias'=>$alias, $property=>$this->{'_instance_'.$instance_name}->{$property} ) ) ) {
					$alias	=	$this->{'_instance_'.$instance_name}->alias.'-'.$i++;
				}
				$this->{'_instance_'.$instance_name}->alias	=	$alias;

				$status		=	$this->store( $instance_name );

				/* TODO: publish_up */
			}
		} else {
			$status			=	$this->store( $instance_name );
		}
		/* TODO: this is no good, and will need to move, but later! */

		return $status;
	}
}
?>