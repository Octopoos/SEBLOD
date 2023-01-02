<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folder.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableFolder extends JTable
{
	// __construct
	public function __construct( &$db )
	{				
		parent::__construct( '#__cck_core_folders', 'id', $db );
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