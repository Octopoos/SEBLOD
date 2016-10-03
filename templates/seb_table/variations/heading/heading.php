<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: index.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$column		=	'';
if ( $options->get( 'order_by' ) ) {
	$column	=	$options->get( 'order_by_fieldname' );
}
if ( $column == '' ) {
	$fields	=	$cck->getFields( $position, '', false );
	$column	=	$fields[0];
}
$name		=	$options->get( 'ordering' );
$position_	=	$cck->getPosition( $position );
$type		=	$options->get( 'type' );

// Prepare
$class		=	$position_->css;
if ( $type == 'ordering' ) {
	$order		=	'';
	$order_dir	=	'';
	$ordering	=	$cck->getValue( $name );
	if ( $ordering && strpos( $ordering, ':' ) !== false ) {
		$order		=	explode( ':', $ordering );
		$order_dir	=	$order[1];
		$order		=	$order[0];
	}
	$value		=	$column.':'.( ( $order == $column ) ? ( ( $order_dir == 'asc' ) ? 'desc' : 'asc' ) : $options->get( 'order_dir', 'asc' ) );
	
	JHtml::_( 'bootstrap.tooltip' );
	
	if ( $column == $order ) {
		$legend	.=	'<i class="icon-arrow-'.( ( $order_dir == 'asc' ) ? 'up' : 'down' ).'-3"></i>';
	}

	$tooltip	=	JHtml::tooltipText( '', 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN' );
	$attr		=	'onclick="jQuery(\'#'.$name.'\').val(\''.$value.'\'); JCck.Core.submit(\'search\'); return false;" class="hasTooltip" title="'.$tooltip.'"';
	$legend		=	'<a href="javascript:void(0);" '.$attr.'>'.$legend.'</a>';
} elseif ( $type == 'selection' ) {
	$legend		=	'<input type="checkbox" name="toggle" value="" title="'.JText::_( 'JGLOBAL_CHECK_ALL' ).'" onclick="Joomla.checkAll(this);">';	
}

// Set
if ( $options->get( 'display', 0 ) ) {
	$index		=	$position;	// $column
	$trigger	=	$options->get( 'display_trigger', '' );
	if ( $trigger ) {
		$values	=	explode( ',', $cck->getValue( $trigger ) );
		if ( !in_array( $index, $values ) ) {
			$class	.=	' hide';
		}
	}
}

$class	=	( $class ) ? ' class="'.$class.'"' : '';
$width	=	$cck->w( $position );
$width	=	( $width ) ? ' width="'.$width.'"' : ''; // ( $width ) ? ' style="width:'.$width.'"' : '';

echo '<th'.$class.$width.'>'.$legend.'</th>';
?>