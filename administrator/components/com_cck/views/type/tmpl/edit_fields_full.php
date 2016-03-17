<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_fields_full.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$bar		=	( $this->uix == 'full' ) ? 'on' : 'off';
$data		=	Helper_Workshop::getParams( 'type', $this->item->master, $this->item->client );
$from_view	=	( $this->item->master == 'content' ) ? ( ( $this->item->client == 'intro' ) ? 'CONTENT' : 'INTRO' ) : ( ( $this->item->client == 'admin' ) ? 'SITE_FORM' : 'ADMIN_FORM' );
$clone		=	( $this->item->id ) ? JText::sprintf( 'COM_CCK_GET_FIELDS_FROM_VIEW', JText::_( 'COM_CCK_'.$from_view ) ) : '';
$positions	=	array();

if ( JCck::on() ) {
    $attr   =   array( 'class'=>' b', 'span'=>'<span class="icon-pencil-2"></span>' );
} else {
    $attr   =   array( 'class'=>' edit', 'span'=>'' );
}
?>
<div class="<?php echo $this->css['wrapper2'].' '.$this->uix; ?>">
    <div class="<?php echo $this->css['w70']; ?>" id="seblod-main">
        <div class="seblod">
            <div id="linkage_wrap"><?php echo JCckDev::getForm( $cck['core_linkage'], 1, $config ); ?></div>
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONSTRUCTION_'.$this->uix ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
            <?php
			$style	=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide' );
            Helper_Workshop::displayHeader( 'type', $this->item->master );
            echo '<ul class="sortable connected" id="sortable1" myid="1">';
			foreach ( $this->positions as $pos ) {
				if ( isset( $this->fields[$pos->name] ) ) {
					$this->setPosition( $pos->name );
					foreach ( $this->fields[$pos->name] as $field ) {
						$type_field		=	'';
						if ( isset( $this->type_fields[$field->id] ) ) {
							$type_field	=	' c-'.$this->type_fields[$field->id]->cc;
						}
						JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data ) );
						Helper_Workshop::displayField( $field, $type_field, $attr );
					}
				} else {
					$positions[]	=	$pos->name;
				}
			}
			foreach ( $positions as $pos ) {
				$this->setPosition( $pos );
			}
			Helper_Workshop::displayPositionEnd( $this->positions_nb );
            echo '</ul>';
            ?>
        </div>
    </div>
    
    <div class="<?php echo $this->css['w30'].' '.$bar; ?> active" id="seblod-sidebar">
        <div class="seblod" id="seblod-sideblock">
            <div class="fltlft seblod-toolbar"><?php Helper_Workshop::displayToolbar( 'type', $this->item->master, $this->item->client, $this->uix, $clone ); ?></div>
            <div class="legend top flexenter"><?php echo $this->lists['af_f'].$this->lists['af_c'].'<br />'.$this->lists['af_t'].$this->lists['af_a']; ?></div>
            <div id="scroll">
            	<ul class="sortable connected" id="sortable2" myid="2">
                    <?php include __DIR__.'/edit_fields_av.php'; ?>
                </ul>
            </div>
            <div style="display: none;">
            	<ul id="sortable3"></ul>
            </div>
        </div>
    </div>
	<input type="hidden" id="fromclient" name="fromclient" value="0" />
</div>
<div class="clr" id="seblod-cleaner"></div>
<div class="hide hidden" style="display: none;">
    <?php
    $lists  =   array( 'access', 'restriction', 'stage' );

    if ( count( $lists ) ) {
        foreach ( $lists as $k=>$v ) {
            if ( isset( $data[$v] ) ) {
                echo JHtml::_( 'select.genericlist', $data[$v], '_wk_'.$v, 'size="1" class="thin hide" data-type="'.$v.'"', 'value', 'text', '' );   
            }
        }
    }
    ?>
</div>