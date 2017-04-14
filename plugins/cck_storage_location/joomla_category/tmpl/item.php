<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: item.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Init
if ( isset( $this ) && isset( $this->config ) ) {
	$config		=	&$this->config;
}

// Prepare
$item->params	=	is_object( $params ) ? clone $params : new JRegistry;
if ( !$loaded ) {
 	$items		=	JCckDatabase::loadObjectList( 'SELECT a.id, b.title AS parent_title, b.alias AS parent_alias'
												. ' FROM #__categories AS a LEFT JOIN #__categories AS b ON b.id = a.parent_id'
												. ' WHERE a.id IN ('.(string)$pks.')', 'id' );
	$loaded		=	1;
}
if ( isset( $items[$item->pk]->parent_title ) ) {
	$item->parent_title	=	$items[$item->pk]->parent_title;
}
if ( isset( $items[$item->pk]->parent_alias ) ) {
	$item->parent_alias	=	$items[$item->pk]->parent_alias;
}
$link		=	plgCCK_Storage_LocationJoomla_Category::getRoute( $item, $config['doSEF'], $config['Itemid'], $config );
?>

<div>
	<?php
	if ( $item->params->get( 'show_category_title' ) ) {
		$tag	=	$plg_params->get( 'item_tag_title', 'h2' );
		$class	=	$plg_params->get( 'item_class_title', '' );
		$class	=	$class ? ' class="'.$class.'"' : '';
		echo '<'.$tag.$class.'>'.'<a href="'.$link.'">'.$item->title.'</a>'.'</'.$tag.'>';
	}
	echo JHtml::_( 'content.prepare', $item->description );
	?>
</div>
<?php if ( $plg_params->get( 'item_separator', 1 ) ) { ?>
<hr />
<?php } ?>