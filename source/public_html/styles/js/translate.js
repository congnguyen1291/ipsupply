/*tao nut bat tat translate ngon ngu*/
//$('body').append('<div class="box-toggle-edit-lang" ><span class="lbl-toggle-edit-lang" ></span><a href="javascript:void(0);" class="btn-toggle-edit-lang" >Switch translate</a></div>');
console.log('dich');
$('body').prepend('<div class="coz-toolbar-action" ><div class="container" ><span class="coz-toolbar-text" >Bạn có muốn chỉnh sửa ngôn ngữ trên giao diện ?</span><a href="javascript:void(0);" class="coz-toolbar-btn" data-neo="btnSwitchTranslate" >Switch translate</a></div></div>');

$('[data-neo="btnSwitchTranslate"]').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseUrlCms+'/language/switchTranslate',
        data: null,
        cache: false,
        success: function(data)
        {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            if(data.flag == true || data.flag == 'true'){
                window.location.reload(true);                   
            }else{
                alert(data.msg);
            }
        },
        error: function(e)
        {
            console.log(e);
        }
    });
});

$('.editer-lang').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    var el_click = $(this);
    word = $(this).attr('data-key');
    $.ajax({
        type: "POST",
        dataType: "html",
        url: baseUrlCms+'/language/popupEditKeyword',
        data: 'word='+word,
        cache: false,
        success: function(html)
        {
            console.log(html);
            $('.form-editer-lang').remove();
            $('body').append('<div class="form-editer-lang" style="top:'+el_click.offset().top+'px;left:'+el_click.offset().left+'px" ><div class="inner-editer-lang" >'+html+'</div></div>');
        },
        error: function(e)
        {
            console.log(e);
        }
    });
});

$(document).on('click', '.btn-save-keyword', function(e){
    e.preventDefault();
    e.stopPropagation();
    var el_click = $(this);
    var formdata = $("#form-edit-keyword").serialize();
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseUrlCms+'/language/editKeywordAjack',
        data: formdata,
        cache: false,
        success: function(data)
        {
            if(data.constructor === String){
                data = $.parseJSON(data);
            }
            console.log(data);
            if(data.flag == true || data.flag == 'true'){
                $('.editer-lang[data-key="'+data.keyword+'"]').html(data.value);
                $('.form-editer-lang').remove();
            }else{
                alert('Có lỗi xảy ra');
            }
        },
        error: function(e)
        {
            console.log(e);
        }
    });
});

$(document).on('click', function(e){
    if($(e.target).closest('.form-editer-lang').length<=0){
        $('.form-editer-lang').remove();
    }
});
