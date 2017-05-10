<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: type.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableType extends JTable
{
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_types', 'id', $db );
	}
	
	// _getAssetName
	protected function _getAssetName()
	{
		$k	=	$this->_tbl_key;
		return 'com_cck.form.'.(int)$this->$k;
	}

	// _getAssetParentId
	protected function _getAssetParentId( JTable $table = null, $id = null )
	{
		return $this->_getAssetParentId2( $table, $id );
	}

	// _getAssetTitle
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	// _getAssetParentId2
	protected function _getAssetParentId2( $table, $id )
	{
		$assetId	=	null;
		$db			=	$this->getDbo();
		
		if ( $this->folder ) {
			$db->setQuery( 'SELECT asset_id FROM #__cck_core_folders WHERE id = '.(int)$this->folder );
			$assetId	=	$db->loadResult();
		}
		
		if ( ! $assetId ) {
			$db->setQuery( 'SELECT id FROM #__assets WHERE name = "com_cck"' );
			$assetId	=	$db->loadResult();
		}
		
		return $assetId;
	}
	
	// check
	public function check()
	{
		$date	=	JFactory::getDate();
		$user	=	JFactory::getUser();

		$this->title			=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		if ( empty( $this->name ) ) {
			$this->name			=	$this->title;
			$this->name 		=	JCckDev::toSafeSTRING( $this->name );
			if( trim( str_replace( '_', '', $this->name ) ) == '' ) {				
				$this->name 	=	$date->format( 'Y_m_d_H_i_s' );
			}
		}

		if ( $this->id ) {
			$this->modified_date		=	$date->toSql();
			$this->modified_user_id		=	$user->id;
		} else {
			if ( !(int)$this->created_date ) {
				$this->created_date		=	$date->toSql();
			}
			if ( empty( $this->created_user_id ) ) {
				$this->created_user_id	=	$user->id;
			}
		}

		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			$clients	=	array( 'admin', 'site', 'content', 'intro' );
			foreach ( $clients as $client ) {
				$Pf	=	'template_'.$client;
				$style	=	JCckDatabase::loadObject( 'SELECT a.id, a.template FROM #__template_styles AS a'
													.	' WHERE a.template IN ( SELECT b.template FROM #__template_styles as b WHERE b.id = '.(int)$this->$Pf.' )'
													.	' ORDER BY a.id' );
				if ( is_object( $style ) && $style->id != $this->$Pf ) {
					JCckDatabase::execute( 'DELETE a.* FROM #__template_styles AS a WHERE a.id='.(int)$this->$Pf );
				}
			}
			
			JCckDatabase::execute( 'DELETE IGNORE a.*, b.*'
							.	' FROM #__cck_core_type_field AS a'
							.	' LEFT JOIN #__cck_core_type_position AS b ON b.typeid = a.typeid'
							.	' WHERE a.typeid='.(int)$this->id );
			
			JCckDatabase::execute( 'DELETE IGNORE a.*'
							.	' FROM #__cck_core AS a'
							.	' WHERE a.storage_location="cck_type" AND a.pk="'.(int)$this->id.'"' );
		}
		
		return parent::delete();
	}
}
?>