<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_( 'behavior.keepalive' );

$app	=	Factory::getApplication();
$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';

if ( !$this->raw_rendering ) { ?>
<div <?php echo $id; ?>class="cck_page"><div>
<?php } elseif ( trim( $this->pageclass_sfx ) ) { ?>
<div class="<?php echo trim( $this->pageclass_sfx ); ?>">
<?php }
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_form_title ) {
	$tag		=	$this->tag_form_title;
	$class		=	trim( $this->class_form_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . $this->title . '</'.$tag.'>';
}
if ( $this->show_form_desc == 1 && $this->description != '' ) {
	$description	=	HTMLHelper::_( 'content.prepare', $this->description );
	$tag_desc		=	'';

	if ( $this->tag_desc == 'div_div' ) {
		$tag_desc	=	'div';
	}
	if ( !( $this->tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$this->tag_desc	=	'div';
	}
	if ( !$this->raw_rendering ) {
		$description	=	'<'.$this->tag_desc.' class="cck_page_desc'.$this->pageclass_sfx.'">' . $description . '</'.$this->tag_desc.'>';

		if ( $this->tag_desc == 'div' ) {
			$description	.=	'<div class="clr"></div>';
		}
	} else {
		$class			=	trim( $this->class_desc );
		$class			=	$class ? ' class="'.$class.'"' : '';

		if ( $tag_desc == 'div' ) {
			$description	=	'<div>'.$description.'</div>';
		}
		$description	=	'<'.$this->tag_desc.$class.'>' . $description . '</'.$this->tag_desc.'>';
	}
}
if ( $this->show_form_desc == 1 && $this->description != '' ) {
	echo $description;
}
if ( isset( $this->config['error'] ) && (int)$this->config['error'] == 1 ) { ?>
	<?php if ( !$this->raw_rendering ) { ?>
		</div></div>
	<?php }
	return;
}
echo $this->loadTemplate( 'form' );
if ( $this->show_form_desc == 2 && $this->description != '' ) {
	echo ( $this->raw_rendering ) ? HTMLHelper::_( 'content.prepare', $this->description ) : '<div class="cck_page_desc'.$this->pageclass_sfx.'">' . HTMLHelper::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
?>
<?php if ( !$this->raw_rendering ) { ?>
</div></div>
<?php } elseif ( trim( $this->pageclass_sfx ) ) { ?>
</div>
<?php }
if ( $app->input->get( 'tmpl', '' ) == 'raw' ) { ?>
<script>
	jQuery(function ($) {
		$(".hasPopover").popover({"html": true,"trigger": "hover","container": "body"}).on("hidden", function (e) {
		   e.stopPropagation();
		});
	});
</script>
<?php } ?>