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

	pjaxComplete();
});