/*inpage*/
neoInpage = function(argument) {
    this.id = '';
    this.el = null;
    this.ads_id = null;
    this.ads = null;

    this.div_before = null;
    this.div_before_inner = null;
    this.div_open = null;
    this.div_after = null;
    this.div_after_inner = null;
    this.INTERVAL = null;
};
neoInpage.prototype.setAds = function( ads_id ) {
    this.ads_id = ads_id;
    this.ads = document.getElementById(ads_id);
    return this;
};
neoInpage.prototype.setContent = function( id ) {
    this.id = id;
    this.el = document.getElementById(id);
    return this;
};
neoInpage.prototype.inpage = function() {
    if( this.el
        && 'undefined' != typeof this.el
        && this.ads
        && 'undefined' != typeof this.ads  ){
        this.create();
    }
};
neoInpage.prototype.create = function() {
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        var k = 100;
        var l = 10;
        if (typeof(t) == "undefined") {
            t = false;
        } else {
            t = true;
        }
        if ( !(this.div_open = document.getElementById("neo_inpage_open")) ) {
            var o = this.el;
            var w = parseInt(o.offsetHeight);
            var g = 0;
            var q = o.children;
            if (q.length > k) {
                if ( this.ads ) {
                    this.ads.innerHTML = '';
                }
                return
            }
            for (i = 1; i < q.length; i++) {
                g += parseInt(q[i].offsetHeight);
                if (g >= w / 2 && g > 200) {
                    break
                }
            }
            var x = document.createElement("DIV");
            x.id = "neo_inpage_after";
            x.style.cssText = "height:0px;z-index:1;background:#fff;position:relative;margin-left: -20px; margin-right: -7px;display:none;";

            var f = document.createElement("DIV");
            f.id = "neo_inpage_open";
            f.setAttribute("class", "neo-inpage-banner");
            f.style.cssText = "position:relative;width:100%;z-index:0;background:transparent;";
            if (i >= q.length - 1) {} else {};

            var b = document.createElement("DIV");
            b.id = "neo_inpage_before";
            b.style.cssText = "height:0px;z-index:1;background:#fff;position:relative;margin-left: -20px; margin-right: -7px;display:none;";
            if (i >= q.length - 1) {
                o.appendChild(b);
                o.appendChild(f);
                o.appendChild(x)
            } else {
                o.insertBefore(x, q[i + 1]);
                o.insertBefore(f, q[i + 1]);
                o.insertBefore(b, q[i + 1])
            }
            this.div_open = document.getElementById("neo_inpage_open");
            this.div_after = document.getElementById("neo_inpage_after");
            this.div_before = document.getElementById("neo_inpage_before");

            this.div_after.innerHTML = '<div id="neo_inpage_after_inner" style="width:100%;height:0px;position:absolute;top:0px;z-index:2;background:#fff;transform: translateZ(0px);-moz-transform: translateZ(0px);-webkit-transform: translateZ(0px);-o-transform: translateZ(0px);-ms-transform: translateZ(0px);"></div>';
            this.div_before.innerHTML = '<div id="neo_inpage_before_inner" style="width:100%;height:0px;position:absolute;bottom:0px;z-index:2;background:#fff;transform: translateZ(0px);-moz-transform: translateZ(0px);-webkit-transform: translateZ(0px);-o-transform: translateZ(0px);-ms-transform: translateZ(0px);"></div>'
            this.div_before_inner = document.getElementById("neo_inpage_before_inner");
            this.div_after_inner = document.getElementById("neo_inpage_after_inner");
        } else {
            t = true
        }
        var d = this.el;
        var a = 1;
        var p = this.div_open.offsetTop;
        var u = 0;
        var h = false;
        do {
            if ( d.tagName.toLowerCase() == 'body'
                || d.tagName.toLowerCase() == 'html'
                || d.tagName.toLowerCase() == 'head' ) {
                break;
            }
            p += d.offsetTop;
            u = d.offsetHeight;
            q = d.children;
            if (a > l || q.length > k) {
                if ( this.ads ) {
                    this.ads.innerHTML = '';
                }
                return
            }
            if (!t) {
                for (i = 0; i < q.length; i++) {
                    if (    q[i].id == this.ads_id 
                            || q[i].id == this.id 
                            || q[i].id == "neo_inpage_before" 
                            || q[i].id == "neo_inpage_open" 
                            || q[i].id == "neo_inpage_after" 
                            || q[i].id == "neo-pin-conten-" + (a - 1) 
                            || (    typeof(q[i].type) != "undefined" 
                                    && q[i].type.toLowerCase() != q[i].type.toLowerCase().replace("javascript", "") ) 
                            || q[i].style.position == "fixed") {
                        continue
                    }
                    if ( q[i].id == this.id
                        || q[i].tagName.toLowerCase() == 'body'
                        || q[i].tagName.toLowerCase() == 'html'
                        || q[i].tagName.toLowerCase() == 'head' ) {
                        h = true;
                        break
                    }
                    var m = parseInt(q[i].style.zIndex);
                    m = isNaN(m) ? 0 : m;
                    if (m >= 3) {
                        continue
                    }
                    q[i].style.zIndex = m + 3;
                    q[i].style.position = "relative"
                }
            }++a;
            d = d.parentElement;
            if (h) {
                break
            }
        } while (d);
        u = Math.max(u, p);
        var n = u - p - this.div_open.offsetHeight;
        n = Math.max(n, (this.el.offsetHeight-this.div_open.offsetTop-this.div_open.offsetHeight));

        this.div_before_inner.style.height = p + "px";
        this.div_open.style.height = window.innerHeight + "px";
        this.div_after_inner.style.height = n + "px";
        this.ads.style.visibility = "visible";
        this.ads.style.zIndex = 1;

        this.ads.style.width = '100%';
        this.ads.style.position = 'fixed';
        this.ads.style.top = '0px';
        this.ads.style.overflowY = 'hidden';
        this.ads.style.display = 'none';
        this.el.style.overflow = 'hidden';
        //this.smoothInpage(true);
        this.checkTimmer();
    }
};
neoInpage.prototype.bind_ = function(thisObj, fn) {
    return function() {
        fn.apply(thisObj, arguments);
    };
};
neoInpage.prototype.checkTimmer = function() {
    clearInterval(this.INTERVAL);
    clearTimeout(this.INTERVAL);
    this.INTERVAL = setTimeout( this.bind_(this, this.checkObj) , 100);
};
neoInpage.prototype.getOffset = function(d) {
    top_ = 0;
    left_ = 0;
    do {
        top_ += d.offsetTop || 0;
        left_ += d.offsetLeft || 0;
        d = d.parentElement;
    } while (d);
    return {top : top_, left: left_};
};
neoInpage.prototype.checkObj = function() {
    if( this.checkObjInScreen(this.div_open) ){
        this.div_before.style.display = 'block';
        this.div_after.style.display = 'block';
        this.ads.style.display = 'block';
    }else{
        this.div_before.style.display = 'none';
        this.div_after.style.display = 'none';
        this.ads.style.display = 'none';
    }
    var d = this.el;
    var p = this.div_open.offsetTop;
    do {
        p += d.offsetTop || 0;
        u = d.offsetHeight || 0;
        d = d.parentElement;
    } while (d);
    u = Math.max(u, p);
    var n = u - p - this.div_open.offsetHeight;
    n = Math.max(n, (this.el.offsetHeight-this.div_open.offsetTop-this.div_open.offsetHeight));
    this.div_before_inner.style.height = p + "px";
    this.div_open.style.height = window.innerHeight + "px";
    this.div_after_inner.style.height = n + "px";
    this.checkTimmer();
};
neoInpage.prototype.checkObjInScreen = function(e) {
    if (!e) {
        return false
    }
    var a = this.scrollLeft();
    var f = a + window.innerWidth;

    var h = this.scrollTop();
    var g = h + window.innerHeight;

    ost = this.getOffset(e);

    var d = ((a < ost.left && ost.left < f) || (a < (ost.left + e.offsetWidth) && (ost.left + e.offsetWidth) < f) || (ost.left <= a && f <= (ost.left + e.offsetWidth)) || (ost.left >= a && f >= (ost.left + e.offsetWidth)));
    var b = ((h < ost.top && ost.top < g) || (h < (ost.top + e.offsetHeight) && (ost.top + e.offsetHeight) < g) || (ost.top <= h && g <= (ost.top + e.offsetHeight)) || (ost.top >= h && g >= (ost.top + e.offsetHeight)) );

    //var d = ((a < ost.left && ost.left < f) || (a < (ost.left + e.offsetWidth) && (ost.left + e.offsetWidth) < f) || (ost.left <= a && f <= (ost.left + e.offsetWidth)) || (ost.left >= a && f >= (ost.left + e.offsetWidth)));
    //var b = ((h < ost.top && ost.top < g) || (h < (ost.top + e.offsetHeight) && (ost.top + e.offsetHeight) < g) || (ost.top <= h && g <= (ost.top + e.offsetHeight)) || (ost.top >= h && g >= (ost.top + e.offsetHeight)) );

    //var d = ((a < e.offsetLeft && e.offsetLeft < f) || (a < (e.offsetLeft + e.offsetWidth) && (e.offsetLeft + e.offsetWidth) < f) || (e.offsetLeft <= a && f <= (e.offsetLeft + e.offsetWidth)));
    //var b = ((h < e.offsetTop && e.offsetTop < g) || (h < (e.offsetTop + e.offsetHeight) && (e.offsetTop + e.offsetHeight) < g) || (e.offsetTop <= h && g <= (e.offsetTop + e.offsetHeight)));
    return (d && b);
};
neoInpage.prototype.checkObjInScreen_ = function(e) {
    if (!e) {
        return false
    }
    var a = this.scrollLeft();
    var f = a + window.innerWidth;
    var h = this.scrollTop();
    var g = h + window.innerHeight;
    var d = ((a < e.offsetLeft && e.offsetLeft < f) || (a < (e.offsetLeft + e.offsetWidth) && (e.offsetLeft + e.offsetWidth) < f) || (e.offsetLeft <= a && f <= (e.offsetLeft + e.offsetWidth)));
    var b = ((h < e.offsetTop && e.offsetTop < g) || (h < (e.offsetTop + e.offsetHeight) && (e.offsetTop + e.offsetHeight) < g) || (e.offsetTop <= h && g <= (e.offsetTop + e.offsetHeight)));
    return (d && b);
};
neoInpage.prototype.filterResults = function(e, b, a) {
    var d = e ? e : 0;
    if (b && (!d || (d > b))) {
        d = b
    }
    return a && (!d || (d > a)) ? a : d
};

