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
class JCckContentJoomla_Article extends JCckContent
{
	// getInstance
	public static function getInstance( $identifier = '', $data = true )
	{
		if ( !$identifier ) {
			return new JCckContentJoomla_Article;
		}

		$key	=	( is_array( $identifier ) ) ? implode( '_', $identifier ) : $identifier;
		if ( !isset( self::$instances[$key] ) ) {
			$instance	=	new JCckContentJoomla_Article( $identifier );
			self::$instances[$key]	=	$instance;
		}

		return self::$instances[$key];
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
			$test		=	JTable::getInstance( 'content' );
			
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