<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: index.php alexandrelapoux $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$attributes =   $cck->id_attributes ? ' '.$cck->id_attributes : '';
$attributes =   $cck->replaceLive( $attributes );
$nLeft      =	$cck->countFields( 'left', true );
$nRight     =	$cck->countFields( 'right', true );
if ( $nLeft && $nRight ) {
	$css	=	'#'.$cck->id.'_m100.cck-m100 {margin: 0 '.$cck->getStyleParam( 'position_right' ).'px 0 '.$cck->getStyleParam( 'position_left' ).'px !important;}'."\n";
	$css	.=	'#'.$cck->id.' .cck-line-left {width: '.$cck->getStyleParam( 'position_left' ).'px;}'."\n";
	$css	.=	'#'.$cck->id.' .cck-line-right {width: '.$cck->getStyleParam( 'position_right' ).'px; margin-left: -'.( $cck->getStyleParam( 'position_left' ) + $cck->getStyleParam( 'position_right' ) ).'px;}';
} elseif ( $nLeft ) {
	$css 	=	'#'.$cck->id.'_m100.cck-m100 {margin: 0 0 0 '.$cck->getStyleParam( 'position_left' ).'px !important;}'."\n";
	$css	.=	'#'.$cck->id.' .cck-line-left {width: '.$cck->getStyleParam( 'position_left' ).'px;}';
} elseif ( $nRight ) {
	$css	=	'#'.$cck->id.'_m100.cck-m100 {margin: 0 '.$cck->getStyleParam( 'position_right' ).'px 0 0 !important;}'."\n";
	$css	.=	'#'.$cck->id.' .cck-line-right {width: '.$cck->getStyleParam( 'position_right' ).'px; margin-left: -'.( $cck->getStyleParam( 'position_right' ) ).'px;}';
} else {
	$css	=	'#'.$cck->id.'_m100.cck-m100 {margin: 0 0 0 '.$cck->getStyleParam( 'position_left' ).'px !important;}' ;
}
$cck->addStyleDeclaration( $css );

