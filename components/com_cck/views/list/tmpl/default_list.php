<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( !$this->raw_rendering ) { ?>
<div class="cck_page_list<?php echo $this->pageclass_sfx; ?> cck-clrfix" id="system">
<?php } ?>
	<?php	
	$pagination_replace	=	'';
	
	if ( $this->show_pagination > -2 && $this->pages_total > 1 ) {
		$url			=	JUri::getInstance()->toString().'&';
		if ( strpos( $url, '=&' ) !== false ) {
			$vars		=	JUri::getInstance()->getQuery( true );
			if ( count( $vars ) ) {
				foreach ( $vars as $k=>$v ) {
					if ( $v == '' ) {
						$pagination_replace	.=	$k.'=&';
					}
				}
			}
		}
	}
	if ( $this->show_items_number ) {
		$label	=	$this->label_items_number;
		if ( $this->config['doTranslation'] ) {
			$label	=	'COM_CCK_' . str_replace( ' ', '_', trim( $label ) );

			if ( ( $this->total == 0 || $this->total == 1 ) && JFactory::getLanguage()->hasKey( $label.'_1' ) ) {
				$label	.=	'_1';
			}
			$label	=	JText::_( $label );
		} elseif ( $this->total == 0 || $this->total == 1 ) {
			if ( JFactory::getLanguage()->hasKey( 'COM_CCK_' . str_replace( ' ', '_', trim( $label ).'_1' ) ) ) {
				$label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $label ).'_1' ) );	
			}			
		}
		echo '<div class="'.$this->class_items_number.'"><span>' . $this->total .'</span> '. $label . '</div>';
	}
	if ( ( $this->show_pagination == -1 || $this->show_pagination == 1 ) && $this->pages_total > 1 ) {
		echo '<div class="'.$this->class_pagination.'">' . ( ( $pagination_replace != '' ) ? str_replace( '?', '?'.$pagination_replace, $this->pagination->getPagesLinks() ) : $this->pagination->getPagesLinks() ) . '</div>';
	}
	if ( @$this->search->content > 0 ) {
		echo ( $this->raw_rendering ) ? $this->data : '<div class="cck_page_items">'.$this->data.'</div>';
	} else {
		echo $this->loadTemplate( 'items' );
	}
	if ( ( $this->show_pages_number || $this->show_pagination > -1 ) && $this->pages_total > 1 ) {
	    echo '<div class="'.$this->class_pagination.'"'.( $this->show_pagination == 8 ? ' style="display:none;"' : '' ).'>';
		$pagesCounter	=	$this->pagination->getPagesCounter();
    	if ( $this->show_pages_number && $pagesCounter ) {
	        echo '<p class="counter">' . $pagesCounter . '</p>';
    	}
		if ( $this->show_pagination > -1 ) {
			if ( $this->show_pagination == 2 || $this->show_pagination == 8 ) {
				echo '<ul class="pagination-list"><li><img id="seblod_form_loading_more" src="media/cck/images/spinner.gif" alt="" style="display:none;" width="28" height="28" /><a id="seblod_form_load_more" href="javascript:void(0);" data-start="0" data-step="'.$this->limitend.'" data-end="'.$this->total.'">'.$this->label_pagination.'</a></li></ul>';
			} else {
				echo ( $pagination_replace != '' ) ? str_replace( '?', '?'.$pagination_replace, $this->pagination->getPagesLinks() ) : $this->pagination->getPagesLinks();
			}
		}
	    echo '</div>';
	}
    ?>
<?php if ( !$this->raw_rendering ) { ?>
</div>
<?php } ?>