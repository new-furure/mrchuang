document.getElementById("registerbutton").setAttribute("disabled","true");

var flag_user=true;   //记录用户是否合法
var flag_pwd=true;      //记录密码是否合法
var flag_repwd=true;    //确认密码是否通过
var flag_email=true;    //记录E-mail地址是否合法
var flag_question=true; //记录密码提示问题是否输入
var flag_answer=true;   //记录提示问题答案是否输入
//验证用户名是否合法，并且未被注册
function checkUser(str){
  if(str==""){      //当用户名为空时
    //alert("昵称不能为空！");
    //document.form1.inputNickname.focus();
    document.getElementById("div_user").innerHTML="请输入昵称.";//设置提示文字
    document.getElementById("tr_user").style.display='block'; //显示提示信息
    flag_user=false;
  }else{    //进行异步操作，判断用户名是否被注册
    document.getElementById("div_user").innerHTML="";
    document.getElementById("tr_user").style.display='none';
    //var loader=new net.AjaxRequest("UserServlet?action=checkUser&username="+str+"&nocache="+new Date().getTime(),deal,onerror,"GET");
  } 
}
//验证密码
function checkPwd(str){
  if(str==""){    //当密码为空时
    //alert("密码不能为空！");
    //document.form1.inputPassword.focus();
    document.getElementById("div_pwd").innerHTML="请输入密码.";  //设置提示文字
    document.getElementById("tr_pwd").style.display='block';    //显示提示信息
    flag_pwd=false;
  }//else if(!checkpwdillgal(str)){    //当密码不合法时
    //document.getElementById("div_pwd").innerHTML="This password is illegal."; //设置提示文字
    //document.getElementById("tr_pwd").style.display='block';  //显示提示信息
    //flag_pwd=false;}
  else{    //当密码合法时
    document.getElementById("div_pwd").innerHTML="";  //清空提示文字
    document.getElementById("tr_pwd").style.display='none';   //隐藏提示信息显示行
    flag_pwd=true;
  }
}

function checkRepwd(str){
  if(str==""){    //当确认密码为空时
    //alert("密码不能为空！");
    //document.form1.inputRepassword.focus();
    document.getElementById("div_repwd").innerHTML="请输入密码.";  //设置提示文字
    document.getElementById("tr_repwd").style.display='block';  //显示提示信息
    flag_repwd=false;
  }else if(user.inputPassword.value!=str){   //当确认密码与输入的密码不一致时
    document.getElementById("div_repwd").innerHTML="两次输入的密码不一样.";  //设置提示文字
    document.getElementById("tr_repwd").style.display='block';  //显示提示信息
    flag_repwd=false;
  }else{  //当两次输入的密码一致时
    document.getElementById("div_repwd").innerHTML="";  //清空提示文字
    document.getElementById("tr_repwd").style.display='none';   //隐藏提示信息显示行
    flag_repwd=true;
    //document.getElementById("registerbutton").disabled=false;
    //document.getElementById("registerbutton").disabled=false;
  }
}

//验证E-mail地址
function checkEmail(str){
  if(str==""){//当E-mail地址为空时
    //alert("邮箱地址不能为空！");
    document.getElementById("div_email").innerHTML="请输入邮箱地址.";//设置提示信息
    document.getElementById("tr_email").style.display='block';    //显示提示信息
    flag_email=false;
  }else if(!checkemailcorrect(str)){//当E-mail地址不合法时
    document.getElementById("div_email").innerHTML="这不是一个有效的E-mail地址";//设置提示信息
    document.getElementById("tr_email").style.display='block';    //显示提示信息
    flag_email=false;
  }else{//合法
    document.getElementById("div_email").innerHTML="";//清空提示信息
    document.getElementById("tr_email").style.display='none';//不显示提示信息
    flag_email=true; 
  }
}

function checkemailcorrect(str)
{
   var re;
          var ss=document.getElementById("inputEmail").value;
          re= /\w@\w*\.\w/
         if(re.test(ss))
          return 1;
         else
          return 0;
}

