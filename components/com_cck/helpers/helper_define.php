<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_define.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// -------- -------- -------- -------- -------- -------- -------- -------- // Core

$root	=	JUri::root( true );

if ( ! defined( 'JROOT_CCK' ) ) {
	define( 'JROOT_CCK', $root );
}
if ( ! defined( 'JROOT_MEDIA_CCK' ) ) {
	define( 'JROOT_MEDIA_CCK', $root.'/media/cck' );
}
if ( ! defined( 'JPATH_LIBRARIES_CCK' ) ) {
	define( 'JPATH_LIBRARIES_CCK', JPATH_SITE.'/libraries/cck' );
}
?>