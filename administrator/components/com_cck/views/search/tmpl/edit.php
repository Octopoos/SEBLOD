<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$ajax_load	=	'components/com_cck/assets/styles/seblod/images/ajax.gif';
$config		=	JCckDev::init( array( '42', 'jform_accesslevel', 'radio', 'select_simple', 'text', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck		=	JCckDev::preload( array( 'core_title_search', 'core_folder', 'core_description', 'core_state', 'core_client_search',
										 'core_layer', 'core_storage_location2', 'core_location2', 'core_alias', 'core_access' ) );
$lang		=	JFactory::getLanguage();
$key		=	'COM_CCK_TRANSLITERATE_CHARACTERS';
$style		=	'seblod';
if ( $lang->hasKey( $key ) == 1 ) {
	$transliterate	=	JText::_( $key );
	$transliterate	=	'{"'.str_replace( array( ':', '||' ), array( '":"', '","' ), $transliterate ).'"}';
} else {
	$transliterate	=	'{}';
}
if ( JCck::on() ) {
	JHtml::_( 'bootstrap.tooltip' );
	$sidebar_inner	=	288;
	$sidebar_top	=	88;
	$tooltipJS		=	'';
} else {
	$sidebar_inner	=	0;
	$sidebar_top	=	0;
	$tooltipJS		=	'$("a[title].qtip_cck").qtip({ style: { classes: "ui-tooltip-cck-<?php echo $style; ?> ui-tooltip-shadow" }, position: { my: "right center", at: "left center" } });'
					.	'$("label[title]").qtip({ style: { classes: "ui-tooltip-cck-<?php echo $style; ?> ui-tooltip-shadow" }, position: { my: "top left", at: "bottom left" } });';	
}
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

if ( $this->item->client == 'list' ) {
	$block_item	=	( strpos( @$this->style->params, '"cck_client_item":"1"' ) !== false || strpos( @$this->style->params, '"cck_client_item":"2"' ) !== false ) ? 0 : 1;	
} else {
	if ( $this->item->template_list ) {
		$params		=	JCckDatabase::loadResult( 'SELECT params FROM #__template_styles WHERE id = '.(int)$this->item->template_list );
		$block_item	=	( strpos( $params, '"cck_client_item":"1"' ) !== false || strpos( $params, '"cck_client_item":"2"' ) !== false ) ? 0 : 1;	
	} else {
		$block_item	=	1;
	}
}
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?> full">
	<div class="seblod first">
    	<div>
            <div id="loading" class="loading"></div>
            <ul class="spe spe_title">
                <?php
                echo JCckDev::renderForm( $cck['core_title_search'], $this->item->title, $config );        
                echo '<input type="hidden" id="name" name="name" value="'.$this->item->name.'" />';
                ?>
            </ul>
            <ul class="spe spe_folder">
				<?php echo JCckDev::renderForm( $cck['core_folder'], $this->item->folder, $config, array( 'label'=>_C0_TEXT ) ); ?>
            </ul>
            <ul class="spe spe_state spe_third">
                <?php echo JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>'clear' ) ); ?>
            </ul>
            <ul class="spe spe_description">
                <?php echo JCckDev::renderForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
            </ul>
        </div>
		<div class="clr"></div>
        <div class="togglebar">
        	<div>
			<?php
			echo JCckDev::getForm( $cck['core_client_search'], $this->item->client, $config );
			echo JCckDev::getForm( $cck['core_layer'], $this->item->layer, $config );
            ?>
        	</div>
        </div>
        <div id="toggle_more" class="toggle_more <?php echo $this->panel_class; ?>"></div>
		<input type="hidden" id="storage_location" name="storage_location" value="joomla_article" />
	</div>
	<div class="seblod first" id="more" style="<?php echo $this->panel_style; ?>height:<?php echo $this->css['panel_height']; ?>;">
    	<div>
            <ul class="spe spe_title">
				<?php echo JCckDev::renderForm( $cck['core_alias'], $this->item->alias, $config ); ?>
            </ul>
            <ul class="spe spe_folder">
				<?php echo JCckDev::renderForm( $cck['core_storage_location2'], $this->item->storage_location, $config, array( 'selectlabel'=>'Select', 'attributes'=>'style="width:140px;"' ) ); ?>
            </ul>
            <ul class="spe spe_third">
				<?php echo JCckDev::renderForm( $cck['core_access'], $this->item->access, $config, array( 'css'=>'max-width-180' ) ); ?>
            </ul>
			<ul class="spe spe_name">
				<?php
				if ( !$this->item->id ) {
					echo '<li><label>'.JText::_( 'COM_CCK_QUICK_MENU_ITEM' ).'</label>'
					 .	 '<select id="quick_menuitem" name="quick_menuitem" class="inputbox" style="max-width:180px;">'
					 .	 '<option value="">- '.JText::_( 'COM_CCK_SELECT_A_PARENT').' -</option>'
					 .	 JHtml::_( 'select.options', JHtml::_( 'menu.menuitems' ) )
					 .	 '</select></li>';
				} else {
					echo '<li><label>'.JText::_( 'COM_CCK_SEARCH_ASSIGNMENTS' ).'</label><span class="variation_value">...</span></li>';
				}
				?>
			</ul>
            <ul class="spe spe_type">
            	<?php echo JCckDev::renderForm( $cck['core_location2'], $this->item->location, $config, array( 'attributes'=>'style="width:140px;"' ) ); ?>
            </ul>
			<ul class="spe spe_sixth">
				<?php echo JCckDev::renderForm( 'core_css_core', $this->item->stylesheets, $config, array( 'label'=>'Stylesheets', 'css'=>'max-width-180', 'storage_field'=>'stylesheets' ) ); ?>
            </ul>
        </div>
	</div>
