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

// JCckContent
class JCckContentJoomla_Category extends JCckContent
{
	// getInstance
	public static function getInstance( $identifier = '', $data = true )
	{
		if ( !$identifier ) {
			return new JCckContentJoomla_Category;
		}

		$key	=	( is_array( $identifier ) ) ? implode( '_', $identifier ) : $identifier;
		if ( !isset( self::$instances[$key] ) ) {
			$instance	=	new JCckContentJoomla_Category( $identifier );
			self::$instances[$key]	=	$instance;
		}

		return self::$instances[$key];
	}

	// preSave
	public function preSave( $instance_name, $data )
	{
		if ( $instance_name == 'base' ) {
			$this->{'_instance_'.$instance_name}->setLocation( $data['parent_id'], 'last-child' );
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
}
?>