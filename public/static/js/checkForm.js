
$(function(){
    var checkForm  = $('.js-ajax-form');
    var checkInput = checkForm.find('input[js-check]');
    if (checkInput.length>0) {
        checkInput.attr('js-check-status',false);
        checkForm.find('.js-submit-btn').addClass('disabled');
    }

    $('.js-ajax-form input[js-check]').on('keyup blur',function() {
        var input = $(this);
        var form  = $(this).parents('.js-ajax-form');
        var tag   = '.js-ajax-form input[name="'+input.attr('name')+'"]';
        var func  = input.attr('js-check');
        if (typeof window[func] == 'function') {
            var re = window[func](this);
            if (re != true) {
                $(tag).attr('js-check-status',false);
                if (!$(tag).data('tips') ||  $(tag).data('msg') != re) {
                    layer.close($(tag).data('tips'));
                    $(tag).data('msg',re);
                    layerTips('<span class="msgFont" style="color:#000">'+re+'</span>',tag,'#FCEEF3',115);
                    $('.msgFont').css({
                        'padding-left': '22px',
                        'background':'url(../../static/img/5.png) no-repeat',
                    });
                }
            } else {
                layer.close($(tag).data('tips'));
                $(tag).attr('js-check-status',true);
                $(tag).data('tips',null);
            }
        }
        if (form.find('input[js-check-status="false"]').length<=0) {
            form.find('.js-submit-btn').removeClass('disabled');
        }else {
            form.find('.js-submit-btn').addClass('disabled');
        }
    });

    function layerTips(content,tag,color,offset){
        content = content ? content : '提示：错误';
        offset  = offset  ? offset  : 0;
        var tips_index = layer.open({
            content:[content,tag],
            type:4,
            shade :false,
            tips :[1,color],
            time:0,
            tipsMore: true,
            closeBtn:0,
        });
        var left = $(tag).offset().left + offset;
        $('.layui-layer-tips').css({
            'left':left
        });
        $(tag).data('tips',tips_index);
    }
})