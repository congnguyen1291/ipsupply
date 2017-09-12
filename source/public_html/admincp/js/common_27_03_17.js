/**
 * Created by viet on 5/29/14.
 */
 function isPhone(phone){
    if(phone == '' || $.isNumeric(phone)==false || phone <= 0 || phone.length <= 9 || phone.length >= 12 || phone.substring(0,1) != '0'){
        return false;
    }
    return true;    
};

function isEmail(a){
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if ($.trim(a).length == 0 || !emailReg.test(a)){
        return false;
    }
    return true;
};
 var config_mini = {              
    height : 100,
    allowedContent : true,           
    extraAllowedContent : 'div(*)',           
    extraPlugins : 'colorbutton,panelbutton,colordialog,youtube,justify,wordcount,font', 
    toolbarGroups : [
        { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'links' },
        { name: 'forms' },
        { name: 'tools' },
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'others' },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'about' }
    ],
    removeButtons : 'Underline,Subscript,Superscript',
    format_tags : 'p;h2;h3;pre'          
};
$(document).ready(function () {
            $('.results-dropdown-map-links-description').each(function(){
                id_ = $(this).attr('data-id');
                if (typeof(CKEDITOR.instances[id_]) == 'undefined') {           
                    CKEDITOR.replace(id_, config_mini);
                } else {
                    CKEDITOR.instances[id_].destroy(true);
                    CKEDITOR.replace(id_, config_mini);
                }
            });
    if(typeof CKEDITOR != 'undefined'){
        try{
            CKEDITOR.replace( 'products_more', {
                height:700
            });
            
        }catch(e){}
    }
    if(typeof CKEDITOR != 'undefined'){
        try{
            CKEDITOR.replace( 'products_longdescription', {
                height:600
            });
            
        }catch(e){}
    }
    //$myCose = $('.my-code-area').ace({ theme: 'chaos', lang: 'html' });
    $('.nicescroll-div').niceScroll({touchbehavior:false,cursorcolor:"#8dc63f",horizrailenabled:true});
    if($('.upload-html5').length>0){
        var str_ = '<input type="file" style="width: 100%;height: 100%;position: absolute;top: 0px;left: 0px;z-index: 99;display: inline-block;opacity: 0;filter:alpha(opacity=0);" class="file-upload-image" data-role="none" data-ajax="false" accept="image/*" />';
        $('.upload-html5').css('position','relative').css('zIndex',10).append(str_);
        $('.upload-html5 .file-upload-image').html5_upload({
            url: baseUrl+'/upload/html5upload?folder=' + folder,
            sendBoundary: window.FormData || $.browser.mozilla  || $.browser.chrome,
            onStart: function(event, total) {
                if(total>0&&total<=5){
                    return true;
                }else{
                    alert('Không thể thực hiện bởi vì mỗi lần upload chỉ tối đa 5 hình . Xin vui lòng chon lại');
                    return false;
                }
            },
            onStartOne: function(event, name, number, total , file , size) {
                if(total>0&&total<=total && (1048576 *2) >= size){ //nho hon 2M
                    return true;
                }else{
                    alert('File quá lớn');
                    return false;
                }
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
                console.log(response);
                if(response.constructor === String){
                    response = $.parseJSON(response);
                }
                console.log(response);
                str_ = '<div class="col-lg-3 col-xs-6 item-img" >'+
                                    '<div class="small-box" >'+
                                        '<div class="inner" >'+
                                        '</div>'+
                                    '</div>'+
                                '</div>';
                if($('.list-image .item-img').length%4==0){
                    $('.list-image').append('<div class="row" ></div>');
                }
                if(response.flag == 'true' || response.flag == true){
                    $(str_).appendTo($('.list-image .row').last()).click(function(e){
                        var url = $(this).find('img').attr('src');
                        $('.preview-img').val('<img src="'+url+'" alt="" >');
                        //console.log(url);
                    }).find('.small-box').addClass('bg-aqua')
                        .find('.inner').append('<div class="sucess-img" ><i class="fa fa-times i-remove" onclick="$(this).parents(\'.col-lg-3\').first().remove();" ></i><img src="'+response.url+'" alt="" ><input name="imgup[]" type="hidden" value="'+response.file+'"></div>');
                }else{
                    $(str_).appendTo($('.list-image .row').last()).find('.small-box').addClass('bg-red')
                        .find('.inner').html('<div class="error-img" ><i class="icon i-remove" onclick="$(this).parents(\'.col-lg-3\').first().remove();" ></i><span>'+response.msg+'</span></div>');
                }           

            },
            onError: function(event, name, e) {         
                console.log(e);
                alert('error while uploading file ' + name);
            }
        });
    }

    $('.feature-info label span').on('click', function(e){
        if($(this).hasClass('open')){
            $('.feature-info .feature-list').css({display:'block'});
            $(this).removeClass('open').html('-');
        }else{
            $('.feature-info .feature-list').css({display:'none'});
            $(this).addClass('open').html('+');
        }
    })

    $('.btn--edit').click(function(e){
        $pp_ = $('.pop-regisemali-pro');
        $code =$pp_.find('.ae-code');
        $seft_ =$('.'+$(this).attr('data-box'));
        $html =$seft_.children('.data-html');       
        if($('.mask-popup').length<=0){
            $('.mask-popup').remove();
        }
        $('<div class="mask-popup"></div>').insertAfter($('.pop-regisemali-pro'));      
        $('.pop-regisemali-pro,.mask-popup').attr('data-sys',$(this).attr('data-box')).fadeIn(200,function(){
            $code.html('<textarea class="my-code-area" rows="4" style="width: 100%; display: none;">'+$.trim($html.html())+'</textarea>').show();
            $pluginMy = $pp_.find('.my-code-area').ace({ theme: 'chaos', lang: 'html',height:300,width:560});
        });
    }); 
        
    $('.pop-regisemali-pro .btn-sentcm.ok').click(function(e){
        $pp_ = $('.pop-regisemali-pro');
        $innertext = $pp_.find('.my-code-area').val();
        $seft_ =$('.'+$pp_.attr('data-sys'));
        $html =$seft_.children('.data-html');   
        $seft_.find('.my-code-area').val($innertext);
        //console.log($seft_.find('.my-code-area').val());
        $html.html($innertext); 
        $('.pop-regisemali-pro .btn-sentcm.cancel').trigger('click');
    });     
    
    $('.pop-regisemali-pro .btn-sentcm.cancel,.pop-regisemali-pro .icon.i-close').on('click',function(e){
        $pp_ = $('.pop-regisemali-pro');
        $code =$pp_.find('.ae-code');
        $pp_.fadeOut(200);$('.mask-popup').remove();
        $code.html('');
    });     
    /*
    if (typeof folder != 'undefined' && typeof uploadfor != 'undefined') {
        switch (uploadfor) {
            case 'product':
            {
                swfUploadComment('thumb_image', 'products_longdescription');
            }
                break;
            case 'articles':
            {
                swfUploadComment('thumb_images', 'articles_content');
            }
                break;
        }
    }
 */
    $('.article_autocomplete').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/articles/find-articles',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            var suggestion = data.item;
            if(article_list.indexOf(suggestion.data) != -1){
                return false;
            }
            article_list.push(suggestion.data);
            var html = '<tr>' +
                '<td style="text-align: center">' + suggestion.data + '</td>' +
                '<td>' +
                '<a href="' + cmsUrl + '/articles/edit/' + suggestion.data + '">' + suggestion.value + '</a>' +
                '</td>' +
                '<td style="text-align: center">' +
                suggestion.ordering +
                '</td>' +
                '<td>' +
                suggestion.date_create +
                '</td>' +
                '<td>' +
                '<input type="checkbox"  name="cid[]" value="' + suggestion.data + '" checked />' +
                '</td>' +
                '</tr>';
            $('.table-data-bind tbody').append(html);
            $(".table-data-bind tbody input[type='checkbox'],.table-data-bind tbody input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
        }
    });
    $('[parentitem]').css({display: 'none'}).addClass('hidden');
    $('.multichoice').select2();
    $('.product_ajax_select2').select2({
        minimumInputLength: 1,
        multiple:true,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: cmsUrl + "/product/filter-choice",
            dataType: 'json',
            type:'POST',
            quietMillis: 50,
            data: function (term) {
                return {
                    query: term
                };
            },
            results: function (data) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter remote JSON data
                return {results: $.map(data, function(item){
                    return {
                        text:item.products_title,
                        id: item.products_id
                    }
                }),more: false};
            }
        },
        initSelection: function(element, callback) {
            // the input tag has a value attribute preloaded that points to a preselected movie's id
            // this function resolves that id attribute to an object that select2 can render
            // using its formatResult renderer - that way the movie name is shown preselected
            var id=$(element).val();
            id = id.split(',');
            if (id!=="") {
                $.ajax(cmsUrl + "/product/ajax-product", {
                    data: {
                        id:id
                    },
                    dataType: "json",
                    type:'post'
                }).done(function(data) { callback(data); });
            }
        }
    });
    $('.product_ajax_select').select2({
        minimumInputLength: 1,
        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
            url: cmsUrl + "/product/filter-choice",
            dataType: 'json',
            type:'POST',
            quietMillis: 50,
            data: function (term) {
                return {
                    query: term
                };
            },
            results: function (data) { // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to alter remote JSON data
                return {results: $.map(data, function(item){
                    return {
                        text:item.products_title,
                        id: item.products_id
                    }
                }),more: false};
            }
        }
    });
    //$(".date-input").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    var todaysDate = moment().format('YYYY-MM-DD HH:MM:SS');
    $('.date-ranger-time-input').daterangepicker({
        timePicker: true,
        timePicker12Hour: false,
        showDropdowns:true,
        timePickerIncrement: 1,
        format: 'YYYY-MM-DD HH:mm:ss',
        separator: ' _to_ '
    });
    $('.date-ranger-input').daterangepicker({
        //timePicker: true,
        //timePicker12Hour: false,
        showDropdowns:true,
        //timePickerIncrement: 1,
        format: 'YYYY-MM-DD 00:00:00',
        separator: ' _to_ '
    });
    $('.date-time-input').daterangepicker({
        singleDatePicker:true,
        /*startDate:todaysDate,*/
        format: 'YYYY-MM-DD HH:mm:ss',
        timePicker12Hour:false,
        timePickerIncrement: 1,
        timePicker:true,
        showDropdowns:true
    });
    $('.date-input').daterangepicker({
        singleDatePicker:true,
//        startDate:todaysDate,
        format: 'YYYY-MM-DD',
        showDropdowns:true
    });
    $('.time-input').timepicker({
        defaultTime: 'current',
        showInputs: true,
        minuteStep:15,
        showSeconds: true,
        showMeridian: false
    });
    $('.colorpiker').colorpicker();
    $('.parent_node').click(function () {
        var itemid = $(this).attr('itemid');
        if ($('[parentitem=' + itemid + ']').hasClass('hidden')) {
            $(this).find('.icon-parent').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            $('[parentitem=' + itemid + ']').slideDown().removeClass('hidden').addClass('nothidden');
        } else {
            $(this).find('.icon-parent').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            $('[parentitem=' + itemid + ']').slideUp().removeClass('nothidden').addClass('hidden');
        }
    });
    $('.date_range_input').daterangepicker();
    $('input.checkall').on('ifChecked', function (event) {
        $('table.table-data-bind input').iCheck('check');
    });
    $('input.checkall').on('ifUnchecked', function (event) {
        $('table.table-data-bind input').iCheck('uncheck');
    });
    $('input.parentcheck').on('ifUnchecked', function (event) {
        var rel = $(this).first();
        var current = $(rel).attr('rel');
        $('.panel.panel-checkbox-' + current).find('input.childcheck').iCheck('uncheck');
    });
    $('input.childcheck').on('ifChecked', function (event) {
        var rel = $(this).first();
        var current = $(rel).attr('rel');
        $('.panel.panel-checkbox-' + current).find('input.parentcheck').iCheck('check');
    });
    $('.moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
    $('.numberInput').inputmask({ "mask": "9", "repeat": 10, "greedy": false });
    /**
     * Filter tất cả thông tin
     */
    $('.filter_form').submit(function () {
        var url = $(this).attr('action');
        var data = $(this).serialize();
        request('POST', 'html', url, data, function (html) {
            $('.table-data-bind tbody').html(html);
            $(".table-data-bind tbody input[type='checkbox'],.table-data-bind tbody input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
        });
        return false;
    });
    var products_order = null;
    if(typeof products_list == 'undefined') {
        var products_list = [];
    }
    $('.product_autocomplete').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/product/find-products',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            var suggestion = data.item;
            $('.products_id').html(suggestion.products_id);
            $('.products_code').html(suggestion.products_code);
            $('.products_title').html(suggestion.products_title);
            $('.price').html(accounting.formatMoney(suggestion.price_sale, 'VNĐ', 0, ".", ",", "%v %s"));
            if (suggestion.is_available == 1) {
                $('.is_available').html('Còn hàng');
            } else {
                $('.is_available').html('Hết hàng');
            }
            products_order = suggestion;
        }
    });
    $('.gold_product_autocomplete').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/product/find-products',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            var suggestion = data.item;
            for(var i = 0; i < list_added.length; i++){
                if(list_added[i] == suggestion.products_id){
                    return false;
                }
            }
            list_added.push(suggestion.products_id);
            var html = "<tr>" +
                           "<td>"+ suggestion.products_code +"<input type='hidden' value='"+suggestion.products_code+"' name='products["+suggestion.products_id+"][products_code]' /></td>" +
                           "<td>"+ suggestion.products_title +"<input type='hidden' value='"+suggestion.products_title+"' name='products["+suggestion.products_id+"][products_title]' /></td>" +
                           "<td><input type='text' class='moneyInput' value='"+ suggestion.price +"' name='products["+suggestion.products_id+"][price]' /></td>" +
                           "<td><input type='text' class='moneyInput' value='0' name='products["+suggestion.products_id+"][price_sale]' /></td>" +
                           "<td><a href='javascript:;' onclick=\"removeDealProduct(this,'"+suggestion.products_id+"')\">Xóa</a></td>" +
                       "</tr>";
            $('.product_deal_table tbody').append(html);
            $('.moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
        }
    });
    
    $('.article_autocomplete_config').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/articles/find-articles',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            var suggestion = data.item;
            $('#value').val("/bai-viet/" +suggestion.articles_alias+"-"+suggestion.articles_id);
        }
    });

    $('.btn-add-to-order').click(function () {
        if (!products_order) {
            return false;
        }
        if(products_order.is_available == 0 || products_order.quantity == 0){
            alert('Sản phẩm không còn hàng!');
            return false;
        }
        if (products_list.indexOf(products_order.products_id) != -1) {
            return false;
        }
        var quantity = $('.quantity_current').val();
        var tmp = "" +
            "<tr>" +
                "<td>" +
                        "" + products_order.products_id + "" +
                        "<input type='hidden' value='"+products_order.products_id+"' name='pdetail["+products_order.products_id+"][products_id]' />" +
                        "<input type='hidden' value='"+products_order.vat+"' name='pdetail["+products_order.products_id+"][vat]' /></td>" +
                "<td>" + products_order.products_code + "<input type='hidden' value='"+products_order.products_code+"' name='pdetail["+products_order.products_id+"][products_code]' /></td>" +
                "<td colspan=\"4\" >" + products_order.products_title + "<input type='hidden' value='"+products_order.products_title+"' name='pdetail["+products_order.products_id+"][products_title]' /></td>" +
                "<td style='text-align:center'>" + accounting.formatMoney(products_order.price_sale, 'VNĐ', 0, ".", ",", "%v %s") +"</td>" +
                "<td style='text-align:center'>" + "<input type='text' name='pdetail[" + products_order.products_id + "][quantity]' value='" + quantity + "' style='width:50px;text-align:center' /> " + "</td>" +
                "<td align='right'>" + accounting.formatMoney(products_order.price_sale*quantity, 'VNĐ', 0, ".", ",", "%v %s") + "<input type='hidden' name='pdetail[" + products_order.products_id + "][price]' value='" + products_order.price + "' /></td>" +
                "<td align='right'>" + accounting.formatMoney(products_order.price_sale*quantity, 'VNĐ', 0, ".", ",", "%v %s") + "<input type='hidden' name='pdetail[" + products_order.products_id + "][price]' value='" + products_order.price + "' /></td>" +
                "<td>" + "<input type='checkbox' value='" + products_order.products_id + "' name='pid[]' checked />" + "</td>" +
            "</tr>";
        $('.table-data-bind tbody').append(tmp);
        $(".table-data-bind tbody input[type='checkbox'],.table-data-bind tbody input[type='radio']").iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });
        products_list.push(products_order.products_id);
        return false;
    })
    $('.product_invoice_autocomplete').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/product/find-products',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            var suggestion = data.item;
            $('#products_id').val(suggestion.products_id);
        }
    });

    $('#add-from-list').click(function(){
        var formdata = {
            extids : $("#extension_type").val()
        };
        request('POST', 'json', cmsUrl + '/extension/load-extension', formdata, function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if(data.success){
                var exts = data.ext;
                for(var ext in exts){
                    var row = exts[ext];
                    addExt(row);
                }
            }
        })
    });

    $('#add-from-list-require').click(function(){
        var formdata = {
            extids : $("#extension_require_type").val()
        };
        request('POST', 'json', cmsUrl + '/extension/load-extension', formdata, function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if(data.success){
                var exts = data.ext;
                for(var ext in exts){
                    var row = exts[ext];
                    addExtRequire(row);
                }
            }
        })
    });
    $('#box_num').on('change', function(){
    
        var val = parseInt($(this).val());
        $('.pos-panel').css({display:'none'});
        $('#position-banner-' + val).css({display:'block'});
        $('#myModal'+val).modal('show');
        if(val >= 7 && val <= 10){
            $('.option-field-banner').slideDown();
        }else{
            $('.option-field-banner').slideUp();
        }
        /*
        if(val <= 4){
            $('#lessthan-4').css({display:'block'});
            $('#myModal3').modal('show');
        }else{
            if(val == 5){
                $('#equal-5').css({display:'block'});
                $('#myModal2').modal('show');
            }else{
                if(val >= 7 && val <= 10){
                    $('#greatthan-7').css({display:'block'});
                    $('#myModal1').modal('show');
                }else{
                    if(val >= 11 && val <= 12){
                        $('#greatthan-11').css({display:'block'});
                        $('#myModal4').modal('show');
                    }
                }
            }
        }*/
    })
});
var loaded = true;
filluser = function(userid){
    if(!loaded){
        return false;
    }
    loaded = false;
    $('#users_id').select2("enable", false);
    $.ajax({
        type:'POST',
        url : cmsUrl + '/user/get-user',
        dataType: 'json',
        data: {users_id:userid},
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            loaded = true;
            $('#users_id').select2("enable", true);
            if(data.success){
                $('#full_name').val(data.result.full_name);
                $('#phone').val(data.result.phone);
                $('#email').val(data.result.user_name);
                $('#address').val(data.result.address);
                $('#cities').select2('val',data.result.cities_id);
                loadDistrict(data.result.cities_id,data.result.districts_id,data.result.wards_id)
            }else{
                alert(data.msg);
                if(confirm('Bạn muốn thử lại không?')){
                    filluser(userid);
                }
            }
        }
    })
}

