<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: language.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$mixin	=	new class() {
	// _getLanguageAssociation
	protected function _getLanguageAssociation()
	{
		return function( $lang_tag ) {
			$associations	=	$this->_getLanguageAssociations();

			return isset( $associations[$lang_tag] ) ? $associations[$lang_tag] : 0;
		};
	}

	// _getLanguageAssociations
	protected function _getLanguageAssociations()
	{
		return function() {
			$associations	=	array();
			$items			=	JLanguageAssociations::getAssociations( 'com_cck', $this->getTable(), 'com_cck.free.'.$this->getTable(), $this->getPk(), 'id', '', '' );

			foreach ( $items as $lang_tag=>$item ) {
				$associations[$lang_tag]	=	$item->id;
			}

			return $associations;
		};
	}

	// _hasLanguageAssociation
	protected function _hasLanguageAssociation()
	{
		return function( $lang_tag ) {
			$associations	=	$this->_getLanguageAssociations();

			return isset( $associations[$lang_tag] ) ? true : false;
		};
	}

	// _setLanguageAssociations
	protected function _storeLanguageAssociations()
	{
		return function( $associations ) {
			$context	=	'com_cck.free.'.$this->getTable();

			if ( !( is_array( $associations ) && count( $associations ) ) ) {
				return false;
			}
			
			foreach ( $associations as $tag=>$id ) {
				if ( empty( $id ) ) {
					unset( $associations[$tag] );
				}

				$associations[$tag] 	=	(int)$associations[$tag];
			}
			
			$db			=	JFactory::getDbo();
			$lang_tag	=	$this->getProperty( 'language' );

			if ( !isset( $associations[$lang_tag] ) ) {
				$associations[$lang_tag]	=	(int)$this->getPk();
			}

			// Delete
			$query	=	$db->getQuery( true )
						   ->delete( '#__associations' )
						   ->where( 'context = ' . $db->quote( $context ) )
						   ->where( 'id IN (' . implode(',', $associations ) . ')' );

			$db->setQuery( $query );
			$db->execute();

			// Insert
			$key	=	md5( json_encode( $associations ) );
			$query->clear()
				  ->insert( '#__associations' );

			foreach ( $associations as $tag=>$id ) {
				$query->values( $id . ',' . $db->quote( $context ) . ',' . $db->quote( $key ) );
			}

			$db->setQuery( $query );
			$db->execute();
		};
	}

	// _translate
	protected function _translate()
	{
		return function( $lang_tag, $lang_from = 'EN' ) {
			$associations	=	$this->_getLanguageAssociations();
			$data			=	$this->getData();
			$pk				=	$this->getPk();

			if ( !( isset( $data['language'] ) && $data['language'] !== '' && $data['language'] !== '*' ) ) {
				return false;
			}

			$data['language']	=	$lang_tag;

			if ( isset( $data['title'] ) ) {
				$data['title']	=	str_replace( '['.$lang_from.']', '['.substr( $lang_tag, 3 ).']', $data['title'] );
			}

			$this->create( $this->getType(), $data );

			if ( $this->isSuccessful() ) {
				if ( !count( $associations ) ) {
					$associations['en-GB']	=	$pk;
				}

				$associations[$lang_tag]	=	$this->getPk();

				$this->_storeLanguageAssociations( $associations );
			} else {
				return false;
			}

			return $this->getPk() !== $pk ? $this->getPk() : false;
		};
	}
}
?>