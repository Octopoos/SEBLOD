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

// Params init
$items_number					=	0;
$id 							= 	$cck->id;
$items							=	$cck->getItems();
$item_margin		 			=	$cck->getStyleParam( 'item_margin', '10' );
$columns_debug	 				=	$cck->getStyleParam( 'debug', '0' );

// Params init Leadings 1
$line_main_1					= 	0;
$row_main_1						= 	0;
$line_items_main_1				= 	0;
$items_number_columns_main_1	=	0;
$columns_width_main_1[0]		= 	'';
$columns_width_main_1[1]		= 	array('100');
$columns_width_main_1[2] 		= 	array('50','50');
$columns_width_main_1[3] 		= 	array('33f','34f','33f');
$columns_width_main_1[4] 		=	array('25','25','25','25');
$columns_width_main_1[5] 		= 	array('20','20','20','20','20');
$columns_width_main_1[6] 		= 	array('17f','16f','17f','17f','16f','17f');
$items_main_1					=	$cck->getStyleParam( 'top_items', '1' );
$top_display					=	$cck->getStyleParam( 'top_display', 'renderItem' );
if ( $top_display == 'renderItem' ) {
	$top_display_params			=	array();
} else {
	$top_display_params			=	array( 'field_name'=>$cck->getStyleParam( 'top_display_field_name', '' ), 'target'=>strtolower( substr( $top_display, strpos( $top_display, '_' ) + 1 ) ) );
	$top_display				=	'renderItemField';
}
$columns_number_main_1			=	$cck->getStyleParam( 'top_columns', '1' );
$columns_number_main_1			=	( !$columns_number_main_1 ) ? 1 : $columns_number_main_1;
$lrortb_main_1					=	$cck->getStyleParam( 'top_item_order', '0' );
$columns_width_data_main_1		=	$cck->getStyleParam( 'top_column_width_custom', '50,50' );
$tmp_main_1						= 	null; preg_match_all('#[0-9]*#',$columns_width_data_main_1, $tmp_main_1);
$columns_width_custom_main_1	=	array_values( array_filter( $tmp_main_1[0] ) );
$top_item_height				=	$cck->getStyleParam( 'top_item_height', '1' );

// Params init Intro	
$line_intro						= 	0;
$row_intro						= 	0;
$line_items_intro				= 	0;
$items_number_columns_intro		=	0;
$columns_width_intro[0]			= 	'';
$columns_width_intro[1]			= 	array('100');
$columns_width_intro[2] 		= 	array('50','50');
$columns_width_intro[3] 		= 	array('33f','34f','33f');
$columns_width_intro[4] 		=	array('25','25','25','25');
$columns_width_intro[5] 		= 	array('20','20','20','20','20');
$columns_width_intro[6] 		= 	array('17f','16f','17f','17f','16f','17f');
$items_intro					=	$cck->getStyleParam( 'middle_items', '4' );
$middle_display					=	$cck->getStyleParam( 'middle_display', 'renderItem' );
if ( $middle_display == 'renderItem' ) {
	$middle_display_params		=	array();
} else {
	$middle_display_params		=	array( 'field_name'=>$cck->getStyleParam( 'middle_display_field_name', '' ), 'target'=>strtolower( substr( $middle_display, strpos( $middle_display, '_' ) + 1 ) ) );
	$middle_display				=	'renderItemField';
}
$columns_number_intro			=	$cck->getStyleParam( 'middle_columns', '2' );
$columns_number_intro			=	( !$columns_number_intro ) ? 1 : $columns_number_intro;
$lrortb_intro					=	$cck->getStyleParam( 'middle_item_order', '0' );
$columns_width_data_intro		=	$cck->getStyleParam( 'middle_column_width_custom', '50,50' );
$tmp_intro						= 	null; preg_match_all('#[0-9]*#',$columns_width_data_intro, $tmp_intro);
$columns_width_custom_intro		=	array_values( array_filter( $tmp_intro[0] ) );
$force_height_intro				=	$cck->getStyleParam( 'middle_item_height', '1' );

