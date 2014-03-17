<?php
/**
 * +---------------------------------------------------
 * +用户模块
 *
 * @author NewFuture
 * @version 2.0
 * 取消组织用户...
 * +---------------------------------------------------
 */

namespace Home\Controller;
use Think\Controller;

class UserController extends Controller{

  /******************************************************
   *             第一部分 账号管理验证
   * reg（） 注册页
   * register()注册信息处理
   * log（） 登录页
   * login() 登陆信息验证
   * logout （）注销
   * changePassword （）更改密码页
   * changePasswordSubmit() 处理修改密码
   * findPassword（） 找回密码
   * findPasswordSubmit()处理密码找回请求
   * switchAcount()切换账号
   * validate( $id=0, $key=0, $type=0 )验证url处理
   *
   * _isValidUser( $user )用户是否有效
   * ********************************************************/

  /**
   * 注册页
   *
   * @author NewFuture
   * @param unknown $goto         注册成功之后的连接
   * @param unknown $isorg        是否为组织注册
   * @param unknown $isfullscreen 是否为全屏
   */
  public function reg( $goto=null, $isorg=false, $isfullscreen=true ) {
    $this->goto=$goto;
    $this->isorg=$isorg;
    if ( I( 'session.user_id' , 0 ) &&!$isorg ) {
      //验证是否已经登录
      $this->error( "您已登录", U( 'User/index' ) );

    }elseif ( !C( "IS_REG_ON" ) ) {
      $this->error( '当前注册已关闭', U( 'Index/index' ) );
    }else {
      if ( $isfullscreen )

        $this->display( 'User/regfullscreen' );
      else
        $this->display( "User/reg" );
    }
  }


  /**
   * 组织用户和个人用户注册表单处理
   *
   * @author NewFuture
   *
   * @param string $goto注册成功后跳转页面
   * @return error或者success
   * //支持异步
   */
  public function register() {
    $error=null;
    $invite_code=I( 'post.invite_code' );

    $goto=I( 'goto' );//注册成功之后跳转的页面
    $User=D( 'User' );
    $isorg=I( "post.isorg" );

    if ( C( "IS_CODE_NEED" )&& !$isorg ) {
      //需要邀请码
      if ( strlen( $invite_code )!=8 ) {
        $error="邀请码错误";
      }elseif ( invite( $invite_code )===true ) {
        //邀请码正确
      }else {
        $error="验证码错误";
      }
    }

    if ( $error ) {
      $this->error( $error );
    }elseif ( !C( 'IS_REG_ON' ) ) {

      $error="注册已关闭！";
    }elseif ( $isorg ) {
      //组织注册
      $p_id=get_id();
      $p_user=M( 'User' )->getByUserId( $p_id );
      if ( !$this->_isValidUser( $p_user ) ) {
        $error="当前账号不可用";
      }elseif ( ac( $p_user['user_type'], 3 )!=C( "NO_ORG" ) ) {
        $error="组织或者已加入组织的账号不可注册组织账号";
      }elseif ( !$User->create() ) {
        $error="注册失败!".$User->getError();
      }else {
        //可以注册
        $User->user_type=0;
        set_user_type( $User->user_type, 1, 1, C( "STARTUP" ) );
        $o_id=$User->add();
        if ( $o_id ) {
          $org['user_id']=$o_id;
          $org['category_id']=C( 'STARTUP' );
          if ( !M( "organization" )->add( $org ) ) {
            //组织表插入失败
            $error="组织创建失败！";
            $User->where( "user_id=".$o_id )->delete();
          }else {
            //关联账户
            $p2o['user_id']=$p_id;
            $p2o['organization_user_id']=$o_id;
            $p2o['user_status']=C( 'ADMIN_USER' );//默认为管理员
            $p2o['belong_to_organization_info']="创始人";

            if ( M( 'belong_to_organization' )->add( $p2o ) ) {
              //组织创建完成
              //修改用户类型
              set_user_type( $p_user['user_type'], null, null, C( "STARTUP" ) );
              M( "User" )->save( $p_user );
              //发送邮件
              send_mail( I( 'post.user_email' ), C( "CREATE_ORG_MAIL" ) );
              send_mail( $p_user['user_email'], C( "CREATE_ORG_MAIL" ) );


              $this->success( "组织创建成功", $goto );

            }else {
              $error="创建组织关联失败";
              M( 'organization' )->where( 'user_id='.$o_id )->delete();
              $User->where( "user_id=".$o_id )->delete();
            }
          }
        }

      }

    }else {
      //个人注册

      if ( !$User->create() ) {
        $error=( "注册失败!".$User->getError() );
      }else {

        if ( !C( 'IS_EMAIL_VALIDATE_ON' ) ) {
          //不需要邮箱验证

          set_user_type( $t_user_type, 1, 0, C( "STARTUP" ) );
          $User->user_type=$t_user_type;

        }
        $id=$User->add();
        if ( $id>0 ) {

          $Person=M( "Person" );
          $person_info["user_id"]=$id;

          if ( $Person->add( $person_info ) ) {

            if ( C( 'IS_CODE_NEED' ) ) {
              //删除邀请码
              delete_invitation( $invite_code );
            }
            if ( $goto==null ) {
              //成功跳转默认为登陆页
              $goto=U( 'User/Log' );
            }

            if ( C( 'IS_EMAIL_VALIDATE_ON' )==true ) {
              //开启邮箱验证
              $key=validate( $id );
              $url="http://".I( 'server.HTTP_HOST' ).U( "/User/validate", "id=$id&key=$key&type=".C( "ACTIVE_MAIL" ) );
              send_mail( I( "post.user_email" ), C( 'ACTIVE_MAIL' ), '<a href="'.$url.'">'.$url.'</a>' );
              $this->success( '注册成功！查看邮箱激活账号！', $goto );
            }elseif(IS_AJAX){
              $this->success($id-C("MIN_USER_ID")+1,$goto);
            }else{
              $this->success( '注册成功！请登录！', $goto, 2 );
            }


          }else {
            $User->where( "user_id=".$id )->delete();
            $error=( "注册失败:".$Person->getError() );
          }
        }
      }
    }

    if ( $error !=null ) {
      $this->error( $error );
    }
  }

