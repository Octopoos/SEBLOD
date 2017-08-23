<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	JCckDev::addValidation( $this->config['validation'], $this->config['validation_options'] );
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
if ( $this->show_list_desc && $this->description != '' ) {
	$description	=	JHtml::_( 'content.prepare', $this->description );
	
	if ( !( $this->tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$this->tag_desc	=	'div';
	}
	if ( !$this->raw_rendering ) {
		$description	=	'<'.$this->tag_desc.' class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . $description . '</'.$this->tag_desc.'>';
	}
	if ( $this->tag_desc == 'div' ) {
		$description	.=	'<div class="clr"></div>';
	}
}
if ( $this->show_list_desc == 1 && $this->description != '' ) {
	echo $description;
}
if ( $this->show_form ) {
	if ( $this->show_form == 1 ) {
		echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? JUri::base( true ) : JRoute::_( 'index.php?option='.$this->option ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';

		if ( $this->raw_rendering ) {
			echo $this->form.$this->loadTemplate( 'hidden' );
		} else {
			echo '<div class="cck_page_search'.$this->pageclass_sfx.' cck-clrfix">'.$this->form.$this->loadTemplate( 'hidden' ).'</div><div class="clr"></div>';
		}
		if ( !$this->form_wrapper ) {
			echo '</form>';
		}
	} elseif ( $this->show_form == 2 && $this->form_wrapper ) {
		echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? JUri::base( true ) : JRoute::_( 'index.php?option='.$this->option ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
	}
}

echo $this->loadTemplate( 'list' );

if ( $this->show_form ) {
	if ( $this->show_form == 2 ) {
		if ( !$this->form_wrapper ) {
			echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? JUri::base( true ) : JRoute::_( 'index.php?option='.$this->option ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
		}
		if ( $this->raw_rendering ) { 
			echo $this->form.$this->loadTemplate( 'hidden' );
		} else {
			echo '<div class="clr"></div><div class="cck_page_search'.$this->pageclass_sfx.'">'.$this->form.$this->loadTemplate( 'hidden' ).'</div>';
		}

		echo '</form>';
	} elseif ( $this->show_form == 1 && $this->form_wrapper ) {
		echo '</form>';
	}
}
if ( $this->show_list_desc == 2 && $this->description != '' ) {
	echo $description;
}
?>
<?php if ( !$this->raw_rendering ) { ?>
</div></div>
<?php } ?>
<?php if ( $this->load_ajax ) {
$context	=	'&context={\'Itemid\':'.$app->input->getInt( 'Itemid', 0 ).',\'view\':\'list\'}';
$pre		=	'';
$url		=	JUri::current();

if ( $app->input->get( 'tmpl' ) == 'raw' ) {
	$pre	=	'#seblod_form_raw ';
	
	if ( $app->input->get( 'search' ) != '' ) {
		$url	=	JCckDevHelper::getAbsoluteUrl( 'auto', 'view=list&search='.$app->input->get( 'search' ) );
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
<?php if ( $this->load_resource && $this->total ) {
	$url	=	JRoute::_( 'index.php?Itemid='.$app->input->getInt( 'Itemid', 0 ) );
	
	if ( $url == '/' ) {
		$url	=	'';
	}
	$url	=	JUri::getInstance()->toString( array( 'scheme', 'host', 'port' ) ).$url;
?>
<script type="text/javascript">
(function ($){
	JCck.Core.loadfragment = JCck.Core.getModal(<?php echo $this->json_resource ? $this->json_resource : '{}'; ?>);
	$(document).ready(function() {
		var fragment = window.location.hash;
		if (fragment != "") {
			fragment = fragment.substring(1);
			setTimeout(function() {
				JCck.Core.loadfragment.loadUrl("<?php echo $url; ?>/"+fragment+"<?php echo ( $this->tmpl_resource ? '?tmpl='.$this->tmpl_resource : '' )?>");
			}, 1);
		}
	});
})(jQuery);
</script>
<?php } ?>