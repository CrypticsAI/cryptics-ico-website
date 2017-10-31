(function (d, w, c) {
    w[c] = {
        projectId: parseInt(getsale_vars.project_id)
    };
    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () {
            n.parentNode.insertBefore(s, n);
        };
    s.type = "text/javascript";
    s.async = true;
    s.src = "//rt.getsale.io/loader.js";
    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else {
        f();
    }
})(document, window, "getSaleInit");

function getsale_del() {
    if (window.getSale) {
        getSale.event('del-from-cart');
        console.log('del-from-cart');
    } else {
        (function (w, c) {
            w[c] = w[c] || [];
            w[c].push(function (getSale) {
                getSale.event('del-from-cart');
                console.log('del-from-cart')
            });
        })(window, 'getSaleCallbacks')
    }
}

jQuery(document).ready(function () {
    jQuery("a").each(function () {
        if (this.href.indexOf("remove_item") + 1)
            var my_funct = "getsale_del();";
        jQuery(this).attr('onclick', my_funct);
    });
});
