(function(){
    var INTERVAL_STATISTICAL = null;
    var delay = 5000;/*5 giay*/
    pingStatisticalVisted = function(){
        clearTimeout(INTERVAL_STATISTICAL);
        clearInterval(INTERVAL_STATISTICAL);
        if( typeof statisticalVisted == 'function' ){
            $.ajax({
                type: "GET",
                dataType: "json",
                url: baseUrl+'/statistical/visted',
                data: '',
                cache: false,
                success: function(data)
                {
                    if(data.constructor === String){
                        data = $.parseJSON(data);
                    }
                    try{
                        statisticalVisted(data);
                    }catch(e){}
                    INTERVAL_STATISTICAL = setTimeout(function(){
                        pingStatisticalVisted();
                    }, delay);
                },
                error: function(e)
                {
                    INTERVAL_STATISTICAL = setTimeout(function(){
                        pingStatisticalVisted();
                    }, delay);
                }
            });
        }
    }
    pingStatisticalVisted();
})();