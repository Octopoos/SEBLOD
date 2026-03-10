<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Layout\FileLayout;

// Init
$pos_markup	=	$options->get( 'position_markup', '' );

// Set
if ( $pos_markup ) {
	$displayData	=	array(
							'cck'=>$this,
							'field'=>null,
							'html'=>$content,
							'options'=>null
						);

	$layout 		=	new FileLayout( 'cck.markup.'.$pos_markup, null, array( 'client'=>0, 'component'=>'com_cck' ) );
	$html			=	$layout->render( $displayData );

	echo $html;
} else {
	echo $content;
}
?>