neoInpage.prototype.scrollLeft = function()  {
    return this.filterResults(window.pageXOffset ? window.pageXOffset : 0, document.documentElement ? document.documentElement.scrollLeft : 0, document.body ? document.body.scrollLeft : 0)
}

neoInpage.prototype.scrollTop = function() {
    return this.filterResults(window.pageYOffset ? window.pageYOffset : 0, document.documentElement ? document.documentElement.scrollTop : 0, document.body ? document.body.scrollTop : 0)
}

neoInpage.prototype.clientWidth = function() {
    return this.filterResults(window.innerWidth ? window.innerWidth : 0, document.documentElement ? document.documentElement.clientWidth : 0, document.body ? document.body.clientWidth : 0)
}

neoInpage.prototype.clientHeight = function() {
    return this.filterResults(window.innerHeight ? window.innerHeight : 0, document.documentElement ? document.documentElement.clientHeight : 0, document.body ? document.body.clientHeight : 0)
}
neoInpage.prototype.smoothInpage = function() {
    var h = this.div_open;
    var d = this.ads;
    var m = document.getElementById("div_inpage_banner_inner");
    var e = this.div_before;
    if (!h || !d || !m || !e) {
        return
    }
    var a = 1;
    if (a == 1) {
        h.style.height = window.innerHeight + "px"
    } else {
        h.style.height = ((m.offsetHeight > 0 && m.offsetHeight < window.innerHeight) ? m.offsetHeight : window.innerHeight) + "px"
    }
    if (typeof(k) != "undefined") {
        var l = k
    } else {
        var l = this.checkObjInScreen(h)
    }
    if (!l) {
        if (typeof(v_interval_banner_inpage) != "undefined" && v_interval_banner_inpage > 0) {
            d.style.height = "0px";
            d.style.top = "250px";
            clearInterval(v_interval_banner_inpage);
            v_interval_banner_inpage = 0
        }
        return
    }
    if (typeof(v_interval_banner_inpage) == "undefined" || v_interval_banner_inpage <= 0) {
        if (typeof(k) == "undefined") {
            v_interval_banner_inpage = setInterval(this.smoothInpage(), 1)
        }
        if (a == 1) {
            var b = 0;
            var g = parseInt((window.innerHeight - m.offsetHeight) / 2);
            var f = window.innerHeight
        } else {
            var b = (window.innerHeight > h.offsetHeight) ? Math.floor((window.innerHeight - h.offsetHeight) / 2) : 0;
            var g = 5;
            var f = (window.innerHeight - (g * 2) > h.offsetHeight) ? (h.offsetHeight + (g * 2)) : window.innerHeight
        }
        d.style.height = f + "px";
        d.style.top = "250px";
        m.style.marginTop = "0px";
        m.style.marginBottom = "0px";
        if (window.innerWidth > m.offsetWidth) {
            m.style.marginLeft = parseInt((window.innerWidth - m.offsetWidth) / 2) + "px"
        }
    }
}