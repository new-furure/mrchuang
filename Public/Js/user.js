//任何疑问找 NewFuture
$(document).ready(function() {
  $(".focus_user").click(function() {
    f = $(this);
    f.attr('disabled',true);
    url = f.attr('to');
    $.get(url, function(data, status) {
      if (status) {
        if (data.status) { //操作成功
          var n = parseInt(f.find(".focus_num").text());
          if (data.info[1] == 1) {
            //关注成功
            f.find(".glyphicon-heart").text("取消关注");
            f.find(".focus_num").text(data.info[2]);
          } else if (data.info[1] == -1) {
            //取消成功
            f.find(".glyphicon-heart").text("关注TA");
            f.find(".focus_num").text(data.info[2]);
          }
        } else {
          //操作失败
          if (data.info[1] === 0) {
            //未登录
            alert("未登录");

          } else {
            //其他错误
            alert(data.info[1]);
          }
        }
      } else {
        alert("请求发送失败" + data);
      }
    });
    f.removeAttr('disabled');
  });


  //加入组织
  $(".apply_jion").click(function() {
    f = $(this);
    url = f.attr('url');
    id = f.attr('id');
    $.post(url, {
        oid: id,
      },
      function(data, status) {
        if (status) {

          if (data.status) {
            //操作成功
            f.attr("url", data.url);

            if (data.info == 1) {
              //申请成功
              f.text("取消申请");
            } else if (data.info == -1) {
              //撤销成功
              f.text("申请加入");
            }

          } else {
            //操作失败
            if (data.info === 0) {
              //未登录
              alert("未登录");

            } else {
              //其他错误
              alert(data.info);
            }
          }
        } else {
          alert("请求发送失败" + data);
        }
      });
  });

  //组织成员管理
  $(".member").click(function() {
    f = $(this);
    url = f.attr('url');
    pid = f.attr('pid');
    $.post(url, {
      id: pid,
    }, function(data, status) {
      if (status) {
        if (data.status) {
          //操作成功
          if (!data.url) {
            //没有返回url
          } else {
            f.attr("url", data.url);
          }

          switch (data.info[0]) {
            case 1: //邀请
              break;

            case -1: //删除
              f.parentsUntil('div').parents('div').eq(0).css("display", "none");
              break;

            case 2: //接受
              f.parentsUntil('div').parents('div').eq(0).css("display", "none");
              break;

            case 3: //成功设为普通成员
              f.text('设为管理员');
              break;

            case 4: //成功设为管理员
              f.text('取消管理员');
              break;
          }

          alert(data.info[1]);

        } else {
          //操作失败
          if (data.info === 0) {
            //未登录
            alert("未登录");

          } else {
            //其他错误
            alert("ERROR:" + data.info);
          }
        }

      } else {
        alert("请求发送失败" + data);
      }
    })
  });

  //编辑按钮
  $(".edit_location").click(function() {
    C = $("#city");
    C.children('div.edit-file').hide();
    C.children("div.edit-input").show();
    s = C.find("#loaction_display").text().split(' ');
    C.citySelect({
      prov: s[0],
      city: s[1],
      dist: s[2],
      nodata: "none"
    });

  }); //end edit 

  //保存信息
  $(".save_location").click(function() {
    C = $("#city");
    l = C.find("select");

    p = l.eq(0).val();
    c = l.eq(1).val();
    d = l.eq(2).val();
    s = '';
    for (i = 0; i < 3; i++) {
      v = l.eq(i).val();
      if (v)
        s += v + " ";
    }
    $.post($(this).parents('div.edit-input').attr('url'), 
    {
      field: 'user_location',
      value: s,
    }, function(data, status) {
      if (status) {
        if (data.status) {
          //修改成功
          C.find("#loaction_display").text(s);
          C.children(".edit-input").hide();
          C.children("div.edit-file").show();
        } else {
          //修改失败
          alert(data.info);
        }
      } else {
        alert("请求发送失败" + data);
      };
    })

  }); //end 保存信息


  //文本异步修改
  $(".edit").click(function() {
    f = $(this).parents(".edit-file");
    s = f.next('.edit-input');
    s.children('.input').val(f.children(".content").text());
    f.hide();
    s.show();
  });

//异步保存
  $(".save").click(function() {
    f = $(this).parents('div.edit-input');
    i = f.children('.input'); //输入框
     c = i.val();
     e = f.prev('.edit-file');
    form = f.parents('div.edit-form');
 
    $.post(form.attr('url'), {
        field: i.attr('name'),
         value: c,
      },
      function(data, status) {
        if (status) {
          if (data.status) {
            //修改成功
            e.children(".content").text(c);
            f.hide();
            e.show();
          } else {
            //修改失败
            alert(data.info);

          }

        } else {

          alert("请求发送失败" + data);
        }

      });
  });

$("#show-article").click(function(){
  f=$('#article');
  f.load(f.attr('from'));
});

}); //end of document