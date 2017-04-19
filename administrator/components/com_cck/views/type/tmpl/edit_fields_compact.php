<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit_fields_compact.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$bar		=	( $this->uix == 'full' ) ? 'on' : 'off';
$data		=	Helper_Workshop::getParams( 'type', $this->item->master, $this->item->client );
$clone		=	'';
$positions	=	array();
$attr       =   array( 'class'=>' b', 'span'=>'<span class="icon-pencil-2"></span>' );
?>
<div class="seb-wrapper <?php echo $this->uix; ?>">
    <div class="width-70 fltlft" id="seblod-main">
        <div class="seblod">
            <div id="linkage_wrap"><?php echo JCckDev::getForm( $cck['core_linkage'], 1, $config ); ?></div>
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONSTRUCTION_'.$this->uix ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
            <?php
			$style	=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide' );
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
			$i	=	0;
			$n	=	count( $positions );
			if ( $this->p <= 6 ) {
				$p	=	$this->p;
				for ( $i = 0; $p <= 5; $i++,$p++ ) {
					$this->setPosition( $positions[$i] );
				}
			}
			Helper_Workshop::displayPositionEnd( $this->positions_nb );
            echo '</ul>';
            ?>
        </div>
        <div class="seblod">
            <span class="legend top left"><?php echo JText::_( 'COM_CCK_AVAILABLE_FIELDS' ); ?></span><span class="toggle" id="more_fields">Toggle</span>
            <?php
            if ( count( $this->fieldsAv ) ) {
                echo '<div class="legend top center">'.$this->lists['af_t'].$this->lists['af_c'].$this->lists['af_f'].$this->lists['af_a'].'</div>';
                echo '<div id="scroll"><ul class="sortable connected" id="sortable2" myid="2">';
                $style	=	array( '1'=>' hide', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide' );
                foreach ( $this->fieldsAv as $field ) {
                    $type_field	=	'';
                    if ( isset( $this->type_fields[$field->id] ) ) {
                        $type_field	=	' c-'.$this->type_fields[$field->id]->cc;
                    }
                    JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Type'.$this->item->master, array( &$field, $style, $data ) );
                    Helper_Workshop::displayField( $field, $type_field, $attr );
                }
                echo '</ul></div><div id="sortable_original" style="display: none;"></div>';
            }
            ?>
        </div>
        <div class="seblod">
            <span class="legend top left"><?php echo JText::_( 'COM_CCK_AVAILABLE_POSITIONS' ); ?></span><span class="toggle" id="more_positions">Toggle</span>
            <ul class="more_pos">
            <?php 
			for ( ; $i < $n; $i++ ) {
				$this->setPosition( $positions[$i] );
			}
            ?>
            </ul>
        </div>
    </div>
    
    <div class="width-30 <?php echo $bar; ?> active" id="seblod-sidebar">
        <div class="seblod" id="seblod-sideblock">
            <div class="fltlft seblod-toolbar"><?php Helper_Workshop::displayToolbar( 'type', $this->item->master, $this->item->client, $this->uix, $clone ); ?></div>
        </div>
    </div>
</div>
<div class="clr" id="seblod-cleaner"></div>

<script type="text/javascript">
jQuery(document).ready(function($){
	$("div#scroll").slideToggle();
	$(document).on("click", "#more_positions", function() {
		$("ul.more_pos").slideToggle();
	});
	$(document).on("click", "#more_fields", function() {
		$("div#scroll").slideToggle();
	});
	$(document).on("change", ".filter", function() {
		if(this.value) {
			$("div#scroll").slideDown();
		}
	});
});
</script>