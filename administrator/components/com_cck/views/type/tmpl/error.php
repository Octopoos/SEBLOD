<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: error.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app	=	JFactory::getApplication();
$error	=	JText::_( $app->input->get( 'error', '...' ) ) .'<br />'. '<strong>Oops!</strong> Try to close the page & re-open it properly.';
echo JText::sprintf( $error, $this->item->id );
?>