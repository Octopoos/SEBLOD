<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>
<div class="cck_module_breadcrumbs<?php echo $class_sfx; ?>">
<?php
$show_last	=	$params->get( 'showLast', 1 );
if ( $params->get( 'showHere', 1 ) ) {
		echo '<span class="showHere">'.JText::_( 'MOD_CCK_BREADCRUMBS_HERE' ).'</span>';
}
for ( $i = 0; $i < $count; $i++ ) {
	if ( $i < $count -1 ) { // If not the last item in the breadcrumbs add the separator
		if ( !empty($list[$i]->link ) ) {
			echo '<a href="'.$list[$i]->link.'" class="pathway">'.$list[$i]->name.'</a>';
		} else {
			echo '<span class="pathway">'.$list[$i]->name.'</span>';
		}
		if( $i < $count -2 ) {
			echo '<span class="'.$separator_class.'">'.$separator.'</span>';
		}
	} elseif ( $show_last ) { // when $i == $count -1 and 'showLast' is true
		if ( $i > 0 ) {
			echo '<span class="'.$separator_class.'">'.$separator.'</span>';
		}
		echo '<span class="pathway-last">'.$list[$i]->name.'</span>';
	}
}
?>
</div>