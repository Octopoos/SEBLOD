<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit2.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( $this->function ) {
	$this->onceFile( 'require' );
	$this->function( $this->item->title, $this->item->name, $this->item->type, $this->item->params );
} else {
	$this->onceFile( 'include' );
}
?>