  //登录页面
  //@作者：NewFuture
  public function log( $p=null ) {
    $id=I( 'session.user_id', 0 );//获取用户id
    if ( $id< 1 ) {
      if ( $p==null )
        $this->display( "logfullscreen" );
      else
        $this-> display( "log" );
    }else {
      $this->error( "您已经登录", U( 'User/index' ) );
    }
  }


  /**
   * 登录验证
   * 修改 使用model获取，加密完全迁移至model中
   *
   * @author 第一版NewFuture
   */
  public function login() {
    $User=D( "User" );
    $error="登录失败！";

    $log_info= $User->create( $_POST, 2 ) ;
    if ( $log_info ) {

      $user_info=M('User')->getByUserEmail($log_info['user_email']);

      if ( $user_info ) {
        //邮箱存在
      
        if ( $user_info["user_passwd"]===$log_info['user_passwd'] ) {
          //密码一致

          $error=$this->_isValidUser( $user_info );
          if ( $error===true ) {
            //登陆成功
            $user_id=$user_info['user_id'] ;
            //更新登录时间
            $User->where( "user_id=".$user_id )->setField( "user_last_time", date( "Y-m-d H:i:s" ) );
            //session
            session( 'user_id',  $user_id );
            //cookie
            cookie( "uid",  $user_id, C( "COOKIE_TIME" ) );
            cookie( "upasswd", I( "post.user_passwd" ), C( "COOKIE_TIME" ) );

            $this->success( '登录成功,进入您的首页！', U( 'User/index' ), 1 );
            return true;
          }
        }
      }
    }
    $this->error( $error, U( 'User/Log' ) );

  }

  //登出
  //@作者：NewFuture
  public function logout() {
    //删除 cookie
    cookie( 'uid', null );
    cookie( 'upasswd', null );
    //删除session
    session_destroy();
    $this->success( '已经成功注销！', U( 'User/Log' ) );
  }


  /**
   * 密码找回页面
   *
   * @author NewFuture
   */
  public function findPassword() {
    if ( I( 'session.user_id', 0 )< 1 ) {
      $this->display( 'findpassword' );
    }  else {
      $this->error( "您已登录", U( 'User/index' ) );
    }
  }

  /**
   * 密码找回验证
   *
   * @author NewFuture
   */
  public function findPasswordSubmit() {
    $user_email=I( "post.user_email" );
    if ( !validate_email( $user_email ) ) {
      $this->error( "邮件格式不合法！" );
    }
    else {
      $User =M( 'user' );
      $user_info=$User->where( 'user_email="'.$user_email.'"' )->find() ;
      if ( $user_info ) {
        //发送找回邮件
        $id=$user_info['user_id'];
        $key=validate( $id );
        $url="http://".I( 'server.HTTP_HOST' ).U( "/User/validate", "id=$id&key=$key&type=".C( 'PASSWORD_MAIL' ) );
        send_mail( $user_email, C( 'PASSWORD_MAIL' ), '<a href="'.$url.'">'.$url.'</a>' );
        $this->success( "已成功发送找回邮件！", U( 'User/Log' ) );
      }else {
        $this->error( $user_email."尙为注册！", U( 'User/reg' ) );
      }
    }
  }

  /**
   * 修改密码页
   */
  public function changePassword() {
    $this->byemail=false;
    $this->display( "User/changepassword" );
  }

  /**
   * 修改密码
   *
   * @author NewFuture
   */
  public function changePasswordSubmit() {

    //邮箱找回修改
    $id=session( "user_id_for_change" );
    $User=D( 'User' );
    if ( $id< 1 ) {
      //普通修改
      $id=get_id();

      $oldPasswd=I( 'post.oldpasswd' );
      $old_info=$User->where( "user_id=".$id )->find();

      if ( $old_info["user_passwd"]!=$oldPasswd ) {
        $this->error( "密码错误！" );
        return false;
      }
    }

    if ( $User->field( 'user_passwd,re_password' )->create( $_POST, 2 ) ) {
      $User->user_id=$id;
      if ( $User->save() ) {
        session( "user_id_for_change", null );
        $this->success( "修改成功，正在注销重新登陆", U( 'User/logout' ) , 0.5 );
        return true;
      }
    }
    $this->error( "密码修改失败!" );
  }

  /**
   * 重新激活邮箱
   *
   * @author Future
   */
  public function reactive() {
    $user_email=I( "post.user_email" );
    if ( !$user_email ) {
      $this->email=I( "email" );
      $this->display( "User/reactive" );
    }elseif ( !validate_email( $user_email ) ) {
      $this->error( "邮件不存在！" );
    }    else {
      $User =M( 'user' );
      $user_info=$User->where( 'user_email="'.$user_email.'"' )->find() ;
      if ( !$user_info ) {
        $this->error( "尚未注册！" );
      } elseif ( ac( $user_info['user_type'], 1 ) ) {
        $this->error( "该邮箱已经激活，亲请登陆", U( "User/log" ) );
      }else {
        //发送激活邮件
        $id=$user_info['user_id'];
        $key=validate( $id );
        $url="http://".I( 'server.HTTP_HOST' ).U( "User/validate", "id=$id&key=$key&type=".C( "ACTIVE_MAIL" ) );
        send_mail( $user_email, C( 'ACTIVE_MAIL' ), '<a href="'.$url.'">'.$url.'</a>' );
        $this->success( "已成功发送激活邮件！", U( 'User/Log' ) );
      }
    }
  }