// Params init Links
$line_links						= 	0;
$row_links						= 	0;
$line_items_links				= 	0;
$items_number_columns_links		=	0;
$columns_width_links[0]			= 	'';
$columns_width_links[1]			= 	array('100');
$columns_width_links[2] 		= 	array('50','50');
$columns_width_links[3] 		= 	array('33f','34f','33f');
$columns_width_links[4] 		=	array('25','25','25','25');
$columns_width_links[5] 		= 	array('20','20','20','20','20');
$columns_width_links[6] 		= 	array('17f','16f','17f','17f','16f','17f');
$items_links					=	$cck->getStyleParam( 'bottom_items', '' );
$bottom_display					=	$cck->getStyleParam( 'bottom_display', 'renderItem' );
if ( $bottom_display == 'renderItem' ) {
	$bottom_display_params		=	array();
} else {
	$bottom_display_params		=	array( 'field_name'=>$cck->getStyleParam( 'bottom_display_field_name', '' ), 'target'=>strtolower( substr( $bottom_display, strpos( $bottom_display, '_' ) + 1 ) ) );
	$bottom_display				=	'renderItemField';
}
$columns_number_links			=	$cck->getStyleParam( 'bottom_columns', '3' );
$columns_number_links			=	( !$columns_number_links ) ? 1 : $columns_number_links;
$lrortb_links					=	$cck->getStyleParam( 'bottom_item_order', '0' );
$columns_width_data_links		=	$cck->getStyleParam( 'bottom_column_width_custom', '33,34,33' );
$tmp_links						= 	null; preg_match_all('#[0-9]*#',$columns_width_data_links, $tmp_links);
$columns_width_custom_links		=	array_values( array_filter( $tmp_links[0] ) );
$force_height_links				=	$cck->getStyleParam( 'bottom_item_height', '1' );

// Params init Even/Odd
$reset_columns					= 	$cck->getStyleParam( 'reset_columns', '0' );
$reverse_lines					= 	$cck->getStyleParam( 'reverse_lines', '0' );
$even_border_size				= 	$cck->getStyleParam( 'even_border_size', '1' );
$even_border_type				= 	$cck->getStyleParam( 'even_border_type', 'solid' );
$even_border_color				= 	$cck->getStyleParam( 'even_border_color', '#d1d1d1' );
$even_border_radius				= 	$cck->getStyleParam( 'even_border_radius', '5px' );
$even_bg_color					= 	$cck->getStyleParam( 'even_bg_color', '#f1f1f1' );
$odd_border_size				= 	$cck->getStyleParam( 'odd_border_size', '1' );
$odd_border_type				= 	$cck->getStyleParam( 'odd_border_type', 'solid' );
$odd_border_color				= 	$cck->getStyleParam( 'odd_border_color', '#d1d1d1' );
$odd_border_radius				= 	$cck->getStyleParam( 'odd_border_radius', '5px' );
$odd_bg_color					= 	$cck->getStyleParam( 'odd_bg_color', '#e1e1e1' );

// Assets CSS
$css							=	'';
$js								=	'';
$css							.=	'#'.$cck->id.' div.cck-even{border:'.$even_border_size.'px '.$even_border_type.' '.$even_border_color.'; background:'.$even_bg_color.';border-radius: '.$even_border_radius."px; position:relative;}\n" ;
$css							.=	'#'.$cck->id.' div.cck-odd{border:'.$odd_border_size.'px '.$odd_border_type.' '.$odd_border_color.'; background:'.$odd_bg_color.';border-radius: '.$odd_border_radius."px; position:relative;}\n" ;
//$cck->addStyleDeclaration( $css );

// Add CSS file
$doc	=	JFactory::getDocument();
$doc->addStyleSheet( JUri::root( true ).'/templates/'.$this->template.'/css/style.css' );

$class		=	$cck->id_class.'cck-f100';
$isMore		=	$cck->isLoadingMore();
if ( $cck->isGoingToLoadMore() ) {
	$class	=	trim( $class.' '.'cck-loading-more' );
}

// Set template
if ( !$isMore ) {
	echo '<div id="'.$id.'" class="'.$class.' cck-pad-'.$item_margin.'">';
}