var loaded_coupon = true;
loadCouponInfo = function(coupon){
    if(!loaded_coupon){
        return false;
    }
    $('#coupon_response').html('loading....');
    $('#coupon_code').attr('readonly',true);
    loaded_coupon = false;
    $.ajax({
        type:'POST',
        url : cmsUrl + '/invoice/use-coupon',
        dataType: 'json',
        data: {coupon:coupon},
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            loaded_coupon = true;
            $('#coupon_code').attr('readonly',false);
            $('#coupon_response').html(data.msg);
        }
    })
}

removeDealProduct = function(a,key){
    var $this = $(a);
    $('.gold_products_item_'+key).remove();
    var i = list_added.indexOf(key);
    list_added.splice(i,1);
    return false;
}

showPopupAddlink = function(id){
    if($('.mask-popup').length<=0){
        $('.mask-popup').remove();
    }
    $('<div class="mask-popup"></div>').insertAfter($('.pop-regisemali-pro'));
    $('.pop-regisemali-pro #region-update').val(id);
    $('.pop-regisemali-pro,.mask-popup').fadeIn(200); 
}

addLink = function(){
    if(checkaddLink() == true){
        var id= $('.pop-regisemali-pro #region-update').val();
        if(id.length>0){
            var txtNd_ = $('#txtPopupComments').val();
            var txtLink_ = $('#link-rg').val();
            if($.trim(txtLink_).length<=0){
                txtLink_ = 'javascript:;';
            }
            str_ = '<a href="'+txtLink_+'" >'+txtNd_+'<span class="icon i-close" onclick="$(this).parents(\'a\').eq(0).remove()" ></span></a>';
            $(str_).insertBefore($('.'+id+' .add-link'));
            $('#txtPopupComments,#link-rg').val('')
        }
        $('.pop-regisemali-pro,.mask-popup').fadeOut(200); 
    }
}

function checkaddLink(){
    if($.trim($('#txtPopupComments').val()).length<=0){
        alert('Bạn vui lòng nhập nội dung');
        return false;
    }
    return true;
}

function load_banner_type(type){
    if(type == 0){
        html = $('#code_template').html();
        $('#banner_content').html(html);
    }else{
    
     html = $('#file_template').html();
        $('#banner_content').html(html);
    }
}
function loadCites(country, selected_dist, selected_ward) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: cmsUrl + '/user/load-city',
        data: {country_id: country},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if (data.success) {
                $('#cities option').remove();
                var c = data.results.length
                $('#cities').append($("<option></option>").attr('value', 0).text('Tất cả'));
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.cities_id == selected_dist) {
                        $('#cities').append($("<option></option>").attr('value', row.cities_id).text(row.cities_title).attr('selected', true));
                    } else {
                        $('#cities').append($("<option></option>").attr('value', row.cities_id).text(row.cities_title));
                    }
                    //if (i == c - 1) {
                        //loadWard($('#cities').val(), selected_ward);
                    //}
                }
            }else{
				$('#cities').append($("<option></option>").attr('value', 0).text('Tất cả'));
			}
        }
    });
}
function loadDistrict(city, selected_dist, selected_ward, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: cmsUrl + '/user/load-district',
        data: {cities_id: city},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if (data.success) {
                $('#districts option').remove();
                var c = data.results.length
                $('#districts').append($("<option></option>").attr('value', 0).text('Tất cả').attr('selected', true));
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.districts_id == selected_dist) {
                        $('#districts').append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                    } else {
                        $('#districts').append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                    }
                    if (i >= c - 1) {
                        //loadWard($('#districts').val(), selected_ward);
                        if( typeof callback == 'function' ){
                            callback();
                        }
                    }
                }
				
			}else{
				 $('#districts').append($("<option></option>").attr('value', 0).text('Tất cả'));
			}
        }
    });
}
function loadWard(district, selected) {
    $('#wards option').remove();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: cmsUrl + '/user/load-ward',
        data: {districts_id: district},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if (data.success) {
                var c = data.results.length;
                $('#wards').append($("<option></option>").attr('value', 0).text('Tất cả'));
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.wards_id == selected) {
                        $('#wards').append($("<option></option>").attr('value', row.wards_id).text(row.wards_title).attr('selected', true));
                    } else {
                        $('#wards').append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                    }
                   // if(i == c - 1){
                       // updateFullAddress();
                   // }
                }
           // }else{
			//	 $('#wards').append($("<option></option>").attr('value', 0).text('Tất cả'));
			}
        }
    });
}

function updateFullAddress(){
    try{
        var address = $('#address').val();
        var ward = $("#wards option:selected").text();
        var district = $("#districts option:selected").text();
        var city = $("#cities option:selected").text();
        var fulladdress = address + ', Phường ' + ward + ' - ' + district + ' - ' + city;
        $('#full_address').val(fulladdress);
    }catch(e){
        console.log(e);
    }
}

function showup(a){
    var $this = $(a);
    if($this.hasClass('open')){
        $this.parent().parent().parent().children('.panel-body').css({display:'none'});
        $this.removeClass('open').addClass('close');
        $this.html('+');
    }else{
        $this.parent().parent().parent().children('.panel-body').css({display:'block'});
        $this.removeClass('close').addClass('open');
        $this.html('-');
    }
}

