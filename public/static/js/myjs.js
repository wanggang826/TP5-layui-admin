
//收藏夹图标						
$(".shouc").hover(
	function() {
		$(".shouc img").attr("src", "img/shoucangh.png")
	},
	function() {
		$(".shouc img").attr("src", "img/shoucang.png")
	}
);


//消息通知选项卡
$(function() {
	$(".posih").eq(0).show();
	$(".notice1").eq(0).css("background", "white");
	$(".notice1").click(function() {
		var index = $(this).index();
		$(this).css("background", "white");
		$(this).siblings().css("background", "#F6F6F6")
		$(".posih").eq(index).show().siblings().hide();

	})
})
//消息通知移除div
$(".guanbi").click(function() {
	$(this).parent().parent().remove();
})
//foot
$(function() {

	$(".ul1").children("li").eq(0).find("a").css({
		"font-weight": "bold",
		color: "black"
	})
	$(".ul2").children("li:first-child").children("a").css({
		"font-weight": "bold",
		color: "black"
	})
})

//侧边栏
			$('.h_sidebar li').click(function(){
			$(this).find('a').parent().parent().parent().find('a').removeClass('sidebar_red')
			
			$(this).parent().parent().find('li').removeClass('sidebar_lired');
			$(this).find('a').addClass('sidebar_red');
			$(this).addClass('sidebar_lired');
		   });	
//收藏选项卡
$(function() {
	$(".schead1").eq(0).css({
		"background": "white",
		"color": "#F8A6BE"
	});
	$(".schead1").eq(0).css("border-bottom", "none");
	$(".scshopp").eq(0).show();
	$(".schead1").click(function() {
		$(this).css("border-bottom", "none").siblings().css("border-bottom", "1px solid #E5E5E5")
		var index = $(this).index();
		$(".scshopp").eq(index).show().siblings(".scshopp").hide();
		$(this).css({
			"background": "white",
			"color": "#F25387"
		}).siblings(".schead1").css({
			"background": "#F6F6F6",
			"color": "#464646"
		})
	})
})
//收藏店铺
$(".tapsdiv2 p").click(function(){
	if($(this).html()==("取消关注")){
		$(this).html("关注店铺")
	}else{
		$(this).html("取消关注")
	}
	
})
//查看订单圆点
 


$(function(){
	$(".messageinfodiv").eq(0).find("img").attr("src","img/hongyuan_03.png");
	
})

