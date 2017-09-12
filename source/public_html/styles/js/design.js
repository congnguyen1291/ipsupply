console.log('design');
(function($, undefined) {    
    if ($.fn.resizable)
        return;

    $.fn.neoDSResizable = function neoDSResizable(options) {
        var opt = {
            handleSelector: null,
            resizeWidth: true,
            resizeHeight: true,
            onDragStart: null,
            onDragEnd: null,
            onDrag: null,
            touchActionNone: true
        };
        if (typeof options == "object") opt = $.extend(opt, options);
        
        return this.each(function () {            
            var startPos, startTransition;
            
            var $el = $(this);
            var $handle = opt.handleSelector ? $(opt.handleSelector) : $el;

            if (opt.touchActionNone)
                $handle.css("touch-action", "none");

            $('.neo-grid').addClass("resizable");
            $handle.bind('mousedown.rsz touchstart.rsz', startDragging);

            function noop(e) {
                e.stopPropagation();
                e.preventDefault();
            };
            
            function startDragging(e) {
                startPos = getMousePos(e);
                startPos.width = parseInt($('.neo-grid').width(), 10);
                startPos.height = parseInt($('.neo-grid').height(), 10);

                startTransition = $('.neo-grid').css("transition");
                $('.neo-grid').css("transition", "none");
                
                if (opt.onDragStart) {
                    if (opt.onDragStart(e, $('.neo-grid'), opt) === false)
                        return;
                }
                opt.dragFunc = doDrag;

                $(document).bind('mousemove.rsz', opt.dragFunc);
                $(document).bind('mouseup.rsz', stopDragging);
                if (window.Touch || navigator.maxTouchPoints) {
                    $(document).bind('touchmove.rsz', opt.dragFunc);
                    $(document).bind('touchend.rsz', stopDragging);                    
                }
                $(document).bind('selectstart.rsz', noop); // disable selection
            }

            function doDrag(e) {                
                var pos = getMousePos(e);
                
                if (opt.resizeWidth) {
                    var newWidth = startPos.width + pos.x - startPos.x;                    
                    $('.neo-grid').width(newWidth);
                }

                if (opt.resizeHeight) {
                    var newHeight = startPos.height + pos.y - startPos.y;                    
                    $('.neo-grid').height(newHeight);
                }

                if (opt.onDrag)
                    opt.onDrag(e, $('.neo-grid'), opt);

                //console.log('dragging', e, pos, newWidth, newHeight);
            }

            function stopDragging(e) {                
                e.stopPropagation();
                e.preventDefault();                

                $(document).unbind('mousemove.rsz', opt.dragFunc);
                $(document).unbind('mouseup.rsz', stopDragging);

                if (window.Touch || navigator.maxTouchPoints) {
                    $(document).unbind('touchmove.rsz', opt.dragFunc);
                    $(document).unbind('touchend.rsz', stopDragging);
                }
                $(document).unbind('selectstart.rsz', noop);

                // reset changed values
                $el.css("transition", startTransition);

                if (opt.onDragEnd)
                    opt.onDragEnd(e, $('.neo-grid'), opt);
                
                return false;
            }

            function getMousePos(e) {
                var pos = { x: 0, y: 0, width: 0, height: 0 };                
                if (typeof e.clientX === "number") {
                    pos.x = e.clientX;
                    pos.y = e.clientY;
                } else if (e.originalEvent.touches) {
                    pos.x = e.originalEvent.touches[0].clientX;
                    pos.y = e.originalEvent.touches[0].clientY;
                } else
                    return null;

                return pos;
            }            
        });
    };
})(jQuery,undefined);
neoDesign = function($){
	this.container = $('body');
	this.neo_grid = null;
	this.nChild = null;

	this.sizeContainerBootrap = $('.container').eq(0).width();
	this.sizeColBootrap = 'lg';
	this.elementSelected = null;
	this.initialGrid();
	this.initialUserAction();
};
neoDesign.prototype.bind_ = function(thisObj, fn) {
    return function() {
        fn.apply(thisObj, arguments);
    };
};
neoDesign.prototype.colResizeDragStart = function(){
};
neoDesign.prototype.colResizeDrag = function(){
};
neoDesign.prototype.colResizeDragEnd = function( e, el, opt ){
	console.log('aa');
	this.elementSelected.trigger('click');
};
neoDesign.prototype.updateColSize = function( n ){
	el = this.elementSelected.closest('[class*="col-'+this.sizeColBootrap+'"]').eq(0).addClass('col-'+this.sizeColBootrap+'-'+n);
	for( k=1;k<=12;k++){
		if( k != n ){
			el.removeClass('col-'+this.sizeColBootrap+'-'+k);
		}
	}
};
neoDesign.prototype.reponsiveCol = function( e, el, opt ) {
    ratio = (this.neo_grid.width()/this.sizeContainerBootrap)*12;
    ratioDot = Math.min(12, ratio);
    ratio = Math.min(12, Math.floor(ratioDot+0.5));
    this.updateColSize(ratio);
};
neoDesign.prototype.initialGrid = function() {
	selt = this;
	this.container.append('<div class="neo-grid" ><div class="neo-grid-top" ></div><div class="neo-grid-left" ></div><div class="neo-grid-bottom" ></div><div class="neo-grid-right" ></div></div>');
	this.neo_grid = $('.neo-grid');
	$(".neo-grid-top").neoDSResizable({
		resizeWidth : false,
	    onDragStart: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragStart(e, $el, opt);
	    },
	    onDrag: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDrag(e, $el, opt);
	    },
	    onDragEnd: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragEnd(e, $el, opt);
	    }
	});
	$(".neo-grid-bottom").neoDSResizable({
		resizeWidth : false,
	    onDragStart: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragStart(e, $el, opt);
	    },
	    onDrag: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDrag(e, $el, opt);
	    },
	    onDragEnd: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragEnd(e, $el, opt);
	    }
	});
	$(".neo-grid-left").neoDSResizable({
		resizeHeight : false,
	    onDragStart: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragStart(e, $el, opt);
	    },
	    onDrag: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDrag(e, $el, opt);
	    },
	    onDragEnd: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragEnd(e, $el, opt);
	    }
	});
	$(".neo-grid-right").neoDSResizable({
		resizeHeight : false,
	    onDragStart: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragStart(e, $el, opt);
	    },
	    onDrag: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDrag(e, $el, opt);
	    },
	    onDragEnd: function (e, $el, opt) {
	        selt.reponsiveCol(e, $el, opt);
	        selt.colResizeDragEnd(e, $el, opt);
	    }
	});
	this.nChild = this.container.find('[data-neo="widget"]');

};
neoDesign.prototype.initialUserAction = function() {
	selt = this;
	this.nChild.off('click').on('click', function(e){
		e.preventDefault();
        e.stopPropagation();
        selt.neo_grid.show().css({position: 'absolute', top : $(this).offset().top + 'px', left : $(this).offset().left + 'px', width : $(this).width()+'px', height : $(this).height()+'px' });
		selt.elementSelected = $(this);
	});
	$(document).on('click', function(e){
		console.log('aa');
		if( $(e.target).closest('.neo-grid').length <=0
			&& $(e.target).closest('[data-neo="widget"]').length <=0 ){
			//selt.neo_grid.hide();
		}
	});
};

//new neoDesign($);