function invoice_change(a){
    var $this = $(a);
    id = $this.attr('item-id');
    name = $this.attr('item-name');
    value = $this.val();
    var formdata = {};
    formdata['data'] = {};
    formdata['id'] = id;
    formdata['data'][name] = value;
    request('POST', 'json', cmsUrl+'/invoice/update-invoice', formdata, function(data){
        if(data.constructor === String){
            data = JSON.parse(data);
        }
        alert(data.msg);
    })
}
function initeExtend(ext){
    $('.moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
    $('.numberInput').inputmask({ "mask": "9", "repeat": 10, "greedy": false });
    $("#ext_box input[type='checkbox'],#ext_box input[type='radio']").iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal'
    });
    $('#ext_box .ext_type').off('change').on('change', function(){
        val = $(this).val();
        if(val == 'default'){
            $(this).parents('.item-ext').eq(0).find('.ext-default').show();
            $(this).parents('.item-ext').eq(0).find('.ext-product').hide();
        }else{
            $(this).parents('.item-ext').eq(0).find('.ext-default').hide();
            $(this).parents('.item-ext').eq(0).find('.ext-product').show();
        }
        
    });
    $('#ext_box .ext_product_name').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/product/find-products',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            $(el.target).parent().find('.ext_product_id').val(data.item.products_id);
        }
    });
    $('#ext_box .ext_type').each(function(){
        val = $(this).val();
        if(val == 'default'){
            $(this).parents('.item-ext').eq(0).find('.ext-default').show();
            $(this).parents('.item-ext').eq(0).find('.ext-product').hide();
        }else{
            $(this).parents('.item-ext').eq(0).find('.ext-default').hide();
            $(this).parents('.item-ext').eq(0).find('.ext-product').show();
        }
    });
}
initeExtend();
function addExt(ext){
    if(typeof ext == 'undefined'){
        ext = {
            ext_id : '',
            ext_name: '',
            ext_description: ''
        }
    }
    var tmp = $('#tmp_extension').html();
    tmp = tmp.replace(/\{EXTID}/g, ext_id);
    tmp = tmp.replace('{ext_id}', ext.ext_id);
    tmp = tmp.replace('{ext_name}', ext.ext_name);
    tmp = tmp.replace('{ext_description}', ext.ext_description);
    tmp = tmp.replace('{price}', '');
    $('#ext_box').append(tmp);
    $('.moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
    $('.numberInput').inputmask({ "mask": "9", "repeat": 10, "greedy": false });
    $("#ext_box input[type='checkbox'],#ext_box input[type='radio']").iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal'
    });
    $('#ext_box .ext_type').off('change').on('change', function(){
        val = $(this).val();
        if(val == 'default'){
            $(this).parents('.item-ext').eq(0).find('.ext-default').show();
            $(this).parents('.item-ext').eq(0).find('.ext-product').hide();
        }else{
            $(this).parents('.item-ext').eq(0).find('.ext-default').hide();
            $(this).parents('.item-ext').eq(0).find('.ext-product').show();
        }
        
    });
    $('#ext_box .ext_product_name').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/product/find-products',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return val;
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            $(el.target).parent().find('.ext_product_id').val(data.item.products_id);
        }
    });
    ext_id++;
}
$(document).on('click', '.btn-remove-item-ext', function(){
    $(this).parents('.item-ext').eq(0).remove();
});
$(document).on('change', '#ext_box_require .ext_box_require_area', function(){
    var area_id = $(this).val();
    var seft_ = $(this);
    if(area_id == 'all'){
        seft_.parents('.row-item-stransport').eq(0).addClass('all-select');
        updateStatusExtRequire();
    }else{
        seft_.parents('.row-item-stransport').eq(0).removeClass('all-select');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/cart/load-cities-by-area',
            data: {area_id: area_id},
            success: function (data) {
                if (data.constructor === String) {
                    data = JSON.parse(data);
                }
                console.log(data);
                if (data.success) {
                    seft_.parents('.row-item-stransport').eq(0).find('.connect-cities').html('');
                    for (var i = 0; i <= data.results.length-1; i++) {
                        var str = '<span class="item-cties_01" >'+data.results[i].cities_title+'<input type="hidden" value="'+data.results[i].cities_id+'"><a href="javascript:void(0);" class="btn-remove-item-cties-stransport"><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
                        seft_.parents('.row-item-stransport').eq(0).find('.list-cities').eq(0).append(str);
                    };
                    updateStatusExtRequire();
                }
            }
        });
    }
});
$(document).on('click', '.btn-remove-item-cties-stransport', function(){
    if($(this).parents('.list-cities').length<=0){
        $(this).parents('.item-cties_01').eq(0).appendTo($(this).parents('.row-item-stransport').eq(0).find('.list-cities').eq(0)).find('input').eq(0).attr('name', '');
    }
});
$(document).on('click', '.btn-remove-item-stransport', function(){
    if($(this).parents('.row-item-stransport').eq(0).find('.item-stransport').length<=1){
        $(this).parents('.row-item-stransport').eq(0).remove();
    }else{
        $(this).parents('.item-stransport').eq(0).find('.list-cities-child').eq(0).find('.item-cties_01').appendTo($(this).parents('.place-area-cities').eq(0).find('.list-cities').eq(0));
        $(this).parents('.item-stransport').eq(0).remove();
    }
    updateStatusExtRequire();
});
$(document).on('click', '.btn-add-all-item-cities-stransport', function(){
    if($(this).parents('.header-list-cities').eq(0).find('.list-cities > .item-cties_01').length>0){
        $(this).parents('.header-list-cities').eq(0).find('.list-cities > .item-cties_01').appendTo($(this).parents('.place-area-cities').eq(0).find('.item-stransport').eq(0).find('.list-cities-child').eq(0));
        $(this).parents('.row-item-stransport').eq(0).find('.list-cities-child input').each(function(i, el){
            $(this).attr('name', 'ext_require['+$(this).parents('.row-item-stransport').eq(0).attr('data-id')+'][data]['+$(this).parents('.item-stransport').eq(0).attr('data-id')+'][area][]');
        });
    }
});
var TIME_UPDATE_CITIES = null;
function updateCitiesForTransport(e, ui){
    clearTimeout(TIME_UPDATE_CITIES);
    clearInterval(TIME_UPDATE_CITIES);
    TIME_UPDATE_CITIES = setTimeout(function(){
        if($(e.target).closest('.list-cities').length>0){
            $(e.toElement).parents('.row-item-stransport').eq(0).find('.list-cities input').each(function(i, el){
                $(this).attr('name', '');
            });
        }else{
            $(e.toElement).parents('.row-item-stransport').eq(0).find('.list-cities-child input').each(function(i, el){
                $(this).attr('name', 'ext_require['+$(this).parents('.row-item-stransport').eq(0).attr('data-id')+'][data]['+$(this).parents('.item-stransport').eq(0).attr('data-id')+'][area][]');
            });
        }
    },1000);
}
var ext_id_child = $('.item-stransport').length + 5;
$('.row-item-stransport').each(function(){
    $(this).find('.place-area-cities').eq(0).find( ".connect-cities" ).sortable({
        connectWith: $(this).find('.place-area-cities').eq(0).find( ".connect-cities" ),
        out: function( event, ui ) {
            updateCitiesForTransport(event, ui);
        }
    }).disableSelection();
});
$(document).on('click', '.btn-add-item-stransport' , function(){
    var tmp = $('#tmp_col_extension_require').html();
    var id_tmp  =  $(this).parents('.row-item-stransport').eq(0).attr('data-id'); 
    tmp = tmp.replace(/\{EXTID}/g, id_tmp);
    tmp = tmp.replace(/\{EXT_ID}/g, ext_id_child);
    ext_id_child++;
    $(this).parents('.row-item-stransport').eq(0).find('.place-area-cities').eq(0).append(tmp);
    $(this).parents('.row-item-stransport').eq(0).find('.place-area-cities').eq(0).find( ".connect-cities" ).sortable({
        connectWith: $(this).parents('.row-item-stransport').eq(0).find('.place-area-cities').eq(0).find( ".connect-cities" ),
        out: function( event, ui ) {
            updateCitiesForTransport(event, ui);
        }
    }).disableSelection();
    $(this).parents('.row-item-stransport').eq(0).find('.moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
    //$( ".selector" ).sortable( "destroy" );
    /*$(this).parents('.place-area-cities').eq(0).find( ".connect-cities" ).sortable({
        connectWith: ".connect-cities",
        activate: function( event, ui ) {
            console.log('activate');
        },
        beforeStop: function( event, ui ) {
            console.log('beforeStop');
        },
        change: function( event, ui ) {
            console.log('change');
        },
        create: function( event, ui ) {
            console.log('create');
        },
        deactivate: function( event, ui ) {
            console.log('deactivate');
        },
        out: function( event, ui ) {
            console.log('out');
        },
        over: function( event, ui ) {
            console.log('over');
        },
        receive: function( event, ui ) {
            console.log('receive');
        },
        remove: function( event, ui ) {
            console.log('remove');
        },
        sort: function( event, ui ) {
            console.log('sort');
        },
        start: function( event, ui ) {
            console.log('start');
        },
        stop: function( event, ui ) {
            console.log('stop');
        },
        update: function( event, ui ) {
            console.log('update');
        }
    }).disableSelection();*/
});

function addExtRequire(ext){
    if(!$('.btn-addExtRequire').hasClass('disable')){
        if(typeof ext == 'undefined'){
            ext = {
                ext_id : '',
                ext_name: '',
                ext_description: ''
            }
        }
        var tmp = $('#tmp_extension_require').html();
        tmp = tmp.replace(/\{EXTID}/g, ext_id_require);
        $('#ext_box_require').append(tmp);
        $('#ext_box_require').find( ".row-item-stransport:last .connect-cities" ).sortable({
            connectWith: "#ext_box_require .row-item-stransport:last .connect-cities",
            out: function( event, ui ) {
                updateCitiesForTransport(event, ui);
            }
        }).disableSelection();
        $('#ext_box_require .moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
        updateStatusExtRequire();
        ext_id_require++;
    }
}

function updateStatusExtRequire(){
    var area_ids = [];
    $('.btn-addExtRequire').removeClass('disable').show();
    $('.ext_box_require_area').each(function(i, el){
        if($(this).val() == 'all'){
            $('.btn-addExtRequire').addClass('disable').hide();
        }
        area_ids[$(this).val()] = $(this).val();
        if(i>=$('.ext_box_require_area').length-1){
            updateOptionStatusExtRequire(area_ids);
        }
    });
}

function updateOptionStatusExtRequire(area_ids){
    $('.ext_box_require_area').each(function(i, el){
        $(el).find('option').each(function(j, ei){
            val = $(this).attr('value');
            if(typeof area_ids[val] != 'undefined' && val != $(el).val()){
               $(this).attr('disabled','disabled'); 
            }else{
                $(this).removeAttr('disabled');
            }
        });
        
    });
}
updateStatusExtRequire();
function removeItem(a){
    var $this = $(a);
    $this.parent().parent().parent().parent().remove();
}

/**
 * Chèn thêm 1 fiel hình ảnh
 * @param pos_append vị trí append
 * @param radio_name tên input radio group
 * @param input_name tên input text list
 *
 */
//function add_more_image(pos_append, radio_name, input_name){
//    var html = '<div style="margin-top: 10px">' +
//        '<div class="input-group">'+
//                    '<span class="input-group-addon">'+
//                    '<input type="radio" name="'+radio_name+'" value="" class="rbtn_thumb" >'+
//                    '</span>'+
//                    '<input type="text" name="'+input_name+'[]" readonly class="form-control" onclick="openProductKCFinder(this, \'images\')" />'+
//                    '<span class="input-group-addon">'+
//                        '<a href="#" onclick="return remove_image(this);" data-toggle="tooltip" title="Bỏ hình này" data-original-title="Bỏ hình này"><i class="fa fa-minus-square"></i></a>'+
//                    '</span>' +
//                '</div>' +
//        '</div>';
//    $('.'+pos_append).append(html);
//
//    $("input[type='checkbox'], input[type='radio']").iCheck({
//        checkboxClass: 'icheckbox_minimal',
//        radioClass: 'iradio_minimal'
//    });
//    return false;
//}
/**
 * Hủy đi 1 field chèn hình hiện tại
 */
//function remove_image(a){
//    var $this = $(a);
//    $this.parent().parent().parent().remove();
//    return false;
//}

/**
 * Tải danh sách đặc tính theo danh mục
 * @param id: id danh mục
 */
function load_feature_by_cat(id) {
    var url = cmsUrl + '/feature/loadfeature';
    var formdata = {
        catid: id
    };
    request('POST', 'html', url, formdata, function (data) {
        $('.feature-list').html(data);
    })
}

function insertImageToCkEditor(value, instanceName) {
    if( instanceName == 'articles_content'){
        $('textarea.articles_content').each(function(){
            var id = $(this).attr('id');
            eval('CKEDITOR.instances.' + id).insertHtml('<img src="' + value + '" />');
        });
    }else{
        eval('CKEDITOR.instances.' + instanceName).insertHtml('<img src="' + value + '" />');
    }
}

/**
 * Cập nhật dữ luệ tại dòng
 * @param a
 * @returns {boolean}
 */
function edit_inline(a) {
    var $this = $(a);
    $this.css({display: 'none'});
    var val = $this.text();
    var rel = $this.attr('href');
    var id = $this.attr('item-id');
    var col_name = $this.attr('data-col');
    $this.parent().append('<input type="text" value="' + val + '" class="value-item-' + id + '" /><button type="button" class="save-inline" data-col="' + col_name + '" data-item="' + id + '" data-href="' + rel + '" onclick="save_inline(this);">Save</button>');
    return false;
}

function add_more_trans(){
    var template = $('#lang-trans-template').html();
    $('#translate').append(template);
}

/**
 * Lưu dữ liệu tại dòng
 * @param a
 */
function save_inline(a) {
    var $this = $(a);
    var id = $this.attr('data-item');
    var rq_href = $this.attr('data-href');
    var vl = $('.value-item-' + id).val();
    var col_name = $this.attr('data-col');
    var formdata = col_name + '=' + vl;
    showMask();
    request('POST', 'json', rq_href, formdata, function (data) {
        window.location.reload();
    })
}
function showMask() {
    //Add overlay and loading img
}

function hideMask() {
    //Remove overlay and loading img
}

/**
 * Thực hiện tất cả các request liên quan đến ajax
 * @param type
 * @param dataType
 * @param url
 * @param data
 * @param callback
 */
function request(type, dataType, url, data, callback) {
    $.ajax({
        type: type,
        dataType: dataType,
        url: url,
        data: data,
        success: function (data) {
            if (typeof callback == 'function') {
                callback(data);
            }
        },
        error: function (e) {
            console.log(e);
        }
    })
}

function change_state(g){
    var $this = $(g);
    if($this.val().trim().length > 0){
        $this.parent().find('.normal').attr('checked', true);
    }else{
        $this.parent().find('.normal').attr('checked', false);
    }
}

/**
 * Bỏ dấu tiếng việt
 * @param str
 * @param fill_to
 * @returns {string|*}
 */
function locdau(str, fill_to) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/!|”|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'||\"|\&|\#|\[|\]|~|$|_/g, "");
    /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */
    str = str.trim();
    str = str.replace(/\s+/g, "-");
    str = str.replace(/-+-/g, "-");//thay thế 2- thành 1-
    str = str.replace(/^\-+|\-+$/g, "-");//cắt bỏ ký tự - ở đầu và cuối chuỗi
    str = str.replace (/[^A-Za-z0-9\-]/g,'' );
    str = $.trim(str, "-");
    $(fill_to).val(str);
    return str;
}

function doSubmit(form, action) {
    form.action = action;
    form.submit();
}

function swfUploadComment(input_thumb_name, id_editor_to_insert) {
    $('#swfupload-control').swfupload({
        upload_url: baseUrl + "/upload/uploadimage?folder=" + folder,
        file_post_name: 'uploadfile',
        file_size_limit: "2048",
        file_types: "*.jpg;*.png;*.gif",
        file_types_description: "Image files",
        file_upload_limit: 20,
        flash_url: baseUrl + "/admincp/js/plugins/scriptupload/swfupload/swfupload.swf",
        button_image_url: baseUrl + '/admincp/js/plugins/scriptupload/swfupload/wdp_buttons_upload_114x29.png',
        button_width: 114,
        button_height: 29,
        button_placeholder: $('#upload_button')[0],
        debug: false
    })
        .bind('fileQueued', function (event, file) {

            var listitem = '';
            listitem += '<div class="col-xs-2" id="' + file.id + '" >';
            listitem += '<span class="textPreocessbar">0%</span>';
            listitem += '<div class="processbarParent">';
            listitem += '<div class="processbarChild" style="width:0%;">';
            listitem += '</div>';
            listitem += '</div>';
            listitem += '<span class="cancel" >&nbsp;</span>';
            listitem += '</div>';
            $('.listImgUploadPopComment').show();
            $('.listImgUploadPopComment').append(listitem);
            $('.listImgUploadPopComment #' + file.id + ' .cancel').bind('click', function () {
                var swfu = $.swfupload.getInstance('#');
                swfu.cancelUpload(file.id);
                $('.listImgUploadPopComment #' + file.id).remove();
            });
            // start the upload since it's queued
            $(this).swfupload('startUpload');
        })
        .bind('fileQueueError', function (event, file, errorCode, message) {
            alert('Size of the file is greater than limit (10)');
        })
        .bind('fileDialogComplete', function (event, numFilesSelected, numFilesQueued) {
            // $('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
        })
        .bind('uploadStart', function (event, file) {
            $('.listImgUploadPopComment #' + file.id).find('span.textPreocessbar').text('Uploading...');
            $('.listImgUploadPopComment #' + file.id).find('div.processbarChild').css('width', '0%');
            $('.listImgUploadPopComment #' + file.id).find('span.cancel').remove();
        })
        .bind('uploadProgress', function (event, file, bytesLoaded) {
            //Show Progress
            var percentage = Math.round((bytesLoaded / file.size) * 100);
            if(percentage > 100){
                percentage = 100;
            }
            $('.listImgUploadPopComment #' + file.id).find('span.textPreocessbar').text(percentage + '%');
            $('.listImgUploadPopComment #' + file.id).find('div.processbarChild').css('width', percentage + '%');
        })
        .bind('uploadSuccess', function (event, file, serverData) {

            $('.listImgUploadPopComment #' + file.id).html('');
            var strImg = '<div class="thumbnail">';
//            strImg += '<img data="' + baseUrl + '/hinhtam/' + folder + '/' + file.name + '" src="' + baseUrl + '/hinhtam/' + folder + '/' + file.name + '" style="display:block;width:100%" onclick="javascript:insertImageToCkEditor(this.src,\'products_description\');">';
            strImg += '<img data="' + baseUrl + '/hinhtam/' + folder + '/' + file.name + '" src="' + baseUrl + '/hinhtam/' + folder + '/' + file.name + '" style="display:block;width:100%" onclick="javascript:insertImageToCkEditor(this.src,\'' + id_editor_to_insert + '\');">';
            strImg += '<label style="text-align: center;display: block">';
//            strImg += '<input type="radio" value="' + file.name + '" name="thumb_image">';
            strImg += '<input type="radio" value="' + file.name + '" name="' + input_thumb_name + '">';
            strImg += '<input type="hidden" value="' + file.name + '" name="hinh[]">';
            strImg += '<input type="hidden" name="val' + file.id + '" id="val' + file.id + '" value="' + file.name + '">';
            strImg += '</label>';
            strImg += '<span  onclick="return delImgTemp(\'' + file.id + '\',\'' + file.name + '\');" class="iconDeleteImg" title="delete image" >x</span>';
            strImg += '</div>';
            $('.listImgUploadPopComment #' + file.id).html(strImg);

            $("input[type='checkbox'], input[type='radio']").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
            if ($('#folder').val() == '') {
                $('#folder').val(folder);
            }

        })
        .bind('uploadComplete', function (event, file) {
            // upload has completed, try the next one in the queue
            $(this).swfupload('startUpload');
        })
}
function delImg(id, filename, item_id, current) {
    if (!confirm('Are you sure?')) {
        return false;
    }

    var formdata = {
        filename: filename,
        itemid: item_id
    };

    var delete_img_url = cmsUrl + '/' + current + '/deleteimage';
    request('POST', 'json', delete_img_url, formdata, function (data) {
        if (data.constructor === String) {
            data = JSON.parse(data);
        }
        if (data.success) {
            $('.listImgUploadPopComment #' + id).remove();
        }
    });
}
function delImgTemp(id, name) {
    var formdata = {
        folder: folder,
        filename: name
    };
    var delete_img_url = baseUrl + '/upload/deleteimagetemp';
    request('POST', 'json', delete_img_url, formdata, function (data) {
        if (data.constructor === String) {
            data = JSON.parse(data);
        }
        if (data.success) {
            $('.listImgUploadPopComment #' + id).remove();
        }
    });
}

/*new */
$(document).on('click', '.btn-by-thame', function(e){
    e.preventDefault();
    e.stopPropagation();
    template_id = $(this).attr('data-id');
    console.log(template_id);
    if($.trim(template_id).length > 0){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: cmsUrl + '/themes/switch-preview',
            data: {template_id: template_id},
            success: function (data) {
                if (data.constructor === String) {
                    data = JSON.parse(data);
                }
                if ( data.flag == true || data.flag == 'true' ) {
                    window.open( data.url , '_blank' );
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }
});

$(document).on('click', '.btn-add-links-menus', function(e){
    e.preventDefault();
    e.stopPropagation();
    _index = $('.row-linklist-links').length;
    var str_ = '<tr class="linklist-links ui-sortable row-linklist-links" >'+
                    '<td class="drag drag-handle text-muted" >'+
                        '<i class="fa fa-arrows fa-lg"></i>'+
                    '</td>'+
                    '<td>'+
                        '<input name="Links['+_index+'][Id]" type="hidden" value="" class="hd-Links-Id" >'+
                        '<input name="Links['+_index+'][Position]" class="link-position hd-Links-Position" type="hidden" value="">'+
                        '<input name="Links['+_index+'][Title]" class="form-control form-control smaller  hd-Links-Title" placeholder="Nhập tên liên kết">'+
                    '</td>'+
                    '<td>'+
                        '<div class="btn-group margin-sm-right" >'+
                            '<select bind="appliesToResource_0" class="form-control form-control smaller list-poisition-for-links hd-Links-Type" name="Links['+_index+'][Type]" >'+
                                '<option selected="selected" value="frontpage">Trang chủ</option>'+
                                '<option value="allcollection">Tất cả danh mục sản phẩm</option>'+
                                '<option value="collection">Danh mục sản phẩm</option>'+
                                '<option value="product">Sản phẩm</option>'+
                                '<option value="catalog">Tất cả sản phẩm</option>'+
                                '<option value="page">Trang tĩnh</option>'+
                                '<option value="article">Trang động</option>'+
                                '<option value="allblog">Tất cả blog</option>'+
                                '<option value="blog">Blog</option>'+
                                '<option value="search">Trang tìm kiếm</option>'+
                                '<option value="http">Địa chỉ web</option>'+
                                '<option value="description">Nội dung</option>'+
                            '</select>'+
                        '</div>'+
                        '<input type="hidden" name="Links['+_index+'][ItemId]" value="" class="form-control form-control smaller single-dropdown-select-id  hd-Links-ItemId" placeholder="">'+
                        '<input type="hidden" name="Links['+_index+'][ItemName]" value="" class="form-control form-control smaller single-dropdown-select-name   hd-Links-ItemName" placeholder="">'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown results-dropdown-map-links-collection applies-to-resource-el" >'+
                            '<a class="btn btn-default dropdown-toggle btn-filter btn-choose-collection" href="javascript:void(0)" bind-event-click="dropdown()">'+
                                '<span class="choosed-single">Chọn danh mục</span>'+
                                '<span class="caret"></span>'+
                            '</a>'+
                        '</div>'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown results-dropdown-map-links-product applies-to-resource-el" >'+
                            '<a class="btn btn-default dropdown-toggle btn-filter btn-choose-product" href="javascript:void(0);" >'+
                                '<span class="choosed-single">Chọn sản phẩm</span>'+
                                '<span class="caret"></span>'+
                            '</a>'+
                        '</div>'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown results-dropdown-map-links-page applies-to-resource-el" >'+
                            '<a class="btn btn-default dropdown-toggle btn-filter btn-choose-page" href="javascript:void(0)" >'+
                                '<span class="choosed-single">Chọn trang nội dung</span>'+
                                '<span class="caret"></span>'+
                            '</a>'+
                        '</div>'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown results-dropdown-map-links-article applies-to-resource-el" >'+
                            '<a class="btn btn-default dropdown-toggle btn-filter btn-choose-article" href="javascript:void(0)" >'+
                                '<span class="choosed-single">Chọn trang nội dung</span>'+
                                '<span class="caret"></span>'+
                            '</a>'+
                        '</div>'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown results-dropdown-map-links-blog applies-to-resource-el" >'+
                            '<a class="btn btn-default dropdown-toggle btn-filter btn-choose-blog" href="javascript:void(0)" >'+
                                '<span class="choosed-single">Chọn blog</span>'+
                                '<span class="caret"></span>'+
                            '</a>'+
                        '</div>'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown-map-links-http applies-to-resource-el" >'+
                            '<input type="text" name="Links['+_index+'][Url]" class="form-control form-control smaller   hd-Links-Url">'+
                        '</div>'+
                        '<div class="btn-group diable-hidden-input hide results-dropdown-map-links-description applies-to-resource-el" style="width: 100%;margin-top: 10px;" data-id="ckeditor-mini-'+_index+'" >'+
                            '<textarea name="Links['+_index+'][Description]" class="form-control ckeditor-mini" id="ckeditor-mini-'+_index+'" rows="3" value="" ></textarea>'+
                        '</div>'+
                    '</td>'+
                    '<td>'+
                        '<a class="btn btn-small btn-white btn-remove-row-links" href="javascript:void(0);" >'+
                            '<i class="fa fa-trash-o"></i>'+
                        '</a>'+
                    '</td>'+
                '</tr>';
    $('.linklist-links tbody').eq(0).append(str_);
    if($('.row-linklist-links').length<=0){
       $('.linklist-links-table').addClass('hide');
       $('.linklist-links-empty').removeClass('hide');
    }else{
        $('.linklist-links-table').removeClass('hide');
        $('.linklist-links-empty').addClass('hide');
    }
});

$(document).on('click', '.btn-remove-row-links', function(e){
    e.preventDefault();
    e.stopPropagation();
    $(this).parents('.row-linklist-links').eq(0).remove();
    if($('.row-linklist-links').length<=0){
       $('.linklist-links-table').addClass('hide');
       $('.linklist-links-empty').removeClass('hide');
    }else{
        $('.linklist-links-table').removeClass('hide');
        $('.linklist-links-empty').addClass('hide');
        $('.row-linklist-links').each(function(i_cp, el){
            $(this).find('.hd-Links-Id').eq(0).attr('name', 'Links['+i_cp+'][Id]');
            $(this).find('.hd-Links-Position').eq(0).attr('name', 'Links['+i_cp+'][Position]');
            $(this).find('.hd-Links-Title').eq(0).attr('name', 'Links['+i_cp+'][Title]');
            $(this).find('.hd-Links-Type').eq(0).attr('name', 'Links['+i_cp+'][Type]');
            $(this).find('.hd-Links-ItemId').eq(0).attr('name', 'Links['+i_cp+'][ItemId]');
            $(this).find('.hd-Links-ItemName').eq(0).attr('name', 'Links['+i_cp+'][ItemName]');
            $(this).find('.hd-Links-Url').eq(0).attr('name', 'Links['+i_cp+'][Url]');
        });
    }
});

$(document).on('change', '.list-poisition-for-links', function(e){
    blog_ = $(this).val();
    $(this).parents('.row-linklist-links').eq(0).find('.applies-to-resource-el').addClass('hide');
    if($(this).parents('.row-linklist-links').eq(0).find('.results-dropdown-map-links-'+blog_).length>0){
        $(this).parents('.row-linklist-links').eq(0).find('.results-dropdown-map-links-'+blog_).removeClass('hide');
        if(blog_ == 'description'){
            id_ = $(this).parents('.row-linklist-links').eq(0).find('.results-dropdown-map-links-'+blog_).attr('data-id');
                       
            if (typeof(CKEDITOR.instances[id_]) == 'undefined') {           
                CKEDITOR.replace(id_, config_mini);
            } else {
                CKEDITOR.instances[id_].destroy(true);
                CKEDITOR.replace(id_, config_mini);
            }
        }
    }
});

$(document).on('click', '.btn-choose-product', function(e){
    e.preventDefault();
    e.stopPropagation();
    var _seft = $(this);
    if(_seft.parents('.results-dropdown').find('.dropdown-menu').length <= 0){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: cmsUrl + '/product/single-drop-down',
            data: null,
            success: function (html) {
                _seft.parents('.results-dropdown').eq(0).append(html).toggleClass('open');
            }
        });
    }else{
        _seft.parents('.results-dropdown').eq(0).toggleClass('open');
    }
});

$(document).on('click', '.btn-choose-collection', function(e){
    e.preventDefault();
    e.stopPropagation();
    var _seft = $(this);
    if(_seft.parents('.results-dropdown').find('.dropdown-menu').length <= 0){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: cmsUrl + '/category/single-dropdown',
            data: null,
            success: function (html) {
                _seft.parents('.results-dropdown').eq(0).append(html).toggleClass('open');
            }
        });
    }else{
        _seft.parents('.results-dropdown').eq(0).toggleClass('open');
    }
});

