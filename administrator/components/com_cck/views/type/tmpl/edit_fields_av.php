<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_fields_av.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( count( $this->fieldsAv ) ) {
	$style	=	array( '1'=>' hide', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide' );
    foreach ( $this->fieldsAv as $field ) {
        $type_field	=	'';
        
        if ( isset( $this->type_fields[$field->id] ) ) {
            $type_field	=	' c-'.$this->type_fields[$field->id]->cc;
        }
        JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data, &$data2 ) );
        Helper_Workshop::displayField( $field, $type_field, $attr );
    }
}
?>