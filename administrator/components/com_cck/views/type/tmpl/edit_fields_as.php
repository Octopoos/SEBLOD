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

$ijk    =   0;
$style	=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide' );
Helper_Workshop::displayHeader( 'type', $this->item->master );
echo '<ul class="sortable connected" id="sortable1" myid="1">';
if ( $this->item->location == 'collection' ) {
	echo '<li class="position p-no ui-state-disabled boundary" id="pos-1"><input class="selector" type="radio" id="position0" name="positions" gofirst="#pos-1" golast="#pos-2"><span class="title"></span><input type="hidden" name="ff[pos-_main_]" value="position" /></li>';

	foreach ( $this->fields as $pos ) {
		foreach ( $pos as $field ) {
			$field->position    =   '_main_';
			$type_field         =   '';
			if ( isset( $this->type_fields[$field->id] ) ) {
				$type_field =   ' c-'.$this->type_fields[$field->id]->cc;
			}
			JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
			Helper_Workshop::displayField( $field, $type_field, $attr );
		}
	}
	$this->positions_nb =   0;
} else {
	echo '<li class="position p-no ui-state-disabled boundary" id="pos-0"><input class="selector" type="radio" id="position0" name="positions" gofirst="#pos-0" golast="#pos-1"><span class="title"></span><input type="hidden" name="ff[pos-_pre_]" value="position" /></li>';

	if ( isset( $this->fields['_pre_'] ) ) {
		foreach ( $this->fields['_pre_'] as $field ) {
			$type_field     =   '';
			if ( isset( $this->type_fields[$field->id] ) ) {
				$type_field =   ' c-'.$this->type_fields[$field->id]->cc;
			}
			JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
			Helper_Workshop::displayField( $field, $type_field, $attr );
		}
	}
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
						$ijk++;
	}
	foreach ( $positions as $pos ) {
		$this->setPosition( $pos['name'], $pos['title'] );
	}
	$ijk++;

	echo '<li class="position p-no ui-state-disabled boundary" id="pos-'.$ijk.'"><input class="selector" type="radio" id="position0" name="positions" gofirst="#pos-'.$ijk.'" golast="#pos-'.( $ijk + 1 ).'"><span class="title"></span><input type="hidden" name="ff[pos-_post_]" value="position" /></li>';

	if ( isset( $this->fields['_post_'] ) ) {
		foreach ( $this->fields['_post_'] as $field ) {
			$type_field     =   '';
			if ( isset( $this->type_fields[$field->id] ) ) {
				$type_field =   ' c-'.$this->type_fields[$field->id]->cc;
			}
			JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
			Helper_Workshop::displayField( $field, $type_field, $attr );
		}
	}
}
Helper_Workshop::displayPositionEnd( ++$this->positions_nb );
echo '</ul>';
?>