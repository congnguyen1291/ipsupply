/**
 * Created by viet on 5/29/14.
 */
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
    console.log('aa');
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
        select: function (suggestion) {
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
        select: function (suggestion) {
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
        select: function(suggestion){
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
        select: function(suggestion){
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
                "<td>" + products_order.products_title + "<input type='hidden' value='"+products_order.products_title+"' name='pdetail["+products_order.products_id+"][products_title]' /></td>" +
                "<td style='text-align:center'>" + "<input type='text' name='pdetail[" + products_order.products_id + "][quantity]' value='" + quantity + "' style='width:50px;text-align:center' /> " + "</td>" +
                "<td align='right'>" + accounting.formatMoney(products_order.price_sale, 'VNĐ', 0, ".", ",", "%v %s") + "<input type='hidden' name='pdetail[" + products_order.products_id + "][price]' value='" + products_order.price + "' /></td>" +
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
        select: function (suggestion) {
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

function loadDistrict(city, selected_dist, selected_ward) {
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
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.districts_id == selected_dist) {
                        $('#districts').append($("<option></option>").attr('value', row.districts_id).text(row.districts_title).attr('selected', true));
                    } else {
                        $('#districts').append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                    }
                    if (i == c - 1) {
                        loadWard($('#districts').val(), selected_ward);
                    }
                }
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
                var c = data.results.length
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.wards_id == selected) {
                        $('#wards').append($("<option></option>").attr('value', row.wards_id).text(row.wards_title).attr('selected', true));
                    } else {
                        $('#wards').append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                    }
                    if(i == c - 1){
                        updateFullAddress();
                    }
                }
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


function loadDistrict(city, selected_dist, selected_ward) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/cart/load-district',
        data: {cities_id: city},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if (data.success) {
                $('#districts option').remove();
                var c = data.results.length
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.districts_id == selected_dist) {
                        $('#districts').append($("<option></option>").attr('value', row.districts_id).text(row.districts_title).attr('selected', true));
                    } else {
                        $('#districts').append($("<option></option>").attr('value', row.districts_id).text(row.districts_title));
                    }
                    if (i == c - 1) {
                        loadWard($('#districts').val(), selected_ward);
                    }
                }
            }
        }
    });
}
function loadWard(district, selected) {
    $('#wards option').remove();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/cart/load-ward',
        data: {districts_id: district},
        success: function (data) {
            if (data.constructor === String) {
                data = JSON.parse(data);
            }
            if (data.success) {
                var c = data.results.length
                for (var i = 0; i < c; i++) {
                    var row = data.results[i];
                    if (row.wards_id == selected) {
                        $('#wards').append($("<option></option>").attr('value', row.wards_id).text(row.wards_title).attr('selected', true));
                    } else {
                        $('#wards').append($("<option></option>").attr('value', row.wards_id).text(row.wards_title));
                    }
                }
            }
        }
    });
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
    ext_id++;
}
function addExtRequire(ext){
    if(typeof ext == 'undefined'){
        ext = {
            ext_id : '',
            ext_name: '',
            ext_description: ''
        }
    }
    var tmp = $('#tmp_extension_require').html();
    tmp = tmp.replace(/\{EXTID}/g, ext_id_require);
    tmp = tmp.replace('{ext_id}', ext.ext_id);
    tmp = tmp.replace('{ext_name}', ext.ext_name);
    tmp = tmp.replace('{ext_description}', ext.ext_description);
    tmp = tmp.replace('{price}', '');
    $('#ext_box_require').append(tmp);
    $('.moneyInput').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3 });
    ext_id_require++;
}

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
    eval('CKEDITOR.instances.' + instanceName).insertHtml('<img src="' + value + '" />');
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
            url: cmsUrl + '/themes/buy',
            data: {template_id: template_id},
            success: function (data) {
                if (data.constructor === String) {
                    data = JSON.parse(data);
                }
                console.log(data);
                if (data.success) {
                    window.location.reload(true);
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
$('.tags').after('<input type="hidden" name="tags_id" />').tagEditor({
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
        select: function (suggestion) {
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