<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>
<div class="cck_module_breadcrumbs <?php echo $class_sfx; ?>">
	<ul class="list-inline" itemscope itemtype="http://schema.org/BreadcrumbList">
	<?php
	$show_last	=	$params->get( 'showLast', 1 );
	if ( $params->get( 'showHere', 1 ) ) {
			echo '<span class="showHere">'.JText::_( 'MOD_CCK_BREADCRUMBS_HERE' ).'</span>';
	}
	for ( $i = 0; $i < $count; $i++ ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		if ( $i < $count -1 ) { // If not the last item in the breadcrumbs add the separator
			if ( !empty($list[$i]->link ) ) {
				echo '<a itemscope itemtype="http://schema.org/Thing"
       itemprop="item" href="'.$list[$i]->link.'" class="pathway"><span itemprop="name">'.$list[$i]->name.'</span></a>';
			} else {
				echo '<span itemscope itemtype="http://schema.org/Thing" itemprop="item" class="pathway"><span itemprop="name">'.$list[$i]->name.'</span></span>';
			}
			echo '<meta itemprop="position" content="'.($i+1).'" />';
			echo '<span class="'.$separator_class.' ">'.$separator.'</span>';
			// if( $i < $count -2 ) {
			// }
		} elseif ( $show_last ) { // when $i == $count -1 and 'showLast' is true
			// if ( $i > 0 ) {
			// 	echo '<span class=" turd '.$separator_class.'">'.$separator.'</span>';
			// }
			echo '<span itemscope itemtype="http://schema.org/Thing" itemprop="item" class="pathway-last"><span itemprop="name">'.$list[$i]->name.'</span></span>';
		}
		echo '</li><wbr>';
	}
	?>
	</ul>
</div>