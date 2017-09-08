/**
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
**/

(function ($){
	JCck.GroupX = {
		add: function(name,max_element,new_elem,rid) {
			var ind_elem, length, position, Child,
				root = $('#'+rid+'_sortable_'+name),
				tmp = ( root.children().length ),
				options = {color:"#d5eeff"};

			$("body").on('click', '.cck_button_add_'+name, function() {

				if ($(this).hasClass('external')) {
					elem = root.children().last();
				} else {
					elem = $(this).closest('[id^="'+rid+'_forms_'+name+'"]');
				}
				length = ( elem.parent().children().length );

				if (length < max_element) {
					position	=	position_in_gx( elem );
					ind_elem	=	ind_group(elem) + 1;

					switch ( position ) {
						case 'first':
							Child	=	root.children(":first");
							set_group_as_no_last(Child);							
							elem.after( update_empty_group(new_elem,ind_elem,name) );
							$('#'+rid+'_button_'+name+'_'+ind_elem).show( 'highlight', options, 1000 );						
							if ( length	==	1 ) {
								set_group_as_no_last(elem);
								set_group_as_last(elem.next());
							} else {
								update_group_position ( elem.next().next(), 'add', name, rid );
							}
							break;
						case 'last':	
							Child	=	root.children(":last");									
							set_group_as_no_last(Child);
							elem.after( update_empty_group(new_elem,ind_elem,name) );
							set_group_as_last(elem.next());
							$('#'+rid+'_button_'+name+'_'+tmp).show( 'highlight', options, 1000 );	
							break;
						case 'middle':	
							elem.after(update_empty_group(new_elem,ind_elem,name));
							$('#'+rid+'_button_'+name+'_'+ind_elem).show( 'highlight', options, 1000 );
							update_group_position ( elem.next().next(), 'add', name, rid );
							break;
					}
				}
			});
		},
		remove: function(name,min_element,rid) {
			var elem, position, Child;
			$("body").on('click', '.cck_button_del_'+name, function() {
				elem = $(this).closest('[id^="'+rid+'_forms_'+name+'"]');
				var n	=	elem.parent().children().length;
				if (n > min_element) {
					position	=	position_in_gx( elem );
					ind_elem	=	ind_group($(this).parent().parent());
					if ( position != 'last' ) {
						update_group_position ( elem.next(), 'del', name, rid );
					}
					elem.toggle();
					elem.remove();
					switch ( position ) {
						case 'first':
							Child	=	$("#"+rid+"_sortable_"+name).children(":first");
							set_group_as_first(Child);
							break;
						case 'last':
							Child	=	$("#"+rid+"_sortable_"+name).children(":last");
							set_group_as_last(Child);
							break;
						case 'middle':
							break;
					}
				}
				if ($.isFunction(JCck.Core.recalc)) { // could be improved with a 2nd check & callback
					JCck.Core.recalc();
				}
			});
		}
	}
	/*
	function callback(elem,time) {	// callback function to bring a hidden box back
		setTimeout(function() { elem.parent().remove(); }, time );
	}
	*/
	function ind_group(group_i) {
		var id = group_i.attr("id"), last_index, ind_group;
		if ( id ) {
			last_index = id.lastIndexOf('_');	
			ind_group = parseInt(id.slice(last_index+1));
		} else {
			ind_group	=	'';
		}
		return ind_group;	
	}
	function position_in_gx( group_i ) {
		var group_x = group_i.parent(),
			ind_group_i = ind_group(group_i),
			position, size_group_x;
		if (ind_group_i == 0) {
			position = 'first';
		} else {
			size_group_x = group_x.children().length;
			if (ind_group_i == size_group_x - 1) {
				position = 'last';
			} else {
				position = 'middle';
			}
		}
		return position;
	}
	function set_group_as_first( group_i ) {
		group_i.addClass("cck_form_group_x_first");
		group_i.find('.cck_cgx_button').addClass("cck_cgx_button_first");
		group_i.find('.cck_cgx_form').addClass("cck_cgx_form_first");	
	}
	function set_group_as_last( group_i ) {
		group_i.addClass("cck_form_group_x_last");	
		group_i.find('.cck_cgx_button').addClass("cck_cgx_button_last");
		group_i.find('.cck_cgx_form').addClass("cck_cgx_form_last");	
	}
	function set_group_as_no_last( group_i ) {
		group_i
			.removeClass("cck_form_group_x_last")
			.find(".cck_cgx_button_last,.cck_cgx_form_last")
			.removeClass("cck_cgx_button_last cck_cgx_form_last");
	}
	function update_empty_group(html,ind_group,name_group) {
		var reg = RegExp(name_group+"_0","g");
		html = html.replace(reg,name_group+"_"+ind_group);
		reg = RegExp(name_group+"[\[]"+'0',"g");
		html = html.replace(reg,name_group+"["+ind_group);
		return html;
	}

	function update_group_i( group_i, op_type, name, rid) {
		var num_group = ind_group(group_i)+'',
			length_ind = num_group.length,
			new_ind_group;
		num_group = parseInt(num_group);
		switch ( op_type ) {
			case 'add' : new_ind_group = num_group + 1; break;
			case 'del' : new_ind_group = num_group - 1; break;
		}
		group_i.attr('id',rid+'_forms_'+name+'_'+new_ind_group);

		var buttons = group_i.find(".cck_cgx_button");
		buttons.attr('id',rid+'button_'+name+'_'+new_ind_group);

		var forms = group_i.find(".cck_cgx_form");
		if (forms.length > 1) {
			forms.each(function( index ) {
				$(this).attr('id',rid+'_form_'+name+'_'+new_ind_group+'_'+index);
			});	
		} else {
			forms.attr('id',rid+'_form_'+name+'_'+new_ind_group);
		}

		var newattr;
		forms.find('[name^="'+name+'['+num_group+']"]').each(function( index ) {
			newattr = $(this).attr("name").replace( name+"["+num_group, name+"["+new_ind_group );
			$(this).attr("name", newattr );
		});

		forms.find('[id^="'+name+'_'+num_group+'_"]').each(function( index ) {
			newattr = $(this).attr("id").replace( name+"_"+num_group, name+"_"+new_ind_group );
			$(this).attr("id", newattr );
		});
	}

	function update_group_position( group_start, op_type, name, rid ) {
		var group_end	=	group_start.parent().children(":last"),
			group_i		=	group_start,
			end = false;
		while ( ! end ) {			
			if ( group_i.attr('id') ==  group_end.attr('id') ) {
				update_group_i (group_i, op_type, name, rid);
				end = true;
			} else {
				update_group_i (group_i, op_type, name, rid);
				group_i	=	group_i.next();
			}
		}
	}
})(jQuery);