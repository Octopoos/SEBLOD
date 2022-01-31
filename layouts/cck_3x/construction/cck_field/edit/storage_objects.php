<?php
defined( '_JEXEC' ) or die;

foreach ( $displayData['items'] as $item ) {
	echo '<li id="op-'.$item['name'].'" class="object-params w100"'.( $item['active'] ? '' : ' style="display:none;"' ).'>'
	 .	 '<ul class="ghost">'
	 .	 $item['html']
	 .	 '</ul>'
	 .	 '</li>';
}
?>