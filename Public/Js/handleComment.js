
  function checkContent(str){
  if(str==""){      //当用户名为空时
    //alert("昵称不能为空！");
    //document.post_form.inputNickname.focus();
    document.getElementById("div_content").innerHTML="请输入内容.";//设置提示文字
    document.getElementById("tr_content").style.display='block'; //显示提示信息

  }
  else{    //进行异步操作，判断用户名是否被注册
    document.getElementById("div_content").innerHTML="";
    document.getElementById("tr_content").style.display='none';
    //var loader=new net.AjaxRequest("UserServlet?action=checkUser&username="+str+"&nocache="+new Date().getTime(),deal,onerror,"GET");
  } 
}

function post_submit(){
  var flag_content=true;

  if(post_form.inputContent.value==""){   //当用户名为空时
     document.getElementById("div_content").innerHTML="请输入内容.";//设置提示文字
    document.getElementById("tr_content").style.display='block'; //显示提示信息
    flag_content=false;
  }  



  var newDigi = document.getElementById("comment_submit"); 
  var article_id=newDigi.getAttribute("aid");


  
  var newDigi2 = document.getElementById("comment_submit");

  var content=$('textarea[name=inputContent]');
  
      if (content!= "") {

        var a=$.post(handleUrlRA,{comment_content:content.val(),article_id:article_id,comment_type:comment_type},function(data){
          if(data.type=0)
            alert('评论失败，请重试');
          else{
            window.location.href=redirectUrl+"/aid/"+article_id+".html";
          }
        },'json');
        a.error(function(){alert('fail')});
      } else {
       alert ("出错了，请您重试");
      
       }
}

function post_submit_second(){

  var child=document.getElementById("add_button");
  var oComment=child.parentNode.parentNode;//这里是关键。找到当前留言对象。

   var user_reply_to_id=oComment.getAttribute("userreplytoid");
   var comment_id=oComment.getAttribute("commentid");
   var user_nickname=oComment.getAttribute("username");

  //定义处理控制器   
 
  //alert(handleUrl);
  var newDigi = document.getElementById("comment_submit"); 
  var article_id=newDigi.getAttribute("aid");
  
  var newDigi2 = document.getElementById("comment_submit");
  var content=$('textarea[name=secondinputContent]');
      if (content.val() != "") {
        // alert(handleUrl);
        // alert(content.val());
        

        var a=$.post(handleUrlRC,{second_comment_content:content.val(),comment_id:comment_id,user_reply_to_id:user_reply_to_id,article_id:article_id},function(data){
          if(data.type=0)
            alert('评论失败，请重试');
          else{
            window.location.href=redirectUrl+"/aid/"+article_id+".html";
          }
        },'json');
        a.error(function(){alert('fail')});
      } else {
        document.getElementById('secondreplyContent').focus();
      
       }
}


//对评论的回复的回复
function post_submit_second_reply(){

  var child=document.getElementById("second_add_button");
  var oComment=child.parentNode.parentNode;//这里是关键。找到当前留言对象。

   var user_reply_to_id=oComment.getAttribute("userreplytoid");
   var comment_id=oComment.getAttribute("commentid");

  //定义处理控制器   

  //alert(handleUrl);
  var newDigi = document.getElementById("comment_submit"); 
  var article_id=newDigi.getAttribute("aid");
  
  var newDigi2 = document.getElementById("comment_submit");
  var content=$('textarea[name=secondreplyContent]');

      if (content.val() != "") {
        // alert(handleUrl);
        // alert(content.val());
        

        var a=$.post(handleUrlRC,{second_comment_content:content.val(),comment_id:comment_id,user_reply_to_id:user_reply_to_id,article_id:article_id},function(data){
          if(data.type=0)
            alert('评论失败，请重试');
          else{
            window.location.href=redirectUrl+"/aid/"+article_id+".html";
          }
        },'json');
        a.error(function(){alert('fail')});
      } else {
       document.getElementById('secondreplyContent').focus();
      
       }
}


 function getid(el){return document.getElementById(el)}

function response(obj)
{
    var oComment=obj.parentNode;//这里是关键。找到当前留言对象。
    oComment.appendChild(getid("response"));
    getid("response").style.display="block";
    //向html传入当前user_id

   //var newDigi=oComment.document.getElementById("response_user"); 
   var user_nickname=oComment.getAttribute("usernickname");
   //document.getElementById("response_info").innerHTML=user_nickname;

}
function response_second(obj)
{
    var oComment=obj.parentNode;//这里是关键。找到当前留言对象。
    oComment.appendChild(getid("response_second"));
    getid("response_second").style.display="block";
}
