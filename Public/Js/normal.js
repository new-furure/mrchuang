// JavaScript Document
$(function(){
	$(".mo").each(function(){
		$(this).mouseover(function(){
			$(this).addClass("over");
		}).mouseout(function(){
			$(this).removeClass("over");
		});
	});
	$(".tab").each(function(){
		$(this).mousedown(function(){
			$(this).addClass("sel").siblings().removeClass("sel");
			type_select=!type_select;
			if(type_select == true)
				article_type='talk';
			else article_type='idea';
		});
	});
	$(".clk").each(function(){
		$(this).mousedown(function(){
			$(this).addClass("sel");
		});
	});
	$(".upImg").click(function(){
		$(this).find(".upImgWindow").toggle();
	});
	$(".msgBox dt").mouseover(function(){
		$(this).find(".userConWindow").show();
	}).mouseout(function(){
		$(this).find(".userConWindow").hide();
	});
});