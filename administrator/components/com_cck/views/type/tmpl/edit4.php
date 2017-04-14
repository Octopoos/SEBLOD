<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit4.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$data		=	Helper_Workshop::getParams( 'type', $this->item->master, $this->item->client );
$data2      =   array(
                    'construction'=>array(
                                        'access'=>array( '_' ),
                                        'link'=>array( '_' ),
                                        'live'=>array( '_' ),
                                        'markup'=>array( '_' ),
                                        'restriction'=>array( '_' ),
                                        'stage'=>array( '_' ),
                                        'typo'=>array( '_' ),
                                        'variation'=>array( '_' )
                                    )
                );
$attr       =   array( 'class'=>' b', 'span'=>'<span class="icon-pencil-2"></span>' );

include __DIR__.'/edit_fields_av.php';

if ( isset( $data2['construction'] ) && count( $data2['construction'] ) ) {
    foreach ( $data2['construction'] as $k=>$v ) {
        if ( count( $v ) ) {
            foreach ( $v as $k2=>$v2 ) {
                if ( $k2 != '_' ) {
                    if ( count( $v2 ) ) {
                        echo JHtml::_( 'select.genericlist', $v2, '_wk_'.$k.'-'.$k2, 'size="1" class="thin hide" data-type="'.$k.'"', 'value', 'text', '' );
                    }
                }
            }
        }
    }
}
?>