function registerallcheck(){

  if(user.inputEmail.value==""){    //当E-mail地址为空时
    
    document.getElementById("div_email").innerHTML="请输入邮箱地址.";//设置提示信息
    document.getElementById("tr_email").style.display='block';    //显示提示信息
    flag_email=false;
    //return;
  }
  if(user.inputNickname.value==""){   //当用户名为空时
    //alert("请输入昵称。");user.user.focus();
    document.getElementById("div_user").innerHTML="请输入昵称.";//设置提示文字
    document.getElementById("tr_user").style.display='block'; //显示提示信息
    flag_user=false;
    //return;
  }
  if(user.inputPassword.value==""){    //当密码为空时
    //alert("请输入密码。");user.pwd.focus();
    document.getElementById("div_pwd").innerHTML="请输入密码.";  //设置提示文字
    document.getElementById("tr_pwd").style.display='block';    //显示提示信息
    flag_pwd=false;
    user.inputPassword.focus();
    //return;
  }
  if(user.inputRepassword.value==""){    //当没有输入确认密码时
    document.getElementById("div_repwd").innerHTML="请输入密码.";  //设置提示文字
    document.getElementById("tr_repwd").style.display='block';  //显示提示信息
    flag_repwd=false;
    document.getElementById("registerbutton").disabled=true;
    document.user.inputRepassword.focus();
    //return;
  }
  if(user.inputPassword.value!=user.inputRepassword.value){   //当确认密码与输入的密码不一致时
    document.getElementById("div_repwd").innerHTML="两次输入的密码不一样.";  //设置提示文字
    document.getElementById("tr_repwd").style.display="block";  //显示提示信息
    flag_repwd=false;
    document.getElementById("registerbutton").disabled=true;
    //return;
  }
  if(user.inputPassword.value==user.inputRepassword.value)
  {
    document.getElementById("div_repwd").innerHTML="";
    //document.getElementById("tr_repwd").style.display="none";
    flag_repwd=true;
    document.getElementById("registerbutton").disabled=false;
  }

    var username_error=document.getElementById("div_user").innerHTML;
     var pass_error=document.getElementById("div_pwd").innerHTML;
     var rpass_error=document.getElementById("div_repwd").innerHTML;
     var email_error=document.getElementById("div_email").innerHTML;

      if (username_error == "" && pass_error == "" && rpass_error == "" && email_error == "") {
      //alert ("验证成功，可以注册~");
      document.getElementById("registerbutton").disabled=false;
      return;
      } else {
       alert ("您的注册信息填写错误，请仔细检查更正后再提交");
      document.getElementById("registerbutton").disabled=true;
      return;
       }
}


// function checkUserLog(str){
//   if(str==""){      //当用户名为空时
//     alert("请输入邮箱地址~");
//     return;
//   }
// }
// //验证密码
// function checkPwdLog(str){
//   if(str==""){    //当密码为空时
//     alert("请输入密码~");
//     return;
//   }
// }
//
function checkLog(){
  if(logform.user_email.value==""){      //当用户名为空时
    alert("请输入邮箱地址~");
    return;
  }
  if(logform.user_passwd.value==""){    //当密码为空时
    alert("请输入密码~");
    return;
  }
  var url=document.getElementById("logform").attr("to");
  alert(url);
  document.getElementById("logform").setAttribute("action",url);
}



