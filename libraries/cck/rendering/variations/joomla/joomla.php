<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Prepare Style
if ( $app->isClient( 'site' ) ) {
	$border_size	=	'1';
	$border_style	=	'solid';
	$border_color	=	'#dddddd';	
	
	$css			.=	'fieldset#'.$id.' div.vertical div.cck_forms { float:left!important; width:100%!important; }'."\n";
	$css			.=	'fieldset#'.$id.' { border: '.$border_size.'px '.$border_style.' '.$border_color.'; padding:20px 15px 20px 15px; margin:10px 0 15px 0; overflow:hidden; }'."\n";
	$css			.=	'#'.$id.' legend { font-weight:bold; padding:3px 5px 3px 5px }'."\n";
}

// Set Style
$class	=	$variation.' '.$orientation;
$cck->addCSS( '/* Variation: '.$variation.' on '.$id.' */' ."\n" . $css  );
$cck->setHeight( $height, $id, $cck->id.'-deepest' );
?>
<fieldset id="<?php echo $id; ?>" class="adminform <?php echo $class; ?>">    
	<?php if ( $legend != '' ) { ?>
	    <legend><?php echo JText::_( $legend ); ?></legend>
    <?php } ?>
    <div class="<?php echo $class .' '. $cck->id.'-deepest'; ?>">
    	<div id="<?php echo $id; ?>_content">
		    <?php echo $content; ?>
	    </div>
    </div>
</fieldset>