  /**
   * 注册激活，密码找回，url验证
   *
   * @author Future
   * @param int     $id验证的用户id
   * @param string  $key                 随机匹配密码
   * @param int     $type                验证类型
   * -1普通用户注册激活验证
   * -2密码找回验证
   * -3邀请的组织激活和初始化
   */
  public function validate( $id=0, $key=0, $type=0 ) {

    $error='';
    if ( $type>0 ) {

      $result=validate( $id, $key );

      if ( $result ===true ) {
        //验证成功
        $User=M( 'user' );
        $user_info=$User->where( 'user_id='. $id )->find();

        switch ( $type ) {
        case C( "ACTIVE_MAIL" ):
          //个人用户成功激活
          set_user_type( $user_info['user_type'], 1 );
          $User->save( $user_info );
          $this->success( '邮箱激活成功,请重新登录', U( 'User/Log' ) , 1 );
          break;

        case C( "PASSWORD_MAIL" ):
          //找回密码
          $this->byemail=true;
          session( "user_id_for_change", $id );
          $this->display( 'changepassword' );
          break;

        case C( "INVITE_ORG_MAIL" ):
          //组织激活初始化

          set_user_type( $user_info['user_type'], 1 );
          $User->save( $user_info );
          session( "user_id_for_change", $id );
          $this->byemail=true;
          $this->display( 'changepassword' );
          break;

        default:

          $this->error( "未知类型" );
          break;
        }
      } else {
        //
        $this->error( "验证失败:".$result[1] );
      }
    }else {
      $this->error( "错误url" );
    }
  }

  // /**
  //  * 切换组织和用户账号
  //  *
  //  * @author Future
  //  */
  // public function switchAcount() {
  //   $id=get_id();
  //   $type=session( "account_type" );
  //   $User=M( "user" );
  //   switch ( $type ) {
  //   case 'o':
  //     //当前为组织账号
  //     /*组织切换到个人*/
  //     $per_id=session( "user_id_p" );
  //     if ( $per_id>0 ) {

  //       //更新登录时间
  //       $User->where( "user_id=".$per_id )->setField( "user_last_time", date( "Y-m-d H:i:s" ) );
  //       //session
  //       session( 'user_id',  $user_id );
  //       session( "user_id", $per_id );
  //       session( "account_type", "p" );//账号类型

  //       $this->success( "成功切换到个人账号", U( 'User/info' ) );
  //     }else {
  //       $this->error( "个人账号不存在，请重新登陆！" );
  //     }
  //     break;

  //   case 'p':
  //     //当前为个人账户

  //     //切换到组织
  //     $org_id=session( "user_id_o" );
  //     if ( $org_id>0 ) {
  //       //更新登录时间
  //       $User->where( "user_id=".$org_id )->setField( "user_last_time", date( "Y-m-d H:i:s" ) );
  //       session( "user_id", $org_id );
  //       session( "account_type", "o" );//账号类型

  //       $this->success( "成功切换到组织账号", U( 'User/info' ) );
  //     }else {
  //       $this->error( "组织账号不存在，请重新登陆！" );
  //     }
  //     break;

  //   default:
  //     //第一次切换

  //     /*个人到组织*/
  //     $org=$this->_getUserOrg( $id );
  //     if ( $org ) {
  //       if ( $org['user_status']==C( "ADMIN_USER" ) ) {
  //         $OrgUser=M( "User" );
  //         $org_id= $org["organization_user_id"];
  //         $org_info=$OrgUser->getByUserId( $org_id );

  //         $result=$this->_isValidUser( $org_info );
  //         if ( $result===true ) {
  //           //个人切换到组织账号,更新session
  //           session( "user_id", $org_id );
  //           session( "user_id_p", $id );//个人账户
  //           session( "user_id_o", $org_id );//组织账号
  //           session( "account_type", "o" );//账号类型

  //           $this->success( "成功切换到组织账号", U( 'User/info' ) );
  //         }else {
  //           $this->error( $result );
  //         }
  //       }else {
  //         $this->error( "您不是管理员！" );
  //       }
  //     }else {
  //       $this->error( "无有效组织" );
  //     }

  //     break;
  //   }
  // }

  /**
   * 验证用户有效性
   *
   * @author Future
   * @param array   $user
   * @return ture/string
   * [有效返回true]
   * [无效返回错误信息]
   */
  protected function _isValidUser( $user ) {
    $error="不存在用户";
    if ( $user ) {
      if ( $user["user_effective"] ) {
        //有效
        if ( ac( $user["user_type"], 1 ) ) {
          //激活
          return true;
        }else {
          $error="尚处于验证期";
        }
      }else {
        $error="账号已封！";
      }
    }
    return $error;
  }

  /********************************************************
  *                 第二部分 用户信息
  *
  *index() 用户主页
  *infoCard( $id=0, $type=1 )资料卡
  *info( $id=0 )用户信息信息页
  *baseinfo( &$User=null, $isvisitor=true )用户基本信息显示
  *update()更新信息
  *edit( $field=null, $value=null )单条信息编辑
  *photo( &$user_info=null, $isvisitor=true )显示头像
  *uploadPhoto()处理上传头像
  *
  *_getUserOrg( $user_id=0 )获取个人用户所在组织
  ********************************************************/

