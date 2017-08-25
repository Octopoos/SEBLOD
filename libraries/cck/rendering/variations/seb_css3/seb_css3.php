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

// Init
if ( $app->isClient( 'administrator' ) ) {
	$background_color	=	'#ffffff';
	$position_padding	=	'15px';
} else {
	$background_color	=	'none';
	$position_padding	=	'5px';
}

// Prepare Style
if ( $hasOptions ) {
	if ( $app->isClient( 'administrator' ) ) {
		$background_color	=	$options->get( 'background_color', $background_color );
		$background_color	=	( $background_color == 'none' ) ? '#ffffff' : $background_color;
		$position_padding	=	$options->get( 'position_padding', $position_padding );
	} else {
		$background_color	=	$options->get( 'background_color', $background_color );
		$position_padding	=	$options->get( 'position_padding', $position_padding );
	}
	$border_size			=	$options->get( 'border_size', '0' );
	$border_style			=	$options->get( 'border_style', 'solid' );
	$border_color			=	$options->get( 'border_color', '#dedede' );
	$border_radius			=	$options->get( 'border_radius', '5' );
	$padding				=	( $position_padding != '' ) ? 'padding:'.$position_padding.';' : '';
	
	$css	=	'/* Variation: '.$variation.' on '.$id.' */' ."\n" . $css
			.	'#'.$id.'.'.$variation.' { background-color:'.$background_color.'; border:'.$border_size.'px '.$border_style.' '.$border_color.'; position:relative; '.$padding
			.	' -moz-border-radius:'.$border_radius.'px; -webkit-border-radius:'.$border_radius.'px; border-radius:'.$border_radius.'px; overflow:hidden; }'."\n";
} elseif ( !isset( $loaded[$variation] ) ) {
	$loaded[$variation]	=	true;
	$padding			=	( $position_padding != '' ) ? 'padding:'.$position_padding.';' : '';
	$css				=	'/* Variation: '.$variation.' */' ."\n" . $css
						.	'div.'.$variation.' { background-color:'.$background_color.'; border:0px solid #dedede; position:relative; '.$padding
						.	' -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; overflow:hidden; }'."\n";
}

// Set Style
$class	=	'cck-pos-'.$position.' '.$variation.' '.$orientation;
$cck->addCSS( $css );
$cck->setHeight( $height, $id );
?>
<div id="<?php echo $id; ?>" class="<?php echo $class .' '. $cck->id.'-deepest'; ?>">
	<?php if ( $legend != '' ) { ?>
	    <div class="legend top <?php echo $options->get( 'legend_align', 'left' ); ?>"><?php echo JText::_( $legend ); ?></div>
    <?php } ?>
	<?php echo $content; ?>    
</div>