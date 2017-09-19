console.log('v1.0');

pjaxStart = function(){
	if( $(".product-image-feature").length > 0 ) {
		var ezApi = $('.product-image-feature').data('elevateZoom');
		ezApi.changeState('disable');
		$('.zoomContainer').hide();
	}
};

pjaxComplete = function(){
	if( $(".product-image-feature").not('.pjax-init').length > 0 ) {
		if( $(window).width() >= 1200 ){
			jQuery(".product-image-feature").not('.pjax-init').addClass('pjax-init').elevateZoom({
				gallery:'sliderproduct',
				scrollZoom : true
			});
		}else{
			jQuery(".product-image-feature").not('.pjax-init').addClass('pjax-init').elevateZoom({
				gallery:'sliderproduct',
				zoomEnabled : false
			});
		}
	}
};
jQuery(document).ready(function(){
	$(".search-bar .input-search").autocomplete({
		source:[
			{
				url: baseUrl + "/product/find?keyword=%QUERY%",
				type: "remote",
				valueKey: "products_title",
				titleKey: "products_title",
				/*getTitle:function(item){
				  	return item['products_title']
				},
				getValue:function(item){
				  	return item['products_title']
				},*/
				render : function( item,source,pid,query ){
					var value = item['products_title'],
					title = item['products_title'];
					img_ = '<img src="'+item['sm_image']+'" width="100" />';
					return '<div '+(value==query?'class="active"':'')+
					' data-value="'+
					encodeURIComponent(value)+'" class="ipsm-sort" >'+
						'<div class="psm-sort clearfix" >' + 
							'<span class="psm-isort" >' + img_ + '</span>'+
							'<span class="psm-nsort" >' + title + '</span>'+
						'</div>'+
					'</div>';
				}
			}
		]
	}).on('selected.xdsoft',function(e,datum){
		if( typeof datum.link != 'undefined' && $.trim(datum.link).length > 0 ){
			window.location.href = datum.link;
		}
	});
	$(document).on('click', '[data-btn="downQuantity"]', function(){
		elq = $(this).parents('[data-plugin="quantity"]').eq(0).find('input[name="quantity"]');
		var qlt =  elq.val();
		qlt = coz.getInt(qlt);
		elq.val( Math.max((qlt - 1), 1));
	});

	$(document).on('click', '[data-btn="upQuantity"]', function(){
		elq = $(this).parents('[data-plugin="quantity"]').eq(0).find('input[name="quantity"]');
		var qlt =  elq.val();
		qlt = coz.getInt(qlt);
		elq.val(qlt + 1);
	});

	$(document).on('click', '.rm-general', function(){
		var r = confirm(language.translate('txt_ban_muon_an_no'));
        if (r == true) {
			$(this).parents('.general-box').eq(0).remove();
		}
	});

	$(document).on('click', '[data-btn="ajackProducts"]', function(e){
		e.preventDefault();
        e.stopPropagation();
        if( !$(this).hasClass('btn-loading') ){
			url = $(this).attr('href') || $(this).attr('data-href') ;
			page = $(this).attr('data-page');
			page = (parseInt(page) + 1);
			page_size = $(this).attr('data-page_size');
			el = $(this).attr('data-el');
			seft_ = $(this).addClass('btn-loading');
			$.ajax({
		        type: 'GET',
		        dataType: 'json',
		        url: url,
		        data: {_AJAX : 1, page : page, page_size : page_size},
		        success: function (data) {
		            if (data.constructor === String) {
		                data = JSON.parse(data);
		            }
		            $(el).append(data.html);
		            if( data.page >= data.pages.length ){
		            	seft_.remove();
		            }else{
		            	seft_.attr('data-page', data.page);
		            }
		            seft_.removeClass('btn-loading');
		        },
		        error : function(e){
		            seft_.removeClass('btn-loading');
		        }
		    });
		}
	});

	$(document).on('click', '.tcase-product.tcase-product-mm .nav-link, .nav-tab-detail-mm .nav-link', function(e){
	    e.preventDefault();
	    var href = $( this ).attr( 'href' );
	    $( '[data-toggle="tab"][href="' + href + '"]' ).trigger( 'click' );
	});

	$(document).on("click", ".back-to-top", function(){
		jQuery(this).removeClass('display');
		jQuery('html, body').animate({
			scrollTop: 0			
		}, 600);
	});

	$(document).on("click", ".btn-more-product-description", function(e) {
		e.preventDefault();
		$(this).hide();
		$('.mini-product-description').hide();
		$('.full-product-description').show();
		$('.btn-mini-product-description').show();
	});

	$(document).on("click", ".btn-mini-product-description", function(e) {
		e.preventDefault();
		$(this).hide();
		$('.mini-product-description').show();
		$('.full-product-description').hide();
		$('.btn-more-product-description').show();
	});

	pjaxComplete();
});

$(window).scroll(function(){
	var top_ = $(this).scrollTop();
	if( top_ > 10){
		$('.back-to-top').show();
	}else{
		$('.back-to-top').hide();
	}
});