  //用户主页
  //@作者：
  public function index() {
    $id=get_id();
    $this->user_name=M( "User" )->getFieldByUserId( $id, 'user_nickname' );

    //关注的用户
    $users=$this->_getFocus( "user" );
    $user_content='';
    if ( $users ) {
      foreach ( $users as $item ) {

        $html=$this->infoCard( $item["user_id_focused"], 1, true );
        if ( !empty( $html ) ) {
          $user_content.='<div class="col-md-4">'.$html.'</div>';
        }
      }
    }
    //$this->show($user_content);
    $this->focus_users_content=$user_content;

    //关注的文章
    $articles=$this->_getFocus( 'article' );
    $article_content="";
    if ( $articles ) {
      foreach ( $articles as $article ) {
        $this->data=M( "Article" )->getByArticleId( $article['article_id'] );

        $article_content.=$this->fetch( "Project:projectitem" );
      }
    }
    $this->focus_article_content=$article_content;



    $this->display( "User/index" );
  }

  /**
   * 获取用户所属组织
   *
   * @author Fututure
   * @param int     $user_id 用户id
   * @return array/null
   * [存在返回组织id，不存在返回null]
   */
  protected function _getUserOrg( $user_id=0 ) {

    if ( $user_id>C( "MIN_USER_ID" ) ) {
      $User=M( 'belong_to_organization' );
      return  $User->getByUserId( $user_id );
    }
    return null;
  }

  /**
   * 用户资料卡
   *
   * @author Future
   * @param unknown $id   用户id
   * @param unknown $type 资料卡类型，
   *  【1，默认，小资料卡】
   *  【2，大资料卡】
   * @return string /null
   *      $isfech=ture 默认返回渲染模板
   *      $isfech=false 直接输出
   */
  public function infoCard( $id=0, $type=1, $isfech=ture ) {

    if ( $id< 1 )
      return false;

    $User=M( "User" );
    $user_info=$User->getByUserId( $id );

    if ( $user_info==null )
      return null;
    if ( $type==1&&$isfech ) {
      $this->user=$user_info;
      return $this->fetch( "User/infocard_tiny" );
    }elseif ( $type==2 ) {
      //大资料卡详细信息
      $user_type=$user_info["user_type"];
      if ( ac( $user_type, 2 ) ) {
        //组织用户
        $Org=M( "organization" );
        $org_info=$Org->getByUserId( $id );
        $this->org=$org_info;
        break;
      }else {
        //个人用户
        $Person=M( "Person" );
        $person_info=$Person->getByUserId( $id );
        $this->person=$person_info;
        break;
      }
      // switch ( $user_type ) {
      // case C( "PERSON_ACTIVE" ):
      //   //个人用户
      //   $Person=M( "Person" );
      //   $person_info=$Person->getByUserId( $id );
      //   $this->person=$person_info;
      //   break;

      // case C( "ORG_ACTIVE" ):
      //   //组织用户
      //   $Org=M( "organization" );
      //   $org_info=$Org->getByUserId( $id );
      //   $this->org=$org_info;
      //   break;

      // default:
      //   // 其他
      //   return false;
      //   break;
      // }
    }
    $this->user=$user_info;
    if ( $isfech ) {
      return $this->fetch( "User/infocard" );
    }
    $this->display( "User/infocard" );
  }

  //用户信息
  //自己查看和他人查看不同
  //建议通过get参数id判断
  //@作者：NewFuture
  public function info( $id=0 ) {

    $visitor_id=get_id();//浏览者的id

    if ( $id==0 ) {
      //获取浏览对象的id
      $id=I( "id", 0 );
    }

    //$id==0,不存在表示查看自己的信息页
    //$id>0 访问$id的主页
    $isvisitor=(bool) $id;


    if ( !$isvisitor ) {
      //查看自己的信息
      $id=$visitor_id;
    }

    $User=M( "user" );
    $user_info=$User->getByUserId( $id );
    $user_type=$user_info["user_type"];

    if ( !$this->_isValidUser( $user_info ) ) {
      $this->error( "不可访问用户" );
    }

    $this->isself=!$isvisitor;
    if ( ac( $user_type, 2 ) ) {
      //组织信息
      $this->type="o";
      $Org=M( "organization" );
      $org_info=$Org->getByUserId( $id );
      $user_info=array_merge( $user_info, $org_info );

      //组织成员管理
      $members=M( 'belong_to_organization' )
      ->join( 'user ON belong_to_organization.user_id = user.user_id ' )
      ->where( 'belong_to_organization.organization_user_id='.$id )->select();

      $in_members_content=null;
      $apply_members_content=null;

      foreach ( $members as $member ) {
        switch ( $member['user_status'] ) {
        case C( "PENDING_USER" ):
          //申请中用户
          $this->user=$member;
          $apply_members_content.=$this->fetch( 'User/member' );
          break;

        case C( "NORMAL_USER" )://正常成员
        case C( 'ADMIN_USER' )://管理员
          $this->user=$member;
          $in_members_content.=$this->fetch( 'User/member' );
          break;

        default:
          // code...
          break;
        }

      }

      $this->members=$in_members_content;
      $this->applys=$apply_members_content;


    }else {
      //个人信息
      $this->type="p";
      $Person=M( "Person" );
      $person_info=$Person->getByUserId( $id );
      $user_info=array_merge( $user_info, $person_info );
    }

    if ( $user_info ) {

      $this->baseinfo( $user_info, $isvisitor, null );
      $this->display( "User/info" );

    }else {

      $this->error( "该用户不存在" );
    }
  }

  /**
   * 显示和编辑用户信息
   *
   * @author NewFuture
   * @param arry    $user       用户信息
   * @param [bool   $isvisitor] 是否只读
   */
  public function baseinfo( &$User=null, $isvisitor=true, $isfech=false ) {
    if ( $User==null ) {
      return null;
    }
    $this->user=$User;

    if ( $isvisitor ) {
      //游客只能看不能修改信息
      $this->isself=false;
      $this->enable='disabled="disabled"';
      $this->isfocuson=is_focus_on( $User['user_id'] );
    }else {
      //自己查看自己
      $this->isself=true;
      $this->enable=null;
    }

    if ( $isfech ) {
      return $this->fetch( "User/baseinfo" );
    }elseif ( $isfech===false ) {
      $this->display( 'User/baseinfo' );
    }
  }