$(document).on('click', '.btn-choose-blog', function(e){
    e.preventDefault();
    e.stopPropagation();
    var _seft = $(this);
    if(_seft.parents('.results-dropdown').find('.dropdown-menu').length <= 0){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: cmsUrl + '/category-articles/single-dropdown',
            data: null,
            success: function (html) {
                _seft.parents('.results-dropdown').eq(0).append(html).toggleClass('open');
            }
        });
    }else{
        _seft.parents('.results-dropdown').eq(0).toggleClass('open');
    }
});

$(document).on('click', '.btn-choose-article', function(e){
    e.preventDefault();
    e.stopPropagation();
    var _seft = $(this);
    if(_seft.parents('.results-dropdown').find('.dropdown-menu').length <= 0){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: cmsUrl + '/articles/single-dropdown',
            data: 'is_static=0',
            success: function (html) {
                _seft.parents('.results-dropdown').eq(0).append(html).toggleClass('open');
            }
        });
    }else{
        _seft.parents('.results-dropdown').eq(0).toggleClass('open');
    }
});
$(document).on('click', '.btn-choose-page', function(e){
    e.preventDefault();
    e.stopPropagation();
    var _seft = $(this);
    if(_seft.parents('.results-dropdown').find('.dropdown-menu').length <= 0){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: cmsUrl + '/articles/single-dropdown',
            data: 'is_static=1',
            success: function (html) {
                _seft.parents('.results-dropdown').eq(0).append(html).toggleClass('open');
            }
        });
    }else{
        _seft.parents('.results-dropdown').eq(0).toggleClass('open');
    }
});

$(document).on('click', '.product-select', function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id');
    var title = $(this).attr('data-title');
    $(this).parents('.results-dropdown').eq(0).find('.choosed-single').eq(0).html(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-name').eq(0).val(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-id').eq(0).val(id);
    $(this).parents('.results-dropdown').eq(0).toggleClass('open');
});

$(document).on('click', '.collection-select', function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id');
    var title = $(this).attr('data-title');
    $(this).parents('.results-dropdown').eq(0).find('.choosed-single').eq(0).html(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-name').eq(0).val(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-id').eq(0).val(id);
    $(this).parents('.results-dropdown').eq(0).toggleClass('open');
});

