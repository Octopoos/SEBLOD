<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: xml.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

echo trim( str_replace( array( '<!-- Begin: SEBLOD 3.x Document -->', '<!-- End: SEBLOD 3.x Document -->' ), '', $this->data ) );
?>