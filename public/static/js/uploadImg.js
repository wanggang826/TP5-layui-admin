imgUpload = {
    index: 0,
    fileList:{},
}
$.fn.imgUpload = function (imgConfig_def) {
    var upConfig = {
        width:'120',//预览框宽度
        height:'120',//预览框高度
        imgWidth:false,//图片上传宽度(isCut为true，则为裁剪宽度)
        imgHeight:false,//图片上传高度(isCut为true，则为裁剪高度)
        isCut:false,//是否裁剪
        cutX:0,//裁剪X轴起点
        cutY:0,//裁剪Y轴起点
        multiple:true,//是否允许多选
        btnText:'&nbsp;选择',//按钮文字
        btnClass:'btn btn-info',//按钮类名
        btnIcon:'fa fa-folder-open fa-fw',//按钮字体图标
        loadIcon:'fa fa-spinner fa-fw fa-spin',//按钮加载字体图标
        allowedNum:1,//允许上传最大数量
        picList:true,//显示上传框
        showView:true,
        showBtn:false,//是否显示按钮,picList为true,allowedNum为1时可配置,
        maxSize: 100,//允许上传最大值(KB)
        autoReplace:true,//自动替换
        allowerExt:['jpeg','png'],//允许的图片类型
        reload:true,//是否重新加载
        allowerType:[],
        files:{},//picList为true时，可自定义指定index的图片
        bg_img:''
    };
    $.extend(upConfig, imgConfig_def);
    var _that      = this;
    var _that_name = _that.attr('id');
    var _that_id   = _that.attr('id');
    _that.attr('accept', 'image/*');

    if (upConfig.reload == true) {
        this.parent().find('.img_prev').empty();
        $("#"+_that_id+"_btn").remove();
    }
    for (var i = upConfig.allowerExt.length - 1; i >= 0; i--) {
        upConfig.allowerType.push('image/'+upConfig.allowerExt[i]);
    }
    _that.attr('accept', upConfig.allowerType);
    var btn_html   = "<label for='"+_that_id+"' id='"+_that_id+"_btn' class='"+upConfig.btnClass+"'><i id='btnIcon"+_that_id+"' class='"+upConfig.btnIcon+"'></i>"+upConfig.btnText+"</label>";
    _that.removeAttr('name').removeAttr('multiple').css({'display': 'none',}).parent().append(btn_html);
    if (upConfig.picList == true) {
        if (upConfig.showBtn == true && upConfig.allowedNum ==1) {
            $("#"+_that_id+"_btn").css('display', 'inline-block');
        } else {
            $("#"+_that_id+"_btn").css('display', 'none');
        }
        for (var i = 0; i < upConfig.allowedNum; i++) {
            var img_id = _that_id+"_img_"+i;
            var html   = getImgHtml(_that_id,i,img_id,getImg());
  
            _that.parent(".img_cont").find('.img_prev').append(html);
         
        }
        setCss();
    } else if (upConfig.multiple == true) {
        _that.attr('multiple', 'multiple');
    }
    if (count(upConfig.files)>0 && upConfig.picList == true) {
        for (index in upConfig.files) {
            if ($('.'+_that_id+'[img-index="'+index+'"]').length) {
                $('.'+_that_id+'[img-index="'+index+'"]')
                        .find('img').attr('src',upConfig.files[index]).css({'max-width':'100%','max-height':'100%'});
            }
        }
    }
    var fileList = imgUpload.fileList[_that_name] = new Object;
    $('.'+_that_id+'.img_span').on('click',function() {
        imgUpload.index = $(this).attr('img-index');
        $('#'+_that_id+'_btn').click();
    });
    _that.change(function(){
        var j = 0;
        var $this = $(this);
        if (upConfig.picList != true && upConfig.autoReplace == false) {
            img_apan = $('span[id^="'+_that_id+'_img_"]');
            img_apan.each(function(index, el) {
                j = $(this).attr('img-index') > j ? $(this).attr('img-index') : j;
            });
        } else if(upConfig.picList == true){
            j = imgUpload.index;
        }
        if (upConfig.autoReplace != true) {
            $filecount = (count(fileList) + this.files.length > upConfig.allowedNum)
        } else {
            $filecount = this.files.length > upConfig.allowedNum;
        }
        if ($filecount == true) {
            showMsg('只能上传【'+upConfig.allowedNum+'】张图片')
            return;
        }
        for (var i = this.files.length - 1; i >= 0; i--) {
            imgSize = this.files[i].size/1024;
            if (!/^image\//.test(this.files[i].type) || $.inArray(this.files[i].type,upConfig.allowerType) == -1) {
                showMsg('允许的文件类型:'+upConfig.allowerExt)
                continue;
            }
            if (upConfig.maxSize>0 && imgSize > upConfig.maxSize) {
                showMsg('图片大小不能超过'+upConfig.maxSize+'KB');
                continue;
            }
            if (!_that.parent(".img_cont").find('.img_prev').length && upConfig.showView) {
                showMsg('img_prev')
                return;
            }
            var objUrl = getObjectURL(this.files[i]);
            if (objUrl) {
                $('#btnIcon'+_that_id).attr('class','').attr('class', upConfig.loadIcon);
                var img_id = _that_id+"_img_"+j;
                if (!$("#"+img_id+"_span").length) {
                    var html   = getImgHtml(_that_id,j,img_id,'');
                    if (upConfig.showView) {
                        $this.parent(".img_cont").find('.img_prev').append(html);
                    }else{
                        $this.parent(".img_cont").find('#'+img_id+'_text').remove();
                        $this.parent(".img_cont").append('<span id="'+img_id+'_text" style="padding-left:5px;">'+this.files[i].name+'</span>');
                    }
                }
                convertImgToBase64(objUrl,j,_that_id,function(_that_id,e,d){
                    var arr = new Array;
                    for(var item in fileList){
                        arr.push(fileList[item])
                    }
                    if ($.inArray(e,arr) != -1) {
                        showMsg('文件已存在，请勿重复选择',_that_id);
                        return;
                    }
                    delete arr;
                    fileList[d] = e;
                    $("#"+img_id).attr("src", e).css({'max-width':'100%','max-height':'100%'});
                    $('#btnIcon'+_that_id).attr('class','').attr('class', upConfig.btnIcon);
                },this.files[i].type);
                j++;
            }
        }
        setCss();
        _that.val('');
    });

    function showMsg(msg,id,icon){
        icon = icon ? icon : 2;
        obj = {
            title:'警告',
            content:'',
            icon: icon,
            btnAlign:'c',
            btn1:function(index){
                $('#btnIcon'+id).attr('class','').attr('class', upConfig.btnIcon);
                layer.close(index)
            }
        };
        if (typeof msg == 'object') {
            $.extend(obj,msg);
        } else {
           obj.content = msg;
        }
        layer.open(obj);
    }

    function getObjectURL(file) {
        var url = null ;
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }

    function convertImgToBase64(url,j,_that_id,callback, outputFormat){
        var canvas = document.createElement('canvas'),
        ctx = canvas.getContext('2d'),
        img = new Image;
        img.crossOrigin = 'Anonymous';
        img.onload = function(){
            canvas.height = upConfig.imgHeight>0 ? upConfig.imgHeight : img.height;
            canvas.width  = upConfig.imgWidth>0  ? upConfig.imgWidth  : img.width;
            if (upConfig.isCut == true) {
                ctx.drawImage(img, 0, 0, img.width, img.height, upConfig.cutX, upConfig.cutY, img.width, img.height);
            } else {
                ctx.drawImage(img,0,0,img.width,img.height,0,0,canvas.width,canvas.height);
            }
            var dataURL = canvas.toDataURL(outputFormat || 'image/png');
            callback.call(this,_that_id,dataURL,j);
            canvas = null;
        };
        img.src = url;
    }
    function getImgHtml(that_id,img_index,img_id,img_src){
        return '<div class="'+that_id+' img_span" img-index="'+img_index+'" id="'+img_id+'_span"><span class="img_wrap"><img id="'+img_id+'" src="'+img_src+'"></span></div>'
    }
    function getImg(){
        if (upConfig.bg_img) {
            return upConfig.bg_img;
        }
        return "data:image/jpg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QOEaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6MDk0NjA4RTlEMjMzRTQxMTg0Q0FCQUE2QzVCMDlFMjkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NEY1QzUzMTg1N0M4MTFFNzk3Q0FEQTE5MTI4RURERTQiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NEY1QzUzMTc1N0M4MTFFNzk3Q0FEQTE5MTI4RURERTQiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOmEyMTQwNjNjLThiMzItYzg0Zi04ZTJlLWU4YzY5NDgzOTdiZSIgc3RSZWY6ZG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOjA4NjVjZDA2LTQ3NWItMTFlNy04MjUxLWU3NDk3YjE4MjI3OSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pv/uAA5BZG9iZQBkwAAAAAH/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQECAgICAgICAgICAgMDAwMDAwMDAwMBAQEBAQEBAgEBAgICAQICAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDA//AABEIAGAAYAMBEQACEQEDEQH/xABpAAEBAQEBAAAAAAAAAAAAAAAABgcFCgEBAAAAAAAAAAAAAAAAAAAAABAAAQIEBAMGBwEAAAAAAAAAAAMEAQIFBhESEwchFBVBUWEiI3UxsaXVNlYXwxEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8A91AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAASl81J7R7QuGqU5blnzGmOHDVfTSW0lpIQyz6S6aqM+HdNLGHgBN7RXFWLotCSqV15zz6NSfN4r8u1beijpacmkzQbo+XNHjlxj2xA08AAAAAAAAAAAQ25n4Ddnszr5QAjtg/wBP3ipf4AbUAAAAAAAAAjbj3AtG0naLC4Kt0924bSu0UuQqbrO3mVVRgpnZMnKcuKiM0MIxhNw+GGAE/8A2nbP9l+jV/7UBKXzuvYFYtC4aXTq9zL59THDdqh0utI6q08IZZNVenJIyY9800IeIE3tFuPZlr2hJS67WeRfQqT5xFDp1Vc+itpac+qzYuEfNljwzYw7YAaf/ads/wBl+jV/7UB06NuhYtwVJrSKRXObqLyKkrZv0ysIakUUVHCkNVzT0UJMqKU0fNNDHDCHHCAF+AAAAAACNuPb+0btdov7gpPUHbdtK0RV5+ptcjeVVVaCeRk9bJzYKLTRxjCM3H44YAT/APFts/1r6zX/ALqBKXztRYFHtC4apTqDyz5jTHDhqv1StLaS0kIZZ9Jeoqoz4d00sYeAE3tFtxZl0WhJVK7RuefRqT5vFfqNVbeijpacmkzfN0fLmjxy4x7Ygaf/ABbbP9a+s1/7qB06NtfYtv1JrV6RQ+UqLOKkzZx1OsL6cVkVG6kdJzUFkJ8yKs0PNLHDHGHHCIF+AAAAAAAAAhtzPwG7PZnXygBHbB/gCfvFS/wA2oAAAAAAAAAAASl8017WLQuGl05HmXz6mOG7VDUSR1Vp4Qyyaq6iSMmPfNNCHiBN7RW7WLXtCSl11nyL6FSfOIocw1c+itpac+qzXcI+bLHhmxh2wA08AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=";
    }

    function setCss(){
        $('.'+_that_id+'.img_span').css({
            'display'    : 'inline-block',
            'background' : '#fefefe',
            'border'     : '1px solid #ddd',
            'box-shadow' : '1px 1px 5px 0 #a2958a',
            'overflow'   : 'hidden',
            'margin'     : '5px',
        });
        $('.'+_that_id+'.img_span .img_wrap').css({
            'margin'     :'6px',
            'width'      : upConfig['width']  + 'px',
            'height'     : upConfig['height'] + 'px',
            'line-height': upConfig['height'] + 'px',
            'display'    :'inline-block',
            'text-align' : 'center',
        })
    }

    function count(obj){
        var objType = typeof obj;
        if(objType == "string"){
            return obj.length;
        }else if(objType == "object"){
            var objLen = 0;
            for(var i in obj){
                objLen++;
            }
            return objLen;
        }
        return false;
    }
}