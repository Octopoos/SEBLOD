<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: processing.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

// JCckProcessing
class JCckProcessingContent extends JCckProcessing
{
	protected static $apps		=	array();
	protected static $apps_map	=	array();

	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct

	// execute
	public function execute( &$content_instance, &$data )
	{
		if ( !is_file( $this->_path ) ) {
			return;
		}

		$this->_data		=&	$data;
		$this->_instance	=&	$content_instance;
		$this->_pk			=	$content_instance->getPk();
		$this->_type		=	$content_instance->getType();

		$this->run();

		return $this->_error ? false : true;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Check

	// isNew
	public function isNew()
	{
		return $this->_instance->isNew();
	}

	// isUi
	public function isUi()
	{
		return false;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Do

	// setError
	public function setError( $key = '', $value = '' )
	{
		// JCckTable::getInstance( '#__aa' )->save( array( 'data'=>'** ERROR **', 'note'=>$key.' -- '.$value ) );

		$this->_error	=	true;

		if ( $key != '' ) {
			$this->_instance->log( 'error', array( $key=>$value ) );
		} else {
			$this->_instance->log( 'error', 'Unknown Error.' );
		}
	}

	// setType
	public function setType( $content_type )
	{
		$this->_instance->setType( $content_type );
	}

	// setValue
	public function setValue( $name, $value )
	{
		$field	=	$this->loadField( $name );

		if ( is_object( $field ) ) {
			$this->_instance->setProperty( $field->storage_field, $value );

			if ( $this->isNew() ) {
				$map_instance	=	$this->_instance->_getDataMapInstance( $field->storage_field );

				$this->_data[$map_instance][$field->storage_field]	=	$value;
			} elseif ( isset( $this->_data[$field->storage_field] ) ) {
				unset( $this->_data[$field->storage_field] );
			}
			
			if ( stripos( $this->_event, 'afterStore' ) !== false && $this->_pk ) {
				$this->_instance->store();
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Get

	// getId
	public function getId()
	{
		return $this->_instance->getId();
	}

	// getObject
	public function getObject()
	{
		return $this->_instance->getObject();
	}

	// getPk
	public function getPk()
	{
		return $this->_instance->getPk();
	}

	// getType
	public function getType()
	{
		return $this->_type;
	}

	// getValue
	public function getValue( $name )
	{
		$field	=	$this->loadField( $name );

		if ( is_object( $field ) ) {
			if ( $this->_pk ) {
				return $this->_instance->getProperty( $field->storage_field );
			} else {
				if ( $field->storage_table == $this->_instance->getTable() ) {
					return isset( $this->_data['base'][$field->storage_field] ) ? $this->_data['base'][$field->storage_field] : '';
				} else {
					static $names	=	array(
											'more'=>'',
											'more_parent'=>'',
											'more2'=>'',
										);
			
					foreach ( $names as $table_instance_name=>$null ) {
						if ( isset( $this->_data[$table_instance_name][$field->storage_field] ) ) {
							return $this->_data[$table_instance_name][$field->storage_field];
						}
					}
				}
			}
		}

		return '';
	}
}
?>