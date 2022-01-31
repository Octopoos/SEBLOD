/* Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved. */
if("undefined"===typeof JCck)var JCck={};
(function($) {
	JCck.DevHelper = {
		ajaxAddType: function(title, fields) {
			var client = $("#"+$("#client .selected").attr('for')).val();
			var folder_id = $("#folder").val();
			var typeid = $("#myid").val();
			$.ajax({
				cache: false,
				data: "title="+title+"&folder_id="+folder_id+"&client="+client+"&fields="+fields+"&type_id="+typeid+"&"+JCck.Dev.token,
				type: "POST",
				url: "index.php?option=com_cck&task=addTypeAjax&format=raw",
				beforeSend:function(){},
				success: function(response) {
					JCck.DevHelper.moveAcross('#sortable2');
					var obj = jQuery.parseJSON(response);
					var elem = $('input:radio[name="positions"]:checked').attr('golast');
					if (!(!elem || elem=="undefined")) {
						$(elem).before(obj.html);
						$("#"+obj.id).addClass("ui-selected");
					}},
				error:function(){}
			});
		},
		ajaxLayer: function(view, layout, elem, mydata, uix) {
			$.ajax({
				cache: false,
				data: mydata,
				type: "GET",
				url: "index.php?option=com_cck&view="+view+"&layout="+layout+"&format=raw",
				beforeSend:function(){
					if (!(layout == "edit3" || layout == "edit4")) {
						$("#seblod-loading").css("display","block");
					}
					$(elem).html("");
				},
				success: function(response){
					if (layout == "edit2") {
						$("#seblod-loading").css("display","none"); $(elem).hide().html(response).fadeIn(88); JCck.DevHelper.setEdit2(uix); $("#client label").removeClass("off");
						// $("#layer_fields").css({"left":(($("#content").width() - ( $("#layers .span8").width() + $("#layers .span4").width() + 9 ))/2)+"px"});
						JCck.DevHelper.ajaxLayer(JCck.Dev.name, "edit3", "#sortable3", mydata, uix);
					} else if (layout == "edit3") {
						document.getElementById("sortable3").innerHTML = response;
						JCck.DevHelper.updateTrash("#sortable3");
						JCck.DevHelper.ajaxLayer(JCck.Dev.name, "edit4", "#sortable3", mydata, uix);
					} else if (layout == "edit4") {
						document.getElementById("sortable3").innerHTML = response;
						JCck.DevHelper.updateTrash("#sortable3");
					} else {
						$("#seblod-loading").css("display","none"); $(elem).html(response); JCck.DevHelper.setEdit2(uix);
					}
				},
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		ajaxTask: function(task, elem, mydata, uix) {
			$("#task").val(task);
			var client = $("#"+$("#client .selected").attr('for')).val(); var layer = $("#"+$("#layer .selected").attr('for')).val();
			var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer;
			$.ajax({
				cache: false,
				data: $("#adminForm").serialize()+mydata,
				type: "POST",
				url: 'index.php?option=com_cck&task='+task,
				beforeSend:function(){ $("#seblod-loading").css("display","block"); $(elem).html(""); JCck.Dev.trash=""; },
				success: function(response){
					$("#seblod-loading").css("display","none"); $(elem).hide().html(response).fadeIn(88); JCck.DevHelper.setEdit2(uix); $("#client label").removeClass("off");
					var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer;
					JCck.DevHelper.ajaxLayer(JCck.Dev.name, "edit3", "#sortable3", data, uix);
				},
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		doSubmit: function() {
			var client = $("#"+$("#client .selected").attr('for')).val();
			$("#layer label").removeClass('selected');
			$("#layer input").prop("checked", false); $("#layer2").prop("checked", true);
			JCck.DevHelper.preSubmit(); JCck.DevHelper.ajaxTask(JCck.Dev.name+".apply", "#layers", "&layout=edit2"+"&brb=workshop&redirappend=client\="+client+" layer\=fields format=raw", JCck.Dev.uix);
			$("#layer2_label").addClass('selected');
		},
		getPane: function(loc) {
			var loc = loc || "";
			return (loc == "parent") ? parent.jQuery(".seblod-toolbar .panel.selected").html() - 1 : $(".seblod-toolbar .panel.selected").html() - 1;
		},
		moveAcross: function(elem) {
			var selected = $('li.ui-selected');
			if ( elem ) {
				selected.appendTo( $(elem) );
				JCck.DevHelper.switchP(-1, '.ui-selected');
			} else {
				var p = $('input:radio[name="positions"]:checked').attr('golast');
				if (p && p != "undefined") {
					$(p).before(selected);	
					JCck.DevHelper.switchP(JCck.DevHelper.getPane(), '.ui-selected');
				}
			}
			JCck.DevHelper.unselect(selected);
		},
		moveBottom: function() {
			var selected = $('li.ui-selected');
			var p = $('input:radio[name="positions"]:checked').attr('golast');
			if (p && p != "undefined") {
				$(p).before(selected);
				JCck.DevHelper.switchP(JCck.DevHelper.getPane(), '.ui-selected');
				JCck.DevHelper.unselect(selected);
			}
		},
		move: function(key, dir) {
			var selected = $('#'+key);
			var elem = selected.parent().attr("id");
			if ( elem[elem.length - 1] == '1' ) {
				elem = '#'+elem.substr(0,elem.length-1)+'2';
				if ($(elem).children().length) {
					$(elem).children().first().before(selected);
				} else {
					selected.appendTo($(elem));
				}
				JCck.DevHelper.switchP(-1, key);
			} else {
				var p = $('input:radio[name="positions"]:checked').attr('golast');
				if (p && p != "undefined") {
					$(p).before(selected);
					JCck.DevHelper.switchP(JCck.DevHelper.getPane(), key);
				}
			}
			JCck.DevHelper.unselect(selected);
		},
		moveTop: function() {
			var selected = $('li.ui-selected');
			var p = $('input:radio[name="positions"]:checked').attr('gofirst');
			if (p && p != "undefined") {
				$(p).after(selected);
				JCck.DevHelper.switchP(JCck.DevHelper.getPane(), '.ui-selected');
				JCck.DevHelper.unselect(selected);
			}
		},
		preSubmit: function() {
			$("#sortable2").remove();
		},
		previewPositions: function() {
			var template = $("#template").val();
			if ( template ) {
				var img = JCck.Dev.root+"templates/"+$("#template").val()+"/template_preview.png";
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file="+img;
				$.colorbox({href:url, iframe:true, innerWidth:850, innerHeight:585, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			}
		},
		setEdit2: function(uix) {
			var wo = JCck.Dev.sb_inner;
			if (wo) {
				var wh = $(window).height();
				$("#scroll").height(wh-wo);
			}
			$(window).scroll(function() {
				var $sidebar = $("#seblod-sidebar");
				var winScroll = $(window).scrollTop();
				var diff = ( $sidebar.length ) ? $sidebar.offset().top : 0;
				if (winScroll > diff) {
					JCck.DevHelper.setSidebar();
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
					JCck.DevHelper.switchP(-1, ui.item.attr("id"));
					ui.item.css({"top":"0","left":"0"}); /* ~Fix */
				},
				stop		: function(event, ui) {
					var pid = ui.item.parent().attr("myid");
					if ( pid == 1 ) {
						JCck.DevHelper.switchP(JCck.DevHelper.getPane(), ui.item.attr("id") );
					} else {
						JCck.DevHelper.switchP(-1, ui.item.attr("id") );
					}		
					ui.item.css({"top":"0","left":"0"}); /* ~Fix */
				}
			});
			$("#sortable1, #sortable2").selectable({
				filter		: "li.field",
				cancel		: "input,img,option,select,span.c_live,span.c_live2,span.c_mat,span.c_mat2,span.c_val,span.c_typo,span.c_link,span.c_res,span.c_comp,span.c_cond,.cbox,.ui-state-disabled,span.c_cancelled,.sp2se,.se2sp",
			});
			$(".wysiwyg_editor_box").colorbox({iframe:true, innerWidth:820, innerHeight:420, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			$('#options_redirection_itemid').isVisibleWhen('options_redirection','content');
			$('#options_redirection_url').isVisibleWhen('options_redirection','url');
			$('#options_redirection_url_no_access').isVisibleWhen('options_action_no_access','redirection');
		},
		setSidebar: function() {
			var w = $("#seblod-sideblock").width();
			var winScroll = $(window).scrollTop();
			var mainHeight = $("#seblod-main").height();
			var sidebarHeight = $("#seblod-sideblock").height();
			if ( sidebarHeight > mainHeight ) {
				if ( $("#seblod-sidebar").hasClass('active') ) {
					$("#seblod-sidebar").removeClass('active');
					$("#seblod-sidebar").addClass('passive');
				}
			} else {
				var $sidebar = $("#seblod-sidebar");
				var diff = ( $sidebar.length ) ? $sidebar.offset().top : 0;
				if ( winScroll > diff ) {
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
						$("#seblod-sideblock").css({'top' : JCck.Dev.sb_top+'px'});
						$("#seblod-sideblock").css({'bottom' : ''});
					}
				}
			}
		},
		submit: function(task) {
			Joomla.submitbutton(task);
		},
		switchP: function(cur, elem, loc) {
			var loc = loc || "";
			var n = ( loc == "parent" ) ? parent.JCck.Dev.count : JCck.Dev.count;
			elem2 = ( elem ) ? (elem[0] == '.' ? elem+' ' : '#'+elem+' ') : '#seblod-main ';
			elem  = ( elem ) ? (elem[0] == '.' ? elem+' ' : '#'+elem+' ') : '#sortable1 ';
			if ( cur == -1 ) {
				for ( var i = 0; i < n; i++ ) {
					var p = i + 1;
					if (loc == "parent") {
						if(parent.jQuery(elem+'.p'+p)) { parent.jQuery(elem+'.p'+p).addClass('hide'); }
					} else {
						if($(elem+'.p'+p)) { $(elem+'.p'+p).addClass('hide'); }	
					}
				}
			} else if ( cur == -2 ) {
				if (loc == "parent") {
					parent.jQuery(elem+'.p'+1).removeClass('hide');
					parent.jQuery(elem+'.p'+2).removeClass('hide');	
				} else {
					$(elem+'.p'+1).removeClass('hide');
					$(elem+'.p'+2).removeClass('hide');	
				}
			} else {
				for ( var i = 0; i < n; i++ ) {
					var p = i + 1;
					if ( i == cur ) {
						if (loc == "parent") {
							parent.jQuery(elem+'.p'+p).removeClass('hide');
							parent.jQuery(elem2+'.ph'+p).removeClass('hide');
						} else {
							$(elem+'.p'+p).removeClass('hide');
							$(elem2+'.ph'+p).removeClass('hide');
						}
					} else {
						if (loc == "parent") {
							if (parent.jQuery(elem+'.p'+p)) { parent.jQuery(elem+'.p'+p).addClass('hide'); }
							if (parent.jQuery(elem2+'.ph'+p)) { parent.jQuery(elem2+'.ph'+p).addClass('hide'); }
						} else {
							if ($(elem+'.p'+p)) { $(elem+'.p'+p).addClass('hide'); }
							if ($(elem2+'.ph'+p)) { $(elem2+'.ph'+p).addClass('hide'); }
						}
					}
				}
			}
			$('select.filter').blur();
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
		},
		update: function($el, type, target) {
			var p = $el.parent();
			var cid = $el.attr("data-"+target);
			if (type == "restriction") {
				$("#"+cid+"_options").val("");
				if($("#"+cid).val()!="") { $(p).find(".c_res").removeClass('hidden'); } else { $(p).find(".c_res").addClass('hidden'); }
			} else if (type == "live") {
				var p = $el.parents().eq(3);
				$("#"+cid+"_options").val("");
				if($("#"+cid).val()!="") { $(p).find(".c_live0").addClass('hide'); $(p).find(".c_live").removeClass('show').addClass('hide'); $(p).find(".c_live2").removeClass('hide');}
				else { $(p).find(".c_live0").removeClass('hide'); $(p).find(".c_live").removeClass('hide').addClass('show'); $(p).find(".c_live2").addClass('hide');}
			} else if (type == "typo") {
				$("#"+cid+"_options").val("");
				if($("#"+cid).val()!="") { $(p).find(".c_typo").removeClass('hidden'); } else { $(p).find(".c_typo").addClass('hidden'); }
			} else if (type == "link") {
				$("#"+cid+"_options").val("");
				if($("#"+cid).val()!="") { $(p).find(".c_link").removeClass('hidden'); } else { $(p).find(".c_link").addClass('hidden'); }
			} else if (type == "match_mode") {
				if($("#"+cid).val()!="none") { $(p).find(".c_mat").removeClass('hidden'); } else { $(p).find(".c_mat").addClass('hidden'); }
			}
		},
		updateTrash: function(target) {
			if (JCck.Dev.trash) { $(target).prepend(JCck.Dev.trash); }
			JCck.Dev.trash = $(target+" li").detach();
			
			$(target+" select").each(function(i) {
				if (!$("#layer_fields_options #"+$(this).attr("id")).length) {
					$(this).appendTo("#layer_fields_options");
				}
  			});
		}
	};
	$(document).ready(function(){
		if (JCck.Dev.name != "field") {
			var client = $("#"+$("#client .selected").attr('for')).val(); var layer = $("#"+$("#layer .selected").attr('for')).val();
			if (JCck.Dev.block_item) {
				$("#client5_label").addClass("disabled"); $("#client5").prop("disabled", true);
				if (client == "item") {	client = "list"; $("#client4_label").addClass("selected");	$("#client5_label").removeClass("selected"); }
			}
			var name = $("#name").val();
			$("#client label").addClass("off");
			$.cookie("cck_"+JCck.Dev.name+name+"_client", client);
			var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer+JCck.Dev.skip;
			JCck.DevHelper.ajaxLayer(JCck.Dev.name, "edit2", "#layers", data, JCck.Dev.uix);
			$("fieldset#client").on("click", "label", function() {
				if ( $("#title").validationEngine("validate") === false ) {
					if (!($("#"+$(this).attr('for')).prop("disabled")===true)) {
						if (!$(this).hasClass("off")&&!$(this).hasClass("selected")) {
							name = $("#name").val();
							$("#client label").addClass("off");
							$("#client label").removeClass('selected'); $(this).addClass('selected');
							var client = $("#"+$(this).attr('for')).val(); var layer = $("#"+$("#layer .selected").attr('for')).val();
							$.cookie("cck_"+JCck.Dev.name+name+"_client", client);
							var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer;
							JCck.DevHelper.preSubmit(); JCck.DevHelper.ajaxTask(JCck.Dev.name+".apply", "#layers", "&layout=edit2"+"&brb=workshop&redirappend=client\="+client+" layer\="+layer+" format=raw", JCck.Dev.uix);
							$("#client input").prop("checked", false); $("#"+$(this).attr('for')).prop("checked", true);
						}
					}
				}
			});
			if (JCck.Dev.insidebox) { $("#title").after(JCck.Dev.insidebox); }
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
					JCck.DevHelper.switchP(v-1);
				} else {
					JCck.DevHelper.switchPos();
				}
			});
			$("div#layers").on("click", "span.c_live", function() {
				var field = $(this).attr("name");
				if (JCck.Dev.name=="type") {
					var cur = "none";
				} else {
					var cur = $("#"+field+"_live").val();
				}
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title="+JCck.Dev.name+"&name="+field+"&type="+field+"_live_value&id="+cur;
				$.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
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
					$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
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
					$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
				}
			});
			$("div#layers").on("contextmenu", "span.c_val", function() {
				var field = $(this).attr("name");
				var validation = $("#"+field+"_validation").val() ? " + 1" : "";
				if ($("#"+field+"_required").val()) {
					$(this).html(Joomla.JText._('COM_CCK_OPTIONAL')+validation);
					$("#"+field+"_required").val("");
				} else {
					$(this).html(Joomla.JText._('COM_CCK_REQUIRED')+validation);
					$("#"+field+"_required").val("required");
				}
				return false;
			});
			$("div#layers").on("click", "span.c_val", function() {
				var field = $(this).attr("name");
				var validation = $("#"+field+"_validation").val();
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/validation.php&type="+JCck.Dev.name+"&id="+field+"&name="+validation+"&validation=1";
				$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			});
			$("div#layers").on("click", "span.c_typo", function() {
				var field = $(this).attr("name");
				var typo = $("#"+field+"_typo").val();
				if (typo) {
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_typo/"+typo+"/tmpl/edit.php&id="+field+"&name="+typo+"&validation=1";
					$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
				}
			});
			$("div#layers").on("click", "span.c_link", function() {
				var field = $(this).attr("name");
				var link = $("#"+field+"_link").val();
				if (link) {
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_link/"+link+"/tmpl/edit.php&id="+field+"&name="+link+"&validation=1";
					$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
				}
			});
			$("div#layers").on("click", "span.c_res", function() {
				var field = $(this).attr("name");
				var restriction = $("#"+field+"_restriction").val();
				if (restriction) {
					var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_restriction/"+restriction+"/tmpl/edit.php&id="+field+"&name="+restriction+"&validation=1";
					$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
				}
			});
			$("div#layers").on("click", "span.c_mat", function() {
				var id = $("#jform_id").val();
				var field = $(this).attr("name");
				var match_mode = $("#"+field+"_match_mode").val();
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/match.php&id="+id+"&name="+field+"&validation=1";
				$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			});
			$("div#layers").on("click", "span.c_mat2", function() {
				var id = $("#jform_id").val();
				var field = $(this).attr("name");
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/ordering.php&id="+id+"&name="+field+"&validation=1";
				$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			});
			$("div#layers").on("click", "span.c_comp", function() {
				var id = $("#jform_id").val();
				var field = $(this).attr("name");
				var computation = $("#ffp_"+field+"_computation").val();
				computation = computation.replace(/#/gi, "");
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/computation.php&id="+id+"&type=type&name="+field+"&title="+computation;
				$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			});
			$("div#layers").on("change", ".se2sp", function() {
				var id = $(this).attr("id");
				var to = $(this).attr("data-to");
				var v = $(this).val();
				var txt = $("#"+id+" option:selected").text();
				if ($(this).attr("data-type") == "variation") {
					txt = txt.replace(' (*)', '<span class="icon-key"></span>');
				}
				$("#"+to).val(v);
				$(this).parent().find("[data-id=\""+to+"\"]").html(txt);
				JCck.DevHelper.update($(this), $(this).attr("data-type"), 'to');
				$(this).trigger("mouseleave");
			});
			$("div#layers").on("mouseleave", ".se2sp", function(e) {
				if (e.fromElement === undefined && e.relatedTarget === null) { return; } /* Firefox */
				var to = $(this).attr("data-to");
				$(this).parent().find("[data-id=\""+to+"\"]").removeClass("hide");
				$(this).remove();
			});
			$("div#layers").on("click", ".sp2se", function() {
				var id = $(this).attr("data-id");
				var target = $(this).attr("data-to");
				var tab = target.split("-");
				var to = "_wk_"+target;
				var v = $("#"+id).val();
				$(this).addClass("hide");
				var $el = $("#"+to).clone();
				var html = $el.html();
				if (tab[0]=='variation') {
					$el.html(html.replace(new RegExp('&lt;span class="icon-key"&gt;&lt;/span&gt;', 'g'), ' (*)'));
				}
				$el.attr("id",id+"_cur").val(v).addClass("se2sp").attr("data-to",id).prependTo($(this).parent()).removeClass("hide").triggersDropdown();
			});
			$("div#layers").on("contextmenu", ".sp2se", function() {
				var id = $(this).attr("data-id");
				var to = "_wk_"+$(this).attr("data-to");
				var v = $("#"+to).val();
				$("#"+to).val($("#"+id).val());
				var $cur = $("#"+to+" option:selected");
				var $next = $cur.next();
				if ( !$next.length ) {
					$next = $cur.parent();
					if ($next.is("optgroup")){
						$next = $next.next();
					} else {
						$next = $cur.next();
					}
				}
				if ($next.is("optgroup")) {
					$next = $next.children().first();
				}
				if ( !$next.length ) {
					$next = $("#"+to+" option:first");
				}
				$("#"+id).val($next.val());
				$(this).html($next.text());
				JCck.DevHelper.update($(this), $(this).attr("data-to"), 'id');
				return false;
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
				var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/helpers/scripts/conditional.php&id="+id+"&type="+JCck.Dev.name+"&name="+field+"&title="+conditional;
				$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
			});
			$("div#layers").on("click", "a#linkage", function() {
				$("#linkage input").prop("checked", false);
				if ( $("#linkage0").prop("checked") == true ) {
					$("#linkage span").removeClass("unlinked").addClass("linked");
					$("#linkage1").prop("checked", true);
				} else {
					$("#linkage span").removeClass("linked").addClass("unlinked");
					$("#linkage0").prop("checked", true);
				}
			});
			$("#toggle_more").css({"top":($("#toggle_more").parent().height() + 12)});
			$("#toggle_more").on("click", function() {
				if ($("#toggle_more").hasClass("open")){ $("#toggle_more").removeClass("open").addClass("closed"); } else { $("#toggle_more").removeClass("closed").addClass("open"); }
				$("#more").slideToggle(88);
			});
			$("div#layers").on("click", "a#initclient", function() {
				if ($("#sortable1").children(".field").length > 0) {
					if (!confirm(Joomla.JText._('COM_CCK_GET_FIELDS_FROM_VIEW_CONFIRM'))) {
						return false;
					}
				}
				var client = $("#"+$("#client .selected").attr('for')).val(); var layer = "fields";
				var cur = $("#myid").val(); var data = "id="+cur+"&client="+client+"&layer="+layer;
				$("#fromclient").val("1"); JCck.DevHelper.preSubmit(); JCck.DevHelper.ajaxTask("type.apply", "#layers", "&layout=edit2"+"&brb=workshop&redirappend=client\="+client+" layer\="+layer+" format=raw", JCck.Dev.uix);
			});
			$("div#layers").on("contextmenu", "a.icon-add", function() {
				var selected = $('ul#sortable1 li.ui-selected');
				var n = selected.length;
				if (n > 0) {
					var ids = [];
					$.each( selected, function(){
						ids.push($(this).attr("id"));
					});
					ids = ids.join();
					var group_title = window.prompt(JCck.Dev.prompt_group,"");
					if (group_title!=null && group_title!=""){
						JCck.DevHelper.ajaxAddType(group_title, ids);
					}
				}
				return false;
			});
			$(document).keypress(function(e) {
				if (!$(':input:focus').length) {
					if (e.which > 48 && e.which < (49 + JCck.Dev.count)) {
						var n = e.which - 49;

						if ($(".seblod-toolbar .pb"+(n+1)).length) {
							e.preventDefault();
							$(".seblod-toolbar .panel").removeClass("selected"); $(".seblod-toolbar .pb"+(n+1)).addClass("selected");
							JCck.DevHelper.switchP(n);
						}
					} else if (e.which == 110) {
						e.preventDefault();
						$(".icon-add").click();
					} else if (e.which == 113) {
						e.preventDefault();
						$("#toolbar-cancel > button").click();
					}
				}
			});
		}
	});
})(jQuery);