  /**
   * //先验证后保存，字段过滤
   * //更新用户数据
   *
   * @author NewFuture
   */
  public function update() {
    $id=get_id();
    $error=null;
    $User=D( 'User' );
    $field="user_nickname,user_profile,user_signature";
    if ( !$User->field( $field )->create( $_POST, 2 ) ) {
      //创建失败
      $this->error( $User->getError() );
      return false;
    }else {

      if ( $User->data()!=M( 'User' )->where( 'user_id='.$id )->field( $field )->find() ) {
        //数据更新
        $User->user_id=$id;
        if ( !$User->save() ) {
          $error=( "保存失败！" );
        }
      }
    }


    $type=I( "type", null );

    switch ( $type ) {

    case "p":
      //个人用户
      $Person=D( "Person" );

      if ( $Person->field( "user_id", true )->create() ) {
        //更新person信息

        if ( $Person->data()!= M( "Person" )->where( "user_id=".$id )->field( "user_id", true )->find() ) {
          $Person->user_id=$id;

          if ( ! $Person->save() ) {
            $error.="保存失败！！";
          }
        }
      }

      break;

    case 'o':
      //组织用户
      $Org=M( "organization" );
      if ( $Org->create() ) {
        //更新organization
        if ( $Org->organization_milestone!=M( "organization" )->getFieldByUserId( $id, "organization_milestone" ) ) {

          $Org->user_id=$id;
          if ( ! $Org->save() ) {
            $error.="保存失败！！！";
          }
        }
      }
      break;

    default:
      // code...
      break;
    }

    if ( empty( $error ) ) {
      $this->success( "保存成功！" );
    }else {
      $this->error( $error );
    }
  }

  /**
   * 单条编辑用户数据
   *
   * @author Future
   * @param string  $field 字段名称
   * @param unknown $value 对应值
   * @return bool 是否成功
   */
  public function edit( $field=null, $value=null ) {
    $field=I( 'field' );
    $value=I( 'value' );
    if ( $field==null||$value===null ) {
      $this->error( "未设定修改内容" );
    }
    $id=get_id();
    $Table=null;

    switch ( $field ) {

      /*user表*/
      //用户表字段更新
    case 'user_nickname':
    case 'user_profile':
    case 'user_signature':

      $Table=M( "user" );
      break;

      /*person表*/
    case 'user_sex':
    case 'user_location':
    case 'user_birthday':
    case 'user_education_experience':
    case 'user_career_experience':
    case 'user_startup_point':
    case 'user_business':


      $Table=M( 'person' );
      break;

      /*organization表*/
    case 'organization_milestone':

      $Table=M( "organization" );
      break;

    default:

      break;
    }
    if ( $Table!=null ) {

      if ( $Table->where( "user_id=".$id )->setField( $field, $value )===false ) {
        $this->error( '保存失败！' );
      }else {
        $this->success( '保存成功！' );
      }

    }else {
      $this->error( '未作任何修改！' );
    }

  }

  /**
   * 头像
   * 显示头像或者更改头像
   *
   * @author NewFuture
   * @param array   $user_info 用户信息
   * @param bool    $isvisitor 是否只读，即是否可更改
   */
  public function photo( &$user_info=null, $isvisitor=true ) {
    if ( $user_info==null ) {
      $this->error( "非法操作", U( "User/index" ), 1 );
    }
    $this->user=$user_info;
    $this->isself=!$isvisitor;
    $this->display( 'User/photo' );
  }

  /**
   * 处理上传图片
   * *存在问题文件扩展名限制，旧的文件未删除
   *
   * @author NewFuture
   */
  public function uploadPhoto() {
    $id=get_id();
    $savePath  = 'Img/User/Photo/';
    $url=upload_file( $savePath, $id, "photo" );
    dump($_FILES["photo"]);
    if ( $url==null ) {
      // 上传错误
      $this->error( "头像上传失败！" );
      //return;
    }else {
      //更新数据库
      $User=M( 'user' );
      $conditon['user_id']=$id;
      $user_info['user_avatar_url']=$url;
      $User->where( $conditon )->save( $user_info );
      //$this->success( "修改成功！" );
    }
  }


  /***************************************************************
 *
 *                   第三部分  组织用户管理
 *
 *----组织操作---
 *inviteMember($id )邀请 成功1
 *acceptMember($id )接受  成功2
 *deleteMember( $id )拒绝删除 成功-1
 *setAdmin( $id ) 设置管理员  成功4
 *setNormal( $id ) 设置为普通成员 成功3
 *
 *---个人用户操作---
 *applyJoin( $oid )申请加入
 *quit()退出组织
 *agree($oid)同意加入
 ****************************************************************/

  /**
   * 组织中邀请成员
   *
   * @author Future
   * @param int     $id 邀请对象的id
   */
  public function inviteMember( $id ) {
    $error=null;
    $oid=get_id( false );
    $id=I( "id" );

    if ( $id<C( "MIN_USER_ID" ) ) {
      $error="无效用户";
    }else {
      $User=M( "User" );
      $user_type=$User->getFieldByUserId( $id, "user_type" );
      if ( ac( $user_type, 1 )&& ( !ac( $user_type, 2 ) ) ) {
        //判断用户类型
        $error="对方为非正常用户";
      }else {
        $P2O=M( "belong_to_organization" );
        $user_status=$P2O->getFieldByUserId( $id, "user_status" );
        if ( !$user_status ) {
          //是否已经加入
          $error="需要该用户解除和其他组织之间的关系，才可被邀请";
        }else {
          $p2o_info["user_id"]=$id;
          $p2o_info["organization_user_id"]=$oid;
          $p2o_info["user_status"]=C( "INVITE_USER" );
          $result=$P2O->add( $p2o_info );
          if ( !$result ) {
            $error="邀请失败！";
          }else {
            //邀请成功
            /*
            发送通知
            send message
       */
          }
        }
      }
    }
    if ( $error===null ) {
      $this->success( array( 1, '邀请成功' ) );
    }else {
      $this->error( $error );
    }
  }

