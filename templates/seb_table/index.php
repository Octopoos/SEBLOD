<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: index.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

$attributes		=	$cck->item_attributes ? ' '.$cck->item_attributes : '';
$has_head		=	(int)$cck->getStyleParam( 'table_header_data', 0 );
$table_columns	=	$cck->getStyleParam( 'table_columns', 0 );
$table_header	=	$cck->getStyleParam( 'table_header', 0 );
$table_layout	=	$cck->getStyleParam( 'table_layout', '' );
$table_width	=	0;
$isFixed		=	( $table_layout == 'fixed' ) ? 1 : ( ( $table_layout == 'calculated' ) ? 2 : 0 );
$isResponsive	=	( $table_layout == 'responsive' ) ? 1 : 0;
$class_body		=	'';
$class_table	=	trim( $cck->getStyleParam( 'class_table', 'category zebra table' ) );
$class_table	=	( $isFixed ) ? $class_table.' fixed' : $class_table;
if ( $isResponsive ) {
	if ( $class_table == 'o-table' ) {
		$class_table	=	'o-table-responsive';
	} else {
		$class_table	=	str_replace( 'o-table ', 'o-table-responsive ', $class_table );
	}
}
$class_table	=	$class_table ? ' class="'.$class_table.'"' : '';
$translate		=	JCck::getConfig_Param( 'language_jtext', 1 );

$doc			=	Factory::getDocument();
$doc->addStyleSheet( Uri::root( true ).'/templates/'.$cck->template. '/css/'.'style.css' );

if ( $translate ) {
	$lang	=	Factory::getLanguage();
}

// Set
$isMore			=	$cck->isLoadingMore();
if ( $cck->isGoingToLoadMore() ) {
	$class_body	=	' class="cck-loading-more"';
}

$html_above		=	$cck->renderPosition( '_above_' );
$html_above_th	=	substr( $html_above, 0, 3 ) == '<tr' ? true : false;
$html_below		=	$cck->renderPosition( '_below_' );
$html_below_tf	=	substr( $html_below, 0, 3 ) == '<tr' ? true : false;

// -- Render
if ( $cck->id_class && !$isMore ) {
?>
<div id="<?php echo $cck->id; ?>" class="<?php echo $cck->id_class; ?>"<?php echo ( $cck->id_attributes ? ' '.$cck->id_attributes : '' ); ?>>
	<div>
	<?php }
	if ( !$html_above_th ) {
		echo $html_above;
	}

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

	if ( $has_head ) {
		$count--;
		$first	=	array_shift( $items );
	}
	
	foreach ( $positions as $name=>$position ) {
		$class					=	$position->css;
		$attr['class'][$name]	=	$class ? ' class="'.$class.'"' : '';
		$attr['data'][$name]	=	'';
		$attr['label'][$name]	=	'';

		$head[$name]	=	array( 'count'=>$count, 'fields'=>0, 'html'=>'', 'items'=>array() );
		$legend			=	'';
		$width			=	$cck->w( $name );

		if ( $has_head ) {
			$legend		=	$first->renderPosition( $name, 'cell' );

			if ( $has_head === 1 ) {
				$attr['data'][$name]	=	' scope="col"';
			}
			if ( $isResponsive ) {
				$attr['label'][$name]	=	' data-label="'.strip_tags( $legend ).'"';
			}
		} elseif ( $position->legend ) {
			$legend		=	trim( $position->legend );
			$legend2	=	$legend;

			if ( $legend != '' && !( $legend[0] == '<' || strpos( $legend, ' / ' ) !== false ) ) {
				if ( $translate ) {
					$key	=	'COM_CCK_' . str_replace( ' ', '_', trim( $legend ) );
					
					if ( $lang->hasKey( $key ) ) {
						$legend	=	Text::_( $key );
					}
				}
				if ( $isResponsive ) {
					$attr['label'][$name]	=	' data-label="'.strip_tags( $legend ).'"';
				}
			}
		} else {
			if ( isset( $position->legend2 ) && $position->legend2 ) {
				$legend		=	trim( $position->legend2 );
				$legend2	=	$legend;

				if ( $isResponsive ) {
					$attr['label'][$name]	=	' data-label="'.strip_tags( $legend ).'"';
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
				$head[$name]['html']	=	'<th'.$attr['class'][$name].$attr['data'][$name].$attr['width'][$name].'>'.$legend.'</th>';	
			}
		}
	}
	?>
	<?php
	if ( $count ) {
		$i	=	0;

        foreach ( $items as $item ) {
        	$body[$i]['cols']	=	array();
			$body[$i]['html']	=	'<tr'.$item->replaceLive( $attributes ).'>';

            foreach ( $positions as $name=>$position ) {
				$col		=	'';
				$width		=	'';

				if ( $isFixed ) {
					$width	=	$attr['width'][$name];
				}
				$col		=	$item->renderPosition( $name, 'cell' );

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
		$html	.=	'<thead>'
				.	'<tr>'.$thead.'</tr>';

		if ( $html_above_th ) {
			$html	.=	$html_above;
		}

		$html	.=	'</thead>';
	} elseif ( $html_above_th ) {
		$html	.=	'<thead>'.$html_above.'</thead>';
	}
	$html		.=	$tbody;

	if ( $thead && ( $table_header == -1 || $table_header == 1 ) ) {
		$html	.=	'<tfoot>';

		if ( $html_below_tf ) {
			$html	.=	$html_below;
		}

		$html	.=	'<tr>'.$thead.'</tr>'
				.	'</tfoot>';
	} elseif ( $html_below_tf ) {
		$html	.=	'<tfoot>'.$html_below.'</tfoot>';
	}
	if ( !$isMore ) {
		$html	.=	'</table>';
	}

	echo $html;

	if ( !$html_below_tf ) {
		echo $html_below;
	}

	if ( $cck->id_class && !$isMore ) { ?>
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