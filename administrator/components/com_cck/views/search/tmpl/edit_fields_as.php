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

$style	=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide', '7'=>' hide' );
Helper_Workshop::displayHeader( 'search', $this->item->master );
echo '<ul class="sortable connected" id="sortable1" myid="1">';
if ( $this->item->client == 'order' ) {
	Helper_Workshop::displayPositionStatic( 1, 'mainbody', '# '.JText::_( 'COM_CCK_ORDER_BY' ) );
    if ( isset( $this->fields['mainbody'] ) ) {
        foreach ( $this->fields['mainbody'] as $field ) {
            $type_field		=	'';
            if ( isset( $this->type_fields[$field->id] ) ) {
                $type_field	=	' c-'.$this->type_fields[$field->id]->cc;
            }
            JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Search'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
            Helper_Workshop::displayField( $field, $type_field, $attr );
        }
    }
	Helper_Workshop::displayPositionEnd();
} else {
    if ( $this->item->client == 'list' && ! $this->item->template ) {
        echo '<li class="position ui-state-disabled" id="pos-1"><span class="title capitalize"># '.JText::_( 'COM_CCK_SELECT_LIST_TEMPLATE' ).'</span></li>';
		Helper_Workshop::displayPositionEnd();
    } else {
		if ( $this->positions_nb ) {
			foreach ( $this->positions as $pos ) {
				if ( isset( $this->fields[$pos->name] ) ) {
					$this->setPosition( $pos->name, @$pos->title );
					foreach ( $this->fields[$pos->name] as $field ) {
						$type_field		=	'';
						if ( isset( $this->type_fields[$field->id] ) ) {
							$type_field	=	' c-'.$this->type_fields[$field->id]->cc;
						}
						JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Search'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
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
		} else {
			echo '<li class="position ui-state-disabled" id="pos-1"><span class="title capitalize"># '.JText::_( 'COM_CCK_NO_POSITION_AVAILABLE' ).'</span></li>';
			Helper_Workshop::displayPositionEnd();
		}
    }
}
echo '</ul>';
?>