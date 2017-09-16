console.log('v1.0');
var $ = jQuery.noConflict();
language = {
    translate : function( key ){
        if( typeof langs != 'undefined'
            && Object.keys(langs).length > 0 
            && typeof langs[key] != 'undefined'  ){
            return langs[key];
        }
        return key;
    }
};
function writeCookie(name,value,days) {var date, expires;if (days) {date = new Date();	date.setTime(date.getTime()+(days*24*60*60*1000));	expires = "; expires=" + date.toGMTString();}else{	expires = "";} document.cookie = name + "=" + value + expires + "; path=/";}
function readCookie(name) {var i, c, ca, nameEQ = name + "=";ca = document.cookie.split(';');for(i=0;i < ca.length;i++) {c = ca[i];	while (c.charAt(0)==' ') {	c = c.substring(1,c.length);}if (c.indexOf(nameEQ) == 0) {return c.substring(nameEQ.length,c.length);}}return '';}

getPriceProduct = function(){
    try{
        var quantity =1;
        if($('input[name="quantity"]').length>0){
            quantity = parseInt($('input[name="quantity"]').val());
        }
        var price_tmp = parseFloat(product_data.price_sale);
        var price_root = parseFloat(product_data.price);
        total_price_extention = 0;
        if(product_data.total_price_extention != null){
            total_price_extention = product_data.total_price_extention;
        }
        if($('[name="product_type"]').length>0 && $('[name="product_type"]').val() > 0 ){
            products_type_id = $('[name="product_type"]').eq(0).val();
            price_tmp = 0;
            price_root = 0;
            for (var i = 0; i < products_type.length; i++) {
                if(products_type_id == products_type[i].products_type_id){
                    price_tmp = quantity*(parseFloat(products_type[i].price_sale) + parseFloat(total_price_extention));
                    price_root = quantity*(parseFloat(products_type[i].price) + parseFloat(total_price_extention));
                    break;
                }
            };
        }else{
            if(typeof product_data.products_type_id != 'undefined' && parseInt(product_data.products_type_id) > 0){
                price_tmp = quantity*(parseFloat(product_data.t_price_sale) + parseFloat(total_price_extention));
                price_root = quantity*(parseFloat(product_data.price) + parseFloat(total_price_extention));
            }else{
                
                price_tmp = quantity*(parseFloat(product_data.price_sale) + parseFloat(total_price_extention));
                price_root = quantity*(parseFloat(product_data.price) + parseFloat(total_price_extention));
            }
        }
        /*
        vat = product_data.vat;
        if(!(!isNaN(parseFloat(product_data.vat)) && isFinite(product_data.vat))){
            vat =0;
        }
        price_tmp = parseFloat(price_tmp) + (parseFloat(price_tmp) * parseFloat(vat) / 100);
        */
        for (var i = 0; i < extensions_data.length; i++) {
            extension_tmp = extensions_data[i];
            if($('input[name="extention[]"][value="'+extension_tmp.id+'"]').is(':checked') && extension_tmp.is_always == 0){
                price_tmp += parseFloat(extension_tmp.price_tmp);
                        price_root += parseFloat(extension_tmp.price);
            }
        };
        $('.neo-value-price-update').html(coz.fomatCurrency(price_tmp));
        $('#price-preview del').html(coz.fomatCurrency(price_root));
    }catch(e){}
}

String.prototype.capitalize = function() {
    return this.replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
};

if( typeof coz == 'undefined' ){
    coz={};
};
if( typeof coz.isMobile == 'undefined'
    && typeof isMobile != 'undefined' ){
    coz.isMobile=isMobile;
};
if( typeof coz.baseUrl == 'undefined'
    && typeof baseUrl != 'undefined' ){
    coz.baseUrl=baseUrl;
};
if( typeof coz.currency == 'undefined'
    && typeof currency != 'undefined' ){
    coz.currency=currency;
};
if( typeof coz.categories == 'undefined'
    && typeof categories_ != 'undefined' ){
    coz.categories=categories_;
};
if( typeof coz.langs == 'undefined'
    && typeof langs != 'undefined' ){
    coz.langs=langs;
};
if( typeof coz.member == 'undefined'
    && typeof MEMBER != 'undefined' ){
    coz.member=MEMBER;
};
coz.INTERVAL = null;
coz.DELAY = 300;
coz.winloaded = false;
coz.loaded = false;
coz.is_sync = 0;/*0 la hoi, 1 la co, 2 la ko*/
coz.is_sync_facebook = 0;/*0 la hoi, 1 la co, 2 la ko*/
coz.is_sync_google = 0;/*0 la hoi, 1 la co, 2 la ko*/
coz.confirm_location = 0;/*0 la hoi, 1 la ko*/
NProgress.start();
windowOnload = function(){};
if( typeof window.onload == 'function'){
    windowOnload = window.onload;
}
window.onload = function(){
    windowOnload();
    coz.winloaded = true;
    NProgress.done(true);
}
coz.aliasString = function(s) {
    if (typeof s == "undefined") {
        return;
    }
    var i=0,uni1,arr1;
    var newclean=s;
    uni1 = 'à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ|A';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'a');

    uni1 = 'è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ|È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ|E';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'e');

    uni1 = 'ì|í|ị|ỉ|ĩ|Ì|Í|Ị|Ỉ|Ĩ|I';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'i');

    uni1 = 'ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ|Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ|O';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'o');

    uni1 = 'ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ|Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ|U';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'u');

    uni1 = 'ỳ|ý|ỵ|ỷ|ỹ|Ỳ|Ý|Ỵ|Ỷ|Ỹ|Y';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'y');

    uni1 = 'd|Đ|D';
    arr1 = uni1.split('|');
    for (i=0; i<uni1.length; i++) newclean = newclean.replace(uni1[i],'d');

    newclean = newclean.toLowerCase()
    ret = newclean.replace(/[\&]/g, '-and-').replace(/[^a-zA-Z0-9._-]/g, '_').replace(/[-]+/g, '_').replace(/-$/, '');

    return ret.toUpperCase();
};
coz.warning = function(){
    try{
        var styles01 = [
            'font-family: \'helvetica neue\',helvetica,arial,\'lucida grande\',sans-serif',
            'font-size: 40px',
            'color: red',
            'font-weight: bold',
            'text-shadow: -1px 0px 0 #000, 1px 0px 0 #000, 0px 1px 0 #000, 1px 0px 0 #000',
            'display: block',
            'line-height: 100px',
            'text-align: center'
        ].join(';');

        console.log('%cDừng lại!', styles01);

        var styles02 = [
            'font-family: \'Roboto\', Helvetica, Arial,  sans-serif',
            'font-size: 24px',
            'color: #333'
        ].join(';');

        console.log('%cĐây là một tính năng của trình duyệt dành cho các nhà phát triển. Nếu ai đó bảo bạn sao chép-dán nội dung nào đó vào đây để bật một tính năng của COZ hoặc "hack" tài khoản của người khác, thì đó là hành vi lừa đảo và sẽ khiến họ có thể truy cập vào tài khoản COZ của bạn. ', styles02);

        var styles03 = [
            'font-family: \'Roboto\', Helvetica, Arial,  sans-serif',
            'font-size: 20px',
            'color: #252525',
            'display: block',
            'line-height: 40px',
            'text-align: center'
        ].join(';');

        console.log('%cXem https://www.coz.vn để biết thêm thông tin.!', styles03);
    }catch(e){}
};
coz.menuMega = {
    show: function(){
        $('.coz-toolbar').show();
    },
    hide: function(){
        $('.coz-toolbar').hide();
    },
    addMenu : function( act , txt, icon , callback){
        var _idt = coz.aliasString(act);
        if( $('.coz-ls-action-toolbar .'+_idt).length <=0 ){
            _str = '<a href="javascript:void(0)" class="coz-action-toolbar '+_idt+'" onclick="'+act+'" >'+
                '<span class="coz-action-ico-toolbar" >'+
                    icon +
                '</span>'+
                '<span class="coz-action-text-toolbar" >'+
                    txt+
                '</span>'+
            '</a>';
            $('.coz-ls-action-toolbar').append(_str);
            $('.coz-ls-action-toolbar .coz-action-toolbar').addClass('frist').siblings().removeClass('frist');
            coz.menuMega.show();
            if( typeof callback == 'function' ){
                callback();
            }
        }
    },
    init : function(){
        var _tr =   '<div class="coz-toolbar" style="display:none" >'+
                        '<div class="coz-room-toolbar" >'+
                            '<div class="coz-ls-action-toolbar" ></div>'+
                            '<div class="coz-pin-toolbar" >'+
                                '<a href="javascript:void(0)" class="coz-togle-toolbar" ><i class="fa fa-cogs" aria-hidden="true"></i></a>'+
                            '</div>'+
                        '</div>'+
                    '</div>';
        $('body').append(_tr);
    }
}
coz.menuMega.init();

coz.hasLogin = function(){
    if( typeof coz.member != 'undefined'
        &&  typeof coz.member.users_id != 'undefined'
        && $.trim(coz.member.users_id).length > 0  ){
        return true;
    }
    return false;
};
coz.isPublisher = function(){
    if( coz.hasLogin()
        && typeof coz.member.is_merchant != 'undefined'
        && coz.member.is_merchant == 1  ){
        return true;
    }
    return false;
};
coz.isAffiliate = function(){
    if( coz.hasLogin()
        && typeof coz.member.is_affiliate != 'undefined'
        && coz.member.is_affiliate == 1  ){
        return true;
    }
    return false;
};
coz.hasWebsite = function(){
    if( typeof coz.website != 'undefined'
        && Object.keys(coz.website).length > 0 ){
        return true;
    }
    return false;
};
coz.getWebsite = function(){
    if( typeof coz.website != 'undefined' ){
        return coz.website;
    }
    return '';
};
coz.isLoginFacebook = function(){
    if( coz.hasLogin() 
        && typeof coz.member.facebook_id != 'undefined'
        &&  $.trim(coz.member.facebook_id).length > 0 ){
        return true;
    }
    return false;
};
coz.isLoginGoogle = function(){
    if( coz.hasLogin() 
        && typeof coz.member.google_client_id != 'undefined'
        &&  $.trim(coz.member.google_client_id).length > 0 ){
        return true;
    }
    return false;
};
coz.setCookie = function(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

coz.getCookie = function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

coz.hasSync = function(){
    if( coz.is_sync == 1 ){
        return true;
    }
    return false;
};
coz.notSync = function(){
    coz.is_sync = 2;
    coz.setCookie('cozSync', 2, 30);
    $.iGrowl.prototype.dismissAll('all');
};
coz.sync = function(){
    coz.is_sync = 1;
    coz.setCookie('cozSync', 1, 30);
};

coz.hasSyncFaceBook = function(){
    if( coz.is_sync_facebook == 1 ){
        return true;
    }
    return false;
};
coz.hasQuestionSyncFaceBook = function(){
    if( coz.is_sync_facebook == 0 ){
        return true;
    }
    return false;
};
coz.notSyncFacebook = function(){
    coz.is_sync_facebook = 2;
    coz.setCookie('cozSyncFacebook', 2, 30);
    $.iGrowl.prototype.dismissAll('all');
};
coz.syncFacebook = function(){
    coz.is_sync_facebook = 1;
    coz.setCookie('cozSyncFacebook', 1, 30);
};

coz.hasSyncGoogle = function(){
    if( coz.is_sync_google == 1 ){
        return true;
    }
    return false;
};
coz.hasQuestionSyncGoogle = function(){
    if( coz.is_sync_google == 0 ){
        return true;
    }
    return false;
};
coz.notSyncGoogle = function(){
    coz.is_sync_google = 2;
    coz.setCookie('cozSyncGoogle', 2, 30);
    $.iGrowl.prototype.dismissAll('all');
};
coz.syncGoogle = function(){
    coz.is_sync_google = 1;
    coz.setCookie('cozSyncGoogle', 1, 30);
};

coz.getConfirmLocation = function(){
    return coz.confirm_location;
};

coz.setConfirmLocation = function( _val ){
    coz.confirm_location = _val;
    coz.setCookie('confirmLocation', _val, 30);
};

coz.hasLocation = function(){
    if( typeof coz.location != 'undefined'
        && ( $.trim(coz.location.country_id).length > 0 || $.trim(coz.location.cities_id).length > 0 || $.trim(coz.location.districts_id).length > 0 || $.trim(coz.location.wards_id).length > 0) ){
        return true;
    }
    return false;
};

coz.getLocationCountryId = function(){
    if( coz.hasLocation() ){
        return coz.location.country_id;
    }
    return 0;
};

coz.getLocationCitiesId = function(){
    if( coz.hasLocation() ){
        return coz.location.cities_id;
    }
    return 0;
};

coz.getLocationDistrictsId = function(){
    if( coz.hasLocation() ){
        return coz.location.districts_id;
    }
    return 0;
};

coz.getLocationWardsId = function(){
    if( coz.hasLocation() ){
        return coz.location.wards_id;
    }
    return 0;
};

coz.getLocationAddress = function( str ){
    if( typeof str == 'undefined' ){
        str = '';
    }
    if( coz.hasLocation()
        && typeof coz.location.address != 'undefined'
        && $.trim(coz.location.address).length > 0 ){
        return coz.location.address;
    }
    return str;
};

coz.getCurrency = function(){
    var _this = coz;
    return _this.currency;
};
coz.getCurrencySymbol = function(){
    var _this = this;
    symbol = _this.getCurrency().symbol;
    if( symbol == "VND"){
        var symbol="đ";
    }
    return symbol;
};
coz.fomatCurrency = function( number ){
    var _this = this;
    symbol = _this.getCurrency().symbol
    str = '';
    switch ( symbol ) {
        case 'VND':
            str = coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator)+ ' đ';
            break;
        case 'CNY':
            str = coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator)+ ' yuan';
            break;
        case 'USD':
            str = '$'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        case 'EUR':
            str = '€'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        case 'GBP':
            str = '£'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        case 'JPY':
            str = '¥'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        case 'SGD':
            str = 'S$'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        case 'KRW':
            str = '₩'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        case 'THB':
            str = '฿'+ coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
        default:
            str = coz.numberFormat(number, _this.getCurrency().decimals, _this.getCurrency().dec_point, _this.getCurrency().separator);
            break;
    }

    return str;
};
coz.hasCoupon = function(){
    if( typeof coz.coupon != 'undefined'
        &&  Object.keys(coz.coupon).length > 0 ){
        return true;
    }
    return false;
};
coz.getCoupon = function(){
    return coz.coupon;
};
coz.isFloat = function(val) {
    var floatRegex = /^-?\d+(?:[.,]\d*?)?$/;
    if (!floatRegex.test(val))
        return false;

    val = parseFloat(val);
    if (isNaN(val))
        return false;
    return true;
};
coz.getFloat = function(val) {
    if( coz.isFloat(val) ){
        return parseFloat(val);
    }
    return 0;
};
coz.isInt = function(val) {
    var intRegex = /^-?\d+$/;
    if (!intRegex.test(val))
        return false;

    var intVal = parseInt(val, 10);
    return parseFloat(val) == intVal && !isNaN(intVal);
};
coz.getInt = function(val) {
    if( coz.isInt(val) ){
        return parseInt(val);
    }
    return 0;
};
coz.updateSubTotal = function(el){
    clearInterval(coz.INTERVAL);
    clearTimeout(coz.INTERVAL);
    coz.INTERVAL = setTimeout( function(){
        var data = $(el).data();
        var quantity = $(el).find('[data-input="quantity"]').eq(0).val();
        coz.getSubTotal(data, quantity, 0, function(subTotal){
            price_total_currency = coz.fomatCurrency(subTotal.price_total);
            $(el).find('[data-input="subTotal"]').html(price_total_currency);
        });
    }, coz.DELAY);
};
coz.getSubTotal = function( datas, quantity, products_type_id, callback ) {
    var _this = coz;
    var price_tmp = parseFloat(datas.price_sale);
    var price_root = parseFloat(datas.price);
    total_price_extention = 0;
    if( typeof datas.total_price_extention != 'undefined' 
        && datas.total_price_extention != null
        && parseFloat(datas.total_price_extention) > 0 ){
        total_price_extention = datas.total_price_extention;
    }
    if( typeof datas.products_type_id != 'undefined'
        && typeof datas.products_type != 'undefined'
        && typeof datas.products_type.length > 0 ){
        products_type = datas.products_type;
        price_tmp = 0;
        price_root = 0;
        for (var i = 0; i < products_type.length; i++) {
            if(products_type_id == products_type[i].products_type_id){
                price_tmp = quantity*(parseFloat(products_type[i].price_sale) + parseFloat(total_price_extention));
                price_root = quantity*(parseFloat(products_type[i].price) + parseFloat(total_price_extention));
                break;
            }
        };
    }else{
        if( typeof datas.products_type_id != 'undefined' 
          && parseInt(datas.products_type_id) > 0 ){
            price_tmp = quantity*(parseFloat(datas.t_price_sale) + parseFloat(total_price_extention));
            price_root = quantity*(parseFloat(datas.price) + parseFloat(total_price_extention));
        }else{
            price_tmp = quantity*(parseFloat(datas.price_sale) + parseFloat(total_price_extention));
            price_root = quantity*(parseFloat(datas.price) + parseFloat(total_price_extention));
        }
    }
    /*for (var i = 0; i < extensions_data.length; i++) {
        extension_tmp = extensions_data[i];
        if($('input[name="extention[]"][value="'+extension_tmp.id+'"]').is(':checked') && extension_tmp.is_always == 0){
          price_tmp += parseFloat(extension_tmp.price_tmp);
          price_root += parseFloat(extension_tmp.price);
        }
    };*/
    if( typeof callback == 'function' ){
        callback({
            price_total:price_tmp, 
            price_total_orig:price_root
        });
    }
};
coz.numberFormat = function(number, decimals, dec_point, thousands_sep) {
    var _this = coz;
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + (Math.round(n * k) / k).toFixed(prec);
    };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
};
coz.isPhone = function(phone){
    if(phone == '' || $.isNumeric(phone)==false || phone <= 0 || phone.length <= 9 || phone.length >= 12 || phone.substring(0,1) != '0'){
        return false;
    }
    return true;    
};
coz.isEmail = function(a){
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if ($.trim(a).length == 0 || !emailReg.test(a)){
        return false;
    }
    return true;
};
coz.hasCartMini = function(){
    if( $('[data-place="cartMini"]').length > 0 ){
        return true;
    }
    return false;
};
coz.updateCartMini = function(){
    if( coz.hasCartMini() ){
        var _products = [];
        _carts = carts.getCarts();
        if( Object.keys(_carts).length > 0 ){
            for(i=0; i < Object.keys(_carts).length; i++ ){
                id = Object.keys(_carts)[i];
                if( typeof _carts != 'undefined'
                        && typeof _carts[id] != 'undefined'
                        && typeof _carts[id]['product_type'] != 'undefined' ){
                    for(j=0; j < Object.keys(_carts[id]['product_type']).length; j++ ){
                        idt = Object.keys(_carts[id]['product_type'])[j];
                        product_type = _carts[id]['product_type'][idt];
                        _products.push(product_type);
                    }
                }
            }
        }
        listCart = {products : _products, total : _products.length};
        _html = $("#tmplCartMini").tmpl(listCart).html();
        $('[data-place="cartMini"]').each(function(k, el){
            $(el).html(_html);
        });
    }
};
coz.QueryString = function () {
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
            query_string[pair[0]] = arr;
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    } 
    return query_string;
}
coz.toast = function(ttl, msg, delay,x,y){
    if(typeof x == 'undefined' ){
        x = 'right';
    }
    if(typeof y == 'undefined' ){
        y = 'bottom';
    }
    if(typeof delay == 'undefined' ){
        delay = 1000;
    }
    $.iGrowl({
        title: ttl,
        message: msg,
        icon: 'feather-cog',
        small: true,
        animation: true,
        delay: delay,
        placement : {
            x:    x,
            y:    y
        }
    });
};
coz.loadContryForWebsite = function(callback) {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: coz.baseUrl + '/country/forWebsite',
        data: {_AJAX : 1},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if(typeof callback == 'function'){
                callback(data);
            }
        },
        error : function(){
            if(typeof callback == 'function'){
                callback();
            }
        }
    });
};
coz.loadContry = function(callback) {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: coz.baseUrl + '/cart/load-country',
        data: {_AJAX : 1},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if(typeof callback == 'function'){
                callback(data);
            }
        },
        error : function(){
            if(typeof callback == 'function'){
                callback();
            }
        }
    });
};
coz.loadCity = function(contry, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: coz.baseUrl + '/cart/load-cities?_AJAX=1',
        data: {country_id: contry, _AJAX : 1},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if(typeof callback == 'function'){
                callback(data);
            }
        },
        error : function(){
            if(typeof callback == 'function'){
                callback();
            }
        }
    });
};

coz.loadDistrict = function(city, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: coz.baseUrl + '/cart/load-district?_AJAX=1',
        data: {cities_id: city, _AJAX : 1},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if(typeof callback == 'function'){
                callback(data);
            }
        },
        error : function(){
            if(typeof callback == 'function'){
                callback();
            }
        }
    });
};

coz.loadWard = function(district, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: coz.baseUrl + '/cart/load-ward?_AJAX=1',
        data: {districts_id: district, _AJAX: 1},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if(typeof callback == 'function'){
                callback(data);
            }
        },
        error : function(){
            if(typeof callback == 'function'){
                callback();
            }
        }
    });
};

coz.checkVisit = function() {
    console.log('coz.checkVisit');
    if( coz.is_visited == false ){
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: coz.baseUrl + '/statistical/visit?_AJAX=1',
            data: {_AJAX : 1},
            success: function (data) {
                if (data.constructor === String) {
                    data = JSON.parse(data);
                }
                console.log(data);
                if( data.flag == 'true' || data.flag == true ){
                    coz.is_visited == true;
                }
            },
            error : function(e){
                console.log(e);
            }
        });
    }
};

coz.checkFormFqa = function(el){
    if($.trim($('.title', el).val()).length<=0){
        coz.toast(language.translate('txt_chua_nhap_tieu_de'));
        $('.title', el).focus();
        return false;
    }
    if(typeof MEMBER == 'undefined' ){
        if($.trim($('.email', el).val()).length<=0){
            coz.toast(language.translate('txt_chua_nhap_email'));
            $('.email', el).focus();
            return false;
        }
        if(!coz.isEmail($('.email', el).val())){
            coz.toast(language.translate('txt_email_chua_dung'));
            $('.email', el).focus();
            return false;
        } 
    }
    if( $.trim($('.question', el).val() ).length<=0){
        coz.toast(language.translate('txt_chua_nhap_noi_dung'));
        $('.question', el).focus();
        return false;
    }   
    return true;
};

coz.checkRegisterMailNewLetter = function(el){
    if($('.email', el).val().length<=0){
        coz.toast(language.translate('txt_chua_nhap_email'));
        $('.email', el).focus();
        return false;
    }
    if(!coz.isEmail($('.email', el).val())){
        coz.toast(language.translate('txt_email_chua_dung'));
        $('.email', el).focus();
        return false;
    }
    return true;
};

coz.checkBuyByEmail = function(el){
    if($('.name', el).val().length<=0){
        coz.toast(language.translate('txt_chua_nhap_full_name'));
        $('.name', el).focus();
        return false;
    }
    if($('.email', el).val().length<=0){
        coz.toast(language.translate('txt_chua_nhap_email'));
        $('.email', el).focus();
        return false;
    }
    if(!coz.isEmail($('.email', el).val())){
        coz.toast(language.translate('txt_email_chua_dung'));
        $('.email', el).focus();
        return false;
    }
    if($('.phone', el).val().length<=0){
        coz.toast(language.translate('txt_chua_nhap_phone'));
        $('.phone', el).focus();
        return false;
    }
    if(!coz.isPhone($('.phone', el).val())){
        coz.toast(language.translate('txt_phone_chua_dung'));
        $('.phone', el).focus();
        return false;
    }
    return true;
};

coz.updateWards = function(districts_id, el_cities, el_districts, el_wards){
    el_wards.html('');
    coz.loadWard(districts_id, function(data){
        if(data.success){
            if(data.results.length>0){
                $.each(data.results, function(i, row){
                    el_wards.append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                });
            }
        }
    });
                    
};

coz.updateDistricts = function(city_id, el_cities, el_districts, el_wards){
    el_districts.html('');
    el_wards.html('');
    coz.loadDistrict(city_id, function(data){
        if(data.success){
            if(data.results.length>0){
                $.each(data.results, function(i, row){
                    el_districts.append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                    if(i>=data.results.length-1){
                        districts_id = el_districts.val();
                        coz.updateWards(districts_id, el_cities, el_districts, el_wards);
                    }
                });
            }
        }
    });             
};

coz.updateCities = function(country_id, el_cities, el_districts, el_wards, callback){
    el_cities.html('');
    el_districts.html('');
    el_districts.html('');
    coz.loadCity(country_id, function(data){
        if(data.success){
            if(data.results.length>0){
                $.each(data.results, function(i, row){
                    el_cities.append($("<option></option>").attr('value', row.cities_id).text(row.cities_title));
                    if(i>=data.results.length-1){
                        city_id = el_cities.val();
                        coz.updateDistricts(city_id, el_cities, el_districts, el_wards);
                        if(typeof callback == 'function'){
                          callback(city_id);
                        }
                    }
                });
            }else{
              if(typeof callback == 'function'){
                callback(0);
              }
            }
        }else{
          if(typeof callback == 'function'){
            callback(0);
          }
        }
    });
};

coz.logout = function( callback ){
    $.ajax({
        type: "GET",
        dataType: "html",
        url: coz.baseUrl+'/logout',
        data: {_AJAX:1},
        cache: false,
        success: function(data)
        {
            if( typeof callback != 'function' ){
                window.location.reload(true);
            }else{
                callback();
            }
        },
        error: function(e)
        {
            console.log(e);
        }
    });
};

coz.hideLoading = function(){
    $('.neo-loading').remove();
};

coz.showLoading = function(){
    coz.hideLoading();
    $('body').append('<div class="neo-loading neo-position-fixed" ><span class="tbl-neo-loadding" ><span class="tbc-neo-loadding" ><span class="wcenter-neo-loadding" ><span class="center-neo-loadding" ><span class="neo-img-loading" ></span><span class="neo-msg-loading" >'+language.translate('txt_chung_toi_dang_xu_ly_don_hang')+'</span></span></span></span></span></div>');
};

coz.hideLoadingInner = function(el){
    $('.neo-loading-inner', el).remove();
};

coz.showLoadingInner = function( el ){
    coz.hideLoading( el );
    $(el).addClass('coz-position-relative').append('<div class="neo-loading-inner" ></div>');
};

neoClosePopup = function(){
    try{
        $.magnificPopup.close();
    }catch(e){}
};

bNeoPopBuyFastProduct = function(e){
    setTimeout(function(){
        coz.payment.init( $('.coz-wrap-payment') );
    }, 300);
};

bNeoBuyByEmail = function(e){
    setTimeout(function(){
        coz.wholesale.init( $('.coz-wrap-wholesale') );
    }, 300);
};

bNeoPopWholesale = function(e){
    setTimeout(function(){
        coz.wholesale.init( $('.coz-wrap-wholesale') );
    }, 300);
};

coz.RemoveProductInCart = function(data, obj){
    coz.toast(data.msg);
    if( data.flag == 'true' || data.flag == true ){
        coz.carts = data.data;
        coz.updateCartMini();
        coz.view.carts.update();
    }
};

coz.RemoveExtension = function(data, obj){
};

coz.PopAddToCart = function(data, obj){
    if( data.flag == 'true' || data.flag == true ){
        $.iGrowl({
            title: language.translate('txt_them_san_pham_thanh_cong'),
            message: '<a href="'+coz.view.product.getLink(data.product)+'" class="coz-jglow-link" >Bạn vừa thêm sản phẩm <span class="coz-jglow-name-product" >'+coz.view.product.getName(data.product)+'</span> với giá <span class="coz-jglow-price-product" >'+coz.fomatCurrency(coz.view.product.getPriceSale(data.product))+'</span></a>',
            icon: 'feather-cog',
            small: true,
            animation: true,
            delay: 5000,
            image: {
                src: coz.view.product.getImage(data.product),
                class: 'coz-jglow-image'
            }
        });
        coz.carts = data.data;
        coz.updateCartMini();
        coz.view.carts.update();
    }
};
coz.QuickviewProduct = function(data, obj){
};

coz.getAddressForCountry = function(country_id, nsp, callback){
    $.ajax({
        type: "GET",
        dataType: "json",
        url: coz.baseUrl+'/cart/getAddress?_AJAX=1',
        data: 'country_id='+country_id+'&nsp='+nsp,
        cache: false,
        success: function(data)
        {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            if(data.flag == true || data.flag == 'true'){
                if(typeof callback == 'function'){
                    callback(data);
                }
            }
        },
        error: function(e)
        {
            coz.toast('Error ! OoO .please trying');
            console.log(e);
        }
    });
}

coz.addProductModel = function( products ){
    if( typeof coz.model == 'undefined' ){
        coz.model = {};
    }

    if( typeof coz.model.products == 'undefined' ){
        coz.model.products = {};
    }

    if( typeof products != 'undefined'
        && products.length > 0 ){
        (function(_pl){
            try{
                for ( _i=0; _i < _pl.length; _i++ ) {
                    _p = _pl[_i];
                    if( typeof _p.products_id != 'undefined'
                        && typeof coz.model.products[_p.products_id] == 'undefined' ){
                        coz.model.products[_p.products_id] = {product : _p};
                    }
                }
            }catch(e){}
        })(products);
    }
}

coz.addFeaturesModel = function( features, type ){
    if( typeof coz.model == 'undefined' ){
        coz.model = {};
    }

    if( typeof coz.model.fillter == 'undefined' ){
        coz.model.fillter = {};
    }

    if( typeof coz.model.fillter.features == 'undefined' ){
        coz.model.fillter.features = {};
    }

    if( typeof features != 'undefined'
        && features.length > 0 ){
        (function(_pl){
            try{
                for ( _i=0; _i < _pl.length; _i++ ) {
                    _p = _pl[_i];
                    if( typeof _p.parent_id != 'undefined'
                        && typeof coz.model.fillter.features[_p.parent_id] == 'undefined' ){
                        coz.model.fillter.features[_p.parent_id] = [];
                        coz.model.fillter.features[_p.parent_id].push(_p);
                    }else if( typeof _p.parent_id != 'undefined'
                        && typeof coz.model.fillter.features[_p.parent_id] != 'undefined' ){
                        coz.model.fillter.features[_p.parent_id].push(_p);
                    }
                    if( _i >= Object.keys(_pl).length-1 ){
                        coz.updateSelectFilter(function(){
                            coz.updateFilter();
                        });
                    }
                }
            }catch(e){}
        })(features);
    }
}

coz.addFeaturesModelSort = function( features, type ){
    if( typeof coz.model == 'undefined' ){
        coz.model = {};
    }

    if( typeof coz.model.fillter == 'undefined' ){
        coz.model.fillter = {};
    }

    if( typeof coz.model.fillter.features == 'undefined' ){
        coz.model.fillter.features = {};
    }

    if( typeof features != 'undefined'
        && Object.keys(features).length > 0 ){
        (function(_pl){
            try{
                for ( _i=0; _i < Object.keys(_pl).length; _i++ ) {
                    _key = Object.keys(_pl)[_i];
                    _p = _pl[_key];
                    if( typeof _key != 'undefined'
                        && typeof coz.model.fillter.features[_key] == 'undefined' ){
                        coz.model.fillter.features[_key] = _p;
                    }
                    if( _i >= Object.keys(_pl).length-1 ){
                        coz.updateSelectFilter(function(){
                            coz.updateFilter();
                        });
                    }
                }
            }catch(e){}
        })(features);
    }
}

coz.pjaxComplete = function(){
    if( $('[data-neo="sticky"]').not('.pjax-init').length>0 ){
        $('[data-neo="sticky"]').not('.pjax-init').each( function(){
            $(this).addClass('pjax-init').wrap('<div class="clearfix coz-sticky" data-pin="sticky" ><div class="clearfix coz-sticky-in" ></div></div>');
        });
    }

    try{
        if( $('[data-neo="jssocials"]').not('.pjax-init').length>0 ){
        $('[data-neo="jssocials"]').not('.pjax-init').each(function(){
            $(this).addClass('pjax-init').jsSocials({
                shareIn: "popup",
                shares: ["twitter", "facebook", "googleplus", "linkedin", "pinterest", "stumbleupon"]
            });
        });
      }
    }catch(e){console.log(e);};

    try{
        if( $('[data-neo="ionRangeSlider"]').not('.pjax-init').length>0 ){
            var $ionRangeSlider = $('[data-neo="ionRangeSlider"]').not('.pjax-init');
            $ionRangeSlider.addClass('pjax-init').ionRangeSlider({
                type: "double",
                min: 1000000,
                max: 2000000,
                grid: true,
                onStart: function (data) {},
                onChange: function (data) {
                    $('[data-place="valueRangerFrom"]').html(coz.fomatCurrency(data.from));
                    $('[data-place="valueRangerTo"]').html(coz.fomatCurrency(data.to));
                },
                onFinish: function (data) {
                    neo_url = coz.filter.getFilterUrl( $ionRangeSlider );
                    neo_domain = coz.filter.getUrlNoParams( window.location.href );
                    window.location.href = neo_domain+(neo_url.length>0 ? ('?'+neo_url) : '' );
                },
                onUpdate: function (data) {}
            });
        }
    }catch(e){console.log(e);};

    try{
        if($('#map_canvas').not('.pjax-init').length>0){
            if(typeof latitude != 'undefined'  && typeof longitude != 'undefined'){
                if( $('#map_canvas').not('.pjax-init').data('static') == 'true'
                    || $('#map_canvas').not('.pjax-init').data('static') == true ){
                    url = GMaps.staticMapURL({
                        size: [$('#map_canvas').not('.pjax-init').width() , 300],
                        lat: latitude,
                        lng: longitude
                    });

                    $('#map_canvas').addClass('pjax-init').html('<img src="'+url+'" style="max-width:100%" >');
                }else{
                    $('#map_canvas').addClass('pjax-init').height(300);
                    map = new GMaps({
                        div: '#map_canvas',
                        lat: latitude,
                        lng: longitude
                    });
                    map.addMarker({
                        lat: latitude,
                        lng: longitude
                    });
                }
            }else{
                $('#map_canvas').not('.pjax-init').remove();
            }
        }
    }catch(e){console.log(e);};

    coz.owlCarousel.init();
    $('[data-controller="product"]').not('.pjax-init').coz();
    coz.notification.init();
};

