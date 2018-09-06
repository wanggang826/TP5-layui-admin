/**
 * 使用此js包，必须先加载jquery
 */
var ajaxData    = {};
ajaxData.url    = '';
ajaxData.method = 'post';
ajaxData.dataType = 'json';
ajaxData.data     = {};
var local_url = window.location.href;
//js-del-btn删除按钮，可用于状态修改，数据删除等
$(document).on('click','.js-del-btn',function () {
	ajaxData.url  = $(this).data('href') ? $(this).data('href') : $(this).attr('href');
	var title   = $(this).attr('title'),
		text    = $(this).attr('text'),
		btn     = $(this).attr('js-btn') ? $(this).attr('js-btn') : $(this).html(),
		color   = $(this).attr('js-color') ?$(this).attr('js-color'):'#ed5565';
	var that    = this;
	layer.msg(text, {
		time: 0, //不自动关闭
		shade:0.2,
		btnAlign: 'c',
		btn: [btn, '取消'],
		success: function(e,index){
			$(e).focus();
			layer.setTop(e);
			$('.layui-layer-btn0').css({'background-color':color,'border':color});
			function fn(event){
				if(event.keyCode === 27) {
				    $('.layui-layer-btn1').click();
				} else if (event.keyCode === 13) {
					$('.layui-layer-btn0').click();
				}
			}
			$(window).off('keydown', fn).on('keydown', fn);
		},
		yes: function(index){
			layer.close(index);
            ajaxData.data.location = local_url;
			ajax_post(that)
		}
	});
    return false;
});
$(document).on('click','.js-update-btn',function () {
    ajaxData.url  = $(this).data('href') ? $(this).data('href') : $(this).attr('href');
    var title   = $(this).attr('title'),
        text    = $(this).attr('text'),
        btn     = $(this).attr('js-btn') ? $(this).attr('js-btn') : $(this).html(),
        color   = $(this).attr('js-color') ?$(this).attr('js-color'):'#ed5565';
    var that    = this;
    layer.msg(text, {
        time: 0, //不自动关闭
        shade:0.2,
        btnAlign: 'c',
        btn: [btn, '取消'],
        success: function(e,index){
            $(e).focus();
            layer.setTop(e);
            $('.layui-layer-btn0').css({'background-color':color,'border':color});
            function fn(event){
                if(event.keyCode === 27) {
                    $('.layui-layer-btn1').click();
                } else if (event.keyCode === 13) {
                    $('.layui-layer-btn0').click();
                }
            }
            $(window).off('keydown', fn).on('keydown', fn);
        },
        yes: function(index){
            layer.close(index);
            ajaxData.data.location = local_url;
            ajax_post(that)
        }
    });
    return false;
});
$(document).on('click','.del-all',function(){
    ajaxData.url  = $(this).data('href') ? $(this).data('href') : $(this).attr('href');
    var title   = $(this).attr('title'),
        text    = $(this).attr('text'),
        btn    = $(this).attr('js-btn') ? $(this).attr('js-btn') : $(this).html(),
        color   = $(this).attr('js-color') ?$(this).attr('js-color'):'#ed5565';
    var that    = this;
    ajaxData.data = {id:[]};
    $(".i-checks").each(function(){
        if(-1==$(this).attr('class').indexOf('i-check-all')){
            if($(this).is(':checked')){
                ajaxData.data.id.push($(this).attr('value'));
                // console.log(ajaxData);
            }
        }
    });
    if (0==ajaxData.data.id.length) {
    	layer.msg('请选择要操作的数据',{shade:0.2,time:1200});
    	return false;
    }
    layer.msg(text, {
        time: 0, //不自动关闭
        shade:0.2,
        btnAlign: 'c',
        btn: [btn, '取消'],
        success: function(e,index){
            $(e).focus();
            layer.setTop(e);
            $('.layui-layer-btn0').css({'background-color':color,'border':color});
            function fn(event){
                if(event.keyCode === 27) {
                    $('.layui-layer-btn1').click();
                } else if (event.keyCode === 13) {
                    $('.layui-layer-btn0').click();
                }
            }
            $(window).off('keydown', fn).on('keydown', fn);
        },
        yes: function(index){
            layer.close(index);
            ajaxData.data.location = local_url;
            ajax_post(that)
        }
    });
	return false;

})

