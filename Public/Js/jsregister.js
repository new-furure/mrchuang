<script type="text/javascript">

//////////////////////
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
  }else if(!checkeUser(str)){ //判断用户名是否符合要求
    document.getElementById("div_user").innerHTML="昵称非法.";  //设置提示文字
    document.getElementById("tr_user").style.display='block';   //显示提示信息
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
  }else if(!checkPwd(str)){    //当密码不合法时
    document.getElementById("div_pwd").innerHTML="This password is illegal."; //设置提示文字
    document.getElementById("tr_pwd").style.display='block';  //显示提示信息
    flag_pwd=false;
  }else{    //当密码合法时
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
  }else if(form1.inputPassword.value!=str){   //当确认密码与输入的密码不一致时
    document.getElementById("div_repwd").innerHTML="两次输入的密码不一样.";  //设置提示文字
    document.getElementById("tr_repwd").style.display='block';  //显示提示信息
    flag_repwd=false;
  }else{  //当两次输入的密码一致时
    document.getElementById("div_repwd").innerHTML="";  //清空提示文字
    document.getElementById("tr_repwd").style.display='none';   //隐藏提示信息显示行
    flag_repwd=true;
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
     var username_error=document.getElementById("div_user").innerHTML;
     var pass_error=document.getElementById("div_pwd").innerHTML;
     var rpass_error=document.getElementById("div_repwd").innerHTML;
     var email_error=document.getElementById("div_email").innerHTML;

  if(form1.inputEmail.value==""){    //当E-mail地址为空时
    alert("请输入E-mail地址。");form1.email.focus();return;
  }
  if(form1.inputNickname.value==""){   //当用户名为空时
    alert("请输入昵称。");form1.user.focus();return;
  }
  if(form1.inputPassword.value==""){    //当密码为空时
    alert("请输入密码。");form1.pwd.focus();return;
  }
  if(form1.inputRepassword.value==""){    //当没有输入确认密码时
    alert("请确认密码。");form1.repwd.focus();return;
  }

      if (username_error == "" && pass_error == "" && rpass_error == "" && email_error == "") {
       var param="user="+form1.inputEmail.value+"&nickname="+form1.inputNickname.value+"&password="+form1.inputPassword.value+"&repassword="+form1.inputRepassword.value;     //组合参数 
       ////////////////////////////save////////////////////////////
      alert ("恭喜，注册成功!");
      } else {
       alert ("您的注册信息填写错误，请仔细检查更正后再提交");
      
       }
}

</script>