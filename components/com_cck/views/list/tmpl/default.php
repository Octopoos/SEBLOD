<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	Helper_Include::addValidation( $this->config['validation'], $this->config['validation_options'] );
	$js	=	'if (jQuery("#'.$this->form_id.'").validationEngine("validate",task) === true) { JCck.Core.submitForm((task=="save"?"search":task), document.getElementById("'.$this->form_id.'")); }';
} else {
	$js	=	'JCck.Core.submitForm((task=="save"?"search":task), document.getElementById("'.$this->form_id.'"));';
}
$app	=	JFactory::getApplication();
$doc	=	JFactory::getDocument();
$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';
?>
<?php if ( $this->show_form ) {
$js		=	$this->config['submit'].' = function(task) {'. $js.' };'
		.	'Joomla.submitbutton = function(task, cid)'
		.	'{'
		.	'if (task == "delete") {'
		.			'if (!confirm(Joomla.JText._(\'COM_CCK_CONFIRM_DELETE\'))) {'
		.				'return false;'
		.			'}'
		.		'}'
		.		'jQuery("#'.$this->form_id.'").append(\'<input type="hidden" id="return" name="return" value="'.base64_encode( JUri::getInstance()->toString() ).'">\');'
		.		'JCck.Core.submitForm(task,document.getElementById(\''.$this->form_id.'\'));'
		.	'};'
		.	'';
$doc->addScriptDeclaration( $js );
} ?>
<?php if ( !$this->raw_rendering ) { ?>
<div <?php echo $id; ?>class="cck_page cck-clrfix"><div>
<?php }
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_list_title ) {
	$tag		=	$this->tag_list_title;
	$class		=	trim( $this->class_list_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . $this->title . '</'.$tag.'>';
}
if ( $this->show_list_desc == 1 && $this->description != '' ) {
	echo ( $this->raw_rendering ) ? JHtml::_( 'content.prepare', $this->description ) : '<div class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
if ( $this->show_form ) {
	echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? JUri::base( true ) : JRoute::_( 'index.php?option='.$this->option ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
}
if ( $this->show_form == 1 ) {
	echo ( $this->raw_rendering ) ? $this->form : '<div class="cck_page_search'.$this->pageclass_sfx.' cck-clrfix">' . $this->form . '</div><div class="clr"></div>';
}
?>
<?php
if ( $this->show_form ) {
if ( !$this->raw_rendering ) { ?>
<div>
<?php } ?>
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<?php if ( !JFactory::getConfig()->get( 'sef' ) || !$this->config['Itemid'] ) { ?>
<input type="hidden" name="option" value="com_cck" />
<input type="hidden" name="view" value="list" />
<?php if ( $this->home === false ) { ?>
<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt( 'Itemid', 0 ); ?>" />
<?php } }
$tmpl	=	$app->input->get( 'tmpl', '' );
if ( $tmpl ) { ?>
<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
<?php } ?>
<input type="hidden" name="search" value="<?php echo $this->search->name; ?>" />
<input type="hidden" name="task" value="search" />
<?php if ( !$this->raw_rendering ) { ?>
</div>
<?php } }
if ( !$this->raw_rendering ) { ?>
<div class="cck_page_list<?php echo $this->pageclass_sfx; ?> cck-clrfix" id="system">
<?php } ?>
	<?php
	if ( isset( $this->pagination->pagesTotal ) ) {
		$pages_total	=	$this->pagination->pagesTotal;
	} elseif ( isset( $this->pagination->{'pages.total'} ) ) {
		$pages_total	=	$this->pagination->{'pages.total'};
	} else {
		$pages_total	=	0;
	}
	$hasAjax			=	( $this->filter_ajax || ( $pages_total > 1 && ( $this->show_pagination == 2 || $this->show_pagination == 8 ) ) ) ? true : false;
	$pagination_replace	=	'';
	if ( $this->show_pagination > -2 && $pages_total > 1 ) {
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
			$label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $label ) ) );
		}
		echo '<div class="'.$this->class_items_number.'"><span>' . $this->total .'</span> '. $label . '</div>';
	}
	if ( ( $this->show_pagination == -1 || $this->show_pagination == 1 ) && $pages_total > 1 ) {
		echo '<div class="'.$this->class_pagination.'">' . ( ( $pagination_replace != '' ) ? str_replace( '?', '?'.$pagination_replace, $this->pagination->getPagesLinks() ) : $this->pagination->getPagesLinks() ) . '</div>';
	}
	if ( @$this->search->content > 0 ) {
		echo ( $this->raw_rendering ) ? $this->data : '<div class="cck_page_items">'.$this->data.'</div>';
	} else {
		echo $this->loadTemplate( 'items' );
	}
	if ( ( $this->show_pages_number || $this->show_pagination > -1 ) && $pages_total > 1 ) {
	    echo '<div class="'.$this->class_pagination.'"'.( $this->show_pagination == 8 ? ' style="display:none;"' : '' ).'>';
		$pagesCounter	=	$this->pagination->getPagesCounter();
    	if ( $this->show_pages_number && $pagesCounter ) {
	        echo '<p class="counter">' . $pagesCounter . '</p>';
    	}
		if ( $this->show_pagination > -1 ) {
			if ( $this->show_pagination == 2 || $this->show_pagination == 8 ) {
				echo '<ul class="pagination-list"><li><img id="seblod_form_loading_more" src="media/cck/images/spinner.gif" alt="" style="display:none;" width="28" height="28" /><a id="seblod_form_load_more" href="javascript:void(0);" data-start="0" data-step="'.$this->limitend.'" data-end="'.$this->total.'">'.JText::_( 'COM_CCK_LOAD_MORE' ).'</a></li></ul>';
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
<?php
if ( $this->show_form == 2 ) {
	echo ( $this->raw_rendering ) ? $this->form : '<div class="clr"></div><div class="cck_page_search'.$this->pageclass_sfx.'">' . $this->form . '</div>';
}
if ( $this->show_form ) {
?>
</form>
<?php
}
if ( $this->show_list_desc == 2 && $this->description != '' ) {
	echo ( $this->raw_rendering ) ? JHtml::_( 'content.prepare', $this->description ) : '<div class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
?>
<?php if ( !$this->raw_rendering ) { ?>
</div></div>
<?php } ?>

<?php if ( $hasAjax ) {
$context	=	'&context={\'Itemid\':'.$app->input->getInt( 'Itemid', 0 ).',\'view\':\'list\'}';
$pre		=	'';
$url		=	JUri::current();

if ( $app->input->get( 'tmpl' ) == 'raw' ) {
	$pre	=	'#seblod_form_raw ';
	
	if ( $app->input->get( 'search' ) != '' ) {
		$url	=	JUri::root().'index.php?option=com_cck&view=list&search='.$app->input->get( 'search' );
	}
}
?>
<script type="text/javascript">
(function ($){
	JCck.Core.loadmore = function(more,stop,search) {
		var elem = "<?php echo $pre; ?>.cck-loading-more";
		var search = search || 0;
		$.ajax({
			cache: false,
			data: "format=raw&task=search&infinite=1<?php echo $context; ?>&return=<?php echo base64_encode( JUri::getInstance()->toString() ); ?>"+more,
			type: "GET",
			url: "<?php echo $url; ?>",
			beforeSend:function(){ $("#seblod_form_load_more").hide(); $("#seblod_form_loading_more").show(); },
			success: function(response){
				if (stop != 1) {
					$("#seblod_form_load_more").show()<?php echo ( $this->show_pagination == 8 ) ? '.click()' : ''; ?>;
				} else {
					$(".cck_page_list .pagination").hide();
				}
				$("#seblod_form_loading_more").hide();
				if (search==1) { $(elem).html(response); } else { $(elem).append(response); }
				<?php
				if ( $this->callback_pagination != '' ) {
					$pos	=	strpos( $this->callback_pagination, '$(' );

					if ( $pos !== false && $pos == 0 ) {
						echo $this->callback_pagination;
					} else {
						echo $this->callback_pagination.'(response);';
					}
				}
				?>
			},
			error:function(){}
		});
	}
	$(document).ready(function() {
		$("#seblod_form_load_more").on("click", function() {
			var start = parseInt($(this).attr("data-start"));
			var step = parseInt($(this).attr("data-step"));
			start = start+step;
			var stop = (start+step>=parseInt($(this).attr("data-end"))) ? 1 : 0;
			$(this).attr("data-start",start);
			JCck.Core.loadmore("&start="+start,stop);
		})<?php echo ( $this->show_pagination == 8 ) ? '.click()' : ''; ?>;
	});
})(jQuery);
</script>
<?php } ?>