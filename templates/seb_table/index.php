<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: index.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// -- Initialize
require_once dirname(__FILE__).'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

$attributes		=	$cck->item_attributes ? ' '.$cck->item_attributes : '';
$table_header	=	$cck->getStyleParam( 'table_header', 0 );
$table_layout	=	$cck->getStyleParam( 'table_layout', '' );
$table_width	=	0;
$isFixed		=	( $table_layout == 'fixed' ) ? 1 : ( ( $table_layout == 'calculated' ) ? 2 : 0 );
$class_body		=	'';
$class_table	=	trim( $cck->getStyleParam( 'class_table', 'category zebra table' ) );
$class_table	=	( $isFixed ) ? $class_table.' fixed' : $class_table;
$class_table	=	$class_table ? ' class="'.$class_table.'"' : '';
$class_row0		=	trim( $cck->getStyleParam( 'class_table_tr_even', 'cat-list-row%i' ) );
$class_row0		=	$class_row0 ? ' class="'.str_replace( '%i', '0', $class_row0 ).'"' : '';
$class_row1		=	trim( $cck->getStyleParam( 'class_table_tr_odd', 'cat-list-row%i' ) );
$class_row1		=	$class_row1 ? ' class="'.str_replace( '%i', '1', $class_row1 ).'"' : '';

$doc	=	JFactory::getDocument();
$doc->addStyleSheet( JURI::root( true ).'/templates/'.$cck->template. '/css/'.'style.css' );

// Set
$isMore			=	$cck->isLoadingMore();
if ( $cck->isGoingToLoadMore() ) {
	$class_body	=	' class="cck-loading-more"';
}

// -- Render
if ( !$isMore ) {
?>
<div id="<?php echo $cck->id; ?>" class="<?php echo $cck->id_class; ?>cck-f100 cck-pad-<?php echo $cck->getStyleParam( 'position_margin', '10' ); ?>">
    <div>
    <?php }
	$attr		=	array(
						'class'=>array(),
						'width'=>array()
					);
	$items		=	$cck->getItems();
	$positions	=	$cck->getPositions();
	unset( $positions['hidden'] );

	if ( !$isMore ) { ?>
		<table<?php echo $class_table; ?>>
		<?php
		}
		$head	=	'';
		$thead	=	false;
		foreach ( $positions as $name=>$position ) {
			$class					=	$position->css;
			$attr['class'][$name]	=	$class ? ' class="'.$class.'"' : '';
			$legend					=	( $position->legend ) ? $position->legend : ( ( $position->legend2 ) ? $position->legend2 : '' );
			$width					=	$cck->w( $name );
			if ( $legend || $width ) {
				if ( $legend ) {
					$thead	=	true;
				}
				if ( $position->variation != '' ) {
					$var		=	$cck->renderVariation( $position->variation, $legend, '', $position->variation_options, $name );
					$matches	=	array();
					preg_match( '#<th class="([a-zA-Z0-9\-\ _]*)"(.*)>#U', $var, $matches );
					if ( isset( $matches[1] ) && $matches[1] != '' ) {
						$class	=	$matches[1];
						if ( $isFixed == 2 && $width && strpos( ' '.$class.' ', ' hide ' ) === false ) {
							$table_width	+=	(int)$width;
						}
					} else {
						$class	=	'';
					}
					$attr['class'][$name]	=	$class ? ' class="'.$class.'"' : '';
					$attr['width'][$name]	=	( $width ) ? ' width="'.$width.'"' : ''; // ( $width ) ? ' style="width:'.$width.'"' : '';
					$head					.=	$var;
				} else {
					$attr['width'][$name]	=	( $width ) ? ' width="'.$width.'"' : ''; // ( $width ) ? ' style="width:'.$width.'"' : '';
					$head					.=	'<th'.$attr['class'][$name].$attr['width'][$name].'>'.$legend.'</th>';	
				}
			}
		}
        ?>
        <?php if ( $isMore < 1 && $head && $thead && ( $table_header == 0 || $table_header == 1 ) ) { ?>
        <thead>
            <tr><?php echo $head; ?></tr>
		</thead>
		<?php }
		if ( $isMore < 1 ) { ?>
			<tbody<?php echo $class_body; ?>>
        <?php
    	}
    	if ( count( $items ) ) {
			$i	=	0;
	        foreach ( $items as $item ) {
				?>
	            <tr <?php echo ${'class_row'.($i % 2)}.$item->replaceLive( $attributes ); ?>>
				<?php
	            foreach ( $positions as $name=>$position ) {
	                $fieldnames	=	$cck->getFields( $name, '', false );
	                $multiple	=	( count( $fieldnames ) > 1 ) ? true : false;
	                $html		=	'';
	                $width		=	'';
	                if ( $isFixed ) {
						$width	=	$attr['width'][$name];
	                }
	                foreach ( $fieldnames as $fieldname ) {
						$content	=	$item->renderField( $fieldname );
						if ( $content != '' ) {
							if ( $item->getMarkup( $fieldname ) != 'none' && ( $multiple || $item->getMarkup_Class( $fieldname ) ) ) {
								$html	.=	'<div class="cck-clrfix'.$item->getMarkup_Class( $fieldname ).'">'.$content.'</div>';
							} else {
								$html	.=	$content;
							}
						}
	                }
	                echo '<td'.$attr['class'][$name].$width.'>'.$html.'</td>';
	            }
	            ?>
	            </tr>
	        <?php $i++;
	    	}
	    }
        if ( $isMore < 1 ) { ?>
			</tbody>
		<?php
		}
		if ( $head && $thead && ( $table_header == -1 || $table_header == 1 ) ) { ?>
        <tfoot>
            <tr><?php echo $head; ?></tr>
		</tfoot>
		<?php }
		if ( !$isMore ) { ?>
		</table>
	<?php }
	if ( !$isMore ) { ?>
    </div>
</div>
<?php
}
// -- Finalize
if ( $table_width ) {
	$cck->addCSS( '#'.$cck->id.' table {min-width:'.$table_width.'px;}' );
}
$cck->finalize();
?>