$(document).on('click', '.collection-blog', function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id');
    var title = $(this).attr('data-title');
    $(this).parents('.results-dropdown').eq(0).find('.choosed-single').eq(0).html(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-name').eq(0).val(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-id').eq(0).val(id);
    $(this).parents('.results-dropdown').eq(0).toggleClass('open');
});

$(document).on('click', '.article-select', function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id');
    var title = $(this).attr('data-title');
    $(this).parents('.results-dropdown').eq(0).find('.choosed-single').eq(0).html(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-name').eq(0).val(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-id').eq(0).val(id);
    $(this).parents('.results-dropdown').eq(0).toggleClass('open');
});

$(document).on('click', '.blog-select', function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id');
    var title = $(this).attr('data-title');
    $(this).parents('.results-dropdown').eq(0).find('.choosed-single').eq(0).html(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-name').eq(0).val(title);
    $(this).parents('.row-linklist-links').eq(0).find('input.single-dropdown-select-id').eq(0).val(id);
    $(this).parents('.results-dropdown').eq(0).toggleClass('open');
});

var INTERVAL_KEYUPQ = null;
var curent_val = '';
$(document).on('keyup', '.query-product.single-query', function(e){
    e.preventDefault();
    e.stopPropagation();
    curent_val = $(this).val();
    var _seft = $(this);
    clearTimeout(INTERVAL_KEYUPQ);
    INTERVAL_KEYUPQ = setTimeout(function(){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: cmsUrl + '/product/single-suggest',
            data: 'page=0&products_title='+curent_val,
            success: function (html) {
                _seft.parents('.dropdown-menu').find('.single-suggest-result').eq(0).html(html);
            }
        });
    }, 500);
});

$(document).on('keyup', '.query-collection.single-query', function(e){
    e.preventDefault();
    e.stopPropagation();
    curent_val = $(this).val();
    var _seft = $(this);
    clearTimeout(INTERVAL_KEYUPQ);
    INTERVAL_KEYUPQ = setTimeout(function(){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: cmsUrl + '/category/single-suggest',
            data: 'page=0&categories_title='+curent_val,
            success: function (html) {
                _seft.parents('.dropdown-menu').find('.single-suggest-result').eq(0).html(html);
            }
        });
    }, 500);
});

$(document).on('keyup', '.query-blog.single-query', function(e){
    e.preventDefault();
    e.stopPropagation();
    curent_val = $(this).val();
    var _seft = $(this);
    clearTimeout(INTERVAL_KEYUPQ);
    INTERVAL_KEYUPQ = setTimeout(function(){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: cmsUrl + '/category-articles/single-suggest',
            data: 'page=0&categories_title='+curent_val,
            success: function (html) {
                _seft.parents('.dropdown-menu').find('.single-suggest-result').eq(0).html(html);
            }
        });
    }, 500);
});

$(document).on('keyup', '.query-article.single-query', function(e){
    e.preventDefault();
    e.stopPropagation();
    curent_val = $(this).val();
    is_static = $(this).attr('data-static');
    var _seft = $(this);
    clearTimeout(INTERVAL_KEYUPQ);
    INTERVAL_KEYUPQ = setTimeout(function(){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: cmsUrl + '/articles/single-suggest',
            data: 'page=0&articles_title='+curent_val+'&is_static='+is_static,
            success: function (html) {
                _seft.parents('.dropdown-menu').find('.single-suggest-result').eq(0).html(html);
            }
        });
    }, 500);
});

function make_friendly_link(s) {
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
}

$(document).on('keyup', '.title-menus', function(e){
    e.preventDefault();
    e.stopPropagation();
    curent_val = $(this).val();
    $('#Alias').val(make_friendly_link(curent_val));
});

$(document).on('click', 'body', function(e){
    if($(e.target).closest('.applies-to-resource-el').length<=0){
        $('.applies-to-resource-el.open').removeClass('open');
    }
});

$(document).on('click', '.btn-format-currency', function(e){
    $('.box-format-currency').toggle();
});
/*
$('.tags').tagsInput({
    width: 'auto',
    //autocomplete_url: cmsUrl + '/product/get-tags',
});
$('.tags').tagsInput({
    width: 'auto',
    //autocomplete_url: cmsUrl + '/product/get-tags',
    autocomplete_url: cmsUrl + '/articles/find-articles',
    autocomplete: {
        serviceUrl: cmsUrl + '/articles/find-articles',
        onSelect: function (suggestion) {
            $('.tags').addTag(suggestion.value,{focus:true,unique:true});
        }
    }
});
*/
$('.tags').tagEditor({
    autocomplete: { 
        delay: 0, 
        position: { collision: 'flip' }, 
        source: function (request, response) {
            $.ajax({
                url: cmsUrl + '/tags/get-tags',
                data: { query: request.term },
                success: function (data) {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    var transformed = $.map(data.suggestions, function (val) {
                        return {
                            label: val.value,
                            id: val.articles_id
                        };
                    });
                    response(transformed);
                },
                error: function () {
                    response([]);
                }
            });
        },
        select: function (el, data) {
            var suggestion = data.item;
            //$('.tags').tagEditor('addTag', suggestion.value);
        }
    },
    placeholder: 'Enter tags ...',
    onChange: function(field, editor, tags) {
    },
    beforeTagSave: function(field, editor, tags, tag, val) {
        console.log(val);
        console.log(tag);
        console.log(tags);
    },
    beforeTagDelete: function(field, editor, tags, val) {
        var q = confirm('Remove tag "'+val+'"?');
        return q;
    }
});
$('.tags01').tagsInput({
    width: 'auto',
    delimiter: ';',
    defaultText : 'add url'
    //autocomplete_url: cmsUrl + '/product/get-tags',
});
if($('input[name="hide_price"]').val() == 1){
    $('.box-hide-price').hide();
}
$('#hide_price').on('ifChecked', function(event){
    $('.box-hide-price').hide();
});
$('#hide_price').on('ifUnchecked', function(event){
    $('.box-hide-price').show();
});
updateViewAddUser = function(){
    if($('.type-user select[name="type"]').val() == 'user'){
        $('.oview-admin-type').hide();
    }else{
        $('.oview-admin-type').show();
        if($('#is_administrator').is(':checked')){
            $('.oview-admin-type.groups_id').hide();
        }
    }
};
$('.type-user select[name="type"]').on('change', function(){
    updateViewAddUser();
});
$('#is_administrator').on('ifChanged', function(event){
    updateViewAddUser();
});
updateViewAddUser();
ext_id_product_type = $('.item-product-type').length+1;
$(document).on('click', '.btn-add-more-type-product', function(e){
    var tmp = $('#tmp_product_type').html();
    tmp = tmp.replace(/\{EXTID}/g, ext_id_product_type);
    $('.data-product-type').append(tmp);
    $(".data-product-type input[type='checkbox'],.data-product-type input[type='radio']").iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal'
    });
    $('.data-product-type .moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
    $('.data-product-type .numberInput').inputmask({ "mask": "9", "repeat": 10, "greedy": false });
    ext_id_product_type++;
});
$(document).on('click', '.btn-remove-item-product-type', function(e){
    //if($('.item-product-type').length>1){
    $(this).parents('.item-product-type').eq(0).remove();
    //}
});


$(document).on('click', '.btn-add-future', function(e){
    var f_ajax = $(this).parents('.add-future-ajax').eq(0);
    if(f_ajax.find('input[name="feature_title"]').length>0 
        && $.trim(f_ajax.find('input[name="feature_title"]').val()).length>0){
        $.ajax({
            type : 'POST',
            url: cmsUrl + '/feature/addAjax',
            data: 'parent_id='+f_ajax.find('input[name="parent_id"]').val() + '&feature_title=' + f_ajax.find('input[name="feature_title"]').val(),
            success: function (data) {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                console.log(data);
                if(data.flag == 'true' || data.flag == true ){
                    var feature = data.feature;
                    if(feature.parent_id == 0){
                        var str = '<li>'+
                                '<div class="clearfix" >'+ 
                                    '<div class="pull-left" >'+
                                        '<label>'+
                                            '<input type="checkbox" name="featureid[]" value="'+feature.feature_id+'" checked="checked" class="checkall" >'+
                                            feature.feature_title+
                                        '</label>'+
                                    '</div>'+
                                    '<div class="pull-right">'+
                                        '<a data-toggle="collapse" data-parent="#accordion1" data-target="#features_'+feature.feature_id+'"><i class="fa fa-chevron-down" aria-hidden="true"></i></a>'+
                                    '</div>'+
                                '</div>'+
                                '<div id="features_'+feature.feature_id+'" class="collapse" >'+
                                    '<ul class="list-child-features" >'+
                                        '<li>'+
                                            '<div class="add-future-ajax" >'+
                                                '<h4>Thêm thuộc tính cho <b>'+feature.feature_title+'</b></h4>'+
                                                '<div class="row" >'+
                                                    '<div class="form-group">'+
                                                        '<input type="hidden" name="parent_id" value="'+feature.feature_id+'" >'+
                                                        '<label class="col-xs-1" >Tên</label>'+
                                                        '<div class="col-xs-6" >'+
                                                            '<input type="text" class="form-control input-sm" name="feature_title" >'+
                                                        '</div>'+
                                                        '<div class="col-xs-3" >'+
                                                            '<a href="javascript:void(0);" class="btn btn-info btn-sm btn-add-future" >Tạo thuộc tính</a>'+
                                                        '</div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</li>'+
                                    '</ul>'+
                                '</div>'+
                            '</li>';
                            $('#accordion_features').append(str);
                    }else{
                        var str = '<li>'+
                                        '<label>'+
                                            '<input type="checkbox" name="featureid[]" value="'+feature.feature_id+'" checked="checked" class="checksub" >'+
                                            feature.feature_title+
                                        '</label>'+
                                    '</li>';
                        f_ajax.parents('li').eq(0).before(str);
                        f_ajax.parents('.list-child-features').eq(0).parents('li').eq(0).find('input.checkall').iCheck('check');
                    }
                    f_ajax.find('input[name="feature_title"]').val('');
                    $("#accordion_features input[type='checkbox'],#accordion_features input[type='radio']").iCheck({
                        checkboxClass: 'icheckbox_minimal',
                        radioClass: 'iradio_minimal'
                    });

                    setupEvenIcheckfeatures();
                }else{
                    alert(data.msg);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }else{
        alert('Chưa nhập tên');
        f_ajax.find('input[name="feature_title"]').focus();
    }
});

var featuresChildChoose =  false;
setupEvenIcheckfeatures = function(){
    $('#accordion_features  input.checkall').on('ifChecked', function (event) {
        if(!featuresChildChoose){
            $(this).parents('li').eq(0).find('.list-child-features input').iCheck('check');
        }
        featuresChildChoose = false;
    });

    $('#accordion_features  input.checkall').on('ifUnchecked', function (event) {
        if($(this).parents('li').eq(0).find('.list-child-features input.checksub:checked').length > 0){
            var seft = $(this);
            setTimeout(function(){
                seft.parents('li').eq(0).find('input.checkall').iCheck('check');
            }, 100);
        }
        
    });

    /*$('#accordion_features  input.checkall').on('ifUnchecked', function (event) {
        $(this).parents('li').eq(0).find('.list-child-features input').iCheck('uncheck');
    });*/

    $('#accordion_features  input.checksub').on('ifChecked', function (event) {
        featuresChildChoose = true;
        $(this).parents('.list-child-features').eq(0).parents('li').eq(0).find('input.checkall').iCheck('check');
        if($(this).parents('.list-child-features').eq(0).find('input.checksub:checked').length > 0 
            && featuresChildChoose ){
            featuresChildChoose = false;
        }
    });

    $('#accordion_features  input.checksub').on('ifUnchecked', function (event) {
        if($(this).parents('.list-child-features').eq(0).find('input.checksub:checked').length == 0){
            $(this).parents('.list-child-features').eq(0).parents('li').eq(0).find('input.checkall').iCheck('uncheck');
        }
    });
};
setupEvenIcheckfeatures();



$('#form-category').on('submit', function(e){
    if($.trim($('#categories_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#categories_title').focus();
        return false;
    }
    if($.trim($('#categories_alias').val()).length<=0){
        alert('vui lòng nhập alias');
        $('#categories_alias').focus();
        return false;
    }
    return true;
});
$('#form-category-articles').on('submit', function(e){
    /*if($.trim($('#categories_articles_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#categories_articles_title').focus();
        return false;
    }
    if($.trim($('#categories_articles_alias').val()).length<=0){
        alert('vui lòng nhập alias');
        $('#categories_articles_alias').focus();
        return false;
    }
    return true;*/
});

$('#form-product').on('submit', function(e){
    if($.trim($('#products_code').val()).length<=0){
        alert('vui lòng nhập mã sản phẩm');
        $('#products_code').focus();
        return false;
    }
    if($.trim($('#products_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#products_title').focus();
        return false;
    }
    if($.trim($('#products_alias').val()).length<=0){
        alert('vui lòng nhập alias');
        $('#products_alias').focus();
        return false;
    }
    if($.trim($('#categories_id').val()).length<=0 || $('#categories_id').val() == 0){
        alert('vui lòng chọn danh mục sản phẩm');
        $('#categories_id').focus();
        return false;
    }
    if($.trim($('#manufacturers_id').val()).length<=0 || $('#manufacturers_id').val() == 0){
        alert('vui lòng chọn nhà sản xuất');
        $('#manufacturers_id').focus();
        return false;
    }
    var products_description = CKEDITOR.instances['products_description'].getData();
    if($.trim(products_description).length<=0 || $(products_description).text().length<=0){
        alert('vui lòng nhập mô tả ngắn');
        $('#products_description').focus();
        return false;
    }
    /*if($.trim($('#price').val()).length<=0){
        alert('vui lòng nhập giá gốc của sản phẩm');
        $('#price').focus();
        return false;
    }
    if($.trim($('#price_sale').val()).length<=0){
        alert('vui lòng nhập giá bán cảu sản phẩm');
        $('#price_sale').focus();
        return false;
    }*/
    return true;
});

$('#form-feature').on('submit', function(e){
    /*if($.trim($('#feature_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#feature_title').focus();
        return false;
    }
    if($.trim($('#feature_alias').val()).length<=0){
        alert('vui lòng nhập alias');
        $('#feature_alias').focus();
        return false;
    }*/
    return true;
});


$('#form-manufacturers').on('submit', function(e){
    if($.trim($('#manufacturers_name').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#manufacturers_name').focus();
        return false;
    }
    return true;
});

$('#form-banner').on('submit', function(e){
    if($.trim($('input[name="banners_title"]').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('input[name="banners_title"]').focus();
        return false;
    }
    if($.trim($('#position_id').val()).length<=0 || $('#position_id').val() == 0){
        alert('vui lòng chọn vị trí banner');
        $('#position_id').focus();
        return false;
    }
    if($.trim($('#type_banners').val()).length<=0 || $('#type_banners').val() == 0){
        alert('vui lòng chọn kiểu banner');
        $('#type_banners').focus();
        return false;
    }
    if($.trim($('#size_id').val()).length<=0 || $('#size_id').val() == 0){
        alert('vui lòng chọn kích thước banner');
        $('#size_id').focus();
        return false;
    }
    if($.trim($('#link').val()).length<=0){
        alert('vui lòng nhập liên kết');
        $('#link').focus();
        return false;
    }
    return true;
});

$('#form-payment').on('submit', function(e){
    if($.trim($('#payment_name').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#payment_name').focus();
        return false;
    }
    return true;
});

$('#form-payment').on('submit', function(e){
    if($.trim($('#payment_name').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#payment_name').focus();
        return false;
    }
    return true;
});

$('#form-articles').on('submit', function(e){
    /*if($.trim($('#articles_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#articles_title').focus();
        return false;
    }
    if($.trim($('#articles_alias').val()).length<=0){
        alert('vui lòng nhập alias');
        $('#articles_alias').focus();
        return false;
    }
    if($.trim($('#categories_articles_id').val()).length<=0 || $('#categories_articles_id').val() == 0){
        alert('vui lòng chọn danh mục');
        $('#categories_articles_id').focus();
        return false;
    }
    if($.trim($('#articles_sub_content').val()).length<=0 ){
        alert('vui lòng nhập giới thiệu');
        $('#articles_sub_content').focus();
        return false;
    }
    var articles_content = CKEDITOR.instances['articles_content'].getData();
    if($.trim(articles_content).length<=0 || $(articles_content).text().length<=0){
        alert('vui lòng nhập nội dung bài viết');
        $('#articles_content').focus();
        return false;
    }*/
    return true;
});

$('#form-transportation').on('submit', function(e){
    if($.trim($('#transportation_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#transportation_title').focus();
        return false;
    }
    if($.trim($('#transportation_type').val()).length<=0){
        alert('vui lòng chọn kiểu vận chuyển');
        $('#transportation_type').focus();
        return false;
    }
    var transportation_description = CKEDITOR.instances['transportation_description'].getData();
    if($.trim(transportation_description).length<=0 || $(transportation_description).text().length<=0){
        alert('vui lòng nhập mô tả');
        $('#transportation_description').focus();
        return false;
    }
    return true;
});

$('#form-branches').on('submit', function(e){
    if($.trim($('#branches_title').val()).length<=0){
        alert('vui lòng nhập tiêu đề');
        $('#branches_title').focus();
        return false;
    }
    if($.trim($('#website').val()).length<=0){
        alert('vui lòng nhập website');
        $('#website').focus();
        return false;
    }
    if($.trim($('#email').val()).length<=0){
        alert('vui lòng nhập email');
        $('#email').focus();
        return false;
    }
    if(!isEmail($('#email').val())){
        alert('Nhập email chưa đúng');
        $('#email').focus();
        return false;
    }
    if($.trim($('#phone').val()).length<=0){
        alert('vui lòng nhập phone');
        $('#phone').focus();
        return false;
    }
    /*
    var description = CKEDITOR.instances['description'].getData();
    if($.trim(description).length<=0 || $(description).text().length<=0){
        alert('vui lòng nhập mô tả');
        $('#description').focus();
        return false;
    }*/
    if($.trim($('#address').val()).length<=0){
        alert('vui lòng nhập address');
        $('#address').focus();
        return false;
    }
    if($.trim($('#cities').val()).length<=0 || $('#cities').val() == 0){
        alert('vui lòng chọn thành phố');
        $('#cities').focus();
        return false;
    }
    if($.trim($('#districts').val()).length<=0 || $('#districts').val() == 0){
        alert('vui lòng chọn quận/huyện');
        $('#districts').focus();
        return false;
    }
    if($.trim($('#wards').val()).length<=0 || $('#wards').val() == 0){
        alert('vui lòng chọn phường/xã');
        $('#wards').focus();
        return false;
    }
/*
    if($.trim($('#latitude').val()).length<=0 || $('#latitude').val() == 0 || $.trim($('#longitude').val()).length<=0 || $('#longitude').val() == 0){
        alert('vui lòng chọn vị trí trên bản đồ');
        $('#latitude').focus();
        return false;
    } */
    return true;
});

/*new*/
$('.btn-upload-icon-category').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    $('.result-list-img').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.result-list-img').html(str);
    }
}});

$('.btn-upload-logo-manufacturers').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    $('.wrap-logo-manufacturers').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.wrap-logo-manufacturers').html(str);
    }
}});

$('.btn-upload-banner').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    $('.wrap-img-banner').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-12" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.wrap-img-banner').html(str);
    }
}});

