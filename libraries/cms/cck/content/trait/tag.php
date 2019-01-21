<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: tag.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( version_compare( PHP_VERSION, '5.4', '<=' ) ) {
	return;
}

// JCckContentTrait
trait JCckContentTraitTag
{
	// _tag
	protected function _tag( $tags, $replace = false )
	{
		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->can( 'save' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		if ( !is_array( $tags ) ) {
			$tags	=	array( $tags );
		}
		foreach ( $tags as $k=>$v ) {
			$tags[$k]	=	(string)$v;
		}

		if ( !$replace ) {
			$tagHelper	=	new JHelperTags;
			$oldTags	=	$tagHelper->getTagIds( $this->_pk, self::$objects[$this->_object]['properties']['context2'] );
			$oldTags	=	explode( ',', $oldTags );
			
			if ( $oldTags[0] != '' ) {
				$tags	=	array_unique( array_merge( $tags, $oldTags ) );
			}
		}

		$this->_instance_base->newTags	=	$tags;

		return $this->_instance_base->store();
	}

	// _untag
	protected function _untag( $tags = array() )
	{
		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->can( 'save' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		if ( !is_array( $tags ) ) {
			$tags	=	array( $tags );
		}
		foreach ( $tags as $k=>$v ) {
			$tags[$k]	=	(string)$v;
		}

		$tagHelper				=	new JHelperTags;
		$tagHelper->typeAlias	=	self::$objects[$this->_object]['properties']['context2'];
		$tagHelper->unTagItem( 0, $this->_instance_base, $tags );
	}
}
?>