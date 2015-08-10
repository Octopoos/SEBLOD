/**
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
**/

(function ($){
	JCck.GroupX = {
		add: function(name,max_element,new_elem) {
			var id;
			var ind_elem;
			var root = $('#cck1_sortable_'+name);
			var tmp = ( root.children().length );
			var length;
			var name_length = name.length;
			var next_elem;
			var options = {color:"#d5eeff"};
			var position;
			var Child;
			var GrandChildren;
			$("body").on('click', '.cck_button_add_'+name, function() {
				elem = $(this).parent().parent();
				length = ( elem.parent().children().length );
				id = $(this).parent().attr("id");
				if (length < max_element) {
					position	=	position_in_gx( elem );
					ind_elem	=	ind_group(elem) + 1;
					switch ( position ) {
						case 'first':
							Child	=	root.children(":first");
							set_group_as_no_last(Child);
							elem.after( update_empty_group(new_elem,ind_elem,name) );
							$('#cck1r_button_'+name+'_'+ind_elem).show( 'highlight', options, 1000 );
							if ( length	==	1 ) {
								set_group_as_no_last(elem);
								set_group_as_last(elem.next());
							} else {
								update_group_position ( elem.next().next(), 'add', name );
							}
							break;
						case 'last':	
							Child	=	root.children(":last");									
							set_group_as_no_last(Child);
							elem.after( update_empty_group(new_elem,ind_elem,name) );
							set_group_as_last(elem.next());
							$('#cck1r_button_'+name+'_'+tmp).show( 'highlight', options, 1000 );	
							break;
						case 'middle':	
							elem.after(update_empty_group(new_elem,ind_elem,name));
							$('#cck1r_button_'+name+'_'+ind_elem).show( 'highlight', options, 1000 );
							update_group_position ( elem.next().next(), 'add', name );
							break;
					}
				}
			});
		},
		remove: function(name,min_element) {
			var elem;
			var next_elem;
			var options = {color:"#d5eeff"};
			var time	= 1000;
			var name_length = name.length;
			var position;
			var Child;
			var GrandChildren;
			var ind;
			$("body").on('click', '.cck_button_del_'+name, function() {
				elem	=	$(this).parent().parent();
				var n	=	elem.parent().children().length;
				if (n > min_element) {
					position	=	position_in_gx( elem );
					ind_elem	=	ind_group($(this).parent().parent());
					if ( position != 'last' ) {
						update_group_position ( elem.next(), 'del', name );
					}
					elem.toggle();
					elem.remove();
					switch ( position ) {
						case 'first':
							Child	=	$("#cck1_sortable_"+name).children(":first");
							set_group_as_first(Child);
							break;
						case 'last':
							Child	=	$("#cck1_sortable_"+name).children(":last");
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
	function callback(elem,time) {	// callback function to bring a hidden box back
		setTimeout(function() { elem.parent().remove(); }, time );
	}
	function ind_group(group_i) {
		var id = group_i.attr("id");
		var last_index;
		var ind_group;
		if ( id ) {
			last_index = id.lastIndexOf('_');	
			ind_group = parseInt(id.slice(last_index+1));
		} else {
			ind_group	=	'';
		}
		return ind_group;	
	}
	function position_in_gx( group_i ) {
		var group_x = group_i.parent();
		var ind_group_i = ind_group(group_i);
		var position;
		var size_group_x;
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
		var group_i_Children	=	group_i.children();
		$(group_i_Children[0]).addClass("cck_cgx_button_first");
		$(group_i_Children[1]).addClass("cck_cgx_form_first");	
	}
	function set_group_as_last( group_i ) {
		group_i.addClass("cck_form_group_x_last");
		var group_i_Children	=	group_i.children();
		$(group_i_Children[0]).addClass("cck_cgx_button_last");
		$(group_i_Children[1]).addClass("cck_cgx_form_last");	
	}
	function set_group_as_no_first( group_i ) {
		group_i.removeClass("cck_form_group_x_first");
		var group_i_Children	=	group_i.children();
		$(group_i_Children[0]).removeClass("cck_cgx_button_first");
		$(group_i_Children[1]).removeClass("cck_cgx_form_first");	
	}
	function set_group_as_no_last( group_i ) {
		group_i.removeClass("cck_form_group_x_last");
		var group_i_Children	=	group_i.children();
		$(group_i_Children[0]).removeClass("cck_cgx_button_last");
		$(group_i_Children[1]).removeClass("cck_cgx_form_last");	
	}
	function update_empty_group(html,ind_group,name_group) {
		var reg = RegExp(name_group+"_0","g");
		html = html.replace(reg,name_group+"_"+ind_group);
		reg = RegExp(name_group+"[\[]"+'0',"g");
		html = html.replace(reg,name_group+"["+ind_group);
		return html;
	}
	function update_field( field, name, new_id, id ) {
		var length, length_b, label, new_iden;
		var iden, form, form_save;
		length = field.children().length;
		if( length == 2 ) {
			label = field.children(":first");
			iden = label.attr("id");
			new_iden = iden.replace( name+"_"+id, name+"_"+new_id );
			label.attr("id", new_iden );
			form = label.next();
		} else {
			form = field.children(":first");		
		}
		iden = form.attr("id");
		if ( iden ) {
			new_iden = iden.replace( name+"_"+id, name+"_"+new_id );
			form.attr("id", new_iden );
		}
		update_form( form, name, new_id, id );
	}
	function update_form( form, name, new_id, id ) {
		var form_child, length, length_b, label, iden, new_iden, new_name_field, form_child, i, j, k;
		var name_field;
		length = form.children().length;
		form = form.children(":first");
		for( i=0; i < length; i++ ) {
			iden = form.attr("id");
			if ( iden ) {
				new_iden = iden.replace( name+"_"+id, name+"_"+new_id );
				form.attr("id", new_iden );
				name_field = form.attr("name");
				if( name_field ) {
					new_name_field = name_field.replace( name+"["+id, name+"["+new_id );
					form.attr("name", new_name_field );
				}
			} else {
				length_b = form.children().length;
				form_child = form.children(":first");
				for ( j=0; j < length_b; j++) {
					iden = form_child.attr("id");
					if (iden) {
						new_iden = iden.replace( name+"_"+id, name+"_"+new_id );
						form_child.attr("id", new_iden );
						name_field = form_child.attr("name");
						if( name_field ) {
							new_name_field = name_field.replace( name+"["+id, name+"["+new_id );
							form_child.attr("name", new_name_field );
						}
					} else {
						length_b2 = form_child.children().length;
						form_child2 = form_child.children(":first");
						for ( k=0; k < length_b2; k++) {
							iden = form_child2.attr("id");
							if (iden) {
								new_iden = iden.replace( name+"_"+id, name+"_"+new_id );
								form_child2.attr("id", new_iden );
								name_field = form_child2.attr("name");
								if( name_field ) {
									new_name_field = name_field.replace( name+"["+id, name+"["+new_id );
									form_child2.attr("name", new_name_field );
								}
							}
							form_child2 = form_child2.next();
						}
					}
	
					form_child = form_child.next();
				}
			}
			form = form.next();
		}
	}
	function update_group_elem_asc(elem,ind_elem,tmp) {
		var i;
		var content;
		var reg;
		var new_ind;
		var id;
		for (i= ind_elem; i < tmp; i++) {
			new_ind = i + 1;
			content = elem.html();
			id = elem.attr('id');
			reg = RegExp(name+"_"+i,"g");
			content = content.replace(reg,name+"_"+new_ind);
			id = id.replace(reg,name+"_"+new_ind);
			elem.attr('id',id);
			reg = RegExp(name+"[\[]"+i,"g");
			content = content.replace(reg,name+"["+new_ind);
			elem.html(content);
			elem = elem.next();
		}
	}
	function update_group_elem_des(elem,ind_elem,tmp) {
		var i;
		var content;
		var reg;
		var new_ind;
		var id;
		for (i= ind_elem + 1, new_ind = ind_elem; i < tmp; i++, new_ind--) {
			content = elem.html();
			new_id = i - 1 ;
			id = elem.attr('id');
			reg = RegExp(name+"_"+i,"g");
			content = content.replace(reg,name+"_"+new_ind);
			id = id.replace(reg,name+"_"+new_ind);
			elem.attr('id',id);
			reg = RegExp(name+"[\[]"+i,"g");
			content = content.replace(reg,name+"["+new_ind);
			elem.html(content);
			elem = elem.next();
		}
	}
	function update_group_i( group_i, op_type, name) {
		var num_group = ind_group(group_i);
		var new_ind_group;
		num_group = num_group+'';
		var length_ind = num_group.length;
		num_group = parseInt(num_group);
		switch ( op_type ) {
			case 'add' : new_ind_group = num_group + 1; break;
			case 'del' : new_ind_group = num_group - 1; break;
		}
		group_i.attr('id','cck1r_forms_'+name+'_'+new_ind_group);
		var buttons = group_i.children(":first");
		var form = group_i.children(":last");
		buttons.attr('id','cck1r_button_'+name+'_'+new_ind_group);
		form.attr('id','cck1r_form_'+name+'_'+new_ind_group);
		var fields	=	form.children(":first");
		var fields_nbr	=	form.children().length;
		var i;
		var field_value;
		var input;
		var identifiant;
		var reg;
		var length_name = name.length;
		var len_begin = length_ind + length_name + 2;
		var length_id;
		var field_name;
		field_value = form;//fields.parent().parent().children(":last");
		update_inputs ( field_value, name, new_ind_group, num_group );
	}
	function update_group_position( group_start, op_type, name ) {
		var group_end	=	group_start.parent().children(":last");
		var group_i		=	group_start;
		var end = false;
		while ( ! end ) {
			if ( group_i.attr('id') ==  group_end.attr('id') ) {
				update_group_i ( group_i, op_type, name);
				end = true;
			} else {
				update_group_i ( group_i, op_type, name);
				group_i	=	group_i.next();
			}
		}
	}
	function update_inputs( inputs, name, new_id, id ) {
		var input, nbr_inputs, identifiant, name_input, length_id, field_name;
		input = inputs.children(":first");
		nbr_inputs = inputs.children().length;
		var nime, idi;
		var input_children_len;
		var toto;
		for ( i = 0; i < nbr_inputs; i++ ) {
			identifiant = input.attr("id");
			if ( identifiant ) {
				toto = identifiant.replace( name+"_"+id, name+"_"+new_id );
				input.attr('id',toto);
			}
			update_field ( input, name, new_id, id )
			input	= input.next();
		}
	}
})(jQuery);