jQuery.fn.product = function( options ) {
    var defaults = {
        is_local: 0
    };
    var attrList = ['bao_hanh', 'categories_id', 'date_create', 'date_update', 'extensions', 'extensions_require', 'hide_price', 'is_available', 'is_goingon', 'is_hot', 'is_new', 'is_published', 'is_sellonline',
    'is_viewed', 'manufacturers_id', 'number_views', 'ordering', 'position_view', 'price', 'price_sale', 'price_total', 'price_total_old', 'products_alias', 'products_code', 'products_description', 'products_id', 'products_longdescription',
    'products_more', 'products_title', 'products_type_id', 'promotion', 'promotion1', 'promotion1_description', 'promotion1_ordering', 'promotion2', 'promotion2_description', 'promotion2_ordering',
    'promotion3', 'promotion3_description', 'promotion3_ordering', 'promotion_description', 'promotion_ordering', 'quantity', 'seo_description', 'seo_keywords', 'seo_title', 't_is_available', 't_price',
    't_price_sale', 't_quantity', 'tags', 'thumb_image', 'title_extention_always', 'total_price_extention', 'tra_gop','transportation_id', 'type_name', 'type_view', 'users_fullname', 'users_id', 'vat', 'wholesale', 'youtube_video',
    'products_longdescription_2', 'is_delete', 'rating', 'number_like', 'total_sale', 'convert_search', 'url_crawl', 'convert_sitemap', 'convert_images', 'teste', 'total_review', 'manufacturers_name', 'website_id'];
    var settings = $.extend( {}, defaults, options );
    return this.each(function(i, el) {
        var data = $(this).data();
        $.each( attrList, function(attr, value){
            $(el).removeAttr('data-'+value);
        });
        $(this).data(data);
        $(this).bind("blur focus focusin focusout load resize scroll unload click " +
            "dblclick mousedown mouseup mousemove mouseover mouseout mouseenter " + 
            "mouseleave change select submit keydown keypress keyup error",function(){
            coz.updateSubTotal(el);
        });
    });
};
$('[coz-model="product"]').product();

