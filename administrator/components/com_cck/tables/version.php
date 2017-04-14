<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: version.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Table
class CCK_TableVersion extends JTable
{	
	// __construct
	function __construct( &$db )
	{
		parent::__construct( '#__cck_core_versions', 'id', $db );
	}
	
	// delete
	public function delete( $pk = null )
	{
		return parent::delete();
	}
}
?>