  /**
   * 接受申请
   *
   * @author Future
   * @param unknown $id 接收者的id
   */
  public function acceptMember( $id ) {
    $oid=get_id( false );//获取组织id
    $id=I( "id" );
    $error=null;
    $is_validate=$this->_isValidUser( $id );

    if ( !$oid ) {
      $error=0;
    }elseif ( $is_validate!=true ) {
      $error=$is_validate;
    }else {

      $P2O=M( "belong_to_organization" );
      $data["user_id"]=$id;
      $data["organization_user_id"]=$oid;
      $p2o_info=$P2O->where( $data )->find();

      if ( !$p2o_info ) {

        $error="该用户尚未于此关联";
      }else {

        if ( $p2o_info["user_status"]!=C( "PENDING_USER" ) ) {
          $error="该用户不处于申请状态";
        }else {
          $result=$P2O->where( $data )->setField( "user_status", C( "NORMAL_USER" ) );
          if ( !$result ) {
            $error="审核失败";
          }
        }
      }
    }
    if ( $error===null ) {
      $this->success( array( 2, "添加成员成功" ), U( 'User/deleteMember' ) );
    } else {
      $this->error( $error );
    }
  }

  /**
   * 删除成员
   * 包括拒绝申请，撤销邀请
   *
   * @author Future
   * @param unknown $id 成员的id
   */
  public function deleteMember( $id ) {
    $oid=get_id( false );
    $id=I( "id" );
    $error=null;

    if ( !$oid ) {
      $error=0;
    } elseif ( $id< C ( "MIN_USER_ID" ) ) {
      $error="不存在的用户";
    }else {

      $P2O=M( "belong_to_organization" );
      $data["user_id"]=$id;
      $data["organization_user_id"]=$oid;

      $result=$P2O->where( $data )->delete();
      if ( !$result ) {
        $error="删除失败";
      }
    }
    if ( $error===null ) {
      $this->success( array( -1, "删除成功" ) );
    } else {
      $this->error( $error );
    }
  }

  /**
   * 设置管理员
   *
   * @author Future
   * @param unknown $id 设置的管理员id
   * //申请用户排除？
   */
  public function setAdmin( $id ) {
    $oid=get_id( false );//获取组织id
    $error=null;
    $id=I( "id" );
    $is_validate=$this->_isValidUser( $id );

    if ( !$oid ) {
      $error=0;
    }elseif ( $is_validate!=true ) {
      //验证用户有效性
      $error=$is_validate;
    }else {

      $P2O=M( "belong_to_organization" );
      $data["user_id"]=$id;
      $data["organization_user_id"]=$oid;

      $result=$p2o_info=$P2O->where( $data )->setField( "user_status", C( "ADMIN_USER" ) );
      if ( !$result ) {
        $error="设置失败";
      }
    }
    if ( $error===null ) {
      $this->success( array( 4, "设置管理员成功" ), U( "User/setNormal" ) );

    }else {
      $this->error( $error );
    }
  }

  /**
   * 设置为普通成员
   *
   * @author Future
   * @param unknown $id 成员id
   */
  public function setNormal( $id ) {
    $oid=get_id();//获取组织id
    $error=null;

    $is_validate=$this->_isValidUser( $id );
    if ( $is_validate!=true ) {
      //验证用户有效性
      $error=$is_validate;
    }else {

      $P2O=M( "belong_to_organization" );
      $data["user_id"]=$id;
      $data["organization_user_id"]=$oid;

      $result=$p2o_info=$P2O->where( $data )->setField( "user_status", C( "NORMAL_USER" ) );

      if ( !$result ) {

        $error="设置失败";
      }
    }
    if ( $error===null ) {
      $this->success( array( 3, '设置成功' ), U( 'User/setAdmin' ) );
    }else {
      $this->error( $error );
    }
  }


  /**
   * 个人申请加入组织
   *
   * @author Future
   * @param unknown $oid 组织id
   */
  public function applyJoin( $oid ) {
    $id=get_id( false );
    if ( !$id ) {
      $this->error( 0, U( 'User/Log' ) );
    }elseif ( !$oid ) {
      $oid=I( "oid" );
    }

    $error="";
    $User=M( "User" );
    $user_type=$User->getFieldByUserId( $id, "user_type" );
    if ( !ac( $user_type, 1 ) ) {
      $error="您的账号尚未激活";
    }elseif ( ac( $user_type, 2 ) ) {

      $error="只有个人用户才能加入组织";

    }else {

      $org_type=$User->getFieldByUserId( $oid, "user_type" );
      if ( !ac( $org_type, 2 ) ) {
        $error="对方不是组织";
      }elseif ( !ac( $org_type, 1 ) ) {
        $error="该组织尚未激活";
      }else {

        $P2O=M( "belong_to_organization" );
        $data["user_id"]=$id;
        $data["organization_user_id"]=$oid;
        $data["user_status"]=C( "PENDING_USER" );

        $result=$P2O->add( $data );

        if ( !$result ) {
          $error="申请失败,每个人只能申请加入一个组织";
        }
      }
    }
    if ( $error!=null ) {
      $this->error( $error );
    }else {
      $this->success( 1, U( 'User/quit' ) );
    }
  }

