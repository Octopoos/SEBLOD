<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_toolbar.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app	=	JFactory::getApplication();
if ( $app->input->get( 'tmpl', '' ) == 'component' ) {
	// Add Toolbar (Html)
}

if ( JCck::getConfig_Param( 'autosave', 0 ) ) {
	Helper_Include::autoSave( JCck::getConfig_Param( 'autosave_interval', 2 ) ); ?>
	<div id="ajaxToolbar" style="float: right; text-align: right; padding-right: 8px; padding-bottom: 8px; font-weight: bold;">
		<div style="float: left; padding-right: 8px;" id="ajaxMessage"></div>
	</div>
<?php } ?>