</div>

<div class="clr"></div>
<div align="center" id="layers"></div>

<div>
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
	<input type="hidden" id="element" name="element" value="search" />
	<?php
	if ( $this->isNew && $this->tpl_list && !$this->item->skip ) {
		echo '<input type="hidden" id="tpl_list" name="tpl_list" value="'.$this->tpl_list.'" />';
	} elseif ( $this->item->skip ) {
		echo '<input type="hidden" id="cck_type" name="cck_type" value="'.$this->item->cck_type.'" />';
	}
    echo $this->form->getInput( 'id' );
	JCckDev::validate( $config );
    echo JHtml::_( 'form.token' );
	?>
</div>
</form>

<?php
Helper_Display::quickCopyright();
?>

<script type="text/javascript">
(function ($){
	JCck.Dev = {
		transliteration:<?php echo $transliterate; ?>,
		trash:"",
		ajaxLayer: function(view, layout, elem, mydata, uix) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";  
			$.ajax({
				cache: false,
				data: mydata,
				type: "POST",
				url: "index.php?option=com_cck&view="+view+"&layout="+layout+"&format=raw",
				beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
				success: function(response){ $("#loading").html(""); $(elem).html(response); JCck.Dev.setEdit2(uix);},
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		ajaxTask: function(task, elem, mydata, uix) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";
			$("#task").val(task);
			$.ajax({
				cache: false,
				data: $("#adminForm").serialize()+mydata,
				type: "POST",
				url: 'index.php?option=com_cck&task='+task,
				beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
				success: function(response){ $("#loading").html(""); $(elem).html(response); JCck.Dev.setEdit2(uix); },
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		doSubmit: function() {
			var uix = '<?php echo $this->uix; ?>';
			var client = $("#"+$("#client .selected").attr('for')).val();
			$("#layer label").removeClass('selected');
			$("#layer input").removeAttr("checked"); $("#layer2").prop("checked", true);
			JCck.Dev.preSubmit(); JCck.Dev.ajaxTask("search.apply", "#layers", "&layout=edit2"+"&brb=workshop&redirappend=client\="+client+" layer\=fields format=raw", uix);
			$("#layer2_label").addClass('selected');
		},
		getPane: function() {
			return $(".seblod-toolbar .panel.selected").html() - 1;
		},
		move: function(elem) {
			var selected = $('li.ui-selected');
			if ( elem ) {
				selected.appendTo( $(elem) );
				JCck.Dev.switchP(-1, '.ui-selected');
			} else {
				var p = $('input:radio[name="positions"]:checked').attr('golast');
				if (p && p != "undefined") {
					$(p).before(selected);	
					JCck.Dev.switchP(JCck.Dev.getPane(), '.ui-selected');
				}
			}
			JCck.Dev.unselect(selected);
		},
		moveBottom: function() {
			var selected = $('li.ui-selected');
			var p = $('input:radio[name="positions"]:checked').attr('golast');
			if (p && p != "undefined") {
				$(p).before(selected);
				JCck.Dev.unselect(selected);
			}
		},
		moveDir: function(key, dir) {
			var selected = $('#'+key);
			var elem = selected.parent().attr("id");
			if ( elem[elem.length - 1] == '1' ) {
				elem = '#'+elem.substr(0,elem.length-1)+'2';
				if ($(elem).children().length) {
					$(elem).children().first().before(selected);
				} else {
					selected.appendTo($(elem));
				}
				JCck.Dev.switchP(-1, key);
			} else {
				var p = $('input:radio[name="positions"]:checked').attr('golast');
				if (p && p != "undefined") {
					$(p).before(selected);
					JCck.Dev.switchP(JCck.Dev.getPane(), key);
				}
			}
			JCck.Dev.unselect(selected);
		},
		moveTop: function() {
			var selected = $('li.ui-selected');
			var p = $('input:radio[name="positions"]:checked').attr('gofirst');
			if (p && p != "undefined") {
				$(p).after(selected);
				JCck.Dev.unselect(selected);
			}
		},
		preSubmit: function() {
			$("#sortable2").remove();
		},
		previewPositions: function() {
			var template = $("#template").val();
			if ( template ) {
				var img = "<?php echo JURI::root(); ?>templates/"+$("#template").val()+"/template_preview.png";
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file="+img;
				$.fn.colorbox({href:url, iframe:true, innerWidth:850, innerHeight:585, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		},
		setEdit2: function(uix) {
			var wo = <?php echo $sidebar_inner;?>;
			if (wo) {
				var wh = $(window).height();
				$("#scroll").height(wh-wo);
			}
			$(window).scroll(function() {
				var sidebarPos = $("#seblod-sidebar").offset();
				var winScroll = $(window).scrollTop();
				if (winScroll > sidebarPos.top) {
					JCck.Dev.setSidebar();
				} else {
					if ($("#seblod-sideblock").css('position') == 'fixed') {
						$("#seblod-sideblock").css({'position' : 'relative'});
						$("#seblod-sideblock").css({'top' : '0px'});
					}
				}
			});
			$("#sortable1").parent().css({"overflow" : "visible"});
			$("#sortable2").parent().parent().css({"overflow" : "visible"});
			$("#pos-1").addClass("boundary");
			/* -- */
			var sortable_ids = (uix=="compact") ? "#sortable1" : "#sortable1, #sortable2";
			$(sortable_ids).sortable({
				cancel		: "input,.ui-state-disabled",
				containment : "document",
				connectWith : ".connected",
				items 		: "li:not(.boundary)",
				handle 		: ".drag",
				placeholder : "drop-highlight",
				scroll		: true,
				forceHelperSize: true,
				start		: function(event, ui) {
					JCck.Dev.switchP(-1, ui.item.attr("id"));
					ui.item.css({"top":"0","left":"0"}); /* ~Fix */
				},
				stop		: function(event, ui) {
					var pid = ui.item.parent().attr("myid");
					if ( pid == 1 ) {
						JCck.Dev.switchP(JCck.Dev.getPane(), ui.item.attr("id") );
					} else {
						JCck.Dev.switchP(-1, ui.item.attr("id") );
					}		
					ui.item.css({"top":"0","left":"0"}); /* ~Fix */
				}
			});
			$("#sortable1, #sortable2").selectable({
				filter		: "li.field",
				cancel		: "input,img,option,select,span.c_live,span.c_live2,span.c_mat,span.c_mat2,span.c_val,span.c_typo,span.c_link,span.c_res,span.c_comp,span.c_cond,.cbox,.ui-state-disabled,span.c_cancelled",
			});
			/* -- */
			$(".wysiwyg_editor_box").colorbox({iframe:true, innerWidth:820, innerHeight:420, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			<?php echo $tooltipJS; ?>
		},
		setSidebar: function() {
			var w = $("#seblod-sideblock").width();
			var sidebarPos = $("#seblod-sidebar").offset();
			var winScroll = $(window).scrollTop();
			var mainHeight = $("#seblod-main").height();
			var sidebarHeight = $("#seblod-sideblock").height();
			if ( sidebarHeight > mainHeight ) {
				if ( $("#seblod-sidebar").hasClass('active') ) {
					$("#seblod-sidebar").removeClass('active');
					$("#seblod-sidebar").addClass('passive');
				}
			} else {
				if ( winScroll > sidebarPos.top ) {
					var mainPos = $("#seblod-main").offset();
					if ( $("#seblod-sideblock").css('position') != 'fixed' ) {
						$("#seblod-sideblock").css({'position' : 'fixed'});
						$("#seblod-sideblock").css({'width' : w});
					}
					if ( (winScroll+sidebarHeight) > (mainHeight+mainPos.top)-10 ) {
						var winHeight = $(document).height();
						var cleanerPos = $("#seblod-cleaner").offset();
						var footer = winHeight - cleanerPos.top;
						$("#seblod-sideblock").css({'top' : ''});
						$("#seblod-sideblock").css({'bottom' : footer+'px'});
					} else {
						$("#seblod-sideblock").css({'top' : '<?php echo $sidebar_top; ?>px'});
						$("#seblod-sideblock").css({'bottom' : ''});
					}
				}
			}
		},
		submit: function(task) {
			Joomla.submitbutton(task);
		},
		switchP: function(cur, elem) {
			var n = 7;
			elem2 = ( elem ) ? (elem[0] == '.' ? elem+' ' : '#'+elem+' ') : '#seblod-main ';
			elem  = ( elem ) ? (elem[0] == '.' ? elem+' ' : '#'+elem+' ') : '#sortable1 ';
			if ( cur == -1 ) {
				for ( var i = 0; i < n; i++ ) { var p = i + 1; if($(elem+'.p'+p)) { $(elem+'.p'+p).addClass('hide'); } }
			} else if ( cur == -2 ) {
				$(elem+'.p'+1).removeClass('hide');
				$(elem+'.p'+2).removeClass('hide');
			} else {
				for ( var i = 0; i < n; i++ ) {
					var p = i + 1;
					if ( i == cur ) {
						$(elem+'.p'+p).removeClass('hide');
						$(elem2+'.ph'+p).removeClass('hide');
					} else {
						if ($(elem+'.p'+p)) { $(elem+'.p'+p).addClass('hide'); }
						if ($(elem2+'.ph'+p)) { $(elem2+'.ph'+p).addClass('hide'); }
					}
				}
			}
		},
		switchDisplay: function(mode) {
			var map = $(mode).attr("data-cck_client_item");
			if (map) {
				map = map.replace(/"/g,"");
				if (map) {
					var tab = map.split(',');
					var len = tab.length;
					var v   = $(mode).val();
					var $el = $("#params_cck_client_item");
					if ($el.length) {
						for (i = 0; i < len; i++) {
							var cur = tab[i].split("=");
							if (v == cur[0]) {
								$el.val(cur[1]);
								break;
							}
						}
					}
				}
			}
		},
		switchPos: function() {
			if ($('ul.sortable_header .ph11').hasClass('hide')) { $('ul.sortable_header .ph11').removeClass('hide'); } else {$('ul.sortable_header .ph11').addClass('hide');}
			$('#sortable1 .la').toggle();
			$('#sortable1 .lb').toggle();
		},
		transliterateName: function() {
			if ($("span.insidebox").length > 0) { var p = $("span.insidebox").html()+"_"; } else { var p = ""; }
			var str = JCck.transliterate(p+$("#title").val(),JCck.Dev.transliteration);
			$("#name").val( str.toLowerCase().replace(/^\s+|\s+$/g,"").replace(/\s/g, "_").replace(/[^a-z0-9_]/gi, "") );
		},
		unselect: function(selected) {
			if ( selected.hasClass('ui-selected') ) {
				selected.removeClass('ui-selected', 600);
			}
		}
	}
	Joomla.submitbutton = function(task) {
		if (task == "search.cancel") {
			$("#layers").remove(); JCck.submitForm(task, document.getElementById('adminForm'));	
		} else {
			if ($("#adminForm").validationEngine("validate",task) === true) {
				JCck.Dev.preSubmit(); JCck.submitForm(task, document.getElementById('adminForm'));
			}
		}
	}
	$(document).ready(function(){
		var uix = '<?php echo $this->uix; ?>';
		var client = $("#"+$("#client .selected").attr('for')).val(); var layer = $("#"+$("#layer .selected").attr('for')).val();
		var block_item = <?php echo $block_item; ?>;
		if (block_item) {
			$("#client5_label").addClass("disabled"); $("#client5").prop("disabled", true);
			if (client == "item") {	client = "list"; $("#client4_label").addClass("selected");	$("#client5_label").removeClass("selected"); }
		}
		var name = $("#name").val();
		$.cookie("cck_search"+name+"_client", client);
		var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer+"&skip=<?php echo $this->item->skip; ?>";
		JCck.Dev.ajaxLayer("search", "edit2", "#layers", data, uix);
		$("fieldset#client").on("click", "label", function() {
			if ( $("#title").validationEngine("validate") === false ) {
				if (!($("#"+$(this).attr('for')).prop("disabled")===true)) {
					if (!$(this).hasClass("off")) {
						name = $("#name").val();
						$("#client label").addClass("off");
						$("#client label").removeClass('selected'); $(this).addClass('selected');
						var client = $("#"+$(this).attr('for')).val(); var layer = $("#"+$("#layer .selected").attr('for')).val();
						$.cookie("cck_search"+name+"_client", client);
						var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer;
						JCck.Dev.preSubmit(); JCck.Dev.ajaxTask("search.apply", "#layers", "&layout=edit2"+"&brb=workshop&redirappend=client\="+client+" layer\="+layer+" format=raw", uix);
						$("#client input").removeAttr("checked"); $("#"+$(this).attr('for')).attr("checked", "checked");
						$("#client label").removeClass("off");
					}
				}
			}
		});
		var insidebox = '<?php echo $this->insidebox; ?>';
		if (insidebox) { $("#title").after(insidebox); }
		$("div#layers").on("change", "select.filter", function() {
			if (JCck.Dev.trash) { $("#sortable2").append(JCck.Dev.trash); }
			var chk = "";
			for (var j=1; j<=4; j++) {			
				var c = $("#filter"+j).attr("prefix");
				var v = $("#filter"+j).val();
				var k = (v) ? ",#sortable2 li:not(."+c+v+")" : "";
				chk += k;
			}
			JCck.Dev.trash = $(chk.substr(1)).hide().detach();
			$("#sortable2 li").show();
		});
		$("div#layers").on("click", "a.panel", function() {
			var v = $(this).html();
			if (parseInt(v)>0) {
				$(".seblod-toolbar .panel").removeClass("selected");
				$(this).addClass("selected");
				JCck.Dev.switchP(v-1);
			} else {
				JCck.Dev.switchPos();
			}
		});
		$("div#layers").on("change", "select.c_live_ck", function() {
			var field = $(this).parents().eq(3).attr("id");
			var cid = $(this).attr("id");
			$("#"+cid+"_options").val("");
			if(this.value!="") { $("#"+field+" .c_live0").addClass('hide'); $("#"+field+" .c_live").removeClass('show').addClass('hide'); $("#"+field+" .c_live2").removeClass('hide');}
			else { $("#"+field+" .c_live0").removeClass('hide'); $("#"+field+" .c_live").removeClass('hide').addClass('show'); $("#"+field+" .c_live2").addClass('hide');}
		});
		$("div#layers").on("click", "span.c_live", function() {
			var field = $(this).attr("name");
			var cur = $("#"+field+"_live").val();
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=search&name="+field+"&type="+field+"_live_value&id="+cur;
			$.fn.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		});
		$("div#layers").on("click", "span.c_live2", function() {
			var field = $(this).attr("name");
			var live = $("#"+field+"_live").val();
			if (live) {
				if (live == "stage") {
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/stage.php&id="+field+"&validation=1";
				} else {
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_live/"+live+"/tmpl/edit.php&id="+field+"&name="+live+"&validation=1";
				}
				$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		});
		$("div#layers").on("change", "select.c_var_ck", function() {
			var pos = $(this).parents().eq(3).attr("id");
			if(this.value!="") { $("#"+pos+" .c_var").removeClass('hidden'); } else { $("#"+pos+" .c_var").addClass('hidden'); }
		});
		$("div#layers").on("click", "span.c_var", function() {
			var pos = $(this).attr("name");
			var variation = $("#pos-"+pos+"_variation").val();
			if (variation) {
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=libraries/cck/rendering/variations/edit.php&id="+pos+"&name="+variation+"&type="+$("#template").val();
				$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		});
		$("div#layers").on("contextmenu", "span.c_val", function() {
			var field = $(this).attr("name");
			var validation = $("#"+field+"_validation").val() ? " + 1" : "";
			if ($("#"+field+"_required").val()) {
				$(this).html("<?php echo JText::_( 'COM_CCK_OPTIONAL' )?>"+validation);
				$("#"+field+"_required").val("");
			} else {
				$(this).html("<?php echo JText::_( 'COM_CCK_REQUIRED' )?>"+validation);
				$("#"+field+"_required").val("required");
			}
			return false;
		});
		$("div#layers").on("click", "span.c_val", function() {
			var field = $(this).attr("name");
			var validation = $("#"+field+"_validation").val();
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/validation.php&type=search&id="+field+"&name="+validation+"&validation=1";
			$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		});
		$("div#layers").on("change", "select.c_typo_ck", function() {
			var field = $(this).parents().eq(3).attr("id");
			var cid = $(this).attr("id");
			$("#"+cid+"_options").val("");
			if(this.value!="") { $("#"+field+" .c_typo").removeClass('hidden'); } else { $("#"+field+" .c_typo").addClass('hidden'); }
		});
		$("div#layers").on("click", "span.c_typo", function() {
			var field = $(this).attr("name");
			var typo = $("#"+field+"_typo").val();
			if (typo) {
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_typo/"+typo+"/tmpl/edit.php&id="+field+"&name="+typo+"&validation=1";
				$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		});
		$("div#layers").on("change", "select.c_link_ck", function() {
			var field = $(this).parents().eq(3).attr("id");
			var cid = $(this).attr("id");
			$("#"+cid+"_options").val("");
			if(this.value!="") { $("#"+field+" .c_link").removeClass('hidden'); } else { $("#"+field+" .c_link").addClass('hidden'); }
		});
		$("div#layers").on("click", "span.c_link", function() {
			var field = $(this).attr("name");
			var link = $("#"+field+"_link").val();
			if (link) {
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_link/"+link+"/tmpl/edit.php&id="+field+"&name="+link+"&validation=1";
				$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		});
		$("div#layers").on("change", "select.c_res_ck", function() {
			var field = $(this).parents().eq(3).attr("id");
			var cid = $(this).attr("id");
			$("#"+cid+"_options").val("");
			if(this.value!="") { $("#"+field+" .c_res").removeClass('hidden'); } else { $("#"+field+" .c_res").addClass('hidden'); }
		});
		$("div#layers").on("click", "span.c_res", function() {
			var field = $(this).attr("name");
			var restriction = $("#"+field+"_restriction").val();
			if (restriction) {
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_restriction/"+restriction+"/tmpl/edit.php&id="+field+"&name="+restriction+"&validation=1";
				$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		});
		$("div#layers").on("change", "select.c_mat_ck", function() {
			var field = $(this).parents().eq(3).attr("id");
			if(this.value!="none") { $("#"+field+" .c_mat").removeClass('hidden'); } else { $("#"+field+" .c_mat").addClass('hidden'); }
		});
		$("div#layers").on("click", "span.c_mat", function() {
			var id = $("#jform_id").val();
			var field = $(this).attr("name");
			var match_mode = $("#"+field+"_match_mode").val();
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/match.php&id="+id+"&name="+field+"&validation=1";
			$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		});
		$("div#layers").on("click", "span.c_mat2", function() {
			var id = $("#jform_id").val();
			var field = $(this).attr("name");
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/ordering.php&id="+id+"&name="+field+"&validation=1";
			$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		});
		$("div#layers").on("click", "span.c_comp", function() {
			var id = $("#jform_id").val();
			var field = $(this).attr("name");
			var computation = $("#ffp_"+field+"_computation").val();
			computation = computation.replace(/#/gi, "");
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/computation.php&id="+id+"&type=type&name="+field+"&title="+computation;
			$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		});
		$("div#layers").on("click", "span.c_cond", function() {
			var id = $("#jform_id").val();
			var field = $(this).attr("name");
			var conditional = $("#ffp_"+field+"_conditional_options").val();
			if (conditional!="") {
				var conditionals = ( conditional != "" ) ? $.parseJSON(conditional) : "";
				conditional = ( conditionals.length === undefined ) ? 1 : conditionals.length;
			} else {
				conditional = 1;
			}
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/conditional.php&id="+id+"&type=search&name="+field+"&title="+conditional;
			$.fn.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		});
		$("#toggle_more").css({"top":($("#toggle_more").parent().height() + 12)});
		$("#toggle_more").live("click", function() {
			if ($("#toggle_more").hasClass("open")){ $("#toggle_more").removeClass("open").addClass("closed"); } else { $("#toggle_more").removeClass("closed").addClass("open"); }
			$("#more").slideToggle("fast");
		});
	});
})(jQuery);
</script>