$('.btn-upload-image-payment').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    $('.wrap-img-payment').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.wrap-img-payment').html(str);
    }
}});

$('.btn-upload-logo-website').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    $('.wrap-logo-website').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.wrap-logo-website').html(str);
    }
}});

$('.btn-upload-image-product').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    if(Object.keys(response).length>0){
        for(var i=0; i<Object.keys(response).length; i++ ){
            var picture_id = Object.keys(response)[i];
            var picture = response[picture_id];
            var src = picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type;
            if($('#image_upload_'+picture_id).length<=0){
                var str = '<div class="col-xs-2" id="image_upload_'+picture_id+'">'+
                                '<div class="thumbnail">'+
                                    '<img data="'+src+'" src="'+src+'" style="display: block;width:100%" onclick="javascript:insertImageToCkEditor(this.src,\'products_longdescription\');">'+
                                    '<label style="text-align: center;display: block">'+
                                        '<input type="radio" value="'+src+'" name="thumb_image" />'+
                                        '<input type="hidden" value="'+picture_id+'" name="picture_id[]" />'+
                                        '<span  onclick="return delImg(\'image_upload_'+picture_id+'\',\''+src+'\','+products_id+', \'product\');" class="iconDeleteImg" title="delete image" >x</span>'+
                                    '</label>'+
                                '</div>'+
                            '</div>';
                $('#swfupload-control').append(str);
                $("#image_upload_"+picture_id+" input[type='checkbox'], #image_upload_"+picture_id+" input[type='radio']").iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal'
                });
            }
        }
    }
}});

$('.btn-upload-image-article').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    if(Object.keys(response).length>0){
        for(var i=0; i<Object.keys(response).length; i++ ){
            var picture_id = Object.keys(response)[i];
            var picture = response[picture_id];
            var src = picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type;
            if($('#image_upload_'+picture_id).length<=0){
                var str = '<div class="col-xs-2" id="image_upload_'+picture_id+'">'+
                                '<div class="thumbnail">'+
                                    '<img data="'+src+'" src="'+src+'" style="display: block;width:100%" onclick="javascript:insertImageToCkEditor(this.src,\'articles_content\');">'+
                                    '<label style="text-align: center;display: block">'+
                                        '<input type="radio" value="'+src+'" name="thumb_images" />'+
                                        '<input type="hidden" value="'+picture_id+'" name="picture_id[]" />'+
                                        '<span  onclick="return delImg(\'image_upload_'+picture_id+'\',\''+src+'\','+articles_id+', \'product\');" class="iconDeleteImg" title="delete image" >x</span>'+
                                    '</label>'+
                                '</div>'+
                            '</div>';
                $('#swfupload-control').append(str);
                $("#image_upload_"+picture_id+" input[type='checkbox'], #image_upload_"+picture_id+" input[type='radio']").iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal'
                });
            }
        }
    }
}});

$('.btn-upload-thumb-templete').modal_upload({ callbacks : function(response, exdata){
    $('.result-list-img').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id[w]" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.result-thumb-website-img').html(str);
    }
}});

$('.btn-upload-thumb-mobile-templete').modal_upload({ callbacks : function(response, exdata){
    $('.result-list-img').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id[m]" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.result-thumb-mobile-img').html(str);
    }
}});

$('.btn-upload-banner-category').modal_upload({ callbacks : function(response, exdata){
    console.log(response);
    console.log(Object.keys(response));
    $('.result-list-img').html('');
    if(Object.keys(response).length>0){
        k_frist = Object.keys(response)[0];
        picture = response[k_frist];
        var str = '<div class="col-sm-4" >'+
                        '<div class="holder-img" ><img src="'+picture.picture.folder+'/'+picture.picture.name+'.'+picture.picture.type+'" class="img-responsive" /><input type="hidden" name="picture_id" value="'+k_frist+'" /></div>'+
                    '</div>';
        $('.result-list-img').html(str);
    }
}});


$('.select-trigger-tab').on('change', function (e) {
    $($(this).val()).addClass('active').siblings().removeClass('active');
});
$('input.bulid-alias').on('blur', function (e) {console.log('A');
    var el = $(this).attr('data-alias');
    locdau($(this).val(), el);
});

$('.payment_code #code').on('change', function(e){
    var code = $(this).val();
    $('.payment_atm').hide();
    $('.payment_paypal').hide();
    $('.payment_onepay').hide();
	$('.payment_vnpay').hide();
    if(code == 'ATM'){
        $('.payment_atm').show();
    }else if(code == 'PAYPAL'){
        $('.payment_paypal').show();
    }else if(code == 'ONEPAY'){
        $('.payment_onepay').show();
    }else if(code == 'VNPAY'){
        $('.payment_vnpay').show();
    }
});
$('.payment_code #code').trigger('change');

$('.citi_ajax_select').select2({
    minimumInputLength: 1,
    multiple:true,
    ajax: {
        url: cmsUrl + "/cities/ajack-filter",
        dataType: 'json',
        type:'POST',
        quietMillis: 50,
        data: function (term) {
            return {
                query: term,
                country_id: $('#country_id').val()
            };
        },
        results: function (data) {
            return {results: $.map(data, function(item){
                return {
                    text:item.cities_title,
                    id: item.cities_id
                }
            }),more: false};
        }
    },
    initSelection: function(element, callback) {
        var id=$(element).val();
        id = id.split(',');
        if (id!=="") {
            $.ajax(cmsUrl + "/cities/ajax-cities", {
                data: {
                    id:id
                },
                dataType: "json",
                type:'post'
            }).done(function(data) { callback(data); });
        }
    }
});

$('.autocomplete-shipping').select2({
    minimumInputLength: 1,
    multiple:false,
    ajax: {
        url: cmsUrl + "/trans/ajack-filter",
        dataType: 'json',
        type:'POST',
        quietMillis: 50,
        data: function (term) {
            return {
                query: term
            };
        },
        results: function (data) {
            return {results: $.map(data, function(item){
                return {
                    text:item.transportation_title,
                    id: item.transportation_id
                }
            }),more: false};
        }
    },
    initSelection: function(element, callback) {
        var id=$(element).val();
        id = id.split(',');
        if (id!=="") {
            $.ajax(cmsUrl + "/trans/ajax-transportation", {
                data: {
                    id:id
                },
                dataType: "json",
                type:'post'
            }).done(function(data) {
                if(data.length>0){
                    callback(data[0]);
                } 
            });
        }
    }
});

$('.select-shipzone-price-type').each(function(){
    val = $(this).val();
    $(this).parents('.wrap-flat-rate').eq(0).find('.shipzone-price_type.price_type-'+val).show().siblings().hide();
});

$('.select-shipzone-price-type').change(function(){
    val = $(this).val();
    $(this).parents('.wrap-flat-rate').eq(0).find('.shipzone-price_type.price_type-'+val).show().siblings().hide();
});

var number_conditions = 1;
$(document).on('change', 'select.select-free-shipzone-dk', function(){
    val = $(this).val();
    if(val == 'del'){
        if($(this).parents('.wrap-conditions').eq(0).find('.col-conditions').length>1){
            $(this).parents('.col-conditions').eq(0).prev().find('.select-free-shipzone-dk').eq(0).find('option[value="xoa"]').remove();
            $(this).parents('.col-conditions').eq(0).remove();
        }
    }else if(val == 'and' || val == 'or'){
        if($(this).parents('.wrap-conditions').eq(0).find('.col-conditions').length<2){
            str = '<div class="row col-conditions">'+
                    '<div class="col-xs-2">'+
                        '<select class="form-control" name="ship_zone[1]['+number_conditions+'][\'column_conditions_free\']">'+
                            '<option value="quality">'+
                                'Quality'+
                            '</option>'+
                            '<option value="subtotal">'+
                                'Subtotal'+
                            '</option>'+
                        '</select>'+
                    '</div>'+
                    '<div class="col-xs-2">'+
                        '<select class="form-control" name="ship_zone[1]['+number_conditions+'][\'conditions_free\']">'+
                            '<option value="<">'+
                                '<'+
                            '</option>'+
                            '<option value="<=">'+
                                '<='+
                            '</option>'+
                            '<option value=">">'+
                                '>'+
                            '</option>'+
                            '<option value=">=">'+
                                '>='+
                            '</option>'+
                            '<option value="=">'+
                                '='+
                            '</option>'+
                        '</select>'+
                    '</div>'+
                    '<div class="col-xs-2">'+
                        '<input type="text" class="form-control" name="ship_zone[1]['+number_conditions+'][\'price_conditions_free\']">'+
                    '</div>'+
                    '<div class="col-xs-3">'+
                        '<select class="form-control select-free-shipzone-dk" name="ship_zone[1]['+number_conditions+'][\'add_conditions_free\']">'+
                            '<option value="">'+
                                'Chức năng'+
                            '</option>'+
                            '<option value="and">'+
                                'Điều kiện và'+
                            '</option>'+
                            '<option value="or">'+
                                'Điều kiện hoặc'+
                            '</option>'+
                            '<option value="del">'+
                                'Xóa'+
                            '</option>'+
                        '</select>'+
                    '</div>'+
                '</div>';
                number_conditions +=1;
            $(this).parents('.col-conditions').eq(0).after(str);
        }
    }
});