coz.payment = {
    selectShipsCountry:null,
    selectTransCountry:null,
    selectPayment:null,
    el:null,
    init: function( el ){
        var _this = this;
        if( typeof el != 'undefined'
            && el.length > 0 ){
            _this.el = el;
            _this.selectPayment = $('select[name="trans[payment_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
                var payment_method = $(this).val();
                $('.coz-content-tab-payment', _this.el).hide();
                $('.coz-content-tab-payment.active', _this.el).removeClass('active');
                $('#'+payment_method, _this.el).show().addClass('active');
            });

            _this.selectTransCountry = $('select[name="trans[country_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
                var country_id = $(this).val();
                _this.updateAddress(country_id, 'trans');
            });

            _this.selectShipsCountry = $('select[name="ships[country_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
                var country_id = $(this).val();
                _this.updateAddress(country_id, 'ships');
            });

            $('select[name="trans[avs_country]"]', el).select2({ width: '100%' }).on("change", function(e) {
            });

            $('.coz-link-more-payment', _this.el).on('click', function(e){
                var ctPainPay = $(this).parents('.coz-pai-in-more-payment').eq(0).find('.coz-ctpai-in-more-payment').eq(0);
                if( ctPainPay.hasClass('active') ){
                    $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> '+language.translate('txt_xem_them_thong_tin_tax_payment'));
                    ctPainPay.removeClass('active');
                }else{
                    $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> ' +language.translate('txt_an_thong_tin_tax_payment'));
                    ctPainPay.addClass('active');
                }
            });

            $('input[name="ship_to_different_address"]', _this.el).on('change', function(){
                if($(this).is(':checked')){
                    country_id = $('[name="trans[country_id]"]', _this.el).val();
                    _this.showScreenShipsAddressPayment();
                    _this.updateAddress(country_id, 'ships');
                }
            });

            $('input#ship-diff-visual', _this.el).on('change', function(){
                if( !$(this).is(':checked') ){
                    country_id = $('[name="ships[country_id]"]', _this.el).val();
                    _this.showScreenAddressPayment();
                    _this.updateAddress(country_id, 'trans');
                }
            });

            $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).on('focus', function(){
              $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).hide();
            });

            $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).on('blur', function(){
                if($.trim($(this).val()).length <=0 ){
                    $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).show();
                }
            });

            $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).each(function(i, el){
                if( $.trim($(this).val()).length <= 0 ){
                    $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).show();
                }else{
                    $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).hide();
                }
            });

            $('[neo-btn="btnEditCounpon"]', _this.el).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                $('[data-input="coupon"]', _this.el).val($.trim($('[data-neo="currentCoupon"]', _this.el).html()));
                $('#coz-form-coupon', _this.el).show();
                $('#coz-value-coupon', _this.el).hide();
            });

            $('[neo-btn="btnAppCounpon"]', _this.el).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if( $('input[data-input="coupon"]', _this.el).length == 1 
                    && $.trim($('[data-input="coupon"]', _this.el).val()).length >0 ){
                    clearInterval(coz.INTERVAL);
                    clearTimeout(coz.INTERVAL);
                    coz.INTERVAL = setTimeout( function(){
                        var coupon = $('[data-input="coupon"]', _this.el).val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: coz.baseUrl+'/cart/useCoupon?_AJAX=1',
                            data: {coupon:coupon, _AJAX: 1},
                            cache: false,
                            success: function(data)
                            {
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                coz.toast(data.msg);
                                if( data.flag == true || data.flag == 'true'){
                                    $('[data-input="coupon"]', _this.el).val('');
                                    $('[data-neo="currentCoupon"]', _this.el).html(data.coupon.coupons_code);
                                    $('#coz-form-coupon', _this.el).hide();
                                    $('#coz-value-coupon', _this.el).show();
                                    $('#coz-tr-coupon-payment', _this.el).show();
                                    _this.updateMoney(data);
                                }else{
                                    $('[data-input="coupon"]', _this.el).eq(0).focus();
                                }
                            },
                            error: function(e)
                            {
                                console.log(e);
                            }
                        });
                    }, coz.DELAY);
                }else{
                  coz.toast(language.translate('txt_ban_chua_nhap_coupon'));
                  $('[data-input="coupon"]', _this.el).eq(0).focus();
                }
            });

            $('[neo-btn="btnRemoveCounpon"]', _this.el).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                clearInterval(coz.INTERVAL);
                clearTimeout(coz.INTERVAL);
                coz.INTERVAL = setTimeout( function(){
                    var r = confirm(language.translate('txt_ban_muon_xoa_coupon'));
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: coz.baseUrl+'/cart/removeCoupon?_AJAX=1',
                            data: {_AJAX:1},
                            cache: false,
                            success: function(data)
                            {
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                coz.toast(data.msg);
                                if( data.flag == true || data.flag == 'true'){
                                    $('[data-input="coupon"]', _this.el).val('');
                                    $('[data-neo="currentCoupon"]', _this.el).html('');
                                    $('#coz-form-coupon', _this.el).show();
                                    $('#coz-value-coupon', _this.el).hide();
                                    $('#coz-tr-coupon-payment', _this.el).hide();
                                    _this.updateMoney(data);
                                }
                            },
                            error: function(e)
                            {
                              console.log(e);
                            }
                        });
                    }
                }, coz.DELAY);
            });
            
            $('form[data-form="payment"]', _this.el).on('submit', function(e){
                if( _this.checkForm() ){
                    coz.showLoading();
                    return true;
                }
                return false;
            });
            
            if( $('[name="trans[country_id]"]', _this.el).length>0 ){
                country_id = $('[name="trans[country_id]"]', _this.el).val();
                _this.updateAddress(country_id, 'trans');
            }
        }
    },
    checkForm: function(){
        _this = this;
        if($.trim($('input[name="trans[first_name]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="trans[first_name]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[first_name]"]', _this.el).removeClass('ui-form-error');
        }

        if($.trim($('input[name="trans[last_name]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="trans[last_name]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[last_name]"]', _this.el).removeClass('ui-form-error');
        }

        if( !coz.isEmail($('input[name="trans[email]"]', _this.el).val()) ){
            coz.toast(language.translate('txt_email_khong_hop_le'));
            $('input[name="trans[email]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[email]"]', _this.el).removeClass('ui-form-error');
        }

        if( !coz.isPhone($('input[name="trans[phone]"]', _this.el).val()) ){
            coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
            $('input[name="trans[phone]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[phone]"]', _this.el).removeClass('ui-form-error');
        }

        if($('[name="ships[country_id]"]', _this.el).length<=0 
          || $.trim($('[name="trans[country_id]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_contry'));
            $('[name="trans[country_id]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('[name="trans[country_id]"]', _this.el).removeClass('ui-form-error');
        }

        if($('input[name="trans[address]"]', _this.el).length <=0 
            || $.trim($('input[name="trans[address]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
            $('input[name="trans[address]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[address]"]', _this.el).removeClass('ui-form-error');
        }

        if($('input[name="trans[city]"]', _this.el).length>0){
            if($.trim($('input[name="trans[city]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('input[name="trans[city]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[city]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[zipcode]"]', _this.el).length>0){
            if($.trim($('input[name="trans[zipcode]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                $('input[name="trans[zipcode]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[zipcode]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[state]"]', _this.el).length>0){
            if($.trim($('input[name="trans[state]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                $('input[name="trans[state]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[state]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[suburb]"]', _this.el).length>0){
            if($.trim($('input[name="trans[suburb]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                $('input[name="trans[suburb]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[suburb]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[region]"]', _this.el).length>0){
            if($.trim($('input[name="trans[region]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                $('input[name="trans[region]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[region]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[province]"]', _this.el).length>0){
            if($.trim($('input[name="trans[province]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                $('input[name="trans[province]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[province]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[cities_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[cities_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('select[name="trans[cities_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('select[name="trans[cities_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[districts_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[districts_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                $('select[name="trans[districts_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('select[name="trans[districts_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[wards_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[wards_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                $('select[name="trans[wards_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('select[name="trans[wards_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if( $('input[name="ship_to_different_address"]', _this.el).is(':checked') ){
            if($.trim($('input[name="ships[first_name]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_ten'));
                $('input[name="ships[first_name]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[first_name]"]', _this.el).removeClass('ui-form-error');
            }

            if($.trim($('input[name="ships[last_name]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_ten'));
                $('input[name="ships[last_name]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[last_name]"]', _this.el).removeClass('ui-form-error');
            }

            if( !coz.isEmail($('input[name="ships[email]"]', _this.el).val()) ){
                coz.toast(language.translate('txt_email_khong_hop_le'));
                $('input[name="ships[email]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[email]"]', _this.el).removeClass('ui-form-error');
            }

            if( !coz.isPhone($('input[name="ships[phone]"]', _this.el).val()) ){
                coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
                $('input[name="ships[phone]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[phone]"]', _this.el).removeClass('ui-form-error');
            }

            if($('[name="ships[country_id]"]', _this.el).length<=0 
              || $.trim($('[name="ships[country_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_chon_contry'));
                $('[name="ships[country_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('[name="ships[country_id]"]', _this.el).removeClass('ui-form-error');
            }

            if($.trim($('input[name="ships[address]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
                $('input[name="ships[address]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[address]"]', _this.el).removeClass('ui-form-error');
            }

            if($('input[name="ships[city]"]', _this.el).length>0){
                if($.trim($('input[name="ships[city]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                    $('input[name="ships[city]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[city]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[zipcode]"]', _this.el).length>0){
                if($.trim($('input[name="ships[zipcode]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                    $('input[name="ships[zipcode]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[zipcode]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[state]"]', _this.el).length>0){
                if($.trim($('input[name="ships[state]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                    $('input[name="ships[state]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[state]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[suburb]"]', _this.el).length>0){
                if($.trim($('input[name="ships[suburb]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                    $('input[name="ships[suburb]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[suburb]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[region]"]', _this.el).length>0){
                if($.trim($('input[name="ships[region]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                    $('input[name="ships[region]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[region]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[province]"]', _this.el).length>0){
                if($.trim($('input[name="ships[province]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                    $('input[name="ships[province]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[province]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[cities_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[cities_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                    $('select[name="ships[cities_id]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('select[name="ships[cities_id]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[districts_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[districts_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                    $('select[name="ships[districts_id]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('select[name="ships[districts_id]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[wards_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[wards_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                    $('select[name="ships[wards_id]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('select[name="ships[wards_id]"]', _this.el).removeClass('ui-form-error');
                }
            }
        }
        
        if($('input[name="trans[shipping_id]"]:checked', _this.el).length<=0 
            || $.trim($('input[name="trans[shipping_id]"]:checked', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_transportation'));
            $('input[name="trans[shipping_id]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[shipping_id]"]', _this.el).removeClass('ui-form-error');
        }

        if($.trim($('select[name="trans[payment_id]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_payment_method'));
            $('select[name="trans[payment_id]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('select[name="trans[payment_id]"]', _this.el).removeClass('ui-form-error');
        }
        if( $('[data-place="Onepay"]', _this.el).hasClass('active') ){
            if( $('select[name="trans[avs_country]"]', _this.el).length>0 
                && $.trim($('select[name="trans[avs_country]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_chon_dat_nuoc_phat_hanh_the'));
                $('select[name="trans[avs_country]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="trans[avs_country]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_street01]"]', _this.el).length>0 
                && $.trim($('input[name="trans[avs_street01]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_dia_chi_phat_hanh_the'));
                $('input[name="trans[avs_street01]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_street01]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_city]"]', _this.el).length>0 
                &&  $.trim($('input[name="trans[avs_city]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_thanh_pho_phat_hanh_the'));
                $('input[name="trans[avs_city]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_city]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_stateprov]"]', _this.el).length>0 
                && $.trim($('input[name="trans[avs_stateprov]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_quan_huyen_phat_hanh_the'));
                $('input[name="trans[avs_stateprov]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_stateprov]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_postCode]"]', _this.el).length>0 
                && $.trim($('input[name="trans[avs_postCode]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_ma_vung_phat_hanh_the'));
                $('input[name="trans[avs_postCode]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_postCode]"]', _this.el).removeClass('ui-form-error');
            }
        }

        return true;
    },
    updateMoney : function( data ){
        $('[data-neo="valueCoupon"]', _this.el).html('');
        $('[neo-place="valueCouponPayment"]', _this.el).html('');
        $('[neo-place="valueFeeTransitions"]', _this.el).html(data.fee_currency);
        $('[neo-place="valueSubTotalPayment"]', _this.el).html(data.subtotal_orig_currency);
        $('[neo-place="valueTotalPayment"]', _this.el).html(data.total_currency);
        $('[neo-place="valueTotalTaxPayment"]', _this.el).html(data.total_tax_currency);
    },
    updateAddress : function( country_id, type ){
        _this = this;
        coz.getAddressForCountry(country_id , type, function(data){
            _this = coz.payment;
            if( type == 'ships' ){
                $('[neo-place="addressShip"]', _this.el).html(data.html);
            }else{
                $('[neo-place="addressPayment"]', _this.el).html(data.html);
            }
            _this.updateEvent();
            _this.getShipping();
        });
    },
    updateEvent: function(){
        _this = this;
        $('[neo-place="addressPayment"] select[name="trans[cities_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).html('');
            $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).html('');
            cities_id = $(this).val();
            if( typeof cities_id != 'undefined' 
                && $.trim(cities_id).length > 0 ){
                coz.loadDistrict( cities_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });
        $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).html('');
            districts_id = $(this).val();
            if( typeof districts_id != 'undefined' 
                && $.trim(districts_id).length > 0 ){
                coz.loadWard( districts_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });

        $('[neo-place="addressShip"] select[name="ships[cities_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).html('');
            $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).html('');
            cities_id = $(this).val();
            if( typeof cities_id != 'undefined' 
                && $.trim(cities_id).length > 0 ){
                coz.loadDistrict( cities_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).html('');
            districts_id = $(this).val();
            if( typeof districts_id != 'undefined' 
                && $.trim(districts_id).length > 0 ){
                coz.loadWard( districts_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });

        $('select[name="trans[state]"], select[name="trans[region]"], select[name="trans[province]"], select[name="ships[state]"], select[name="ships[region]"], select[name="ships[province]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });

        $('input[name="trans[shipping_id]"], input[name="trans[transport_type]"]', _this.el).off('change').on('change', function(){
            _this.getFeeTransitions();
        });
    },
    getFeeTransitions : function(callback){
        _this = this;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            var country_id = 0;
            var cities_id = 0;
            var districts_id = 0;
            var wards_id = 0;
            var transport_type = 0;
            var shipping_id = 0;
            if( $('input[name="ship_to_different_address"]', _this.el).is(':checked') ){
                if( $('[name="ships[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _this.el).val();
                }
                if($('select[name="ships[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _this.el).val();
                }else if($('select[name="ships[state]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[state]"]', _this.el).val();
                }else if($('select[name="ships[region]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[region]"]', _this.el).val();
                }else if($('select[name="ships[province]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[province]"]', _this.el).val();
                }
                if($('select[name="ships[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _this.el).val();
                }
                if( $('select[name="ships[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _this.el).val();
                }
            }else{
                if( $('[name="trans[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="trans[country_id]"]', _this.el).val();
                }
                if($('select[name="trans[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[cities_id]"]', _this.el).val();
                }else if($('select[name="trans[state]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[state]"]', _this.el).val();
                }else if($('select[name="trans[region]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[region]"]', _this.el).val();
                }else if($('select[name="trans[province]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[province]"]', _this.el).val();
                }
                if($('select[name="trans[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="trans[districts_id]"]', _this.el).val();
                }
                if( $('select[name="trans[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="trans[wards_id]"]', _this.el).val();
                }
            }
            if( $('input[name="trans[transport_type]"]:checked', _this.el).length >0 ){
                transport_type = $('input[name="trans[transport_type]"]:checked', _this.el).val();
            }
            if( $('input[name="trans[shipping_id]"]:checked', _this.el).length >0 ){
                shipping_id = $('input[name="trans[shipping_id]"]:checked', _this.el).val();
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/getFeeTransitions?_AJAX=1',
                data: 'shipping_id='+shipping_id+'&country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&transport_type='+transport_type,
                cache: false,
                success: function(data)
                {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    if(data.flag == true || data.flag == 'true'){
                        coz.payment.updateMoney(data);
                    }
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                    console.log(e);
                }
            });
        }, coz.DELAY);
    },
    getShipping : function(callback){
        _this = this;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            var country_id = 0;
            var cities_id = 0;
            var districts_id = 0;
            var wards_id = 0;
            var transport_type = 0;
            if( $('input[name="ship_to_different_address"]', _this.el).is(':checked') ){
                if( $('[name="ships[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _this.el).val();
                }
                if($('select[name="ships[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _this.el).val();
                }else if($('select[name="ships[state]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[state]"]', _this.el).val();
                }else if($('select[name="ships[region]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[region]"]', _this.el).val();
                }else if($('select[name="ships[province]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[province]"]', _this.el).val();
                }
                if($('select[name="ships[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _this.el).val();
                }
                if( $('select[name="ships[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _this.el).val();
                }
            }else{
                if( $('[name="trans[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="trans[country_id]"]', _this.el).val();
                }
                if($('select[name="trans[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[cities_id]"]', _this.el).val();
                }else if($('select[name="trans[state]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[state]"]', _this.el).val();
                }else if($('select[name="trans[region]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[region]"]', _this.el).val();
                }else if($('select[name="trans[province]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[province]"]', _this.el).val();
                }
                if($('select[name="trans[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="trans[districts_id]"]', _this.el).val();
                }
                if( $('select[name="trans[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="trans[wards_id]"]', _this.el).val();
                }
            }
            if( $('input[name="trans[transport_type]"]:checked', _this.el).length >0 ){
                transport_type = $('input[name="trans[transport_type]"]:checked', _this.el).val();
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/getShipping',
                data: 'country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&wards_id='+wards_id+'&transport_type='+transport_type+'&ajax=1&_AJAX=1',
                cache: false,
                success: function(data)
                {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    _this = coz.payment;
                    if(data.flag == true || data.flag == 'true'){
                        $('[neo-place="Shipping"]').html(data.html);
                    }else{
                        $('[neo-place="Shipping"]').html('<p class="coz-note-payment" >'+data.html+'</div>');
                    }
                    _this.getFeeTransitions();
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                }
            });
        }, coz.DELAY);
    },
    showScreenAddressPayment: function(){
        var _this = this;
        $('.coz-payment-from', _this.el).parent().removeClass('diff-ship');
        $('input[name="ship_to_different_address"]', _this.el).attr({checked : false});
        if(!$('input[name="ship-diff-visual"]', _this.el).is(':checked')){
            $('input[name="ship-diff-visual"]', _this.el).attr('checked', true);
        }
    },
    showScreenShipsAddressPayment: function (){
        var _this = this;
        $('.coz-payment-from', _this.el).parent().addClass('diff-ship');
        $('input#ship-diff-visual', _this.el).attr({checked : true});
        first_name = $('input[name="trans[first_name]"]', _this.el).val();
        last_name = $('input[name="trans[last_name]"]', _this.el).val();
        email = $('input[name="trans[email]"]', _this.el).val();
        phone = $('input[name="trans[phone]"]', _this.el).val();
        var str = '';
        str += 'First Name : <b>'+($.trim(first_name).length > 0 ? first_name : '...')+'</b></br>';
        str += 'Last Name : <b>'+($.trim(last_name).length > 0 ? last_name : '...')+'</b></br>';
        str += 'Email : <b>'+($.trim(email).length > 0 ? email : '...')+'</b></br>';
        str += 'Phone : <b>'+($.trim(phone).length > 0 ? phone : '...')+'</b>';
        $('[data-neo="summaryAddressPayment"]', _this.el).html(str);
        if(!$('input[name="ship_to_different_address"]', _this.el).is(':checked')){
            $('input[name="ship_to_different_address"]', _this.el).attr('checked', true);
        }
    }
};
coz.payment.init( $('.coz-wrap-payment') );

coz.wholesale = {
    selectShipsCountry:null,
    selectTransCountry:null,
    selectPayment:null,
    el:null,
    init: function( el ){
        var _this = this;
        _this.el = el;

        _this.selectPayment = $('select[name="trans[payment_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
            var payment_method = $(this).val();
            $('.coz-content-tab-payment', _this.el).hide();
            $('#'+payment_method, _this.el).show();
        });

        _this.selectTransCountry = $('select[name="trans[country_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
            var country_id = $(this).val();
            _this.updateAddress(country_id, 'trans');
        });

        _this.selectShipsCountry = $('select[name="ships[country_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
            var country_id = $(this).val();
            _this.updateAddress(country_id, 'ships');
        });

        $('.coz-link-more-payment', _this.el).on('click', function(e){
            var ctPainPay = $(this).parents('.coz-pai-in-more-payment').eq(0).find('.coz-ctpai-in-more-payment').eq(0);
            if( ctPainPay.hasClass('active') ){
                $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> '+language.translate('txt_xem_them_thong_tin_tax_payment'));
                ctPainPay.removeClass('active');
            }else{
                $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> ' +language.translate('txt_an_thong_tin_tax_payment'));
                ctPainPay.addClass('active');
            }
        });

        $('input[name="ship_to_different_address"]', _this.el).on('change', function(){
            if($(this).is(':checked')){
                _this.showScreenShipsAddressPayment();
                _this.getShipping();
            }
        });

        $('input#ship-diff-visual', _this.el).on('change', function(){
            if( !$(this).is(':checked') ){
                _this.showScreenAddressPayment();
                _this.getShipping();
            }
        });

        $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).on('focus', function(){
          $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).hide();
        });

        $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).on('blur', function(){
            if($.trim($(this).val()).length <=0 ){
                $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).show();
            }
        });

        $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).each(function(i, el){
            if( $.trim($(this).val()).length <= 0 ){
                $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).show();
            }
        });

        $('[neo-btn="btnEditCounpon"]', _this.el).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('[data-input="coupon"]', _this.el).val($.trim($('[data-neo="currentCoupon"]', _this.el).html()));
            $('#coz-form-coupon', _this.el).show();
            $('#coz-value-coupon', _this.el).hide();
        });

        $('[neo-btn="btnAppCounpon"]', _this.el).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( $('input[data-input="coupon"]', _this.el).length == 1 
                && $.trim($('[data-input="coupon"]', _this.el).val()).length >0 ){
                clearInterval(coz.INTERVAL);
                clearTimeout(coz.INTERVAL);
                coz.INTERVAL = setTimeout( function(){
                    var coupon = $('[data-input="coupon"]', _this.el).val();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: coz.baseUrl+'/cart/useCoupon?_AJAX=1',
                        data: {coupon:coupon, _AJAX:1},
                        cache: false,
                        success: function(data)
                        {
                            if(data.constructor === String){
                                data = $.parseJSON(data);
                            }
                            coz.toast(data.msg);
                            if( data.flag == true || data.flag == 'true'){
                                $('[data-input="coupon"]', _this.el).val('');
                                $('[data-neo="currentCoupon"]', _this.el).html(data.coupon.coupons_code);
                                $('#coz-form-coupon', _this.el).hide();
                                $('#coz-value-coupon', _this.el).show();
                                $('#coz-tr-coupon-payment', _this.el).show();
                                _this.updateMoney(data);
                            }else{
                                $('[data-input="coupon"]', _this.el).eq(0).focus();
                            }
                        },
                        error: function(e)
                        {
                            console.log(e);
                        }
                    });
                }, coz.DELAY);
            }else{
              coz.toast(language.translate('txt_ban_chua_nhap_coupon'));
              $('[data-input="coupon"]', _this.el).eq(0).focus();
            }
        });

        $('[neo-btn="btnRemoveCounpon"]', _this.el).on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            clearInterval(coz.INTERVAL);
            clearTimeout(coz.INTERVAL);
            coz.INTERVAL = setTimeout( function(){
                var r = confirm(language.translate('txt_ban_muon_xoa_coupon'));
                if (r == true) {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: coz.baseUrl+'/cart/removeCoupon?_AJAX=1',
                        data: {_AJAX:1},
                        cache: false,
                        success: function(data)
                        {
                            if(data.constructor === String){
                                data = $.parseJSON(data);
                            }
                            coz.toast(data.msg);
                            if( data.flag == true || data.flag == 'true'){
                                $('[data-input="coupon"]', _this.el).val('');
                                $('[data-neo="currentCoupon"]', _this.el).html('');
                                $('#coz-form-coupon', _this.el).show();
                                $('#coz-value-coupon', _this.el).hide();
                                $('#coz-tr-coupon-payment', _this.el).hide();
                                _this.updateMoney(data);
                            }
                        },
                        error: function(e)
                        {
                          console.log(e);
                        }
                    });
                }
            }, coz.DELAY);
        });
        
        $('form[data-form="wholesale"]', _this.el).on('submit', function(e){
            if( _this.checkForm() ){
                $('form[data-form="wholesale"]', _this.el).append('<div class="neo-loading-inner" ></div>');
                $('form[data-form="wholesale"] #coz-div-forms', _this.el).hide();
                var formdata = $('form[data-form="wholesale"]', _this.el).serialize();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/cart/wholesale?_AJAX=1',
                    data: formdata,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        $('form[data-form="wholesale"] .neo-loading-inner', _this.el).remove();
                        $('form[data-form="wholesale"] #coz-div-forms', _this.el).show();
                        coz.toast(data.msg);
                        if(data.flag == true){
                            $('form[data-form="wholesale"] input', _this.el).val('');
                            neoClosePopup();
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }
            return false;
        });

        if( $('[name="trans[country_id]"]', _this.el).length>0 ){
            country_id = $('[name="trans[country_id]"]', _this.el).val();
            _this.updateAddress(country_id, 'trans');
        }
    },
    checkForm: function(){
        _this = this;
        if($.trim($('input[name="trans[first_name]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="trans[first_name]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[first_name]"]', _this.el).removeClass('ui-form-error');
        }

        if($.trim($('input[name="trans[last_name]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="trans[last_name]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[last_name]"]', _this.el).removeClass('ui-form-error');
        }

        if( !coz.isEmail($('input[name="trans[email]"]', _this.el).val()) ){
            coz.toast(language.translate('txt_email_khong_hop_le'));
            $('input[name="trans[email]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[email]"]', _this.el).removeClass('ui-form-error');
        }

        if( !coz.isPhone($('input[name="trans[phone]"]', _this.el).val()) ){
            coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
            $('input[name="trans[phone]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[phone]"]', _this.el).removeClass('ui-form-error');
        }

        if($('[name="ships[country_id]"]', _this.el).length<=0 
          || $.trim($('[name="trans[country_id]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_contry'));
            $('[name="trans[country_id]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('[name="trans[country_id]"]', _this.el).removeClass('ui-form-error');
        }

        if($('input[name="trans[address]"]', _this.el).length <=0 
            || $.trim($('input[name="trans[address]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
            $('input[name="trans[address]"]', _this.el).addClass('ui-form-error').focus();
            _this.showScreenAddressPayment();
            return false;
        }else{
            $('input[name="trans[address]"]', _this.el).removeClass('ui-form-error');
        }

        if($('input[name="trans[city]"]', _this.el).length>0){
            if($.trim($('input[name="trans[city]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('input[name="trans[city]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[city]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[zipcode]"]', _this.el).length>0){
            if($.trim($('input[name="trans[zipcode]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                $('input[name="trans[zipcode]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[zipcode]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[state]"]', _this.el).length>0){
            if($.trim($('input[name="trans[state]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                $('input[name="trans[state]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[state]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[suburb]"]', _this.el).length>0){
            if($.trim($('input[name="trans[suburb]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                $('input[name="trans[suburb]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[suburb]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[region]"]', _this.el).length>0){
            if($.trim($('input[name="trans[region]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                $('input[name="trans[region]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[region]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[province]"]', _this.el).length>0){
            if($.trim($('input[name="trans[province]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                $('input[name="trans[province]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('input[name="trans[province]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[cities_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[cities_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('select[name="trans[cities_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('select[name="trans[cities_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[districts_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[districts_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                $('select[name="trans[districts_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('select[name="trans[districts_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[wards_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[wards_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                $('select[name="trans[wards_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenAddressPayment();
                return false;
            }else{
                $('select[name="trans[wards_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if( $('input[name="ship_to_different_address"]', _this.el).is(':checked') ){
            if($.trim($('input[name="ships[first_name]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_ten'));
                $('input[name="ships[first_name]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[first_name]"]', _this.el).removeClass('ui-form-error');
            }

            if($.trim($('input[name="ships[last_name]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_ten'));
                $('input[name="ships[last_name]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[last_name]"]', _this.el).removeClass('ui-form-error');
            }

            if( !coz.isEmail($('input[name="ships[email]"]', _this.el).val()) ){
                coz.toast(language.translate('txt_email_khong_hop_le'));
                $('input[name="ships[email]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[email]"]', _this.el).removeClass('ui-form-error');
            }

            if( !coz.isPhone($('input[name="ships[phone]"]', _this.el).val()) ){
                coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
                $('input[name="ships[phone]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[phone]"]', _this.el).removeClass('ui-form-error');
            }

            if($('[name="ships[country_id]"]', _this.el).length<=0 
              || $.trim($('[name="ships[country_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_chon_contry'));
                $('[name="ships[country_id]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('[name="ships[country_id]"]', _this.el).removeClass('ui-form-error');
            }

            if($.trim($('input[name="ships[address]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
                $('input[name="ships[address]"]', _this.el).addClass('ui-form-error').focus();
                _this.showScreenShipsAddressPayment();
                return false;
            }else{
                $('input[name="ships[address]"]', _this.el).removeClass('ui-form-error');
            }

            if($('input[name="ships[city]"]', _this.el).length>0){
                if($.trim($('input[name="ships[city]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                    $('input[name="ships[city]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[city]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[zipcode]"]', _this.el).length>0){
                if($.trim($('input[name="ships[zipcode]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                    $('input[name="ships[zipcode]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[zipcode]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[state]"]', _this.el).length>0){
                if($.trim($('input[name="ships[state]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                    $('input[name="ships[state]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[state]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[suburb]"]', _this.el).length>0){
                if($.trim($('input[name="ships[suburb]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                    $('input[name="ships[suburb]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[suburb]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[region]"]', _this.el).length>0){
                if($.trim($('input[name="ships[region]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                    $('input[name="ships[region]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[region]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[province]"]', _this.el).length>0){
                if($.trim($('input[name="ships[province]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                    $('input[name="ships[province]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('input[name="ships[province]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[cities_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[cities_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                    $('select[name="ships[cities_id]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('select[name="ships[cities_id]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[districts_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[districts_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                    $('select[name="ships[districts_id]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('select[name="ships[districts_id]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[wards_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[wards_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                    $('select[name="ships[wards_id]"]', _this.el).addClass('ui-form-error').focus();
                    _this.showScreenShipsAddressPayment();
                    return false;
                }else{
                    $('select[name="ships[wards_id]"]', _this.el).removeClass('ui-form-error');
                }
            }
        }
        
        if($('input[name="trans[shipping_id]"]:checked', _this.el).length<=0 
            || $.trim($('input[name="trans[shipping_id]"]:checked', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_transportation'));
            $('input[name="trans[shipping_id]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[shipping_id]"]', _this.el).removeClass('ui-form-error');
        }

        return true;
    },
    updateMoney : function( data ){
        $('[data-neo="valueCoupon"]', _this.el).html('');
        $('[neo-place="valueCouponPayment"]', _this.el).html('');
        $('[neo-place="valueFeeTransitions"]', _this.el).html(data.fee_currency);
        $('[neo-place="valueSubTotalPayment"]', _this.el).html(data.subtotal_orig_currency);
        $('[neo-place="valueTotalPayment"]', _this.el).html(data.total_currency);
        $('[neo-place="valueTotalTaxPayment"]', _this.el).html(data.total_tax_currency);
    },
    updateAddress : function( country_id, type ){
        _this = this;
        coz.getAddressForCountry(country_id , type, function(data){
            if( type == 'ships' ){
                $('[neo-place="addressShip"]', _this.el).html(data.html);
            }else{
                $('[neo-place="addressPayment"]', _this.el).html(data.html);
            }
            _this.updateEvent();
            _this.getShipping();
        });
    },
    updateEvent: function(){
        _this = this;
        $('[neo-place="addressPayment"] select[name="trans[cities_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).html('');
            $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).html('');
            cities_id = $(this).val();
            if( typeof cities_id != 'undefined' 
                && $.trim(cities_id).length > 0 ){
                coz.loadDistrict( cities_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).html('');
            districts_id = $(this).val();
            if( typeof districts_id != 'undefined' 
                && $.trim(districts_id).length > 0 ){
                coz.loadWard( districts_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });

        $('[neo-place="addressShip"] select[name="ships[cities_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).html('');
            $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).html('');
            cities_id = $(this).val();
            if( typeof cities_id != 'undefined' 
                && $.trim(cities_id).length > 0 ){
                coz.loadDistrict( cities_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).html('');
            districts_id = $(this).val();
            if( typeof districts_id != 'undefined' 
                && $.trim(districts_id).length > 0 ){
                coz.loadWard( districts_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });

        $('select[name="trans[state]"], select[name="trans[region]"], select[name="trans[province]"], select[name="ships[state]"], select[name="ships[region]"], select[name="ships[province]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });

        $('input[name="trans[shipping_id]"], input[name="trans[transport_type]"]', _this.el).off('change').on('change', function(){
            _this.getFeeTransitions();
        });
    },
    getFeeTransitions : function(callback){
        _this = this;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            var country_id = 0;
            var cities_id = 0;
            var districts_id = 0;
            var wards_id = 0;
            var transport_type = 0;
            var shipping_id = 0;
            if( $('input[name="ship_to_different_address"]', _this.el).is(':checked') ){
                if( $('[name="ships[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _this.el).val();
                }
                if($('select[name="ships[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _this.el).val();
                }else if($('select[name="ships[state]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[state]"]', _this.el).val();
                }else if($('select[name="ships[region]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[region]"]', _this.el).val();
                }else if($('select[name="ships[province]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[province]"]', _this.el).val();
                }
                if($('select[name="ships[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _this.el).val();
                }
                if( $('select[name="ships[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _this.el).val();
                }
            }else{
                if( $('[name="trans[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="trans[country_id]"]', _this.el).val();
                }
                if($('select[name="trans[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[cities_id]"]', _this.el).val();
                }else if($('select[name="trans[state]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[state]"]', _this.el).val();
                }else if($('select[name="trans[region]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[region]"]', _this.el).val();
                }else if($('select[name="trans[province]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[province]"]', _this.el).val();
                }
                if($('select[name="trans[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="trans[districts_id]"]', _this.el).val();
                }
                if( $('select[name="trans[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="trans[wards_id]"]', _this.el).val();
                }
            }
            if( $('input[name="trans[transport_type]"]:checked', _this.el).length >0 ){
                transport_type = $('input[name="trans[transport_type]"]:checked', _this.el).val();
            }
            if( $('input[name="trans[shipping_id]"]:checked', _this.el).length >0 ){
                shipping_id = $('input[name="trans[shipping_id]"]:checked', _this.el).val();
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/getFeeTransitions?_AJAX=1',
                data: 'shipping_id='+shipping_id+'&country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&transport_type='+transport_type,
                cache: false,
                success: function(data)
                {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    if(data.flag == true || data.flag == 'true'){
                        _this.updateMoney(data);
                    }
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                    console.log(e);
                }
            });
        }, coz.DELAY);
    },
    getShipping : function(callback){
        _this = this;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            var country_id = 0;
            var cities_id = 0;
            var districts_id = 0;
            var wards_id = 0;
            var transport_type = 0;
            if( $('input[name="ship_to_different_address"]', _this.el).is(':checked') ){
                if( $('[name="ships[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _this.el).val();
                }
                if($('select[name="ships[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _this.el).val();
                }else if($('select[name="ships[state]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[state]"]', _this.el).val();
                }else if($('select[name="ships[region]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[region]"]', _this.el).val();
                }else if($('select[name="ships[province]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[province]"]', _this.el).val();
                }
                if($('select[name="ships[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _this.el).val();
                }
                if( $('select[name="ships[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _this.el).val();
                }
            }else{
                if( $('[name="trans[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="trans[country_id]"]', _this.el).val();
                }
                if($('select[name="trans[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[cities_id]"]', _this.el).val();
                }else if($('select[name="trans[state]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[state]"]', _this.el).val();
                }else if($('select[name="trans[region]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[region]"]', _this.el).val();
                }else if($('select[name="trans[province]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[province]"]', _this.el).val();
                }
                if($('select[name="trans[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="trans[districts_id]"]', _this.el).val();
                }
                if( $('select[name="trans[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="trans[wards_id]"]', _this.el).val();
                }
            }
            if( $('input[name="trans[transport_type]"]:checked', _this.el).length >0 ){
                transport_type = $('input[name="trans[transport_type]"]:checked', _this.el).val();
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/getShipping?_AJAX=1',
                data: 'country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&wards_id='+wards_id+'&transport_type='+transport_type+'&ajax=1',
                cache: false,
                success: function(data)
                {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    if(data.flag == true || data.flag == 'true'){
                        $('[neo-place="Shipping"]').html(data.html);
                    }else{
                        $('[neo-place="Shipping"]').html('<p class="coz-note-payment" >'+data.html+'</div>');
                    }
                    _this.getFeeTransitions();
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                }
            });
        }, coz.DELAY);
    },
    showScreenAddressPayment: function(){
        var _this = this;
        $('.coz-payment-from', _this.el).parent().removeClass('diff-ship');
        $('input[name="ship_to_different_address"]', _this.el).attr({checked : false});
        if(!$('input[name="ship-diff-visual"]', _this.el).is(':checked')){
            $('input[name="ship-diff-visual"]', _this.el).attr('checked', true);
        }
    },
    showScreenShipsAddressPayment: function (){
        var _this = this;
        $('.coz-payment-from', _this.el).parent().addClass('diff-ship');
        $('input#ship-diff-visual', _this.el).attr({checked : true});
        first_name = $('input[name="trans[first_name]"]', _this.el).val();
        last_name = $('input[name="trans[last_name]"]', _this.el).val();
        email = $('input[name="trans[email]"]', _this.el).val();
        phone = $('input[name="trans[phone]"]', _this.el).val();
        var str = '';
        str += 'First Name : <b>'+($.trim(first_name).length > 0 ? first_name : '...')+'</b></br>';
        str += 'Last Name : <b>'+($.trim(last_name).length > 0 ? last_name : '...')+'</b></br>';
        str += 'Email : <b>'+($.trim(email).length > 0 ? email : '...')+'</b></br>';
        str += 'Phone : <b>'+($.trim(phone).length > 0 ? phone : '...')+'</b>';
        $('[data-neo="summaryAddressPayment"]', _this.el).html(str);
        if(!$('input[name="ship_to_different_address"]', _this.el).is(':checked')){
            $('input[name="ship_to_different_address"]', _this.el).attr('checked', true);
        }
    }
};
coz.wholesale.init( $('.coz-wrap-wholesale') );

coz.filter = {
    init: function(){
        $('.neo-trigger-filter').on('change', function(){
            self = $(this);
            neo_url = coz.filter.getFilterUrl($(this));
            neo_domain = coz.filter.getUrlNoParams( window.location.href );
            var formSerialize = $(this).parents('form').eq(0).serialize();
            var urlParams = formSerialize.split('&');
            for( var d=0; d<urlParams.length;d++){
              var iParam = urlParams[d];
              var nameParam = iParam.split('=');
              var name_ = nameParam[0];
              if( nameParam.length > 1
                  && name_ != 'price'
                  && name_ != 'feature'
                  && name_.indexOf('feature') < 0
                  && name_ != 'sort'){
                neo_url += (neo_url.length>0 ? '&' : '') +name_+'='+nameParam[1];
              }
            }
            window.location.href = neo_domain+(neo_url.length>0 ? ('?'+neo_url) : '' );
        });
    },
    getFilterUrl: function( self ){
        neo_form = self.parents('form').eq(0);
        neo_price = neo_form.find('[data-input="price"]');
        neo_feature = neo_form.find('[data-input="feature"]:checked');
        neo_sort = neo_form.find('[data-input="sort"]');
        val_url = '';
        if( neo_price.length>0 
            && $.trim(neo_price.eq(0).val()).length>0 ){
            val_url = 'price='+neo_price.eq(0).val();
        }
        str_feature = '';
        neo_feature.each(function(i, el){
            if( $(this).is(':checked') ){
                str_feature += (str_feature.length>0 ? ';' : '')+$(this).val();
            }
        });
        if( str_feature.length>0 ){
            val_url += (val_url.length>0 ? '&' : '') +'feature='+str_feature;
        }
        if( neo_sort.length>0 
            && $.trim(neo_sort.eq(0).val()).length>0 ){
            val_url += (val_url.length>0 ? '&' : '') +'sort='+neo_sort.eq(0).val();
        }
        return val_url;
    },
    getUrlNoParams: function( url ){
        var index = 0;
        var newURL = url;
        index = url.indexOf('?');
        if(index == -1){
            index = url.indexOf('#');
        }
        if(index != -1){
            newURL = url.substring(0, index);
        }
        return newURL;
    }
};
coz.filter.init();

coz.profile = {
    init: function(){
        _this = this;
        if( $('#profileHistoryChart').length>0 ){
            _this.initProfileInvoiceRepot();
        }
        if( $('#profileAssignsChart').length>0 ){
            _this.getSummaryAssign();
        }
        if( $('[data-input="editAvatarProfile"]').length>0 ){
            $('[data-input="editAvatarProfile"]').html5_upload({
                url: function(number) {
                    return baseUrl +'/profile/avatar?trash=false';
                },
                sendBoundary: window.FormData || $.browser.mozilla,
                onStart: function(event, total) {
                    return true;
                },
                onStartOne: function(event, name, number, total) {
                    return true;
                },
                onProgress: function(event, progress, name, number, total) {
                },
                setName: function(text) {
                },
                setStatus: function(text) {
                },
                setProgress: function(val) {
                },
                onFinishOne: function(event, response, name, number, total) {
                    if(response.constructor === String){
                        response = $.parseJSON(response);
                    }
                    if( typeof response != 'undefined'
                        && ( response.flag == 'true' || response.flag == true ) ){
                        $('[data-place="avatarProfile"]').attr('src', response.avatar);
                    }
                },
                onFinish: function(event, total) {
                },
                onError: function(event, name, error) {
                }
            });
        }
        if( $('[data-form="summaryIndustry"]').length>0 ){
            try{
                $('[data-form="summaryIndustry"] input[name="from"]').datepicker('reset').datepicker('destroy').datepicker({format : 'yyyy-mm-dd'});
                $('[data-form="summaryIndustry"] input[name="to"]').datepicker('reset').datepicker('destroy').datepicker({format : 'yyyy-mm-dd'});
            }catch(e){}
        }

        $(document).on('click', '[data-neo="openFormEditPassword"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('[data-form="editProfile"]').hide();
            $('[data-form="editPassword"]').show();
        });

        $(document).on('click', '[data-neo="openFormEditProfile"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('[data-form="editProfile"]').show();
            $('[data-form="editPassword"]').hide();
        });

        $('[data-form="editProfile"] select[name="country_id"]').select2({ width: '100%' }).on("change", function(e) {
            var country_id = $(this).val();
            coz.getAddressForCountry(country_id , '', function(data){
                $('[data-form="editProfile"] [neo-place="addressProfile"]').html(data.html);

                $('[data-form="editProfile"] select[name="cities_id"]').off('change').on('change', function(){
                    $('[data-form="editProfile"] select[name="districts_id"]').html('');
                    $('[data-form="editProfile"] select[name="wards_id"]').html('');
                    cities_id = $(this).val();
                    if( typeof cities_id != 'undefined' 
                        && $.trim(cities_id).length > 0 ){
                        coz.loadDistrict( cities_id , function( datas ){
                            if( typeof datas.success != 'undefined'
                                &&  (datas.success == 'true' || datas.success == true) ){
                                if(datas.results.length>0){
                                    $.each(datas.results, function(i, row){
                                        $('[data-form="editProfile"] select[name="districts_id"]').append($("<option "+( ($.trim(coz.member.districts_id).length>0 && parseInt(coz.member.districts_id) > 0 && coz.member.districts_id == row.districts_id) ? 'selected' : '')+" ></option>").attr('value', row.districts_id).text(row.districts_title));
                                        if(i>=datas.results.length-1){
                                            $('[data-form="editProfile"] select[name="districts_id"]').trigger('change');
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
                $('[data-form="editProfile"] select[name="districts_id"]').off('change').on('change', function(){
                    $('[data-form="editProfile"] select[name="wards_id"]').html('');
                    districts_id = $(this).val();
                    if( typeof districts_id != 'undefined' 
                        && $.trim(districts_id).length > 0 ){
                        coz.loadWard( districts_id , function( datas ){
                            if( typeof datas.success != 'undefined'
                                &&  (datas.success == 'true' || datas.success == true) ){
                                if(datas.results.length>0){
                                    $.each(datas.results, function(i, row){
                                        $('[data-form="editProfile"] select[name="wards_id"]').append($("<option "+( ($.trim(coz.member.wards_id).length>0 && parseInt(coz.member.wards_id) > 0 && coz.member.wards_id == row.wards_id) ? 'selected' : '')+" ></option>").attr('value', row.wards_id).text(row.wards_title));
                                    });
                                }
                            }
                        });
                    }
                });
                $('[data-form="editProfile"] [name="address"]').val(coz.member.address);
                $('[data-form="editProfile"] [name="city"]').val(coz.member.city);
                $('[data-form="editProfile"] [name="zipcode"]').val(coz.member.zipcode);
                $('[data-form="editProfile"] [name="state"]').val(coz.member.state);
                $('[data-form="editProfile"] [name="suburb"]').val(coz.member.suburb);
                $('[data-form="editProfile"] [name="region"]').val(coz.member.region);
                $('[data-form="editProfile"] [name="province"]').val(coz.member.province);
                if( $.trim(coz.member.cities_id).length>0 
                    && parseInt(coz.member.cities_id) > 0 ){
                    $('[data-form="editProfile"] [name="cities_id"]').val(coz.member.cities_id);
                    $('[data-form="editProfile"] select[name="cities_id"]').trigger('change');
                }
                if( $.trim(coz.member.districts_id).length>0
                    && parseInt(coz.member.districts_id) > 0 ){
                    $('[data-form="editProfile"] [name="districts_id"]').val(coz.member.districts_id);
                }
                if( $.trim(coz.member.wards_id).length>0
                    && parseInt(coz.member.wards_id) > 0 ){
                    $('[data-form="editProfile"] [name="wards_id"]').val(coz.member.wards_id);
                }
            });
        });

        if( $.trim($('[data-form="editProfile"] select[name="country_id"]').val()).length>0 ){
            $('[data-form="editProfile"] select[name="country_id"]').trigger('change');
        }

        if( $('[data-form="editProfile"] input[name="birthday"]').length>0 ){
            $('[data-form="editProfile"] input[name="birthday"]').datepicker('reset').datepicker('destroy').datepicker({format : 'yyyy-mm-dd'});
        }

        try{
            if($('[data-neo="profileMap"]').length>0){
                var latitude = 21.033333;
                var longitude = 105.849998;
                if( typeof $('[data-neo="profileMap"]').attr('data-latitude') != 'undefined'
                    && $('[data-neo="profileMap"]').attr('data-latitude').length >0 ){
                    latitude = $('[data-neo="profileMap"]').attr('data-latitude');
                }
                if( typeof $('[data-neo="profileMap"]').attr('data-longitude') != 'undefined'
                    && $('[data-neo="profileMap"]').attr('data-longitude').length >0 ){
                    longitude = $('[data-neo="profileMap"]').attr('data-longitude');
                }
                var id_map = $('[data-neo="profileMap"]').attr('id');
                if( typeof latitude != 'undefined'  
                    && typeof longitude != 'undefined' ){
                    $('#'+id_map).height(300);
                    map = new GMaps({
                        div: '#'+id_map,
                        lat: latitude,
                        lng: longitude
                    });
                    map.addMarker({
                        lat: latitude,
                        lng: longitude,
                        draggable: ($('[data-form="editProfile"]').length > 0 ? true : false),
                        dragend: function(e) {
                            if( $('[data-form="editProfile"]').length > 0 ){
                                $('[data-form="editProfile"] input[name="latitude"]').val(this.getPosition().lat());
                                $('[data-form="editProfile"] input[name="longitude"]').val(this.getPosition().lng());
                            }
                        }
                    });
                }else{
                    $('#'+id_map).remove();
                }
            }
        }catch(e){console.log(e);};

        $('[data-form="editProfile"]').off('submit').on('submit', function(){
            return coz.profile.checkProfile();
        });
    },
    checkProfile: function(){
        if($.trim($('[data-form="editProfile"] input[name="first_name"]').val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('[data-form="editProfile"] input[name="name="first_name"]').addClass('ui-form-error').focus();
            return false;
        }else{
            $('[data-form="editProfile"] input[name="first_name"]').removeClass('ui-form-error');
        }

        if($.trim($('[data-form="editProfile"] input[name="last_name"]').val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('[data-form="editProfile"] input[name="last_name"]').addClass('ui-form-error').focus();
            return false;
        }else{
            $('[data-form="editProfile"] input[name="last_name"]').removeClass('ui-form-error');
        }

        if($.trim($('[data-form="editProfile"] input[name="phone"]').val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_so_dien_thoai'));
            $('[data-form="editProfile"] input[name="phone"]').addClass('ui-form-error').focus();
            return false;
        }else{
            $('[data-form="editProfile"] input[name="phone"]').removeClass('ui-form-error');
        }

        if($('[data-form="editProfile"] [name="country_id"]').length<=0 
          || $.trim($('[data-form="editProfile"] [name="country_id"]').val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_contry'));
            $('[data-form="editProfile"] [name="country_id"]').addClass('ui-form-error').focus();
            return false;
        }else{
            $('[data-form="editProfile"] [name="country_id"]').removeClass('ui-form-error');
        }

        if($('[data-form="editProfile"] input[name="address"]').length <=0 
            || $.trim($('[data-form="editProfile"] input[name="address"]').val()).length <=0 ){
            coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
            $('[data-form="editProfile"] input[name="address"]').addClass('ui-form-error').focus();
            return false;
        }else{
            $('[data-form="editProfile"] input[name="address"]').removeClass('ui-form-error');
        }

        if($('[data-form="editProfile"] input[name="city"]').length>0){
            if($.trim($('[data-form="editProfile"] input[name="city"]').val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] input[name="city"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] input[name="city"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] input[name="zipcode"]').length>0){
            if($.trim($('[data-form="editProfile"] input[name="zipcode"]').val()).length <=0 ){
                coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] input[name="zipcode"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] input[name="zipcode"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] input[name="state"]').length>0){
            if($.trim($('[data-form="editProfile"] input[name="state"]').val()).length <=0 ){
                coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] input[name="state"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] input[name="state"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] input[name="suburb"]').length>0){
            if($.trim($('[data-form="editProfile"] input[name="suburb"]').val()).length <=0 ){
                coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] input[name="suburb"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] input[name="suburb"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] input[name="region"]').length>0){
            if($.trim($('[data-form="editProfile"] input[name="region"]').val()).length <=0 ){
                coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] input[name="region"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] input[name="region"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] input[name="province"]').length>0){
            if($.trim($('[data-form="editProfile"] input[name="province"]').val()).length <=0 ){
                coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] input[name="province"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] input[name="province"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] select[name="cities_id"]').length>0){
            if($.trim($('[data-form="editProfile"] select[name="cities_id"]').val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] select[name="cities_id"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] select[name="cities_id"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] select[name="districts_id"]').length>0){
            if($.trim($('select[name="districts_id"]').val()).length <=0 ){
                coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] select[name="districts_id"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] select[name="districts_id"]').removeClass('ui-form-error');
            }
        }

        if($('[data-form="editProfile"] select[name="wards_id"]').length>0){
            if($.trim($('select[name="wards_id"]').val()).length <=0 ){
                coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                $('[data-form="editProfile"] select[name="wards_id"]').addClass('ui-form-error').focus();
                return false;
            }else{
                $('[data-form="editProfile"] select[name="wards_id"]').removeClass('ui-form-error');
            }
        }

        return true;
    },
    getSummaryAssign : function(){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: coz.baseUrl + '/assign/summary?_AJAX=1',
            data: null,
            success: function ( res ) {
                if (res.constructor === String) {
                    res = JSON.parse(res);
                }
                if( typeof res != 'undefined'
                        &&  (res.flag == 'true' ||  res.flag == true) ){
                    window.chartColors = {
                        red: 'rgb(255, 99, 132)',
                        orange: 'rgb(255, 159, 64)',
                        yellow: 'rgb(255, 205, 86)',
                        green: 'rgb(75, 192, 192)',
                        blue: 'rgb(54, 162, 235)',
                        purple: 'rgb(153, 102, 255)',
                        grey: 'rgb(201, 203, 207)'
                    };
                    var config = {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: [
                                    res.data.total,
                                    res.data.pending,
                                    res.data.accept,
                                    res.data.cancel,
                                    res.data.finish
                                    //Math.min(Math.round((res.data.pending/res.data.total)*100), 100),
                                    //Math.min(Math.round((res.data.accept/res.data.total)*100), 100),
                                    //Math.min(Math.round((res.data.cancel/res.data.total)*100), 100),
                                    //Math.min(Math.round((res.data.finish/res.data.total)*100), 100)
                                ],
                                backgroundColor: [
                                    window.chartColors.red,
                                    window.chartColors.orange,
                                    window.chartColors.yellow,
                                    window.chartColors.green,
                                    window.chartColors.blue
                                ],
                                label: 'Summary assign'
                            }],
                            labels: [
                                'Total',
                                'Pending',
                                'Accept',
                                'Cancel',
                                'Finish'
                            ]
                        },
                        options: {
                            responsive: true
                        }
                    };
                    var ctx = document.getElementById("profileAssignsChart");
                    window.myPolarArea = Chart.PolarArea(ctx, config);
                }else{
                    $('#profileAssignsChart').remove();
                }
            },
            error : function(){
                $('#profileAssignsChart').remove();
            }
        });
    },
    initProfileInvoiceRepot : function(){
        var today = new Date();
        today.setDate(today.getDate() - 90);
        $.ajax({
            url: coz.baseUrl + '/profile/chartInvoice?_AJAX=1',
            data: 'from='+today.getFullYear()+'-'+today.getMonth()+'-'+today.getDate(),
            success: function (data) {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                var labels = [];
                var invoices_by_day = [];
                if(typeof data.invoices_by_day != 'undefined'
                    && Object.keys(data.invoices_by_day).length >0 ){
                    var keys = Object.keys(data.invoices_by_day);
                    for(i=0;i<=keys.length-1;i++){
                        iday = data.invoices_by_day[keys[i]];
                        labels.push(iday['day']);
                        invoices_by_day.push(iday['subtotal']);
                        if(i>=keys.length-1){
                            var config = {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: "Subtotal",
                                        data: invoices_by_day,
                                        fill: false,
                                        borderDash: [5, 5],
                                        pointRadius: 15,
                                        pointHoverRadius: 10
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    legend: {
                                        position: 'bottom',
                                    },
                                    hover: {
                                        mode: 'label'
                                    },
                                    scales: {
                                        xAxes: [{
                                            display: true,
                                            scaleLabel: {
                                                display: true,
                                                labelString: 'Day'
                                            }
                                        }],
                                        yAxes: [{
                                            display: true,
                                            scaleLabel: {
                                                display: true,
                                                labelString: 'Value'
                                            }
                                        }]
                                    },
                                    title: {
                                        display: true,
                                        text: 'report invoice'
                                    }
                                }
                            };
                            var ctx = document.getElementById("profileHistoryChart").getContext("2d");
                            window.profileInvoiceChart = new Chart(ctx, config);
                        }
                    }
                }
            },
            error: function () {}
        });
    }
};
coz.profile.init();

coz.shippipng = {
    selectTransCountry:null,
    selectShipsCountry:null,
    el:null,
    init: function( el ){
        var _this = coz.shippipng;
        if( el.length > 0){
            _this.el = el;
            _this.selectTransCountry = $('select[name="trans[country_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
                var country_id = $(this).val();
                _this.updateAddress(country_id, 'trans');
            });
            _this.selectShipsCountry = $('select[name="ships[country_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
                var country_id = $(this).val();
                _this.updateAddress(country_id, 'ships');
            });
            $('input[name="trans[first_name]"]', el).off('keyup focus blur').on('keyup focus blur', function(){
                $('input[data-update="first_name"]', el).val( $(this).val() );
            });
            $('input[name="trans[last_name]"]', el).off('keyup focus blur').on('keyup focus blur', function(){
                $('input[data-update="last_name"]', el).val( $(this).val() );
            });
            $('input[name="trans[email]"]', el).off('keyup focus blur').on('keyup focus blur', function(){
                $('input[data-update="email"]', el).val( $(this).val() );
            });
            $('input[name="trans[phone]"]', el).off('keyup focus blur').on('keyup focus blur', function(){
                $('input[data-update="phone"]', el).val( $(this).val() );
            });
            $('input[name="ship_for_you"]', el).off('change').on('change', function(){
                if( $(this).is(':checked') ){
                    $('.ship-for-you').show();
                    $('.ship-for-me').hide();
                    var country_id = $('[name="ships[country_id]"]', el).val();
                    _this.updateAddress(country_id, 'ships');
                }else{
                    $('.ship-for-you').hide();
                    $('.ship-for-me').show();
                    var country_id = $('[name="trans[country_id]"]', el).val();
                    _this.updateAddress(country_id, 'trans');
                }
            });

            if( $.trim($('[name="trans[country_id]"]', el).val()).length>0 ){
                var country_id = $('[name="trans[country_id]"]', el).val();
                _this.updateAddress(country_id, 'trans');
            }

            if( $('input[name="ship_for_you"]', _this.el).is(':checked') ){
                $('.ship-for-you').show();
                $('.ship-for-me').hide();
                if( $.trim($('[name="ships[country_id]"]', el).val()).length>0 ){
                    var country_id = $('[name="ships[country_id]"]', el).val();
                    _this.updateAddress(country_id, 'ships');
                }
            }

            $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).on('focus', function(){
              $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).hide();
            });

            $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).on('blur', function(){
                if($.trim($(this).val()).length <=0 ){
                    $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).show();
                }
            });
            $('input[name="trans[first_name]"], input[name="trans[last_name]"], input[name="trans[email]"], input[name="ships[first_name]"], input[name="ships[last_name]"], input[name="ships[email]"]', _this.el).each(function(i, el){
                if( $.trim($(this).val()).length <= 0 ){
                    $(this).parents('.ui-input-payment').eq(0).find('.ui-placeholder-text-payment').eq(0).show();
                }
            });
            $('[data-form="shipping"]', _this.el).off('submit').on('submit', function(){
                if( _this.checkAddress() ){
                    return true;
                }
                return false;
            });
            $('form[data-form="shipping"]', _this.el).data('no_shipping', true);
        }
    },
    checkAddress: function(){
        _this = coz.shippipng;
        if($.trim($('input[name="trans[first_name]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="trans[first_name]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[first_name]"]', _this.el).removeClass('ui-form-error');
        }

        if($.trim($('input[name="trans[last_name]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="trans[last_name]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[last_name]"]', _this.el).removeClass('ui-form-error');
        }

        if( !coz.isEmail($('input[name="trans[email]"]', _this.el).val()) ){
            coz.toast(language.translate('txt_email_khong_hop_le'));
            $('input[name="trans[email]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[email]"]', _this.el).removeClass('ui-form-error');
        }

        if( !coz.isPhone($('input[name="trans[phone]"]', _this.el).val()) ){
            coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
            $('input[name="trans[phone]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[phone]"]', _this.el).removeClass('ui-form-error');
        }

        if($('[name="ships[country_id]"]', _this.el).length<=0 
          || $.trim($('[name="trans[country_id]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_contry'));
            $('[name="trans[country_id]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('[name="trans[country_id]"]', _this.el).removeClass('ui-form-error');
        }

        if($('input[name="trans[address]"]', _this.el).length <=0 
            || $.trim($('input[name="trans[address]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
            $('input[name="trans[address]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[address]"]', _this.el).removeClass('ui-form-error');
        }

        if($('input[name="trans[city]"]', _this.el).length>0){
            if($.trim($('input[name="trans[city]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('input[name="trans[city]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[city]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[zipcode]"]', _this.el).length>0){
            if($.trim($('input[name="trans[zipcode]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                $('input[name="trans[zipcode]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[zipcode]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[state]"]', _this.el).length>0){
            if($.trim($('input[name="trans[state]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                $('input[name="trans[state]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[state]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[suburb]"]', _this.el).length>0){
            if($.trim($('input[name="trans[suburb]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                $('input[name="trans[suburb]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[suburb]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[region]"]', _this.el).length>0){
            if($.trim($('input[name="trans[region]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                $('input[name="trans[region]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[region]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('input[name="trans[province]"]', _this.el).length>0){
            if($.trim($('input[name="trans[province]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                $('input[name="trans[province]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[province]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[cities_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[cities_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('select[name="trans[cities_id]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="trans[cities_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[districts_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[districts_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                $('select[name="trans[districts_id]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="trans[districts_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if($('select[name="trans[wards_id]"]', _this.el).length>0){
            if($.trim($('select[name="trans[wards_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                $('select[name="trans[wards_id]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="trans[wards_id]"]', _this.el).removeClass('ui-form-error');
            }
        }

        if( $('input[name="ship_for_you"]', _this.el).is(':checked') ){
            if($.trim($('input[name="ships[first_name]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_ten'));
                $('input[name="ships[first_name]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="ships[first_name]"]', _this.el).removeClass('ui-form-error');
            }

            if($.trim($('input[name="ships[last_name]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_ten'));
                $('input[name="ships[last_name]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="ships[last_name]"]', _this.el).removeClass('ui-form-error');
            }

            if( !coz.isEmail($('input[name="ships[email]"]', _this.el).val()) ){
                coz.toast(language.translate('txt_email_khong_hop_le'));
                $('input[name="ships[email]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="ships[email]"]', _this.el).removeClass('ui-form-error');
            }

            if( !coz.isPhone($('input[name="ships[phone]"]', _this.el).val()) ){
                coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
                $('input[name="ships[phone]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="ships[phone]"]', _this.el).removeClass('ui-form-error');
            }

            if($('[name="ships[country_id]"]', _this.el).length<=0 
              || $.trim($('[name="ships[country_id]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_chon_contry'));
                $('[name="ships[country_id]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('[name="ships[country_id]"]', _this.el).removeClass('ui-form-error');
            }

            if($.trim($('input[name="ships[address]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
                $('input[name="ships[address]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="ships[address]"]', _this.el).removeClass('ui-form-error');
            }

            if($('input[name="ships[city]"]', _this.el).length>0){
                if($.trim($('input[name="ships[city]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                    $('input[name="ships[city]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('input[name="ships[city]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[zipcode]"]', _this.el).length>0){
                if($.trim($('input[name="ships[zipcode]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                    $('input[name="ships[zipcode]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('input[name="ships[zipcode]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[state]"]', _this.el).length>0){
                if($.trim($('input[name="ships[state]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                    $('input[name="ships[state]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('input[name="ships[state]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[suburb]"]', _this.el).length>0){
                if($.trim($('input[name="ships[suburb]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                    $('input[name="ships[suburb]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('input[name="ships[suburb]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[region]"]', _this.el).length>0){
                if($.trim($('input[name="ships[region]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                    $('input[name="ships[region]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('input[name="ships[region]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('input[name="ships[province]"]', _this.el).length>0){
                if($.trim($('input[name="ships[province]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                    $('input[name="ships[province]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('input[name="ships[province]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[cities_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[cities_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                    $('select[name="ships[cities_id]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('select[name="ships[cities_id]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[districts_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[districts_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                    $('select[name="ships[districts_id]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('select[name="ships[districts_id]"]', _this.el).removeClass('ui-form-error');
                }
            }

            if($('select[name="ships[wards_id]"]', _this.el).length>0){
                if($.trim($('select[name="ships[wards_id]"]', _this.el).val()).length <=0 ){
                    coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                    $('select[name="ships[wards_id]"]', _this.el).addClass('ui-form-error').focus();
                    return false;
                }else{
                    $('select[name="ships[wards_id]"]', _this.el).removeClass('ui-form-error');
                }
            }
        }
        no_shipping = $('form[data-form="shipping"]', _this.el).data('no_shipping');
        if( no_shipping == 'true' || no_shipping == true ){
            coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
            return false;
        }
        return true;
    },
    updateEvent: function(){
        _this = coz.shippipng;
        $('[neo-place="addressPreviewShips"] input[name="trans[address]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'address');
        $('[neo-place="addressPreviewShips"] input[name="trans[address01]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'address01');
        $('[neo-place="addressPreviewShips"] input[name="trans[city]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'city');
        $('[neo-place="addressPreviewShips"] input[name="trans[zipcode]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'zipcode');
        $('[neo-place="addressPreviewShips"] input[name="trans[state]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'state');
        $('[neo-place="addressPreviewShips"] select[name="trans[state]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'state');
        $('[neo-place="addressPreviewShips"] input[name="trans[suburb]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'suburb');
        $('[neo-place="addressPreviewShips"] select[name="trans[region]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'region');
        $('[neo-place="addressPreviewShips"] select[name="trans[province]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'province');
        $('[neo-place="addressPreviewShips"] select[name="trans[cities_id]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'cities_id');
        $('[neo-place="addressPreviewShips"] select[name="trans[districts_id]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'districts_id');
        $('[neo-place="addressPreviewShips"] select[name="trans[wards_id]"]', _this.el).attr('disabled','disabled').removeAttr('name').removeAttr('data-input').removeAttr('id').attr('data-update', 'wards_id');

        $('[neo-place="addressPayment"] input[name="trans[address]"]', _this.el).off('keyup focus blur').on('keyup focus blur', function(){
            $('[neo-place="addressPreviewShips"] input[data-update="address"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] input[name="trans[address01]"]', _this.el).off('keyup focus blur').on('keyup focus blur', function(){
            $('[neo-place="addressPreviewShips"] input[data-update="address01"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] input[name="trans[city]"]', _this.el).off('keyup focus blur').on('keyup focus blur', function(){
            $('[neo-place="addressPreviewShips"] input[data-update="city"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] input[name="trans[zipcode]"]', _this.el).off('keyup focus blur').on('keyup focus blur', function(){
            $('[neo-place="addressPreviewShips"] input[data-update="zipcode"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] input[name="trans[state]"]', _this.el).off('keyup focus blur').on('keyup focus blur', function(){
            $('[neo-place="addressPreviewShips"] input[data-update="state"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] select[name="trans[state]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPreviewShips"] select[data-update="state"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] input[name="trans[suburb]"]', _this.el).off('keyup focus blur').on('keyup focus blur', function(){
            $('[neo-place="addressPreviewShips"] input[data-update="suburb"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] select[name="trans[region]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPreviewShips"] select[data-update="region"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] select[name="trans[province]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPreviewShips"] select[data-update="province"]', _this.el).val( $(this).val() );
        });
        $('[neo-place="addressPayment"] select[name="trans[cities_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPreviewShips"] select[data-update="cities_id"]', _this.el).val( $(this).val() );
            $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).html('');
            $('[neo-place="addressPreviewShips"] select[data-update="districts_id"]', _this.el).html('');
            $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).html('');
            $('[neo-place="addressPreviewShips"] select[data-update="wards_id"]', _this.el).html('');
            cities_id = $(this).val();
            if( typeof cities_id != 'undefined' 
                && $.trim(cities_id).length > 0 ){
                coz.loadDistrict( cities_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                $('[neo-place="addressPreviewShips"] select[data-update="districts_id"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });
        $('[neo-place="addressPayment"] select[name="trans[districts_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPreviewShips"] select[data-update="districts_id"]', _this.el).val( $(this).val() );
            $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).html('');
            $('[neo-place="addressPreviewShips"] select[data-update="wards_id"]', _this.el).html('');
            districts_id = $(this).val();
            if( typeof districts_id != 'undefined' 
                && $.trim(districts_id).length > 0 ){
                coz.loadWard( districts_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                $('[neo-place="addressPreviewShips"] select[data-update="wards_id"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });
        $('[neo-place="addressPayment"] select[name="trans[wards_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressPreviewShips"] select[data-update="wards_id"]', _this.el).val( $(this).val() );
            _this.getShipping();
        });

        $('[neo-place="addressShip"] select[name="ships[cities_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).html('');
            $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).html('');
            cities_id = $(this).val();
            if( typeof cities_id != 'undefined' 
                && $.trim(cities_id).length > 0 ){
                coz.loadDistrict( cities_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressShip"] select[name="ships[districts_id]"]', _this.el).off('change').on('change', function(){
            $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).html('');
            districts_id = $(this).val();
            if( typeof districts_id != 'undefined' 
                && $.trim(districts_id).length > 0 ){
                coz.loadWard( districts_id , function( datas ){
                    if( typeof datas.success != 'undefined'
                        &&  (datas.success == 'true' || datas.success == true) ){
                        if(datas.results.length>0){
                            $.each(datas.results, function(i, row){
                                $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                                if(i>=datas.results.length-1){
                                    $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).trigger('change');
                                }
                            });
                        }else{
                            _this.getShipping();
                        }
                    }else{
                        _this.getShipping();
                    }
                });
            }else{
                _this.getShipping();
            }
        });

        $('[neo-place="addressShip"] select[name="ships[wards_id]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });
        $('[neo-place="addressPayment"] select[name="trans[state]"], [neo-place="addressPayment"] select[name="trans[region]"], [neo-place="addressPayment"] select[name="trans[province]"], [neo-place="addressShip"] select[name="ships[state]"], [neo-place="addressShip"] select[name="ships[region]"], [neo-place="addressShip"] select[name="ships[province]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });
    },
    updateAddress : function( country_id, type ){
        _this = coz.shippipng;
        $('select[data-update="country_id"]', _this.el).val( _this.selectTransCountry.val() );
        coz.getAddressForCountry(country_id , type, function(data){
            _this = coz.shippipng;
            if( type == 'ships' ){
                $('[neo-place="addressShip"]', _this.el).html(data.html);
            }else{
                $('[neo-place="addressPayment"]', _this.el).html(data.html);
                $('[neo-place="addressPreviewShips"]', _this.el).html(data.html);
            }
            _this.updateEvent();
            _this.getShipping();
        });
    },
    getShipping : function(callback){
        _this = coz.shippipng;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            var country_id = 0;
            var cities_id = 0;
            var districts_id = 0;
            var wards_id = 0;
            var transport_type = 0;
            if( $('input[name="ship_for_you"]', _this.el).is(':checked') ){
                if( $('[name="ships[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _this.el).val();
                }
                if($('select[name="ships[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _this.el).val();
                }else if($('select[name="ships[state]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[state]"]', _this.el).val();
                }else if($('select[name="ships[region]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[region]"]', _this.el).val();
                }else if($('select[name="ships[province]"]', _this.el).length>0){
                    cities_id = $('select[name="ships[province]"]', _this.el).val();
                }
                if($('select[name="ships[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _this.el).val();
                }
                if( $('select[name="ships[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _this.el).val();
                }
            }else{
                if( $('[name="trans[country_id]"]', _this.el).length > 0){
                    country_id = $('[name="trans[country_id]"]', _this.el).val();
                }
                if($('select[name="trans[cities_id]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[cities_id]"]', _this.el).val();
                }else if($('select[name="trans[state]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[state]"]', _this.el).val();
                }else if($('select[name="trans[region]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[region]"]', _this.el).val();
                }else if($('select[name="trans[province]"]', _this.el).length>0){
                    cities_id = $('select[name="trans[province]"]', _this.el).val();
                }
                if($('select[name="trans[districts_id]"]', _this.el).length>0){
                    districts_id = $('select[name="trans[districts_id]"]', _this.el).val();
                }
                if( $('select[name="trans[wards_id]"]', _this.el).length > 0){
                    wards_id = $('select[name="trans[wards_id]"]', _this.el).val();
                }
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/hasShipping?_AJAX=1',
                data: 'country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&wards_id='+wards_id+'&transport_type='+transport_type+'&ajax=1',
                cache: false,
                success: function(data)
                {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    $('form[data-form="shipping"]', _this.el).data('no_shipping', true);
                    if( data.flag == 'true'
                        || data.flag == true ){
                        $('form[data-form="shipping"]', _this.el).data('no_shipping', data.no_shipping);
                        if( data.no_shipping == 'true' || data.no_shipping == true ){
                            coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
                        }
                    }
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                }
            });
        }, coz.DELAY);
    }
};
coz.shippipng.init( $('.coz-wrap-shiping-02') );


coz.payment02 = {
    country_id: 0,
    cities_id: 0,
    state: 0,
    region: 0,
    province: 0,
    zipcode: 0,
    districts_id: 0,
    wards_id: 0,
    data: [],
    selectPayment:null,
    el:null,
    init: function( el ){
        var _this = this;
        if( typeof el != 'undefined' 
            && el.length > 0){
            _this.el = el;
            _this.data = el.data();
            _this.country_id = $('form[data-form="payment"]', _this.el).data('country_id');
            _this.cities_id = $('form[data-form="payment"]', _this.el).data('cities_id');
            _this.state = $('form[data-form="payment"]', _this.el).data('state');
            _this.region = $('form[data-form="payment"]', _this.el).data('region');
            _this.province = $('form[data-form="payment"]', _this.el).data('province');
            _this.zipcode = $('form[data-form="payment"]', _this.el).data('zipcode');
            _this.districts_id = $('form[data-form="payment"]', _this.el).data('districts_id');
            _this.wards_id = $('form[data-form="payment"]', _this.el).data('wards_id');

            _this.selectPayment = $('select[name="trans[payment_id]"]', el).select2({ width: '100%' }).on("change", function(e) {
                var payment_id = $(this).val();
                $('.coz-content-tab-payment', _this.el).hide();
                $('.coz-content-tab-payment.active', _this.el).removeClass('active');
                $('#'+payment_id, _this.el).show().addClass('active');
            });

            $('select[name="trans[avs_country]"]', el).select2({ width: '100%' }).on("change", function(e) {
            });

            $('.coz-link-more-payment', _this.el).on('click', function(e){
                var ctPainPay = $(this).parents('.coz-pai-in-more-payment').eq(0).find('.coz-ctpai-in-more-payment').eq(0);
                if( ctPainPay.hasClass('active') ){
                    $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> '+language.translate('txt_xem_them_thong_tin_tax_payment'));
                    ctPainPay.removeClass('active');
                }else{
                    $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> ' +language.translate('txt_an_thong_tin_tax_payment'));
                    ctPainPay.addClass('active');
                }
            });

            _this.getShipping();

            $('form[data-form="payment"]', _this.el).on('submit', function(e){
                if( _this.checkForm() ){
                    coz.showLoading();
                    return true;
                }
                return false;
            });

            $('[neo-btn="btnEditCounpon"]', _this.el).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                $('[data-input="coupon"]', _this.el).val($.trim($('[data-neo="currentCoupon"]', _this.el).html()));
                $('#coz-form-coupon', _this.el).show();
                $('#coz-value-coupon', _this.el).hide();
            });

            $('[neo-btn="btnAppCounpon"]', _this.el).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if( $('input[data-input="coupon"]', _this.el).length == 1 
                    && $.trim($('[data-input="coupon"]', _this.el).val()).length >0 ){
                    clearInterval(coz.INTERVAL);
                    clearTimeout(coz.INTERVAL);
                    coz.INTERVAL = setTimeout( function(){
                        var coupon = $('[data-input="coupon"]', _this.el).val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: coz.baseUrl+'/cart/useCoupon?_AJAX=1',
                            data: {coupon:coupon},
                            cache: false,
                            success: function(data)
                            {
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                coz.toast(data.msg);
                                if( data.flag == true || data.flag == 'true'){
                                    $('[data-input="coupon"]', _this.el).val('');
                                    $('[data-neo="currentCoupon"]', _this.el).html(data.coupon.coupons_code);
                                    $('#coz-form-coupon', _this.el).hide();
                                    $('#coz-value-coupon', _this.el).show();
                                    $('#coz-tr-coupon-payment', _this.el).show();
                                    _this.updateMoney(data);
                                }else{
                                    $('[data-input="coupon"]', _this.el).eq(0).focus();
                                }
                            },
                            error: function(e)
                            {
                                console.log(e);
                            }
                        });
                    }, coz.DELAY);
                }else{
                  coz.toast(language.translate('txt_ban_chua_nhap_coupon'));
                  $('[data-input="coupon"]', _this.el).eq(0).focus();
                }
            });

            $('[neo-btn="btnRemoveCounpon"]', _this.el).on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                clearInterval(coz.INTERVAL);
                clearTimeout(coz.INTERVAL);
                coz.INTERVAL = setTimeout( function(){
                    var r = confirm(language.translate('txt_ban_muon_xoa_coupon'));
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: coz.baseUrl+'/cart/removeCoupon?_AJAX=1',
                            data: null,
                            cache: false,
                            success: function(data)
                            {
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                coz.toast(data.msg);
                                if( data.flag == true || data.flag == 'true'){
                                    $('[data-input="coupon"]', _this.el).val('');
                                    $('[data-neo="currentCoupon"]', _this.el).html('');
                                    $('#coz-form-coupon', _this.el).show();
                                    $('#coz-value-coupon', _this.el).hide();
                                    $('#coz-tr-coupon-payment', _this.el).hide();
                                    _this.updateMoney(data);
                                }
                            },
                            error: function(e)
                            {
                              console.log(e);
                            }
                        });
                    }
                }, coz.DELAY);
            });
        }
    },
    checkForm: function(){
        _this = this;
        if($('input[name="trans[shipping_id]"]:checked', _this.el).length<=0 
            || $.trim($('input[name="trans[shipping_id]"]:checked', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_transportation'));
            $('input[name="trans[shipping_id]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[shipping_id]"]', _this.el).removeClass('ui-form-error');
        }

        if($.trim($('select[name="trans[payment_id]"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_payment_method'));
            $('select[name="trans[payment_id]"]', _this.el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('select[name="trans[payment_id]"]', _this.el).removeClass('ui-form-error');
        }

        if( $('[data-place="Onepay"]', _this.el).hasClass('active') ){
            if( $('select[name="trans[avs_country]"]', _this.el).length>0 
                && $.trim($('select[name="trans[avs_country]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_chon_dat_nuoc_phat_hanh_the'));
                $('select[name="trans[avs_country]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="trans[avs_country]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_street01]"]', _this.el).length>0 
                && $.trim($('input[name="trans[avs_street01]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_dia_chi_phat_hanh_the'));
                $('input[name="trans[avs_street01]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_street01]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_city]"]', _this.el).length>0 
                &&  $.trim($('input[name="trans[avs_city]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_thanh_pho_phat_hanh_the'));
                $('input[name="trans[avs_city]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_city]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_stateprov]"]', _this.el).length>0 
                && $.trim($('input[name="trans[avs_stateprov]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_quan_huyen_phat_hanh_the'));
                $('input[name="trans[avs_stateprov]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_stateprov]"]', _this.el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_postCode]"]', _this.el).length>0 
                && $.trim($('input[name="trans[avs_postCode]"]', _this.el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_ma_vung_phat_hanh_the'));
                $('input[name="trans[avs_postCode]"]', _this.el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_postCode]"]', _this.el).removeClass('ui-form-error');
            }
        }
        return true;
    },
    getCitiesId : function(){
        _this = this;
        if( $.trim(_this.cities_id).length > 0 ){
            return _this.cities_id;
        }else if( $.trim(_this.state).length > 0 ){
            return _this.state;
        }else if( $.trim(_this.region).length > 0 ){
            return _this.region;
        }else if( $.trim(_this.province).length > 0 ){
            return _this.province;
        }
    },
    updateEvent : function(){
        _this = this;
        $('input[name="trans[shipping_id]"], input[name="trans[transport_type]"]', _this.el).off('change').on('change', function(){
            _this.getShipping();
        });
    },
    getShipping : function(callback){
        _this = this;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            _this = coz.payment02;
            var country_id = _this.country_id;
            var cities_id = _this.getCitiesId();
            var districts_id = _this.districts_id;
            var wards_id = _this.wards_id;
            var transport_type = 0;
            if( $('input[name="trans[transport_type]"]:checked', _this.el).length >0 ){
                transport_type = $('input[name="trans[transport_type]"]:checked', _this.el).val();
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/getShipping?_AJAX=1',
                data: 'country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&wards_id='+wards_id+'&transport_type='+transport_type+'&ajax=1',
                cache: false,
                success: function(data)
                {
                    _this = coz.payment02;
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    if(data.flag == true || data.flag == 'true'){
                        $('[neo-place="Shipping"]', _this.el).html(data.html);
                    }else{
                        $('[neo-place="Shipping"]', _this.el).html('<p class="coz-note-payment" >'+data.html+'</div>');
                    }
                    _this.updateEvent();
                    _this.getFeeTransitions();
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                }
            });
        }, coz.DELAY);
    },
    getFeeTransitions : function(callback){
        _this = this;
        clearInterval(coz.INTERVAL);
        clearTimeout(coz.INTERVAL);
        coz.INTERVAL = setTimeout( function(){
            _this = coz.payment02;
            var country_id = _this.country_id;
            var cities_id = _this.getCitiesId();
            var districts_id = _this.districts_id;
            var wards_id = _this.wards_id;
            var transport_type = 0;
            var shipping_id = 0;
            if( $('input[name="trans[transport_type]"]:checked', _this.el).length >0 ){
                transport_type = $('input[name="trans[transport_type]"]:checked', _this.el).val();
            }
            if( $('input[name="trans[shipping_id]"]:checked', _this.el).length >0 ){
                shipping_id = $('input[name="trans[shipping_id]"]:checked', _this.el).val();
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: coz.baseUrl+'/cart/getFeeTransitions?_AJAX=1',
                data: 'shipping_id='+shipping_id+'&country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&transport_type='+transport_type,
                cache: false,
                success: function(data)
                {
                    _this = coz.payment02;
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    if(data.flag == true || data.flag == 'true'){
                        _this.updateMoney(data);
                    }
                    if( typeof callback == 'function'){
                        callback(data);
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                    console.log(e);
                }
            });
        }, coz.DELAY);
    },
    updateMoney : function( data ){
        $('[data-neo="valueCoupon"]', _this.el).html('');
        $('[neo-place="valueCouponPayment"]', _this.el).html('');
        $('[neo-place="valueFeeTransitions"]', _this.el).html(data.fee_currency);
        $('[neo-place="valueSubTotalPayment"]', _this.el).html(data.subtotal_orig_currency);
        $('[neo-place="valueTotalPayment"]', _this.el).html(data.total_currency);
        $('[neo-place="valueTotalTaxPayment"]', _this.el).html(data.total_tax_currency);
    }
}
coz.payment02.init( $('.coz-wrap-payment-02') );

coz.facebook = {
    response : {},
    sync : function(){
        _this = this;
        coz.syncFacebook();
        coz.facebook.loginFacebook(_this.response);
        coz.menuMega.hide();
    },
    syncOut: function(){
        coz.notSyncFacebook();
        coz.logout();
    },
    getUser: function(){
        _this = this;
        FB.api('/me?fields=id,name,email,birthday,cover,currency,first_name,gender,last_name,link,locale,middle_name,name_format,timezone,website', function(response) {
            coz.facebook.response = response;
            coz.facebook.loginFacebook(response);
        });
    },
    login : function(){
        _this = this;
        FB.login(function(response) {
            if ( response.authResponse ) {
                _this.getUser();
            }
        }, {scope: 'email,public_profile', return_scopes: true});
    },
    getLoginStatus : function(){
        _this = this;
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                coz.syncFacebook();
                coz.facebook.getUser();
            } else {}
        }, true);
    },
    loginFacebook: function( response ){
        if( !coz.hasLogin() ){
            if( !coz.hasSyncFaceBook() ){
                if( coz.hasQuestionSyncFaceBook() ){
                    $.iGrowl({
                            type: 'notice',
                            title: 'Bạn có muôn login facebook',
                            message: 'Chúng tôi thấy bạn trên facebook, bạn có muốn kết nối với chúng tôi ? <br/> Hãy nhấn <a href="javascript:void(0);" class="coz-link-btn" onclick="coz.facebook.sync()" >kết nối</a> nếu bạn muốn.Nhấn nút <a href="javascript:void(0);" class="coz-link-btn" onclick="coz.notSyncFacebook()" >tắt</a> nếu bạn thấy bị làm phiền',
                            icon: 'feather-cog',
                            small: false,
                            animation: true,
                            delay: 10000000,
                            image: {
                                src: response.cover.source,
                                class: 'example-image'
                            }
                        });
                }
                coz.menuMega.addMenu( 'coz.facebook.sync()' , language.translate('txt_ket_noi_facebook'), '<i class="fa fa-facebook" aria-hidden="true"></i>' );
                coz.menuMega.addMenu( 'coz.notSyncFacebook()' , language.translate('txt_tat_tu_dong_ket_noi_facebook'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
            }else{
                coz.menuMega.addMenu( 'coz.facebook.syncOut()' , language.translate('txt_thoat_tai_khoan_facebook'), '<i class="fa fa-sign-out" aria-hidden="true"></i>' );
                coz.menuMega.addMenu( 'coz.notSyncFacebook()' , language.translate('txt_tat_tu_dong_ket_noi_facebook'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
                var data = {
                    email : response.email,
                    first_name : response.first_name,
                    last_name : response.last_name,
                    gender : response.gender,
                    id : response.id,
                    link : response.link,
                    locale : response.locale,
                    name : response.name,
                    name_format : response.name_format,
                    timezone : response.timezone,
                    currency : response.currency.user_currency,
                    cover : response.cover.source
                };
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/facebook/login?_AJAX=1',
                    data: data,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        if( data.flag == 'true' 
                            || data.flag == true){
                            coz.member = data.member;
                            window.location.reload(true);
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }
        }else{
            coz.menuMega.addMenu( 'coz.facebook.syncOut()' , language.translate('txt_thoat_tai_khoan_facebook'), '<i class="fa fa-sign-out" aria-hidden="true"></i>' );
            coz.menuMega.addMenu( 'coz.notSyncFacebook()' , language.translate('txt_tat_tu_dong_ket_noi_facebook'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
        }
    },
    detectionFB : function( callback ){
        _this = this;
        (function(obj, callback){
            var INTERVALDISMITFB = null;
            var INTERVALFB = null;
            var FBERROR = false;
            var FBLoaded = false;
            var slideCheckFB = function(){
                clearInterval(INTERVALFB);
                clearTimeout(INTERVALFB);
                if( !FBLoaded && !FBERROR ){
                    INTERVALFB = setTimeout( function(){
                        if( typeof FB != 'undefined' 
                            && FB != null ){
                            if( !FBLoaded ){
                                FB.getLoginStatus(function(response) {
                                    clearInterval(INTERVALFB);
                                    clearTimeout(INTERVALFB);
                                    if( !FBLoaded ){
                                        callback( response );
                                    }
                                    FBLoaded = true;
                                });
                            }
                        }
                        slideCheckFB();
                    }, 50);
                }
            }
            slideCheckFB();
            var TIMECHECK = 0;
            dismitIntervalDB = function(){
                clearInterval(INTERVALDISMITFB);
                clearTimeout(INTERVALDISMITFB);
                TIMECHECK = parseInt(TIMECHECK) + 300;
                if( TIMECHECK <= 5000 ){
                    if( !FBLoaded ){
                        if( coz.winloaded ){
                            FBERROR = true;
                        }else{
                            INTERVALDISMITFB = setTimeout( function(){
                                dismitIntervalDB();
                            }, 300);
                        }
                    }
                }else{
                    FBERROR = true;
                }
            }
            dismitIntervalDB();
        })(_this, callback);
    },
    init : function(){
        _this = this;
        if( typeof coz.facebook_id != 'undefined' 
            && $.trim(coz.facebook_id).length > 0 ){
            if( !coz.hasLogin() ){
                coz.facebook.detectionFB( function( response ){
                    if (response.status === 'connected') {
                        coz.facebook.getUser();
                    } else {}
                    FB.Event.subscribe('auth.login', function(response) {
                        console.log('auth.login');
                    });
                });
            }else if(   coz.isLoginFacebook() ){
                coz.menuMega.addMenu( 'coz.facebook.syncOut()' , language.translate('txt_thoat_tai_khoan_facebook'), '<i class="fa fa-sign-out" aria-hidden="true"></i>' );
                coz.menuMega.addMenu( 'coz.notSyncFacebook()' , language.translate('txt_tat_tu_dong_ket_noi_facebook'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
            }
        }
    }
};
coz.facebook.init();

var googleUser = {};
coz.google = {
    el : null,
    googleUser : {},
    sync : function(){
        coz.syncGoogle();
        coz.google.getBasicProfile( coz.google.googleUser );
        coz.menuMega.hide();
    },
    syncOut: function(){
        coz.notSyncGoogle();
        coz.logout();
    },
    customBtn : function(){
        _this = this;
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.currentUser.listen( function( googleUser ){
            if( !coz.hasLogin() ){
                if( googleUser.isSignedIn() ){
                    coz.google.googleUser = googleUser;
                    coz.google.getBasicProfile(googleUser);
                }else{}
            }else{
                coz.menuMega.addMenu( 'coz.google.syncOut()' , language.translate('txt_thoat_tai_khoan_google'), '<i class="fa fa-sign-out" aria-hidden="true"></i>' );
                coz.menuMega.addMenu( 'coz.notSyncGoogle()' , language.translate('txt_tat_tu_dong_ket_noi_google'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
            }
        });
        _this.el.each(function( i, el ){
            var id = 'coz-login-google-' + new Date().getTime();
            $(el).attr('id', id);
            auth2.attachClickHandler( document.getElementById(id) , {},
            function( googleUser ) {
                if( !coz.hasLogin() && googleUser.isSignedIn() ){
                    coz.syncGoogle();
                }
            }, function(error) {});
        });
    },
    getBasicProfile : function( googleUser ){
        if( googleUser.isSignedIn()
            && !coz.hasLogin() ){
            var profile = googleUser.getBasicProfile();
            if( !coz.hasSyncGoogle() ){
                if( coz.hasQuestionSyncGoogle() ){
                    $.iGrowl({
                            type: 'notice',
                            title: 'Bạn có muôn login google',
                            message: 'Chúng tôi thấy bạn trên google, bạn có muốn kết nối với chúng tôi ? <br/> Hãy nhấn <a href="javascript:void(0);" class="coz-link-btn" onclick="coz.google.sync()" >kết nối</a> nếu bạn muốn.Nhấn nút <a href="javascript:void(0);" class="coz-link-btn" onclick="coz.notSyncGoogle()" >tắt</a> nếu bạn thấy bị làm phiền',
                            icon: 'feather-cog',
                            small: false,
                            animation: true,
                            delay: 10000000,
                            image: {
                                src: profile.getImageUrl(),
                                class: 'example-image'
                            }
                        });
                }
                coz.menuMega.addMenu( 'coz.google.sync()' , language.translate('txt_ket_noi_google'), '<i class="fa fa-google" aria-hidden="true"></i>' );
                coz.menuMega.addMenu( 'coz.notSyncGoogle()' , language.translate('txt_tat_tu_dong_ket_noi_google'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
            }else{
                coz.menuMega.addMenu( 'coz.google.syncOut()' , language.translate('txt_thoat_tai_khoan_google'), '<i class="fa fa-sign-out" aria-hidden="true"></i>' );
                coz.menuMega.addMenu( 'coz.notSyncGoogle()' , language.translate('txt_tat_tu_dong_ket_noi_google'), '<i class="fa fa-toggle-off" aria-hidden="true"></i>' );
                var data = {
                    email : profile.getEmail(),
                    first_name : profile.getGivenName(),
                    last_name : profile.getFamilyName(),
                    name : profile.getName(),
                    id : profile.getId(),
                    cover : profile.getImageUrl()
                };
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/google/login?_AJAX=1',
                    data: data,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        if( data.flag == 'true' 
                            || data.flag == true){
                            coz.member = data.member;
                            window.location.reload(true);
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }
        }else{}
    },
    onSignIn : function( googleUser ){
        var profile = googleUser.getBasicProfile();
    },
    onSignOut : function(){
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut().then(function () {
            console.log('User signed out.');
        });
    },
    onFailure : function(){
        console.log('onFailure');
    },
    init : function( el ){
        _this = coz.google;
        _this.el = el;
        if( typeof coz.google_client_id != 'undefined' 
            && $.trim(coz.google_client_id).length > 0 ){
            gapi.load('auth2', function(){
                auth2 = gapi.auth2.init({
                    client_id: coz.google_client_id,
                    cookiepolicy: 'single_host_origin'
                });
                coz.google.customBtn();
            });
        }
    }
}
coz.google.init( $('[data-btn="loginGoogle"]') );

coz.twitter = {
    init : function(){
        _this = this;
        twttr.anywhere(function (T) {
            T.signIn();
            T.bind("authComplete", function (e, user) {  
                var token = user.attributes._identity;  
                console.log(user);
            });
        });  
    },
    signIn : function(){
         _this = this;
        twttr.anywhere(function (T) {  
            T.signIn();  
        });
    },
    signOut : function(){
         _this = this;
        twttr.anywhere(function (T) {  
            T.signOut();  
        });
    }
};

coz.cart = {};
coz.cart.products = {};
coz.cart.auth = {
    el:null,
    init: function( el ){
        var _this = this;
        if( typeof el != 'undefined'
            && el.length > 0 ){
            _this.el = el;
            $('form[data-form="login"]', _this.el).off('submit').on('submit', function(e){
                if( _this.checkForm() ){
                    var formdata = $('form[data-form="login"]', _this.el).serialize();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: coz.baseUrl+'/login/loginAjack?_AJAX=1',
                        data: formdata,
                        cache: false,
                        success: function(data)
                        {
                            if(data.constructor === String){
                                data = $.parseJSON(data);
                            }
                            coz.toast(data.html);
                            if( data.flag == 'true' 
                                || data.flag == true){
                                window.location.href = coz.baseUrl + '/checkout/shipping';
                            }
                        },
                        error: function(e)
                        {
                            console.log(e);
                        }
                    });
                }
                return false;
            });

            $('[data-btn="loginFacebook"]', _this.el).off('click').on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                coz.syncFacebook();
                coz.facebook.login();
                return false;
            });

            $('[data-btn="loginTwister"]', _this.el).off('click').on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                console.log('login loginTwister');
                return false;
            });
        }
    },
    checkForm: function(){
        _this = this;
        if($.trim($('form[data-form="login"] input[name="email"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_username'));
            $('form[data-form="login"] input[name="email"]', _this.el).focus();
            return false;
        }

        if($.trim($('form[data-form="login"] input[name="password"]', _this.el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_password'));
            $('form[data-form="login"] input[name="password"]', _this.el).focus();
            return false;
        }

        return true;
    }
}
coz.cart.auth.init( $('[data-place="authCart"]') );

/*cart container*/
carts = {
    getCarts : function(){
        return coz.carts;
    },
    removeProduct : function(id){
        _carts = carts.getCarts();
        if( typeof _carts != 'undefined'
            && typeof _carts[id] != 'undefined' ){
            delete _carts[id];
            coz.carts = _carts;
        }
        return carts.getCarts();
    },
    removeProductType : function(id, type){
        _carts = carts.getCarts();
        if( typeof _carts != 'undefined'
            && typeof _carts[id] != 'undefined'
            && typeof _carts[id]['product_type'][type] != 'undefined' ){
            delete _carts[id]['product_type'][type];
            if( typeof _carts[id]['product_type'] != 'undefined'
                || Object.keys(_carts[id]['product_type']).length <= 0 ){
                carts.removeProduct(id);
            }
            coz.carts = _carts;
        }
        return carts.getCarts();
    },
    getNumberProductInCart : function(){
        _number = 0;
        _carts = carts.getCarts();
        for(i=0; i < Object.keys(_carts).length; i++ ){
            id = Object.keys(_carts)[i];
            if( typeof _carts != 'undefined'
                && typeof _carts[id] != 'undefined'
                && typeof _carts[id]['product_type'] != 'undefined' ){
                _number += parseInt(Object.keys(_carts[id]['product_type']).length);
            }
        }
        return _number;
    },
    getSubTotalPrice  : function(){
        price_total = 0;
        price_total_old = 0;
        price_total_orig = 0;
        price_total_tax = 0;
        price_total_old_tax = 0;
        price_total_orig_tax = 0;
        _carts = carts.getCarts();
        if( Object.keys(_carts).length > 0 ){
            for(i=0; i < Object.keys(_carts).length; i++ ){
                id = Object.keys(_carts)[i];
                if( typeof _carts != 'undefined'
                        && typeof _carts[id] != 'undefined'
                        && typeof _carts[id]['product_type'] != 'undefined' ){
                    for(j=0; j < Object.keys(_carts[id]['product_type']).length; j++ ){
                        idt = Object.keys(_carts[id]['product_type'])[j];
                        product_type = _carts[id]['product_type'][idt];
                        price_total += parseFloat(product_type['price_total']);
                        price_total_old += parseFloat(product_type['price_total_old']);
                        vat = 0;
                        if( typeof product_type['vat'] != 'undefined'
                            &&  parseFloat(product_type['vat']) > 0 ){
                            vat = product_type['vat'];
                        }
                        price_total_tax += parseFloat(product_type['price_total']) + parseFloat(product_type['price_total']*vat/100);
                        price_total_old_tax += parseFloat(product_type['price_total_old']) + parseFloat(product_type['price_total_old']*vat/100);
                    }
                }
            }
            price_total_orig = price_total;
            price_total_orig_tax = price_total_tax;
            if( coz.hasCoupon() ){
                coupon = coz.getCoupon();
                price_total =  price_total > coupon['coupon_price'] ? (price_total - coupon['coupon_price']) : 0;
                price_total_old =  price_total_old > coupon['coupon_price'] ? (price_total_old - coupon['coupon_price']) : 0;
                
                price_total_tax =  price_total_tax > coupon['coupon_price'] ? (price_total_tax - coupon['coupon_price']) : 0;
                price_total_old_tax =  price_total_old_tax > coupon['coupon_price'] ? (price_total_old_tax - coupon['coupon_price']) : 0;
            }
        }
        return {
            'price_total' : price_total,
            'price_total_old' : price_total_old,
            'price_total_orig' : price_total_orig,
            'price_total_tax' : price_total_tax,
            'price_total_old_tax' : price_total_old_tax,
            'price_total_orig_tax' : price_total_orig_tax
        }
    }
}

coz.view = {};
coz.view.product = {
    link : ['[data-place="linkProduct"]'],
    name : ['[data-place="nameProduct"]'],
    price : ['[data-place="priceProduct"]'],
    priceSale : ['[data-place="priceSaleProduct"]'],
    quantityProduct : ['[data-place="quantityProduct"]'],
    priceTotalProduct : ['[data-place="priceTotalProduct"]'],
    btnEditPropertiesCartProduct : ['[data-place="btnEditPropertiesCartProduct"]'],
    hasTypeName : ['[data-place="hasTypeName"]'],
    typeNameProduct : ['[data-place="typeNameProduct"]'],
    hasExtentionProduct : ['[data-place="hasExtentionProduct"]'],
    extentionProduct : ['[data-place="extentionProduct"]'],
    delCartProduct : ['[data-btn="delCartProduct"]'],
    isProduct : function(product){
        if( typeof product != 'undefined' ){
            return true;
        }
        return false;
    },
    getProductsUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_alias'] != 'undefined'
            && $.trim(product['products_alias']).length > 0 ){
            _url = coz.baseUrl +'/'+ product['products_alias'];
        }
        return _url;
    },
    getAddToCartProductsUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0 ){
            _url = coz.baseUrl +'/cart/popAddToCart/'+ product['products_id'];
        }
        return _url;
    },
    getQuickviewProductsUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_alias'] != 'undefined'
            && $.trim(product['products_alias']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0 ){
            _url = coz.baseUrl +'/quickview/'+product['products_alias']+'-'+product['products_id'];
        }
        return _url;
    },
    getBuyProductsByEmailUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_alias'] != 'undefined'
            && $.trim(product['products_alias']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0  ){
            _url = coz.baseUrl +'/buyByEmail/'+product['products_alias']+'-'+product['products_id'];
        }
        return _url;
    },
    getHeartProductsUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_alias'] != 'undefined'
            && $.trim(product['products_alias']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0  ){
            _url = coz.baseUrl +'/heart/'+product['products_alias']+'-'+product['products_id'];
        }
        return _url;
    },
    getBuyFastProductsUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_alias'] != 'undefined'
            && $.trim(product['products_alias']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0  ){
            _url = coz.baseUrl +'/cart/popBuyFastProduct/'+product['products_id'];
        }
        return _url;
    },
    getCartExtentionUrl : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_type_id'] != 'undefined'
            && $.trim(product['products_type_id']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0  ){
            _url = coz.baseUrl +'/cart/getExtention?products_id='+product['products_id']+'&product_type='+product['products_type_id'];
        }
        return _url;
    },
    getUrlEditProduct : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_type_id'] != 'undefined'
            && $.trim(product['products_type_id']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0  ){
            _url = coz.baseUrl +'/cart/popEditProduct?products_id='+product['products_id']+'&product_type='+product['products_type_id'];
        }
        return _url;
    },
    getUrlDel : function( product ){
        _url = 'javascript:void(0);';
        if( coz.view.product.isProduct(product)
            && typeof product['products_type_id'] != 'undefined'
            && $.trim(product['products_type_id']).length > 0
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0  ){
            _url = coz.baseUrl +'/cart/remove?products_id='+product['products_id']+'&product_type='+product['products_type_id'];
        }
        return _url;
    },
    getPrice : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['products_type_id'] == 'undefined'
                || $.trim(product['products_type_id']).length <= 0
                || coz.getInt(product['products_type_id']) <= 0 ){
                return coz.getFloat(product['price'])+coz.getFloat(product['total_price_extention']);
            }else{
                return coz.getFloat(product['t_price'])+coz.getFloat(product['total_price_extention']);
            }
        }
        return 0;
    },
    getPriceSimple : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['products_type_id'] == 'undefined'
                || $.trim(product['products_type_id']).length <= 0 
                || coz.getInt(product['products_type_id']) <= 0 ){
                return coz.getFloat(product['price']);
            }else{
                return coz.getFloat(product['t_price']);
            }
        }
        return 0;
    },
    getPriceSaleSimple : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['products_type_id'] == 'undefined'
                || $.trim(product['products_type_id']).length <= 0
                || coz.getInt(product['products_type_id']) <= 0 ){
                if( typeof product['price'] != 'undefined'
                    && $.trim(product['price']).length > 0
                    && (typeof product['price_sale'] == 'undefined'
                    || $.trim(product['price_sale']).length <= 0) ){
                    return coz.getFloat(product['price']);
                }
                return coz.getFloat(product['price_sale']);
            }else{
                if( typeof product['t_price'] != 'undefined'
                    && $.trim(product['t_price']).length > 0
                    && (typeof product['t_price_sale'] == 'undefined'
                    || $.trim(product['t_price_sale']).length <= 0) ){
                    return coz.getFloat(product['t_price']);
                }
                return coz.getFloat(product['t_price_sale']);
            }
        }
        return 0;
    },
    getPriceSale : function( product ){
        if( coz.view.product.isProduct(product) ){
            _price = coz.view.product.getPriceSaleSimple(product);
            return coz.getFloat(_price)+ coz.getFloat(product['total_price_extention']);
        }
        return 0;
    },
    getIsAvailable : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['products_type_id'] == 'undefined'
                || $.trim(product['products_type_id']).length <= 0
                || coz.getInt(product['products_type_id']) <= 0 ){
                return coz.getInt(product['is_available']);
            }else{
                return coz.getInt(product['t_is_available']);
            }
        }
        return 0;
    },
    getQuantity : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['products_type_id'] == 'undefined'
                || $.trim(product['products_type_id']).length <= 0
                || coz.getInt(product['products_type_id']) <= 0 ){
                return coz.getInt(product['quantity']);
            }else{
                return coz.getInt(product['t_quantity']);
            }
        }
        return 0;
    },
    getProductsQuantityInCart : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['quantity'] != 'undefined'
                && $.trim(product['quantity']).length > 0 ){
                return coz.getInt(product['quantity']);
            }
        }
        return 0;
    },
    getExtension : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['extensions'] != 'undefined'
                && $.trim(product['extensions']).length > 0 ){
                return product['extensions'];
            }
        }
        return '';
    },
    getExtensionRequire : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['extensions_require'] != 'undefined'
                && $.trim(product['extensions_require']).length > 0 ){
                return product['extensions_require'];
            }
        }
        return '';
    },
    getIsGoingOn : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['is_goingon'] != 'undefined'
                && $.trim(product['is_goingon']).length > 0 ){
                return coz.getInt(product['is_goingon']);
            }
        }
        return 0;
    },
    getIsDelete : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['is_delete'] != 'undefined'
                && $.trim(product['is_delete']).length > 0 ){
                return coz.getInt(product['is_delete']);
            }
        }
        return 0;
    },
    getIsPublished : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['is_published'] != 'undefined'
                && $.trim(product['is_published']).length > 0 ){
                return coz.getInt(product['is_published']);
            }
        }
        return 0;
    },
    getNameType : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['products_type_id'] == 'undefined'
                || $.trim(product['products_type_id']).length <= 0
                || coz.getInt(product['products_type_id']) <= 0 ){
                return product['products_title'];
            }else{
                return product['type_name'];
            }
        }
        return '';
    },
    getToBuy : function( product ){
        if( coz.view.product.isProduct(product) ){
            _price_sale = coz.view.product.getPriceSale(product);
            _price = coz.view.product.getPrice(product);
            _is_available = coz.view.product.getIsAvailable(product);//quản lý kho
            _quantity = coz.view.product.getQuantity(product);
            _is_goingon = coz.view.product.getIsGoingOn(product);
            _is_delete = coz.view.product.getIsDelete(product);
            _is_published = coz.view.product.getIsPublished(product);
            /*có quản lý kho và số lượng sản phẩm nhỏ hơn 0
            hoặc đã xóa hoặc chưa hiển thị hoặc giá bán nhỏ hơn 0 hoặc hàng sắp về*/
            if( !((_is_available == 1 && _quantity <= 0) 
                || _is_delete == 1 || _is_published == 0 || _price_sale <= 0 || _is_goingon == 1) ){
                return true;
            }
        }
        return false;
    },
    getPromotion : function( product ){
        if( coz.view.product.isProduct(product) ){
            _promotion = '';
            _price = coz.view.product.getPrice(product);
            _price_sale = coz.view.product.getPriceSale(product);
            /*
            _promotion = isset($p['promotion_description']) ? $p['promotion_description'] :  null;
            if (!promotion) {
                _promotion = isset($p['promotion1_description']) ? $p['promotion1_description'] :  null;
            }
            if (!$promotion) {
                _promotion = isset($p['promotion2_description']) ? $p['promotion2_description'] :  null;
            }
            if (!$promotion) {
                _promotion = isset($p['promotion3_description']) ? $p['promotion3_description'] :  null;
            }
            if (!empty($promotion)){
                _promotion = $promotion;
            }else if( !empty($price_sale) 
                    && !empty($price)
                    && $price_sale < $price 
                    && floor((1-($price_sale/$price))*100) > 0){
                _promotion = '-'.floor((1-($price_sale/$price))*100).'%';
            }*/
            return _promotion;
        }
        return '';
    },
    getName : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_title'] != 'undefined'
            && $.trim(product['products_title']).length > 0 ){
            return product['products_title'];
        }
        return '';
    },
    getProductsId : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_id'] != 'undefined'
            && $.trim(product['products_id']).length > 0
            && coz.getInt(product['products_id']) > 0 ){
            return product['products_id'];
        }
        return 0;
    },
    getCategoriesId : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['categories_id'] != 'undefined'
            && $.trim(product['categories_id']).length > 0 ){
            return product['categories_id'];
        }
        return 0;
    },
    getProductTypeId : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_type_id'] != 'undefined'
            && $.trim(product['products_type_id']).length > 0
            && coz.getInt(product['products_type_id']) > 0 ){
            return product['products_type_id'];
        }
        return 0;
    },
    getProductTypeName : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['type_name'] != 'undefined'
            && $.trim(product['type_name']).length > 0 ){
            return product['type_name'];
        }
        return '';
    },
    getTitle : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_title'] != 'undefined'
            && $.trim(product['products_title']).length > 0 ){
            return product['products_title'];
        }
        return '';
    },
    getProductsDescription : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_description'] != 'undefined'
            && $.trim(product['products_description']).length > 0 ){
            return product['products_description'];
        }
        return '';
    },
    getProductsMore : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_more'] != 'undefined'
            && $.trim(product['products_more']).length > 0 ){
            return product['products_more'];
        }
        return '';
    },
    getProductsLongdescription : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['products_longdescription'] != 'undefined'
            && $.trim(product['products_longdescription']).length > 0 ){
            return product['products_longdescription'];
        }
        return '';
    },
    getYoutubeVideo : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['youtube_video'] != 'undefined'
            && $.trim(product['youtube_video']).length > 0 ){
            return product['youtube_video'];
        }
        return '';
    },
    getImage : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['thumb_image'] != 'undefined'
            && $.trim(product['thumb_image']).length > 0 ){
            return product['thumb_image'];
        }
        return '';
    },
    getRating : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['rating'] != 'undefined'
            && $.trim(product['rating']).length > 0 ){
            return coz.getInt(product['rating']);
        }
        return 0;
    },
    getManufacturersName : function( product ){
        if( coz.view.product.isProduct(product)
            && typeof product['manufacturers_name'] != 'undefined'
            && $.trim(product['manufacturers_name']).length > 0 ){
            return product['manufacturers_name'];
        }
        return '';
    },
    getLink : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['type_view'] != 'undefined'
                && product['type_view'] ==  2 ){
                return coz.view.product.getQuickviewProductsUrl(product);
            }else{
                return coz.view.product.getProductsUrl(product);
            }
        }
        return '';
    },
    getTypeView : function( product ){
        if( coz.view.product.isProduct(product) ){
            if( typeof product['type_view'] != 'undefined'
                && product['type_view'] ==  2 ){
                return 'data-neo="popup"';
            }
        }
        return '';
    },
    getStringAvailability : function( product ){
        if( coz.view.product.isProduct(product) ){
            _price_sale = coz.view.product.getPriceSale(product);
            _price = coz.view.product.getPrice(product);
            _is_available = coz.view.product.getIsAvailable(product);//quản lý kho
            _quantity = coz.view.product.getQuantity(product);
            _is_goingon = coz.view.product.getIsGoingOn(product);
            _is_delete = coz.view.product.getIsDelete(product);
            _is_published = coz.view.product.getIsPublished(product);
            /*có quản lý kho và số lượng sản phẩm nhỏ hơn 0
            hoặc đã xóa hoặc chưa hiển thị hoặc giá bán nhỏ hơn 0 hoặc hàng sắp về*/
            if( !((_is_available == 1 && _quantity <= 0) 
                || _is_delete == 1 || _is_published == 0 || _price_sale <= 0 || _is_goingon == 1) ){
                return language.translate('txt_availability_product');
            }
        }
        return language.translate('txt_not_availability_product');
    },
    IsAvailablePrice : function( product ){
        if( coz.view.product.isProduct(product) ){
            _price_sale = coz.view.product.getPriceSale(product);
            _price = coz.view.product.getPrice(product);
            /*có quản lý kho và số lượng sản phẩm nhỏ hơn 0
            hoặc đã xóa hoặc chưa hiển thị hoặc giá bán nhỏ hơn 0 hoặc hàng sắp về*/
            if( _price > 0){
                return true;
            }
            
        }
        return false;
    },
    stringWhenNotAvailablePrice : function(){
        return '<span data-neo="priceZero" >'+language.translate('txt_price_zero')+'</span>';
    },
    update : function( product, product_id, product_type_id ){
        _product_id = coz.view.product.getProductsId(product);
        _product_type_id = coz.view.product.getProductTypeId(product);
        if( typeof product_id != 'undefined' 
            && $.trim(product_id).length > 0
            && typeof product_type_id != 'undefined' 
            && $.trim(product_type_id).length > 0 ){
            _filter = '[data-product_id="'+product_id+'"][data-product_type_id="'+product_type_id+'"]';
        }else{
            _filter = '[data-product_id="'+_product_id+'"][data-product_type_id="'+_product_type_id+'"]';
        }

        if( coz.view.product.link.length > 0 ){
            listObjLink = coz.view.product.link.join(',');
            _link = coz.view.product.getLink(product);
            $(listObjLink).filter(_filter).each(function(i, el){
                if( $(el).is('a') ){
                    $(el).attr('href', _link);
                }
            });
        }

        if( coz.view.product.name.length > 0 ){
            listObjName = coz.view.product.name.join(',');
            _name = coz.view.product.getName(product);
            $(listObjName).filter(_filter).each(function(i, el){
                $(el).html(_name);
            });
        }

        if( coz.view.product.price.length > 0 ){
            listObjPrice = coz.view.product.price.join(',');
            _price = coz.fomatCurrency(coz.view.product.getPrice(product));
            $(listObjPrice).filter(_filter).each(function(i, el){
                $(el).html(_price);
            });
        }

        if( coz.view.product.priceSale.length > 0 ){
            listObjPriceSale = coz.view.product.priceSale.join(',');
            _priceSale = coz.fomatCurrency(coz.view.product.getPriceSale(product));
            $(listObjPriceSale).filter(_filter).each(function(i, el){
                $(el).html(_priceSale);
            });
        }

        if( coz.view.product.quantityProduct.length > 0 ){
            listObjQuantity = coz.view.product.quantityProduct.join(',');
            _quantity = coz.view.product.getProductsQuantityInCart(product);
            $(listObjQuantity).filter(_filter).each(function(i, el){
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(_quantity);
                }else{
                    $(el).val(_quantity).attr('name', 'quantity['+_product_id+']['+_product_type_id+']');
                }
            });
        }

        if( coz.view.product.priceTotalProduct.length > 0 ){
            listObjPriceTotal = coz.view.product.priceTotalProduct.join(',');
            /*_quantity = coz.view.product.getProductsQuantityInCart(product);
            _priceSale = coz.view.product.getPriceSale(product);
            _priceTotal = coz.fomatCurrency(_quantity*_priceSale);*/
            _priceTotal = product.price_total;
            $(listObjPriceTotal).filter(_filter).each(function(i, el){
                $(el).html(_priceTotal);
            });
        }

        if( coz.view.product.btnEditPropertiesCartProduct.length > 0 ){
            listObjBtnEditPropertiesCartProduct = coz.view.product.btnEditPropertiesCartProduct.join(',');
            _link = coz.view.product.getUrlEditProduct(product)
            $(listObjBtnEditPropertiesCartProduct).filter(_filter).each(function(i, el){
                if( $(el).is('a') ){
                    $(el).attr('href', _link);
                }
            });
        }

        if( coz.view.product.hasTypeName.length > 0 ){
            listObjHasTypeName = coz.view.product.hasTypeName.join(',');
            _typeName = coz.view.product.getProductTypeName(product);
            $(listObjHasTypeName).filter(_filter).each(function(i, el){
                if( $.trim(_typeName).length > 0 ){
                    $(el).show();
                }else{
                    $(el).hide();
                }
            });
        }

        if( coz.view.product.typeNameProduct.length > 0 ){
            listObjTypeNameProduct = coz.view.product.typeNameProduct.join(',');
            _typeName = coz.view.product.getProductTypeName(product);
            $(listObjTypeNameProduct).filter(_filter).each(function(i, el){
                $(el).html(_typeName);
            });
        }

        if( coz.view.product.hasExtentionProduct.length > 0 ){
            listObjHasExtentionProduct = coz.view.product.hasExtentionProduct.join(',');
            _ext = coz.view.product.getExtension(product);
            _ext_m = coz.view.product.getExtensionRequire(product);
            _lsext = {};
            if( Object.keys(_ext).length >0 ){
                _lsext = _ext;
            }
            if( Object.keys(_ext_m).length >0 ){
                _lsext = Object.assign({}, _lsext, _ext_m);
            }
            $(listObjHasExtentionProduct).filter(_filter).each(function(i, el){
                if( Object.keys(_lsext).length > 0 ){
                    $(el).show();
                }else{
                    $(el).hide();
                }
            });
        }

        if( coz.view.product.extentionProduct.length > 0 ){
            listObjExtentionProduct = coz.view.product.extentionProduct.join(',');
            _ext = coz.view.product.getExtension(product);
            _ext_m = coz.view.product.getExtensionRequire(product);
            _lsext = {};
            if( Object.keys(_ext).length >0 ){
                _lsext = _ext;
            }
            if( Object.keys(_ext_m).length >0 ){
                _lsext = Object.assign({}, _lsext, _ext_m);
            }
            _name_ext = '';
            for( k=0; k< Object.keys(_lsext).length; k++){
                _ke = Object.keys(_lsext)[k];
                _name_ext += (k==0 ? '' : ';') +_lsext[_ke].ext_name;
            }
            $(listObjExtentionProduct).filter(_filter).each(function(i, el){
                $(el).html(_name_ext);
            });
        }

        if( coz.view.product.delCartProduct.length > 0 ){
            listObjDelCartProduct = coz.view.product.delCartProduct.join(',');
            _link = coz.view.product.getUrlDel(product)
            $(listObjDelCartProduct).filter(_filter).each(function(i, el){
                if( $(el).is('a') ){
                    $(el).attr('href', _link);
                }
            });
        }

        if( _product_id != product_id 
            || _product_type_id != product_type_id ){
            $('[data-product_id="'+product_id+'"][data-product_type_id="'+product_type_id+'"]').each(function(i, el){
                $(el).attr('data-product_id', _product_id);
                $(el).data('product_id', _product_id);
                $(el).attr('data-product_type_id', _product_type_id);
                $(el).data('product_type_id', _product_type_id);
                $(el).attr('data-cartid', _product_id+'-'+_product_type_id);
                $(el).data('cartid', _product_id+'-'+_product_type_id);
            });
        }
    }
};
coz.view.carts = {
    btnDel : ['[data-btn="delCartProduct"]'],
    numberProductInCart : ['[data-btn="numberProductInCart"]'],
    price_total : ['[data-place="priceCartTotal"]'],
    price_total_old : ['[data-place="priceCartTotalOld"]'],
    price_total_orig : ['[data-place="priceCartTotalOrig"]'],
    price_total_tax : ['[data-place="priceCartTotalTax"]'],
    price_total_old_tax : ['[data-place="priceCartTotalOldTax"]'],
    price_total_orig_tax : ['[data-place="priceCartTotalOrigTax"]'],
    price_tax : ['[data-place="priceCartTax"]'],
    has_price_tax : ['[data-place="hasPriceCartTax"]'],
    btnEditPropertiesCartProduct : ['[data-place="btnEditPropertiesCartProduct"]'],
    btnAppCoupon : ['[data-btn="appCoupon"]'],
    btnhHideFromCoupon : ['[data-btn="hideFromCoupon"]'],
    notCoupon : ['[data-place="notCoupon"]'],
    hasCoupon : ['[data-place="hasCoupon"]'],
    btnHideFromCoupon : ['[data-btn="hideFormCoupon"]'],
    currentCoupon : ['[data-place="currentCoupon"]'],
    btnEditCounpon : ['[data-btn="editCounpon"]'],
    btnRemoveCounpon : ['[data-btn="removeCounpon"]'],
    valueCoupon : ['[data-place="valueCoupon"]'],
    notContainCart : ['[data-place="notContainCart"]'],
    hasContainCart : ['[data-place="hasContainCart"]'],
    update : function(){
        _subTotal = carts.getSubTotalPrice();
        _number = carts.getNumberProductInCart();
        if( coz.view.carts.numberProductInCart.length > 0 ){
            listObjNumberProductInCart = coz.view.carts.numberProductInCart.join(',');
            $(listObjNumberProductInCart).each(function(i, el){
                $(el).val(_number);
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(_number);
                }
            });
        }
        if( coz.view.carts.price_total.length > 0 ){
            listObjPriceTotal = coz.view.carts.price_total.join(',');
            $(listObjPriceTotal).each(function(i, el){
                $(el).val(coz.fomatCurrency(_subTotal.price_total));
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(coz.fomatCurrency(_subTotal.price_total));
                }
            });
        }
        if( coz.view.carts.price_total_old.length > 0 ){
            listObjPriceTotalOld = coz.view.carts.price_total_old.join(',');
            $(listObjPriceTotalOld).each(function(i, el){
                $(el).val(coz.fomatCurrency(_subTotal.price_total_old));
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(coz.fomatCurrency(_subTotal.price_total_old));
                }
            });
        }
        if( coz.view.carts.price_total_orig.length > 0 ){
            listObjPriceTotalOrig = coz.view.carts.price_total_orig.join(',');
            $(listObjPriceTotalOrig).each(function(i, el){
                $(el).val(coz.fomatCurrency(_subTotal.price_total_orig));
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(coz.fomatCurrency(_subTotal.price_total_orig));
                }
            });
        }
        if( coz.view.carts.price_total_tax.length > 0 ){
            listObjPriceTotalTax = coz.view.carts.price_total_tax.join(',');
            $(listObjPriceTotalTax).each(function(i, el){
                $(el).val(coz.fomatCurrency(_subTotal.price_total_tax));
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(coz.fomatCurrency(_subTotal.price_total_tax));
                }
            });
        }
        if( coz.view.carts.price_total_old_tax.length > 0 ){
            listObjPriceTotalOldTax = coz.view.carts.price_total_old_tax.join(',');
            $(listObjPriceTotalOldTax).each(function(i, el){
                $(el).val(coz.fomatCurrency(_subTotal.price_total_old_tax));
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(coz.fomatCurrency(_subTotal.price_total_old_tax));
                }
            });
        }
        if( coz.view.carts.price_total_orig_tax.length > 0 ){
            listObjPriceTotalOrigTax = coz.view.carts.price_total_orig_tax.join(',');
            $(listObjPriceTotalOrigTax).each(function(i, el){
                $(el).val(coz.fomatCurrency(_subTotal.price_total_orig_tax));
                if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                    $(el).html(coz.fomatCurrency(_subTotal.price_total_orig_tax));
                }
            });
        }
        if( coz.view.carts.price_tax.length > 0 ){
            listObjPriceTax = coz.view.carts.price_tax.join(',');
            listObjHasPriceTax = coz.view.carts.has_price_tax.join(',');
            if( _subTotal.price_total_tax > _subTotal.price_total ){
                $(listObjHasPriceTax).each(function(i, el){
                    $(el).show();
                });
                $(listObjPriceTax).each(function(i, el){
                    $(el).val(coz.fomatCurrency(_subTotal.price_total_tax - _subTotal.price_total));
                    if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                        $(el).html(coz.fomatCurrency(_subTotal.price_total_tax - _subTotal.price_total));
                    }
                });
            }else{
                $(listObjHasPriceTax).each(function(i, el){
                    $(el).hide();
                });
                $(listObjPriceTax).each(function(i, el){
                    $(el).val('');
                    if( !($(el).is('input') || $(el).is('select') || $(el).is('textarea')) ){
                        $(el).html('');
                    }
                });
            }
        }

        if( coz.view.carts.notContainCart.length > 0
            && coz.view.carts.hasContainCart.length > 0 ){
            listObjNotContainCart = coz.view.carts.notContainCart.join(',');
            listObjHasContainCart = coz.view.carts.hasContainCart.join(',');
            if( Object.keys(coz.carts).length <= 0 ){
                $(listObjNotContainCart).each(function(i, el){
                    $(el).show();
                });
                $(listObjHasContainCart).each(function(i, el){
                    $(el).hide();
                });
            }else{
                $(listObjNotContainCart).each(function(i, el){
                    $(el).hide();
                });
                $(listObjHasContainCart).each(function(i, el){
                    $(el).show();
                });
            }
        }
    },
    start : function(){
        coz.view.carts.update();
        
        if( coz.view.carts.btnDel.length > 0 ){
            listObjDel = coz.view.carts.btnDel.join(',');
            $(document).on('click', listObjDel ,function(e){
                e.preventDefault();
                e.stopPropagation();
                var r = confirm(language.translate('txt_ban_muon_xoa_coupon'));
                if (r == true) {
                    var self = $(this);
                    var href = $(this).attr('href')||$(this).attr('data-url');
                    href += href.indexOf('?')>=0 ? '&ajax=1' : '?ajax=1';
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: href,
                        data: {_AJAX:1},
                        cache: false,
                        success: function(data)
                        {
                            try{
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                if( data.flag == true || data.flag == 'true' ){
                                    _carts = data.data;
                                    if( typeof _carts['shipping'] != 'undefined' ){
                                        delete _carts['shipping'];
                                    }
                                    if( typeof _carts['coupon'] != 'undefined' ){
                                        delete _carts['coupon'];
                                    }
                                    coz.carts = _carts;
                                    coz.view.carts.update();
                                    cartid = self.data('cartid');
                                    $('[data-cartid="'+cartid+'"]').remove();
                                }
                            }catch(e){
                                console.log(e);
                            }
                        },
                        error: function(e)
                        {
                            coz.toast('Error ! OoO .please trying');
                        }
                    });
                }
            }); 
        }

        if( coz.view.carts.btnEditPropertiesCartProduct.length > 0 ){
            listObjEditPropertiesCartProduct = coz.view.carts.btnEditPropertiesCartProduct.join(',');
            $(document).on('click', listObjEditPropertiesCartProduct ,function(e){
                e.preventDefault();
                e.stopPropagation();
                $('.popover-edit-product-cart').remove();
                var self = $(this);
                var href = $(this).attr('href')||$(this).attr('data-url');
                href += href.indexOf('?')>=0 ? '&ajax=1' : '?ajax=1';
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: href,
                    data: {_AJAX:1},
                    cache: false,
                    success: function(data)
                    {
                        try{
                            if(data.constructor === String){
                                data = $.parseJSON(data);
                            }
                            if( data.flag == true || data.flag == 'true' ){
                                $('body').append(data.html);
                                cartid = self.data('cartid');
                                _product_id = self.data('product_id');
                                _product_type_id = self.data('product_type_id');
                                pin = self.parents('.coz-table-cart').eq(0).find('[data-product_id="'+_product_id+'"][data-product_type_id="'+_product_type_id+'"][data-place="nameProduct"]').eq(0);
                                offset = $(pin).eq(0).offset();
                                _top = parseFloat(offset.top) + parseFloat($(pin).eq(0).height());
                                _left = offset.left;
                                $('.popover-edit-product-cart[data-cartid="'+cartid+'"]').css({'display':'block', 'top' : _top+'px', 'left' : _left+'px'});
                                $('.popover-edit-product-cart[data-cartid="'+cartid+'"] [data-btn="btnCancelPopProductCart"]').off('click').on('click', function(){
                                    $('.popover-edit-product-cart').remove();
                                });
                                $('.popover-edit-product-cart[data-cartid="'+cartid+'"] [data-btn="btnSaveProductCart"]').off('click').on('click', function(){
                                    _quantity = 0;
                                    _min = 1;
                                    _max = 1;
                                    if( $('.popover-edit-product-cart[data-cartid="'+cartid+'"] input[name="quantity"]').length > 0 ){
                                        _quantity = $('.popover-edit-product-cart[data-cartid="'+cartid+'"] input[name="quantity"]').val();
                                        _max = $('.popover-edit-product-cart[data-cartid="'+cartid+'"] input[name="quantity"]').attr('max');
                                        if( coz.getInt(_max) <= 0 ){
                                            _max = _quantity;
                                        }
                                    }
                                    if( coz.getInt(_quantity) >= _min
                                        && coz.getInt(_quantity) <= _max ){
                                        _trans = $('.popover-edit-product-cart[data-cartid="'+cartid+'"] form').serialize();
                                        $.ajax({
                                            type: "POST",
                                            dataType: "json",
                                            url: coz.baseUrl+'/cart/updateProduct?_AJAX=1',
                                            data: _trans,
                                            cache: false,
                                            success: function(data)
                                            {
                                                if(data.constructor === String){
                                                    data = $.parseJSON(data);
                                                }
                                                coz.toast(data.msg);
                                                if( data.flag == true || data.flag == 'true' ){
                                                    _carts = data.cart;
                                                    coz.carts = _carts;
                                                    coz.view.carts.update();
                                                    coz.view.product.update(data.product, _product_id, _product_type_id);
                                                    $('.popover-edit-product-cart').remove();
                                                    if( typeof coz.buyer != 'undefined' && typeof coz.shipper != 'undefined'
                                                        && Object.keys(coz.buyer).length > 0 && Object.keys(coz.shipper).length > 0
                                                        && typeof coz.buyer.shipping_id != 'undefined' && typeof coz.buyer.payment_id != 'undefined' ){
                                                        $('[data-place="payStep04"]').html('');
                                                        coz.paymentStep.gotoPageThree();
                                                        delete coz.buyer.shipping_id;
                                                    }
                                                    coz.paymentStep.getShipping('ships');
                                                }
                                            },
                                            error: function(e)
                                            {
                                                coz.toast('Error ! OoO .please trying');
                                            }
                                        });
                                    }else{
                                        coz.toast(language.translate('ban_nhap_so_luong_chua_dung'));
                                    }
                                });
                                
                            }else{
                                coz.toast('Error ! OoO .please trying');
                            }
                        }catch(e){
                            console.log(e);
                        }
                    },
                    error: function(e)
                    {
                        coz.toast('Error ! OoO .please trying');
                    }
                });
            });

            $(document).on('click', function(e){
                if( $(e.target).closest('.popover-edit-product-cart').length <=0 ){
                    $('.popover-edit-product-cart').remove();
                }
            }); 
        }

    }
};
coz.view.carts.start();

coz.view.coupon = {
    btnEditCounpon : ['[data-btn="btnEditCounpon"]'],
    btnAppCounpon : ['[data-btn="btnAppCounpon"]'],
    btnRemoveCounpon : ['[data-btn="btnRemoveCounpon"]'],
    valueCoupon : ['[data-place="valueCoupon"]'],
    valueCouponCode : ['[data-place="valueCouponCode"]'],
    hasCoupon : ['[data-place="hasCoupon"]'],
    notHasCoupon : ['[data-place="notHasCoupon"]'],
    wrapCoupon : ['[data-place="coupon"]'],
    update : function(){
        listObjWrapCoupon = coz.view.coupon.wrapCoupon.join(',');
        listObjValueCoupon = coz.view.coupon.valueCoupon.join(',');
        listObjValueCouponCode = coz.view.coupon.valueCouponCode.join(',');
        listObjHasCoupon = coz.view.coupon.hasCoupon.join(',');
        listObjNotHasCoupon = coz.view.coupon.notHasCoupon.join(',');

        if( typeof coz.coupon != 'undefined'
            && coz.coupon != ''
            && typeof coz.coupon.coupons_code != 'undefined' ){
            $(listObjValueCoupon).each(function(i, el){
                $(el).html(coz.fomatCurrency(coz.coupon.coupon_price));
            });

            $(listObjValueCouponCode).each(function(i, el){
                $(el).html(coz.coupon.coupons_code);
            });

            $(listObjHasCoupon).each(function(i, el){
                $(el).show();
            });

            $(listObjNotHasCoupon).each(function(i, el){
                $(el).hide();
            });
        }else{
            $(listObjValueCoupon).each(function(i, el){
                $(el).val('');
            });

            $(listObjValueCouponCode).each(function(i, el){
                $(el).html('');
            });

            $(listObjHasCoupon).each(function(i, el){
                $(el).hide();
            });

            $(listObjNotHasCoupon).each(function(i, el){
                $(el).show();
            });
        }
    },
    start : function(){
        coz.view.coupon.update();

        if( coz.view.coupon.btnEditCounpon.length > 0 ){
            listObjEditCounpon = coz.view.coupon.btnEditCounpon.join(',');
            $(document).on('click', listObjEditCounpon, function(e){
                e.preventDefault();
                e.stopPropagation();
                listObjWrapCoupon = coz.view.coupon.wrapCoupon.join(',');
                listObjValueCouponCode = coz.view.coupon.valueCouponCode.join(',');
                listObjHasCoupon = coz.view.coupon.hasCoupon.join(',');
                listObjNotHasCoupon = coz.view.coupon.notHasCoupon.join(',');
                _parentCp = $(this).parents(listObjWrapCoupon).eq(0);
                $('[data-input="coupon"]', _parentCp).val($.trim($(listObjValueCouponCode, _parentCp).eq(0).html()));
                $(listObjNotHasCoupon, _parentCp).show();
                $(listObjHasCoupon, _parentCp).hide();
            });
        }

        if( coz.view.coupon.btnAppCounpon.length > 0 ){
            listObjAppCounpon = coz.view.coupon.btnAppCounpon.join(',');
            $(document).on('click', listObjAppCounpon, function(e){
                e.preventDefault();
                e.stopPropagation();
                listObjNotHasCoupon = coz.view.coupon.notHasCoupon.join(',');
                _parentCp = $(this).parents(listObjNotHasCoupon).eq(0);
                if( $('input[data-input="coupon"]', _parentCp).length == 1 
                    && $.trim($('[data-input="coupon"]', _parentCp).val()).length >0 ){
                    clearInterval(coz.INTERVAL);
                    clearTimeout(coz.INTERVAL);
                    coz.INTERVAL = setTimeout( function(){
                        var coupon = $('[data-input="coupon"]', _parentCp).val();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: coz.baseUrl+'/cart/useCoupon?_AJAX=1',
                            data: {coupon:coupon},
                            cache: false,
                            success: function(data)
                            {
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                coz.toast(data.msg);
                                if( data.flag == true || data.flag == 'true'){
                                    _carts = data.cart;
                                    _coupon = data.coupon;
                                    if( typeof _carts['shipping'] != 'undefined' ){
                                        delete _carts['shipping'];
                                    }
                                    if( typeof _carts['coupon'] != 'undefined' ){
                                        delete _carts['coupon'];
                                    }
                                    coz.carts = _carts;
                                    coz.coupon = _coupon;
                                    coz.view.coupon.update();
                                    coz.view.carts.update();
                                }else{
                                    $('[data-input="coupon"]', _parentCp).eq(0).focus();
                                }
                            },
                            error: function(e)
                            {
                                console.log(e);
                            }
                        });
                    }, coz.DELAY);
                }else{
                  coz.toast(language.translate('txt_ban_chua_nhap_coupon'));
                  $('[data-input="coupon"]', _parentCp).eq(0).focus();
                }
            });
        }

        if( coz.view.coupon.btnRemoveCounpon.length > 0 ){
            listObjRemoveCounpon = coz.view.coupon.btnRemoveCounpon.join(',');
            $(document).on('click', listObjRemoveCounpon, function(e){
                e.preventDefault();
                e.stopPropagation();
                clearInterval(coz.INTERVAL);
                clearTimeout(coz.INTERVAL);
                coz.INTERVAL = setTimeout( function(){
                    var r = confirm(language.translate('txt_ban_muon_xoa_coupon'));
                    if (r == true) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: coz.baseUrl+'/cart/removeCoupon?_AJAX=1',
                            data: null,
                            cache: false,
                            success: function(data)
                            {
                                if(data.constructor === String){
                                    data = $.parseJSON(data);
                                }
                                coz.toast(data.msg);
                                if( data.flag == true || data.flag == 'true'){
                                    _carts = data.cart;
                                    _coupon = data.coupon;
                                    if( typeof _carts['shipping'] != 'undefined' ){
                                        delete _carts['shipping'];
                                    }
                                    if( typeof _carts['coupon'] != 'undefined' ){
                                        delete _carts['coupon'];
                                    }
                                    coz.carts = _carts;
                                    coz.coupon = '';
                                    delete coz.coupon;
                                    coz.view.coupon.update();
                                    coz.view.carts.update();
                                }
                            },
                            error: function(e)
                            {
                              console.log(e);
                            }
                        });
                    }
                }, coz.DELAY);
            });
        }
    }
};
coz.view.coupon.start();

coz.paymentStep = {
    gotoPageOne : function(){
        if( !$('#coz_nav_step_01').hasClass('active') ){
            $('#coz_nav_step_01').addClass('active').siblings().removeClass('active');
            $('#coz_step_01').addClass('active').siblings().removeClass('active');
        }
    },
    gotoPageTwo : function(){
        if( !$('#coz_nav_step_02').hasClass('active') ){
            if( typeof coz.buyer != 'undefined'
                && Object.keys(coz.buyer).length > 0 ){
                $('#coz_nav_step_02').addClass('active').siblings().removeClass('active');
                $('#coz_step_02').addClass('active').siblings().removeClass('active');
            }
        }
    },
    gotoPageThree : function(){
        if( !$('#coz_nav_step_03').hasClass('active') ){
            if( typeof coz.buyer != 'undefined' && typeof coz.shipper != 'undefined'
                && Object.keys(coz.buyer).length > 0 && Object.keys(coz.shipper).length > 0 ){
                _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
                if( typeof _no_shipping != 'undefined' 
                    && _no_shipping != 'true' && _no_shipping != true ){
                    $('#coz_nav_step_03').addClass('active').siblings().removeClass('active');
                    $('#coz_step_03').addClass('active').siblings().removeClass('active');
                }else{
                    coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
                }
            }
        }
    },
    gotoPageFour : function(){
        _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
        if( !$('#coz_nav_step_04').hasClass('active') ){
            if( typeof coz.buyer != 'undefined' && typeof coz.shipper != 'undefined'
                && Object.keys(coz.buyer).length > 0 && Object.keys(coz.shipper).length > 0
                && typeof coz.buyer.shipping_id != 'undefined' && typeof coz.buyer.payment_id != 'undefined' ){
                _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
                if( typeof _no_shipping != 'undefined' 
                    && _no_shipping != 'true' && _no_shipping != true ){
                    $('#coz_nav_step_04').addClass('active').siblings().removeClass('active');
                    $('#coz_step_04').addClass('active').siblings().removeClass('active');
                }else{
                    coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
                }
            }
        }
    },
    getFeeTransitions : function(type){
        if( type == 'ships' && $('[data-place="payStep02"]').length > 0 ){
            clearInterval(coz.INTERVAL);
            clearTimeout(coz.INTERVAL);
            coz.INTERVAL = setTimeout( function(){
                var _el = '[data-place="payStep02"]';
                var country_id = 0;
                var cities_id = 0;
                var districts_id = 0;
                var wards_id = 0;
                var transport_type = 0;
                var shipping_id = 0;
                if( $('[name="ships[country_id]"]', _el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _el).val();
                }
                if($('select[name="ships[cities_id]"]', _el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _el).val();
                }else if($('select[name="ships[state]"]', _el).length>0){
                    cities_id = $('select[name="ships[state]"]', _el).val();
                }else if($('select[name="ships[region]"]', _el).length>0){
                    cities_id = $('select[name="ships[region]"]', _el).val();
                }else if($('select[name="ships[province]"]', _el).length>0){
                    cities_id = $('select[name="ships[province]"]', _el).val();
                }
                if($('select[name="ships[districts_id]"]', _el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _el).val();
                }
                if( $('select[name="ships[wards_id]"]', _el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _el).val();
                }
                if( $('[name="trans[transport_type]"]:checked', '[data-place="payStep03"]').length > 0){
                    transport_type = $('[name="trans[transport_type]"]:checked', '[data-place="payStep03"]').val();
                }
                if( $('[name="trans[shipping_id]"]:checked', '[data-place="payStep03"]').length >0 ){
                    shipping_id = $('input[name="trans[shipping_id]"]:checked', '[data-place="payStep03"]').val();
                }
                
                $.ajax({
                    type: "GET",
                    dataType: "html",
                    url: coz.baseUrl+'/cart/getFeeTransitions?_AJAX=1',
                    data: 'shipping_id='+shipping_id+'&country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&transport_type='+transport_type,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        if(data.flag == true || data.flag == 'true'){
                            $('[data-place="hasFeeTransitions"]').each(function(i, el){
                                if( $.trim(data.fee).length <= 0
                                    || coz.getFloat(data.fee) <= 0 ){
                                    $(this).hide().find('[data-place="valueFeeTransitions"]').html('');
                                }else{
                                    $(this).show().find('[data-place="valueFeeTransitions"]').html(data.fee_currency);
                                }
                            });
                        }else{
                            $('[data-place="hasFeeTransitions"]').each(function(i, el){
                                $(this).hide().find('[data-place="valueFeeTransitions"]').html('');
                            });
                        }
                    },
                    error: function(e)
                    {
                        coz.toast('Error ! OoO .please trying');
                        console.log(e);
                    }
                });
            }, coz.DELAY);
        }
    },
    getShipping : function( type ){
        if( type == 'ships' && $('[data-place="payStep02"]').length > 0 ){
            clearInterval(coz.INTERVAL);
            clearTimeout(coz.INTERVAL);
            coz.INTERVAL = null;
            coz.INTERVAL = setTimeout( function(){
                var _el = '[data-place="payStep02"]';
                var country_id = 0;
                var cities_id = 0;
                var districts_id = 0;
                var wards_id = 0;
                var transport_type = 0;
                if( $('[name="ships[country_id]"]', _el).length > 0){
                    country_id = $('[name="ships[country_id]"]', _el).val();
                }
                if($('select[name="ships[cities_id]"]', _el).length>0){
                    cities_id = $('select[name="ships[cities_id]"]', _el).val();
                }else if($('select[name="ships[state]"]', _el).length>0){
                    cities_id = $('select[name="ships[state]"]', _el).val();
                }else if($('select[name="ships[region]"]', _el).length>0){
                    cities_id = $('select[name="ships[region]"]', _el).val();
                }else if($('select[name="ships[province]"]', _el).length>0){
                    cities_id = $('select[name="ships[province]"]', _el).val();
                }
                if($('select[name="ships[districts_id]"]', _el).length>0){
                    districts_id = $('select[name="ships[districts_id]"]', _el).val();
                }
                if( $('select[name="ships[wards_id]"]', _el).length > 0){
                    wards_id = $('select[name="ships[wards_id]"]', _el).val();
                }
                if( $('[name="trans[transport_type]"]:checked', '[data-place="payStep03"]').length > 0){
                    transport_type = $('[name="trans[transport_type]"]:checked', '[data-place="payStep03"]').val();
                }
                $.ajax({
                    type: "GET",
                    dataType: "html",
                    url: coz.baseUrl+'/cart/getShipping?_AJAX=1',
                    data: 'country_id='+country_id+'&cities_id='+cities_id+'&districts_id='+districts_id+'&wards_id='+wards_id+'&transport_type='+transport_type+'&ajax=1',
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        $('[neo-place="Shipping"]', '[data-place="payStep03"]').html(data.html);
                        $(_el).data('no_shipping', true);
                        if( data.flag == 'true'
                            || data.flag == true ){
                            $(_el).data('no_shipping', data.no_shipping);
                            $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping', data.no_shipping);
                            $('form[data-form="corfirm"]', '[data-place="payStep04"]').data('no_shipping', data.no_shipping);
                            $('[data-place="hasErrorShipper"]', '[data-place="processPayment"]').each(function(){
                                $(this).hide().find('[data-place="errorShipper"]').hide().html('');
                            });
                            if( data.no_shipping == 'true' || data.no_shipping == true ){
                                coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
                                $('[data-place="hasErrorShipper"]', '[data-place="processPayment"]').each(function(){
                                    $(this).show().find('[data-place="errorShipper"]').show().html(data.msg);
                                });
                            }
                        }

                        $('[name="trans[transport_type]"]', '[data-place="payStep03"]').off('change').on('change', function(e){
                            coz.paymentStep.getShipping('ships');
                        });
                        $('[name="trans[shipping_id]"]', '[data-place="payStep03"]').off('change').on('change', function(e){
                            coz.paymentStep.getShipping('ships');
                        });

                        coz.paymentStep.getFeeTransitions(type);
                    },
                    error: function(e)
                    {
                        coz.toast('Error ! OoO .please trying');
                    }
                });
            }, coz.DELAY);
        }
    },
    updateEvent: function( type ){
        _name = '';
        _el = '';
        if( type == 'trans' ){
            _name = 'trans';
            _el = '[data-place="buyNotSign"]';
        }else if( type == 'ships' ){
            _name = 'ships';
            _el = '[data-place="payStep02"]';
        }else if( type == 'user' ){
            _name = 'user';
            _el = '[data-place="paySignAndBuy"]';
        }
        if( $.trim(_name).length > 0
            && $(_el).length > 0 
            && $.trim(_el).length > 0 ){
            (function(_name, _el){
                _address = '';
                if( _name == 'trans' 
                    && typeof coz.buyer != 'undefined' ){
                    _address = coz.buyer.address;
                }else if( _name == 'ships' ){
                    if( typeof coz.shipper != 'undefined' ){
                        _address = coz.shipper.ships_address;
                    }else if( typeof coz.buyer != 'undefined' ){
                        _address = coz.buyer.address;
                    }
                }
                if( $.trim(_address).length <= 0
                    && coz.hasLogin()
                    && typeof coz.member.address != 'undefined'
                    && $.trim(coz.member.address).length >0 ){
                    _address = coz.member.address;
                }
                $('input[name="'+_name+'[address]"]', _el).val(_address);
                $('select[name="'+_name+'[cities_id]"]', _el).off('change').on('change', function(){
                    $('select[name="'+_name+'[districts_id]"]', _el).html('');
                    $('select[name="'+_name+'[wards_id]"]', _el).html('');
                    _districts_id = 0;
                    if( _name == 'trans' 
                        && typeof coz.buyer != 'undefined' ){
                        _districts_id = coz.buyer.districts_id;
                    }else if( _name == 'ships' ){
                        if( typeof coz.shipper != 'undefined' ){
                            _districts_id = coz.shipper.ships_districts_id;
                        }else if( typeof coz.buyer != 'undefined' ){
                            _districts_id = coz.buyer.districts_id;
                        }
                    }
                    cities_id = $(this).val();
                    if( typeof cities_id != 'undefined' 
                        && $.trim(cities_id).length > 0 ){
                        (function(_dname, _dl, type, _cid, _did){
                            coz.loadDistrict( _cid , function( datas ){
                                if( typeof datas.success != 'undefined'
                                    &&  (datas.success == 'true' || datas.success == true) ){
                                    if(datas.results.length>0){
                                        $.each(datas.results, function(i, row){
                                            $('select[name="'+_dname+'[districts_id]"]', _dl).append($("<option "+ (row.districts_id == _did ? 'selected' : '' ) +" ></option>").attr('value', row.districts_id).text(row.districts_title));
                                            if(i>=datas.results.length-1){
                                                $('select[name="'+_dname+'[districts_id]"]', _dl).trigger('change');
                                            }
                                        });
                                    }else{
                                        coz.paymentStep.getShipping(type);
                                    }
                                }else{
                                    coz.paymentStep.getShipping(type);
                                }
                            });
                        })(_name, _el, type, cities_id, _districts_id);
                    }else{
                        coz.paymentStep.getShipping(type);
                    }
                });

                $('select[name="'+_name+'[districts_id]"]', _el).off('change').on('change', function(){
                    $('select[name="'+_name+'[wards_id]"]', _el).html('');
                    _wards_id = 0;
                    if( _name == 'trans' 
                        && typeof coz.buyer != 'undefined' ){
                        _wards_id = coz.buyer.wards_id;
                    }else if( _name == 'ships' ){
                        if( typeof coz.shipper != 'undefined' ){
                            _wards_id = coz.shipper.ships_wards_id;
                        }else if( typeof coz.buyer != 'undefined' ){
                            _wards_id = coz.buyer.wards_id;
                        }
                    }
                    districts_id = $(this).val();
                    if( typeof districts_id != 'undefined' 
                        && $.trim(districts_id).length > 0 ){
                        (function(_wname, _wl, type, _did, _wid){
                            coz.loadWard( _did , function( datas ){
                                if( typeof datas.success != 'undefined'
                                    &&  (datas.success == 'true' || datas.success == true) ){
                                    if(datas.results.length>0){
                                        $.each(datas.results, function(i, row){
                                            $('select[name="'+_wname+'[wards_id]"]', _wl).append($("<option "+ (row.wards_id == _wid ? 'selected' : '' ) +" ></option>").attr('value', row.wards_id).text(row.wards_title));
                                            if(i>=datas.results.length-1){
                                                $('select[name="'+_wname+'[wards_id]"]', _wl).trigger('change');
                                            }
                                        });
                                    }else{
                                        coz.paymentStep.getShipping(type);
                                    }
                                }else{
                                    coz.paymentStep.getShipping(type);
                                }
                            });
                        })(_name, _el, type, districts_id, _wards_id);
                    }else{
                        coz.paymentStep.getShipping(type);
                    }
                });

                $('select[name="'+_name+'[wards_id]"], select[name="'+_name+'[state]"], select[name="'+_name+'[region]"], select[name="'+_name+'[province]"]', _el).off('change').on('change', function(){
                    coz.paymentStep.getShipping(type);
                });
                if( _name == 'trans' 
                    && typeof coz.buyer != 'undefined' ){
                    _cities_id = coz.buyer.cities_id;
                    $('select[name="'+_name+'[cities_id]"]', _el).val(_cities_id);
                    $('select[name="'+_name+'[cities_id]"]', _el).trigger('change');
                    $('input[name="'+_name+'[city]"]', _el).val(coz.buyer.city);
                    $('input[name="'+_name+'[zipcode]"]', _el).val(coz.buyer.zipcode);
                    $('input[name="'+_name+'[state]"]', _el).val(coz.buyer.state);
                    $('input[name="'+_name+'[suburb]"]', _el).val(coz.buyer.suburb);
                    $('input[name="'+_name+'[state]"]', _el).val(coz.buyer.state);
                    $('input[name="'+_name+'[address01]"]', _el).val(coz.buyer.address01);
                    $('input[name="'+_name+'[province]"]', _el).val(coz.buyer.province);
                }else if( _name == 'ships' ){
                    if( typeof coz.shipper != 'undefined' ){
                        _cities_id = coz.shipper.ships_cities_id;
                        $('select[name="'+_name+'[cities_id]"]', _el).val(_cities_id);
                        $('select[name="'+_name+'[cities_id]"]', _el).trigger('change');
                        $('input[name="'+_name+'[city]"]', _el).val(coz.shipper.ships_city);
                        $('input[name="'+_name+'[zipcode]"]', _el).val(coz.shipper.ships_zipcode);
                        $('input[name="'+_name+'[state]"]', _el).val(coz.shipper.ships_state);
                        $('input[name="'+_name+'[suburb]"]', _el).val(coz.shipper.ships_suburb);
                        $('input[name="'+_name+'[state]"]', _el).val(coz.shipper.ships_state);
                        $('input[name="'+_name+'[address01]"]', _el).val(coz.shipper.ships_address01);
                        $('input[name="'+_name+'[province]"]', _el).val(coz.shipper.ships_province);
                    }else if( coz.buyer != 'undefined' ){
                        _cities_id = coz.buyer.cities_id;
                        $('select[name="'+_name+'[cities_id]"]', _el).val(_cities_id);
                        $('select[name="'+_name+'[cities_id]"]', _el).trigger('change');
                        $('input[name="'+_name+'[city]"]', _el).val(coz.buyer.city);
                        $('input[name="'+_name+'[zipcode]"]', _el).val(coz.buyer.zipcode);
                        $('input[name="'+_name+'[state]"]', _el).val(coz.buyer.state);
                        $('input[name="'+_name+'[suburb]"]', _el).val(coz.buyer.suburb);
                        $('input[name="'+_name+'[state]"]', _el).val(coz.buyer.state);
                        $('input[name="'+_name+'[address01]"]', _el).val(coz.buyer.address01);
                        $('input[name="'+_name+'[province]"]', _el).val(coz.buyer.province);
                    }
                }
            })(_name, _el);
        }
    },
    updateAddress : function( country_id, type ){
        coz.getAddressForCountry(country_id , type, function(data){
            if( type == 'ships' ){
                $('[neo-place="addressShip"]', '[data-place="payStep02"]').html(data.html);
            }else if( type == 'trans' ){
                $('[neo-place="addressPayment"]', '[data-place="buyNotSign"]').html(data.html);
            }else if( type == 'user' ){
                $('[neo-place="addressUser"]', '[data-place="paySignAndBuy"]').html(data.html);
            }
            coz.paymentStep.updateEvent(type);
            if( (_name == 'ships' 
                    && typeof coz.buyer != 'undefined') ){
                coz.paymentStep.getShipping(type);
            }
        });
    },
    checkBuyer : function( type ){
        _name = '';
        _el = '';
        if( type == 'trans' ){
            _name = 'trans';
            _el = '[data-place="buyNotSign"]';
        }else if( type == 'ships' ){
            _name = 'ships';
            _el = '[data-place="payStep02"]';
        }else if( type == 'user' ){
            _name = 'user';
            _el = '[data-place="paySignAndBuy"]';
        }

        if( type == 'ships' ){
            _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
            if( !(typeof _no_shipping != 'undefined' 
                && _no_shipping != 'true' && _no_shipping != true) ){
                coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
                return false;
            }
        }

        if($.trim($('input[name="'+_name+'[first_name]"]', _el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="'+_name+'[first_name]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="'+_name+'[first_name]"]', _el).removeClass('ui-form-error');
        }

        if($.trim($('input[name="'+_name+'[last_name]"]', _el).val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_ten'));
            $('input[name="'+_name+'[last_name]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="'+_name+'[last_name]"]', _el).removeClass('ui-form-error');
        }

        if( !coz.isEmail($('input[name="'+_name+'[email]"]', _el).val()) ){
            coz.toast(language.translate('txt_email_khong_hop_le'));
            $('input[name="'+_name+'[email]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="'+_name+'[email]"]', _el).removeClass('ui-form-error');
        }

        if( type == 'user' ){
            if($.trim($('input[name="'+_name+'[password]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_mat_khau'));
                $('input[name="'+_name+'[password]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[password]"]', _el).removeClass('ui-form-error');
            }
            if($.trim($('input[name="'+_name+'[repassword]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_ban_phai_nhap_lai_mat_khau'));
                $('input[name="'+_name+'[repassword]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[repassword]"]', _el).removeClass('ui-form-error');
            }
            if( $('input[name="'+_name+'[password]"]', _el).val() != $('input[name="'+_name+'[repassword]"]', _el).val() ){
                coz.toast(language.translate('txt_ban_phai_nhap_mat_khau_chua_khop'));
                $('input[name="'+_name+'[repassword]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[repassword]"]', _el).removeClass('ui-form-error');
            }
        }

        if( !coz.isPhone($('input[name="'+_name+'[phone]"]', _el).val()) ){
            coz.toast(language.translate('txt_so_dien_thoai_khong_hop_le'));
            $('input[name="'+_name+'[phone]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="'+_name+'[phone]"]', _el).removeClass('ui-form-error');
        }

        if($('[name="'+_name+'[country_id]"]', _el).length<=0 
          || $.trim($('[name="'+_name+'[country_id]"]', _el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_contry'));
            $('[name="'+_name+'[country_id]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('[name="'+_name+'[country_id]"]', _el).removeClass('ui-form-error');
        }

        if($('input[name="'+_name+'[address]"]', _el).length <=0 
            || $.trim($('input[name="'+_name+'[address]"]', _el).val()).length <=0 ){
            coz.toast(language.translate('txt_dia_chi_khong_duoc_bo_trong'));
            $('input[name="'+_name+'[address]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="'+_name+'[address]"]', _el).removeClass('ui-form-error');
        }

        if($('input[name="'+_name+'[city]"]', _el).length>0){
            if($.trim($('input[name="'+_name+'[city]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('input[name="'+_name+'[city]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[city]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('input[name="'+_name+'[zipcode]"]', _el).length>0){
            if($.trim($('input[name="'+_name+'[zipcode]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_zipcode_khong_duoc_bo_trong'));
                $('input[name="'+_name+'[zipcode]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[zipcode]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('input[name="'+_name+'[state]"]', _el).length>0){
            if($.trim($('input[name="'+_name+'[state]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_state_khong_duoc_bo_trong'));
                $('input[name="'+_name+'[state]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[state]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('input[name="'+_name+'[suburb]"]', _el).length>0){
            if($.trim($('input[name="'+_name+'[suburb]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_suburb_khong_duoc_bo_trong'));
                $('input[name="'+_name+'[suburb]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[suburb]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('input[name="'+_name+'[region]"]', _el).length>0){
            if($.trim($('input[name="'+_name+'[region]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_region_khong_duoc_bo_trong'));
                $('input[name="'+_name+'[region]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[region]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('input[name="'+_name+'[province]"]', _el).length>0){
            if($.trim($('input[name="'+_name+'[province]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_province_khong_duoc_bo_trong'));
                $('input[name="'+_name+'[province]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="'+_name+'[province]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('select[name="'+_name+'[cities_id]"]', _el).length>0){
            if($.trim($('select[name="'+_name+'[cities_id]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_city_khong_duoc_bo_trong'));
                $('select[name="'+_name+'[cities_id]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="'+_name+'[cities_id]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('select[name="'+_name+'[districts_id]"]', _el).length>0){
            if($.trim($('select[name="'+_name+'[districts_id]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_districts_khong_duoc_bo_trong'));
                $('select[name="'+_name+'[districts_id]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="'+_name+'[districts_id]"]', _el).removeClass('ui-form-error');
            }
        }

        if($('select[name="'+_name+'[wards_id]"]', _el).length>0){
            if($.trim($('select[name="'+_name+'[wards_id]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_wards_khong_duoc_bo_trong'));
                $('select[name="'+_name+'[wards_id]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="'+_name+'[wards_id]"]', _el).removeClass('ui-form-error');
            }
        }

        return true;
    },
    buyer : function(){
        var formdata = $('form[data-form="buyer"]', '[data-place="buyNotSign"]').serialize();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: coz.baseUrl+'/checkout/buyer?_AJAX=1',
            data: formdata,
            cache: false,
            success: function(data)
            {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                coz.toast(data.msg);
                if( data.flag == 'true'
                    || data.flag == true){
                    $('[data-place="payStep02"]').html(data.html);
                    $('[data-place="payStep04"]').html(data.html_confirm);
                    coz.buyer = data.buyer;
                    coz.paymentStep.gotoPageTwo();
                    coz.paymentStep.stepTwo();
                    coz.paymentStep.stepFour();
                }
            },
            error: function(e)
            {
                console.log(e);
            }
        });
    },
    shipper : function(){
        var formdata = $('form[data-form="shipper"]', '[data-place="payStep02"]').serialize();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: coz.baseUrl+'/checkout/shipper?_AJAX=1',
            data: formdata,
            cache: false,
            success: function(data)
            {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                coz.toast(data.msg);
                if( data.flag == 'true'
                    || data.flag == true){
                    $('[data-place="payStep03"]').html(data.html);
                    $('[data-place="payStep04"]').html(data.html_confirm);
                    coz.shipper = data.shipper;
                    coz.paymentStep.gotoPageThree();
                    coz.paymentStep.stepThree();
                    
                    if( $.trim($('[name="ships[country_id]"]', '[data-place="payStep02"]').val()).length>0 ){
                        var country_id = $('[name="ships[country_id]"]', '[data-place="payStep02"]').val();
                        coz.paymentStep.updateAddress(country_id, 'ships');
                    }
                    coz.paymentStep.stepFour();
                }
            },
            error: function(e)
            {
                console.log(e);
            }
        });
    },
    checkShipping : function(){
        _el = '[data-place="payStep03"]';
        _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
        if( !(typeof _no_shipping != 'undefined' 
            && _no_shipping != 'true' && _no_shipping != true) ){
            coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
            return false;
        }
        if($('input[name="trans[shipping_id]"]:checked', _el).length<=0 
            || $.trim($('input[name="trans[shipping_id]"]:checked', _el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_transportation'));
            $('input[name="trans[shipping_id]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('input[name="trans[shipping_id]"]', _el).removeClass('ui-form-error');
        }

        if($.trim($('select[name="trans[payment_id]"]', _el).val()).length <=0 ){
            coz.toast(language.translate('txt_chua_chon_payment_method'));
            $('select[name="trans[payment_id]"]', _el).addClass('ui-form-error').focus();
            return false;
        }else{
            $('select[name="trans[payment_id]"]', _el).removeClass('ui-form-error');
        }

        if( $('[data-place="Onepay"]', _el).hasClass('active') ){
            if( $('select[name="trans[avs_country]"]', _el).length>0 
                && $.trim($('select[name="trans[avs_country]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_chon_dat_nuoc_phat_hanh_the'));
                $('select[name="trans[avs_country]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('select[name="trans[avs_country]"]', _el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_street01]"]', _el).length>0 
                && $.trim($('input[name="trans[avs_street01]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_dia_chi_phat_hanh_the'));
                $('input[name="trans[avs_street01]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_street01]"]', _el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_city]"]', _el).length>0 
                &&  $.trim($('input[name="trans[avs_city]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_thanh_pho_phat_hanh_the'));
                $('input[name="trans[avs_city]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_city]"]', _el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_stateprov]"]', _el).length>0 
                && $.trim($('input[name="trans[avs_stateprov]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_quan_huyen_phat_hanh_the'));
                $('input[name="trans[avs_stateprov]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_stateprov]"]', _el).removeClass('ui-form-error');
            }

            if( $('input[name="trans[avs_postCode]"]', _el).length>0 
                && $.trim($('input[name="trans[avs_postCode]"]', _el).val()).length <=0 ){
                coz.toast(language.translate('txt_chua_nhap_ma_vung_phat_hanh_the'));
                $('input[name="trans[avs_postCode]"]', _el).addClass('ui-form-error').focus();
                return false;
            }else{
                $('input[name="trans[avs_postCode]"]', _el).removeClass('ui-form-error');
            }
        }

        return true;
    },
    shipping : function(){
        var formdata = $('form[data-form="shipping"]', '[data-place="payStep03"]').serialize();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: coz.baseUrl+'/checkout/shipping?_AJAX=1',
            data: formdata,
            cache: false,
            success: function(data)
            {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                coz.toast(data.msg);
                if( data.flag == 'true'
                    || data.flag == true){
                    $('[data-place="payStep04"]').html(data.html);
                    coz.buyer = data.buyer;
                    coz.paymentStep.gotoPageFour();
                    coz.paymentStep.getFeeTransitions('ships');
                    coz.paymentStep.stepFour();
                }
            },
            error: function(e)
            {
                console.log(e);
            }
        });
    },
    checkLogin : function(){
        if($.trim($('form[data-form="login"] input[name="email"]', '[data-place="payLoginAndBuy"]').val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_username'));
            $('form[data-form="login"] input[name="email"]', '[data-place="payLoginAndBuy"]').focus();
            return false;
        }

        if($.trim($('form[data-form="login"] input[name="password"]', '[data-place="payLoginAndBuy"]').val()).length <=0 ){
            coz.toast(language.translate('txt_ban_phai_nhap_password'));
            $('form[data-form="login"] input[name="password"]', '[data-place="payLoginAndBuy"]').focus();
            return false;
        }

        return true;
    },
    loginAndBuy : function(){
        var formdata = $('form[data-form="login"]', '[data-place="payLoginAndBuy"]').serialize();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: coz.baseUrl+'/login/loginAjack?_AJAX=1',
            data: formdata,
            cache: false,
            success: function(data)
            {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                coz.toast(data.html);
                if( data.flag == 'true'
                    || data.flag == true){
                    window.location.reload(true);
                }
            },
            error: function(e)
            {
                console.log(e);
            }
        });
    },
    signupAndBuy : function(){
        var formdata = $('form[data-form="signup"]', '[data-place="paySignAndBuy"]').serialize();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: coz.baseUrl+'/login/signup?ajax=1&_AJAX=1',
            data: formdata,
            cache: false,
            success: function(data)
            {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                coz.toast(data.html);
                if( data.flag == 'true'
                    || data.flag == true){
                    window.location.reload(true);
                }
            },
            error: function(e)
            {
                console.log(e);
            }
        });
    },
    payment : function(){
        _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
        if( !(typeof _no_shipping != 'undefined' 
            && _no_shipping != 'true' && _no_shipping != true) ){
            coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
            return false;
        }else{
            coz.showLoading();
            return true;
        }
    },
    stepOne : function(){
        $('[data-btn="buyNotSign"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('[data-place="buyNotSign"]').addClass('active').siblings().removeClass('active');
        });

        $('[data-btn="paySignAndBuy"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('[data-place="paySignAndBuy"]').addClass('active').siblings().removeClass('active');
        });

        $('[data-btn="payLoginAndBuy"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('[data-place="payLoginAndBuy"]').addClass('active').siblings().removeClass('active');
        });

        $('[data-place="payLoginAndBuy"] [data-btn="payLogin"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( coz.paymentStep.checkLogin() ){
                coz.paymentStep.loginAndBuy();
            }
        });

        $('select[name="trans[country_id]"]', '[data-place="buyNotSign"]').select2({ width: '100%' }).on("change", function(e) {
            var country_id = $(this).val();
            coz.paymentStep.updateAddress(country_id, 'trans');
        });

        if( $.trim($('[name="trans[country_id]"]', '[data-place="buyNotSign"]').val()).length>0 ){
            var country_id = $('[name="trans[country_id]"]', '[data-place="buyNotSign"]').val();
            coz.paymentStep.updateAddress(country_id, 'trans');
        }

        $('[data-btn="paySignOutAndBuy"]', '[data-place="buyNotSign"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            coz.logout(function(){
                window.location.reload(true);
            });
            return false;
        });

        $('[data-place="buyNotSign"] [data-btn="payBuyer"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( coz.paymentStep.checkBuyer('trans') ){
                coz.paymentStep.buyer();
            }
        });

        $('select[name="user[country_id]"]', '[data-place="paySignAndBuy"]').select2({ width: '100%' }).on("change", function(e) {
            var country_id = $(this).val();
            coz.paymentStep.updateAddress(country_id, 'user');
        });

        if( $.trim($('[name="user[country_id]"]', '[data-place="paySignAndBuy"]').val()).length>0 ){
            var country_id = $('[name="user[country_id]"]', '[data-place="paySignAndBuy"]').val();
            coz.paymentStep.updateAddress(country_id, 'user');
        }
        $('[data-place="paySignAndBuy"] [data-btn="paySignup"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( coz.paymentStep.checkBuyer('user') ){
                coz.paymentStep.signupAndBuy();
            }
        });

        $('[data-btn="loginFacebook"]', '[data-place="payStep01"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            coz.syncFacebook();
            if( coz.hasLogin() ){
                coz.logout(function(){
                    coz.facebook.login();
                });
            }else{
                coz.facebook.login();
            }
            return false;
        });

        $('[data-btn="loginTwister"]', '[data-place="payStep01"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    },
    stepTwo : function(){
        _el = '[data-place="payStep02"]';
        $('select[name="ships[country_id]"]', _el).select2({ width: '100%' }).on("change", function(e) {
            var country_id = $(this).val();
            coz.paymentStep.updateAddress(country_id, 'ships');
        });

        if( $.trim($('[name="ships[country_id]"]', _el).val()).length>0 ){
            var country_id = $('[name="ships[country_id]"]', _el).val();
            coz.paymentStep.updateAddress(country_id, 'ships');
        }

        $('[data-btn="payShipper"]', _el).off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( coz.paymentStep.checkBuyer('ships') ){
                coz.paymentStep.shipper();
            }
        });

        $('.coz-item-nav-tab-step', _el).off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            _href = $(this).attr('href');
            if( !$(this).hasClass('active') 
                && $(_href).length > 0 ){
                $(this).addClass('active').siblings().removeClass('active');
                $(_href).addClass('active').siblings().removeClass('active');
            }
        });
    },
    stepThree : function(){
        _el = '[data-place="payStep03"]';
        $('select[name="trans[payment_id]"]', _el).select2({ width: '100%' }).on("change", function(e) {
            _el = '[data-place="payStep03"]';
            var payment_id = $(this).val();
            if( $.trim(payment_id).length >0 && $('#'+payment_id, _el).length > 0 ){
                $('.coz-content-tab-payment', _el).hide();
                $('.coz-content-tab-payment.active', _el).removeClass('active');
                $('#'+payment_id, _el).show().addClass('active');
            }
        });

        $('select[name="trans[avs_country]"]', _el).select2({ width: '100%' }).on("change", function(e) {
        });

        $('[data-btn="payShippingPayment"]', _el).off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( coz.paymentStep.checkShipping() ){
                coz.paymentStep.shipping();
            }
        });

        $('.coz-item-nav-tab-step', _el).off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            _href = $(this).attr('href');
            if( !$(this).hasClass('active') 
                && $(_href).length > 0 ){
                $(this).addClass('active').siblings().removeClass('active');
                $(_href).addClass('active').siblings().removeClass('active');
            }
        });

        $('select[name="trans[payment_id]"]', _el).trigger('change');
    },
    stepFour : function(){
        _el = '[data-place="payStep04"]';
        $('.date_delivery').datepicker('reset').datepicker('destroy').datepicker({format : 'yyyy-mm-dd'});
        $('form[data-form="corfirm"]', _el).on('submit', function(e){
            _no_shipping = $('form[data-form="shipper"]', '[data-place="payStep02"]').data('no_shipping');
            if( !(typeof _no_shipping != 'undefined' 
                && _no_shipping != 'true' && _no_shipping != true) ){
                coz.toast(language.translate('txt_dia_diem_nay_khong_giao_hang'));
                return false;
            }else{
                return coz.paymentStep.payment();
            }
            return false;
        });

        $('[data-btn="processPayment"]', _el).off('click').on('click', function(e){
            return coz.paymentStep.payment();
        });

        $('[data-btn="toggleBoxCoupon"]', _el).off('click').on('click', function(e){
            $(this).parents('.box-staus-coupon').eq(0).toggleClass('open');
        });
        
        $('[data-btn="moreLinkPayment"]', _el).off('click').on('click', function(e){
            var ctPainPay = $(this).parents('[data-place="moreLinkPayment"]').eq(0).find('[data-place="moreBodyLinkPayment"]').eq(0);
            if( ctPainPay.hasClass('active') ){
                $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> '+language.translate('txt_xem_them_thong_tin_tax_payment'));
                ctPainPay.removeClass('active');
            }else{
                $(this).html('<i class="fa fa-angle-double-right" aria-hidden="true"></i> ' +language.translate('txt_an_thong_tin_tax_payment'));
                ctPainPay.addClass('active');
            }
        });
    },
    togleClassMobile : function(){
        if( $('[data-place="processPayment"]').length > 0){
            _w = $('[data-place="processPayment"]').width();
            if( _w <= 678 ){
                $('body').addClass('coz-payment-mobile');
            }else{
                $('body').removeClass('coz-payment-mobile');
            }
        }
    },
    start : function(){
        $('.coz-item-step').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( !$(this).hasClass('active') ){
                _idx = $(this).index();
                if( _idx == 0 ){
                    coz.paymentStep.gotoPageOne();
                }else if( _idx == 1 ) {
                    coz.paymentStep.gotoPageTwo();
                }else if( _idx == 2 ) {
                    coz.paymentStep.gotoPageThree();
                }else if( _idx == 3 ) {
                    coz.paymentStep.gotoPageFour();
                }
            }
        });

        $('[data-btn="prevStepPayment"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( $('.coz-item-step.active').length > 0 ){
                _idx = $('.coz-item-step.active').index();
                if( _idx == 0 ){
                    coz.paymentStep.gotoPageFour();
                }else if( _idx == 1 ) {
                    coz.paymentStep.gotoPageOne();
                }else if( _idx == 2 ) {
                    coz.paymentStep.gotoPageTwo();
                }else if( _idx == 3 ) {
                    coz.paymentStep.gotoPageThree();
                }
            }
        });

        $('[data-btn="nextStepPayment"]').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( $('.coz-item-step.active').length > 0 ){
                _idx = $('.coz-item-step.active').index();
                if( _idx == 0 ){
                    coz.paymentStep.gotoPageTwo();
                }else if( _idx == 1 ) {
                    coz.paymentStep.gotoPageThree();
                }else if( _idx == 2 ) {
                    coz.paymentStep.gotoPageFour();
                }else if( _idx == 3 ) {
                    coz.paymentStep.gotoPageOne();
                }
            }
        });

        $('.coz-item-nav-tab-step').off('click').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            _href = $(this).attr('href');
            if( !$(this).hasClass('active') 
                && $(_href).length > 0 ){
                $(this).addClass('active').siblings().removeClass('active');
                $(_href).addClass('active').siblings().removeClass('active');
            }
        });

        coz.paymentStep.stepOne();
        coz.paymentStep.stepTwo();
        coz.paymentStep.stepThree();
        coz.paymentStep.stepFour();


        $(window).resize(function(){
            coz.paymentStep.togleClassMobile();
        });
        coz.paymentStep.togleClassMobile();
    }
};
coz.paymentStep.start();

coz.whereAreYou = {
    checkForm: function(){
        return true;
    },
    showpopup : function(){
        $.magnificPopup.open({
            items: {
                src: '#coz-popup-where-are-you',
                type: 'inline'
            },
            showCloseBtn: false,
            callbacks: {
                open: function() {
                },
                close: function() {
                    $('#coz-popup-where-are-you').hide();
                },
                beforeOpen: function() {
                    $('#coz-popup-where-are-you').show();
                },
                change: function() {
                },
                resize: function() {
                },
                beforeClose: function() {
                    if( coz.getConfirmLocation() != 1
                        && !coz.hasLocation() ){
                        var r = confirm(language.translate('txt_ban_ko_muon_hien_thi_popup_nay'));
                        if (r == true) {
                            coz.setConfirmLocation(1);
                        }
                    }
                },
                afterClose: function() {
                },
                imageLoadComplete: function() {
                }
            }
        },0);
    },
    init : function(){
        if( coz.hasWebsite() 
            && coz.getWebsite()['confirm_location'] == 1 ){
            /*var _str = '<div class="coz-popup-where-are-you" id="coz-popup-where-are-you" data-place="whereAreYou" style="display:none" >'+
                '<div class="clearfix coz-inner-popup-where-are-you" >'+
                    '<div class="clearfix coz-logo-popup-where-are-you" >'+
                        '<a href="/" >'+
                            '<img src="'+coz.getWebsite()['logo']+'" class="img-fluid" />'+
                        '</a>'+
                    '</div>'+
                    '<a href="javascript:neoClosePopup();" class="coz-btn-close-popup-where-are-you" ><i class="fa fa-times" aria-hidden="true"></i></a>'+
                    '<div class="clearfix coz-bd-popup-where-are-you" >'+
                        '<h2 class="coz-title-location">'+language.translate('txt_cap_nhat_vi_tri_cua_ban')+'</h2>'+
                        '<p class="coz-note-location" >'+language.translate('txt_cap_nhat_des_vi_tri_cua_ban')+'</p>'+
                        '<div class="clearfix" >'+
                            '<form action="" class="form-where-are-you" data-form="whereAreYou" >'+
                                '<div class="row" >'+
                                    '<div class="col-sm-6" data-place="hasTypeLocation" style="display:none" >'+
                                        '<div class="form-element-payment" >'+
                                            '<div class="row" >'+
                                                '<div class="col-sm-12" >'+
                                                    '<label class="ui-lb-payment">'+language.translate('txt_kieu_vi_tri')+'</label>'+
                                                '</div>'+
                                                '<div class="col-sm-12" >'+
                                                    '<div class="ui-select-payment" >'+
                                                        '<select class="coz-select-payment" name="type_location" id="whay_type_location" >'+
                                                            '<option value="0" >'+language.translate('txt_option_kieu_vi_tri')+'</option>'+
                                                            '<option value="1">'+language.translate('txt_option_residence')+'</option>'+
                                                            '<option value="2">'+language.translate('txt_option_business')+'</option>'+
                                                            '<option value="3">'+language.translate('txt_option_funeral_home')+'</option>'+
                                                            '<option value="4">'+language.translate('txt_option_hospital')+'</option>'+
                                                            '<option value="5">'+language.translate('txt_option_apartment')+'</option>'+
                                                            '<option value="6">'+language.translate('txt_option_school')+'</option>'+
                                                            '<option value="7">'+language.translate('txt_option_church')+'</option>'+
                                                        '</select>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="row" >'+    
                                    '<div class="col-sm-6" data-place="hasCountry" >'+
                                        '<div class="form-element-payment" data-place="country" style="display:none" >'+
                                            '<div class="row" >'+
                                                '<div class="col-sm-12" >'+
                                                    '<label class="ui-lb-payment">'+language.translate('txt_dat_nuoc')+'</label>'+
                                                '</div>'+
                                                '<div class="col-sm-12" >'+
                                                    '<div class="ui-select-payment" >'+
                                                        '<select class="coz-select-payment" name="country_id" id="whay_country" >'+
                                                            '<option value="0" >'+language.translate('txt_option_tat_ca_country')+'</option>'+
                                                        '</select>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="form-element-payment" data-place="noZipcode" style="margin-bottom:20px;display:none" >'+
                                    '<div class="row" >'+
                                        '<div class="col-sm-6"  data-place="hasCities" >'+
                                            '<label class="ui-lb-payment">'+language.translate('txt_thanh_pho')+'</label>'+
                                            '<div class="ui-select-payment" >'+
                                                '<select class="coz-select-payment" name="cities_id"  id="whay_city" >'+
                                                    '<option value="0" >'+language.translate('txt_option_tat_ca_cities')+'</option>'+
                                                '</select>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-sm-6" data-place="hasDistricts" >'+
                                            '<label class="ui-lb-payment">'+language.translate('txt_quan_huyen')+'</label>'+
                                            '<div class="ui-select-payment" >'+
                                                '<select class="coz-select-payment" name="districts_id"  id="whay_distrist" >'+
                                                    '<option value="0" >'+language.translate('txt_option_tat_ca_districts')+'</option>'+
                                                '</select>'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-sm-4" data-place="hasWard" style="display:none" >'+
                                            '<label class="ui-lb-payment">'+language.translate('txt_phuong_xa')+'</label>'+
                                            '<div class="ui-select-payment" >'+
                                                '<select class="coz-select-payment" name="wards_id"  id="whay_ward" >'+
                                                    '<option value="0" >'+language.translate('txt_option_tat_ca_wards')+'</option>'+
                                                '</select>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-element-payment" data-place="txtOpenCountry" style="display:none" >'+
                                        '<a href="javascript:void(0);" class="btn-open-country-where-are-you" data-btn="openCountry" >'+
                                            language.translate('txt_ban_o_ngoai_vung')+
                                        '</a>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="form-element-payment" data-place="hasZipcode" style="margin-bottom:20px;display:none" >'+
                                    '<div class="row" >'+
                                        '<div class="col-sm-6" >'+
                                            '<label class="ui-lb-payment">'+language.translate('txt_thanh_pho')+'</label>'+
                                            '<div class="ui-input-payment" >'+
                                                '<input type="text" class="input-payment" name="city"  id="whay_city" >'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-sm-6" >'+
                                            '<label class="ui-lb-payment">'+language.translate('txt_zipcode')+'</label>'+
                                            '<div class="ui-input-payment" >'+
                                                '<input type="text" class="input-payment" name="zipcode"  id="whay_zipcode" >'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+

                                '<div class="form-element-payment" >'+
                                    '<div class="row" >'+
                                        '<div class="col-sm-offset-3 col-sm-6" >'+
                                            '<a href="javascript:void(0);" data-btn="appLocation" class="coz-btn-flat coz-btn-flat-active coz-btn-flat-block" >'+
                                                language.translate('txt_app_location')+
                                            '</a>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+

                            '</form>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>';
            $('body').append(_str);*/

            $('[data-place="whereAreYou"] [name="country_id"]').off('change').on('change', function(){
                if( $('[data-place="whereAreYou"] [name="cities_id"]').is('select') ){
                    $('[data-place="whereAreYou"] [data-place="noZipcode"]').hide();
                    $('[data-place="whereAreYou"] [data-place="hasCities"]').hide();
                    $('[data-place="whereAreYou"] [data-place="hasDistricts"]').hide();
                    $('[data-place="whereAreYou"] [data-place="hasWard"]').hide();
                    $('[data-place="whereAreYou"] select[name="cities_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_cities')+'</option>');
                    $('[data-place="whereAreYou"] select[name="districts_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_districts')+'</option>');
                    $('[data-place="whereAreYou"] select[name="wards_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_wards')+'</option>');
                    $('[data-place="whereAreYou"] input[name="city"]').val('');
                    $('[data-place="whereAreYou"] input[name="zipcode"]').val('');
                    coz.showLoadingInner('[data-place="whereAreYou"]');
                    country_id = $(this).val();
                    if( typeof country_id != 'undefined' 
                        && $.trim(country_id).length > 0 ){
                        coz.loadCity( country_id , function( datas ){
                            if( typeof datas.success != 'undefined'
                                &&  (datas.success == 'true' || datas.success == true) ){
                                if(datas.results.length>0){
                                    $('[data-place="whereAreYou"] [data-place="noZipcode"]').show();
                                    $('[data-place="whereAreYou"] [data-place="hasZipcode"]').hide();
                                    $('[data-place="whereAreYou"] [data-place="hasCities"]').show();
                                    $.each(datas.results, function(i, row){
                                        $('[data-place="whereAreYou"] select[name="cities_id"]').append($("<option "+(row.cities_id == coz.getLocationCitiesId() ? 'selected="selected"' : '' )+" ></option>").attr('value', row.cities_id).text(row.cities_title));
                                        if(i>=datas.results.length-1){
                                            $('[data-place="whereAreYou"] select[name="cities_id"]').trigger('change');
                                        }
                                    });
                                }else{
                                    $('[data-place="whereAreYou"] [data-place="hasZipcode"]').show();
                                    $('[data-place="whereAreYou"] [data-place="noZipcode"]').hide();
                                }
                            }
                            coz.hideLoadingInner();
                        });
                    }else{
                        coz.hideLoadingInner();
                    }
                }else if( $.trim($('[data-place="whereAreYou"] [name="cities_id"]').val()).length > 0 ){
                    $('[data-place="whereAreYou"] [name="cities_id"]').trigger('change');
                }
            });

            $('[data-place="whereAreYou"] select[name="cities_id"]').off('change').on('change', function(){
                if( $('[data-place="whereAreYou"] [name="districts_id"]').is('select') ){
                    $('[data-place="whereAreYou"] [data-place="hasDistricts"]').hide();
                    $('[data-place="whereAreYou"] [data-place="hasWard"]').hide();
                    $('[data-place="whereAreYou"] select[name="districts_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_districts')+'</option>');
                    $('[data-place="whereAreYou"] select[name="wards_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_wards')+'</option>');
                    coz.showLoadingInner('[data-place="whereAreYou"]');
                    cities_id = $(this).val();
                    if( typeof cities_id != 'undefined' 
                        && $.trim(cities_id).length > 0 ){
                        coz.loadDistrict( cities_id , function( datas ){
                            if( typeof datas.success != 'undefined'
                                &&  (datas.success == 'true' || datas.success == true) ){
                                if(datas.results.length>0){
                                    $('[data-place="whereAreYou"] [data-place="hasDistricts"]').show();
                                    $.each(datas.results, function(i, row){
                                        $('[data-place="whereAreYou"] select[name="districts_id"]').append($("<option "+(row.districts_id == coz.getLocationDistrictsId() ? 'selected="selected"' : '' )+" ></option>").attr('value', row.districts_id).text(row.districts_title));
                                        if(i>=datas.results.length-1){
                                            $('[data-place="whereAreYou"] select[name="districts_id"]').trigger('change');
                                        }
                                    });
                                }
                            }
                            coz.hideLoadingInner('[data-place="whereAreYou"]');
                        });
                    }else{
                        coz.hideLoadingInner('[data-place="whereAreYou"]');
                    }
                }else if( $.trim($('[data-place="whereAreYou"] [name="districts_id"]').val()).length > 0 ){
                    $('[data-place="whereAreYou"] [name="districts_id"]').trigger('change');
                }
            });

            $('[data-place="whereAreYou"] select[name="districts_id"]').off('change').on('change', function(){
                $('[data-place="whereAreYou"] select[name="wards_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_wards')+'</option>');
                coz.showLoadingInner('[data-place="whereAreYou"]');
                districts_id = $(this).val();
                if( typeof districts_id != 'undefined' 
                    && $.trim(districts_id).length > 0 ){
                    coz.loadWard( districts_id , function( datas ){
                        if( typeof datas.success != 'undefined'
                            &&  (datas.success == 'true' || datas.success == true) ){
                            if(datas.results.length>0){
                                $.each(datas.results, function(i, row){
                                    $('[data-place="whereAreYou"] select[name="wards_id"]').append($("<option "+(row.wards_id == coz.getLocationWardsId() ? 'selected="selected"' : '' )+" ></option>").attr('value', row.wards_id).text(row.wards_title));
                                });
                            }
                        }
                        coz.hideLoadingInner('[data-place="whereAreYou"]');
                    });
                }else{
                    coz.hideLoadingInner('[data-place="whereAreYou"]');
                }
            });

            /*if( $('[data-place="whereAreYou"] [name="country_id"]').is('select') ){
                coz.showLoadingInner('[data-place="whereAreYou"]');
                coz.loadContryForWebsite(function( datas ){
                    if( typeof datas.results != 'undefined'
                        && datas.results.length > 0 ){
                        $('[data-place="whereAreYou"] [data-place="hasTypeLocation"]').show();
                        $('[data-place="whereAreYou"] [name="country_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_country')+'</option>');
                        $('[data-place="whereAreYou"] select[name="cities_id]"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_cities')+'</option>');
                        $('[data-place="whereAreYou"] select[name="districts_id]"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_districts')+'</option>');
                        $('[data-place="whereAreYou"] select[name="wards_id"]').html('<option value="0" >'+language.translate('txt_option_tat_ca_wards')+'</option>');
                        $.each(datas.results, function(i, row){
                            id_default = 0;
                            if( row.is_default == 1){
                                id_default = row.id;
                            }
                            $('[data-place="whereAreYou"] [name="country_id"]').append($("<option "+((row.id == coz.getLocationCountryId() || row.is_default == 1 ) ? 'selected="selected"' : '' )+" ></option>").attr('value', row.id).text(row.title));
                            if(i>=datas.results.length-1){
                                $('[data-place="whereAreYou"] select[name="country_id"]').trigger('change');
                                $('[data-place="whereAreYou"] [data-btn="openCountry"]').html(language.translate('txt_ban_o_ngoai_vung')+ $('[data-place="whereAreYou"] [name="country_id"]>option[value="'+$('[data-place="whereAreYou"] [name="country_id"]').val()+'"]').text());
                                if( coz.getLocationCountryId() >0 && coz.getLocationCountryId() != id_default ){
                                    $('[data-place="whereAreYou"] [data-place="country"]').show();
                                    $('[data-place="whereAreYou"] [data-place="txtOpenCountry"]').hide();
                                }else{
                                    $('[data-place="whereAreYou"] [data-place="country"]').hide();
                                    $('[data-place="whereAreYou"] [data-place="txtOpenCountry"]').show();
                                }
                            }
                        });
                    }
                    coz.hideLoadingInner('[data-place="whereAreYou"]');
                });
            }else if( $.trim($('[data-place="whereAreYou"] [name="country_id"]').val()).length > 0 ){
                $('[data-place="whereAreYou"] [name="country_id"]').trigger('change');
            }*/

            if( $('[data-place="whereAreYou"] [name="country_id"]').val() <=0 
                || $.trim($('[data-place="whereAreYou"] [name="country_id"]')).length <=0 ){
                $('[data-place="whereAreYou"] [data-place="country"]').show();
                $('[data-place="whereAreYou"] [data-place="txtOpenCountry"]').hide();
            }else{
                $('[data-place="whereAreYou"] [data-place="country"]').hide();
                $('[data-place="whereAreYou"] [data-place="txtOpenCountry"]').show();
            }
            $('[data-place="whereAreYou"] [name="country_id"]').trigger('change');

            $('[data-place="whereAreYou"] [data-btn="openCountry"]').off('click').on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                $('[data-place="whereAreYou"] [data-place="country"]').show();
                $('[data-place="whereAreYou"] [data-place="txtOpenCountry"]').hide();
                $('[data-place="whereAreYou"] select[name="country_id"]').focus();
            });

            $('[data-place="whereAreYou"] [data-btn="appLocation"]').off('click').on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if( coz.whereAreYou.checkForm() ){
                    var formdata = $('[data-place="whereAreYou"] form[data-form="whereAreYou"]').serialize();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: coz.baseUrl+'/location?_AJAX=1',
                        data: formdata,
                        cache: false,
                        success: function(data)
                        {
                            if(data.constructor === String){
                                data = $.parseJSON(data);
                            }
                            coz.toast(data.msg);
                            if( typeof data.flag != 'true'
                                && typeof data.flag != true ){
                                coz.location = data.location;
                                coz.setCookie('location', JSON.stringify(coz.location), 30);
                                coz.setConfirmLocation(1);
                                window.location.reload(true);
                            }
                        },
                        error: function(e)
                        {
                            console.log(e);
                        }
                    });
                    return false;
                }
            });

            if( coz.getConfirmLocation() != 1
                && !coz.hasLocation() ){
                coz.whereAreYou.showpopup();
            }

            if( $('[data-place="toolbar"]').length <= 0 ){
                coz.menuMega.addMenu( 'coz.whereAreYou.showpopup()' , language.translate('txt_loc_san_pham_theo_vi_tri'), '<i class="fa fa-map-marker" aria-hidden="true"></i>' );
            }else{
                var str_ = '<div class="coz-toolbar-inline clearfix" >'+
                                '<div class="coz-toolbar-inline-inner" >'+
                                    '<span class="coz-item-toolbar-lime" >'+
                                        '<span class="coz-lb-toolbar" >'+
                                            language.translate('txt_vi_tri_hien_tai_cua_ban')+
                                        '</span>'+
                                        '<span class="coz-value-toolbar" onclick="coz.whereAreYou.showpopup()" >'+
                                            '<i class="fa fa-pencil" aria-hidden="true"></i>'+
                                            '<span class="coz-invalue-toolbar" >'+
                                                coz.getLocationAddress( '<i class="fa fa-question-circle-o" aria-hidden="true"></i>' )+
                                            '</span>'+
                                        '</span>'+
                                    '</span>'+
                                '</div>'+
                            '</div>';
                $('[data-place="toolbar"]').html(str_);
            }
        }
    }
};

coz.updateCartMini();

coz.owlCarousel = {
    updateClass : function(el, cl , type){
        el.each(function(){
            if( type == 0){
                $(this).addClass(cl);
            }else{
                $(this).removeClass(cl);
            }
        });
    },
    updateStatus : function(event){
        if( typeof event.target != 'undefined' ){
            var element   = event.target;
            var name      = event.type;
            var namespace = event.namespace;
            var items     = event.item.count;
            var item      = event.item.index;

            var pages     = event.page.count;
            var page      = event.page.index;
            var size      = event.page.size;

            var ownName = $(element).attr('data-own-name');
            var ownSync = $(element).attr('data-own-sync');

            if( item <=0 ){
                coz.owlCarousel.updateClass( $('[data-neo="navOwl"][data-target="'+ownName+'"][data-direct="prev"]'), 'nav-owl-disabled', 0);
                $(element).find('.owl-prev').addClass('nav-owl-disabled');
            }else{
                coz.owlCarousel.updateClass( $('[data-neo="navOwl"][data-target="'+ownName+'"][data-direct="prev"]'), 'nav-owl-disabled', 1);
                $(element).find('.owl-prev').removeClass('nav-owl-disabled');
            }
            if( item >= (items - size) ){
                coz.owlCarousel.updateClass( $('[data-neo="navOwl"][data-target="'+ownName+'"][data-direct="next"]'), 'nav-owl-disabled', 0);
                $(element).find('.owl-next').addClass('nav-owl-disabled');
            }else{
                coz.owlCarousel.updateClass( $('[data-neo="navOwl"][data-target="'+ownName+'"][data-direct="next"]'), 'nav-owl-disabled', 1);
                $(element).find('.owl-next').removeClass('nav-owl-disabled');
            }
            if( typeof ownSync != 'undefined' ){
                $('[data-neo="navOwl"][data-own-name="'+ownSync+'"]').trigger('to.owl.carousel', item, 300, true);
            }
        }
    },
    initE : function(){
        $(document).on('click', '[data-neo="navOwl"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var direct = $(this).data('direct');
            var target = $(this).data('target');
            if( typeof direct != 'undefined'
                && direct != ''
                && typeof target != 'undefined'
                && typeof target != ''
                && $('[data-own-name="'+target+'"]').length > 0 ){
                if( direct == 'next' ){
                    $('[data-own-name="'+target+'"]').data('owlCarousel').next();
                }else if( direct == 'prev' ){
                    $('[data-own-name="'+target+'"]').data('owlCarousel').prev();
                }
            }

        });
    },
    init : function(){
        if( $('[data-neo="owlCarousel"]').not('.pjax-init').length > 0 ){
            $('[data-neo="owlCarousel"]').not('.pjax-init').each(function(index, el) {
                if( typeof $(el).data('owlCarousel') == 'undefined' ){
                    var config = $(el).data();
                    var navText = [];
                    if( typeof config.navtextleft != 'undefined' ){
                        navText.push(config.navtextleft);
                    }
                    if( typeof config.navtextright != 'undefined' ){
                        navText.push(config.navtextright);
                    }
                    if( navText.length >0 ){
                        config.navText = navText;
                    }
                    config.callbacks = true;
                    config.onInitialized = function( e ){
                        (function(el){
                            setTimeout(function(){
                                var dataOwl = el.data('owlCarousel');
                                if( typeof dataOwl != 'undefined' ){
                                    var items_ = el.find('.owl-item').length;
                                    var item_ = 0;
                                    var size_ = el.find('.owl-item.active').length;
                                    var ownName = el.attr('data-own-name');
                                    var ownSync = el.attr('data-own-sync');

                                    coz.owlCarousel.updateClass( $('[data-neo="navOwl"][data-target="'+ownName+'"][data-direct="prev"]'), 'nav-owl-disabled', 0);
                                    el.find('.owl-prev').addClass('nav-owl-disabled');
                                    if( item_ >= (items_-size_) ){
                                        coz.owlCarousel.updateClass( $('[data-neo="navOwl"][data-target="'+ownName+'"][data-direct="next"]'), 'nav-owl-disabled', 0);
                                        el.find('.owl-next').addClass('nav-owl-disabled');
                                    }
                                }
                            }, 200);
                        })($(el));
                    };
                    $(el).owlCarousel(config).addClass('pjax-init').on('resized.owl.carousel', function(event) {
                    }).on('refreshed.owl.carousel', function(event) {
                    }).on('changed.owl.carousel', function(event) {
                        coz.owlCarousel.updateStatus(event);
                    });
                }
            });
        }
    }
}
coz.owlCarousel.initE();
coz.owlCarousel.init();

(function($) {
    jQuery.fn.coz = function(options) {
        var _model = {
            id : 0,
            extention : [],
            type : 0,
            images : [],
            quantity : 1,
        };

        var options = jQuery.extend({}, options);

        function getSubTotal( callback ) {
            if( typeof coz.model != 'undefined'
                && typeof coz.model.products != 'undefined'
                && typeof coz.model.products[_model.id] != 'undefined' ){
                c_product = coz.model.products[_model.id];
                _product = c_product.product;
                var price_tmp = coz.getFloat(_product.price_sale);
                var price_root = coz.getFloat(_product.price);
                total_price_extention = 0;
                if( typeof _product.total_price_extention != 'undefined' 
                    && _product.total_price_extention != null
                    && coz.getFloat(_product.total_price_extention) > 0 ){
                    total_price_extention = _product.total_price_extention;
                }

                if( typeof _product.products_type_id != 'undefined'
                    && coz.getInt(_product.products_type_id) > 0
                    && typeof c_product.types != 'undefined'
                    && c_product.types.length > 0
                    && _model.type > 0 ){
                    products_type = _product.products_type;
                    price_tmp = 0;
                    price_root = 0;
                    for (var i = 0; i < c_product.types.length; i++) {
                        _type = c_product.types[i];
                        if( _model.type == _type.products_type_id ){
                            price_tmp = _model.quantity*(coz.getFloat(_type.price_sale) + coz.getFloat(total_price_extention));
                            price_root = _model.quantity*(coz.getFloat(_type.price) + coz.getFloat(total_price_extention));
                            break;
                        }
                    };
                }else{
                    if( typeof _product.products_type_id != 'undefined' 
                      && coz.getInt(_product.products_type_id) > 0 ){
                        price_tmp = _model.quantity*(coz.getFloat(_product.t_price_sale) + coz.getFloat(total_price_extention));
                        price_root = _model.quantity*(coz.getFloat(_product.price) + coz.getFloat(total_price_extention));
                    }else{
                        price_tmp = _model.quantity*(coz.getFloat(_product.price_sale) + coz.getFloat(total_price_extention));
                        price_root = _model.quantity*(coz.getFloat(_product.price) + coz.getFloat(total_price_extention));
                    }
                }

                if( typeof c_product.extensions != 'undefined'
                    && c_product.extensions.length > 0 ){

                    for (var _i = 0; _i < c_product.extensions.length; _i++) {
                        extension_tmp = c_product.extensions[_i];
                        _index = _model.extention.indexOf(extension_tmp.id);
                        if( _index > -1 && extension_tmp.is_always == 0 ){
                            price_tmp = coz.getFloat(price_tmp) + coz.getFloat(extension_tmp.price);
                            price_root = coz.getFloat(price_root) + coz.getFloat(extension_tmp.price);
                        }
                    };
                }

                if( typeof callback == 'function' ){
                    callback({
                        price_total:price_tmp, 
                        price_total_orig:price_root
                    });
                }
            }
        };

        function updateView(){
            try{
                $('[data-place="noExtension"]').each(function(){
                    var _id = $(this).attr('data-id');
                    _index = _model.extention.indexOf(_id);
                    if( _index < 0 ){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                });
            }catch(e){}

            try{
                $('[data-place="hasExtension"]').each(function(){
                    var _id = $(this).attr('data-id');
                    _index = _model.extention.indexOf(_id);
                    if( _index > -1 ){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                });
            }catch(e){}
            try{
                if( $('[data-model="imageOwl"]').length >0 && typeof _model.images != 'undefined' ){
                    $('[data-model="imageOwl"]').data('owlCarousel').destroy();
                    $('[data-model="imageOwl"]').removeClass('pjax-init').html('');
                    for( i=0; i< _model.images.length; i++){
                        tr =    '<div class="item" >'+
                                    '<img src="'+_model.images[i].image+'" alt="" itemprop="image" class="img-fluid" />'+
                                '</div>';
                        $('[data-model="imageOwl"]').append(tr);
                        if( i >=_model.images.length-1 ){
                            coz.owlCarousel.init();
                        }
                    }
                }
            }catch(e){}
            getSubTotal(function(data){
                $('[data-model="priceSale"]').html(coz.fomatCurrency(data.price_total));
                $('[data-model="price"]').html(coz.fomatCurrency(data.price_total_orig));
            });
        };

        function addExtension( id ){
            _index = _model.extention.indexOf(id);
            if( _index < 0 ){
                _model.extention.push(id);
                updateView();
            }
        };

        function removeExtension( id ){
            _index = _model.extention.indexOf(id);
            if( _index > -1 ){
                _model.extention.splice(_index, 1);
                updateView();
            }
        };

        try {
            return this.each(function() {
                var obj = $(this);
                _id = obj.addClass('pjax-init').attr('data-id');
                _model.id = _id;
                _ql = $('[data-model="quantity"]', obj).val();
                _ql = coz.getInt(_ql);
                if( _ql < 1){
                    _ql = 1;
                }
                _model.quantity = _ql;
                _model.type = coz.getInt($('[data-model="type"]', obj).val());
                _model.images = $('[data-model="type"]:checked', obj).data('images');
                $('[data-btn="increaseQuantity"]', obj).off('click').on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var ql = $('[data-model="quantity"]', obj).val();
                    ql = coz.getInt(ql);
                    ql = parseInt(ql) + 1;
                    $('[data-model="quantity"]', obj).val(ql).trigger('change');
                });

                $('[data-btn="reducedQuantity"]', obj).off('click').on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var ql = $('[data-model="quantity"]', obj).val();
                    ql = coz.getInt(ql);
                    if( ql > 1 ){
                        ql = parseInt(ql) - 1;
                        $('[data-model="quantity"]', obj).val(ql).trigger('change');
                    }
                });

                $('[data-model="quantity"]', obj).on('propertychange change keyup paste input' , function(){
                    _model.quantity = $(this).val();
                    updateView();
                });

                $('[data-model="type"]', obj).off('change').on('change', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var _type = $(this).val();
                    _model.type = _type;
                    _model.images = $(this).data('images');
                    updateView();
                });

                $('[data-model="extention"]', obj).off('change').on('change', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                });

                $('[data-btn="appExtension"]', obj).off('click').on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var _id = $(this).attr('data-id');
                    if( typeof _id != 'undefined'
                        && _id != 'undefined'
                        && $.trim(_id).length >0 ){
                        $('[data-model="extention"][data-extention_id="'+_id+'"]', obj).attr('checked', true);
                        addExtension(_id);
                    }
                });

                $('[data-btn="cancelExtension"]', obj).off('click').on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var _id = $(this).attr('data-id');
                    if( typeof _id != 'undefined'
                        && _id != 'undefined'
                        && $.trim(_id).length >0 ){
                        $('[data-model="extention"][data-extention_id="'+_id+'"]', obj).attr('checked', false);
                        removeExtension(_id);
                    }
                });
            });
        }
        catch (ex) {
            return false;
        }
    };
})(jQuery);
$('[data-controller="product"]').not('.pjax-init').coz();

coz.feature = {
    hasFeature : function(){
        if( typeof coz.model.fillter != undefined
            && typeof coz.model.fillter.features != 'undefined'
            && typeof coz.model.fillter.features[0] != 'undefined'
            &&  coz.model.fillter.features[0].length > 0){
            return true;
        }
        return false;
    },
    getRoot : function(){
        if( coz.feature.hasFeature() ){
            return coz.model.fillter.features[0];
        }
        return [];
    },
    getID : function( fe ){
        if( coz.feature.hasFeature()
            && typeof  fe != 'undefined'
            && typeof  fe.feature_id != 'undefined'
            && $.trim(fe.feature_id).length > 0 ){
            return fe.feature_id;
        }
        return '';
    },
    getName : function( fe ){
        if( coz.feature.hasFeature()
            && typeof  fe != 'undefined'
            && typeof  fe.feature_title != 'undefined'
            && $.trim(fe.feature_title).length > 0 ){
            return fe.feature_title;
        }
        return '';
    },
    getNum : function( fe ){
        return 0;
    },
    isChecked : function( fe ){
        if( coz.feature.hasFeature()
            && typeof  fe != 'undefined'
            && typeof  fe.is_checked != 'undefined' ){
            return fe.is_checked;
        }
        return false;
    },
    isClose : function( fe ){
        if( coz.feature.hasFeature()
            && typeof  fe != 'undefined'
            && typeof  fe.is_close != 'undefined' ){
            return fe.is_close;
        }
        return false;
    },
    hasChild : function( feature_id ){
        if( coz.feature.hasFeature()
            && typeof coz.model.fillter.features[feature_id] != 'undefined'
            &&  coz.model.fillter.features[feature_id].length > 0 ){
            return true;
        }
        return false;
    },
    getChild : function( feature_id ){
        if( coz.feature.hasFeature()
            && typeof coz.model.fillter.features[feature_id] != 'undefined'
            &&  coz.model.fillter.features[feature_id].length > 0 ){
            return coz.model.fillter.features[feature_id];
        }
        return '';
    }
}
coz.model.fillter= {
    is_pjax : false
};
coz.model.fillter.sort = {
    val : 'price_asc',
    listOption : [
        {   value : 'price_asc' , text : 'txt_sort_ascending'}, 
        {   value : 'price_desc' , text : 'txt_sort_price_reduced'}, 
        {   value : 'new' , text : 'txt_sort_newest'}, 
        {   value : 'old' , text : 'txt_sort_oldest'}, 
        {   value : 'az' , text : 'txt_sort_az'}, 
        {   value : 'za' , text : 'txt_sort_za'},
    ],
    is_close : false,
    isClose : function(){
        return coz.model.fillter.price.is_close;
    },
    getOptions : function(){
        return coz.model.fillter.sort.listOption;
    },
    isSelected : function( op ){
        if( typeof op != 'undefined'
            && typeof op.value != 'undefined'
            && op.value ==  coz.model.fillter.sort.val ){
            return true;
        }
        return false;
    },
    getValue : function( op ){
        if( typeof op != 'undefined'
            && typeof op.value != 'undefined'
            && $.trim(op.value).length > 0 ){
            return op.value;
        }
        return '';
    },
    getText : function( op ){
        if( typeof op != 'undefined'
            && typeof op.text != 'undefined'
            && $.trim(op.text).length > 0 ){
            return language.translate(op.text);
        }
        return '';
    }
};
coz.model.fillter.price = {
    min : 0,
    max : 2000000,
    from : 0,
    to : 2000000,
    is_close : false,
    isClose : function(){
        return coz.model.fillter.price.is_close;
    },
    getMin : function(){
        return coz.model.fillter.price.min;
    },
    getMax : function(){
        return coz.model.fillter.price.max;
    },
    getFrom : function(){
        return coz.model.fillter.price.from;
    },
    getTo : function(){
        return coz.model.fillter.price.to;
    },
    getVal : function(){
        return coz.model.fillter.price.getFrom()+';'+coz.model.fillter.price.getTo();
    }
};
coz.hasFillter = function(){
    if( $('[data-place="fillterFeature"]').length > 0
        || $('[data-place="fillterSort"]').length > 0 ){
        return true;
    }
    return false;
};
coz.getFeatureForPage = function( callback ){
    if( !coz.feature.hasFeature() 
        && $('[data-place="fillterFeature"]').length > 0 ){
        var id_ = $('[data-place="fillterFeature"]').data('id');
        var url = coz.baseUrl+'/features';
        var type_ = '';
        if( typeof id_ != 'undefined'
            && $.trim(id_).length > 0 ){
            url += '/'+id_;
        }
        $.ajax({
            type: "GET",
            dataType: "json",
            url: url,
            data: {_AJAX:1},
            cache: false,
            success: function(data)
            {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                coz.addFeaturesModelSort(data, type_);
            },
            error: function(e)
            {
                console.log(e);
                if( typeof callback != 'function' ){
                    //callback();
                }
            }
        });
    }
};
coz.urlForFilters = function(){
    var url_ = 'partial=1';
    if( coz.feature.hasFeature()  ){
        url_ += '&feature=';
        for ( _i=0; _i < Object.keys(coz.model.fillter.features).length; _i++ ) {
            _key = Object.keys(coz.model.fillter.features)[_i];
            _p = coz.model.fillter.features[_key];
            for(j=0; j< _p.length; j++){
                if( typeof coz.model.fillter.features[_key][j]['is_checked'] != 'undefined'
                    && coz.model.fillter.features[_key][j]['is_checked'] == true ){
                    url_ += coz.model.fillter.features[_key][j]['feature_id']+';';
                }
            }
        }
        url_ = url_.slice(0, -1);
    }

    if( $('[data-place="fillterSort"]').length > 0
        && typeof coz.model.fillter.sort.val != 'undefined'
        && $.trim(coz.model.fillter.sort.val).length >0  ){
        url_ += '&sort='+coz.model.fillter.sort.val;
    }

    if( $('[data-place="itemFillter"][data-id="price"]').length > 0 ){
        url_ += '&price='+coz.model.fillter.price.getVal();
    }

    var pv = coz.QueryString();
    if( typeof pv != 'undefined' ){
        for(k=0;    k< Object.keys(pv).length; k++){
            ku = Object.keys(pv)[k];
            if( $.trim(ku).length >0
                && typeof pv[ku] != 'undefined'
                && $.trim(pv[ku]).length >0
                && ku != 'partial' 
                && ku != 'feature' 
                && ku != 'sort' 
                && ku != 'price' ){
                url_ += '&'+ku+'='+pv[ku];
            }
        }
    }
    href = window.location.href;
    exhref_ = href.split('?');
    link = exhref_[0];
    return link +'?'+ url_;
};
coz.updateSelectFeature = function( fe ){
    afe = fe.split(';');
    if( afe.length > 0
        && coz.feature.hasFeature() ){
        for ( _i=0; _i < Object.keys(coz.model.fillter.features).length; _i++ ) {
            _key = Object.keys(coz.model.fillter.features)[_i];
            _p = coz.model.fillter.features[_key];
            for(j=0; j< _p.length; j++){
                if( typeof coz.model.fillter.features[_key][j]['feature_id'] != 'undefined'
                    && (coz.model.fillter.features[_key][j]['feature_id'] == afe || afe.indexOf(coz.model.fillter.features[_key][j]['feature_id']) >=0 ) ){
                    coz.model.fillter.features[_key][j]['is_checked'] = true;
                }
            }
        }
    }
};
coz.updateSelectSort = function( sort_ ){
    coz.model.fillter.sort.val = sort_;
};
coz.updateSelectPrice = function( price_ ){
    ap = price_.split(';');
    if( ap.length > 1 ){
        coz.model.fillter.price.from = ap[0];
        coz.model.fillter.price.to = ap[1];
    }
};
coz.updateSelectFilter = function( callback ){
    var pv = coz.QueryString();
    if( typeof pv != 'undefined' ){
        for(k=0;    k< Object.keys(pv).length; k++){
            if( typeof pv.feature != 'undefined'
                && $.trim(pv.feature).length >0 ){
                coz.updateSelectFeature(pv.feature);
            }
            if( typeof pv.sort != 'undefined'
                && $.trim(pv.sort).length >0 ){
                coz.updateSelectSort(pv.sort);
            }
            if( typeof pv.price != 'undefined'
                && $.trim(pv.price).length >0 ){
                coz.updateSelectPrice(pv.price);
            }
            if( k>= Object.keys(pv).length-1 ){
                callback(pv);
            }
        }
    }
};
coz.updateFilter = function(){
    if( coz.hasFillter() ){
        if( $('[data-place="fillterFeature"]').length > 0 ){
            _html = $("#tmplFillterFeature").tmpl(coz.model.fillter).html();
            $('[data-place="fillterFeature"]').each(function(k, el){
                $(el).html(_html);
                try{
                    if( $('[data-neo="ionRangeSlider"]', el).length>0 ){
                        var $ionRangeSlider = $('[data-neo="ionRangeSlider"]', el);
                        $ionRangeSlider.ionRangeSlider({
                            type: "double",
                            min: coz.model.fillter.price.min,
                            max: coz.model.fillter.price.max,
                            grid: true,
                            onStart: function (data) {},
                            onChange: function (data) {
                                $('[data-place="valueRangerFrom"]').html(coz.fomatCurrency(data.from));
                                $('[data-place="valueRangerTo"]').html(coz.fomatCurrency(data.to));
                            },
                            onFinish: function (data) {
                                coz.model.fillter.price.from = data.from;
                                coz.model.fillter.price.to = data.to;
                                coz.model.fillter.is_pjax = true;
                                coz.updateFilter();
                            },
                            onUpdate: function (data) {}
                        });
                    }
                }catch(e){console.log(e);};
                $('.coz-item-check', el).off('click').on('click', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var id_ = $(this).data('id');
                    if( $.trim(id_).length > 0
                        && coz.feature.hasFeature() ){
                        for ( _i=0; _i < Object.keys(coz.model.fillter.features).length; _i++ ) {
                            _key = Object.keys(coz.model.fillter.features)[_i];
                            _p = coz.model.fillter.features[_key];
                            for(j=0; j< _p.length; j++){
                                if( typeof coz.model.fillter.features[_key][j]['feature_id'] != 'undefined'
                                    && coz.model.fillter.features[_key][j]['feature_id'] == id_ ){
                                    if( typeof coz.model.fillter.features[_key][j]['is_checked'] == 'undefined' ){
                                        coz.model.fillter.features[_key][j]['is_checked'] = true;
                                    }else{
                                        coz.model.fillter.features[_key][j]['is_checked'] = !coz.model.fillter.features[_key][j]['is_checked'];
                                    }
                                    coz.model.fillter.is_pjax = true;
                                    coz.updateFilter();
                                }
                            }
                        }
                    }
                });
            });
        }

        if( $('[data-place="fillterSort"]').length > 0 ){
            _html = $("#tmplFillterSort").tmpl(coz.model.fillter).html();
            $('[data-place="fillterSort"]').each(function(k, el){
                $(el).html(_html);
                $('select[name="sort"]', el).off('change').on('change', function(){
                    coz.model.fillter.sort.val = $(this).val();
                    coz.model.fillter.is_pjax = true;
                    coz.updateFilter();
                });
            });
        }

        if (    $.support.pjax
                && typeof coz.model.fillter.is_pjax != 'undefined'
                && coz.model.fillter.is_pjax ) {
            var url = coz.urlForFilters();
            $.pjax({url: url, container: '[data-pjax-container="ProductCategory"]'});
            coz.model.fillter.is_pjax = false;
        }
    }
};
coz.eventFilter = function(){
    $(document).on('click', '[data-btn="toggleFillter"]', function(e){
        e.preventDefault();
        e.stopPropagation();
        var id_ = $(this).data('id');
        if( id_ == 'price' ){
            coz.model.fillter.price.is_close = !coz.model.fillter.price.is_close;
            coz.updateFilter();
        }else if( id_ == 'sort' ){
            coz.model.fillter.sort.is_close = !coz.model.fillter.sort.is_close;
            coz.updateFilter();
        }else if( $.trim(id_).length > 0
                && coz.feature.hasFeature() ){
            for ( _i=0; _i < Object.keys(coz.model.fillter.features).length; _i++ ) {
                _key = Object.keys(coz.model.fillter.features)[_i];
                _p = coz.model.fillter.features[_key];
                for(j=0; j< _p.length; j++){
                    if( typeof coz.model.fillter.features[_key][j]['feature_id'] != 'undefined'
                        && coz.model.fillter.features[_key][j]['feature_id'] == id_ ){
                        if( typeof coz.model.fillter.features[_key][j]['is_close'] == 'undefined' ){
                            coz.model.fillter.features[_key][j]['is_close'] = true;
                        }else{
                            coz.model.fillter.features[_key][j]['is_close'] = !coz.model.fillter.features[_key][j]['is_close'];
                        }
                        coz.model.fillter.is_pjax = false;
                        coz.updateFilter();
                    }
                }
            }
        }
    });
};
coz.updateSelectFilter(function(){
    coz.updateFilter();
    coz.eventFilter();
});
coz.notification = {
    init : function(){
        if( coz.isPublisher() ){
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: coz.baseUrl + '/assign/notification?_AJAX=1',
                data: null,
                success: function (data) {
                    if (data.constructor === String) {
                        data = JSON.parse(data);
                    }
                    if( typeof data != 'undefined'
                            &&  (data.flag == 'true' ||  data.flag == true) ){
                        $('.dropdown-item-bell-assign').remove();
                        $('[data-place="miniUser"] .icon-bell').remove();
                        tru = '<span class="dropdown-item dropdown-item-bell-assign" >'+
                                    '<a title="'+language.translate('txt_bell_assign')+'" href="/profile/assign" class="bell-assign" rel="nofollow" >'+
                                        '<i class="fa fa-bell" aria-hidden="true"></i>'+
                                        ' Có ' +data.assigns.length+ ' assign chưa xử lý'+
                                        //language.translate('txt_has_bell_assign').replace(/{{NUMBER}}/g, data.assigns.length)+
                                    '</a>'+
                                '</span>';
                        $('[data-place="miniDropdownUser"]').append(tru);
                        if( data.assigns.length > 0){
                            $('[data-place="miniUser"]').addClass('has-bell');
                            $('[data-place="miniUser"]>.link-lb-summary').append('<span class="icon-bell" ><i class="fa fa-bell" aria-hidden="true" ></i></span>');
                        }else{
                            $('[data-place="miniUser"]').removeClass('has-bell');
                        }
                    }else{
                        
                    }
                },
                error : function(){}
            });
        }
    }
};

coz.common = {
    init: function(){
        coz.warning();
        is_sync = coz.getCookie('cozSync');
        if( is_sync == 1 || is_sync == 2 ){
            coz.is_sync = is_sync;
        }else{
            coz.is_sync = 0;
        }

        is_sync_facebook = coz.getCookie('cozSyncFacebook');
        if( is_sync_facebook == 1 || is_sync_facebook == 2 ){
            coz.is_sync_facebook = is_sync_facebook;
        }else{
            coz.is_sync_facebook = 0;
        }

        is_sync_google = coz.getCookie('cozSyncGoogle');
        if( is_sync_google == 1 || is_sync_google == 2 ){
            coz.is_sync_google = is_sync_google;
        }else{
            coz.is_sync_google = 0;
        }

        coz.confirm_location = 1;//đã lấy dc vi trí hoặc không muốn lấy vị trí
        if( coz.hasWebsite() 
            && coz.getWebsite()['confirm_location'] == 1 ){//muốn lấy vị trí
            coz.confirm_location = 0;
            confirm_location = coz.getCookie('confirmLocation');
            if( confirm_location == 1 ){
                coz.confirm_location = 1;
            }
        }

        if( typeof coz.location == 'undefined' ){
            location_ = coz.getCookie('location');
            if ( location_ ) {
                coz.location = JSON.parse(location_);
            }
        }

        if( coz.confirm_location == 0 ){
            coz.whereAreYou.init();
        }
        
        try{
            $('.date_delivery').datepicker('reset').datepicker('destroy').datepicker({format : 'yyyy-mm-dd'});
        }catch(e){}

        coz.checkVisit();

        $(document).on('submit', '#loginform', function(e){
            if($('#loginform .user_name').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_username'));
                $('#loginform .user_name').focus();
                return false;
            }
            if(!coz.isEmail($('#loginform .user_name').val())){
                $(language.translate('txt_username_chua_dung'));
                $('#loginform .user_name').focus();
                return false;
            }
            if($('#loginform .password').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_mat_khau'));
                $('#loginform .password').focus();
                return false;
            }
            return true;
        });

        $(document).on('submit', '#registerform', function(e){
            if($('#registerform .full_name').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_full_name'));
                $('#registerform .full_name').focus();
                return false;
            }
            if($('#registerform .user_name').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_username'));
                $('#registerform .user_name').focus();
                return false;
            }
            if(!coz.isEmail($('#registerform .user_name').val())){
                coz.toast(language.translate('txt_username_chua_dung'));
                $('#registerform .user_name').focus();
                return false;
            }
            if($('#registerform .phone').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_phone'));
                $('#registerform .phone').focus();
                return false;
            }
            if(!coz.isPhone($('#registerform .phone').val())){
                coz.toast(language.translate('txt_phone_chua_dung'));
                $('#registerform .phone').focus();
                return false;
            }
            if($('#registerform .password').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_mat_khau'));
                $('#registerform .password').focus();
                return false;
            }
            if($('#registerform .password').val() != $('#registerform .repassword').val()){
                coz.toast(language.translate('txt_nhap_mat_khau_chua_dung'));
                $('#registerform .repassword').focus();
                return false;
            }
            return true;
        });

        $(document).on('submit', '#searchform', function(e){
            if($('#searchform input[name="keyword"]').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_keyword'));
                $('#searchform input[name="keyword"]').focus();
                return false;
            }
            return true;
        });

        $(document).on('click', '.neo-sent-fqa', function(e){
            e.preventDefault();
            e.stopPropagation();
            if( coz.checkFormFqa( $("#fqaform") ) ){
                var formdata = $("#fqaform").serialize();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/product/post-fqa?_AJAX=1',
                    data: formdata,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        coz.toast(data.html);
                        if(data.flag == true){
                            $('#fqaform input,#fqaform textarea').val('');                    
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }
        });

        $(document).on('click', '.neo-sent-newsletter', function(e){
            e.preventDefault();
            e.stopPropagation();
            if(coz.checkRegisterMailNewLetter()){
                var formdata = $("#newsletterform").serialize();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/product/email-new-letter?_AJAX=1',
                    data: formdata,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        coz.toast(data.html);
                        if(data.flag == true || data.flag == 'true'){
                            $('#newsletter').val();
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }
        });

        $(document).on('click', '.neo-submit-buy-by-email', function(e){
            e.preventDefault();
            e.stopPropagation();
            if(coz.checkBuyByEmail( $("#buybyemailform") ) ){
                var formdata = $("#buybyemailform").serialize();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/product/post-email?_AJAX=1',
                    data: formdata,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        coz.toast(data.msg);
                        if(data.flag == true){
                            $('#buybyemailform input').val('');
                            neoClosePopup();
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }
        });

        $(document).on('submit', '#contactform', function(e){
            if($('#contactform .fullname').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_full_name'));
                $('#contactform .fullname').focus();
                return false;
            }
            if($('#contactform .phone').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_phone'));
                $('#contactform .phone').focus();
                return false;
            }
            if(!coz.isPhone($('#contactform .phone').val())){
                coz.toast(language.translate('txt_phone_chua_dung'));
                $('#contactform .phone').focus();
                return false;
            }
            if($('#contactform .email').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_email'));
                $('#contactform .email').focus();
                return false;
            }
            if(!coz.isEmail($('#contactform .email').val())){
                coz.toast(language.translate('txt_email_chua_dung'));
                $('#contactform .email').focus();
                return false;
            }
            if($('#contactform .title').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_tieu_de'));
                $('#contactform .title').focus();
                return false;
            }
            if($('#contactform .content').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_noi_dung'));
                $('#contactform .content').focus();
                return false;
            }
            return true;
        });

        $(document).on('submit', '#cartauthform', function(e){
            if($('#cartauthform .email').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_email'));
                $('#cartauthform .email').focus();
                return false;
            }
            if(!coz.isEmail($('#cartauthform .email').val())){
                coz.toast(language.translate('txt_email_chua_dung'));
                $('#cartauthform .email').focus();
                return false;
            }
            if($('#cartauthform .password').val().length<=0){
                coz.toast(language.translate('txt_chua_nhap_mat_khau'));
                $('#cartauthform .password').focus();
                return false;
            }
            return true;
        });

        $(document).on('hidden.bs.collapse', '#accordion_payment .panel', function (e) {
            $(e.currentTarget).find('input[name="payment_method"]:eq(0)').attr('checked', false);
        });

        $(document).on('show.bs.collapse', '#accordion_payment .panel', function (e) {
            $(e.currentTarget).find('input[name="payment_method"]:eq(0)').attr('checked', true);
        });

        $(document).on('click', '.neo-update-extension', function(){
            if(!$(this).hasClass('active')){
                $(this).addClass('active');
            }else{
                $(this).removeClass('active');
            }
            getPriceProduct();
        });

        $(document).on('change', '.neo-select-product-type', function(){
            getPriceProduct();
        });

        $(document).on('propertychange change keyup paste input', '.neo-quantity-selector', function(){
            getPriceProduct();
        });

        $(document).on('change', 'input[type="radio"][name="product_type"]', function(){
            getPriceProduct();
        });

        $(document).on('click change input', '[data-neo="popup"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var self = $(this);
            var href = $(this).attr('href')||$(this).attr('data-url');
            href += href.indexOf('?')>=0 ? '&ajax=1&_AJAX=1' : '?ajax=1&_AJAX=1';
            if(typeof $(this).attr('data-role') != 'undefined' || $(this).attr('data-role') == 'form'){
                var neoform = $(this).parents('form').eq(0);
                var ex_data = neoform.serialize();
                href += href.indexOf('?')>=0 ? ('&'+ex_data) : ('?'+ex_data);
            }
            if(typeof onStarNeoPopup == 'function'){
                onStarNeoPopup(self);
            }
            $.magnificPopup.open(
            {
                closeOnBgClick: true,
                closeBtnInside: true,
                enableEscapeKey: true,
                items: {
                    src: href
                },
                callbacks:
                {
                    parseAjax: function(response) {
                        var data = response.data;
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        if(typeof onBeforeNeoPopup == 'function'){
                            onBeforeNeoPopup(data, self);
                        }
                        response.data = $('<div class="wrap-magnific-popup" ></div>').html(data.html);
                        if(typeof data.type != 'undefined' 
                            && $.trim(data.type).length>0 ){
                            try{
                                var fun = data.type.capitalize();
                                eval('bNeo'+fun)(data, self);
                            }catch(e){}
                            try{
                                var fun = data.type.capitalize();
                                eval('neo'+fun)(data, self);
                            }catch(e){}
                            try{
                                var fun = data.type.capitalize();
                                eval('aNeo'+fun)(data, self);
                            }catch(e){}
                        }
                        if(typeof onAfterNeoPopup == 'function'){
                            onAfterNeoPopup(data, self);
                        }
                    },
                    ajaxContentAdded: function(data) {
                    },
                    updateStatus: function(data) {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        if(typeof data.status != 'undefined' 
                            && $.trim(data.status).length>0 ){
                            try{
                                var fun = data.status.capitalize();
                                eval('neo'+fun)(self);
                            }catch(e){}
                        }
                        if(data.status === 'loading') {
                        } else if(data.status === 'ready') {
                        } else if(data.status === 'error') {} 
                    }
                },
                type: 'ajax',
                ajax: {
                    settings: {
                        dataType: 'json',
                        type: 'GET',
                        data: ex_data
                   }
                }
            });
        });

        $(document).on('click change input', '[data-neo="ajax"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var self = $(this);
            var href = $(this).attr('href')||$(this).attr('data-url');
            href += href.indexOf('?')>=0 ? '&ajax=1&_AJAX=1' : '?ajax=1&_AJAX=1';
            var ex_data = '';
            if(typeof $(this).attr('data-role') != 'undefined' || $(this).attr('data-role') == 'form'){
                var neoform = $(this).parents('form').eq(0);
                ex_data = neoform.serialize();
                href += href.indexOf('?')>=0 ? ('&'+ex_data) : ('?'+ex_data);
            }
            if(typeof onBeforeNeoAjax == 'function'){
                onBeforeNeoAjax(self);
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: href,
                data: ex_data,
                cache: false,
                success: function(data)
                {
                    if(typeof onAfterNeoAjax == 'function'){
                        onAfterNeoAjax(data, self);
                    }
                    try{
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                    }catch(e){
                        console.log(e);
                    }
                    if(typeof data.type != 'undefined' 
                        && $.trim(data.type).length>0 ){
                        var fun = data.type.capitalize();
                        try{
                            eval('neo'+fun)(data, self);
                        }catch(e){}
                        try{
                            eval('coz.'+fun)(data, self);
                        }catch(e){}
                    }
                },
                error: function(e)
                {
                    coz.toast('Error ! OoO .please trying');
                    if(typeof onErrorNeoAjax == 'function'){
                        onErrorNeoAjax(self, e);
                    }
                }
            });
        });

        $(document).on('change input', '[data-neo="quantity"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var self = $(this);
            var href = $(this).attr('href')||$(this).attr('data-url');
            href += href.indexOf('?')>=0 ? '&ajax=1&_AJAX=1' : '?ajax=1&_AJAX=1';
            var ex_data = '';
            if(typeof $(this).val() != 'undefined' && $.trim($(this).val()).length>0 ){
                var quantity = $(this).val();
                href += href.indexOf('?')>=0 ? ('&quantity='+quantity) : ('?quantity='+quantity);
                if(typeof onBeforeNeoQuantity == 'function'){
                    onBeforeNeoQuantity(self);
                }
                $.ajax({
                    type: "GET",
                    dataType: "html",
                    url: href,
                    data: ex_data,
                    cache: false,
                    success: function(data)
                    {
                        if(typeof onAfterNeoQuantity == 'function'){
                            onAfterNeoQuantity(data, self);
                        }
                        try{
                            if(data.constructor === String){
                                data = $.parseJSON(data);
                            }
                            if(typeof data.type != 'undefined' 
                                && $.trim(data.type).length>0 ){
                                var fun = data.type.capitalize();
                                eval('neo'+fun)(data, self);
                            }
                        }catch(e){
                            console.log(e);
                        }
                    },
                    error: function(e)
                    {
                        coz.toast('Error ! OoO .please trying');
                        if(typeof onErrorNeoQuantity == 'function'){
                            onErrorNeoAjax(self, e);
                        }
                    }
                });
            }
        });

        $(document).on('click', '[data-neo="magnificPopup"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var type = $(this).attr('data-type');
            var src = $(this).attr('data-src');
            if( $(src).length <= 0 ){
                src += src.indexOf('?')>=0 ? '&ajax=1&_AJAX=1' : '?ajax=1&_AJAX=1';
            }
            $.magnificPopup.open({
                items: {
                    src: src,
                    type: type
                },
                callbacks: {
                    open: function() {
                    },
                    close: function() {
                    },
                    beforeOpen: function() {
                        if( $(src).length>0 ){
                            $(src).show();
                        }
                    },
                    change: function() {
                    },
                    resize: function() {
                    },
                    beforeClose: function() {
                    },
                    afterClose: function() {
                    },
                    imageLoadComplete: function() {
                    }
                }
            },0);
        });

        $(document).on('click', '[data-neo="silling"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var from = $(this).attr('data-from');
            var to = $(this).attr('data-to');
            if( $(from).length>0 && $(to).length>0 ){
                $(to).show();
                $(from).hide();
            }
        });

        $(document).on('click', '[data-neo="toggle"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            var target = $(this).attr('data-target');
            var dtclass = $(this).attr('data-class');
            if( $(target).length>0 ){
                $(target).toggleClass(dtclass);
            }
        });

        $(document).on('click', '[data-form="wholesale"] .coz-close', function(e){
            e.preventDefault();
            e.stopPropagation();
            neoClosePopup();
        });

        $(document).on('click', '[data-btn="togleMenuMobile"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            $(this).parents('[data-place="menuMobile"]').eq(0).toggleClass('active');
        });

        $(document).on('click', function(e){
            if( $(e.target).closest('[data-place="menuMobile"]').length <= 0 ){
                $('[data-place="menuMobile"]').each(function(){
                    $(this).removeClass('active');
                });
            }
        });

        $(document).on('click', '[data-btn="triggerTab"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            _target = $(this).attr('data-target');
            $(_target).each(function(){
                $(this).trigger('click');
            });
        });

        $(document).on('click', '[data-btn="switch"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            data = $(this).data();
            if( typeof data != 'undefined'
                && typeof data.class != 'undefined'
                && data.class.length >0
                && typeof data.group != 'undefined'
                && data.group.length >0
                && typeof data.target != 'undefined'
                && data.target.length >0 ){
                class_ = data.class;
                group_ = data.group;
                target_ = data.target;
                $('[data-btn="switch"][data-group="'+group_+'"]').removeClass(class_);
                $(this).addClass(class_);
                for(i_= 0;i_ < target_.length;i_++){
                    tg = target_[i_];
                    if( typeof tg != 'undefined'
                        && typeof tg.el != 'undefined'
                        && typeof tg.from != 'undefined'
                        && typeof tg.to != 'undefined' ){
                        $(tg.el).removeClass(tg.from).addClass(tg.to);
                    }
                }
            }
        });

        $(document).on('click', '[data-btn="share"]', function(e){
            e.preventDefault();
            var $link   = $(this);
            var href    = $link.attr('href');
            var network = $link.attr('data-network');

            var networks = {
                facebook : { width : 600, height : 300 },
                twitter  : { width : 600, height : 254 },
                google   : { width : 515, height : 490 },
                linkedin : { width : 600, height : 473 }
            };
            var popup = function(network){
                var options = 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,';
                window.open(href, '', options+'height='+networks[network].height+',width='+networks[network].width);
            }
            popup(network);
        });

        $(document).on('click', 'a[href="/logout"]', function(e){
            e.preventDefault();
            coz.notSyncFacebook();
            coz.notSyncGoogle();
            window.location.href = baseUrl + '/logout';
        });

        $(document).on('click', '[data-btn="anythingContact"]', function(e){
            e.preventDefault();
            e.stopPropagation();
            anythingContact = $(this).parents('[data-form="anythingContact"]').eq(0);
            var emptyBoxes = $('[data-required="true"]', anythingContact).filter(function() { return $(this).val() == ""; });
            if( emptyBoxes.length <=0 ){
                var formdata = anythingContact.serialize();
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: coz.baseUrl+'/contact/anything',
                    data: formdata,
                    cache: false,
                    success: function(data)
                    {
                        if(data.constructor === String){
                            data = $.parseJSON(data);
                        }
                        coz.toast(data.msg);
                        anythingContact.find('input, select').val('');
                        if( data.flag == true || data.flag == 'true'){
                            if( (data.has_payment == true || data.has_payment == 'true')
                                && data.payments.length > 0 ){
                                coz.showPopupPayment( data.id, 'contact');
                            }
                        }
                    },
                    error: function(e)
                    {
                        console.log(e);
                    }
                });
            }else{
                coz.toast(language.translate('txt_chua_dien_day_du_thong_tin'));
            }
        });

        $(document).on('click', '[data-btn="acceptAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            if( !$(this).hasClass('disable') ){
                var r = confirm(language.translate('txt_ban_muon_accept_assign'));
                if (r == true) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: coz.baseUrl + '/assign/accept?_AJAX=1',
                        data: {id : id},
                        success: function (data) {
                            if (data.constructor === String) {
                                data = JSON.parse(data);
                            }
                            if( typeof data != 'undefined'
                                &&  (data.flag == 'true' ||  data.flag == true) ){
                                coz.toast(language.translate('txt_accept_assign_thanh_cong'));
                                $('[data-btn="acceptAssign"][data-id="'+id+'"]').addClass('disable');
                                $('[data-btn="cancelAssign"][data-id="'+id+'"]').addClass('disable');
                            }else{
                                coz.toast(language.translate('txt_co_loi_xay_ra'));
                            }
                        },
                        error : function(){
                            coz.toast(language.translate('txt_co_loi_xay_ra'));
                        }
                    });
                }
            }
        });

        $(document).on('click', '[data-btn="cancelAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            if( !$(this).hasClass('disable') ){
                var r = confirm(language.translate('txt_ban_muon_cancel_assign'));
                if (r == true) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: coz.baseUrl + '/assign/cancel?_AJAX=1',
                        data: {id : id},
                        success: function (data) {
                            if (data.constructor === String) {
                                data = JSON.parse(data);
                            }
                            if( typeof data != 'undefined'
                                &&  (data.flag == 'true' ||  data.flag == true) ){
                                coz.toast(language.translate('txt_cancel_assign_thanh_cong'));
                                $('[data-btn="acceptAssign"][data-id="'+id+'"]').addClass('disable');
                                $('[data-btn="cancelAssign"][data-id="'+id+'"]').addClass('disable');
                            }else{
                                coz.toast(language.translate('txt_co_loi_xay_ra'));
                            }
                        },
                        error : function(){
                            coz.toast(language.translate('txt_co_loi_xay_ra'));
                        }
                    });
                }
            }
        });

        $(document).on('click', '[data-btn="finishAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            if( !$(this).hasClass('disable') ){
                var r = confirm(language.translate('txt_ban_muon_finish_assign'));
                if (r == true) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: coz.baseUrl + '/assign/finish?_AJAX=1',
                        data: {id : id},
                        success: function (data) {
                            if (data.constructor === String) {
                                data = JSON.parse(data);
                            }
                            if( typeof data != 'undefined'
                                &&  (data.flag == 'true' ||  data.flag == true) ){
                                coz.toast(language.translate('txt_finish_assign_thanh_cong'));
                                $('[data-btn="acceptAssign"][data-id="'+id+'"]').addClass('disable');
                                $('[data-btn="cancelAssign"][data-id="'+id+'"]').addClass('disable');
                                $('[data-btn="finishAssign"][data-id="'+id+'"]').addClass('disable');
                            }else{
                                coz.toast(language.translate('txt_co_loi_xay_ra'));
                            }
                        },
                        error : function(){
                            coz.toast(language.translate('txt_co_loi_xay_ra'));
                        }
                    });
                }
            }
        });

        $(document).on('click', '[data-btn="readAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: coz.baseUrl + '/assign/read?_AJAX=1',
                data: {id : id},
                success: function (data) {
                    if (data.constructor === String) {
                        data = JSON.parse(data);
                    }
                    if( typeof data != 'undefined'
                            &&  (data.flag == 'true' ||  data.flag == true) ){
                        //coz.toast(language.translate('txt_read_assign_thanh_cong'));
                        seft.attr('data-btn', 'unreadAssign').html('<i class="fa fa-circle-o" aria-hidden="true"></i>');
                    }else{
                        coz.toast(language.translate('txt_co_loi_xay_ra'));
                    }
                },
                error : function(){
                    coz.toast(language.translate('txt_co_loi_xay_ra'));
                }
            });
        });

        $(document).on('click', '[data-btn="unreadAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: coz.baseUrl + '/assign/unread?_AJAX=1',
                data: {id : id},
                success: function (data) {
                    if (data.constructor === String) {
                        data = JSON.parse(data);
                    }
                    if( typeof data != 'undefined'
                            &&  (data.flag == 'true' ||  data.flag == true) ){
                        //coz.toast(language.translate('txt_unread_assign_thanh_cong'));
                        seft.attr('data-btn', 'readAssign').html('<i class="fa fa-circle" aria-hidden="true"></i>');
                    }else{
                        coz.toast(language.translate('txt_co_loi_xay_ra'));
                    }
                },
                error : function(){
                    coz.toast(language.translate('txt_co_loi_xay_ra'));
                }
            });
        });

        $(document).on('click', '[data-btn="importantAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: coz.baseUrl + '/assign/important?_AJAX=1',
                data: {id : id},
                success: function (data) {
                    if (data.constructor === String) {
                        data = JSON.parse(data);
                    }
                    if( typeof data != 'undefined'
                            &&  (data.flag == 'true' ||  data.flag == true) ){
                        //coz.toast(language.translate('txt_important_assign_thanh_cong'));
                        seft.attr('data-btn', 'unimportantAssign').html('<i class="fa fa-star" aria-hidden="true"></i>');
                    }else{
                        coz.toast(language.translate('txt_co_loi_xay_ra'));
                    }
                },
                error : function(){
                    coz.toast(language.translate('txt_co_loi_xay_ra'));
                }
            });
        });

        $(document).on('click', '[data-btn="unimportantAssign"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: coz.baseUrl + '/assign/unimportant?_AJAX=1',
                data: {id : id},
                success: function (data) {
                    if (data.constructor === String) {
                        data = JSON.parse(data);
                    }
                    if( typeof data != 'undefined'
                            &&  (data.flag == 'true' ||  data.flag == true) ){
                        //coz.toast(language.translate('txt_unimportant_assign_thanh_cong'));
                        seft.attr('data-btn', 'importantAssign').html('<i class="fa fa-star-o" aria-hidden="true"></i>');
                    }else{
                        coz.toast(language.translate('txt_co_loi_xay_ra'));
                    }
                },
                error : function(){
                    coz.toast(language.translate('txt_co_loi_xay_ra'));
                }
            });
        });

        $(document).on('click', '[data-btn="acceptInvoice"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            if( !$(this).hasClass('disable') ){
                var r = confirm(language.translate('txt_ban_muon_accept_invoice'));
                if (r == true) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: coz.baseUrl + '/invoice/accept?_AJAX=1',
                        data: {id : id},
                        success: function (data) {
                            if (data.constructor === String) {
                                data = JSON.parse(data);
                            }
                            if( typeof data != 'undefined'
                                &&  (data.flag == 'true' ||  data.flag == true) ){
                                coz.toast(language.translate('txt_accept_invoice_thanh_cong'));
                                $('[data-btn="acceptInvoice"][data-id="'+id+'"]').addClass('disable');
                                $('[data-btn="finishInvoice"][data-id="'+id+'"]').removeClass('disable');
                            }else{
                                coz.toast(language.translate('txt_co_loi_xay_ra'));
                            }
                        },
                        error : function(){
                            coz.toast(language.translate('txt_co_loi_xay_ra'));
                        }
                    });
                }
            }
        });

        $(document).on('click', '[data-btn="finishInvoice"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            seft = $(this);
            if( !$(this).hasClass('disable') ){
                var r = confirm(language.translate('txt_ban_muon_finish_invoice'));
                if (r == true) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: coz.baseUrl + '/invoice/finish?_AJAX=1',
                        data: {id : id},
                        success: function (data) {
                            if (data.constructor === String) {
                                data = JSON.parse(data);
                            }
                            if( typeof data != 'undefined'
                                &&  (data.flag == 'true' ||  data.flag == true) ){
                                coz.toast(language.translate('txt_finish_invoice_thanh_cong'));
                                $('[data-btn="finishInvoice"][data-id="'+id+'"]').addClass('disable');
                                $('[data-btn="acceptInvoice"][data-id="'+id+'"]').removeClass('disable');
                            }else{
                                coz.toast(language.translate('txt_co_loi_xay_ra'));
                            }
                        },
                        error : function(){
                            coz.toast(language.translate('txt_co_loi_xay_ra'));
                        }
                    });
                }
            }
        });

        $(document).on('click', '[data-btn="printInvoice"]', function(e){
            e.preventDefault();
            id = $(this).data('id');
            if( $('[data-bodyprint="true"][data-id="'+id+'"]').length >0 ){
                html = $('[data-bodyprint="true"][data-id="'+id+'"]').html();
                $('[data-place="bodyprint"]').remove();
                $('body').append('<div class="clearfix" data-place="bodyprint" ><div class="container" >'+html+'</div></div>');
            }
            window.print();
        });

        $(window).scroll(function(){
            var cur_ = $(this).scrollTop();
            if( $('[data-pin="sticky"]').length >0 ){
                var tpin = $('[data-pin="sticky"]').eq(0).offset().top;
                var enpin = $('[data-neo="sticky"]').eq(0).attr('data-end');
                if( (cur_ >tpin && $(enpin).length<=0) 
                || (cur_ >tpin && $(enpin).length >0 && (parseInt(cur_) + $('[data-neo="sticky"]').eq(0).height())<=$(enpin).eq(0).offset().top) ){
                    if( !$('[data-pin="sticky"]').eq(0).hasClass('fixky') ){
                        $('[data-pin="sticky"]').eq(0).addClass('fixky');
                    }
                    $('[data-pin="sticky"]').eq(0).find('[data-neo="sticky"]').eq(0).css({'position':'fixed','z-index': 9999, 'width': $('[data-pin="sticky"]').eq(0).width()+'px','top':'0px'});
                }else{
                    if( $('[data-pin="sticky"]').eq(0).hasClass('fixky') ){
                        $('[data-pin="sticky"]').eq(0).removeClass('fixky');
                    }
                    $('[data-pin="sticky"]').eq(0).find('[data-neo="sticky"]').eq(0).attr('style', '');
                }
            }
        });

        coz.pjaxComplete();

    }
};
coz.common.init();

(function() {

    var beforePrint = function() {
        $('[data-place="bodyprint"]').fadeIn(300).siblings().hide();
    };

    var afterPrint = function() {
        $('[data-place="bodyprint"]').hide().siblings().fadeIn(300, function(){
            $('[data-place="bodyprint"]').remove();
        });
    };

    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (mql.matches) {
                beforePrint();
            } else {
                afterPrint();
            }
        });
    }

    window.onbeforeprint = beforePrint;
    window.onafterprint = afterPrint;

}());

$(document).on('pjax:start', function() {
    NProgress.set(0); 
    NProgress.start();
    try{
        pjaxStart();
    }catch(e){}
});
$(document).on('pjax:end',   function() { 
    /*NProgress.done(true);
    coz.model.fillter.is_pjax = false;
    coz.updateFilter();
    coz.pjaxComplete();
    try{
        pjaxComplete();
    }catch(e){}*/
});
$(document).on('pjax:send', function() {
    NProgress.start();
});
$(document).on('pjax:complete', function() {
    NProgress.done(true);
    coz.model.fillter.is_pjax = false;
    coz.updateSelectFilter(function(){
        coz.updateFilter();
    });
    coz.pjaxComplete();
    try{
        pjaxComplete();
    }catch(e){
        console.log(e);
    }
    coz.getFeatureForPage(function(){
        coz.updateSelectFilter(function(){
            coz.updateFilter();
        });
    });
    coz.checkVisit();
});

if ($.support.pjax) {
    $(document).on('click', '[data-pjax]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var link = $(this).attr('href') || $(this).data('href') || $(this).data('link');
        if( typeof link != 'undefined'
            && $.trim(link).length >0 ){
            var container = $(this).data('container');
            if( $('[data-pjax-container="'+container+'"]').length >0 ){
                coz.model.fillter.features = {};
                $.pjax({url: link, container: '[data-pjax-container="'+container+'"]', fragment : 'body' });
            }else{
                window.location.href = link;
            }
        }
    })
}
coz.loaded = true;