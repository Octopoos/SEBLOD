<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: site.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableSite extends JTable
{	
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_sites', 'id', $db );
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		$this->name	=	str_replace( 'http://', '', $this->name );
		$this->name	=	( $this->name[strlen( $this->name )-1] == '/' ) ? substr( $this->name, 0, -1 ) : $this->name;
		if ( empty( $this->name ) ) {
			return false;
		}
		
		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		return parent::delete();
	}
}
?>