initTodayRepot = function(){
    $.ajax({
        url: cmsUrl + '/invoice/repot-today',
        data: null,
        success: function (data) {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            invoices_by_day = [];
            if(typeof data.invoices_by_day != 'undefined'
                && Object.keys(data.invoices_by_day).length >0 ){
                var keys = Object.keys(data.invoices_by_day);
                for(i=0;i<=keys.length-1;i++){
                    iday = data.invoices_by_day[keys[i]];
                    invoices_by_day.push({ y:iday['day'], a: iday['subtotal'] });
                    if(i>=keys.length-1){
                        var today_chart = Morris.Bar({
                            element: 'today-chart',
                            data: invoices_by_day,
                            xkey: 'y',
                            ykeys: ['a'],
                            labels: ['Total']
                        });
                        today_chart.redraw();
                    }
                }
            }else{
                var today_chart = Morris.Bar({
                    element: 'today-chart',
                    data: [{ y: 'Today', a: 0}],
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['Total']
                });
                today_chart.redraw();
            }

            if(typeof data.products != 'undefined'
                && data.products.length >0 ){
                for(j=0;j<=data.products.length-1;j++){
                    var p = data.products[j];
                    str = '<li><a href="/'+p['products_alias']+'-'+p['products_id']+'" target="_blank" >'+p['products_title']+'</a></li>';
                    $('#tab-today-chart .list-product-buyest:eq(0)').append(str);
                }
            }else{
                $('#tab-today-chart .warp-product-buyest:eq(0)').remove();
            }
        },
        error: function () {
            response([]);
        }
    });
    $('.repot-tab.nav-tabs a[href="#tab-today-chart"]').addClass('init-chart');
};
initYesterdayRepot = function(){
    $.ajax({
        url: cmsUrl + '/invoice/repot-yesterday',
        data: null,
        success: function (data) {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            invoices_by_yesterday = [];
            if(typeof data.invoices_by_day != 'undefined' 
                && Object.keys(data.invoices_by_day).length >0){
                var keys = Object.keys(data.invoices_by_day);
                for(i=0;i<=keys.length-1;i++){
                    iday = data.invoices_by_day[keys[i]];
                    invoices_by_yesterday.push({ y:iday['day'], a: iday['subtotal'] });
                    if(i>=keys.length-1){
                        var yesterday_chart = Morris.Bar({
                            element: 'yesterday-chart',
                            data: invoices_by_yesterday,
                            xkey: 'y',
                            ykeys: ['a'],
                            labels: ['Total']
                        });
                        yesterday_chart.redraw();
                    }
                }
            }else{
                var yesterday_chart = Morris.Bar({
                    element: 'yesterday-chart',
                    data: [{ y: 'Yesterday', a: 0}],
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['Total']
                });
                yesterday_chart.redraw();
            }

            if(typeof data.products != 'undefined'
                && data.products.length >0 ){
                for(j=0;j<=data.products.length-1;j++){
                    var p = data.products[j];
                    str = '<li><a href="/'+p['products_alias']+'-'+p['products_id']+'" target="_blank" >'+p['products_title']+'</a></li>';
                    $('#tab-yesterday-chart .list-product-buyest:eq(0)').append(str);
                }
            }else{
                $('#tab-yesterday-chart .warp-product-buyest:eq(0)').remove();
            }
        },
        error: function () {
            $('#tab-yesterday-chart .warp-product-buyest:eq(0)').remove();
        }
    });
    $('.repot-tab.nav-tabs a[href="#tab-yesterday-chart"]').addClass('init-chart');
}
initLastweekRepot = function(){
    $.ajax({
        url: cmsUrl + '/invoice/repot-last-seven-days',
        data: null,
        success: function (data) {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            console.log(data);
            invoices_by_week = [];
            if(typeof data.invoices_by_day != 'undefined'
                && Object.keys(data.invoices_by_day).length >0 ){
                var keys = Object.keys(data.invoices_by_day);
                for(i=0;i<=keys.length-1;i++){
                    iday = data.invoices_by_day[keys[i]];
                    invoices_by_week.push({ y:iday['day'], item1: iday['subtotal'] });
                    if(i>=keys.length-1){
                        var lastweek_chart = Morris.Line({
                            element: 'lastweek-chart',
                            resize: true,
                            data: invoices_by_week,
                            xkey: 'y',
                            ykeys: ['item1'],
                            labels: ['Total'],
                            lineColors: ['#9ccd4f'],
                            lineWidth: 2,
                            hideHover: 'auto',
                            gridTextColor: "#fff",
                            gridStrokeWidth: 0.4,
                            pointSize: 4,
                            pointStrokeColors: ["#9ccd4f"],
                            gridLineColor: "#9ccd4f",
                            gridTextFamily: "Open Sans",
                            gridTextSize: 10
                        });
                        lastweek_chart.redraw();
                    }
                }
            }else{
                var lastweek_chart = Morris.Line({
                    element: 'lastweek-chart',
                    resize: true,
                    data: [
                      {y: 'Lastweek', item1: 0}
                    ],
                    xkey: 'y',
                    ykeys: ['item1'],
                    labels: ['Total'],
                    lineColors: ['#9ccd4f'],
                    lineWidth: 2,
                    hideHover: 'auto',
                    gridTextColor: "#fff",
                    gridStrokeWidth: 0.4,
                    pointSize: 4,
                    pointStrokeColors: ["#9ccd4f"],
                    gridLineColor: "#9ccd4f",
                    gridTextFamily: "Open Sans",
                    gridTextSize: 10
                });
                lastweek_chart.redraw();
            }

            if(typeof data.products != 'undefined'
                && data.products.length >0 ){
                console.log(data.products);
                for(j=0;j<=data.products.length-1;j++){
                    var p = data.products[j];
                    str = '<li><a href="/'+p['products_alias']+'-'+p['products_id']+'" target="_blank" >'+p['products_title']+'</a></li>';
                    $('#tab-lastweek-chart .list-product-buyest:eq(0)').append(str);
                }
            }else{
                $('#tab-lastweek-chart .warp-product-buyest:eq(0)').remove();
            }
        },
        error: function () {
            var lastweek_chart = Morris.Line({
                element: 'lastweek-chart',
                resize: true,
                data: [
                  {y: 'Lastweek', item1: 0}
                ],
                xkey: 'y',
                ykeys: ['item1'],
                labels: ['Total'],
                lineColors: ['#9ccd4f'],
                lineWidth: 2,
                hideHover: 'auto',
                gridTextColor: "#fff",
                gridStrokeWidth: 0.4,
                pointSize: 4,
                pointStrokeColors: ["#9ccd4f"],
                gridLineColor: "#9ccd4f",
                gridTextFamily: "Open Sans",
                gridTextSize: 10
            });
            lastweek_chart.redraw();
            $('#tab-lastweek-chart .warp-product-buyest:eq(0)').remove();
        }
    });
    $('.repot-tab.nav-tabs a[href="#tab-lastweek-chart"]').addClass('init-chart');
};
iniThirtyRepot = function(){
    $.ajax({
        url: cmsUrl + '/invoice/repot-last-thirty-days',
        data: null,
        success: function (data) {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            invoices_by_thirty = [];
            if(typeof data.invoices_by_day != 'undefined'
                && Object.keys(data.invoices_by_day).length >0 ){
                var keys = Object.keys(data.invoices_by_day);
                for(i=0;i<=keys.length-1;i++){
                    iday = data.invoices_by_day[keys[i]];
                    invoices_by_thirty.push({ y:iday['day'], item1: iday['subtotal'] });
                    if(i>=keys.length-1){
                        var lastthirty_chart = Morris.Line({
                            element: 'lastthirty-chart',
                            resize: true,
                            data: invoices_by_thirty,
                            xkey: 'y',
                            ykeys: ['item1'],
                            labels: ['Total'],
                            lineColors: ['#9ccd4f'],
                            lineWidth: 2,
                            hideHover: 'auto',
                            gridTextColor: "#fff",
                            gridStrokeWidth: 0.4,
                            pointSize: 4,
                            pointStrokeColors: ["#9ccd4f"],
                            gridLineColor: "#9ccd4f",
                            gridTextFamily: "Open Sans",
                            gridTextSize: 10
                        });
                        lastthirty_chart.redraw();
                    }
                }
            }else{
                var lastthirty_chart = Morris.Line({
                    element: 'lastthirty-chart',
                    resize: true,
                    data: [
                      {y: 'Lastweek', item1: 0}
                    ],
                    xkey: 'y',
                    ykeys: ['item1'],
                    labels: ['Total'],
                    lineColors: ['#9ccd4f'],
                    lineWidth: 2,
                    hideHover: 'auto',
                    gridTextColor: "#fff",
                    gridStrokeWidth: 0.4,
                    pointSize: 4,
                    pointStrokeColors: ["#9ccd4f"],
                    gridLineColor: "#9ccd4f",
                    gridTextFamily: "Open Sans",
                    gridTextSize: 10
                });
                lastthirty_chart.redraw();
            }

            if(typeof data.products != 'undefined'
                && data.products.length >0 ){
                for(j=0;j<=data.products.length-1;j++){
                    var p = data.products[j];
                    str = '<li><a href="/'+p['products_alias']+'-'+p['products_id']+'" target="_blank" >'+p['products_title']+'</a></li>';
                    $('#tab-lastthirty-chart .list-product-buyest:eq(0)').append(str);
                }
            }else{
                $('#tab-lastthirty-chart .warp-product-buyest:eq(0)').remove();
            }
        },
        error: function () {
            var lastthirty_chart = Morris.Line({
                element: 'lastthirty-chart',
                resize: true,
                data: [
                  {y: 'Lastweek', item1: 0}
                ],
                xkey: 'y',
                ykeys: ['item1'],
                labels: ['Total'],
                lineColors: ['#9ccd4f'],
                lineWidth: 2,
                hideHover: 'auto',
                gridTextColor: "#fff",
                gridStrokeWidth: 0.4,
                pointSize: 4,
                pointStrokeColors: ["#9ccd4f"],
                gridLineColor: "#9ccd4f",
                gridTextFamily: "Open Sans",
                gridTextSize: 10
            });
            lastthirty_chart.redraw();
            $('#tab-lastthirty-chart .warp-product-buyest:eq(0)').remove();
        }
    });
    $('.repot-tab.nav-tabs a[href="#tab-lastthirty-chart"]').addClass('init-chart');
};
iniSixtyRepot = function(){
    $.ajax({
        url: cmsUrl + '/invoice/repot-last-ninety-days',
        data: null,
        success: function (data) {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            invoices_by_ninety = [];
            if(typeof data.invoices_by_day != 'undefined'
                && Object.keys(data.invoices_by_day).length >0 ){
                var keys = Object.keys(data.invoices_by_day);
                for(i=0;i<=keys.length-1;i++){
                    iday = data.invoices_by_day[keys[i]];
                    invoices_by_ninety.push({ y:iday['day'], item1: iday['subtotal'] });
                    if(i>=keys.length-1){
                        var lastsixty_chart = Morris.Line({
                            element: 'lastsixty-chart',
                            resize: true,
                            data: invoices_by_ninety,
                            xkey: 'y',
                            ykeys: ['item1'],
                            labels: ['Total'],
                            lineColors: ['#9ccd4f'],
                            lineWidth: 2,
                            hideHover: 'auto',
                            gridTextColor: "#fff",
                            gridStrokeWidth: 0.4,
                            pointSize: 4,
                            pointStrokeColors: ["#9ccd4f"],
                            gridLineColor: "#9ccd4f",
                            gridTextFamily: "Open Sans",
                            gridTextSize: 10
                        });
                        lastsixty_chart.redraw();
                    }
                }
            }else{
                var lastsixty_chart = Morris.Line({
                    element: 'lastsixty-chart',
                    resize: true,
                    data: [
                      {y: 'Lastweek', item1: 0}
                    ],
                    xkey: 'y',
                    ykeys: ['item1'],
                    labels: ['Total'],
                    lineColors: ['#9ccd4f'],
                    lineWidth: 2,
                    hideHover: 'auto',
                    gridTextColor: "#fff",
                    gridStrokeWidth: 0.4,
                    pointSize: 4,
                    pointStrokeColors: ["#9ccd4f"],
                    gridLineColor: "#9ccd4f",
                    gridTextFamily: "Open Sans",
                    gridTextSize: 10
                });
                lastsixty_chart.redraw();
            }

            if(typeof data.products != 'undefined'
                && data.products.length >0 ){
                for(j=0;j<=data.products.length-1;j++){
                    var p = data.products[j];
                    str = '<li><a href="/'+p['products_alias']+'-'+p['products_id']+'" target="_blank" >'+p['products_title']+'</a></li>';
                    $('#tab-lastsixty-chart .list-product-buyest:eq(0)').append(str);
                }
            }else{
                $('#tab-lastsixty-chart .warp-product-buyest:eq(0)').remove();
            }
        },
        error: function () {
            var lastsixty_chart = Morris.Line({
                element: 'lastsixty-chart',
                resize: true,
                data: [
                  {y: 'Lastweek', item1: 0}
                ],
                xkey: 'y',
                ykeys: ['item1'],
                labels: ['Total'],
                lineColors: ['#9ccd4f'],
                lineWidth: 2,
                hideHover: 'auto',
                gridTextColor: "#fff",
                gridStrokeWidth: 0.4,
                pointSize: 4,
                pointStrokeColors: ["#9ccd4f"],
                gridLineColor: "#9ccd4f",
                gridTextFamily: "Open Sans",
                gridTextSize: 10
            });
            lastsixty_chart.redraw();
            $('#tab-lastsixty-chart .warp-product-buyest:eq(0)').remove();
        }
    });
    $('.repot-tab.nav-tabs a[href="#tab-lastsixty-chart"]').addClass('init-chart');
}
if( $('#today-chart').length>0 ){
    initTodayRepot();
}
$('.repot-tab.nav-tabs a').on('shown.bs.tab', function (e) {
    if(!$(this).hasClass('init-chart')){
        var id = $(this).attr('href');
        if(id == '#tab-today-chart'){
            initTodayRepot();
        }else if(id == '#tab-yesterday-chart'){
            initYesterdayRepot();
        }else if(id == '#tab-lastweek-chart'){
            initLastweekRepot();
        }else if(id == '#tab-lastthirty-chart'){
            iniThirtyRepot();
        }else if(id == '#tab-lastsixty-chart'){
            iniSixtyRepot();
        }
    }
});
var INTERVAL_KEYUPQ = null;
var curent_val = '';
$(document).on('keyup change', '#products_alias', function(e){
    e.preventDefault();
    e.stopPropagation();
    curent_val = $(this).val();
    var _seft = $(this);
    clearTimeout(INTERVAL_KEYUPQ);
    INTERVAL_KEYUPQ = setTimeout(function(){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: cmsUrl + '/product/find-products-by-alias',
            data: 'aias='+curent_val,
            success: function (data) {
                if(data.constructor === String){
                    data = $.parseJSON(data);
                }
                if( typeof data != 'undefined' 
                    && typeof data.products != 'undefined' 
                    && typeof data.products.products_id != 'undefined' 
                    && ( $('[name="products_id"]').eq(0).length<=0 
                         || ( $('[name="products_id"]').eq(0).length>0 && $.trim($('[name="products_id"]').eq(0).val()).length == 0)
                         || ( $('[name="products_id"]').eq(0).length>0 &&  data.products.products_id != $('[name="products_id"]').eq(0).val())) ){
                    $('#products_alias').addClass('alias-error');
                }else{
                    $('#products_alias').removeClass('alias-error');
                }
            }
        });
    }, 500);
});

