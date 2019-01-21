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

if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	include_once __DIR__ . '/content_placeholder.php';
} else {
	include_once __DIR__ . '/content_placeholder_legacy.php';
}

// JCckContent
class JCckContentJoomla_Category extends JCckContentJoomla_CategoryPlaceholder
{
	// initialize
	protected function initialize()
	{
		JPluginHelper::importPlugin( 'content' );

		JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );
	}

	// preSave
	protected function preSave( $table_instance_name, &$data )
	{
		if ( $table_instance_name == 'base' ) {
			if ( !( isset( $data['extension'] ) && $data['extension'] != '' ) ) {
				$data['extension']	=	'com_content';
			}
			if ( !isset( $data['parent_id'] ) ) {
				$data['parent_id']	=	( $this->getPk() ) ? $this->_instance_base->parent_id : 1;
			}
			if ( !$this->getPk() || ( $data['parent_id'] != $this->_instance_base->parent_id ) ) {
				$this->_instance_base->setLocation( $data['parent_id'], 'last-child' );
			}
		}
	}

	// saveBase
	protected function saveBase()
	{
		if ( property_exists( $this->_instance_base, 'language' ) && $this->_instance_base->language == '' ) {
			$this->_instance_base->language	=	'*';
		}
		$status			=	$this->_instance_base->store();

		if ( !$this->_pk && !$status ) {
			$i			=	2;
			$alias		=	$this->_instance_base->alias.'-'.$i;
			$property	=	self::$objects[$this->_object]['properties']['parent'];
			$test		=	JTable::getInstance( 'Category' );
			
			while ( $test->load( array( 'alias'=>$alias, $property=>$this->_instance_base->$property ) ) ) {
				$alias	=	$this->_instance_base->alias.'-'.$i++;
			}
			$this->_instance_base->alias	=	$alias;

			$status		=	$this->_instance_base->store();

			/* TODO#SEBLOD: publish_up */
		}

		return $status;
	}

	// triggerSave
	public function triggerSave( $event )
	{
		$result	=	$this->_dispatcher->trigger( self::$objects[$this->_object]['properties']['events'][$event], array( self::$objects[$this->_object]['properties']['context'], $this->_instance_base, $this->_is_new ) );

		if ( $event == 'afterSave' ) {
			unset( $this->_instance_base->catid );
		}

		return $result;
	}
	
	// postSave
	protected function postSave( $table_instance_name, $data )
	{
		if ( $table_instance_name == 'base' ) {
			$this->_instance_base->rebuildPath( $this->getPk() );
		}
	}
}
?>