$count	=	count( $items );
foreach ( $items as $item ) {
	// # TOP BLOCK
	if ( $items_number < $items_main_1 ) {
		$items_number_columns_main_1 =	$items_number ;
		$count_items				 =	count( $items );
		$items_per_columns_main_1	 =	ceil( $count_items / $columns_number_main_1 );
		if ( $columns_number_main_1 == 0 || $columns_number_main_1 == 1 || $columns_number_main_1 == '' ) {
			if ( $items_number % 2 ) {
				$even_odd = "even";
			} else {
				$even_odd = "odd";
			}
			echo '<div class="blog-top cck-w100 cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.'">'.$cck->$top_display( $item->pk, $top_display_params );
			// Debug
			if ( $columns_debug == 1 ) {
				echo	'<fieldset class="cck-debug"><legend>Debug:</legend>'
					.' // Number:'.($items_number + 1)
					.' // Total items:'.count( $items )
					.' // Total columns:'.$columns_number_main_1
					.' // Total leading:'.$items_main_1
					.' // Even/odd:'.$even_odd
					.' // Columns width:100%'
					.'</fieldset>';
			}
			echo '</div></div></div>';
			echo '<div class="cck-clear"></div>';
		} else {
			// Count Line
			if ( $items_number_columns_main_1 % $columns_number_main_1 == 0 ) {
				$line_main_1++;
			}
			// Variable Even/Odd and reverse line
			if ( $reset_columns == 1 ) {
				if ( $reverse_lines == 1 ) {
					if ( ( ( $items_number_columns_main_1 + 1 ) + $line_main_1 ) % 2 ) {
						$even_odd = "even";
					} else {
						$even_odd = "odd" ;
					}
				} elseif ( $items_number_columns_main_1 % 2 ) {
					$even_odd = "even";
				} else {
					$even_odd = "odd";
				}
			} else {
				if ( $reverse_lines == 1 ) {
					if ( ( $items_number + $line_main_1 ) % 2 ) {
						$even_odd = "even";
					} else {
						$even_odd = "odd";
					}
				} elseif ( $items_number % 2 ) {
					$even_odd = "even";
				} else {
					$even_odd = "odd";
				}
			}
			// Columns Width Custom
			if ( count ( $columns_width_custom_main_1 ) >= $columns_number_main_1 ) {
				$columns_width_main_1[$columns_number_main_1] = $columns_width_custom_main_1;
			}
			// Items per Row
			if ( (  $items_number_columns_main_1 == ( $items_per_columns_main_1 * $row_main_1 ) ) && $lrortb_main_1 != 0 ) {
				echo '<div id="cck-row'.( $row_main_1 + 1 ).'" class="cck-w'.$columns_width_main_1[$columns_number_main_1][$row_main_1].' cck-row">';
				$row_main_1++;
			}
			// Items per Line
			if ( ( ( $items_number_columns_main_1  ) % $columns_number_main_1 == 0 ) && $lrortb_main_1 == 0 ) {
				echo '<div class="cck-line-blog-top'.( $line_main_1 ).' cck-w100 cck-line">';
			}
			// If Down Items Width 100%
			if ( $lrortb_main_1 != 0 ) {
				echo '<div class="cck-w100 cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.'">'.$cck->$top_display( $item->pk, $top_display_params );
			} else {
				// If Across Items Width array or custom
				if ( $top_item_height != 0 ) {
					$js		.= 'jQuery(document).ready(function($){$("#'.$id.' .cck-line-blog-top'.$line_main_1.' > div:not(.clr)").deepestHeight();$("#'.$id.' .cck-line-blog-top'.$line_main_1.' div.cck-deepest-blog").deepestHeight();});';
				}
				echo '<div id="'.$id.'_top'.$line_main_1.'-'.( $line_items_main_1 + 1 )
					.'" class="cck-w'.$columns_width_main_1[$columns_number_main_1][$line_items_main_1].' cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.' cck-deepest-blog">'.$cck->$top_display( $item->pk, $top_display_params );
				$line_items_main_1 =	( ( $items_number_columns_main_1 + 1 ) % $columns_number_main_1 == 0 ) ? 0 : $line_items_main_1 + 1;
			}
			// Debug
			if ( $columns_debug == 1 ) {
				echo	'<fieldset class="cck-debug"><legend>Debug:</legend>'
					.' // Number:'.($items_number + 1)
					.' // Number after leading:'.($items_number_columns_main_1 + 1).'/'.$count_items_after_leading
					.' // Total items:'.count( $items )
					.' // Total columns:'.$columns_number_main_1
					.' // Total leading:'.$items_main_1
					.' // Even/odd:'.$even_odd
					.' // Items number:'.$items_number
					.'</fieldset>';
			}
			echo '</div></div></div>';
			// Close Row div if down
			if ( ( $items_number_columns_main_1 == ( $items_per_columns_main_1 * $row_main_1 ) - 1 ) && $lrortb_main_1 == 1 ) {
				echo '</div>';
			}
			// Clear Line div if across
			if ( ( ( ( $items_number_columns_main_1 + 1 ) % $columns_number_main_1 == 0 ) && $lrortb_main_1 == 0 ) || ( $items_number == ( $items_main_1 - 1 ) )
				|| ( $items_number == ( $count - 1 ) ) ) { /*!*/
				echo '</div><div class="cck-clear"></div>';
			}
		}
		// # MIDDLE BLOCK
	} elseif ( $items_number >= $items_main_1 ) {
		$items_number_columns_intro	=	$items_number - $items_main_1;
		if ( $items_intro == 0 || $items_intro == '' ) {
			if ( $items_links == 0 || $items_links == '' ) {
				$count_items_after_leading	=	( count ( $items ) - $items_main_1 );
			} else {
				$count_items_after_leading	=	( count ( $items ) - ( $items_main_1 + $items_links ) );
				$items_intro = $count_items_after_leading ;
			}
		} else if ( $items_intro != '' || $items_intro != 0 ) {
			$count_items_after_leading		=	$items_intro;
		}
		$items_per_columns_intro			=	ceil( $count_items_after_leading / $columns_number_intro );
		if ( $items_number < ( $items_main_1 + $count_items_after_leading ) ) {
			if ( $columns_number_intro == 0 || $columns_number_intro == 1 || $columns_number_intro == '' ) {
				if ( $items_number % 2 ) {
					$even_odd = "even";
				} else {
					$even_odd = "odd";
				}
				echo '<div class="blog-middle cck-w100 cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.'">'.$cck->$middle_display( $item->pk, $middle_display_params );
				// Debug
				if ( $columns_debug == 1 ) {
					echo	'<fieldset class="cck-debug"><legend>Debug:</legend>'
						.' // Number:'.($items_number + 1)
						.' // Total items:'.count( $items )
						.' // Total columns:'.$columns_number_intro
						.' // Total leading:'.$items_main_1
						.' // Even/odd:'.$even_odd
						.' // Columns width:100%'
						.'</fieldset>';
				}
				echo '</div></div></div>';
				echo '<div class="cck-clear"></div>';
			} else {
				// Count Line
				if ( $items_number_columns_intro % $columns_number_intro == 0 ) {
					$line_intro++;
				}
				// Variable Even/Odd and reverse line
				if ( $reset_columns == 1 ) {
					if ( $reverse_lines == 1 ) {
						if ( ( ( $items_number_columns_intro + 1 ) + $line_intro ) % 2 ) {
							$even_odd = "even";
						} else {
							$even_odd = "odd" ;
						}
					} elseif ( $items_number_columns_intro % 2 ) {
						$even_odd = "even";
					} else {
						$even_odd = "odd";
					}
				} else {
					if ( $reverse_lines == 1 ) {
						if ( ( $items_number + $line_intro ) % 2 ) {
							$even_odd = "even";
						} else {
							$even_odd = "odd";
						}
					} elseif ( $items_number % 2 ) {
						$even_odd = "even";
					} else {
						$even_odd = "odd";
					}
				}
				// Columns Width Custom
				if ( count ( $columns_width_custom_intro ) >= $columns_number_intro ) {
					$columns_width_intro[$columns_number_intro] = $columns_width_custom_intro;
				}
				// Items per Row
				if ( (  $items_number_columns_intro == ( $items_per_columns_intro * $row_intro ) ) && $lrortb_intro != 0 ) {
					echo '<div id="cck-row'.( $row_intro + 1 ).'" class="cck-w'.$columns_width_intro[$columns_number_intro][$row_intro].' cck-row">';
					$row_intro++;
				}
				// Items per Line
				if ( ( ( $items_number_columns_intro  ) % $columns_number_intro == 0 ) && $lrortb_intro == 0 ) {
					echo '<div class="cck-line-blog-middle'.( $line_intro ).' cck-w100 cck-line">';
				}
				// If Down Items Width 100%
				if ( $lrortb_intro != 0 ) {
					echo '<div class="cck-w100 cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.'">'.$cck->$middle_display( $item->pk, $middle_display_params );
				} else {
					// If Across Items Width array or custom
					if ( $force_height_intro != 0 ) {
						$js		.= 'jQuery(document).ready(function($){$("#'.$id.' .cck-line-blog-middle'.$line_intro.' > div:not(.clr)").deepestHeight();$("#'.$id.' .cck-line-blog-middle'.$line_intro.' div.cck-deepest-blog").deepestHeight();});';
					}
					echo '<div id="'.$id.'_middle'.$line_intro.'-'.( $line_items_intro + 1 )
						.'" class="cck-w'.$columns_width_intro[$columns_number_intro][$line_items_intro].' cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.' cck-deepest-blog">'.$cck->$middle_display( $item->pk, $middle_display_params );
					$line_items_intro =	( ( $items_number_columns_intro + 1 ) % $columns_number_intro == 0 ) ? 0 : $line_items_intro + 1;
				}
				// Debug
				if ( $columns_debug == 1 ) {
					echo	'<fieldset class="cck-debug"><legend>Debug:</legend>'
						.' // Number:'.($items_number + 1)
						.' // Number after leading:'.($items_number_columns_intro + 1).'/'.$count_items_after_leading
						.' // Total items:'.count( $items )
						.' // Total columns:'.$columns_number_intro
						.' // Total leading:'.$items_main_1
						.' // Even/odd:'.$even_odd
						.' // Items number:'.$items_number
						.'</fieldset>';
				}
				echo '</div></div></div>';
				// Close Row div if down
				if ( ( $items_number_columns_intro == ( $items_per_columns_intro * $row_intro ) - 1 ) && $lrortb_intro == 1 ) {
					echo '</div>';
				}
				// Clear Line div if across
				if ( ( ( ( $items_number_columns_intro + 1 ) % $columns_number_intro == 0 ) && $lrortb_intro == 0 ) || ( $items_number == ( $items_main_1 + $count_items_after_leading - 1 ) )
					|| ( $items_number == ( $count - 1 ) ) ) { /*!*/
					echo '</div><div class="cck-clear"></div>';
				}
			}
		} else {
			// # BOTTOM BLOCK
			$items_number_columns_links	=	$items_number - $items_main_1 - $items_intro;
			$count_items_after_intro	=	( count ( $items ) - $items_main_1 - $items_intro );
			$items_per_columns_links	=	ceil( $count_items_after_intro / $columns_number_links );
			if ( $columns_number_links == 0 || $columns_number_links == 1 || $columns_number_links == '' ) {
				if ( $items_number % 2 ) {
					$even_odd = "even";
				} else {
					$even_odd = "odd";
				}
				echo '<div class="blog-bottom cck-w100 cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.'">'.$cck->$bottom_display( $item->pk, $bottom_display_params );
				// Debug
				if ( $columns_debug == 1 ) {
					echo	'<fieldset class="cck-debug"><legend>Debug:</legend>'
						.' // Number:'.($items_number + 1)
						.' // Total items:'.count( $items )
						.' // Total columns:'.$columns_number_links
						.' // Total leading:'.$items_main_1
						.' // Even/odd:'.$even_odd
						.' // Columns width:100%'
						.'</fieldset>';
				}
				echo '</div></div></div>';
				echo '<div class="cck-clear"></div>';
			} else {
				// Count Line
				if ( $items_number_columns_links % $columns_number_links == 0 ) {
					$line_links++;
				}
				// Variable Even/Odd and reverse line
				if ( $reset_columns == 1 ) {
					if ( $reverse_lines == 1 ) {
						if ( ( ( $items_number_columns_links + 1 ) + $line_links ) % 2 ) {
							$even_odd = "even";
						} else {
							$even_odd = "odd" ;
						}
					} elseif ( $items_number_columns_links % 2 ) {
						$even_odd = "even";
					} else {
						$even_odd = "odd";
					}
				} else {
					if ( $reverse_lines == 1 ) {
						if ( ( $items_number + $line_links ) % 2 ) {
							$even_odd = "even";
						} else {
							$even_odd = "odd";
						}
					} elseif ( $items_number % 2 ) {
						$even_odd = "even";
					} else {
						$even_odd = "odd";
					}
				}
				// Columns Width Custom
				if ( count ( $columns_width_custom_links ) >= $columns_number_links ) {
					$columns_width_links[$columns_number_links] = $columns_width_custom_links;
				}
				// Items per Row
				if ( (  $items_number_columns_links == ( $items_per_columns_links * $row_links ) ) && $lrortb_links != 0 ) {
					echo '<div id="cck-row'.( $row_links + 1 ).'" class="cck-w'.$columns_width_links[$columns_number_links][$row_links].' cck-row">';
					$row_links++;
				}
				// Items per Line
				if ( ( ( $items_number_columns_links  ) % $columns_number_links == 0 ) && $lrortb_links == 0 ) {
					echo '<div class="cck-line-blog-bottom'.( $line_links ).' cck-w100 cck-line">';
				}
				// If Down Items Width 100%
				if ( $lrortb_links != 0 ) {
					echo '<div class="cck-w100 cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.'">'.$cck->$bottom_display( $item->pk, $bottom_display_params );
				} else {
					// If Across Items Width array or custom
					if ( $force_height_links != 0 ) {
						$js		.= 'jQuery(document).ready(function($){$("#'.$id.' .cck-line-blog-bottom'.$line_links.' > div:not(.clr)").deepestHeight();$("#'.$id.' .cck-line-blog-bottom'.$line_links.' div.cck-deepest-blog").deepestHeight();});';
					}
					echo '<div id="'.$id.'_bottom'.$line_links.'-'.( $line_items_links + 1 )
						.'" class="cck-w'.$columns_width_links[$columns_number_links][$line_items_links].' cck-fl cck-ptb"><div class="cck-plr"><div class="cck-'.$even_odd.' cck-deepest-blog">'.$cck->$bottom_display( $item->pk, $bottom_display_params );
					$line_items_links =	( ( $items_number_columns_links + 1 ) % $columns_number_links == 0 ) ? 0 : $line_items_links + 1;
				}
				// Debug
				if ( $columns_debug == 1 ) {
					echo	'<fieldset class="cck-debug"><legend>Debug:</legend>'
						.' // Number:'.($items_number + 1)
						.' // Number after leading:'.($items_number_columns_links + 1).'/'.$count_items_after_intro
						.' // Total items:'.count( $items )
						.' // Total columns:'.$columns_number_links
						.' // Total leading:'.$items_main_1
						.' // Even/odd:'.$even_odd
						.' // Items number:'.$items_number
						.'</fieldset>';
				}
				echo '</div></div></div>';
				// Close Row div if down
				if ( ( $items_number_columns_links == ( $items_per_columns_links * $row_links ) - 1 ) && $lrortb_links == 1 ) {
					echo '</div>';
				}
				// Clear Line div if across
				if ( ( ( ( $items_number_columns_links + 1 ) % $columns_number_links == 0 ) && $lrortb_links == 0 ) || ( $items_number == ( $items_main_1 + $count_items_after_leading + $count_items_after_intro - 1 ) ) ) {
					echo '</div><div class="cck-clear"></div>';
				}
			}
		}
	}
	$items_number++;
	$items_number_columns_main_1++;
	$items_number_columns_intro++;
	$items_number_columns_links++;
};
if ( !$isMore ) {
	echo '</div>';
}

$cck->addScriptDeclaration( $js );
$cck->finalize();
?>