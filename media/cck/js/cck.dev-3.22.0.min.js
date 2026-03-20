/* Copyright (C) 2009 - 2019 SEBLOD. All Rights Reserved. */
if("undefined"===typeof JCck)var JCck={};
(function($) {
	JCck.submitForm = function(task, form) {
	    if (typeof(form) === 'undefined') {
	        form = document.getElementById('adminForm');
	    }

	    if (typeof(task) !== 'undefined' && task !== "") {
	        form.task.value = task;
	    }
	    if (typeof form.onsubmit == 'function') {
	        form.onsubmit();
	    }
	    if (typeof form.fireEvent == "function") {
	        form.fireEvent('onsubmit');
	    }
	    /*
	    if (typeof jQuery == "function") {
	        jQuery(form).submit();
	    }
	    */
	    form.submit();
	};
	JCck.transliterate = function(str,characters) {
		var text = new String(str);
		if (text){
			var charMap = {};
			var chars = [];
			var i = 0;
			for (var k in characters) {
				var fromChars = characters[k].split(',');
				var len = fromChars.length;
				for(var j = 0; j < len; j++) {
					var c = fromChars[j];
					chars[i] = c;
					charMap[c] = k;
					i++;
				};
			}
			if (chars.length) {
				var re = new RegExp(chars.join("|"), "g");
				text = text.replace(re, function(c) {
					if (charMap[c]){
						return charMap[c];
					} else {
						return c;
					};
				});
			}
		}
		return text;
	};
	$.fn.isDisabledWhen = function(id, values) {
		var trigger = $( '#'+id );
		var tab	= values.split( ',' );
		var $el = $(this);
		
		if (trigger != null) {
			// Load
			if ($.inArray( trigger.val(), tab ) >= 0) {
				$el.prop("disabled", true);
			} else {
				$el.prop("disabled", false);
			}
			// Change
			trigger.change( function() {
				var selected	=	trigger.val();
				if ($.inArray(selected, tab) >= 0) {
					$el.prop("disabled", true);
				} else {
					$el.prop("disabled", false);
				}
			});
		}
	};
	$.fn.isHiddenWhen = function(id, values, parent_tag, property) {
		$(this).isVisibleWhen(id, values, parent_tag, property, true);
	};
	$.fn.isVisibleWhen = function(id, values, parent_tag, property,invert) {
		var trigger = $( '#'+id );
		var tab	= values.split( ',' );
		var p = true;
		var legacy = false;
		var $el = $(this);
		var invert = invert || false;

		if (parent_tag==true || parent_tag=='true') {
			var p_tag = '';
			if ($el.parent().prop("tagName") == "LI") {
				legacy = true;
			}
		} else if(parent_tag==false || parent_tag=='false') {
			var p_tag = '';
			p = false;
		} else {
			var p_tag = parent_tag || '';
			if (p_tag) {
				legacy = true;
			}
		}
		if (property=='visibility') {
			var property_on = invert ? 'hidden' : 'visible';
			var property_off = invert ? 'visible' : 'hidden';
		} else {
			property = 'display';
			var property_on = invert ? 'none' : '';
			var property_off = invert ? '' : 'none';
		}

		var eid = $(this).attr("id");
		if (eid !== undefined) {
			var $el2 = $("#"+eid+"_chzn");
		} else {
			var $el2 = "";
		}

		if (trigger != null) {
			// Load
			if ($.inArray( trigger.myVal(), tab ) >= 0) {
				if ($el2 && $el2.length) {
					$el2.css(property, property_on);
				} else {
					(p == true) ? (!legacy ? $el.parent().parent(p_tag).css(property, property_on) : $el.parent(p_tag).css(property, property_on))
								: $el.css(property, property_on);
				}
			} else {
				if ($el2 && $el2.length) {
					$el2.css( property, property_off);
				} else {
					(p == true) ? (!legacy ? $el.parent().parent(p_tag).css(property, property_off) : $el.parent(p_tag).css(property, property_off))
								: $el.css(property, property_off);	
				}
			}
			// Change
			trigger.change( function() {
				var selected	=	trigger.myVal();
				if ($.inArray(selected, tab) >= 0) {
					if ($el2 && $el2.length) {
						$el2.css(property, property_on);
					} else {
						(p == true) ? (!legacy ? $el.parent().parent(p_tag).css(property, property_on) : $el.parent(p_tag).css(property, property_on))
									: $el.css(property, property_on);
					}
				} else {			
					if ($el2 && $el2.length) {
						$el2.css( property, property_off);
					} else {
						(p == true) ? (!legacy ? $el.parent().parent(p_tag).css(property, property_off) : $el.parent(p_tag).css(property, property_off))
									: $el.css(property, property_off);	
					}
				}
			});
		}
	};
	$.fn.myVal = function(v) {
		if (arguments.length == 1) {
			var a = 1;
		} else {
			var a = 0;
			var v = "";
		}
		if (!this[0]){
			return "";
		}
		var elem = this[0];
		if ( elem.tagName == 'FIELDSET' || elem.tagName == 'DIV' ) {
			var eid = '#'+this.attr("id");
			var val = "";
			if (v) {
				$(eid+' input').val(v.split(','));
			} else {
				if ($(eid+' input:checked').length > 1) {
					$(eid+' input:checked').each(function() {
						val +=  ","+$(this).val();
					});
					return val.substr(1);
				} else {
					if ($(eid+' input:checked').length == 1) {
						return $(eid+' input:checked').val();
					}
				}
			}
		} else if ( elem.tagName == 'SELECT' && this.prop("multiple") ) {
			return (a) ? (v ? this.val(v.split(',')) : this.val(v)) : this.val();
		} else {
			return (a) ? this.val(v) : this.val();
		}
	};
	$.fn.serializeObject = function() {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function() {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || "");
			} else {
				o[this.name] = this.value || "";
			}
		});
		return o;
	};
	$.fn.triggersDropdown = function() {
		return this.each(function(idx,domEl) {
			if (document.createEvent) {
				var event = document.createEvent("MouseEvents");
				event.initMouseEvent("mousedown", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
				domEl.dispatchEvent(event);
			} else if (element.fireEvent) {
				domEl.fireEvent("onmousedown");
			}
		});
	};
})(jQuery);