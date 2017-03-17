<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );

// JCckContent
class JCckContentJoomla_Category extends JCckContent
{
	// preSave
	public function preSave( $instance_name, &$data )
	{
		if ( $instance_name == 'base' ) {
			if ( !( isset( $data['extension'] ) && $data['extension'] != '' ) ) {
				$data['extension']	=	'com_content';
			}
			if ( !isset( $data['parent_id'] ) ) {
				$data['parent_id']	=	( $this->getPk() ) ? $this->{'_instance_'.$instance_name}->parent_id : 1;
			}
			if ( !$this->getPk() || ( $data['parent_id'] != $this->{'_instance_'.$instance_name}->parent_id ) ) {
				$this->{'_instance_'.$instance_name}->setLocation( $data['parent_id'], 'last-child' );
			}
		}
	}

	// saveBase
	public function saveBase()
	{
		if ( property_exists( $this->_instance_base, 'language' ) && $this->_instance_base->language == '' ) {
			$this->_instance_base->language	=	'*';
		}
		$status			=	$this->store( 'base' );

		if ( !$this->_pk && !$status ) {
			$i			=	2;
			$alias		=	$this->_instance_base->alias.'-'.$i;
			$property	=	$this->_columns['parent'];
			$test		=	JTable::getInstance( 'category' );
			
			while ( $test->load( array( 'alias'=>$alias, $property=>$this->_instance_base->{$property} ) ) ) {
				$alias	=	$this->_instance_base->alias.'-'.$i++;
			}
			$this->_instance_base->alias	=	$alias;

			$status		=	$this->store( 'base' );

			/* TODO: publish_up */
		}

		return $status;
	}
	
	// postSave
	public function postSave( $instance_name, $data )
	{
		if ( $instance_name == 'base' ) {
			$this->{'_instance_'.$instance_name}->rebuildPath( $this->getPk() );
		}
	}
}
?>