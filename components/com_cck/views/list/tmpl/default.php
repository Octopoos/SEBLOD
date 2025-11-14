<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

if ( ( (int)JCck::getConfig_Param( 'validation', '3' ) > 1 ) && $this->config['validation'] != '' ) {
	JCckDev::addValidation( $this->config['validation'], $this->config['validation_options'] );
	$js	=	'if (jQuery("#'.$this->form_id.'").validationEngine("validate",task) === true) { JCck.Core.submitForm((task=="save"?"search":task), document.getElementById("'.$this->form_id.'")); }';
} else {
	$js	=	'JCck.Core.submitForm((task=="save"?"search":task), document.getElementById("'.$this->form_id.'"));';
}
$app	=	Factory::getApplication();
$doc	=	Factory::getDocument();
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
		.		'jQuery("#'.$this->form_id.'").append(\'<input type="hidden" id="return" name="return" value="'.base64_encode( Uri::getInstance()->toString() ).'">\');'
		.		'JCck.Core.submitForm(task,document.getElementById(\''.$this->form_id.'\'));'
		.	'};'
		.	'';
$doc->addScriptDeclaration( $js );
} ?>
<?php if ( !$this->raw_rendering ) { ?>
<div <?php echo $id; ?>class="cck_page cck-clrfix"><div>
<?php } elseif ( trim( $this->pageclass_sfx ) ) { ?>
<div class="<?php echo trim( $this->pageclass_sfx ); ?>">
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
if ( $this->show_list_desc && $this->description != '' && $app->input->get( 'tmpl' ) != 'raw' ) {
	$description	=	HTMLHelper::_( 'content.prepare', $this->description );
	$tag_desc		=	'';

	if ( $this->tag_desc == 'div_div' ) {
		$tag_desc	=	'div';
	}
	if ( !( $this->tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$this->tag_desc	=	'div';
	}
	if ( !$this->raw_rendering ) {
		$description	=	'<'.$this->tag_desc.' class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . $description . '</'.$this->tag_desc.'>';

		if ( $this->tag_desc == 'div' ) {
			$description	.=	'<div class="clr"></div>';
		}
	} else {
		$class			=	trim( $this->class_desc );
		$class			=	$class ? ' class="'.$class.'"' : '';

		if ( $tag_desc == 'div' ) {
			$description	=	'<div>'.$description.'</div>';
		}
		$description	=	'<'.$this->tag_desc.$class.'>' . $description . '</'.$this->tag_desc.'>';
	}
} else {
	$description	=	'';
}
if ( $this->show_list_desc == 1 && $this->description != '' ) {
	echo $description;
}
if ( $this->show_form ) {
	if ( $this->show_form == 1 ) {
		echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? Uri::base( true ) : htmlspecialchars( Uri::getInstance()->getPath() ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';

		if ( $this->raw_rendering ) {
			echo $this->form.$this->loadTemplate( 'hidden' );
		} else {
			echo '<div class="cck_page_search'.$this->pageclass_sfx.' cck-clrfix">'.$this->form.$this->loadTemplate( 'hidden' ).'</div><div class="clr"></div>';
		}
		if ( !$this->form_wrapper ) {
			echo '</form>';
		}
	} elseif ( $this->show_form == 2 && $this->form_wrapper ) {
		echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? Uri::base( true ) : htmlspecialchars( Uri::getInstance()->getPath() ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
	}
}

echo $this->loadTemplate( 'list' );

if ( $this->show_form ) {
	if ( $this->show_form == 2 ) {
		if ( !$this->form_wrapper ) {
			echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? Uri::base( true ) : htmlspecialchars( Uri::getInstance()->getPath() ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
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
<?php } elseif ( trim( $this->pageclass_sfx ) ) { ?>
</div>
<?php }
if ( $this->load_ajax ) {
$pre		=	'';
$url		=	Uri::current();

if ( $app->input->get( 'tmpl' ) == 'raw' ) {
	$pre					=	'#seblod_form_raw ';
	$this->context['tmpl']	=	'raw';
	
	if ( $app->input->get( 'search' ) != '' ) {
		$url	=	JCckDevHelper::getAbsoluteUrl( 'auto', 'view=list&search='.$app->input->get( 'search' ) );
	}
}
?>
<script type="text/javascript">
(function ($){
	JCck.Core.loadmore = function(query_params,has_more,replace_html) {
		var data_type    = 'html';
		var elem_target  = "<?php echo $pre; ?>.cck-loading-more";
		var replace_html = replace_html || 0;		
		$("form#<?php echo $this->form_id; ?> [data-cck-ajax=\'\']").each(function(i) {
			var name = $(this).attr("name");
			query_params += "&"+(name !== undefined ? name : $(this).attr("id"))+"="+$(this).myVal().replace("&", "%26");
		});
		if (has_more < 0) {
			data_type = 'json';
			query_params += "&wrapper=1";
		}
		$.ajax({
			cache: false,
			data: 'format=raw&task=search&infinite=1&context=<?php echo json_encode( $this->context ); ?>&return=<?php echo base64_encode( Uri::getInstance()->toString() ); ?>'+query_params,
			dataType: data_type,
			type: "GET",
			url: "<?php echo $url; ?>",
			beforeSend:function() { $("#seblod_form_load_more").hide(); $("#seblod_form_loading_more").show(); },
			success: function(response) {
				if (has_more < 0) {
					var $el = $("#seblod_form_load_more");
					$($el).attr("data-start",0).attr("data-end",response.total);
					if (response.total > response.count) {
						$("#seblod_form_load_more, [data-cck-loadmore-pagination]").show();
					} else {
						$("[data-cck-loadmore-pagination]").hide();
					}
					if ($("[data-cck-total]").length) {
 						$("[data-cck-total]").text(response.total);
 					}
 					if (response.html_form !== undefined) {
						for (const [key, value] of Object.entries(response.html_form)) {
							if (!$("#"+`${key}`).length) {
								continue;
							}
							if ($("#"+`${key}`+"_").length) {
								$("#"+`${key}`).remove();
								$("#"+`${key}`+"_").replaceWith(`${value}`);
							} else {
								$("#"+`${key}`).replaceWith(`${value}`);
							}
						}
					}
					response = response.html;
				} else {
					if (has_more != 1) {
						$("#seblod_form_load_more").show()<?php echo ( $this->show_pagination == 8 ) ? '.click()' : ''; ?>;
					} else {
						$("[data-cck-loadmore-pagination]").hide();
					}	
				}
				$("#seblod_form_loading_more").hide();
				if (replace_html==1) { $(elem_target).html(response); } else { $(elem_target).append(response); }
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
	};
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
	$auto_id	=	$this->autoid_resource;
	if ( !$auto_id ) {
		$auto_id	=	$app->input->getInt( 'Itemid', 0 );
	}
	$url		=	Route::_( 'index.php?Itemid='.$auto_id );
	
	if ( $url == '/' ) {
		$url	=	'';
	}
	$url	=	Uri::getInstance()->toString( array( 'scheme', 'host', 'port' ) ).$url;
?>
<script type="text/javascript">
(function ($){
	JCck.Core.loadfragment = JCck.Core.getModal(<?php echo $this->json_resource ? $this->json_resource : '{}'; ?>);
	$(document).ready(function() {
		var fragment = window.location.hash;
		if (fragment != "" && !$("form"+fragment).length) {
			fragment = fragment.substring(1);
			setTimeout(function() {
				JCck.Core.loadfragment.loadUrl("<?php echo $url; ?>/"+fragment+"<?php echo ( $this->tmpl_resource ? '?tmpl='.$this->tmpl_resource : '' )?>");
			}, 1);
		}
	});
})(jQuery);
</script>
<?php } ?>