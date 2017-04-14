<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( $show_list_title ) {
	$tag		=	$tag_list_title;
	$class		=	trim( $class_list_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . @$search->title . '</'.$tag.'>';
}
if ( $show_list_desc && $description != '' ) {
	$description	=	JHtml::_( 'content.prepare', $description );
	
	if ( !( $tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$tag_desc	=	'div';
	}
	$description	=	'<'.$tag_desc.' class="cck_module_desc'.$class_sfx.'">' . $description . '</'.$tag_desc.'>';

	if ( $tag_desc == 'div' ) {
		$description	.=	'<div class="clr"></div>';
	}
}
if ( $show_list_desc == 1 && $description != '' ) {
	echo $description;
}
?>
<?php if ( !$raw_rendering ) { ?>
<div class="cck_module_list<?php echo $class_sfx; ?>">
<?php }
if ( $search->content > 0 ) {
	echo ( $raw_rendering ) ? $data : '<div>'.$data.'</div>';
} else {
	include __DIR__.'/default_items.php';
}
?>
<?php if ( $show_more_link ) { ?>
	<div class="more"><a<?php echo $show_more_class; ?> href="<?php echo $show_more_link; ?>"><?php echo $show_more_text; ?></a></div>
<?php } if ( !$raw_rendering ) { ?>
</div>
<?php }
if ( $show_list_desc == 2 && $description != '' ) {
	echo $description;
}
?>