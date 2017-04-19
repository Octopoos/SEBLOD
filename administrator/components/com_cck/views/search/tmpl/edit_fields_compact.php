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
$data		=	Helper_Workshop::getParams( 'search', $this->item->master, $this->item->client );
$positions	=	array();
$attr       =   array( 'class'=>' b', 'span'=>'<span class="icon-pencil-2"></span>' );
?>
<div class="seb-wrapper <?php echo $this->uix; ?>">
    <div class="width-70 fltlft" id="seblod-main">
        <div class="seblod">
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONSTRUCTION_'.$this->uix ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
            <?php
			$style	=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide' );
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
                        JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Search'.$this->item->master, array( &$field, $style, $data ) );
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
								$this->setPosition( $pos->name );
								foreach ( $this->fields[$pos->name] as $field ) {
									$type_field		=	'';
									if ( isset( $this->type_fields[$field->id] ) ) {
										$type_field	=	' c-'.$this->type_fields[$field->id]->cc;
									}
									JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Search'.$this->item->master, array( &$field, $style, $data ) );
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
					} else {
						echo '<li class="position ui-state-disabled" id="pos-1"><span class="title capitalize"># '.JText::_( 'COM_CCK_NO_POSITION_AVAILABLE' ).'</span></li>';
						Helper_Workshop::displayPositionEnd();
					}
                }
            }
            echo '</ul>';
            ?>
        </div>
    </div>
    
    <div class="width-30 <?php echo $bar; ?> active" id="seblod-sidebar">
        <div class="seblod" id="seblod-sideblock">
            <div class="fltlft seblod-toolbar"><?php Helper_Workshop::displayToolbar( 'search', $this->item->master, $this->item->client, $this->uix, '' ); ?></div>
			<?php
			if ( count( $this->fieldsAv ) ) {
                echo '<div class="legend top center">'.$this->lists['af_t'].$this->lists['af_a'].'<br />'.$this->lists['af_c'].$this->lists['af_f'].'</div>';
                echo '<div id="scroll"><ul class="sortable connected" id="sortable2" myid="2">';
                $style	=	array( '1'=>' hide', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide' );
                foreach ( $this->fieldsAv as $field ) {
                    $type_field	=	'';
                    if ( isset( $this->type_fields[$field->id] ) ) {
                        $type_field	=	' c-'.$this->type_fields[$field->id]->cc;
                    }
                    JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_Search'.$this->item->master, array( &$field, $style, $data ) );
                    Helper_Workshop::displayField( $field, $type_field, $attr );
                }
                echo '</ul></div><div id="sortable_original" style="display: none;"></div>';
            }
            ?>
        </div>
    </div>
</div>
<div class="clr" id="seblod-cleaner"></div>