  /**
   * 个人撤销申请,退出组织，否决请求
   *
   * @author Future
   */
  public function quit() {
    $id=get_id( false );
    if ( !$id ) {
      $this->error( 0, U( 'User/Log' ) );
    }elseif ( !$oid ) {
      $oid=I( "oid" );
    }

    $result=M( "belong_to_organization" )->where( "user_id=".$id )->delete();
    if ( !$result ) {
      $this->error( "撤销失败" );
    }else {
      $this->success( -1, U( 'User/applyJoin' ) );
    }
  }

  /**
   * 接受请求
   *
   * @author Future
   * @param unknown $oid
   */
  public function agree( $oid ) {
    $error=null;
    $id=get_id();

    $User=M( "User" );
    $org_type=$User->getFieldByUserId( $oid, "user_type" );
    $org_type=$User->getFieldByUserId( $oid, "user_type" );
    if ( !ac( $org_type, 2 ) ) {
      $error="对方不是组织";
    }elseif ( !ac( $org_type, 1 ) ) {
      $error="该组织尚未激活";
    }else {
      $P2O=M( "belong_to_organization" );
      $data["user_id"]=$id;
      $data["organization_user_id"]=$oid;
      $p2o_info=$P2O->where( $data )->find();

      if ( $p2o_info["user_status"]!=C( "INVITE_USER" ) ) {
        $error="该组织现在未邀请您";

      }else {
        $p2o_info["user_status"]=C( "NORMAL_USER" );
        if ( !$P2O->save( $p2o_info ) ) {
          $error="接受失败";
        }
      }

    }
    if ( $error!=null ) {
      $this->error( $error );
    }
  }


  /********************************************************************
  *                    第四部分 文章管理
  *
  *dislpayAirtcle() 显示用户发表的文章
  *deleteArticle( $aid )删除文章
  *********************************************************************/

  /**
   * 显示文章
   *
   * @author Future
   */
  public function displayArticle( $page=1 ) {
    $id=get_id();
    $Article=M( "Article" );
    $article_config["user_id"]=$id;
    //有效
    $article_config["article_effctive"]=1;
    //查询文章
    $articles=$Article->where( $article_config )->page( 1, 10 )->select();

    $article_content="";
    if ( $articles ) {
      foreach ( $articles as $article ) {
        // $this->data=M( "Article" )->getByArticleId( $article['article_id'] );
        $this->data=$article;
        $article_content.=$this->fetch( "article" );
      }
    }
    echo $article_content;
  }

  /**
   * 删除文章
   *
   * @author Future
   * @param unknown $aid 文章id
   */
  public function deleteArticle( $aid ) {
    $id=get_id();
    $error=null;
    if ( $aid ) {
      $error="不存在的内容";
    }else {
      //标记为无效
      $Article=M( "Article" );
      $article_config["user_id"]=$id;
      $article_config["article_id"]=$aid;
      $result= $Article->where( $article_config )->setField( "article_effctive", 0 );
      if ( !$result ) {
        $error="删除失败";
      }
    }

    if ( $error!=null ) {
      $this->error( $error );
    }

  }


  /**********************************************************
*
*                    第五部分  关注内容
*
*focus 关注取消关注
***********************************************************/

  /**
   * 关注或者取消关注
   *
   * @param unknown $id 关注对象的id
   */
  public function focus( $id=0 ) {
    $id=I( "id" );
    if ( $id>0 ) {

      $result=focus( $id );
      if ( $result[0] ) {
        $this->success( $result );
      }else {
        $this->error( $result );
      }
    }
  }

  /**
   * 获取关注内容
   *
   * @param string  $typename 关注的名称user，article ，tag
   * @return  array or null ,返回查询对象id数组
   */
  protected function _getFocus( $typename ) {
    $id=get_id();
    $table=null;
    $item_name=null;
    $template=null;

    switch ( $typename ) {

    case 'user':
      $table="focus_on_user";
      $item_name="user_id_focused";
      $template="User/infocard_tiny";
      break;

    case 'article':
      $table="focus_on_article";
      $item_name="article_id";

      break;

    case 'tag':
      $table="focus_on_tag";
      $item_name='tag_id';
      break;

    default:
      break;
    }

    if ( $table==null ) {
      $this->error( "不可关注对象" );
    }else {
      $Table=M( $table );
      $focus=$Table->where( "user_id=".$id )->field( $item_name )->select();

      return $focus;
      // $items=null;
      // foreach ($focus as $item) {
      //   array_push($items,$Table->getByUserId($item[$item_name]));
      // }
      // return $item;
    }
  }

  /**
   * 显示关注信息
   *
   */
  public function displayFocus() {
    //用户
    $users=$this->_getFocus( "user" );
    $user_content='';
    foreach ( $focus_users as $user ) {
      // 所有关注的人
      $this->user=M( 'user' )->getByUserId( $user['user_id_focused'] );
      $user_content.=$this->fetch( "User/infocard_tiny" );
    }
    $this->show( $user_content );


    $articles=$this->_getFocus( "article" );

    $tags=$this->_getFocus( "tag" );

    $this->show( $content );
  }

  /**
   * 批量添加标签
   * [注意 如果只是添加单个标签请使用 focusOn效率更高]
   *
   * @param string  $tags 以空格分开的标签字符串
   */
  public function addTags( $tags ) {
    if ( link_tags( get_id(), $tags )!=null ) {
      $this->error( "添加失败" );
    }
  }

  /*****************************************
认证 部分
*******************************************/
  /*个人认证暂时取消*/
  // //个人认证页面
  // //@作者：NewFuture
  // public function pVerify() {
  //   $id=I( "session.user_id", 0 );
  //   if ( $id< 1 ) {
  //     $this->display( 'log' );
  //     return ;
  //   }else {
  //     $User=M( 'User' );
  //     $this->user=$User->where( 'user_id='.$id )->find();
  //     $this->display( 'pverify' );
  //   }
  // }

