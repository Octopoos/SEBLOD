<?php
defined( '_JEXEC' ) or die;

foreach ( $displayData['items'] as $item ) {
	echo '<div id="op-'.$item['name'].'" class="object-params w100"'.( $item['active'] ? '' : ' style="display:none;"' ).'>'
	 .	 '<div class="ghost">'
	 .	 $item['html']
	 .	'</div>'
	 .	 '</div>';
}
?>