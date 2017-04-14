<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: search.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableSearch extends JTable
{
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_searchs', 'id', $db );
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
		
		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			$clients	=	array( 'search', 'filter', 'list', 'item' );
			foreach ( $clients as $client ) {
				$Pf	=	'template_'.$client;
				$style	=	JCckDatabase::loadObject( 'SELECT a.id, a.template FROM #__template_styles AS a'
													. ' WHERE a.template IN ( SELECT b.template FROM #__template_styles as b WHERE b.id = '.(int)$this->$Pf.' )'
													. ' ORDER BY a.id' );
				if ( $style->id != $this->$Pf ) {
					JCckDatabase::execute( 'DELETE a.* FROM #__template_styles AS a WHERE a.id='.(int)$this->$Pf );
				}
			}
			
			JCckDatabase::execute( 'DELETE IGNORE a.*, b.*'
								 . ' FROM #__cck_core_search_field AS a'
								 . ' LEFT JOIN #__cck_core_search_position AS b ON b.searchid = a.searchid'
								 . ' WHERE a.searchid='.(int)$this->id );
		}
		
		return parent::delete();
	}
}
?>