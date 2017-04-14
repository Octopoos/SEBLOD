<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folder.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( version_compare( JVERSION, '3.2', 'ge' ) ) {
	// TableAdapter
	class CCK_TableFolderAdapter extends JTable
	{
		// _getAssetParentId
		protected function _getAssetParentId( JTable $table = null, $id = null )
		{
			return $this->_getAssetParentId2( $table, $id );
		}
	}
} else {
	// TableAdapter
	class CCK_TableFolderAdapter extends JTable
	{
		// _getAssetParentId
		protected function _getAssetParentId( $table = null, $id = null )
		{
			return $this->_getAssetParentId2( $table, $id );
		}
	}
}

// Table
class CCK_TableFolder extends CCK_TableFolderAdapter
{
	// __construct
	function __construct( &$db )
	{				
		parent::__construct( '#__cck_core_folders', 'id', $db );
	}
	
	// _getAssetName
	protected function _getAssetName()
	{
		$k	=	$this->_tbl_key;
		
		return CCK_COM.'.folder.'.(int)$this->$k;
	}

	// _getAssetTitle
	protected function _getAssetTitle()
	{
		return $this->title;
	}
	
	// _getAssetParentId2
	protected function _getAssetParentId2( $table, $id )
	{
		$assetId	=	0;
		$k			=	$this->_tbl_key;
		$db			=	$this->getDbo();
		
		if ( $this->$k == 1 || $this->$k == 2 ) {
			$assetId	=	0;
		} else {
			require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_folder.php';
			$parentId	=	Helper_Folder::getParent( $this->$k );
			$db->setQuery( 'SELECT asset_id FROM #__cck_core_folders WHERE id = '.(int)$parentId );
			$assetId	=	$db->loadResult();
		}

		if ( ! $assetId ) {
			$query		=	'SELECT id FROM #__assets WHERE name = "com_cck"';
			$db->setQuery( $query );
			$assetId	=	$db->loadResult();
		}
		
		return $assetId;
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		if( empty( $this->name ) ) {
			$this->name	=	$this->title;
			$this->name =	JCckDev::toSafeSTRING( $this->name );
			if( trim( str_replace( '_', '', $this->name ) ) == '' ) {
				$datenow	=	JFactory::getDate();
				$this->name =	$datenow->format( 'Y_m_d_H_i_s' );
			}
		}
		
		$prefix	=	JCck::getConfig_Param( 'development_prefix', '' );
		if ( !$this->id && $prefix ) {
			$pos	=	strpos( $this->name, $prefix.'_' );
			if ( $this->home && ( $pos === false || $pos != 0 ) ) {
				$this->name	=	$prefix.'_'.$this->name;
			} elseif ( !$this->home && ( $pos !== false && $pos == 0 ) ) {
				$this->name	=	substr( $this->name, strlen( $prefix ) + 1 );
			}
		}
		
		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			$count	=	0;
			$types		=	JCckDatabase::loadResult( 'SELECT COUNT( a.folder ) FROM #__cck_core_types AS a WHERE folder = '.(int)$this->id.' GROUP BY a.folder' );
			$fields		=	JCckDatabase::loadResult( 'SELECT COUNT( a.folder ) FROM #__cck_core_fields AS a WHERE folder = '.(int)$this->id.' GROUP BY a.folder' );
			$searchs	=	JCckDatabase::loadResult( 'SELECT COUNT( a.folder ) FROM #__cck_core_searchs AS a WHERE folder = '.(int)$this->id.' GROUP BY a.folder' );
			$templates	=	JCckDatabase::loadResult( 'SELECT COUNT( a.folder ) FROM #__cck_core_templates AS a WHERE folder = '.(int)$this->id.' GROUP BY a.folder' );
			$count		=	$types + $fields + $searchs + $templates;
			if ( (int)$count > 0 ) {
				JFactory::getApplication()->enqueueMessage( JText::sprintf( 'COM_CCK_DELETE_FOLDER_NOT_ALLOWED', $this->title, $count ), 'error' );
				return false;
			} else {
				return parent::delete();
			}
		}
	}
}
?>