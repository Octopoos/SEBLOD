<?php
/**
* @version 			SEBLOD 3.x More ~ $Id: index.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$attributes		=	$cck->item_attributes ? ' '.$cck->item_attributes : '';
$class			=	trim( $cck->getStyleParam( 'class', '' ) );
$custom_attr	=	trim( $cck->getStyleParam( 'attributes', '' ) );
$custom_attr	=	$custom_attr ? ' '.$custom_attr : '';
$display_mode	=	(int)$cck->getStyleParam( 'list_display', '0' );
$tags			=	$cck->getStyleParam( 'tag', 'ul_li' );
$tags			=	explode( '_', $tags );
$html			=	'';
$id_class		=	$cck->id_class;
$items			=	$cck->getItems();
$fieldnames		=	$cck->getFields( 'element', '', false );
$multiple		=	( count( $fieldnames ) > 1 ) ? true : false;
$count			=	count( $items );
$auto_clean		=	(int)$cck->getStyleParam( 'auto_clean', 0 );

if ( $auto_clean == 2 ) {
	$isRaw		=	1;
} else {
	$isRaw		=	( $count == 1 ) ? $auto_clean : 0;
}

// Set
$isMore			=	$cck->isLoadingMore();
if ( $cck->isGoingToLoadMore() ) {
	$class		=	trim( $class.' '.'cck-loading-more' );
}
$class			=	str_replace( '$total', $count, $class );
$class			=	$class ? ' class="'.$class.'"' : '';

// -- Render
if ( $id_class && !$isMore ) {
?>
<div class="<?php echo trim( $cck->id_class ); ?>"><?php }
if ( !( $isRaw || $isMore ) ) { ?>
<<?php echo $tags[0]; ?><?php echo $class.$custom_attr; ?>>
<?php }
	if ( $count ) {
		if ( $display_mode == 2 ) {
			foreach ( $items as $item ) {
				$row	=	$item->renderPosition( 'element' );
				if ( $row && !$isRaw ) {
					$row	=	'<'.$tags[1].$item->replaceLive( $attributes ).'>'.$row.'</'.$tags[1].'>';
				}
				$html	.=	$row;
			}
		} elseif ( $display_mode == 1 ) {
			foreach ( $items as $pk=>$item ) {
				$row	=	$cck->renderItem( $pk );
				if ( $row && !$isRaw ) {
					$row	=	'<'.$tags[1].$item->replaceLive( $attributes ).'>'.$row.'</'.$tags[1].'>';
				}
				$html	.=	$row;
			}
		} else {
			foreach ( $items as $item ) {
				$row	=	'';
				foreach ( $fieldnames as $fieldname ) {
					$content	=	$item->renderField( $fieldname );
					if ( $content != '' ) {
						if ( $item->getMarkup( $fieldname ) != 'none' && ( $multiple || $item->getMarkup_Class( $fieldname ) ) ) {
							$row	.=	'<div class="cck-clrfix'.$item->getMarkup_Class( $fieldname ).'">'.$content.'</div>';
						} else {
							$row	.=	$content;
						}
					}
				}
				if ( $row && !$isRaw ) {
					$row	=	'<'.$tags[1].$item->replaceLive( $attributes ).'>'.$row.'</'.$tags[1].'>';
				}
	            $html	.=	$row;
			}
		}
		echo $html;
	}
if ( !( $isRaw || $isMore ) ) {
?>
</<?php echo $tags[0]; ?>>
<?php
}
if ( $id_class && !$isMore ) { ?>
</div>
<?php } ?>
<?php
// -- Finalize
$cck->finalize();
?>