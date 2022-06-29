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

$bar		=	( $this->uix == 'full' ) ? 'on' : 'off';
$data		=	Helper_Workshop::getParams( 'search', $this->item->master, $this->item->client );
$data2      =   array(
                    'construction'=>array(
                                        'access'=>array( '_' ),
                                        'link'=>array( '_' ),
                                        'live'=>array( '_' ),
                                        'markup'=>array( '_' ),
                                        'match_mode'=>array( '_' ),
                                        'restriction'=>array( '_' ),
                                        'stage'=>array( '_' ),
                                        'typo'=>array( '_' ),
                                        'variation'=>array( '_' )
                                    )
                );
$positions	=	array();
$attr       =   array( 'class'=>' b', 'span'=>'<span class="icon-pencil"></span>', 'user_id'=>JFactory::getUser()->id );
?>
<div class="<?php echo $this->css['wrapper2'].' '.$this->uix; ?>">
    <div class="<?php echo $this->css['w70']; ?>" id="seblod-main">
        <?php if ( JCck::on( '4.0' ) ) { ?>
            <fieldset class="options-form seblod">
                <?php include __DIR__.'/edit_fields_as.php'; ?>
            </fieldset>
        <?php } else { ?>
        <div class="seblod">
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_CONSTRUCTION_'.$this->uix ) . '<span class="mini">('.JText::_( 'COM_CCK_FOR_VIEW_'.$this->item->client ).')</span>'; ?></div>
            <?php include __DIR__.'/edit_fields_as.php'; ?>
        </div>
        <?php } ?>
    </div>
    
    <div class="<?php echo $this->css['w30'].' '.$bar; ?> active" id="seblod-sidebar">
        <?php if ( JCck::on( '4.0' ) ) { ?>
            <fieldset class="options-form seblod">
                <div class="fltlft seblod-toolbar"><?php Helper_Workshop::displayToolbar( 'search', $this->item->master, $this->item->client, $this->uix, '' ); ?></div>
                <div id="scroll">
                    <div>
                        <?php echo '<div class="row"><div class="col">'.$this->lists['af_f'].'</div><div class="col-auto">'.$this->lists['af_a'].'</div></div>'.$this->lists['af_c'].$this->lists['af_t']; ?>
                    </div>
                    <hr>
                    <ul class="sortable connected" id="sortable2" myid="2">
                        <?php include __DIR__.'/edit_fields_av.php'; ?>
                    </ul>
                </div>
                <div style="display: none;">
                    <ul id="sortable3"></ul>
                </div>
                <span class="expand-main">&nbsp;</span>
            </fieldset>
        <?php } else { ?>
            <div class="seblod" id="seblod-sideblock">
                <div class="fltlft seblod-toolbar"><?php Helper_Workshop::displayToolbar( 'search', $this->item->master, $this->item->client, $this->uix, '' ); ?></div>
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
        <?php } ?>
    </div>
</div>
<div class="clr" id="seblod-cleaner"></div>
<div id="layer_fields_options" class="hide hidden" style="display: none;">
    <?php
    if ( isset( $data2['construction'] ) && count( $data2['construction'] ) ) {
        foreach ( $data2['construction'] as $k=>$v ) {
            if ( count( $v ) ) {
                foreach ( $v as $k2=>$v2 ) {
                    if ( $k2 == '_' ) {
                        if ( isset( $data[$k] ) ) {
                            if ( $k == 'variation' ) {
                                 $data['variation']['300']  =   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAR_IS_SECURED' ) );
                                 $data['variation']['301']  =   JHtml::_( 'select.option', '</OPTGROUP>', '' );    
                            }
                            echo JHtml::_( 'select.genericlist', $data[$k], '_wk_'.$k, 'size="1" class="thin form-select xs hide" data-type="'.$k.'"', 'value', 'text', '' );
                        }
                    } else {
                        if ( count( $v2 ) ) {
                            echo JHtml::_( 'select.genericlist', $v2, '_wk_'.$k.'-'.$k2, 'size="1" class="thin form-select xs hide" data-type="'.$k.'"', 'value', 'text', '' );
                        }
                    }
                }
            }
        }
    }
    ?>
</div>