//js-ajax-form js-submit-btn 处理ajax提交表单
$(document).on('click','.js-submit-btn',function() {

    $('.kv-zoom-cache').remove();
	window.that    = this;
    var submit_before = $(this).attr('submit-before');
	var formData = {};
	var forms = $(this).parents('form.js-ajax-form');
    if ($(this).hasClass('disabled')) { return false; }
	if (0 == forms.length) { return false; }
	ajaxData.url = forms.attr('action')?forms.attr('action'):'';
    //获取表单常规数据
	$.each(forms.serializeArray(),function() {
		ajaxData.data[this.name] = this.value;
	});
    //获取未选中复选框
    $('input[type="checkbox"]').not("input:checked").each(function(){
        var name = $(this).attr('name');
        if(!ajaxData.data.hasOwnProperty(name) && name != undefined){
            ajaxData.data[name] = '';
        }
    })
    // console.log(ajaxData.data);
    //获取base64图片
    if (typeof imgUpload == 'object') {
        ajaxData.data.uploadImg = imgUpload.fileList;
    }

    if (typeof window[submit_before] == 'function') {
        var re = window[submit_before](ajaxData);
        if (re==false) {
            return re;
        }
    }
	ajax_post(that);
	return false;
});

// js-return-btn 返回按钮处理
$('.js-return-btn').click(function(){
    var url = $(this).attr('href');
    if (url) {
        window.location.href=url;
    } else {
        window.history.go(-1);
    }
    return false;
})

//复选框全选
$(document).on('click','.my-all-check',function(){
    var elem ='input[type ="checkbox"]';
    var i = 0;
    if($(".my-all-check").is(':checked')){
        $(elem).each(function(){
             this.checked = true;
             i = i+1
        })
    var msg="选中"+(i-1)+"条数据请谨慎操作"
    // alert(msg);
    layer.msg(msg,{shade:0,time:2000});
    }else{
        $(elem).each(function(){
            this.checked = false;
        })
    }

});
function setImgUpload(id,file){
    var imgs = [],isImgUpload = $(id).parents('.imgUpload');
    if (isImgUpload.length != 1 || !isImgUpload.attr('js-img')) { return false; }
    var config = {
        language:'zh',
        showCaption: false,
        uploadUrl:'{:url("")}',
        showUpload:false,
        dropZoneEnabled: false,
        maxFileCount:5,
        autoReplace:false,
        allowedFileTypes:['jpg','jpeg','png'],
    };
    if (file) {
        for (var i = file.length - 1; i >= 0; i--) {
            var str = "<img src='"+file[i]+"' class='file-preview-image'>"
            imgs.push(str);
        }
    }
    if (imgs.length>0) {
        config.initialPreview = imgs;
        config.append = true;
        // config.initialPreviewConfig =[
        //     {
        //         url: 'http://127.0.0.3/admin/layouts/ztree', // server delete action
        //         key: 100,
        //     }
        // ]
    }
    $('.kv-zoom-cache').remove();
    $(id).fileinput(config)
    var initImg = [];
    $(id).parents('.file-input').find('.file-preview-initial').each(function(index, el) {
        if (index%2 == 0) {
            initImg.push(this);
        }
    });
    return $(id).on("filebatchselected", function(event, files) {
        // $(initImg).find('.kv-file-remove').css('visibility', 'hidden');
        // $(id).parents('.file-input').find('.file-live-thumbs').append(initImg);
    });
}

/**
 * ajax 提交数据
 */
function ajax_post(obj){
	var value   = $(obj).html()?$(obj).html():$(obj).val();
    var submit_status = '<span id="submittext"><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>';
    $(obj).addClass('disabled');
	$(obj).val (submit_status+value)
          .html(submit_status+value);
	layer.load(1,{shade:0.2});
	$.ajax(ajaxData)
	.done(ajax_success)
	.fail(ajax_fail)
	.always(ajax_always);
}

function ajax_success(re) {
	var icon;
	switch(re.code){
		case 0:
			icon = 2;break;
		case 1:
			icon = 1;break;
		case 2:
			icon = 0;break;
		default:
			icon = -1;break;
	}
    var obj = window.parent.document;
    $(obj ).find('strong').html(re['data']);
    layer.msg(re.msg,{icon:icon,time:1000,shade:0.2},function(){
		if (re.code == 1 && parent.layer.getFrameIndex(window.name)) {
            parent.layer.closeAll('page');
            parent.layer.closeAll('iframe');
            if (re.url) {
                parent.location.href = re.url;
            } else {
                parent.location.reload();
            }
		}
		if( re.url != 0 || re.url !='0' ){
			window.location.href = re.url;
		}
	});
}
function ajax_fail(re) {
	layer.msg('服务器响应超时',{icon:2,time:1000,shade:0.2});
}
function ajax_always(){
	$('#submittext').parent().removeClass('disabled');
	$('#submittext').remove();
	layer.closeAll('loading');
}

function cloneObj(obj){
      var str, newobj = obj.constructor === Array ? [] : {};
      if(typeof obj !== 'object'){
          return;
     } else if(window.JSON){
          str = JSON.stringify(obj), //序列化对象
          newobj = JSON.parse(str); //还原
      } else {
          for(var i in obj){
             newobj[i] = typeof obj[i] === 'object' ? cloneObj(obj[i]) : obj[i];
         }
     }
     return newobj;
}

$('input[type="radio"]').focus(function(){
	$(this).blur();
})
