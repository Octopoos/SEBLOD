<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: template.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableTemplate extends JTable
{	
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_templates', 'id', $db );
	}
	
	// check
	public function check()
	{
		$this->title	=	trim( $this->title );
		if ( empty( $this->title ) ) {
			return false;
		}
		if ( empty( $this->name ) ) {
			return false;
		}
		
		return true;
	}
	
	// delete
	public function delete( $pk = null )
	{
		if ( $this->id ) {
			// Delete Template
			// Delete Views
		}
		
		return parent::delete();
	}
}
?>