
/**
 * Created by Administrator on 2017/6/16 0016.
 */
$.fn.initStar = function(config_user){
    var _config = {
            width   : 30,
            height  : 22,
            starNum : 5,
            score   : 0      //分数初始化
        },
        html    = '',
        bg_odd  = "-" + (_config.width/2) + "px 0",
        bg_size = _config.width + "px " + _config.height + "px";
    _config = $.extend(_config, config_user);

        if((_config.score * 2) > (_config.starNum * 2)){
            throw "傻逼 分数不能大于 5 你是不是傻！！！ score不能大于startNum score取值在0-5区间 ";
        }
    for (var i = 0; i < _config.starNum * 2; i++) {
        html += "<li></li>";
    }
    $(this).append(html);
    $(this).find('li').css({
        'width'      : _config.width/2,
        'height'     : _config.height,
        'display'    : 'block',
        'list-style' : 'none',
        'float'      : 'left',
        'box-sizing' : 'border-box',
        'background' : 'url(./img/stark2.png) no-repeat center',
        'background-size': bg_size
    });
    for (var i = 0; i < (_config.score*2); i++) {
        $(this).find('li').eq(i).css({
            'background' : 'url(./img/stars2.png) no-repeat center',
            'background-size': bg_size
        })
    }
    $(this).find('li:even').css({
        'background-position':"0 0"
    })
    $(this).find('li:odd').css({
        'background-position':bg_odd
    })
}





















