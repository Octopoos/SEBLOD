<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_fields_full.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$style	=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide' );
Helper_Workshop::displayHeader( 'type', $this->item->master );
echo '<ul class="sortable connected" id="sortable1" myid="1">';
foreach ( $this->positions as $pos ) {
	if ( isset( $this->fields[$pos->name] ) ) {
		$this->setPosition( $pos->name, @$pos->title );
		foreach ( $this->fields[$pos->name] as $field ) {
			$type_field		=	'';
			if ( isset( $this->type_fields[$field->id] ) ) {
				$type_field	=	' c-'.$this->type_fields[$field->id]->cc;
			}
			JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
			Helper_Workshop::displayField( $field, $type_field, $attr );
		}
	} else {
		$positions[] =   array( 'name'=>$pos->name, 'title'=>$pos->title );
	}
}
foreach ( $positions as $pos ) {
	$this->setPosition( $pos['name'], $pos['title'] );
}
Helper_Workshop::displayPositionEnd( $this->positions_nb );
echo '</ul>';
?>