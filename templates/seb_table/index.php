<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: index.php sebastienheraud $
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

$attributes		=	$cck->item_attributes ? ' '.$cck->item_attributes : '';
$table_columns	=	$cck->getStyleParam( 'table_columns', 0 );
$table_header	=	$cck->getStyleParam( 'table_header', 0 );
$table_layout	=	$cck->getStyleParam( 'table_layout', '' );
$table_width	=	0;
$isFixed		=	( $table_layout == 'fixed' ) ? 1 : ( ( $table_layout == 'calculated' ) ? 2 : 0 );
$isResponsive	=	( $table_layout == 'responsive' ) ? 1 : 0;
$class_body		=	'';
$class_table	=	trim( $cck->getStyleParam( 'class_table', 'category zebra table' ) );
$class_table	=	( $isFixed ) ? $class_table.' fixed' : $class_table;
$class_table	=	( $isResponsive ) ? $class_table.' responsive' : $class_table;
$class_table	=	$class_table ? ' class="'.$class_table.'"' : '';
$class_row0		=	trim( $cck->getStyleParam( 'class_table_tr_even', 'cat-list-row%i' ) );
$class_row0		=	$class_row0 ? ' class="'.str_replace( '%i', '0', $class_row0 ).'"' : '';
$class_row1		=	trim( $cck->getStyleParam( 'class_table_tr_odd', 'cat-list-row%i' ) );
$class_row1		=	$class_row1 ? ' class="'.str_replace( '%i', '1', $class_row1 ).'"' : '';
$translate		=	JCck::getConfig_Param( 'language_jtext', 0 );

$doc			=	JFactory::getDocument();
$doc->addStyleSheet( JUri::root( true ).'/templates/'.$cck->template. '/css/'.'style.css' );

if ( $translate ) {
	$lang	=	JFactory::getLanguage();
}

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
	$body		=	array();
	$head		=	array();
	$html		=	'';
	$items		=	$cck->getItems();
	$positions	=	$cck->getPositions();
	ksort( $positions );
	$tbody		=	'';
	$thead		=	false;
	$unset		=	array();

	unset( $positions['hidden'] );

	$count		=	count( $items );
	
	foreach ( $positions as $name=>$position ) {
		$class					=	$position->css;
		$attr['class'][$name]	=	$class ? ' class="'.$class.'"' : '';
		$attr['label'][$name]	=	'';

		$head[$name]			=	array( 'count'=>$count, 'fields'=>0, 'html'=>'', 'items'=>array() );
		$legend					=	'';
		$width					=	$cck->w( $name );

		if ( $position->legend ) {
			$legend						=	trim( $position->legend );
			$legend2					=	$legend;

			if ( $legend != '' && !( $legend[0] == '<' || strpos( $legend, ' / ' ) !== false ) ) {
				if ( $translate ) {
					$key				=	'COM_CCK_' . str_replace( ' ', '_', trim( $legend ) );
					
					if ( $lang->hasKey( $key ) ) {
						$legend			=	JText::_( $key );
					}
				}
				if ( $isResponsive ) {
					$attr['label'][$name]	=	' data-label="'.$legend.'"';
				}
			}
		} else {
			if ( isset( $position->legend2 ) && $position->legend2 ) {
				$legend					=	trim( $position->legend2 );
				$legend2				=	$legend;
				if ( $isResponsive ) {
					$attr['label'][$name]	=	' data-label="'.$legend.'"';
				}
			}
		}

		if ( $legend || $width ) {
			if ( $legend ) {
				$thead	=	true;
			}
			if ( $position->variation != '' ) {
				$var		=	$cck->renderVariation( $position->variation, $legend2, '', $position->variation_options, $name );
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
				$head[$name]['html']	=	$var;
			} else {
				$attr['width'][$name]	=	( $width ) ? ' width="'.$width.'"' : ''; // ( $width ) ? ' style="width:'.$width.'"' : '';
				$head[$name]['html']	=	'<th'.$attr['class'][$name].$attr['width'][$name].'>'.$legend.'</th>';	
			}
		}
	}
	?>
	<?php
	if ( $count ) {
		$i	=	0;
        foreach ( $items as $item ) {
        	$body[$i]['cols']	=	array();
			$body[$i]['html']	=	'<tr '.${'class_row'.($i % 2)}.$item->replaceLive( $attributes ).'>';

            foreach ( $positions as $name=>$position ) {
				$fieldnames	=	$cck->getFields( $name, '', false );

				if ( $i == 0 ) {
					$head[$name]['fields']	=	( count( $fieldnames ) > 1 ) ? true : false;
				}
				$col		=	'';
				$multiple	=	$head[$name]['fields'];
				$width		=	'';
				if ( $isFixed ) {
					$width	=	$attr['width'][$name];
				}
                foreach ( $fieldnames as $fieldname ) {
					$content	=	$item->renderField( $fieldname );
					if ( $content != '' ) {
						if ( $item->getMarkup( $fieldname ) != 'none' && ( $multiple || $item->getMarkup_Class( $fieldname ) ) ) {
							$col	.=	'<div class="cck-clrfix'.$item->getMarkup_Class( $fieldname ).'">'.$content.'</div>';
						} else {
							$col	.=	$content;
						}
					}
				}
				if ( $col == '' ) {
					if ( !$table_columns ) {
						$head[$name]['count']--;
					}
				}
				$body[$i]['cols'][$name]	=	'<td'.$attr['class'][$name].$attr['label'][$name].$width.'>'.$col.'</td>';
			}
			$body[$i]['html2']	=	'</tr>';
			$i++;
		}
		if ( count( $head ) ) {
			foreach ( $head as $k=>$v ) {
				if ( $v['count'] == 0 ) {
					$unset[$k]	=	$k;
					unset( $head[$k] );
				}
			}
		}
		if ( count( $body ) ) {
			foreach ( $body as $k=>$v ) {
				foreach ( $unset as $col ) {
					unset( $v['cols'][$col] );
				}
				$row		=	implode( $v['cols'] );
				$tbody		.=	$v['html'].$row.$v['html2'];
			}
		}
	}
	if ( $isMore < 1 ) {
		$tbody	=	'<tbody'.$class_body.'>'.$tbody.'</tbody>';
	}
	if ( !$isMore ) {
		$html	.=	'<table'.$class_table.'>';
	}
	if ( $isMore < 1 && $thead && count( $head ) ) {
		$thead	=	'';

		foreach ( $head as $k=>$v ) {
			$thead	.=	$v['html'];
		}
	} else {
		$thead	=	'';
	}
	if ( $thead && ( $table_header == 0 || $table_header == 1 ) ) {
		$html	.=	'<thead><tr>'.$thead.'</tr></thead>';
	}
	$html		.=	$tbody;

	if ( $thead && ( $table_header == -1 || $table_header == 1 ) ) {
		$html	.=	'<tfoot><tr>'.$thead.'</tr></tfoot>';
	}
	if ( !$isMore ) {
		$html	.=	'</table>';
	}
	echo $html;

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