$(function () {

	$( '#main' ).height( $( window ).height() - $( '#top' ).height() - 45);

	var paper = $( '.paper' );
	var FW = $( window ).width();
	var FH = $( '#main' ).height();
	for (var i = 0; i < paper.length; i++) {
		var obj = paper.eq(i);
		obj.css( {
			left : parseInt(Math.random() * (FW - obj.width())) + 'px',
			top : parseInt(Math.random() * (FH - obj.height())) + 'px'
		} );
		drag(obj, $( 'dt', obj ));
	}

	paper.click( function () {
		$( this ).css( 'z-index', 1 ).siblings().css( 'z-index', 0 );
	} );

	$( '.close' ).click( function () {
		$( this ).parents( 'dl' ).fadeOut('slow');
		return false;
	} );

	$( '#send' ).click( function () {
		$( '<div id="windowBG"></div>' ).css( {
			width : $(document).width(),
 			height : $(document).height(),
 			position : 'absolute',
 			top : 0,
 			left : 0,
 			zIndex : 998,
 			opacity : 0.3,
 			filter : 'Alpha(Opacity = 30)',
 			backgroundColor : '#000000'
		} ).appendTo( 'body' );

		var obj = $( '#send-form' );
		obj.css( {
			left : ( $( window ).width() - obj.width() ) / 2,
			top : $( document ).scrollTop() + ( $( window ).height() - obj.height() ) / 2
		} ).fadeIn();
	} );

	$( '#close' ).click( function () {
		$( '#send-form' ).fadeOut( 'slow', function () {
			$( '#windowBG' ).remove();
		} );
		return false;
	} );
	

	$( 'textarea[name=content]' ).keyup( function () {
		var content = $(this).val();
		var lengths = check(content);  //调用check函数取得当前字数

		//最大允许输入50个字
		if (lengths[0] >= 50) {
			$(this).val(content.substring(0, Math.ceil(lengths[1])));
		}

		var num = 50 - Math.ceil(lengths[0]);
		var msg = num < 0 ? 0 : num;
		//当前字数同步到显示提示
		$( '#font-num' ).html( msg );
	} );

	$( '#phiz img' ).click( function () {
		var phiz = '[' + $( this ).attr('alt') + ']';
		var obj = $( 'textarea[name=content]' );
		obj.val(obj.val() + phiz);
	} );
	//alert(handleUrl);
	$('#send-btn').click( function () {

	
	var username=$('input[name=username]');
	var content=$('textarea[name=content]');
	
	if(username.val()==''){
	alert('用户名不能为空');
	username.focus();
	return;
	}
	
	if(content.val()==''){
	alert('内容不能为空');
	content.focus();
	return;
	
	}
	//var con=content.val();
	//alert(con);
	alert(handleUrl);
	$.post(handleUrl,{username:username.val(),content:content.val()},
	function(data){},'json');
	
	
	//alert('111');
	}); 
});

/**
* 元素拖拽
* @param  obj		拖拽的对象
* @param  element 	触发拖拽的对象
*/
function drag (obj, element) {
	var DX, DY, moving;

	element.mousedown(function (event) {
		obj.css( {
			zIndex : 1,
			opacity : 0.5,
 			filter : 'Alpha(Opacity = 50)'
		} );

		DX = event.pageX - parseInt(obj.css('left'));	//鼠标距离事件源宽度
		DY = event.pageY - parseInt(obj.css('top'));	//鼠标距离事件源高度

		moving = true;	//记录拖拽状态
	});

	$(document).mousemove(function (event) {
		if (!moving) return;

		var OX = event.pageX, OY = event.pageY;	//移动时鼠标当前 X、Y 位置
		var	OW = obj.outerWidth(), OH = obj.outerHeight();	//拖拽对象宽、高
		var DW = $(window).width(), DH = $(window).height();  //页面宽、高

		var left, top;	//计算定位宽、高

		left = OX - DX < 0 ? 0 : OX - DX > DW - OW ? DW - OW : OX - DX;
		top = OY - DY < 0 ? 0 : OY - DY > DH - OH ? DH - OH : OY - DY;

		obj.css({
			'left' : left + 'px',
			'top' : top + 'px'
		});

	}).mouseup(function () {
		moving = false;	//鼠标抬起消取拖拽状态

		obj.css( {
			opacity : 1,
 			filter : 'Alpha(Opacity = 100)'
		} );

	});
}

/**
 * 统计字数
 * @param  字符串
 * @return 数组[当前字数, 最大字数]
 */
function check (str) {
	var num = [0, 50];
	for (var i=0; i<str.length; i++) {
		//字符串不是中文时
		if (str.charCodeAt(i) >= 0 && str.charCodeAt(i) <= 255){
			num[0] = num[0] + 0.5;//当前字数增加0.5个
			num[1] = num[1] + 0.5;//最大输入字数增加0.5个
		} else {//字符串是中文时
			num[0]++;//当前字数增加1个
		}
	}
	return num;
}
/*政策发布的输入为空验证
*/
var flag_title=true;
var flag_profile=true;
var flag_content=true;

function CheckTitle(str)
{
  if(str=="")
  {
    document.getElementById("div_title").innerHTML="请输入标题.";
  }
  else
  {
    document.getElementById("div_title").innerHTML="";
  }
}
function CheckProfile(str)
{
  if(str=="")
  {
    document.getElementById("div_profile").innerHTML="请输入简介.";
  }
  else
  {
    document.getElementById("div_profile").innerHTML="";
  }
}
function CheckContent(str)
{
  if(str=="")
  {
    document.getElementById("div_content").innerHTML="请输入内容.";
  }
  else
  {
    document.getElementById("div_content").innerHTML="";
  }
}