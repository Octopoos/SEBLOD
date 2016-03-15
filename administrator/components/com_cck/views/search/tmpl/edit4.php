<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit4.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$data		=	Helper_Workshop::getParams( 'search', $this->item->master, $this->item->client );

if ( JCck::on() ) {
    $attr   =   array( 'class'=>' b', 'span'=>'<span class="icon-pencil-2"></span>' );
} else {
    $attr   =   array( 'class'=>' edit', 'span'=>'' );
}
include __DIR__.'/edit_fields_av.php';
?>