// -- Render
?>
<div id="<?php echo $cck->id; ?>" class="<?php echo $cck->id_class; ?>cck-f100 cck-pad-<?php echo $cck->getStyleParam( 'position_margin', '10' ); ?>"<?php echo $attributes; ?>>
	<div>
    <?php // header-line
    if ( $cck->getStyleParam( 'position_header' ) ) {
        if ( $n = $cck->countFields( 'header', true ) ) {
			echo $cck->renderPositions( 'header', $cck->getStyleParam( 'position_header_variation', '' ), $n );
        }
    } ?>
    <?php // if left-column || right-column
	if ( $nLeft || $nRight ) { ?>
	<div class="cck-f200">
		<div class="cck-m50">
			<div id="<?php echo $cck->id; ?>_m100" class="cck-m100">
    		<?php } ?>
				<?php // top-line
                if ( $cck->getStyleParam( 'position_top' ) ) {
                    if ( $n = $cck->countFields( 'top', true ) ) {
						echo $cck->renderPositions( 'top', $cck->getStyleParam( 'position_top_variation', '' ), $n, '' );
                    }
                } ?>
                <?php // body
                if ( $cck->countFields( 'body', true ) ) { ?>
                    <div class="cck-line-body">
                        <?php if ( $cck->countFields( 'sidebody-a' ) && !$cck->countFields( 'sidebody-b' ) ) { 
                            if ($cck->getStyleParam( 'position_sidebody_a' )) { ?>
                                <div class="cck-w30 cck-fl cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-a', '', $cck->h( 'sidebody-a' ) ); ?></div>
                                </div>
                            <?php } else { ?>
                                <div class="cck-w30 cck-fr cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-a', '', $cck->h( 'sidebody-a' ) ); ?></div>
                                </div>
                            <?php }?>
                        <?php } elseif ( !$cck->countFields( 'sidebody-a' ) && $cck->countFields( 'sidebody-b' ) ) { 
                            if ($cck->getStyleParam( 'position_sidebody_b' )) { ?>
                                <div class="cck-w30 cck-fl cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-b', '', $cck->h( 'sidebody-b' ) ); ?></div>
                                </div>
                            <?php } else { ?>
                                <div class="cck-w30 cck-fr cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-b', '', $cck->h( 'sidebody-b' ) ); ?></div>
                                </div>
                            <?php }?>
                        <?php } elseif ( $cck->countFields( 'sidebody-a' ) && $cck->countFields( 'sidebody-b' ) ) { 
                            if ( $cck->getStyleParam( 'position_sidebody_a' ) && $cck->getStyleParam( 'position_sidebody_b' ) ){ ?>
                                <div class="cck-w20 cck-fl cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-a', '', $cck->h( 'sidebody-a' ) ); ?></div>
                                </div>
                                <div class="cck-w20 cck-fl cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-b', '', $cck->h( 'sidebody-b' ) ); ?></div>
                                </div>
                            <?php } elseif ( $cck->getStyleParam( 'position_sidebody_a' ) && !$cck->getStyleParam( 'position_sidebody_b' ) ) { ?>
                                <div class="cck-w20 cck-fl cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-a', '', $cck->h( 'sidebody-a' ) ); ?></div>
                                </div>
                                <div class="cck-w20 cck-fr cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-b', '', $cck->h( 'sidebody-b' ) ); ?></div>
                                </div>
                            <?php } elseif ( !$cck->getStyleParam( 'position_sidebody_a' ) && $cck->getStyleParam( 'position_sidebody_b' ) ) { ?>
                                <div class="cck-w20 cck-fl cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-b', '', $cck->h( 'sidebody-b' ) ); ?></div>
                                </div>
                                <div class="cck-w20 cck-fr cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-a', '', $cck->h( 'sidebody-a' ) ); ?></div>
                                </div>
                            <?php } elseif ( !$cck->getStyleParam( 'position_sidebody_a' ) && !$cck->getStyleParam( 'position_sidebody_b' ) ) { ?>
                                <div class="cck-w20 cck-fr cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-b', '', $cck->h( 'sidebody-b' ) ); ?></div>
                                </div>
                                <div class="cck-w20 cck-fr cck-ptb">
                                    <div class="cck-plr"><?php echo $cck->renderPosition( 'sidebody-a', '', $cck->h( 'sidebody-a' ) ); ?></div>
                                </div>
                        <?php }} ?>
                        <?php if ( $cck->countFields( 'mainbody' ) ) { 
                        $a = ( $cck->countFields( 'sidebody-a' ) ) ? 1 : 0; $b = ( $cck->countFields( 'sidebody-b' ) ) ? 1 : 0; $c = ( $a + $b == 2 ) ? 60 : ( ( $a + $b == 1 ) ? 70 : 100 ); ?>
                            <?php // if left-column || right-column
							if ( $cck->countFields( 'sidebody-a' ) || $cck->countFields( 'sidebody-b' ) ) {?>
                            <div class="cck-w<?php echo $c; ?> cck-fl cck-ptb cck-body">                
							<?php } ?>
                                <?php if ( $cck->countFields( 'topbody' ) ) {?>
                                	<?php // if left-column || right-column
									if ( $cck->countFields( 'sidebody-a' ) || $cck->countFields( 'sidebody-b' ) ) {?>
                                   	<div class="cck-w100 cck-fl cck-pb">
                                    <?php } else {?>
                                   	<div class="cck-w100 cck-fl cck-ptb">
                                    <?php } ?>
                                        <div class="cck-plr"><?php echo $cck->renderPosition( 'topbody', '', $cck->h( 'topbody' ) ); ?></div>
                                    </div>
                                <?php } ?>
                                <?php if ( $cck->countFields( 'topbody' ) || $cck->countFields( 'bottombody' ) ) {?>
	                                <?php // if left-column || right-column
									if ( $cck->countFields( 'sidebody-a' ) || $cck->countFields( 'sidebody-b' ) ) {?>
    	                            <div class="cck-w100 cck-fl">
        	                        <?php } else {?>
            	                    <div class="cck-w100 cck-fl cck-ptb">
                	                <?php } ?>
                    	                <div class="cck-plr"><?php echo $cck->renderPosition( 'mainbody', '', $cck->h( 'mainbody' ) ); ?></div>
                                	</div>                                
                                <?php } else {?>
                                	<div class="cck-plr cck-ptb">
                        	                <?php echo $cck->renderPosition( 'mainbody', '', $cck->h( 'mainbody' ) ); ?>
                            	    </div>
                                <?php } ?>
                                <?php if ( $cck->countFields( 'bottombody' ) ) {?>
                                    <?php // if left-column || right-column
									if ( $cck->countFields( 'sidebody-a' ) || $cck->countFields( 'sidebody-b' ) ) {?>
	                                <div class="cck-w100 cck-fl cck-pt">
                                    <?php } else {?>
                                  	<div class="cck-w100 cck-fl cck-ptb">
                                    <?php } ?>
                                        <div class="cck-plr"><?php echo $cck->renderPosition( 'bottombody', '', $cck->h( 'bottombody' ) ); ?></div>
                                    </div>
                                <?php } ?>
                            <?php if ( $cck->countFields( 'sidebody-a' ) || $cck->countFields( 'sidebody-b' ) ) {?>    
                            </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="clr"></div>	
                    </div>
                <?php } ?>
                <?php // bottom-line
                if ( $cck->getStyleParam( 'position_bottom' ) ) {
                    if ( $n = $cck->countFields( 'bottom', true ) ) {
						echo $cck->renderPositions( 'bottom', $cck->getStyleParam( 'position_bottom_variation', '' ), $n );
                    }
                } ?>
        	<?php // if left-column || right-column
			if ( $nLeft || $nRight ) { ?>
			</div>
		</div>
		<?php } ?>
		<?php // left-column
        if ( $cck->getStyleParam( 'position_left' ) ) {
            if ( $nLeft ) {
				echo $cck->renderPositions( 'left', $cck->getStyleParam( 'position_left_variation', '' ), $nLeft, '100' );
            }
        } ?>
        <?php // right-column
        if ( $cck->getStyleParam( 'position_right' ) ) {
            if ( $nRight ) {
				echo $cck->renderPositions( 'right', $cck->getStyleParam( 'position_right_variation', '' ), $nRight, '100' );
            }
        } ?>
    <?php // if left-column || right-column
	if ( $nLeft || $nRight ) { ?>
    </div>
    <?php } ?>
	<?php // footer-line
    if ( $cck->getStyleParam( 'position_footer' ) ) {
        if ( $n = $cck->countFields( 'footer', true ) ) {
			echo $cck->renderPositions( 'footer', $cck->getStyleParam( 'position_footer_variation', '' ), $n );
        }
    } ?>
    <?php // hidden
    if ( $cck->countFields( 'modal' ) ) {
        JHtml::_( 'bootstrap.modal', 'collapseModal' );
        
        $class = $cck->getPosition( 'modal' )->css;
        $class = ( $class ) ? ' ' . $class : '';
        ?>
        <div class="modal hide fade<?php echo $class; ?>" id="collapseModal">
            <?php echo $cck->renderPosition( 'modal' ); ?>
        </div>
    <?php } ?>
	<?php // hidden
    if ( $cck->countFields( 'hidden' ) ) { ?>
        <div style="display: none;">
            <?php echo $cck->renderPosition( 'hidden' ); ?>
        </div>
    <?php } ?>
    <?php // debug
    if ( $cck->doDebug() ) { ?>
        <div class="cck-line-debug">	
            <div class="cck-w100 cck-fl cck-ptb">
                <div class="cck-plr"><?php echo $cck->renderPosition( 'debug', 'none' ); ?></div>
            </div>
        </div>            
    <?php } ?>
    </div>
</div>
<?php
// -- Finalize
$cck->finalize();
?>