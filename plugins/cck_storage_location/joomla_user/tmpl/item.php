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
$item->password	=	'';
$item->params	=	is_object( $params ) ? clone $params : new JRegistry;
if ( !$loaded ) {
	if ( $item->params->get( 'link_titles' ) ) {
	 	$items	=	JCckDatabase::loadObjectList( 'SELECT a.id, a.language, b.title AS category_title, b.alias AS category_alias'
												. ' FROM #__content AS a LEFT JOIN #__categories AS b ON b.id = a.catid'
												. ' WHERE a.id IN ('.(string)$pkbs.')', 'id' );
	} else {
		$items	=	array();
	}
	$loaded		=	1;
}
if ( $plg_params->get( 'bridge' ) ) {
	if ( isset( $items[$item->pkb]->language ) ) {
		$item->language			=	$items[$item->pkb]->language;
	}
	if ( isset( $items[$item->pkb]->category_title ) ) {
		$item->category_title	=	$items[$item->pkb]->category_title;
	}
	if ( isset( $items[$item->pkb]->category_alias ) ) {
		$item->category_alias	=	$items[$item->pkb]->category_alias;
	}
	$item->pk	=	$item->pkb;
	$link		=	plgCCK_Storage_LocationJoomla_Article::getRoute( $item, $config['doSEF'], $config['Itemid'], $config );
	?>
	<div>
		<?php
		if ( $item->params->get( 'show_title' ) ) {
			$tag	=	$plg_params->get( 'item_tag_title', 'h2' );
			$class	=	$plg_params->get( 'item_class_title', '' );
			$class	=	$class ? ' class="'.$class.'"' : '';
			echo '<'.$tag.$class.'>'. ( ( $item->params->get( 'link_titles' ) ) ? '<a href="'.$link.'">'.$item->title.'</a>' : $item->title ) .'</'.$tag.'>';
		}
		echo JHtml::_( 'content.prepare', $item->introtext );
		?>
	</div>
<?php } else { ?>
	<div>
		<?php
		if ( $item->params->get( 'show_title' ) ) {
			$tag	=	$plg_params->get( 'item_tag_title', 'h2' );
			$class	=	$plg_params->get( 'item_class_title', '' );
			$class	=	$class ? ' class="'.$class.'"' : '';
			echo '<'.$tag.$class.'>'. $item->name .'</'.$tag.'>';
		}
		?>
	</div>
<?php } ?>
<?php if ( $plg_params->get( 'item_separator', 1 ) ) { ?>
<hr />
<?php } ?>