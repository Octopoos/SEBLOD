<?php
defined( '_JEXEC' ) or die;

$items	=	array();

if ( isset( $displayData['grid'] ) ) {
	if ( strpos( $displayData['grid'], '|' ) !== false ) {
		$items	=	explode( '|', $displayData['grid'] );
	}
	foreach ( $items as $k=>$item ) {
		if ( $item == '' ) {
			continue;
		}

		$idx	=	strlen( $item ) - 1;

		if ( $item[$idx] == '%' ) {
			$item		=	substr( $item, 0, -1 );
			$items[$k]	=	round( 12 * (int)$item / 100 );
		} elseif ( $item[$idx] == 'auto' ) {
			$items[$k]	=	'auto';
		}
	}
}

echo '<div class="row row-grid">';

foreach ( $displayData['html'] as $k=>$html ) {
	$class	=	'';

	if ( isset( $items[$k] ) && $items[$k] ) {
		$class	=	'-'.$items[$k];
	}

	echo '<div class="col'.$class.'">';
	echo $html;
	echo '</div>';
}

echo '</div>';
?>