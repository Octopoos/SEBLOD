<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Model
class CCKModelForm extends JModelLegacy
{
	// store
	function store( $preconfig, $task = '' )
	{
		$client	=	'admin';	// admin or admin_edit
		
		jimport( 'cck.base.form.form' );
		include_once JPATH_LIBRARIES_CCK.'/base/form/store_inc.php';
		
		return $config;
	}
}
?>