  // //个人认证信息提交
  // // @author : NewFuture
  // public function pVerifySubmit() {
  //   $id=I( "session.user_id", 0 );
  //   if ( $id< 1 ) {
  //     $this->display( 'log' );
  //     return ;
  //   }else {
  //     $User=M( 'User' );
  //     $user_info=$User->where( "user_id=".$id )->find();
  //     if ( $user_info["user_certified"]!=0 )//已经验证通过
  //       {
  //       $this->error( "您已经验过或正在验证!" );
  //       return false;
  //     }

  //     $id_number=I( 'post.user_id_number', null );
  //     if ( $id_number==null ) {

  //       $this->error( "输入正确身份证件号！" );
  //       return false;
  //     }
  //     //upload
  //     $savePath="Img/User/Id_card/";
  //     $url=upload_file( $savePath, $id, "id_card" );
  //     if ( $url==null ) {
  //       $this->error( "上传错误！" );
  //       return;
  //     }
  //     $user_info["user_certified"]=1;
  //     $user_info["user_certification_info"]=$url;
  //     $user_info["user_id_number"]=$id_number;
  //     if ( $User->save( $user_info ) ) {
  //       $this->success( "认证申请已成功提交！" );
  //     }
  //   }
  // }

  // /**
  //  * 撤销认证
  //  *
  //  * @author NewFuture
  //  *  待解决疑问：1. 是否同时撤销企业认证，
  //  * 删除上传的认证信息
  //  * */
  // public function deletePVerify() {
  //   $id=get_id();
  //   $User=M( 'User' );
  //   $user_info["user_certified"]=0;
  //   if ( $User->where( "user_id=".$id )->save( $user_info ) ) {
  //     $this->success( "已撤销成功认证信息！" );
  //   }
  // }

  //公司认证页
  //@作者：NewFUture
  public function cVerify() {
    $id=get_id();
    $User=M( 'User' );
    $user_info=$User->where( "user_id=".$id )->find();
    if ( $user_info["user_certified"]!=2 ) {
      $this->error( "您尚未个人认证！", U( 'User/info' ) );
      return ;
    }
    if ( $user_info["enterprise_id"]===NULL ) {
      $Enterprise=M( "Enterprise" );
      $this->enterprise=$Enterprise->where( "user_id=".$id )->find();
      $this->display( "cverify" );
    }else {
      $this->error( "您的企业已经认证或者正在认证！", U( "User/info" ) );
    }
  }

  //公司认证提交
  //@作者：NewFuture
  public function cVerifySubmit() {
    $id=get_id();
    $User=M( "User" );
    if ( $user_info=$User->where( "user_id=".$id )->find() ) {
      if ( $user_info["user_certified"]!=2 ) {
        $this->error( "您尚未个人认证！", U( 'User/info' ) );
        return ;
      }
      if ( $user_info["enterprise_id"]!=NULL ) {
        $this->error( "已经认证过或者正在认证！" );
      }else {
        $user_info["enterprise_id"]=0;
        $User->save( $user_info );
      }
    }

    $NewInterData=D( "Enterprise" );
    if ( $NewInterData->create() ) {
      $Enterprise=M( "Enterprise" );
      $old_enterprise_info=$Enterprise->where( "user_id=".$id )->find();
      $savePath="Img/Enterprise/Yyzz/";


      if ( $old_enterprise_info )//曾经提交过认证请求
        {
        $e_id=$old_enterprise_info["enterprise_id"];
        if ( isset( $_POST['yyzz'] ) ) {
          $url=upload_file( $savePath, $e_id, "yyzz" );
          if ( $url ) {
            $NewInterData->enterprise_pic_of_yyzz_url=$url;
          }else {
            $this->deleteCVerify( false );
            $this->error( "营业执照上传失败！" );
          }
        }
        $NewInterData->enterprise_id=$e_id;
        $NewInterData->save();
        $this->success( "已更新！" );
      }else {//第一次认证
        $NewInterData->user_id=$id;
        $NewInterData->add();
        $e_id=$NewInterData->enterprise_id;
        $url=upload_file( $savePath, $e_id, "yyzz" );
        if ( $url ) {
          $NewInterData->enterprise_pic_of_yyzz_url=$url;
          if ( $NewInterData->save() ) {
            $this->success( "保存成功！" );
          }else {
            $this->deleteCVerify( false );
            $this->error( "保存失败！" );
          }
        }else {
          $this->deleteCVerify( false );
          $this->error( "营业执照上传失败1！"  );
        }
      }
    }else {
      $this->deleteCVerify( false );
      $this->error( "提交失败！" );
    }
  }

  /**
   * 撤销企业认证
   * user.enterprise_id置为NULL
   * enterprise.enterprise_certified设为0;
   *
   * @author NewFuture
   * */
  public function deleteCVerify( $showError=true ) {
    $id=get_id();
    $User=M( 'User' );

    $user_info=$User->where( "user_id=".$id )->find();

    if ( $user_info["enterprise_id"]!=NULL ) {

      $user_info["enterprise_id"]=NULL;
      if ( $User->save( $user_info ) ) {
        $Enterprise=M( "Enterprise" );
        $enterprise=$Enterprise->where( "user_id=".$id )->find();
        if ( $enterprise['enterprise_certified']!=0 ) {
          $enterprise['enterprise_certified']=0;
          if ( !$Enterprise->save( $enterprise ) && $showError ) {
            $this->error( "撤销认证失败！" );
          }
        }
        if ( $showError ) {
          $this->success( "撤销成功！" );
          return;
        }
      }else {
        if ( $showError ) {
          $this->error( "信息修改失败！" );
        }
      }
    }else {
      if ( $showError ) {
        $this->error( "您的尚未进行企业已经认证！", U( "User/info" ) );
      }
    }
  }

  //--------------------------------测试-------------------------------------
  //测试


  public function test( $id=0 ) {
echo C("IS_SAE")?1:0;
  }
}
