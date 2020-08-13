/* Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved. */
if ("undefined" === typeof JCck.More) {
    JCck.More = {};
}
!(function (f) {
    function h(t) {
        var e,
            c,
            r = t.attr("id");
        return r ? ((e = r.lastIndexOf("_")), (c = parseInt(r.slice(e + 1)))) : (c = ""), c;
    }
    function u(t) {
        var e = t.parent(),
            c = h(t);
        return 0 == c ? "first" : c == e.children().length - 1 ? "last" : "middle";
    }
    function g(t) {
        t.addClass("cck_form_group_x_last"), t.find(".cck_cgx_button").addClass("cck_cgx_button_last"), t.find(".cck_cgx_form").addClass("cck_cgx_form_last");
    }
    function m(t) {
        t.removeClass("cck_form_group_x_last").find(".cck_cgx_button_last,.cck_cgx_form_last").removeClass("cck_cgx_button_last cck_cgx_form_last");
    }
    function i(t, e, c) {
        var r = RegExp(c + "_0", "g");
        return (t = t.replace(r, c + "_" + e)), (r = RegExp(c + "[[]0", "g")), (t = t.replace(r, c + "[" + e));
    }
    function _(t, e, c, r) {
        var n,
            i = h(t) + "";
        i.length;
        switch (((i = parseInt(i)), e)) {
            case "add":
                n = i + 1;
                break;
            case "del":
                n = i - 1;
        }
        t.attr("id", r + "_forms_" + c + "_" + n), t.find(".cck_cgx_button").attr("id", r + "_button_" + c + "_" + n);
        var a,
            _ = t.find(".cck_cgx_form");
        1 < _.length
            ? _.each(function (t) {
                f(this).attr("id", r + "_form_" + c + "_" + n + "_" + t);
            })
            : _.attr("id", r + "_form_" + c + "_" + n),
            _.find('[name^="' + c + "[" + i + ']"]').each(function (t) {
                (a = f(this)
                    .attr("name")
                    .replace(c + "[" + i, c + "[" + n)),
                    f(this).attr("name", a);
            }),
            _.find('[id^="' + c + "_" + i + '_"]').each(function (t) {
                (a = f(this)
                    .attr("id")
                    .replace(c + "_" + i, c + "_" + n)),
                    f(this).attr("id", a);
            }),
            _.find('[id^="' + r + "_" + c + "_" + i + '_"]').each(function (t) {
                (a = f(this)
                    .attr("id")
                    .replace(r + "_" + c + "_" + i, r + "_" + c + "_" + n)),
                    f(this).attr("id", a);
            }),
            t.find("script").each(function (t) {
                var e = new RegExp(c + "_" + i, "g");
                f(this).text(
                    f(this)
                        .text()
                        .replace(e, c + "_" + n)
                );
            });
    }
    function x(t, e, c, r) {
        for (var n = t.parent().children(":last"), i = t, a = !1; !a; ) i.attr("id") == n.attr("id") ? (_(i, e, c, r), (a = !0)) : (_(i, e, c, r), (i = i.next()));
    }
    JCck.More.GroupX = {
        add: function (t, e, c, r) {
            var n,
                i,
                a,
                _,
                s,
                l = f("#" + r + "_sortable_" + t),
                o = l.children().length,
                d = { color: "#d5eeff" };
            f("body").on("click", ".cck_button_add_" + t, function () {
                if (
                    (f(this).hasClass("external") ? (elem = l.children("div").last()) : (elem = f(this).closest('[id^="' + r + "_forms_" + t + '"]')),
                        0 == l.children().length ? ((s = l), (_ = "inside")) : ((s = elem), (_ = "after")),
                    (i = elem.parent().children().length) < e)
                )
                    switch (((a = u(elem)), (n = h(elem) + 1), a)) {
                        case "first":
                            m(l.children("div:first")), JCck.More.GroupX.insert(s, c, n, t, _), f("#" + r + "_button_" + t + "_" + n).show("highlight", d, 1e3), 1 == i ? (m(elem), g(elem.next())) : x(elem.next().next(), "add", t, r);
                            break;
                        case "last":
                            m(l.children("div:last")), JCck.More.GroupX.insert(s, c, n, t, _), g(elem.next()), f("#" + r + "_button_" + t + "_" + o).show("highlight", d, 1e3);
                            break;
                        case "middle":
                            JCck.More.GroupX.insert(s, c, n, t, _), f("#" + r + "_button_" + t + "_" + n).show("highlight", d, 1e3), x(elem.next().next(), "add", t, r);
                    }
            });
        },
        insert: function (t, e, c, r, n) {
            var rr = jQuery(i(e, c, r));
            if(rr.find('.cck_jform_media').length){
                rr.find('.field-media-wrapper').first().fieldMedia();
            }
            "after" == n ? t.after(rr) : t.append(rr);
        },
        remove: function (c, r, n) {
            var i, a, _;
            f("body").on("click", ".cck_button_del_" + c, function () {
                var t,
                    e = (i = f(this).closest('[id^="' + n + "_forms_" + c + '"]')).parent().children().length;
                if (r < e)
                    switch (((a = u(i)), (ind_elem = h(f(this).parent().parent())), "last" != a && x(i.next(), "del", c, n), i.toggle(), i.remove(), a)) {
                        case "first":
                            (_ = f("#" + n + "_sortable_" + c).children(":first")),
                                (t = _).addClass("cck_form_group_x_first"),
                                t.find(".cck_cgx_button").addClass("cck_cgx_button_first"),
                                t.find(".cck_cgx_form").addClass("cck_cgx_form_first");
                            break;
                        case "last":
                            g((_ = f("#" + n + "_sortable_" + c).children(":last")));
                    }
                f.isFunction(JCck.Core.recalc) && JCck.Core.recalc();
            });
        },
    };
})(jQuery);