$(document).on('keyup blur', '#products_title', function(e){
    e.preventDefault();
    e.stopPropagation();
    locdau(this.value, '.products_alias');
    $("#products_alias").trigger("change");
});
$("#products_alias").trigger("change");

/*api service*/
$(document).on('click', '.switch-hot-product', function(e){
    e.preventDefault();
    e.stopPropagation();
    var self = this;
    var is_hot = 0;
    if( $(this).attr('data-hot') == 0 ){
        is_hot = 1;
    }
    var url = apiUrl + '/product/'+$(this).attr('data-id')+'?module=cms&controller=product&action=singlenothot';
    $.ajax({
        type:'PUT',
        url : url,
        dataType: 'json',
        data: { is_hot: is_hot },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if( data.flag == true || data.flag == 'true'){console.log(data);
                $(self).attr('data-hot', data.product.is_hot);
                if(data.product.is_hot == 1){
                    $(self).find('.fa').removeClass('fa-circle-o').addClass('fa-circle');
                }else{
                    $(self).find('.fa').removeClass('fa-circle').addClass('fa-circle-o');
                }
            }
        },
        error: function (data) {
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
        }
    })
});

$(document).on('click', '.switch-available-product', function(e){
    e.preventDefault();
    e.stopPropagation();
    var self = this;
    var is_available = 0;
    if( $(this).attr('data-available') == 0 ){
        is_available = 1;
    }
    var url = apiUrl + '/product/'+$(this).attr('data-id')+'?module=cms&controller=product&action=singleavailable';
    $.ajax({
        type:'PUT',
        url : url,
        dataType: 'json',
        data: { is_available: is_available },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if( data.flag == true || data.flag == 'true'){console.log(data);
                $(self).attr('data-available', data.product.is_available);
                if(data.product.is_available == 1){
                    $(self).find('.fa').removeClass('fa-circle-o').addClass('fa-circle');
                }else{
                    $(self).find('.fa').removeClass('fa-circle').addClass('fa-circle-o');
                }
            }
        },
        error: function (data) {
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
        }
    })
});

$(document).on('click', '.switch-goingon-product', function(e){
    e.preventDefault();
    e.stopPropagation();
    var self = this;
    var is_goingon = 0;
    if( $(this).attr('data-goingon') == 0 ){
        is_goingon = 1;
    }
    var url = apiUrl + '/product/'+$(this).attr('data-id')+'?module=cms&controller=product&action=singlegoingon';
    $.ajax({
        type:'PUT',
        url : url,
        dataType: 'json',
        data: { is_goingon: is_goingon },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if( data.flag == true || data.flag == 'true'){console.log(data);
                $(self).attr('data-goingon', data.product.is_goingon);
                if(data.product.is_goingon == 1){
                    $(self).find('.fa').removeClass('fa-circle-o').addClass('fa-circle');
                }else{
                    $(self).find('.fa').removeClass('fa-circle').addClass('fa-circle-o');
                }
            }
        },
        error: function (data) {
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
        }
    })
});

$(document).on('click', '.switch-published-product', function(e){
    e.preventDefault();
    e.stopPropagation();
    var self = this;
    var is_published = 0;
    if( $(this).attr('data-published') == 0 ){
        is_published = 1;
    }
    var url = apiUrl + '/product/'+$(this).attr('data-id')+'?module=cms&controller=product&action=singlepublish';
    $.ajax({
        type:'PUT',
        url : url,
        dataType: 'json',
        data: { is_published: is_published },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if( data.flag == true || data.flag == 'true'){console.log(data);
                $(self).attr('data-published', data.product.is_published);
                if(data.product.is_published == 1){
                    $(self).find('.fa').removeClass('fa-circle-o').addClass('fa-circle');
                }else{
                    $(self).find('.fa').removeClass('fa-circle').addClass('fa-circle-o');
                }
            }
        },
        error: function (data) {
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
        }
    })
});

$(document).on('click', '.switch-new-product', function(e){
    e.preventDefault();
    e.stopPropagation();
    var self = this;
    var is_new = 0;
    if( $(this).attr('data-new') == 0 ){
        is_new = 1;
    }
    var url = apiUrl + '/product/'+$(this).attr('data-id')+'?module=cms&controller=product&action=singlepublish';
    $.ajax({
        type:'PUT',
        url : url,
        dataType: 'json',
        data: { is_new: is_new },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if( data.flag == true || data.flag == 'true'){console.log(data);
                $(self).attr('data-new', data.product.is_new);
                if(data.product.is_new == 1){
                    $(self).parent().eq(0).find('.tooltip-inner').html('Sản phẩm Mới');
                    $(self).attr({'title': 'Sản phẩm Mới', 'data-original-title':'Sản phẩm Mới'}).find('.fa').removeClass('fa-circle-o').addClass('fa-circle');
                }else{
                    $(self).parent().eq(0).find('.tooltip-inner').html('Không phải sản phẩm Mới');
                    $(self).attr({'title': 'Không phải sản phẩm Mới', 'data-original-title':'Không phải  sản phẩm Mới'}).find('.fa').removeClass('fa-circle').addClass('fa-circle-o');
                }
            }
        },
        error: function (data) {
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
        }
    })
});



$(document).on('change', '#group_regions_id', function(e){
    e.preventDefault();
    e.stopPropagation();
    var url = cmsUrl + '/group-regions/districts';
    var group_regions_id = $(this).val();
    $('#data-districts').html('<tr><td colspan="6" >Không có dữ liệu</td></tr>');
    $.ajax({
        type:'GET',
        url : url,
        dataType: 'json',
        data: { group_regions_id : group_regions_id },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
            if(data.cities.length > 0){
                $('#fg-shipping-districts').show();
                var str =   '';
                var keys = Object.keys(data.list);
                if( keys.length > 0){
                    $('#data-districts').html('');
                    for( d=0; d<keys.length; d++){
                        var key = keys[d];
                        var dataId = data.list[key];
                        if( dataId['data'].length>0 ){
                            str =   '<tr>'+
                                        '<td colspan="9" >'+
                                            dataId['city'].cities_title+
                                        '</td>'+
                                    '</tr>';
                            $('#data-districts').append(str); 
                            for( k=0; k<dataId['data'].length; k++){
                                var shipping_fixed_value = '';
                                var shipping_fast_fixed_value = '';
                                var shipping_fixed_time = '';
                                var no_shipping = 0;
                                if( typeof districtsShip != 'undefined'
                                    && typeof districtsShip[dataId['data'][k].districts_id] != 'undefined' ){
                                    shipping_fixed_value = districtsShip[dataId['data'][k].districts_id].shipping_fixed_value;
                                    shipping_fast_fixed_value = districtsShip[dataId['data'][k].districts_id].shipping_fast_fixed_value;
                                    shipping_fixed_time = districtsShip[dataId['data'][k].districts_id].shipping_fixed_time;
                                    no_shipping = districtsShip[dataId['data'][k].districts_id].no_shipping;
                                }
                                str =   '<tr>'+
                                            '<td style="text-align: center">'+
                                                k+
                                            '</td>'+
                                            '<td style="text-align: center">'+
                                                dataId['data'][k].districts_title+
                                            '</td>'+
                                            '<td colspan="2" >'+
                                                '<div class="clearfix">'+
                                                    '<div class="row">'+
                                                        '<div class="col-sm-6">'+
                                                            '<input name="datas['+dataId['data'][k].districts_id+'][shipping_fixed_value]" class="form-control input-sm shipping_fixed_value moneyInput'+dataId['data'][k].districts_id+'" type="text" placeholder="Chỉnh giá" value="'+((shipping_fixed_value != '' && shipping_fixed_value != 0) ? shipping_fixed_value : '')+'" >'+
                                                        '</div>'+
                                                        '<label class="col-sm-6 control-label" >'+
                                                            '<span class="txt-shipping-value txt-shipping-tbl" >'+$('#shipping_value').val()+'</span>'+
                                                        '</label>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td colspan="2" >'+
                                                '<div class="clearfix">'+
                                                    '<div class="row">'+
                                                        '<div class="col-sm-6">'+
                                                            '<input name="datas['+dataId['data'][k].districts_id+'][shipping_fast_fixed_value]" class="form-control input-sm shipping_fast_fixed_value moneyInput'+dataId['data'][k].districts_id+'" type="text" placeholder="Chỉnh giá" value="'+((shipping_fast_fixed_value != '' && shipping_fast_fixed_value != 0) ? shipping_fast_fixed_value : '')+'" >'+
                                                        '</div>'+
                                                        '<label class="col-sm-6 control-label" >'+
                                                            '<span class="txt-shipping-fast-value txt-shipping-tbl" >'+$('#shipping_fast_value').val()+'</span>'+
                                                        '</label>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td  colspan="2" >'+
                                                '<div class="clearfix">'+
                                                    '<div class="row">'+
                                                        '<div class="col-sm-6">'+
                                                            '<input name="datas['+dataId['data'][k].districts_id+'][shipping_fixed_time]" class="form-control input-sm shipping_fixed_time" type="text" placeholder="Chỉnh thời gian"  value="'+shipping_fixed_time+'" >'+
                                                        '</div>'+
                                                        '<label class="col-sm-6 control-label" >'+
                                                            '<span class="txt-shipping-time txt-shipping-tbl" >'+$('#shipping_time').val()+'</span>'+
                                                        '</label>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td>'+
                                                '<input type="hidden" name="datas['+dataId['data'][k].districts_id+'][no_shipping]" value="1" />'+
                                                '<input type="checkbox" class="cbx-'+dataId['data'][k].districts_id+'" name="datas['+dataId['data'][k].districts_id+'][no_shipping]" '+(no_shipping == 0 ? 'checked="checked"' : '')+' value="0" />'+
                                                '<a href="#" class="tooltip-'+dataId['data'][k].districts_id+'" data-toggle="tooltip" data-placement="top" title="adasd" >'+
                                                    '<i class="fa fa-question-circle" aria-hidden="true"></i>'+
                                                '</a>'+
                                            '</td>'+
                                        '</tr>';
                                $('#data-districts').append(str);
                                $("input.cbx-"+dataId['data'][k].districts_id+"[type='checkbox']").iCheck({
                                    checkboxClass: 'icheckbox_minimal',
                                    radioClass: 'iradio_minimal'
                                });
                                $('.moneyInput'+dataId['data'][k].districts_id).inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 }); 
                                $('a.tooltip-'+dataId['data'][k].districts_id+'[data-toggle="tooltip"]').tooltip();
                            }
                        }
                    }
                }else{
                    $('#data-districts').html('<tr><td colspan="6" >Không có dữ liệu</td></tr>');
                }
            }else{
                $('#fg-shipping-districts').hide();
                $('#data-districts').html('<tr><td colspan="6" >Không có dữ liệu</td></tr>');
            }
        },
        error: function (data) {
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            console.log(data);
        }
    })
});

$(document).on('keyup', 'input#shipping_value', function(){
    $('.txt-shipping-value').html($(this).val());
});
$(document).on('keyup', 'input#shipping_fast_value', function(){
    $('.txt-shipping-fast-value').html($(this).val());
});
$(document).on('keyup', 'input#shipping_time', function(){
    $('.txt-shipping-time').html($(this).val());
});

$('#group_regions_id').trigger('change');

$(document).on('change', 'select.reload-form-change-contry', function(){
    $(this).parents('form').eq(0).submit();
});

if( $('#wrap-coupons-max-use').length > 0 ){
    $('input#coupons_type[type="checkbox"]').on('ifChecked', function (event) {
        $('#wrap-coupons-max-use').show();
    });
    $('input#coupons_type[type="checkbox"]').on('ifUnchecked', function (event) {
        $('#wrap-coupons-max-use').hide();
    });
    if( $('input#coupons_type[type="checkbox"]').is(':checked') ){
        $('#wrap-coupons-max-use').show();
    }else{
        $('#wrap-coupons-max-use').hide();
    }
}

loadCitiesOfContry = function( country_id, callback ){
    $.ajax({
        url: cmsUrl + "/cities/ajack-filter",
        dataType: 'json',
        type:'POST',
        data: { country_id: country_id },
        success: function(data){
            if(data.constructor === String){
                data = JSON.parse(data);
            }
            if( typeof callback == 'function' ){
                callback(data);
            }
        },
        error: function (e) {
        }
    })
};

if( $('#form-group select.country_id').length > 0 ){
    $(document).on('change', '#form-group select.country_id', function(){
        loadCitiesOfContry( $(this).val(), function(data){
            if( data.length >0 ){
                $('#form-group #fg-regions').show();
            }else{
                $('#form-group #fg-regions').hide();
            }
        });
    });
    $('#form-group select.country_id').trigger('change');
}