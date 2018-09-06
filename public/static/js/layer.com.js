// 依赖layer.js 必须先加载layer.js
$(function(){
    //js-window-load 打开窗口
    $(document).on('click','.js-window-load',function(){
        var url,title,width,height,shade=0.2,isId,maxmin,type = 2;
        url        = $(this).data('href') ? $(this).data('href') : $(this).attr('href');
        title      = $(this).attr('js-title') ? $(this).attr('js-title') : ($(this).attr('title') ? $(this).attr('title') :false);
        width      = $(this).attr('js-width') ? $(this).attr('js-width') : '70%';
        height     = $(this).attr('js-height') ? $(this).attr('js-height') : '85%';
        isId       = /^\#.*/;
        isFunc     = /^editor/;
        maxmin     = title ? true : false;
        shadeClose = false;
        if(isId.test(url)){
            type   = 1;
            url    = $(url);
        } else if(isFunc.test(url)){
            type       = 1;
            title      = title ? title : '预览';maxmin=true;
            url        = window[url].txt.html();
            shadeClose = true;
        }

        if ($(this).attr('js-unique') =='false') {
            shade = false;
        }
        layer.open({
            type: type,
            title: title,
            // skin: 'layui-layer-rim', //加上边框
            shadeClose: shadeClose,//点击背景关闭
            shade: shade,//背景遮罩
            closeBtn: 1,//关闭按钮
            maxmin: maxmin, //开启最大化最小化按钮
            resize:true,//拖动右下角改变大小
            area: [width, height],
            content: url,
            anim :0,//动画
            zIndex: layer.zIndex ,
            success: function(e,index){
                layer.setTop(e);
                function fn(event){
                    if(event.keyCode === 27) {
                        layer.close(index)
                    }
                 }
                $(window).off('keydown', fn).on('keydown', fn);
            }
        });
        return false;
    });

    var Base = {
        format: 'YYYY-MM-DD',
        // min: laydate.now(-7),
        // max: laydate.now(),
        istime: false, //是否开启时间选择
        isclear: false, //是否显示清空
        istoday: true, //是否显示今天
        issure: true, //是否显示确认
    }
    var date = cloneObj(Base);
    var start = cloneObj(Base);
    var end = cloneObj(Base);
    // laydate(start);
    // laydate(end);

    $('.i-date').focus(function(e){
        var id = 'date-' +parseInt(e.timeStamp);
        $(this).attr('id',id).attr('value',laydate.now('',date.format));
        date.format = $(this).attr('istime') ? 'YYYY-MM-DD hh:mm:ss' : 'YYYY-MM-DD';
        date.istime = $(this).attr('istime') ? true : false;
        date.elem = '#'.id;
        laydate(date);
        delete  date;
    })
    $('.i-datestart').focus(function(e){
        var id = 'date-' +parseInt(e.timeStamp);
        $(this).attr('id',id).attr('value',laydate.now('',start.format));
        start.elem = '#'.id;
        end.min = laydate.now('',start.format);
        start.choose = function (datas) {
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
        laydate(start);
        delete start;
    })

    $('.i-dateend').focus(function(e){
        var id = 'date-' +parseInt(e.timeStamp);
        $(this).attr('id',id).attr('value',laydate.now('',end.format));
        start.elem = '#'.id;
        end.choose = function (datas) {
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
        laydate(end);
        delete end;
    })

    $('.i-date').addClass('laydate-icon')
    $('.i-datestart').addClass('laydate-icon')
    $('.i-dateend').addClass('laydate-icon')

})


